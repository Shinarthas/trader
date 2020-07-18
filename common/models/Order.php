<?php

namespace common\models;

use common\components\BinanceExchange;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $account_id
 * @property int $status
 * @property int $sell
 * @property float $tokens_count
 * @property float $rate
 * @property int $progress
 * @property string $data_json
 * @property string|null $external_id
 * @property string|null $market_order_id
 * @property int|null $canceled
 * @property int $time
 * @property int $created_at
 * @property int $loaded_at
 * @property float|null $start_rate
 * @property int $currency_one
 * @property int $currency_two
 * @property int|null $is_user
 * @property int|null $is_downgrade
 * @property float|null $local_max
 */
class Order extends \yii\db\ActiveRecord
{
    public static $currency_to_usdt=null;

    const STATUS_NEW = 0;
    //const STATUS_STARTED = 1;
    const STATUS_CREATED = 2;
    const STATUS_PRICE_ERROR = 3;
    const STATUS_CANCELED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_ACCOUNT_NOT_FOUND = 6;
    const STATUS_VALUE_ERROR = 7;
    const STATUS_FAILED = 8;
	const STATUS_POSITION_STOPPED = 9;


    public static $statuses = [
        self::STATUS_NEW => 'new',
        //self::STATUS_STARTED => 'started',
        self::STATUS_CREATED => 'created',
        self::STATUS_PRICE_ERROR => 'error',
        self::STATUS_CANCELED => 'canceled by system',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_ACCOUNT_NOT_FOUND => 'account not found',
        self::STATUS_VALUE_ERROR => 'too low order',
        self::STATUS_FAILED => 'transaction failed',
		self::STATUS_POSITION_STOPPED => 'position stopped',
    ];
    //выполняет ордер
    public function make($additional_info=[]) {
        $currency_one=Currency::findOne($this->currency_one);
        $currency_two=Currency::findOne($this->currency_two);
        //возьмем список валюты к доллару
        $usdt_remapped=CurrencyToUsd::getUsdtRatesRemapped();

        $this->status = self::STATUS_CREATED;

        $this->save();

        //если ставка вышла меньше доллра
        if(isset($usdt_remapped[$currency_one->symbol]) && $usdt_remapped[$currency_one->symbol]['rate']*$this->tokens_count<1){
            //Log::log(['msg'=>'Order total less than 1 dollar abort','order'=>ArrayHelper::toArray($this),'rates'=>ArrayHelper::toArray($usdt_remapped[$currency_one->symbol])],'warning','task');
            $this->status = self::STATUS_VALUE_ERROR;
            $this->save();
            return 0;
        }

        $account=Account::findOne($this->account_id);

		$exchanger = '\\common\\components\\'.$account->exchanger->class;
        $result = $exchanger::marginOrder($currency_one,$currency_two,$this->tokens_count,$this->rate,$account,$this->sell,$additional_info);
        if(isset($result['clientOrderId']) || isset($result['id'])) {
            $this->status = self::STATUS_CREATED;
            $this->external_id=isset($result['orderId'])?$result['orderId']:$result['id'];

            $this->progress = intval($result['executedQty']/$this->tokens_count*100);
            $this->created_at = time();
            $this->market_order_id=$result['clientOrderId'];
            Log::log($result,'info');
            //обновим инфу
            AccountBalance::checkBalance($account);

        }else{
            $this->status=self::STATUS_PRICE_ERROR;
            Log::log($result,'error');
        }
        $this->data=$result;

        $this->save();

        return $result;
    }

    //выполнить маркет ордер
    public function makeMarket($additional_info=[]) {
        $currency_one=Currency::findOne($this->currency_one);
        $currency_two=Currency::findOne($this->currency_two);
        //возьмем список валюты к доллару
        $usdt_remapped=CurrencyToUsd::getUsdtRatesRemapped();

        $this->status = self::STATUS_CREATED;

        $this->save();

        //если ставка вышла меньше доллра
        if(isset($usdt_remapped[$currency_one->symbol]) && $usdt_remapped[$currency_one->symbol]['rate']*$this->tokens_count<1){
            //Log::log(['msg'=>'Order total less than 1 dollar abort','order'=>ArrayHelper::toArray($this),'rates'=>ArrayHelper::toArray($usdt_remapped[$currency_one->symbol])],'warning','task');
            $this->status = self::STATUS_VALUE_ERROR;
            $this->save();
            return 0;
        }

        $account=Account::findOne($this->account_id);
		
		$exchanger = '\\common\\components\\'.$account->exchanger->class;
        $result = $exchanger::marginMarketOrder($currency_one,$currency_two,$this->tokens_count,$account,$this->sell,$additional_info, 'market');
        if(isset($result['clientOrderId']) || isset($result['id'])) {
            $this->status = self::STATUS_CREATED;
            $this->external_id=isset($result['orderId'])?$result['orderId']:$result['id'];

            $this->progress = intval($result['executedQty']/$this->tokens_count*100);
            if($this->progress>=99.8)
                $this->status=self::STATUS_COMPLETED;
            $this->created_at = time();
            $this->market_order_id=$result['clientOrderId'];
            Log::log($result,'info');
            //обновим инфу
            AccountBalance::checkBalance($account);
        }else{
            $this->status=self::STATUS_PRICE_ERROR;
            Log::log($result,'error');
        }
        $this->data=$result;

        $this->save();

        return $result;
    }

