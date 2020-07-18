<?php
namespace backend\controllers;

use common\models\Currency;
use common\models\Log;
use common\models\Notification;
use common\models\Order;
use Yii;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;

class OrderController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
    public function actionCancel(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data=Yii::$app->request->get();

	    if(!isset($data['id'])){
            return ['status'=>"MISSING ID"];
        }

        $order=Order::findOne($data['id']);
	    $resp=$order->cancel();

	    return $resp;
    }


}
