<?php
namespace backend\controllers;

use common\models\Currency;
use common\models\Log;
use common\models\Notification;
use Yii;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;

class NotificationController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
    //возвращает не прочитанные уведомления
    public function actionIndex()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $notifications=Notification::find()->where(['is_viewed'=>0])->orderBy('id desc')->all();
        return $notifications;
    }
    public function actionMark_as_read(){
	    $id=$_GET['id'];
	    $notification=Notification::findOne($id);
	    $notification->is_viewed=1;
	    $notification->save();
    }


}