    public function checkStatus(){
		$account=Account::findOne($this->account_id);
		
		$exchanger = '\\common\\components\\'.$account->exchanger->class;
        $response = $exchanger::marginOrderStatus($this);
		
        if(isset($response['status'])){
            if($response['status']=='FILLED'){
                $this->data=$response;
                $this->status=self::STATUS_COMPLETED;

                $this->save();
            }
            if($response['status']=='CANCELED'){
                $this->data=$response;
                $this->status=self::STATUS_CANCELED;

                $this->save();
            }

            if($response['status']=='NEW'){
                //ослеживать локальный максимум и писать тайк профит или стоплос
                $trading_pair=Currency::findOne($this->currency_one)->symbol.Currency::findOne($this->currency_two)->symbol;
                $pair=GlobalPair::find()->where(['trading_pair'=>$trading_pair])->orderBy('id desc')->limit(1)->one();

                if($pair->bid>$this->local_max)
                    $this->local_max=$pair->bid;
                //account
                $account=Account::findOne($this->account_id);
                if($pair->bid>$this->start_rate*$account->take_profit){
                    //Отправить уведомление
                    Notification::make('profit');
                    echo $pair->bid." ".$this->start_rate."profit";
                }
                if($pair->bid<$this->start_rate*$account->stop_loss){
                    //Отправить уведомление
                    Notification::make('loss');
                    echo $pair->bid." ".$this->start_rate." loss";
                }
                $this->data=$response;
                $this->status=self::STATUS_CREATED;

                $this->save();

                //если это был LIMIT и вышло время
                if($this->sell){
                    //таймаут у нас в часах
                    if($account->order_timeout_close*3600+strtotime($this->created_at)>time())
                        $this->cancel();
                }else{
                    if($account->order_timeout_open*3600+strtotime($this->created_at)>time())
                        $this->cancel();
                }
            }
        }else{
            $this->data=$response;
            $this->status=self::STATUS_PRICE_ERROR;

            $this->save();
        }

    }

    //проверяет статуты незавршенных ордеров
    public static function checkOrders(){
        $orders=Order::find()
            ->where(['in','status',[self::STATUS_CREATED]])->all();

        foreach ($orders as $order){
            //проверить статус этого ордера
            $order->checkStatus();
        }
    }

    public function cancel(){
		$account=Account::findOne($this->account_id);
		
		$exchanger = '\\common\\components\\'.$account->exchanger->class;
        $response=$exchanger::cancelMarginOrder($this);
        //print_r($response);
        if(isset($response['status']) && $response['status']=='CANCELED') {
            $this->progress = floatval($response['executedQty']) / floatval($response['origQty']) * 100;
            $this->status = Order::STATUS_CANCELED;
            $this->external_id = $response['orderId'];
            $this->market_order_id = $response['clientOrderId'];
            $this->canceled = 1;
            $this->progress = intval($response['executedQty']/$this->tokens_count*100);
            $this->data=$response;
            $this->save();

            //оюновим балансы
            AccountBalance::checkBalance(Account::findOne($this->account_id));
        }else{
            $this->status=Order::STATUS_FAILED;
            $this->data=$response;
            $this->save();
        }
        Notification::make(Yii::$app->user->identity->username." canceled order ".$this->id." ");
        return $response;

    }
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'status', 'sell', 'tokens_count', 'rate', 'progress', 'data_json', 'time', 'created_at', 'loaded_at'], 'required'],
            [['account_id','currency_one', 'currency_two', 'status', 'sell', 'progress', 'canceled', 'time', 'created_at', 'loaded_at', 'is_user','is_downgrade'], 'integer'],
            [['tokens_count', 'rate', 'start_rate','local_max'], 'number'],
            [['data_json'], 'string'],
            [[ 'market_order_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'status' => 'Status',
            'sell' => 'Sell',
            'tokens_count' => 'Tokens Count',
            'rate' => 'Rate',
            'progress' => 'Progress',
            'data_json' => 'Data Json',
            'external_id' => 'External ID',
            'market_order_id' => 'Market Order ID',
            'canceled' => 'Canceled',
            'time' => 'Time',
            'created_at' => 'Created At',
            'loaded_at' => 'Loaded At',
            'start_rate' => 'Start Rate',
            'currency_one' => 'Currency One',
            'currency_two' => 'Currency Two',
            'is_user' => 'Is User',
            'is_downgrade' => 'Is Downgrade',
            'local_max' => 'Local Max',
        ];
    }
    public function getData($assoc = true)
    {
        return json_decode($this->data_json,$assoc);
    }

    public function setData($data)
    {
        $this->data_json = json_encode($data);
    }
    public function getCurrencyOne(){
        return $this->hasOne(Currency::className(), ['id' => 'currency_one']);
    }

    public function getCurrencyTwo(){
        return $this->hasOne(Currency::className(), ['id' => 'currency_two']);
    }
}
