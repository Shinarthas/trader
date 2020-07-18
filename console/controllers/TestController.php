<?php

namespace console\controllers;

use api\v1\renders\ResponseRender;
use app\models\Bot;
use ccxt\okex3;
use common\assets\CoinMarketCapApi;
use common\components\BinanceExchange;
use common\components\HitbtcExchange;
use common\components\OkexExchange;
use common\components\PoloniexExchange;
use common\models\AccBalance;
use common\models\Account;
use common\models\AccountBalance;
use common\models\Currency;
use common\models\Forecast;
use common\models\ForecastStatistics;
use common\models\Market;
use common\models\Order;
use common\models\OrderList;
use common\models\PairRating;
use common\models\TokenPairs;
use console\models\TokenTransfer;
use common\models\Proxy;
use yii\console\Controller;
use Yii;
use console\models\Task;
use yii\helpers\ArrayHelper;
use common\models\GlobalPair;

class TestController extends MainConsoleController
{
    public function actionUncompletedOrders(){
        $res=BinanceExchange::loadUncompletedOrders(Account::findOne(29),Order::findOne(71));
        print_r($res);
    }
    public function actionCourse(){
        $res=BinanceExchange::exchangeRates(Currency::findOne(5),Currency::findOne(1));
        print_r($res);
    }

    public function actionOrder(){
        $order=new Order();

        $order->sell=1;
        $order->status=0;
        $order->account_id=1;
        $order->tokens_count=0.005;
        $order->rate=9500;
        $order->canceled=0;
        $order->time=time();
        $order->created_at=time();
        $order->loaded_at=time();
        $order->currency_one=Currency::findOne(['symbol'=>'BTC'])->id;
        $order->currency_two=Currency::findOne(['symbol'=>'USDT'])->id;
        $order->is_user=0;
        $order->data_json="[]";
        $order->progress=0;


        $order->save();
        print_r($order->errors);
        $order->makeMarket(['sideEffectType'=>'MARGIN_BUY']);
    }
    public function actionCancel($id){
        $order=Order::findOne($id);
        $order->cancel();
    }

    //берет текущий баланс и пишет в БД
    public function actionBalance()
    {
        $accounts = Account::find()->all();
        foreach ($accounts as $account){
            AccountBalance::checkBalance($account);
			$account->checkStatusByBalance();
        }
    }
	
	public function actionOneBalance($id)
    {
        $account = Account::findOne($id);

        AccountBalance::checkBalance($account);
		$account->checkStatusByBalance();

    }
    //СОКЕТ берет текущий баланс и пишет в БД СОКЕТ
    public function actionBalanceSocket()
    {
        $accounts = Account::find()->all();
        foreach ($accounts as $account){
            AccountBalance::checkBalanceSocket($account);
        }
    }
    //СОКЕТ берет текущий баланс и пишет в БД СОКЕТ
    public function actionBalanceMarginSocket()
    {
        $accounts = Account::find()->all();
        foreach ($accounts as $account){
            AccountBalance::checkBalanceMarginSocket($account);
        }
    }

    public function actionOrderStatus(){
        Order::checkOrders();
    }
	public function actionOneOrder($id){
        Order::findOne($id)->checkStatus();
	}
    public function actionShortSell(){
        $account=Account::findOne(1);
        $currency=Currency::findOne(['symbol'=>'BTC']);

        $resp=$account->short_sell($currency);
    }
    public function actionShortPurchase(){
        $account=Account::findOne(1);
        $currency=Currency::findOne(['symbol'=>'BTC']);

        $resp=$account->short_purchase($currency);
        print_r($resp);
    }

}
