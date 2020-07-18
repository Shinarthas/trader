<?
namespace common\components;
use common\assets\BinanceApi;
use common\models\AccBalance;
use common\models\Account;
use common\models\GlobalPair;
use common\models\Order;
use common\models\Proxy;
use common\models\Task;
use common\models\AccountBalance;
use common\models\Log;
use common\models\Currency;
use common\models\RateHistory;
use common\models\TaskAdditional;
use Binance;

use yii\helpers\ArrayHelper;

class BinanceExchange {
	
	const CONTRACT_ADDRESS = '41b3bddae866b2ce2349bdcb59dfbfa1a75f8552da';

	const BINANCE=3;

	//additional_info это массив который содержит следующие поля
    /*
     * stopPrice	DECIMAL	NO	Used with STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, and TAKE_PROFIT_LIMIT orders.
     * icebergQty	DECIMAL	NO	Used with LIMIT, STOP_LOSS_LIMIT, and TAKE_PROFIT_LIMIT to create an iceberg order.
     * sideEffectType	ENUM	NO	NO_SIDE_EFFECT, MARGIN_BUY, AUTO_REPAY; default NO_SIDE_EFFECT.
     */
	public static function sellMarginOrder(Currency $currency_one, Currency $currency_two,$tokens_count,$price, Account $account,Array $additional_info=[], $type="LIMIT"){
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $res=$api->exchangeInfo();
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $order = $api->marginOrder("SELL",
            $currency_pair,
            self::numberPrecision($res,$currency_pair,$api,$tokens_count,'amount'),
            self::numberPrecision($res,$currency_pair,$api,$price,'price'),
            $type,
            $additional_info);

        return $order;
    }
    public static function buyMarginOrder(Currency $currency_one, Currency $currency_two,$tokens_count,$price, Account $account,Array $additional_info=[],$type="LIMIT"){
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $res=$api->exchangeInfo();
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $order = $api->marginOrder("BUY",
            $currency_pair,
            self::numberPrecision($res,$currency_pair,$api,$tokens_count,'amount'),
            self::numberPrecision($res,$currency_pair,$api,$price,'price'),
            $type,
            $additional_info);

        return $order;
    }
    public static function marginOrder(Currency $currency_one, Currency $currency_two,$tokens_count,$price, Account $account, $sell,Array $additional_info=[],$type="LIMIT"){
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $res=$api->exchangeInfo();
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $order = $api->marginOrder($sell==1?"SELL":'BUY', $currency_pair,
            self::numberPrecision($res,$currency_pair,$api,$tokens_count,'amount'),
            self::numberPrecision($res,$currency_pair,$api,$price,'price'),
            $type,
            $additional_info);
        Log::log([self::numberPrecision($res,$currency_pair,$api,$tokens_count,'amount')],'info','order percision');
        print_r($order);
        return $order;
    }
    public static function marginMarketOrder(Currency $currency_one, Currency $currency_two,$tokens_count, Account $account, $sell,Array $additional_info=[]){
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $res=$api->exchangeInfo();
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $order = self::marginOrder($currency_one,
            $currency_two,
            self::numberPrecision($res,$currency_pair,$api,$tokens_count,'amount'),
            0,
            $account,
            $sell,
            $additional_info,
            'MARKET');

        return $order;
    }

    public static function cancelMarginOrder(Order $order) {

        $account=Account::findOne($order->account_id);



        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $currency_one=Currency::findOne($order->currency_one);
        $currency_two=Currency::findOne($order->currency_two);

        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        $order_id= $order->external_id;//ORDER MARKET ID

        $response = $api->cancelMargin($currency_pair, $order_id);
        return $response;

    }


    public static function marginOrderStatus(Order $order){
        $account=Account::findOne($order->account_id);



        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $currency_one=Currency::findOne($order->currency_one);
        $currency_two=Currency::findOne($order->currency_two);

        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        $order_id= $order->external_id;//ORDER MARKET ID

        $response = $api->orderStatusMargin($currency_pair, $order_id);
        return $response;
    }
	// ������� ���� ������� � �������?


