<?php namespace Todaymade\Daux\Format\Confluence;

use GuzzleHttp\Client;

class Api {

    protected $base_url;
    protected $user;
    protected $pass;

    protected $space;

    public function __construct($base_url, $user, $pass, $space_id) {
        $this->base_url = $base_url;
        $this->user = $user;
        $this->pass = $pass;
        $this->setSpace($space_id);
    }

    public function setSpace($space_id) {
        $this->space = $space_id;
    }

    /**
     * /rest/api/content/{id}/child/{type}
     *
     * @param $rootPage
     * @return mixed
     */
    public function getHierarchy($rootPage) {
        $hiera = $this->getClient()->get("content/$rootPage/child/page?expand=version,body.storage")->json();

        $children = [];
        foreach($hiera['results'] as $result) {
            $children[$result['title']] = [
                "id" => $result['id'],
                "title" => $result['title'],
                "version" => $result['version']['number'],
                "content" => $result['body']['storage']['value'],
                "children" => $this->getHierarchy($result['id'])
            ];
        }

        return $children;
    }

    public function createPage($parent_id, $title, $content) {

        $body = [
            'type' => 'page',
            'space' => ['key' => $this->space],
            'ancestors' => [['type' => 'page', 'id' => $parent_id]],
            'title' => $title,
            'body' => ['storage' => ['value' => $content, 'representation' => 'storage']]
        ];

        $response = $this->getClient()->post('content', [ 'json' => $body ])->json();

        return $response['id'];
    }

    public function updatePage($parent_id, $page_id, $newVersion, $title, $content) {
        $body = [
            'type' => 'page',
            'space' => ['key' => $this->space],
            'ancestors' => [['type' => 'page', 'id' => $parent_id]],
            'version' => ['number' => $newVersion, "minorEdit" => false],
            'title' => $title,
            'body' => ['storage' => ['value' => $content, 'representation' => 'storage']]
        ];

        $this->getClient()->put("content/$page_id", [ 'json' => $body ])->json();
    }

    public function deletePage($page_id) {
        return $this->getClient()->delete('content/' . $page_id)->json();
    }

    protected function getClient() {

        $options = [
            'base_url' => $this->base_url . 'rest/api/',
            'defaults' => [
                'auth' => [$this->user, $this->pass]
            ]
        ];

        return new Client($options);
    }
}
