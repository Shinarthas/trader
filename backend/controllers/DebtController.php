<?php
namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;
use common\models\Order;
use common\models\Currency;

class DebtController extends Controller
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
		$currencies = Currency::find()->all();
		$accounts = Account::find()->limit(50)->with("last_balance")->all();

        return $this->render('index', ['accounts' => $accounts, 'currencies'=> $currencies]);
    }
	
	public function actionRepay() {
		$account = Account::findOne($_POST['account_id']);
		$debt = 0;
		$free = 0;
		
		$main_balance = $a->last_balance->getMargin_main_balance(0);
		foreach($main_balance as $balance) {
			if($balance['currency'] == $_POST['currency_id']) {
				$debt = $balance['borrowed'];
				$free = $balance['free'];
			}
		}
		
		if($free >= $debt)
			$account->repay($_POST['currency_id'],$debt);
		else {	
			$amount_for_order = $debt - $free;
			// makeOrder with amount amount_for_order
			
			// $account->repay($_POST['currency_id'],$debt);
		}
	}
	
}
