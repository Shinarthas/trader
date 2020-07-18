<?php


namespace common\components;


use common\assets\BinanceApi;
use common\models\Currency;
use common\models\Proxy;
use yii\helpers\ArrayHelper;

class UsdtCurrency
{
    public static function writeRate(Currency $currency){
        return [
            'buy_price' => 1,
            'sell_price' => 1
        ];
    }
}