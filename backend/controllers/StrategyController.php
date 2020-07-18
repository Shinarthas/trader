<?php
namespace backend\controllers;

use common\models\Currency;
use common\models\Log;
use common\models\Notification;
use common\models\Strategy;
use Yii;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;

class StrategyController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
    public function actionAdd() {
        $s = new Strategy();

        if(isset($_POST['save'])) {

            $s->load($_POST);
            $s->created_at = date("Y-m-d H:i:s",time());

            if($s->save()) {

                return $this->redirect("/strategy/");
            }
            print_r($s->errors);
            print_r($s->errors);
            print_r($s->errors);
            print_r($s->errors);
            print_r($s->errors);
            print_r($s->errors);
           //die();
        }
        return $this->render("add", ['strategy'=>$s]);
    }
    public function actionIndex(){
	    $strategies=Strategy::find()->all();
        return $this->render("index", ['strategies'=>$strategies]);

    }
    public function actionEdit($id) {
        $success = '';
        $error = '';

        if(!$a = Strategy::findOne($id))
            $this->redirect("/strategy/");

        if(isset($_POST['save'])) {
            $a->load($_POST);

            if($a->save()){
                Notification::make(Yii::$app->user->identity->username." edited a strategy ".$a->name);
                return $this->redirect("/account/");
            }

        }
        return $this->render("add", ['strategy'=>$a, 'edit'=>true, 'success'=>$success,'error'=>$error]);
    }

}