    /* deprecated
	public static function sellOrder($currency_one, $currency_two, $tokens_count, $price, $account) {
		if($account->type!=self::BINANCE)
		    return 0;

		$proxy = $account->proxy;
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        if(isset($currency_two->decimals))
            $tokens_count=$tokens_count* 10**$currency_two->decimals;
        //TODO:support proxy
        $order = $api->sell($currency_pair, $tokens_count, $price);
        return $order;
	}
    */
	
	// ������� ���� ������� � �������?
    /* deprecated
	public static function buyOrder($currency_one, $currency_two, $tokens_count, $price, $account) {

        if($account->type!=self::BINANCE)
            return 0;

        $proxy = $account->proxy;
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        if(isset($currency_two->decimals))
            $tokens_count=$tokens_count* 10**$currency_two->decimals;
        //TODO:support proxy
        $order = $api->buy($currency_pair, $tokens_count, $price);
        return $order;
	}
    */
	
	public static function exchangeRates($currency_one, $currency_two) {
        ob_start();// do not show errors of missing key secret pair

	    $api = new BinanceApi();
        //TODO: �������� �� ������ �� �� � �������� �� ������
        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        $depth = $api->depth($currency_pair);
        $content = ob_get_contents();

// отключаем и очищаем буфер
        ob_end_clean();
        return [
            'buy_price' => array_key_first($depth['asks']),
            'sell_price' => array_key_first($depth['bids'])
        ];
	}
	
	public static function loadUncompletedOrders($account,$order) {
	    if($account->type!=Account::BINANCE)
            return 0;//��� �� ������� �������
        echo $order->id;
        $tmp=ArrayHelper::toArray($account);
        unset($tmp['balances']);
        $data = ApiRequest::accounts('v1/account/load-uncompleted-orders',[
            'account'=>ArrayHelper::toArray($tmp),
            'order'=>ArrayHelper::toArray($order)
        ]);
        if(!$data->status){
            //TODO: handle error from accounts
            return $data->data;
        }
        $order=json_decode(json_encode($data->data),true);//so you know it's a response not AQ model

        //uncomment if we are going to retrive batch not per order
        //foreach($data->data as $order) {

        $query = Order::find()->where(['account_id'=>$account->id, 'sell' => $order['side']=='SELL'?'1':'0','external_id'=>$order['orderId']]);
        if($t = $query->one()){
            $t->progress = floatval($order['executedQty'])/floatval($order['origQty'])*100;

            $t->external_id = $order['orderId'];
            $t->status = Order::STATUS_CREATED;
            if(floatval($t->progress)>=99.8){//consider as finished
                $t->status=Order::STATUS_COMPLETED;
                $t->canceled=1;
            }
            $t->save();
            $res1=ApiRequest::control('v1/task/update', ['id'=>$t->id,'canceled'=>$t->canceled ,'external_id'=>$t->external_id,'progress'=>$t->progress, 'status' => $t->status]);
            $res1=ApiRequest::accounts('v1/orders/update', ['id'=>$t->id,'canceled'=>0 ,'external_id'=>$t->external_id,'progress'=>$t->progress, 'status' => $t->status]);


            //return ['id'=>$t->id,'canceled'=>0 ,'external_id'=>$t->external_id,'progress'=>$t->progress, 'status' => $t->status];
        }

        //}

        return 0;//error or no result;
        //return $data->data;

    }

