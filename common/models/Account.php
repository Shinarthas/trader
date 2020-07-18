<?php

namespace common\models;

use Yii;
use common\components\BinanceExchange;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $strategy_id
 * @property int $group_id
 * @property string $data_json
 * @property int $created_at
 * @property int|null $sum_st
 * @property int|null $market
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
 * @property float|null $deposit
 * @property int|null $in_position
 * @property string|null $currency
 * @property int|null $is_downgrade
 * @property float|null $take_profit
 * @property float|null $stop_loss
 */
class Account extends \yii\db\ActiveRecord
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

    //минимальный размер ставки в долларах
	const MIN_ORDER_VALUE = 2;



	const STATUS_INACTIVE = 0;
	const STATUS_AVAILABLE = 1;
	const STATUS_PURCHASING = 2;
	const STATUS_SELLING = 3;
	const STATUS_IN_POSITION = 4;
	const STATUS_DISABLED = 5;
	const STATUS_HIDDEN = 6;

	public static $statuses = [
		self::STATUS_INACTIVE => 'неактивен',
		self::STATUS_AVAILABLE => 'доступен',
		self::STATUS_PURCHASING => 'покупка',
		self::STATUS_SELLING => 'продажа',
		self::STATUS_IN_POSITION => 'в позиции',
		self::STATUS_DISABLED => 'отключен',
		self::STATUS_HIDDEN => 'скрыт',
	];
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'group_id', 'created_at', 'access_key', 'secret_key'], 'required'],
            [['status','strategy_id', 'group_id', 'created_at', 'sum_st', 'order_open_type', 'order_timeout_open', 'order_close_type', 'order_timeout_close','market'], 'integer'],
            [['sum_fix', 'percent_sum', 'shoulder', 'shoulder_add','limited_order_percent_open' ,'order_open_threshold_open', 'shoulder_down', 'limited_order_percent_close', 'order_open_threshold_close','deposit','take_profit', 'stop_loss'], 'number'],
            [['data_json'], 'string'],
            [['name', 'pair', 'currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'status' => 'Status',
            'strategy_id' => 'strategy',
            'group_id' => 'Группа',
            'data_json' => 'Data Json',
            'created_at' => 'Created At',
            'market' => 'Биржа',
			'access_key' => 'Access Key',
			'secret_key' => 'Secret Key',
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
            'deposit' => 'Депозит',
            'in_position' => 'В позиции',
            'currency' => 'Валюта позиции',
            'is_downgrade' => 'Is Downgrade',
            'take_profit' => 'Take Profit',
            'stop_loss' => 'Stop Loss',
        ];
    }
	
	public function verify() {
        if($this->last_balance==null){
            $this->status = self::STATUS_AVAILABLE;
            $this->save();
            return;
        }
		$main_balance = $this->last_balance->getMargin_main_balance(5);

		//посчитаем тоталы для каждой валюты и оценим их
		$usdt_total=0;
		$others_total=0;
		foreach ($main_balance as $balance){
		    if($balance['symbol']=='USDT')
                $usdt_total+=$balance['free'];
		    else
                $others_total+=$balance['free']*$balance['rate'];
        }

        //выведем дополнительное сообщение по надобности
        $error='';
        if(count($main_balance)==0){
			$error='Balance of margin account is clear';
			$this->status = self::STATUS_INACTIVE;
            $this->save();
            return ['status'=>true, 'error'=>$error];
		}
            
        if(count($main_balance)>1)
            $error='Exchange your coins to USDT to start use this account';

        //если больше долларов то аккаунт доступен
        if($usdt_total>$others_total){
            $this->status = self::STATUS_AVAILABLE;
            $this->save();
            return ['status'=>true, 'error'=>$error];
        }else{
            $this->status = self::STATUS_IN_POSITION;
            $this->save();

            return ['status'=>true, 'error'=>$error];
        }



	}
	public function checkStatusByBalance() {
		if(!$a_b =AccountBalance::find()->where(['status'=>1,"account_id"=>$this->id])->one()) {
			$this->status = self::STATUS_INACTIVE;
            $this->save();
            return ['status'=>true];
		}
		
		$main_balance = $a_b->getMargin_main_balance(5);
        //посчитаем тоталы для каждой валюты и оценим их
        $usdt_total=0;
        $others_total=0;
        foreach ($main_balance as $balance){
            if($balance['symbol']=='USDT')
                $usdt_total+=$balance['free'];
            else
                $others_total+=$balance['free']*$balance['rate'];
        }
        //если больше долларов то аккаунт доступен
        if($usdt_total>$others_total){
            $this->status = self::STATUS_AVAILABLE;
            $this->save();
            return ['status'=>true];
        }else{
            $this->status = self::STATUS_IN_POSITION;
            $this->save();

            return ['status'=>true];
        }
	}
	
	public function purchase(Currency $currency,$shoulder=1.0,$name='') {

        //["USDT"=>"Success"];
		$resp=[];//массив ответов ключ валюьа, значение мообщение

		//из рачета что мы торгуем USDT
        $usdt_balance=[];
        foreach ($this->margin_main_balance as $balance){
            if($balance['symbol']=='USDT'){
                $usdt_balance=$balance;
                break;
            }
        }

        //не достаточно монеты для сделки
        if (empty($usdt_balance)){
            $resp['USDT']=["msg"=>"not enough balance", 'status'=>'FAILED'];
            return $resp;
        }
        //еслм меньше минималки
        if($usdt_balance['free']*$usdt_balance['rate']<self::MIN_ORDER_VALUE){
            $resp['USDT']=["msg"=>"Not enough FREE tokens", 'status'=>'FAILED'];
            return $resp;
        }
        //посмотрим на стретегию если мы не можем поставить депозит с плечом
        if($usdt_balance['free']*$usdt_balance['rate']*$this->shoulder<$this->deposit){
            $resp['USDT']=["msg"=>"Bad Strategy", 'status'=>'FAILED'];
            return $resp;
        }

        //возьмем стратегию


        //делаем маркет ордер на всю котлету
        $order=new Order();
        $order->account_id=$this->id;
        $order->status=0;
        $order->sell=0;

        $order->progress=0;
        $order->data_json="[]";
        $order->time=time();
        $order->created_at=time();
        $order->loaded_at=time();
        $order->currency_one=intval($currency->id);
        $order->currency_two=Currency::findOne(['symbol'=>'USDT'])->id;
        //у нас есть доллары но количество монет должно быть в первой валюте, найдем цену и перезапишем
        //так как это доллар можно взять из Currency_to_usd, если что-то поменяется то из global_pair
        $trasfer_rate=CurrencyToUsd::find()->where(['currency'=>$currency->id])->orderBy('id desc')->limit(1)->one();
        $order->tokens_count=$usdt_balance['free']/$trasfer_rate->rate*0.99*$shoulder;//переведем доллары в монеты,играем на все
        $order->rate=0;//так как это будет маркет
        $order->is_user=0;

        $order->start_rate=$trasfer_rate->rate;
        $order->local_max=$trasfer_rate->rate;
        $order->save();



        //ЗАМУТИМ БИНАНС
        ob_start();
        if($this->order_open_type==1){//Limit
            $order->rate=$trasfer_rate->rate*$this->limited_order_percent_open;//так как это будет маркет

            $order->save();
            $order_info=$order->make(['sideEffectType'=>'MARGIN_BUY']);
        }else{//Market
            $order_info=$order->makeMarket(['sideEffectType'=>'MARGIN_BUY']);
        }

        ob_end_clean();

        $resp['USDT']=$order_info;

		return $resp;
	}
	//продаем все монеты что у нас есть
	public function sell($name='',$auto_repay=1) {
        $resp=[];//массив ответов ключ валюьа, значение мообщение
		
        foreach (AccountBalance::find()->where(['status'=>1,"account_id"=>$this->id])->one()->getMargin_main_balance(0) as $balance){
			
			if(($balance['free']+$balance['amount'])*$balance['rate']<0.1){
				continue;
			}
			
			$market_order = false;
			
			if($balance['symbol']=="USDT" || $balance['symbol']=="USD")
				continue;
			
			if($balance['in_position']==1) {
				$market_order = true;
			/*	foreach(Order::find()->where(['currency_one'=>$balance['currency'], 'account_id'=>$this->id, 'status'=>Order::STATUS_CREATED])->all() as $order)
					$order->stop_position();
					
				continue;*/
			}
			
            if(($balance['free']+$balance['amount'])*$balance['rate']<self::MIN_ORDER_VALUE){
                $resp[$balance['symbol']] = ['msg' => "Not enough FREE tokens", 'status'=>'FAILED'];
                continue;
            }
            $order=new Order();
            $order->account_id=$this->id;
            $order->status=0;
            $order->sell=1;

            $order->progress=0;
            $order->data_json="[]";
            $order->time=time();
            $order->created_at=time();
            $order->loaded_at=time();
            $order->currency_one=$balance['currency'];
            $order->currency_two=Currency::findOne(['symbol'=>'USDT'])->id;

            $order->tokens_count=$balance['free']+$balance['amount'];
            $order->rate=0;
            $order->is_user=0;
            $order->save();

            //ЗАМУТИМ БИНАНС
			ob_start();
		   
            if($this->order_close_type==1 AND !$market_order){//Limit

                //найдем рейты этой пары сейчас
                $global_pair=GlobalPair::find()
                    ->where(['trading_pair'=>Currency::findOne($order->currency_one)->symbol.Currency::findOne($order->currency_two)->symbol])
                    ->orderBy('id desc')->limit(1)->one();
                //если такой пары нет то ошибка
                if(empty($global_pair)){
                    $order->data=['msg'=>"Missing pair in DataBase"];
                    $order->save();
                    $resp[$balance['symbol']] = ['msg' => "Missing pair in DataBase", 'status'=>'FAILED'];
                }

                $order->start_rate=$global_pair->bid;
                $order->local_max=$global_pair->bid;
                $order->save();
                $order->rate=$global_pair->bid*$this->limited_order_percent_close;//так как это будет маркет

                $order->save();
                $order_info=$order->make(['sideEffectType'=>'AUTO_REPAY']);
            }else{//Market
                $order_info=$order->makeMarket(['sideEffectType'=>'AUTO_REPAY']);
            }
            $output = ob_get_contents();
            ob_end_clean();

            $resp[$balance['symbol']]=$order_info;

        }
		return $resp;
	}


	//функция на понижение, берет в долг валюту которая должна опасть и и продает ее к основной
    public function short_sell(Currency $currency){
        //возьмем депосит (он у нас в долларах и попробуем приести его к валюте которую хотим занять)
        //["USDT"=>"Success"];
        $resp=[];//массив ответов ключ валюьа, значение мообщение
        //берем валюту которую хотим занять
        $currency_to_usd=CurrencyToUsd::getUsdtRatesRemapped();
        if(!isset($currency_to_usd[$currency->symbol])){
            $resp['USDT']=["msg"=>"Missing Currency", 'status'=>'FAILED'];
            return $resp;
        }
        //сколько монет нам нужно занять, с учетом плеча вниз, расчет по стратегии
        $amount_to_loan=$this->deposit/$currency_to_usd[$currency->symbol]['rate']*$this->shoulder_down;
        //$this->loan($currency,$amount_to_loan);


        //делаем маркет ордер на всю котлету
        $order=new Order();
        $order->account_id=$this->id;
        $order->status=0;
        $order->sell=1;

        $order->progress=0;
        $order->data_json="[]";
        $order->time=time();
        $order->created_at=time();
        $order->loaded_at=time();
        $order->currency_one=intval($currency->id);
        $order->currency_two=Currency::findOne(['symbol'=>'USDT'])->id;
        //у нас есть доллары но количество монет должно быть в первой валюте, найдем цену и перезапишем
        //так как это доллар можно взять из Currency_to_usd, если что-то поменяется то из global_pair
        $trasfer_rate=CurrencyToUsd::find()->where(['currency'=>$currency->id])->orderBy('id desc')->limit(1)->one();
        $order->tokens_count=$amount_to_loan;//переведем доллары в монеты,играем на все
        $order->rate=0;//так как это будет маркет
        $order->is_user=0;

        $order->start_rate=$trasfer_rate->rate;
        $order->local_max=$trasfer_rate->rate;
        $order->save();



        //ЗАМУТИМ БИНАНС
        ob_start();
        if($this->order_open_type==1){//Limit
            $order->rate=$trasfer_rate->rate*$this->limited_order_percent_open;//так как это будет маркет

            $order->save();
            
            $order_info=$order->make(['sideEffectType'=>'MARGIN_BUY']);
        }else{//Market
            $order_info=$order->makeMarket(['sideEffectType'=>'MARGIN_BUY']);
        }

        ob_end_clean();

        $resp['USDT']=$order_info;

        return $resp;

    }

    //взять все валюты по которыс есть долг и погасить
    public function short_purchase($name='',$auto_repay=1) {
        $currency_to_usd=CurrencyToUsd::getUsdtRatesRemapped();
        $resp=[];//массив ответов ключ валюьа, значение мообщение
        foreach ($this->margin_main_balance as $balance){
            if($balance['symbol']=="USDT")
                continue;
            //если задолжености нет, пропускаем
            if($balance['borrowed']<=0)
                continue;
            //если нет инфы о такой валюте то ошибка
            if(!isset($currency_to_usd[$balance['symbol']])){
                $resp[$balance['symbol']] = ['msg' => "Missing pair in DataBase", 'status'=>'FAILED'];
            }

            //если долг меньше  доллара ничего не дделаем MIN_ORDER_VALUE
            if($balance['borrowed']*$balance['rate']<self::MIN_ORDER_VALUE){
                $resp[$balance['symbol']] = ['msg' => "order is less than ".self::MIN_ORDER_VALUE."$", 'status'=>'FAILED'];
                continue;
            }


            $order=new Order();
            $order->account_id=$this->id;
            $order->status=0;
            $order->sell=0;

            $order->progress=0;
            $order->data_json="[]";
            $order->time=time();
            $order->created_at=time();
            $order->loaded_at=time();
            $order->currency_one=$balance['currency'];
            $order->currency_two=Currency::findOne(['symbol'=>'USDT'])->id;

            $order->tokens_count=$balance['borrowed']*1.004;//с учетом комиссии
            $order->rate=0;
            $order->is_user=0;
            $order->save();

            //ЗАМУТИМ БИНАНС
            ob_start();

            if($this->order_close_type==1){//Limit
                //найдем рейты этой пары сейчас
                $global_pair=GlobalPair::find()
                    ->where(['trading_pair'=>Currency::findOne($order->currency_one)->symbol.Currency::findOne($order->currency_two)->symbol])
                    ->orderBy('id desc')->limit(1)->one();
                //если такой пары нет то ошибка
                if(empty($global_pair)){
                    $order->data=['msg'=>"Missing pair in DataBase"];
                    $order->save();
                    $resp[$balance['symbol']] = ['msg' => "Missing pair in DataBase", 'status'=>'FAILED'];
                }

                $order->start_rate=$global_pair->bid;
                $order->local_max=$global_pair->bid;
                $order->save();
                $order->rate=$global_pair->bid*$this->limited_order_percent_close;//так как это будет маркет

                $order->save();
                $order_info=$order->make(['sideEffectType'=>'AUTO_REPAY']);
            }else{//Market
                $order_info=$order->makeMarket(['sideEffectType'=>'AUTO_REPAY']);
            }
            ob_end_clean();
            print_r(ArrayHelper::toArray($order));
            $resp[$balance['symbol']]=$order_info;

        }


        return $resp;
    }

	public function loan() {
		$currency = Currency::findOne(['symbol'=>'USDT']);
		$exchanger = '\\common\\components\\'.$this->exchanger->class;
		
		$exchanger::loan($currency, $this, 10);
	}
	
	public function repay() {
		$currency = Currency::findOne(['symbol'=>'USDT']);
		$exchanger = '\\common\\components\\'.$this->exchanger->class;
		
		$exchanger::repay($currency, $this, 10);
	}
	
	public function getAccess_key() {
		return $this->data['access_key'];
	}
	
	public function setAccess_key($key) {
		$data = $this->data;
		$data['access_key'] = $key;
		$this->data = $data;
	}
	
	public function getSecret_key() {
		return $this->data['secret_key'];
	}
	public function setSecret_key($key) {
		$data = $this->data;
		$data['secret_key'] = $key;
		$this->data = $data;
	}
	
	public function getData($assoc = true)
    {
        return json_decode($this->data_json,$assoc);
    }
	 
    public function setData($data)
    {
        $this->data_json = json_encode($data);
    }
	
	public function getStatus_string() {
		return self::$statuses[$this->status];
	}
	
	public function getLast_balance() {
		return $this->hasOne(AccountBalance::className(), ['account_id' => 'id'])->andWhere(['status'=>1]);
	}
	
	public function getBalances() {
		return $this->hasMany(AccountBalance::className(), ['account_id' => 'id']);
	}

    public function getOrders() {
        return $this->hasMany(Order::className(), ['account_id' => 'id']);
    }
	
	public function getMargin_main_balance() {
		return $this->last_balance->margin_main_balance;
	}
	
	public function getExchanger() {
		return $this->hasOne(Market::className(), ['id'=>'market']);
	}
}
