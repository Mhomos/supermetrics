<?php
namespace App\Services;

use App\Helpers\HelperMethods;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;

class SupermetricsSocialNetworkService
{
    /**
     * @var bool
     */
    public $fetchMore = true;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var void
     */
    private $response;

    /**
     * @var
     */
    private $token;

    /**
     * @var false|string
     */
    private $tokenCreatedAt;

    /**
     * Posts Data
     * @var
     */
    private $posts;
    /**
     * @var Current Page Number
     */
    public $pageNumber;

    /**
     * SupermetricsSocialNetworkService constructor.
     */
    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => HelperMethods::config('base_uri'),
        ]);
    }

    public function registerToken()
    {
        $response = $this->http->request('POST', 'assignment/register', [
            'form_params' => [
                'client_id' => HelperMethods::config('client_id'),
                'email' => HelperMethods::config('email'),
                'name' => HelperMethods::config('name'),
            ]
        ]);

        $this->setResponse($response->getBody());

        $this->setToken();

        return $this;
    }

    /**
     * Get Token , Register if token is expired or there is no token
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken()
    {
        if (!$this->isValidToken()) {
            $this->registerToken();
        }

        return $this->token;
    }

    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchPosts()
    {
        $response = $this->http->request('GET', 'assignment/posts', [
            'query' => [
                'sl_token' => $this->getToken(),
                'page' => $this->pageNumber,
            ]
        ]);

        $this->setResponse($response->getBody());

        $this->setPosts();

        return $this;
    }

    /**
     * get Posts Data
     *
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param Stream $response
     */
    private function setResponse(Stream $response)
    {
        $this->response = json_decode($response);
    }

    /**
     * Set Token , Set Token Expiry Date
     */
    private function setToken()
    {
        $this->token = $this->response->data->sl_token;

        $this->setTokenExpiryDate();
    }

    /**
     * Set Posts
     */
    private function setPosts()
    {
        $this->posts = $this->response->data->posts;
    }

    /**
     * Set Token Expiry Date
     */
    private function setTokenExpiryDate()
    {
        $this->tokenCreatedAt = $this->getCurrentDate();
    }

    /**
     * Check if token is valid , its expired or empty
     *
     * @return bool
     */
    private function isValidToken()
    {
        return ($this->token || !$this->isTokenExpired());
    }

    /**
     * Check if token is expired > if exceeded the config token expiry time
     *
     * @return bool
     */
    private function isTokenExpired()
    {
        $hourdiff = round((strtotime($this->getCurrentDate()) - strtotime($this->tokenCreatedAt)) / 60, 1);

        return ($hourdiff >= HelperMethods::config('token_expiry'));
    }

    /**
     * @return false|string Get the current datetime.
     */
    private function getCurrentDate()
    {
        return date('Y-m-d H:i:s');
    }
}
