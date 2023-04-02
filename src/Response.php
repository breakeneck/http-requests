<?php

namespace Breakeneck\Http;

use LaLit\XML2Array;

class Response
{
    public Request $request;

    public array $headers = [];
    public $rawBody;
    /** @var mixed  - Parsed body */
    public $content;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function setRawHeaders($headers): Response
    {
        foreach ($headers as $header) {
            $data = explode(':', $header);
            if (count($data) == 2) {
                list($key, $value) = $data;
                $this->headers[trim($key)] = trim($value);
            }
            if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $header, $out)) {
                $this->headers['response_code'] = intval($out[1]);
            }
        }
        return $this;
    }

    public function setRawBody($body): Response
    {
        $this->rawBody = $body;
        switch ($this->request->type) {
            case Request::TYPE_JSON:
                $this->content = json_decode($body);
                break;
            case Request::TYPE_XML:
                $this->content = XML2Array::createArray($body);
                break;
            default:
                $this->content = $body;
        }
        return $this;
    }

    public function __toString()
    {
        return json_encode($this->content, JSON_PRETTY_PRINT);
    }
}
