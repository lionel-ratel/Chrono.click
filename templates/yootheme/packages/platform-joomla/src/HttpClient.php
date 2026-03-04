<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Http\Http;
use Joomla\CMS\Http\HttpFactory;
use Joomla\Registry\Registry;
use YOOtheme\Http\Response;
use YOOtheme\HttpClientInterface;

class HttpClient implements HttpClientInterface
{
    /**
     * @inheritdoc
     */
    public function get(string $url, array $options = []): Response
    {
        return $this->createResponse($this->createClient($options)->get($url));
    }

    /**
     * @inheritdoc
     */
    public function post(string $url, $data = null, array $options = []): Response
    {
        return $this->createResponse($this->createClient($options)->post($url, $data));
    }

    /**
     * @inheritdoc
     */
    public function put(string $url, $data = null, array $options = []): Response
    {
        return $this->createResponse($this->createClient($options)->put($url, $data));
    }

    /**
     * @inheritdoc
     */
    public function delete(string $url, array $options = []): Response
    {
        return $this->createResponse($this->createClient($options)->delete($url));
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return Http
     */
    protected function createClient(array $options = [])
    {
        return HttpFactory::getHttp(new Registry($options));
    }

    /**
     * @param \Joomla\Http\Response $response
     */
    protected function createResponse($response): Response
    {
        return (new Response($response->getStatusCode(), $response->getHeaders()))->write(
            $response->getBody(),
        );
    }
}
