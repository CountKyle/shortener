<?php

use PHPUnit\Framework\TestCase;

class ShortenerTest extends TestCase
{
    public function test_for_long_url_exists()
    {
        $client =  new GuzzleHttp\Client(['http_errors' => false]);

        $res = $client->request('GET', 'http://shortlinks.test/shorten?url=http://www.google.com');

        $this->assertEquals(200, $res->getStatusCode());
    }

    public function test_for_invalid_url()
    {
        $client =  new GuzzleHttp\Client(['http_errors' => false]);

        $res = $client->request('GET', 'http://shortlinks.test/shorten?url=thisisprobablynotaresolvableurl');

        $this->assertEquals(400, $res->getStatusCode());
    }

    public function test_to_create_new_url()
    {
        $client =  new GuzzleHttp\Client(['http_errors' => false]);

        $res = $client->request('GET', 'http://shortlinks.test/shorten?url=http://www.bing.com');

        $this->assertEquals(200, $res->getStatusCode());
    }

    public function test_redirection()
    {
        $client =  new GuzzleHttp\Client(['http_errors' => false, 'allow_redirects' => false]);

        $res = $client->request('GET', 'http://shortlinks.test/qkt880');

        $this->assertEquals(301, $res->getStatusCode());
    }
}