    public static function loadUncompletedOrdersAndOrderHistory($account) {

        if($account->type!=Account::BINANCE)
            return 0;//��� �� ������� �������
        $data = ApiRequest::accounts('v1/account/load-uncompleted-orders-and-order-history',[
            'account'=>ArrayHelper::toArray($account),
        ]);

        if(!isset($data->status) || !$data->status){
            //print_r(ArrayHelper::toArray($order));
            //print_r($data);
            //TODO: handle error from accounts
            return $data;
        }

        $data=json_decode(json_encode($data->data),true);//so you know it's a response not AQ model

        foreach ($data['open'] as $open_order){

            $o=Order::find()->where(['sell'=>$open_order['side']=='SELL'?'1':0,'account_id'=>$account->id,'external_id'=>$open_order['orderId']])
                ->limit(1)
                //->createCommand()->rawSql;
                ->one();

            if(empty($o)){
                $max_id_tmp=ApiRequest::control('v1/task/get-max',[]);
                $max_id=isset($max_id_tmp->data->id)?$max_id_tmp->data->id:1;

                $o= new Order();
                $o->sell=$open_order['side']=='SELL'?1:0;
                $o->id=$max_id+1;
                $o->promotion_id=0;
                $o->market_id=$account->type;
                $o->account_id=$account->id;
                $o->tokens_count=$open_order['origQty'];
                $o->rate=$open_order['price'];
                $o->progress=$open_order['executedQty']/$open_order['origQty']*100;
                $o->status=2;
                $o->external_id=$open_order['orderId'];
                $o->canceled=0;
                $o->created_at=(int)($open_order['time']/1000);

                //костыль для валют
                $symbol=$open_order['symbol'];
                $currencies=Currency::find()->all();
                foreach($currencies as $currency){
                    $wtf=explode($currency->symbol,$symbol);
                    //если валюта разбила 1 часть и она была в конце
                    if(count($wtf)==2 && $wtf[1]=='' && strpos ( $symbol, $currency->symbol  )>1){
                        $o->currency_two=$currency->id;
                        $o->currency_one=Currency::find()->where(['symbol'=>$wtf[0]])->limit(1)->one()->id;
                    }
                }

                $o->save();
                $res1=ApiRequest::control('v1/task/update', ArrayHelper::toArray($o));
                $res1=ApiRequest::accounts('v1/orders/update', ArrayHelper::toArray($o));
            }elseif ($o->rate!=$open_order['price']){
                $o->tokens_count=$open_order['origQty'];
                $o->rate=$open_order['price'];
                $o->save();
                $res1=ApiRequest::control('v1/task/update', ArrayHelper::toArray($o));
                print_r($res1);
                $res1=ApiRequest::accounts('v1/orders/update', ArrayHelper::toArray($o));
            }

        }
        foreach ($data['history'] as $symbol_orders){
            foreach ($symbol_orders as $open_order){
                $o=Order::find()->where(['sell'=>$open_order['isBuyer']=='1'?'0':1,'account_id'=>$account->id,'external_id'=>$open_order['orderId']])
                    ->limit(1)
                    //->createCommand()->rawSql;
                    ->one();

                if(empty($o)){

                    $max_id_tmp=ApiRequest::control('v1/task/get-max',[]);
                    if(isset($max_id_tmp->data->id))
                        $max_id=$max_id_tmp->data->id;
                    else
                        $max_id=0;

                    $o= new Order();
                    $o->sell=$open_order['isBuyer']=='1'?'0':1;
                    $o->id=$max_id+1;
                    $o->promotion_id=0;
                    $o->market_id=$account->type;
                    $o->account_id=$account->id;
                    $o->tokens_count=$open_order['qty'];
                    $o->rate=$open_order['price'];
                    $o->progress=100;
                    $o->status=5;
                    $o->external_id=$open_order['orderId'];
                    $o->canceled=0;
                    $o->created_at=(int)($open_order['time']/1000);

                    //костыль для валют
                    $symbol=$open_order['symbol'];
                    $currencies=Currency::find()->all();
                    foreach($currencies as $currency){
                        $wtf=explode($currency->symbol,$symbol);
                        //если валюта разбила 1 часть и она была в конце
                        if(count($wtf)==2 && $wtf[1]=='' && strpos ( $symbol, $currency->symbol  )>1){
                            $o->currency_two=$currency->id;
                            $o->currency_one=Currency::find()->where(['symbol'=>$wtf[0]])->limit(1)->one()->id;
                        }
                    }

                    $o->save();
                    $res1=ApiRequest::control('v1/task/update', ArrayHelper::toArray($o));

                    $res1=ApiRequest::accounts('v1/orders/update', ArrayHelper::toArray($o));
                }elseif (floatval($o->rate)!=floatval($open_order['price'])){
                    echo  floatval($o->rate)." ".floatval($open_order['price']);
                    $o->tokens_count=$open_order['qty'];
                    $o->rate=$open_order['price'];
                    $o->save();
                    //print_r($open_order);
                    $res1=ApiRequest::control('v1/task/update', ArrayHelper::toArray($o));
                    $res1=ApiRequest::accounts('v1/orders/update', ArrayHelper::toArray($o));
                }
            }

        }
        //return 0;//error or no result;
        return $data;
    }

