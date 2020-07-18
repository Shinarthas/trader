<?php
namespace backend\controllers;

use common\models\Currency;
use common\models\Notification;
use common\models\Order;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;

class TradeController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	

    public function actionIndex()
    {
		$out = [];
		$accounts = Account::find()->all();
		foreach($accounts as $a){
		    //дополним инфу текущими балансами
            $out[$a->group_id][] = $a;
        }

        $currencies = [];
        foreach(Currency::find()->orderBy('sort_order desc')->all() as $c)
            $currencies[$c->id] = $c;
        $order=Order::find()->orderBy('id desc')->with('currencyOne')->with('currencyTwo')->limit(30)->all();

        return $this->render('index', ['accounts' => $out,'currencies'=>$currencies,'orders'=>$order]);
    }
	
	public function actionPurchase() {
	    $response=[];
	    $currency = Currency::findOne($_POST["currency_id"]);

	    $account_names=[];//нужно для логов
		foreach(Account::find()->where(['id' => $_POST["account_ids"]])->all() as $a) {
			$result = $a->purchase($currency,$_POST['shoulder'],$_POST['name']);
            $account_names[]=$a->name;
			foreach($result as $symbol=>$item)
				$response[$a->id]['result'][] = array_merge($item, ['symbol'=>$symbol]);

			$a_b =AccountBalance::find()->where(['status'=>1,"account_id"=>$a->id])->one();
			$response[$a->id]['balance']=$a_b->margin_main_balance;
			$a->checkStatusByBalance();
		}
        Notification::make(Yii::$app->user->identity->username." created a purchase for [".implode(', ',$account_names)."] signal ".$_POST['name']);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->asJson($response);
	}
	
	public function actionSell() {
        $response=[];
        $account_names=[];//нужно для логов
	    foreach(Account::find()->where(['id' => $_POST["account_ids"]])->all() as $a) {
			$result = $a->sell();
            $account_names[]=$a->name;
			foreach($result as $symbol=>$item)
				$response[$a->id]['result'][] = array_merge($item, ['symbol'=>$symbol]);
				
			$a_b =AccountBalance::find()->where(['status'=>1,"account_id"=>$a->id])->one();
			$response[$a->id]['balance']=$a_b->margin_main_balance;
			$a->checkStatusByBalance();
		}

        Notification::make(Yii::$app->user->identity->username." created a sell for [".implode(', ',$account_names)."] signal ".$_POST['name']);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->asJson($response);
	}
}
