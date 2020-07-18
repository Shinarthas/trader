<?php

namespace common\models;

use Yii;
use common\components\BinanceExchange;

/**
 * This is the model class for table "global_pair".
 *
 * @property int $id
 * @property string $trading_pair
 * @property double $bid
 * @property double $bids
 * @property double $ask
 * @property double $asks
 * @property string $created_at
 * @property double $rating
 * @property double $price_change
 * @property double $price_change_percent
 * @property double $weighted_avg_price
 * @property double $last_price
 * @property double $last_qty
 * @property double $open_price
 * @property double $high_price
 * @property double $low_price
 * @property double $volume
 * @property double $volume_24h_change
 * @property double $quote_volume
 * @property double $quote_volume_24h_change
 * @property double $prediction
 * @property double $currency_group
 * @property double $result
 */
class GlobalPair extends \yii\db\ActiveRecord
{
    public static $current_rates=null;// remapped
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'global_pair';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bid', 'bids', 'ask', 'asks', 'rating', 'price_change', 'price_change_percent', 'weighted_avg_price', 'last_price', 'last_qty', 'open_price', 'high_price', 'low_price', 'volume', 'volume_24h_change', 'quote_volume', 'quote_volume_24h_change', 'prediction','currency_group','result'], 'number'],
            [['created_at'], 'safe'],
            [['trading_pair'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trading_pair' => 'Trading Pair',
            'bid' => 'Bid',
            'bids' => 'Bids',
            'ask' => 'Ask',
            'asks' => 'Asks',
            'created_at' => 'Created At',
            'rating' => 'Rating',
            'price_change' => 'Price Change',
            'price_change_percent' => 'Price Change Percent',
            'weighted_avg_price' => 'Weighted Avg Price',
            'last_price' => 'Last Price',
            'last_qty' => 'Last Qty',
            'open_price' => 'Open Price',
            'high_price' => 'High Price',
            'low_price' => 'Low Price',
            'volume' => 'Volume',
            'volume_24h_change' => 'Volume 24h Change',
            'quote_volume' => 'Quote Volume',
            'quote_volume_24h_change' => 'Quote Volume 24h Change',
            'prediction' => 'Prediction',
            'currency_group' => 'Currency Group',
            'result' => 'Result',
        ];
    }
    public static function calculateRating($symbol){
        return BinanceExchange::calculateRating($symbol);
    }

    //remapped
    public static function getCurrentRates(){
        if(!is_null(self::$current_rates))
            return self::$current_rates;
        $ts=GlobalPair::find()->orderBy('created_at desc')->limit(1)->one();
        $aq=GlobalPair::find()->where(['created_at'=>$ts->created_at])->all();
        self::$current_rates=[];
        foreach ($aq as $global_pair){
            self::$current_rates[$global_pair->trading_pair]=$global_pair;
        }
        return self::$current_rates;
    }
}
