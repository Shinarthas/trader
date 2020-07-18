<?php
namespace backend\controllers;

use common\models\Notification;
use common\models\Strategy;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Account;
use common\models\AccountBalance;
use common\models\Order;
use common\models\Currency;

class AccountController extends Controller
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
		$accounts = Account::find()->limit(50)->all();

        return $this->render('index', ['accounts' => $accounts]);
    }
	
	public function actionInfoJson()
    {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		 
		$accounts = Account::find()->all();
		return $accounts;
    }
	
	public function actionReloadBalance()
    {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		AccountBalance::checkBalance(Account::findOne($_GET['id']));
			
		$a_b =AccountBalance::find()->where(['status'=>1,"account_id"=>$_GET['id']])->one();
		
		return $a_b->margin_main_balance;
    }
	
	public function actionAdd() {
		$a = new Account;
			
		if(isset($_POST['save'])) {
		    $a->status=Account::STATUS_DISABLED;
			$a->load($_POST);
			$a->created_at = time();
			
			if($a->save()) {
			    Notification::make(Yii::$app->user->identity->username." created new account ".$a->name);
				AccountBalance::checkBalance($a);
				$result = $a->verify();
				 
				return $this->redirect("/account/");
			}
		}
		return $this->render("add", ['account'=>$a , 'success'=>$success,'error'=>$error]);
	}
	public function actionEdit($id) {
		$success = '';
		$error = '';
		
		if(!$a = Account::findOne($id))
			$this->redirect("/account/");
			
		if(isset($_POST['save'])) {
			$a->load($_POST);
			
			if($a->save()){
                Notification::make(Yii::$app->user->identity->username." edited an account ".$a->name);
                return $this->redirect("/account/");
            }

		}
		if(isset($_POST['verify'])) {
			$result = $a->verify();
			if($result['status']) {
				$success = 'Verified';
			}
			else
				$error = $result['error'];
		}
		if(isset($_POST['loan']))
			$a->loan();
		if(isset($_POST['repay']))
			$a->repay();
		
		return $this->render("add", ['account'=>$a, 'edit'=>true, 'success'=>$success,'error'=>$error]);
	}
    public function actionStrategies($id) {
        $success = '';
        $error = '';

        if(!$a = Account::findOne($id))
            $this->redirect("/account/");

        if(Yii::$app->request->isPost) {

            $a->strategy_id=$_POST['strategy_id'];
            $a->save();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return ['msg'=>'success'];

        }
        $strategies=Strategy::find()->all();
        return $this->render("strategies", ['account'=>$a,'strategies'=>$strategies, 'edit'=>true, 'success'=>$success,'error'=>$error]);
    }
	
	public function actionStat($id) {
		if(!$a = Account::findOne($id))
			$this->redirect("/account/");



		$a_b =AccountBalance::find()->where(['status'=>1,"account_id"=>$id])->one();
		//если нет баланса то с аккаунтом какаято херня
		if(empty($a_b)){
		    $a->status=Account::STATUS_DISABLED;
		    $a->save();
            $this->redirect("/account/");

        }else{
            $main_balance = $a_b->getMargin_main_balance(0);
        }

		
		$orders = Order::find()->where(['account_id'=>$id])->orderBy("id DESC")->limit(100)->all();
		
		$graph = [];
		$a_b = AccountBalance::find()->where(["account_id"=>$id])->andWhere(['>', 'timestamp', date("Y-m-d, H:i:s", time() - (3600*24*7))])->all();
		
		foreach($a_b as $balance)
		{
			$graph[] = ['time'=>$balance->timestamp,'value'=>$balance->total_margin];
		}

		$currencies = [];
		foreach(Currency::find()->all() as $c)
		    $currencies[$c->id] = $c;
		
		return $this->render("stat", ['account'=>$a, 'graph'=>$graph, 'orders'=>$orders, 'currencies'=>$currencies, 'main_balance'=>$main_balance]);
	}
	
	public function actionView($id) {
		$a = Account::findOne($id);
		if(Yii::$app->request->isPost){
            $res = ApiRequest::accounts('v1/account/update', $_POST);
            $a2=Account::findOne( $res->data->account_id);

            $a2->name = $_POST['name'];
            $a2->check_balance = $_POST['check_balance'];
            $a2->label = $_POST['label'];
            $a2->save();
            if(empty($a2->errors))
                $a=$a2;
        }

		return $this->render("view", ['a'=>$a,'account_types'=>self::$account_types]);
	}
}
