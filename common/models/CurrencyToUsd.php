<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "currency_to_usd".
 *
 * @property int $id
 * @property int $currency
 * @property int $rate
 * @property int $created_at
 */
class CurrencyToUsd extends \yii\db\ActiveRecord
{
    public static $usdtRatesRemapped=[];
    public static function getUsdtRatesRemapped(){
        if(!empty(self::$usdtRatesRemapped))
            return self::$usdtRatesRemapped;
        $last_timestamp=CurrencyToUsd::find()
            ->orderBy('id desc')->limit(1)->one();

        $usdt_rates=CurrencyToUsd::find(['created_at'=>$last_timestamp->created_at])
            ->select('currency_to_usd.*, c.symbol')
            ->leftJoin('currency as c','c.id=currency_to_usd.currency')
            ->asArray()->all();

        $usdt_rates_remapped=[];
        foreach ($usdt_rates as $currency){
            $usdt_rates_remapped[$currency['symbol']]=$currency;
        }
        self::$usdtRatesRemapped=$usdt_rates_remapped;
        return self::$usdtRatesRemapped;
    }
    //ключ - id валюты
    public static $currencies=[];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_to_usd';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency', 'created_at'], 'required'],
            [['currency', 'created_at'], 'integer'],
            [['rate'],'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currency' => 'Currency',
            'rate' => 'Rate',
            'created_at' => 'Created At',
        ];
    }
    public static function getRate($currency_id){
        if(isset(self::$currencies[$currency_id]))
            return self::$currencies[$currency_id]->rate;
        else{
            $res=CurrencyToUsd::find()->where(["currency"=>$currency_id])->orderBy('created_at desc')->limit(1)->one();
            if($res){
                self::$currencies[$currency_id]=$res;
                return $res->rate;
            }


            return 0;
        }

    }
    public static function getRateDate($currency_id,$timestamp){
        $res=CurrencyToUsd::find()->where(["currency"=>$currency_id])
            ->andWhere(['<','created_at',$timestamp])
            ->orderBy('created_at desc')->limit(1)->one();
        if($res)
            return $res->rate;

        return 0;
    }
    public static function writeRate($currency_id){
        $currency=Currency::findOne($currency_id);

        $class = '\\common\\components\\' .$currency->class;
        if (class_exists($class)) {
            $rate=$class::writeRate($currency);

            $ctd=new CurrencyToUsd();
            $ctd->currency=$currency->id;
            $ctd->rate=($rate['sell_price']+$rate['buy_price'])/2;
            $ctd->created_at=time();
            $ctd->save();

            return $rate;
        }else{
            return 0;
        }

    }
    public static function findByTimestamp($currency_id,$timestamp){
        $res=CurrencyToUsd::find()->where(["currency"=>$currency_id])
            ->andWhere(['<','created_at',$timestamp])
            ->orderBy('created_at desc')->limit(1)->one();
        if($res)
            return $res->rate;

        return 0;
    }


}
