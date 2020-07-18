<?php
namespace common\assets;

use Binance\API;

class BinanceApi extends API
{
    protected $sapi = 'https://api.binance.com/';
    private $subscriptions = []; // /< View all websocket subscriptions
    static $proxy=[];
    /**
     * httpRequest curl wrapper for all http api requests.
     * You can't call this function directly, use the helper functions
     *
     * @see buy()
     * @see sell()
     * @see marketBuy()
     * @see marketSell() $this->httpRequest( "https://api.binance.com/api/v1/ticker/24hr");
     *
     * @param $url string the endpoint to query, typically includes query string
     * @param $method string this should be typically GET, POST or DELETE
     * @param $params array addtional options for the request
     * @param $signed bool true or false sign the request with api secret
     * @return array containing the response
     * @throws \Exception
     */
    private function httpRequest(string $url, string $method = "GET", array $params = [], bool $signed = false)
    {
        $real_base=$this->base;
        if(strpos($url,'sapi/')!==false)
            $real_base=$this->sapi;

        if (function_exists('curl_init') === false) {
            throw new \Exception("Sorry cURL is not installed!");
        }

        if ($this->caOverride === false) {
            if (file_exists(getcwd() . '/ca.pem') === false) {
                $this->downloadCurlCaBundle();
            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, $this->httpDebug);
        $query = http_build_query($params, '', '&');

        // signed with params
        if ($signed === true) {
            if (empty($this->api_key)) {
                throw new \Exception("signedRequest error: API Key not set!");
            }

            if (empty($this->api_secret)) {
                throw new \Exception("signedRequest error: API Secret not set!");
            }

            $base = $real_base;
            $ts = (microtime(true) * 1000) + $this->info['timeOffset'];
            $params['timestamp'] = number_format($ts, 0, '.', '');
            if (isset($params['wapi'])) {
                unset($params['wapi']);
                $base = $this->wapi;
            }
            $query = http_build_query($params, '', '&');
            $signature = hash_hmac('sha256', $query, $this->api_secret);
            if ($method === "POST") {
                $endpoint = $base . $url;
                $params['signature'] = $signature; // signature needs to be inside BODY
                $query = http_build_query($params, '', '&'); // rebuilding query
            } else {
                $endpoint = $base . $url . '?' . $query . '&signature=' . $signature;
            }

            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-MBX-APIKEY: ' . $this->api_key,
            ));
        }
        // params so buildquery string and append to url
        else if (count($params) > 0) {
            curl_setopt($curl, CURLOPT_URL, $real_base . $url . '?' . $query);
        }
        // no params so just the base url
        else {
            curl_setopt($curl, CURLOPT_URL, $real_base . $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-MBX-APIKEY: ' . $this->api_key,
            ));
        }
        curl_setopt($curl, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; PHP Binance API)");
        // Post and postfields
        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        }
        // Delete Method
        if ($method === "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        // PUT Method
        if ($method === "PUT") {
            curl_setopt($curl, CURLOPT_PUT, true);
        }

        // proxy settings
        if (is_array($this->proxyConf)) {
            curl_setopt($curl, CURLOPT_PROXY, $this->getProxyUriString());
            if (isset($this->proxyConf['user']) && isset($this->proxyConf['pass'])) {
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->proxyConf['user'] . ':' . $this->proxyConf['pass']);
            }
        }
        //my own proxy
        if(!empty(self::$proxy)){
            curl_setopt($curl, CURLOPT_PROXY, self::$proxy['address']);
            if(trim( self::$proxy['username'])!='' && trim( self::$proxy['password'])!=''){
                curl_setopt($curl, CURLOPT_PROXYUSERPWD,  self::$proxy['username'] . ':' .  self::$proxy['password']);
            }
        }

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // headers will proceed the output, json_decode will fail below
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        // set user defined curl opts last for overriding
        foreach ($this->curlOpts as $key => $value) {
            curl_setopt($curl, constant($key), $value);
        }

        if ($this->caOverride === false) {
            if (file_exists(getcwd() . '/ca.pem') === false) {
                $this->downloadCurlCaBundle();
            }
        }
        $output = curl_exec($curl);

        // Check if any error occurred
        if (curl_errno($curl) > 0) {
            // should always output error, not only on httpdebug
            // not outputing errors, hides it from users and ends up with tickets on github
            echo 'Curl error: ' . curl_error($curl) . "\n";
            return [];
        }
        curl_close($curl);
        $json = json_decode($output, true);
        if (isset($json['msg'])) {
            // should always output error, not only on httpdebug
            // not outputing errors, hides it from users and ends up with tickets on github
            echo "signedRequest error: {$output}" . PHP_EOL;
        }
        $this->transfered += strlen($output);
        $this->requestCount++;
        return $json;
    }


    public function margin_balances($priceData = false)
    {
        if (is_array($priceData) === false) {
            $priceData = false;
        }

        $account = $this->httpRequest("sapi/v1/margin/account", "GET", [], true);

        if (is_array($account) === false) {
            echo "Error: MARGIN unable to fetch your account details" . PHP_EOL;
        }

        if (isset($account['userAssets']) === false) {
            echo "Error: MARGIN your balances were empty or unset" . PHP_EOL;
        }
        return $account;
        //старая функция для банасов
        //return $this->balanceData($account, $priceData);
    }

    public function userDataMargin(&$balance_callback, &$execution_callback = false)
    {
        $response = $this->httpRequest("sapi/v1/userDataStream", "POST", []);
        $this->listenKey = $response['listenKey'];
        $this->info['balanceCallback'] = $balance_callback;
        $this->info['executionCallback'] = $execution_callback;

        $this->subscriptions['@userdata_margin'] = true;

        // @codeCoverageIgnoreStart
        // phpunit can't cover async function
        \Ratchet\Client\connect($this->stream . $this->listenKey)->then(function ($ws) {
            $ws->on('message', function ($data) use ($ws) {
                if ($this->subscriptions['@userdata_margin'] === false) {
                    //$this->subscriptions[$endpoint] = null;
                    $ws->close();
                    return; //return $ws->close();
                }
                $json = json_decode($data);
                $type = $json->e;
                if ($type === "outboundAccountInfo") {
                    $balances = ($json->B);
                    $this->info['balanceCallback']($this, $balances);
                } elseif ($type === "executionReport") {
                    $report = ($json);
                    if ($this->info['executionCallback']) {
                        $this->info['executionCallback']($this, $report);
                    }
                }
            });
            $ws->on('close', function ($code = null, $reason = null) {
                // WPCS: XSS OK.
                echo "userdata_margin: WebSocket Connection closed! ({$code} - {$reason})" . PHP_EOL;
            });
        }, function ($e) {
            // WPCS: XSS OK.
            echo "userdata_margin: Could not connect: {$e->getMessage()}" . PHP_EOL;
        });
        // @codeCoverageIgnoreEnd
    }

    public function marginOrder(string $side, string $symbol, $quantity, $price, string $type = "LIMIT", array $flags = [], bool $test = false)
    {
        $opt = [
            "symbol" => $symbol,
            "side" => $side,
            "type" => $type,
            "quantity" => $quantity,
            "recvWindow" => 60000,
        ];

        // someone has preformated there 8 decimal point double already
        // dont do anything, leave them do whatever they want
        if (gettype($price) !== "string") {
            // for every other type, lets format it appropriately
            $price = number_format($price, 8, '.', '');
        }

        if (is_numeric($quantity) === false) {
            // WPCS: XSS OK.
            echo "warning: quantity expected numeric got " . gettype($quantity) . PHP_EOL;
        }

        if (is_string($price) === false) {
            // WPCS: XSS OK.
            echo "warning: price expected string got " . gettype($price) . PHP_EOL;
        }

        if ($type === "LIMIT" || $type === "STOP_LOSS_LIMIT" || $type === "TAKE_PROFIT_LIMIT") {
            $opt["price"] = $price;
            $opt["timeInForce"] = "GTC";
        }

        if (isset($flags['stopPrice'])) {
            $opt['stopPrice'] = $flags['stopPrice'];
        }

        if (isset($flags['icebergQty'])) {
            $opt['icebergQty'] = $flags['icebergQty'];
        }
        if (isset($flags['sideEffectType'])) {
            $opt['sideEffectType'] = $flags['sideEffectType'];
        }

        if (isset($flags['newOrderRespType'])) {
            $opt['newOrderRespType'] = $flags['newOrderRespType'];
        }

        $qstring = ($test === false) ? "sapi/v1/margin/order" : "v3/order/test";
        return $this->httpRequest($qstring, "POST", $opt, true);
    }

    public function cancelMargin(string $symbol, $orderid, $flags = [])
    {
        $params = [
            "symbol" => $symbol,
            "orderId" => $orderid,
        ];
        return $this->httpRequest("sapi/v1/margin/order", "DELETE", array_merge($params, $flags), true);
    }
    public function orderStatusMargin(string $symbol, $orderid)
    {
        return $this->httpRequest("sapi/v1/margin/order", "GET", [
            "symbol" => $symbol,
            "orderId" => $orderid,
        ], true);
    }
	
	public function loan(string $symbol, $amount) {
		
		$opt = [
            "asset" => $symbol,
            "amount" => $amount
        ];
		
		 $qstring = "/sapi/v1/margin/loan";
		 return $this->httpRequest($qstring, "POST", $opt, true);
	}
	
	public function repay(string $symbol, $amount) {
		
		$opt = [
            "asset" => $symbol,
            "amount" => $amount
        ];
		
		 $qstring = "/sapi/v1/margin/repay";
		 return $this->httpRequest($qstring, "POST", $opt, true);
	}
}