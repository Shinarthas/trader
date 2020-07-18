<?php
namespace backend\controllers;

use Codeception\PHPUnit\Constraint\Page;
use common\models\Account;
use common\models\AccountBalance;
use common\models\Order;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $orders_week=Order::find()->where(['>','time',time()-3600*7*24])->count();
        $accounts_count=Account::find()->count();

        //возьмем аккаунта
        $accounts=Account::find()->all();
        // возьмем их id
        $accounts_ids=[];
        foreach ($accounts as $a){
            $accounts_ids[]=$a->id;
        }
        //возьмем балансы сейчас
        $account_balances_now = AccountBalance::find()->where(['in','account_id',$accounts_ids])
            ->andWhere(['status'=>1])->all();
        //вощьмем айдишники балансов день назад
        $account_balances_day_ids = AccountBalance::find()->select("min(id) as id")->where(['in','account_id',$accounts_ids])
            //->andWhere(['<','timestamp',date("Y-m-d H:i:s",time()-3600*24)])
            ->andWhere(['>','timestamp',date("Y-m-d H:i:s",time()-3600*24*7)])
            ->groupBy('account_id')->limit($accounts_count)->asArray()->all();

        $account_balances_day=AccountBalance::find()->where(['in','id',array_column($account_balances_day_ids,'id')])->all();
        $profit=0;
        $netWorth=0;
        foreach ($account_balances_now as $ab){
            $netWorth+=$ab->total_margin;
            foreach ($account_balances_day as $ab2){
                if($ab2->account_id==$ab->account_id)
                    $profit+=$ab->total_margin;
            }


        }

        foreach ($account_balances_day as $ab){
            foreach ($account_balances_now as $ab2){
                if($ab2->account_id==$ab->account_id)
                    $profit-=$ab->total_margin;
            }
        }

        return $this->render('index',[
            'orders_week'=>$orders_week,
            'accounts_count'=>$accounts_count,
            'profit'=>$profit,
            'net_worth'=>$netWorth,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
