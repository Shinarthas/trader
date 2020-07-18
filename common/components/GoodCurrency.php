<?php


namespace common\components;


use common\assets\BinanceApi;
use common\models\Currency;
use common\models\Proxy;
use yii\helpers\ArrayHelper;
function array_key_first(array $array) { foreach ($array as $key => $value) { return $key; } }
class GoodCurrency
{
    public static function writeRate(Currency$currency){

        ob_start();// do not show errors of missing key secret pair
        //BinanceApi::$proxy=ArrayHelper::toArray(Proxy::top());
        $api = new BinanceApi();
        //TODO: �������� �� ������ �� �� � �������� �� ������
        $currency_pair=$currency->symbol.'USDT';

        $depth = $api->depth($currency_pair);
        $content = ob_get_contents();
        //костыль

// отключаем и очищаем буфер
        ob_end_clean();

        return [
            'buy_price' => array_key_first($depth['asks']),
            'sell_price' => array_key_first($depth['bids'])
        ];
    }
}