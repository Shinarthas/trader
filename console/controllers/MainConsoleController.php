<?php


namespace console\controllers;


use app\models\Bot;
use console\models\Task;
use frontend\models\Proxy;
use yii\console\Controller;

class MainConsoleController extends Controller
{
    public static $delay=60;
    public static $proxyStatic=[];
    public static $default_offset=20;
    private $proxy=[];
    public $time;
    public function checkIfTaskAlive($test_param,$task_id){
        $task=1;//если это ручной запуск то скрипт не прервется
        if($test_param=='scheduled'){
            $task=Task::find()->where(['=','id',$task_id])->one();
            //echo get_class($task)." aaaa ";

            if(!$task->status){
                if(!file_exists($task->stop_file) && $task->start_type==2){
                    unlink($task->stop_file.'.sh');
                    $task->delete();
                }
                return 0;
            }
            if(!file_exists($task->stop_file) && $task->start_type==2){
                unlink($task->stop_file.'.sh');
                $task->delete();
            }

        }
        return $task;
    }
    public function finishTask($task){
        if(get_class($task)==Task::className()){
            //$task->counter+=1;
            //  зменить дату окончания
            $task->save();
        }

    }

    public function getUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy['port']);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTPS');
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy['ip']);
        if(trim($this->proxy['username'])!='' && trim($this->proxy['password'])!=''){
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy['username'] . ':' . $this->proxy['password']);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;

    }

    public function getProxy(){
        $bot=new Bot();
        $config=$bot->getConfig();
        $proxyM=new Proxy();
        $proxy=$proxyM->getProxy($config['proxy_options']['type'],$config['proxy_options']['id']);

    }
    public function checkInterruptions($test_param,$task_id){
        $task=1;//если это ручной запуск то скрипт не прервется
        if($this->time+self::$delay>time() || $test_param=='scheduled'){
            $this->time=time();
            $task=Task::find()->where(['id'=>$task_id])->one();
            if(!$task->status){
                if(!file_exists($task->stop_file) && $task->start_type==2){
                    unlink($task->stop_file.'.sh');
                    $task->delete();
                }
                echo 'abort';
                return 0;
            }
            if(!file_exists($task->stop_file) && $task->start_type==2){
                unlink($task->stop_file.'.sh');
                $task->delete();
                return 0;
            }
        }
        return $task;
    }
    public function curlProxyRequest($url, $host, $port, $user = '', $password = '', $method = 'HTTPS')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, $port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, $method);
        curl_setopt($ch, CURLOPT_PROXY, $host);
        if ($user != '') {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user . ':' . $password);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}