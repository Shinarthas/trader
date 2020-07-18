<?php


namespace common\assets;


use common\models\CmcCoin;
use common\models\CmcExchange;
use common\models\Currency;
use CoinMarketCap;
use common\models\GlobalCurrency;
use common\models\Market;

class CoinMarketCapApi
{
    public static function info(Currency $currency){
        $cmc = new CoinMarketCap\Api(\Yii::$app->params['coinmarketcap-key']);
        $response = $cmc->partners()->flipsideFCASQuotesLatest(['symbol' => $currency->symbol]);

        print_r($response);
    }

    public static function listAll(){
        $cmc = new CoinMarketCap\Api(\Yii::$app->params['coinmarketcap-key']);
        $response = $cmc->cryptocurrency()->map(['limit' => 10]);
        foreach ($response->data as $c){
            $currency=GlobalCurrency::find()->where(['symbol'=>$c->symbol])->limit(1)->one();
            if(empty($currency)){
                $currency=new GlobalCurrency();
                $currency->created_at=date('Y-m-d H:i:s',time());
            }
            $currency->name=$c->name;
            $currency->symbol=$c->symbol;
            $currency->is_active=$c->is_active;
            $currency->updated_at=date('Y-m-d H:i:s',time());

            $currency->save();
        }
        //print_r($response);
    }
    public static function getAllExchanges(){
        $ch = curl_init();
        $params=http_build_query([
            'key'=>\Yii::$app->params['nomics-key'],
            'start'=>1,
            'limit'=>10,

        ]);
        $url="https://api.nomics.com/v1/exchanges/ticker?".$params;
        curl_setopt($ch, CURLOPT_URL,$url);
        echo $url."\n\n";
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'X-CMC_PRO_API_KEY: '.\Yii::$app->params['coinmarketcap-key'],
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        print  $server_output ;
    }
    public static function markets(){
        $url = 'https://pro-api.coinmarketcap.com/v1/exchange/map';
        $parameters = [
            //'slug' => 'binance'
            'limit'=>5000
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: '.\Yii::$app->params['coinmarketcap-key']
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
// Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $response=(json_decode($response)); // print json decoded response
        curl_close($curl); // Close request
        $time=time();
        foreach ($response->data as $cmc_market){

            $market=CmcExchange::find()->where(['cmc_id'=>$cmc_market->id])->limit(1)->one();

            if(empty($market)){

                $market =new CmcExchange();
                $market->cmc_id=$cmc_market->id;
            }
            $market->name=$cmc_market->name;
            $market->is_active=$cmc_market->is_active;

            $market_inner=Market::find()->where(['name'=>$cmc_market->name])->limit(1)->one();
            if(!empty($market_inner))
                $market->inner_id=$market_inner->id;
            $market->created_at=date('Y-m-d H:i:s',$time);
            $market->image="https://s2.coinmarketcap.com/static/img/exchanges/32x32/$cmc_market->id.png";
            $market->save();

        }
    }

    //used to update info about coins
    public static function coins(){
        $time=time();
        $cmc = new CoinMarketCap\Api(\Yii::$app->params['coinmarketcap-key']);
        $response = $cmc->cryptocurrency()->listingsLatest(['limit'=>5000]);
        foreach ($response->data as $coin_cmc){
            $coin=CmcCoin::find()->where(['cmc_id'=>$coin_cmc->id])->limit(1)->one();

            if(empty($coin)){

                $coin =new CmcCoin();
                $coin->cmc_id=$coin_cmc->id;
            }
            $coin->symbol=$coin_cmc->symbol;

            $currency=Currency::find()->where(['symbol'=>$coin_cmc->symbol])->limit(1)->one();
            if(!empty($currency))
                $coin->inner_id=$currency->id;
            $coin->created_at=date('Y-m-d H:i:s',$time);
            $coin->image="https://s2.coinmarketcap.com/static/img/coins/32x32/".$coin->id.".png";
            $coin->save();
        }
    }
}
