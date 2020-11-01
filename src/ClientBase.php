<?php

namespace Balsama;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;

class ClientBase
{

    protected string $apiUrl = 'https://liproduction-reportsbucket-bhk8fnhv1s76.s3-us-west-1.amazonaws.com/v1/latest/';
    protected string $endpoint = 'timeseries-byLocation.json';

    /* @var ClientInterface */
    protected ClientInterface $client;

    protected $currentRawData;

    public function __construct()
    {
        $this->setupTools();
        $this->currentRawData = $this->fetchCurrentRawData();
    }

    /**
     * @return mixed
     */
    public function getAllRawData()
    {
        return $this->currentRawData;
    }

    protected function fetchCurrentRawData($retryOnError = true)
    {
        try {
            /**
 * @var $response ResponseInterface $response
*/
            $response = $this->client->get($this->apiUrl . $this->endpoint);
            $body = json_decode($response->getBody());
            return $body;
        } catch (ServerException $e) {
            if ($retryOnError) {
                return $this->getCurrentData();
            }
            echo 'Caught response: ' . $e->getResponse()->getStatusCode();
        }
    }

    protected function setupTools()
    {
        $this->client = new Client();
    }
}
