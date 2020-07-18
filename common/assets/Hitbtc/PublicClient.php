<?php

namespace common\assets\Hitbtc;

use GuzzleHttp\Client as HttpClient;

class PublicClient
{
    protected $host;
    protected $httpClient;

    public function __construct($demo = false)
    {
        if ($demo) {
            $this->host = 'https://demo-api.hitbtc.com';
        } else {
            $this->host = 'https://api.hitbtc.com';
        }
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = new HttpClient([
                'base_uri' => $this->host,
            ]);
        }

        return $this->httpClient;
    }

    public function getSymbols()
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/symbol')->getBody(), true);
    }

    public function getSymbol($symbol)
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/symbol/'.$symbol)->getBody(), true);
    }

    public function getTicker($symbol)
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/ticker/'.$symbol)->getBody(), true);
    }

    public function getTickers()
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/ticker')->getBody(), true);
    }
   
    public function getTrades($symbol)
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/trades/'.$symbol)->getBody(), true);
    }

    public function getOderBook($symbol)
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/orderbook/'.$symbol)->getBody(), true);
    }

    public function getCandles($symbol)
    {
        return json_decode($this->getHttpClient()->get('/api/2/public/candles/'.$symbol)->getBody(), true);
    }
}