    public static function loadOrderHistory($account) {
        if($account->type!=Account::BINANCE)
            return 0;//��� �� ������� �������

        $data = ApiRequest::accounts('v1/account/load-order-history',[
            'account'=>ArrayHelper::toArray($account),
        ]);
        if(!isset($data->status) || !$data->status){
            //print_r(ArrayHelper::toArray($order));
            //print_r($data);
            //TODO: handle error from accounts
            return $data;
        }

        $order=json_decode(json_encode($data->data),true);//so you know it's a response not AQ model
        //print_r($order);
        //uncomment if we are going to retrive batch not per order
        //foreach($data->data as $order) {
        $query = Order::find()->where(['account_id'=>$account->id, 'sell' => $order['side']=='SELL'?'1':'0','external_id'=>$order['orderId']]);
        if($t = $query->one()){
            $t->progress = floatval($order['executedQty'])/floatval($order['origQty'])*100;

            $t->external_id = $order['orderId'];
            $t->status = Order::STATUS_CREATED;
            if(floatval($t->progress)>=99.8){//consider as finished
                $t->status=Order::STATUS_COMPLETED;
                //$t->canceled=1;
            }
            $t->save();
            $res1=ApiRequest::control('v1/task/update', ['id'=>$t->id,'canceled'=>$t->canceled ,'external_id'=>$t->external_id,'progress'=>$t->progress, 'status' => $t->status]);
            $res1=ApiRequest::accounts('v1/orders/update', ['id'=>$t->id,'canceled'=>$t->canceled ,'external_id'=>$t->external_id,'progress'=>$t->progress, 'status' => $t->status]);


            //return ['id'=>$t->id,'canceled'=>0 ,'external_id'=>$t->external_id,'progress'=>$t->progress, 'status' => $t->status];
        }
        //}

        //return 0;//error or no result;
        return $data->data;
    }

    public static function getBalanceSocket($account){
    //ob_start();
    $balance_update = function($api, $balances) use ($account) {
        AccountBalance::balance_update_spot($api,$balances,$account);
    };
    $order_update = function($api, $report) {
        echo "Order update".PHP_EOL;
        print_r($report);
        $price = $report['price'];
        $quantity = $report['quantity'];
        $symbol = $report['symbol'];
        $side = $report['side'];
        $orderType = $report['orderType'];
        $orderId = $report['orderId'];
        $orderStatus = $report['orderStatus'];
        $executionType = $report['orderStatus'];
        if ( $executionType == "NEW" ) {
            if ( $executionType == "REJECTED" ) {
                echo "Order Failed! Reason: {$report['rejectReason']}".PHP_EOL;
            }
            echo "{$symbol} {$side} {$orderType} ORDER #{$orderId} ({$orderStatus})".PHP_EOL;
            echo "..price: {$price}, quantity: {$quantity}".PHP_EOL;
            return;
        }
        //NEW, CANCELED, REPLACED, REJECTED, TRADE, EXPIRED
        echo "{$symbol} {$side} {$executionType} {$orderType} ORDER #{$orderId}".PHP_EOL;
    };
    $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
    $api->userData($balance_update, $order_update);
    //$out1 = ob_get_contents();//DO NOT REMOVE, IF THERE IS ECHO OR PRINT IT WILL  BREAK API
    //ob_end_clean();
    //return ['margin'=>$balances_margin,'spot'=>$balances];
}
    public static function getBalanceMarginSocket($account){
        //ob_start();
        $balance_update = function($api, $balances) use ($account) {
            AccountBalance::balance_update_margin($api,$balances,$account);
        };
        $order_update = function($api, $report) {
            echo "Order update MARGIN ".PHP_EOL;
            return 1;
            $report=ArrayHelper::toArray($report);
            print_r($report);
            $price = $report['price'];
            $quantity = $report['quantity'];
            $symbol = $report['symbol'];
            $side = $report['side'];
            $orderType = $report['orderType'];
            $orderId = $report['orderId'];
            $orderStatus = $report['orderStatus'];
            $executionType = $report['orderStatus'];
            if ( $executionType == "NEW" ) {
                if ( $executionType == "REJECTED" ) {
                    echo "Order Failed! Reason: {$report['rejectReason']}".PHP_EOL;
                }
                echo "{$symbol} {$side} {$orderType} ORDER #{$orderId} ({$orderStatus})".PHP_EOL;
                echo "..price: {$price}, quantity: {$quantity}".PHP_EOL;
                return;
            }
            //NEW, CANCELED, REPLACED, REJECTED, TRADE, EXPIRED
            echo "{$symbol} {$side} {$executionType} {$orderType} ORDER #{$orderId}".PHP_EOL;
        };
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        $api->userDataMargin($balance_update, $order_update);
        //$out1 = ob_get_contents();//DO NOT REMOVE, IF THERE IS ECHO OR PRINT IT WILL  BREAK API
        //ob_end_clean();
        //return ['margin'=>$balances_margin,'spot'=>$balances];
    }
    public static function getBalance($account){
        //ob_start();

        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);
        //TODO: �������� �� ������ �� �� � �������� �� ������
        $balances_margin = $api->margin_balances();
		if(isset($balances_margin['msg']))
			return ['error'=>$balances_margin['msg']];
		
