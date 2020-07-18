<?php

namespace console\controllers;

use common\components\BinanceExchange;
use common\models\Currency;
use common\models\CurrencyToUsd;
use common\models\Market;
use common\models\Order;
use common\models\OrderList;
use common\models\TokenPairs;
use console\models\Task;
use Yii;
use yii\console\Controller;
//use common\models\CheckProxy as tmp;
use common\models\Proxy;

class  CurrencyController extends MainConsoleController
{
    public function actionIndex()
    {
        error_reporting(0);
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $task_id=null;
        $test_param=null;
        if($numargs>1){
            $task_id=$arg_list[$numargs-1];
            $test_param=$arg_list[$numargs-2];
        }

        //$proxy=Proxy::random();

        if(!$task=$this->checkIfTaskAlive($test_param,$task_id)){
            return; //если 0 заканчиваем выполнение
        }
        if($test_param=='scheduled'){
            $task=Task::find()->where(['=','id',$task_id])->one();
        }
        $currencies=Currency::find()->all();
        $this->time=time()-self::$delay-1;
        $timeOut=7200;

        if($task && $task!=1)
            $timeOut=$task->freq;
        foreach ($currencies as $currency){

            if(!$task=$this->checkInterruptions($test_param,$task_id) ||  time()-$this->time-180>$timeOut){
                echo 'die';
                die();//или return;
            }
            CurrencyToUsd::writeRate($currency->id);

        }
        if($test_param=='scheduled')
            $this->finishTask($task);
        ///var_dump($this->checkProxyList());
    }
    public function actionTrader2(){

        BinanceExchange::miniTicker();
        //sleep(30);
        //BinanceExchange::miniTicker();

    }
    public function actionPrediction(){
        sleep(10);
        Order::buildPredictionStatistics();
    }
/*
    public function actionTrader2(){
        error_reporting(0);
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $task_id=null;
        $test_param=null;
        if($numargs>1){
            $task_id=$arg_list[$numargs-1];
            $test_param=$arg_list[$numargs-2];
        }

        //$proxy=Proxy::random();

        if(!$task=$this->checkIfTaskAlive($test_param,$task_id)){
            return; //если 0 заканчиваем выполнение
        }
        if($test_param=='scheduled'){
            $task=Task::find()->where(['=','id',$task_id])->one();
        }

        $this->time=time()-self::$delay-1;
        $timeOut=7200;

        if($task && $task!=1)
            $timeOut=$task->freq;
        if(!$task=$this->checkInterruptions($test_param,$task_id) ||  time()-$this->time-180>$timeOut){
            echo 'die';
            die();//или return;
        }


        BinanceExchange::miniTicker();

        if($test_param=='scheduled')
            $this->finishTask($task);
        ///var_dump($this->checkProxyList());
    }
*/
}