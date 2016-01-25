<?php namespace Todaymade\Daux\Format\Confluence;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ParseException;

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

    protected function getClient()
    {
        $options = [
            'base_url' => $this->base_url . 'rest/api/',
            'defaults' => [
                'auth' => [$this->user, $this->pass]
            ]
        ];

        return new Client($options);
    }

    /**
     * The standard error message from guzzle is quite poor in informations,
     * this will give little bit more sense to it and return it
     *
     * @param BadResponseException $e
     * @return BadResponseException
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
            ' [url] ' . $request->getUrl() .
            ' [status code] ' . $response->getStatusCode() .
            ' [message] ';

        try {
            $message .= $response->json()['message'];
        } catch (ParseException $e) {
            $message .= (string) $response->getBody();
        }

        if ($level == '4' && strpos($message, "page with this title already exists") !== false) {
            return new DuplicateTitleException($message, 0, $e->getPrevious());
        }

        return new BadResponseException($message, $request, $response, $e->getPrevious());
    }

    /**
     * Get a list of pages
     *
     * @param integer $rootPage
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
                $hierarchy = $this->getClient()->get($url)->json();
            } catch (BadResponseException $e) {
                throw $this->handleError($e);
            }

            foreach ($hierarchy['results'] as $result) {
                $pages[$result['title']] = [
                    "id" => $result['id'],
                    "title" => $result['title'],
                    "version" => $result['version']['number'],
                    "content" => $result['body']['storage']['value'],
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
     * @param integer $parent_id
     * @param string $title
     * @param string $content
     * @return integer
     */
    public function createPage($parent_id, $title, $content)
    {
        $body = [
            'type' => 'page',
            'space' => ['key' => $this->space],
            'ancestors' => [['type' => 'page', 'id' => $parent_id]],
            'title' => $title,
            'body' => ['storage' => ['value' => $content, 'representation' => 'storage']]
        ];

        try {
            $response = $this->getClient()->post('content', ['json' => $body])->json();
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }

        return $response['id'];
    }

    /**
     * @param integer $parent_id
     * @param integer $page_id
     * @param integer $newVersion
     * @param string $title
     * @param string $content
     */
    public function updatePage($parent_id, $page_id, $newVersion, $title, $content)
    {
        $body = [
            'type' => 'page',
            'space' => ['key' => $this->space],
            'ancestors' => [['type' => 'page', 'id' => $parent_id]],
            'version' => ['number' => $newVersion, "minorEdit" => true],
            'title' => $title,
            'body' => ['storage' => ['value' => $content, 'representation' => 'storage']]
        ];

        try {
            $this->getClient()->put("content/$page_id", ['json' => $body])->json();
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }
    }

    /**
     * Delete a page
     *
     * @param integer $page_id
     * @return mixed
     */
    public function deletePage($page_id)
    {
        try {
            return $this->getClient()->delete('content/' . $page_id)->json();
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }
    }

    /**
     * @param integer $id
     * @param array $attachment
     */
    public function uploadAttachment($id, $attachment)
    {
        // Check if an attachment with
        // this name is uploaded
        try {
            $result = $this->getClient()->get("content/$id/child/attachment?filename=$attachment[filename]")->json();
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
                    'body' => ['file' => fopen($attachment['file']->getPath(), 'r')],
                    'headers' => ['X-Atlassian-Token' => 'nocheck'],
                ]
            );
        } catch (BadResponseException $e) {
            throw $this->handleError($e);
        }
    }
}