        $balances = $api->balances();
		if(isset($balances['msg']))
			return ['error'=>$balances['msg']];
        //$out1 = ob_get_contents();//DO NOT REMOVE, IF THERE IS ECHO OR PRINT IT WILL  BREAK API
        //ob_end_clean();
        return ['margin'=>$balances_margin,'spot'=>$balances];
    }


    public static function miniTicker(){
	    //truncate if 00:01
        $truncate=date('H:i');
        if($truncate=='00:00' ) {
            //\Yii::$app->db->createCommand()->truncateTable(GlobalPair::tableName())->execute();
            // OPTIMIZE TABLE foo;
            $connection = \Yii::$app->getDb();
            $sql="delete from ".GlobalPair::tableName()." where created_at<'".date('Y-m-d H:i:s',time()-3600*25)."'";
            $command = $connection->createCommand($sql);
            $result = $command->execute();
            $command = $connection->createCommand("
    OPTIMIZE TABLE ".GlobalPair::tableName().";");
            $result = $command->execute();
        }

        $expanded_results=self::ticker24h();

        $api = new BinanceApi();
        $ticker = $api->bookPrices();
        $timestamp=date('Y-m-d H:i:s',time());
        //print_r($ticker);
        //перенесем рейтинг
        $ts=GlobalPair::find()->where(['>','created_at',date("Y-m-d H:i:s",time()-3600)])->orderBy('created_at desc')->limit(1)->one();
        $pairs=[];
        if(!empty($ts)){
            $pairs=GlobalPair::find()->where(['created_at'=>$ts->created_at])->limit(1000)->all();
        }

        $pairs_rating=[];

        foreach ($pairs as $p){
            $pairs_rating[$p->trading_pair]=$p;
        }

        foreach ($ticker as $trading_pair=>$value){

            if(strpos($trading_pair,'BTC')!==false
                || $trading_pair=='BTCUSDT'
                || strpos($trading_pair,'USDT')!==false){
            //if(strpos($trading_pair,'USDT')!==false){
                $gp=new GlobalPair();
                $gp->trading_pair=$trading_pair;
                $gp->bid=$value['bid'];
                $gp->bids=$value['bids'];
                $gp->ask=$value['ask'];
                $gp->asks=$value['asks'];
                if(isset($pairs_rating[$trading_pair])) {

                    //если нет сильныых колебаний в цене
                    //и за последний час валюта росле
                    if(abs($expanded_results[$trading_pair]['priceChangePercent'])<10 && $pairs_rating[$trading_pair]->bid>0 && $gp->bid>0){
                        $gp->rating=abs($expanded_results[$trading_pair]['priceChangePercent'])+($gp->bid/$pairs_rating[$trading_pair]->bid-1)*300;
                    }else{
                        $gp->rating=0;
                    }
                }
                else{

                    $new_rating=0;
                    //$new_rating = GlobalPair::calculateRating($trading_pair);
                    $gp->rating=$new_rating;
                }
                if(isset($expanded_results[$trading_pair])){
                    $gp->price_change=$expanded_results[$trading_pair]['priceChange'];
                    $gp->price_change_percent=$expanded_results[$trading_pair]['priceChangePercent'];
                    $gp->weighted_avg_price=$expanded_results[$trading_pair]['weightedAvgPrice'];
                    $gp->last_price=$expanded_results[$trading_pair]['lastPrice'];
                    $gp->last_qty=$expanded_results[$trading_pair]['lastQty'];
                    $gp->open_price=$expanded_results[$trading_pair]['openPrice'];
                    $gp->high_price=$expanded_results[$trading_pair]['highPrice'];
                    $gp->low_price=$expanded_results[$trading_pair]['lowPrice'];
                    $gp->volume=$expanded_results[$trading_pair]['volume'];
                    $gp->quote_volume=$expanded_results[$trading_pair]['quoteVolume'];
                    $gp->prediction=(mt_rand() / mt_getrandmax()*2)-1;
                    if(strpos($trading_pair,'BTC')!==false
                        && in_array($trading_pair,['ETHBTC','BNBBTC','LINKBTC','BCHBTC','LTCBTC','XTZBTC','EOSBTC','XMRBTC','MATICBTC','ATOMBTC','ADAGRC','TRXBTC','DASHBTC','NEOBTC','RVNBTC'
                        ,'XLMBTC','BATBTC','ZECBTC','QTUMBTC','VETBTC','ONTBTC','IOSTBTC','IOTABTC',])){
                        $gp->currency_group=0;
                    }else{
                        $gp->currency_group=1;
                    }

                    $day_ago=GlobalPair::find()->where(['trading_pair'=>$trading_pair])
                        ->andWhere(['<','created_at',date('Y-m-d H:i:s',strtotime($timestamp)-3600*24)])
                        ->orderBy('id desc')->limit(1)->one();
                    if(!empty($day_ago)){
                        $gp->volume_24h_change=$gp->volume-$day_ago->volume;
                        $gp->quote_volume_24h_change=$gp->quote_volume-$day_ago->quote_volume;
                    }


                }

                $gp->created_at=$timestamp;
                $gp->save();
                print_r($gp->errors);
            }else{


            }

        }
        //Order::buildPredictionStatistics(strtotime($timestamp));
        return $ticker;
    }
    public static function ticker24h(){
        $api = new BinanceApi();
        $res=$api->prevDay();

        $response=[];
        foreach ($res as $pair){
            $response[$pair['symbol']]= $pair;
        }
        return $response;
    }

    public static function retrieveGraphic($symbol,$date_start,$date_end,$limit=1000,$timeframe='5m'){
        ob_start();// do not show errors of missing key secret pair
        $api = new BinanceApi();
        //$candles1=$api->candlesticks($symbol,'1m',9999);
        //echo strtotime('2019-01-01');
        //$candles1=$api->candlesticks($symbol,'5m',1000,strtotime($date_start)*1000,strtotime($date_end)*1000);
        $interval=5*60;
        if($timeframe=='1d'){
            $interval=24*3600;
        }
        Log::log($timeframe);

        $candles1=[];
        for($i=strtotime($date_start);$i<strtotime($date_end);$i+=1000*$interval){
            Log::log([strtotime($date_start),strtotime($date_end),1000*$interval,(strtotime($date_start)+1000*$interval-strtotime($date_end)),$i]);
            $candles1 = $candles1+ $api->candlesticks($symbol,$timeframe,$limit,$i*1000,($i+1000*$interval)*1000);
        }

        $content = ob_get_contents();

// отключаем и очищаем буфер
        ob_end_clean();
        return $candles1;
    }
    public static function calculateRating($symbol){
        $api = new BinanceApi();
        $data=$api->prevDay($symbol);
        $rating=(floatval($data['priceChangePercent'])*-1)+$data['count']/6000;
        //$rating=(floatval($data['priceChangePercent'])*-1)+$data['count']/1000*$data['volume']/646141;
        return $rating;

    }
    //округляет число до нужного
    private static function numberPrecision($data, $symbol,BinanceAPI $api,$value,$type)
    {
        $info=$data['symbols'][$symbol];

        foreach ($info['filters'] as $filter){
            if($type=='price' && $filter['filterType']=="PRICE_FILTER"){
                $number=self::numberOfDecimals(($filter['tickSize']));
                return floor($value*(10**$number))/10**$number;
            }

            if($type=='amount' && $filter['filterType']=="LOT_SIZE"){
                $number=self::numberOfDecimals(($filter['stepSize']));
                return floor($value*(10**$number))/10**$number;
            }
        }
        return $value;
    }
    private static function numberOfDecimals($val){
        $wtf=strval($val);
        //возьмем все после запятой
        $wtf=explode('.',$wtf);
        //если после зяпатой ничего нет
        if(!isset($wtf[1]))
            return 100;//создадиим ошибку
        $wtf=$wtf[1];
        $number=0;
        for($i=0;$i<strlen($wtf);$i++){
            $number++;
            if($wtf[$i]=='1'){
                break;
            }
            //если мы прошли до конца и так и не нашли 1, то занков после запятой 0
            if($wtf[$i]=='0' && $i==strlen($wtf)-1)
                $number=0;
        }
        return $number;
    }
	
	public static function loan(Currency $currency, Account $account, $amount){
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);

        $data = $api->loan($currency->symbol,$amount);

        return $data;
    }
	
	public static function repay(Currency $currency, Account $account, $amount){
        $api = new BinanceAPI($account->data['access_key'],$account->data['secret_key']);

        $data = $api->repay($currency->symbol,$amount);

        return $data;
    }
	
	public static function saveBalance($edit_balance, $balances_from_market, $usdt_remapped) {
	
		$margin_balances=[];
       $margin_total=0;
        foreach ($balances_from_market['margin']['userAssets'] as $market_balance){
            //если ничего нет то не пишем
            if( floatval($market_balance['borrowed'])==0 &&
                floatval($market_balance['free'])==0 &&
                floatval($market_balance['interest'])==0 &&
                floatval($market_balance['locked'])==0 &&
                floatval($market_balance['netAsset'])==0
            ){
                continue;
            }
            $margin_balance['symbol']=$market_balance['asset'];
            $margin_balance['borrowed']=floatval($market_balance['borrowed']);
            $margin_balance['free']=floatval($market_balance['free']);
            $margin_balance['interest']=floatval($market_balance['interest']);
            $margin_balance['locked']=floatval($market_balance['locked']);
            $margin_balance['netAsset']=floatval($market_balance['netAsset']);
            $margin_balance['rate']=$usdt_remapped[$margin_balance['symbol']]['rate'];
            $margin_balance['currency']=$usdt_remapped[$margin_balance['symbol']]['currency'];

            $margin_total+=($margin_balance['free']+$margin_balance['locked'])*$margin_balance['rate'];

            $margin_balances[]=$margin_balance;
        }
        $edit_balance->balances_margin=$margin_balances;
        $edit_balance->total_margin=$margin_total;

        $spot_balances=[];
        $spot_total=0;

        foreach ($balances_from_market['spot'] as $symbol=>$market_balance){
            //если ничего нет то не пишем

            if( floatval($market_balance['available'])==0 &&
                floatval($market_balance['onOrder'])==0
            ){
                continue;
            }
     //       print_r($market_balance);
		 if(!isset($spot_balance['symbol']))
			 continue;
		 
            $spot_balance['symbol']=$symbol;
            $spot_balance['available']=$market_balance['available'];
            $spot_balance['onOrder']=$market_balance['onOrder'];
            $spot_balance['rate']=$usdt_remapped[$spot_balance['symbol']]['rate'];
            $spot_balance['currency']=$usdt_remapped[$spot_balance['symbol']]['currency'];

            $spot_total+=($spot_balance['available']+$spot_balance['onOrder'])*$spot_balance['rate'];
            $spot_balances[]=$spot_balance;
        }
		$edit_balance->balances=$spot_balances;
        $edit_balance->total=$spot_total;
        $edit_balance->save();
	}
}


?>