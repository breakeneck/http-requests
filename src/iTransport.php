<?php

namespace Breakeneck\Http;

interface iTransport
{
    public function __construct($opts = []);

    /**
     * @param Request $request
     * @return Response
     */
    public function send(Request $request): Response;
}
