<?php

namespace Breakeneck\Http;

class StreamTransport implements iTransport
{
    private $opts = [];

    public function __construct($opts = [])
    {
        $this->opts = $opts;
    }

    public function send(Request $request): Response
    {
        $this->opts = ['http' => [
            'ignore_errors' => true,
            'method' => strtoupper($request->method),
            'timeout' => $request->timeout ?? Request::DEFAULT_TIMEOUT,
        ]];
        if ($request->data) {
            $this->opts['http']['content'] = $request->data;
        }
        if ($request->headers) {
            $parsedHeaders = [];
            foreach ($request->headers as $key => $value) {
                $parsedHeaders[] = "$key: $value";
            }
            $this->opts['http']['header'] = implode("\r\n", $parsedHeaders);
        }

        $httpResponse = file_get_contents($request->getUrl(), 0, stream_context_create($this->opts));

        return (new Response($request))
            ->setRawBody($httpResponse)
            ->setRawHeaders($http_response_header);
    }
}
