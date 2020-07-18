<?php
namespace backend\controllers;

use common\models\Currency;
use common\models\Log;
use Yii;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;

class LogController extends Controller
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
		$logs=Log::find();

		if(isset($_GET['type']))
            $logs->where(['type'=>$_GET['type']]);
        if(isset($_GET['page']))
            $page=$_GET['page'];
        else
            $page=1;
        $logs->orderBy('id desc');
		$logs=$logs->offset(($page-1)*200)->limit(200)->all();
        return $this->render('index', ['logs' => $logs]);
    }

}
