<?php
namespace Breakeneck\Http;

class CurlTransport implements iTransport
{
    private $opts = [];

    public function __construct($opts = [])
    {
        $this->opts = $opts;
    }

    public function send(Request $request): Response
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);

        if (!empty($this->opts)) {
            foreach ($this->opts as $option => $value) {
                curl_setopt($curl, $option, $value);
            }
        }

        if ($request->headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $request->headers);
        }

        switch (strtoupper($request->method)) {
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->data);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->data);
                break;
        }
        curl_setopt($curl, CURLOPT_URL, $request->getUrl());
        $httpResponse = curl_exec($curl);

        $response = new Response($request);
        $response->setRawBody($httpResponse);
        $response->setRawHeaders($this->prepareHeaders($curl, $httpResponse));

        curl_close($curl);

        return $response;
    }

    private function prepareHeaders($curl, $result): array
    {
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($result, 0, $header_size);
        return self::parseHeaders(explode("\r\n", trim(substr($headers, strpos($headers, "\r\n")))));
    }

    private static function parseHeaders($headers): array
    {
        $result = [];
        foreach ($headers as $header) {
            $data = explode(':', $header);
            if (count($data) == 2) {
                list($key, $value) = $data;
                $result[trim($key)] = trim($value);
            }
            if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $header, $out)) {
                $result['response_code'] = intval($out[1]);
            }
        }
        return $result;
    }

}
