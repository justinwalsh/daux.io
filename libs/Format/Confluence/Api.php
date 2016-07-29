<?php namespace Todaymade\Daux\Format\Confluence;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class Api
{
    protected $base_url;
    protected $user;
    protected $pass;

    protected $space;

    public function __construct($base_url, $user, $pass)
    {
        $this->base_url = $base_url;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function setSpace($space_id)
    {
        $this->space = $space_id;
    }

    /**
     * This method is public due to test purposes
     * @return Client
     */
    public function getClient()
    {
        $options = [
            'base_uri' => $this->base_url . 'rest/api/',
            'auth' => [$this->user, $this->pass],
        ];

        return new Client($options);
    }

    /**
     * The standard error message from guzzle is quite poor in informations,
     * this will give little bit more sense to it and return it
     *
     * @param BadResponseException $e
     * @return \Exception
     */
    protected function handleError(BadResponseException $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();

        $level = floor($response->getStatusCode() / 100);

        if ($level == '4') {
            $label = 'Client error response';
        } elseif ($level == '5') {
            $label = 'Server error response';
        } else {
            $label = 'Unsuccessful response';
        }

        $message = $label .
            ' [url] ' . $request->getUri() .
            ' [status code] ' . $response->getStatusCode() .
            ' [message] ';

        $body = $response->getBody();
        $json = json_decode($body, true);
        $message .= ($json != null && array_key_exists('message', $json)) ? $json['message'] : $body;

        if ($level == '4' && strpos($message, 'page with this title already exists') !== false) {
            return new DuplicateTitleException($message, 0, $e->getPrevious());
        }

        return new BadResponseException($message, $request, $response, $e->getPrevious());
    }

    public function getPage($id)
    {
        $url = "content/$id?expand=ancestors,version,body.storage";

        try {
            $result = json_decode($this->getClient()->get($url)->getBody(), true);
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }

        $ancestor_id = null;
        if (array_key_exists('ancestors', $result) && count($result['ancestors'])) {
            $ancestor_page = end($result['ancestors']); // We need the direct parent
            $ancestor_id = $ancestor_page['id'];
        }

        return [
            'id' => $result['id'],
            'ancestor_id' => $ancestor_id,
            'title' => $result['title'],
            'version' => $result['version']['number'],
            'content' => $result['body']['storage']['value'],
        ];
    }

    /**
     * Get a list of pages
     *
     * @param int $rootPage
     * @param bool $recursive
     * @return array
     */
    public function getList($rootPage, $recursive = false)
    {
        $increment = 15;

        // We set a limit of 15 as it appears that
        // Confluence fails silently when retrieving
        // more than 20 entries with "body.storage"
        $base_url = $url = "content/$rootPage/child/page?expand=version,body.storage&limit=$increment";
        $start = 0;

        $pages = [];

        do {
            try {
                $hierarchy = json_decode($this->getClient()->get($url)->getBody(), true);
            } catch (BadResponseException $e) {
                throw $this->handleError($e);
            }

            foreach ($hierarchy['results'] as $result) {
                $pages[$result['title']] = [
                    'id' => $result['id'],
                    'title' => $result['title'],
                    'version' => $result['version']['number'],
                    'content' => $result['body']['storage']['value'],
                ];

                if ($recursive) {
                    $pages[$result['title']]['children'] = $this->getList($result['id'], true);
                }
            }

            // We don't use _links->next as after ~30 elements
            // it doesn't show any new elements. This seems
            // to be a bug in Confluence
            $start += $increment;
            $url = "$base_url&start=$start";
        } while (!empty($hierarchy['results']));

        return $pages;
    }

    /**
     * @param int $parent_id
     * @param string $title
     * @param string $content
     * @return int
     */
    public function createPage($parent_id, $title, $content)
    {
        $body = [
            'type' => 'page',
            'space' => ['key' => $this->space],
            'title' => $title,
            'body' => ['storage' => ['value' => $content, 'representation' => 'storage']],
        ];

        if ($parent_id) {
            $body['ancestors'] = [['type' => 'page', 'id' => $parent_id]];
        }

        try {
            $response = json_decode($this->getClient()->post('content', ['json' => $body])->getBody(), true);
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }

        return $response['id'];
    }

    /**
     * @param int $parent_id
     * @param int $page_id
     * @param int $newVersion
     * @param string $title
     * @param string $content
     */
    public function updatePage($parent_id, $page_id, $newVersion, $title, $content)
    {
        $body = [
            'type' => 'page',
            'space' => ['key' => $this->space],
            'version' => ['number' => $newVersion, 'minorEdit' => true],
            'title' => $title,
            'body' => ['storage' => ['value' => $content, 'representation' => 'storage']],
        ];

        if ($parent_id) {
            $body['ancestors'] = [['type' => 'page', 'id' => $parent_id]];
        }

        try {
            $this->getClient()->put("content/$page_id", ['json' => $body]);
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }
    }

    /**
     * Delete a page
     *
     * @param int $page_id
     * @return mixed
     */
    public function deletePage($page_id)
    {
        try {
            return json_decode($this->getClient()->delete('content/' . $page_id)->getBody(), true);
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }
    }

    /**
     * @param int $id
     * @param array $attachment
     */
    public function uploadAttachment($id, $attachment)
    {
        // Check if an attachment with
        // this name is uploaded
        try {
            $result = json_decode($this->getClient()->get("content/$id/child/attachment?filename=$attachment[filename]")->getBody(), true);
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }

        $url = "content/$id/child/attachment";

        // If the attachment is already uploaded,
        // the update URL is different
        if (count($result['results'])) {
            $url .= "/{$result['results'][0]['id']}/data";
        }

        try {
            $this->getClient()->post(
                $url,
                [
                    'multipart' => [['name' => 'file', 'contents' => fopen($attachment['file']->getPath(), 'r')]],
                    'headers' => ['X-Atlassian-Token' => 'nocheck'],
                ]
            );
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }
    }
}
