<?php


namespace console\controllers;
use app\models\Bot;
use COM;
use common\models\Log;
use Yii;
use yii\console\Controller;
use common\models\Task;

class TaskLauncherController extends Controller
{
    private $shellPath = 'c:\xampp\php\php.exe c:\xampp\htdocs\market-parser\yii ';
    //private $shellPath = 'php yii ';

    public function actionIndex()
    {
        //echo $this->shellPath;
        //echo $_SERVER['BASE_PATH'];
        //Yii::warning("launcher started",__METHOD__);

        //$allTask = Task::find()->where(['<>','status',0])->andWhere(['=','start_type',1])->all();
        $allTask = Task::find()->where(['<>','status',0])->all();
        foreach ($allTask as $task) {
            //echo $this->shellPath . $task->command;
            if($task['start_type']==1){
                if(time()>strtotime($task['activated_at'])+$task['freq']){
                        $cmd="php ".Yii::getAlias('@home')."/yii $task[command] scheduled $task[id]";
                    $task->activated_at=date('Y-m-d H:i:s',time());
                    $task->save();
                    //echo $cmd;
                    //shell_exec($cmd);
                    if (substr(php_uname(), 0, 7) == "Windows"){
                        //echo $cmd;
                        $handle = new COM('WScript.Shell');
                        $handle->Run($cmd, 0, false);
                        //pclose(popen("start /B ". $cmd, "r"));
                        //shell_exec($cmd);
                    }
                    else {

                        //shell_exec($cmd );
                        shell_exec($cmd . " >/dev/null 2>/dev/null &");
                    }
                }
            }elseif($task['start_type']==2){
                // тут только shell
                if(file_exists($task['stop_file'])){
                    //наверное ничего
                }else{
                    if(file_exists($task['stop_file'].'.sh')){
                        unlink($task['stop_file'].'.sh');
                    }else{
                        $bot=new Bot();
                        $config=$bot->getConfig();
                        $script='
                            NAME='.$task['stop_file'].'
                            while [ -f $NAME ]
                            do
                                php yii '.$task['command'].' scheduled '.$task['id'].' 
                                sleep '.($config['proxy_options']['call_freq']/1000).'
                            done 
                            ';
                        file_put_contents($task['stop_file'].'.sh',$script);
                        file_put_contents($task['stop_file'],"");
                        if(is_file($task['stop_file'])){
                            $contents = $task['stop_file'].'.sh';
                            if (substr(php_uname(), 0, 7) == "Windows"){
                                $cmd="start /B sh ".str_replace('\\','/',Yii::getAlias('@home')).'/'. $task['stop_file'].'.sh';
                                //echo $cmd;
                                pclose(popen($cmd, "r"));
                                $task->command=$config['proxy_options']['command'];
                            }
                            else {
                                $cmd="sh ".Yii::getAlias('@home').'/'.$task['stop_file'].'.sh'. " >/dev/null 2>/dev/null &";
                                shell_exec($cmd);
                                $task->command=$config['proxy_options']['command'];
                            }
                        }
                    }
                }
            }

        }
        $allShellTask = Task::find()->where(['<>','status',0])->andWhere(['=','start_type',2])->all();
        foreach ($allShellTask as $disabledShellTask){
            unlink($disabledShellTask['stop_file']);
            unlink($disabledShellTask['stop_file'].'.sh');
        }
        //TODO: затестить это
        //УДАЛИТЬ ВСЕ СКРИПТЫ которых нет в тасках
        $allShellTask = Task::find()->andWhere(['=','start_type',2])->all();
        $stopFiles=[];
        foreach ($allShellTask as $shell_task){
            $stopFiles[]=$shell_task['stop_file'];
            $stopFiles[]=$shell_task['stop_file'].'.sh';
        }
        $files = glob( './tasks/*');

        foreach($files as $file){
            //Make sure that this is a file and not a directory.
            if(is_file($file) && !in_array($file,$stopFiles)){
                unlink($file);
            }
        }

        echo 'launching...';
    }
}