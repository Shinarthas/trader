<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "strategy".
 *
 * @property int $id
 * @property string|null $created_at
 * @property int|null $sum_st
 * @property float|null $sum_fix
 * @property float|null $percent_sum
 * @property float|null $shoulder
 * @property float|null $shoulder_add
 * @property int|null $order_open_type
 * @property float|null $limited_order_percent_open
 * @property float|null $order_open_threshold_open
 * @property int|null $order_timeout_open
 * @property float|null $shoulder_down
 * @property int|null $order_close_type
 * @property float|null $limited_order_percent_close
 * @property float|null $order_open_threshold_close
 * @property int|null $order_timeout_close
 * @property string|null $pair
 * @property int|null $is_downgrade
 * @property float|null $take_profit
 * @property float|null $stop_loss
 * @property string|null $name
 */
class Strategy extends \yii\db\ActiveRecord
{
    const SUM_ST=[
        '1'=>'Reinvestment',
        '2'=>'Fixed'
    ];
    const ORDER_TYPE=[
        '1'=>'LIMIT',
        '2'=>'MARKET'
    ];
    const MARKETS=[
        '1'=>'BINANCE',
        '2'=>'BITFINEX',
    ];
    const GROUPS = [
        1 => 'Small',
        2 => 'Big',
        3 => 'Other'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'strategy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['sum_st', 'order_open_type', 'order_timeout_open', 'order_close_type', 'order_timeout_close', 'is_downgrade'], 'integer'],
            [['sum_fix', 'percent_sum', 'shoulder', 'shoulder_add', 'limited_order_percent_open', 'order_open_threshold_open', 'shoulder_down', 'limited_order_percent_close', 'order_open_threshold_close', 'take_profit', 'stop_loss'], 'number'],
            [['pair', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'sum_st' => 'Sum St',
            'sum_fix' => 'Sum Fix',
            'percent_sum' => 'Percent Sum',
            'shoulder' => 'Shoulder',
            'shoulder_add' => 'Shoulder Add',
            'order_open_type' => 'Order Open Type',
            'limited_order_percent_open' => 'Limited Order Percent Open',
            'order_open_threshold_open' => 'Order Open Threshold Open',
            'order_timeout_open' => 'Order Timeout Open',
            'shoulder_down' => 'Shoulder Down',
            'order_close_type' => 'Order Close Type',
            'limited_order_percent_close' => 'Limited Order Percent Close',
            'order_open_threshold_close' => 'Order Open Threshold Close',
            'order_timeout_close' => 'Order Timeout Close',
            'pair' => 'Pair',
            'is_downgrade' => 'Is Downgrade',
            'take_profit' => 'Take Profit',
            'stop_loss' => 'Stop Loss',
            'name' => 'Name',
        ];
    }
}
