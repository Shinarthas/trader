<?php
namespace backend\controllers;

use common\models\Currency;
use Yii;
use yii\web\Controller;
use common\models\Account;

class Trade2Controller extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
    public function actionTrade()
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

        return $this->render('trade', ['accounts' => $out,'currencies'=>$currencies]);
    }

    public function actionIndex()
    {

        return $this->render('index');
    }
	
	public function actionInWork() {
		return $this->render('in-work');
	}
}
