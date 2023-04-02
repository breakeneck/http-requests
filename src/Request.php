<?php

namespace Breakeneck\Http;

use LaLit\Array2XML;

class Request
{
    use RequestFacade;
    const DEFAULT_TIMEOUT = 60;
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    const METHODS_WITHOUT_BODY = [self::METHOD_GET, self::METHOD_DELETE];

    const TRANSPORT_STREAM = 'Breakeneck\Http\StreamTransport';
    const TRANSPORT_CURL = 'Breakeneck\Http\CurlTransport';

    const JSON_HEADER = ['Content-Type' => 'application/json'];
    const XML_HEADER = ['Content-Type' => 'text/xml'];

    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_PLAIN = 'plain';

    public string $type;
    public array $headers = [];
    /**
     * @var mixed null
     */
    public $data = null;
    public ?string $xmlRootNode = 'root';
    public string $method = self::METHOD_GET;
    public string $url;
    public array $query = [];
    public ?int $timeout = null;
    protected ?iTransport $transport = null;

    public function __construct()
    {
    }

    protected function setTransport($transportClass = self::TRANSPORT_STREAM, $transportOpts = [])
    {
        $this->transport = new $transportClass($transportOpts);
    }

    /**
     * @param $url string
     * @param $params array Example: for url 'some_uri/{product_id}' params should be ['product_id' => 100]
     * @return $this
     */
    protected function setUrl(string $url, array $params = []): Request
    {
        $this->url = $params ? str_replace(array_keys($params), array_values($params), $url) : $url;
        return $this;
    }

    public function setMethod($method): Request
    {
        $this->method = strtoupper($method);

        return $this;
    }

    protected function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    protected function addQuery(array $query): Request
    {
        if ($query) {
            $this->query = array_merge($this->query, $query);
        }
        return $this;
    }

    /**
     * @param $url string|array - Can be [$url, $params] Example: ['some_uri/{product_id}', ['product_id' => 100]],
     * @return Response
     */
    public function send(string $url, $urlReplaceParams = []): Response
    {
        $this->setUrl($url, $urlReplaceParams);

        if (!$this->url) {
            throw new \Exception('Url is not set for request');
        }

        if (in_array($this->method, self::METHODS_WITHOUT_BODY)) {
            if ($this->data) {
                $this->addQuery($this->data);
            }
        }

        switch ($this->type) {
            case self::TYPE_JSON:
                $this->addHeaders(self::JSON_HEADER);
                if (! in_array($this->method, self::METHODS_WITHOUT_BODY) && $this->data) {
                    $this->data = json_encode($this->data);
                }
                break;
            case self::TYPE_XML:
                $this->addHeaders(self::XML_HEADER);
                if (! in_array($this->method, self::METHODS_WITHOUT_BODY) && $this->data) {
                    $this->data = Array2XML::createXML($this->xmlRootNode, $this->data)->saveXML();
                }
                break;
        }

        if ($this->transport === null) {
            $this->setTransport();
        }

        return $this->transport->send($this);
    }

    protected static function encodeParams($params): array
    {
        return array_map(function ($key, $value) {
            return $key . '=' . urlencode($value);
        }, array_keys($params), $params);
    }
}
