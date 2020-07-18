<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use common\components\ApiRequest;
use common\components\BinanceExchange;
use common\models\Currency;
use console\models\Task;
use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Order;
use common\models\Proxy;
use common\models\Account;

class AutoController extends Controller
{
	public static $affectedIds=[];


    //my tasks not bot,not orders, not markets
    public function actionAddTask($command, $peroid=7200){
	    $task=new Task();
	    $task->freq=$peroid;
	    $task->command=$command;
        $task->name="no name";
        $task->alias="cmd";
        $task->status=1;
        $task->start_type=1;
        $task->parse_counter=0;

        $task->save();
        print_r($task->errors);
    }

}