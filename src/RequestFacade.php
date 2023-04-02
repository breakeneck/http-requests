<?php

namespace Breakeneck\Http;

trait RequestFacade
{
    public function addHeaders(array $headers): Request
    {
        if ($headers) {
            $this->headers = array_merge($this->headers, $headers);
        }
        return $this;
    }

    public function setData($data): Request
    {
        $this->data = $data;
        return $this;
    }

    public function setTimeout(int $timeout): Request
    {
        if (!empty($timeout)) {
            $this->timeout = $timeout;
        }
        return $this;
    }
    public function get(string $url, $urlReplaceParams = []): Response
    {
        return $this->setMethod(Request::METHOD_GET)->send($url, $urlReplaceParams);
    }

    public function post(string $url, $urlReplaceParams = []): Response
    {
        return $this->setMethod(Request::METHOD_POST)->send($url, $urlReplaceParams);
    }

    public function put(string $url, $urlReplaceParams = []): Response
    {
        return $this->setMethod(Request::METHOD_PUT)->send($url, $urlReplaceParams);
    }

    public function delete(string $url, $urlReplaceParams = []): Response
    {
        return $this->setMethod(Request::METHOD_DELETE)->send($url, $urlReplaceParams);
    }

    public function patch(string $url, $urlReplaceParams = []): Response
    {
        return $this->setMethod(Request::METHOD_PATCH)->send($url, $urlReplaceParams);
    }

    public function xml($rootXmlNode = null)
    {
        $this->xmlRootNode = $rootXmlNode;
        $this->setType(Request::TYPE_XML);
        return $this;
    }

    public function json()
    {
        $this->setType(Request::TYPE_JSON);
        return $this;
    }

    public function curl()
    {
        $this->setTransport(Request::TRANSPORT_CURL);
    }

    public function getUrl(): string
    {
        return $this->url
            . (strpos($this->url, '?') ? '&' : '?')
            . implode('&', self::encodeParams($this->query));
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
