<?
namespace common\components;
use common\assets\BitfinexApi;
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

class BitfinexExchange {
	
	const BINANCE=3;

	//additional_info это массив который содержит следующие поля
    /*
     * stopPrice	DECIMAL	NO	Used with STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, and TAKE_PROFIT_LIMIT orders.
     * icebergQty	DECIMAL	NO	Used with LIMIT, STOP_LOSS_LIMIT, and TAKE_PROFIT_LIMIT to create an iceberg order.
     * sideEffectType	ENUM	NO	NO_SIDE_EFFECT, MARGIN_BUY, AUTO_REPAY; default NO_SIDE_EFFECT.
     */
	public static function sellMarginOrder(Currency $currency_one, Currency $currency_two,$tokens_count,$price, Account $account,Array $additional_info=[], $type="LIMIT"){
        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
		
		$order = $api->new_order($currency_pair, $tokens_count, $price, "bitfinex", 'sell', strtolower($type));
        
		return $order;
    }
    public static function buyMarginOrder(Currency $currency_one, Currency $currency_two,$tokens_count,$price, Account $account,Array $additional_info=[],$type="LIMIT"){
        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        
		$order = $api->new_order($currency_pair, $tokens_count, $price, "bitfinex", 'buy', strtolower($type));
        
		return $order;
    }
	
    public static function marginOrder(Currency $currency_one, Currency $currency_two,$tokens_count,$price, Account $account, $sell,Array $additional_info=[],$type="LIMIT"){

		$api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
		
		$currency_pair = str_replace("USDT", "UST", $currency_pair);
		
		$tokens_count = (string)$tokens_count;
		$price = (string)$price;
		if($type=="MARKET" AND $sell==1)
			$price = '0.000001';
		
		$order = $api->new_order($currency_pair, $tokens_count, $price, "bitfinex", $sell==1?"sell":'buy', strtolower($type));
        
		return $order;
    }
	
	public static function marginMarketOrder(Currency $currency_one, Currency $currency_two,$tokens_count, Account $account, $sell,Array $additional_info=[]){
		return self::marginOrder( $currency_one, $currency_two,$tokens_count,0,  $account, $sell, $additional_info,"MARKET");
	}
	
    public static function cancelMarginOrder(Order $order) {

        $account=Account::findOne($order->account_id);



        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);

        $order_id= (int)$order->external_id;//ORDER MARKET ID

        $response = $api->cancel_order($order_id);
        return $response;

    }


    public static function marginOrderStatus(Order $order){
        $account=Account::findOne($order->account_id);

        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);

        $order_id= (int)$order->external_id;//ORDER MARKET ID

        $response = $api->get_order($order_id);

		$response['status'] = '';
		
		if($response['remaining_amount']==0)
			$response['status'] = 'FILLED';
		if($response['remaining_amount']>0)
			$response['status'] = 'NEW';
		
		if($response['is_cancelled'])
			$response['status'] = 'CANCELED';

        return $response;
    }
	
    public static function getBalance($account){
        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);
        $balances = $api->get_balances();
		$positions = $api->get_positions();

        return ['balances'=> $balances, 'positions'=>$positions];
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

        $api = new BitfinexApi();
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
        Order::buildPredictionStatistics(strtotime($timestamp));
        return $ticker;
    }
    public static function ticker24h(){
        $api = new BitfinexApi();
        $res=$api->prevDay();

        $response=[];
        foreach ($res as $pair){
            $response[$pair['symbol']]= $pair;
        }
        return $response;
    }

    public static function calculateRating($symbol){
        $api = new BitfinexApi();
        $data=$api->prevDay($symbol);
        $rating=(floatval($data['priceChangePercent'])*-1)+$data['count']/6000;
        //$rating=(floatval($data['priceChangePercent'])*-1)+$data['count']/1000*$data['volume']/646141;
        return $rating;

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
        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);
		
		$symbol = $currency->symbol;
		if($symbol == "USDT")
			$symbol = "ust";

        $data = $api->new_offer($symbol,$amount, 0, 365, "loan");

        return $data;
    }
	
	public static function repay(Currency $currency, Account $account, $amount){
        $api = new BitfinexApi($account->data['access_key'],$account->data['secret_key']);

		$symbol = $currency->symbol;
		if($symbol == "USDT")
			$symbol = "ust";
		
        $data = $api->new_offer($currency->symbol,$amount, 0, 365, "lend");

        return $data;
    }
	
	public static function saveBalance($edit_balance, $balances_from_market, $usdt_remapped) {
		$margin_balances=[];
		$spot_balances=[];
		$margin_total=0;
		$spot_total = 0;
		
		//print_r($balances_from_market);
		foreach($balances_from_market['balances'] as $b) {
			if($b['currency']=="ust")
				$b['currency'] = 'usdt';
			
			
			$data = [];
			$data['symbol'] = strtoupper($b['currency']);
			$data['free'] = $b['available'];
			$data['amount'] = $b['amount'];
			$data['currency'] = $usdt_remapped[$data['symbol']]['currency'];
			$data['rate'] = $usdt_remapped[$data['symbol']]['rate'];
			
			if($b['currency']=='usd')
				$data['rate'] = 1;
			
			$data['locked'] = 0;
			$data['borrowed'] = 0;
			
			if($b['type']=="exchange") {
				$spot_balances[] = $data;
				$spot_total += $data['amount']*$data['rate'];
			}
			else {
				$margin_balances[] = $data;
				$margin_total+=$data['amount']*$data['rate'];
			}
		}
		
		foreach($balances_from_market['positions'] as $b) {
			$data = [];
			$data['free'] = 0;
			$data['symbol'] = strtoupper($b['symbol']);
			$data['amount'] = $b['amount'];
			$data['status'] = $b['status'];
			$temp_symbol = str_replace(['UST', 'USDT'],'', $data['symbol']);
			$data['currency'] = $usdt_remapped[$temp_symbol]['currency'];
			$data['rate'] = $usdt_remapped[$temp_symbol]['rate'];
			$data['in_position'] = 1;
			$margin_balances[] = $data;
		}
		
		$edit_balance->balances_margin=$margin_balances;
		$edit_balance->total_margin=$margin_total;
		$edit_balance->balances=$spot_balances;
        $edit_balance->total=$spot_total;
        $edit_balance->save();
	}
	

}


?>