<?php

namespace app\models;

use console\models\Currency;
use console\models\Task;
use Yii;


class Bot
    //extends \yii\db\ActiveRecord
{

    const DEFAULT_ID = 1;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    private $config_file = 'bot_setting.json';

    public $config = [];

    public function SetProxy($option, $id = self::DEFAULT_ID)
    {
        $this->load();
        switch ($option) {
//            case 1:
//                $this->config['proxy_options']['type'] = 'random';
//                $this->config['proxy_options']['id'] = $id;
//                break;
            case 2:
                $this->config['proxy_options']['type'] = 'queue';
                $this->config['proxy_options']['id'] = $id;
                break;
            case 3:
                $this->config['proxy_options']['type'] = 'specific';
                $this->config['proxy_options']['id'] = $id;
                break;
            default:
                $this->config['proxy_options']['type'] = 'random';
                $this->config['proxy_options']['id'] = $id;
                break;
        }
        $this->save();

    }

    public function SetFreq($s)
    {
        $config = $this->load();
        $this->config['proxy_options']['call_freq'] = $s * 1000;
        $this->save();

    }

    public function SetCommand($command='parser/parse',$name='sample')
    {
        $c='parser/parse';
        if(trim($command)!=''){
            $c=$command;
        }

        $config = $this->load();
        $task=new Task();
        $task->name=$name;
        $task->command=$c;
        $task->status=0;
        $task->status=0;

        $this->config['proxy_options']['command'] = $c;

        $this->save();

    }
    public function SetLifeTime($seconds=7200)
    {
        $config = $this->load();

        $this->config['proxy_options']['lifetime'] = $seconds;

        $this->save();

    }
    public function load()
    {
        $init_config="{\"proxy_options\":{\"type\":\"random\",\"id\":1,\"call_freq\":10000,\"command\":\"parser\/parse\",\"lifetime\":7200}}";
        $c=json_decode($init_config,true);
        if (file_exists($this->config_file)) {
            $data = json_decode(file_get_contents($this->config_file), true);
            $this->config = $data ? $data : $c;
            return $data ? $data : $c;
        } else {

            file_put_contents($this->config_file, $init_config);
            $this->config = $c;
            return $c;
            Yii::warning("no config file",__METHOD__);
        }
    }

    public function save()
    {
        Yii::warning("bot settings saved",__METHOD__);
        print_r(json_encode($this->config));
        file_put_contents($this->config_file, json_encode($this->config));

    }
    public function getConfigFile(){
        return $this->config_file;
    }
    public function getConfig(){
        Yii::warning("config retrieve",__METHOD__);
        return $this->load();
    }

    public function start(){

        $this->stop();
        $config=$this->load();
        Yii::warning("bot started",__METHOD__);
        $defaultFolder='tasks';
        if(!isset($config['proxy_options']['command'])){
            echo "status failed to start";
        }

        if($config['proxy_options']['call_freq']<30000){
            $task=new Task();
            $task->freq=$config['proxy_options']['call_freq']/1000;
            $task->alias="sh rapid cmd";
            $task->start_type=2;
            $task->save();

            //bash+cron
            $taskName='task-'.time();
            $bashFile=$taskName.'.sh';
            $script='
            NAME=tasks/'.$taskName.'
            while [ -f $NAME ]
            do
                php yii '.$config['proxy_options']['command'].' scheduled '.$task['id'].' 
                sleep '.($config['proxy_options']['call_freq']/1000).'
            done
            ';
            file_put_contents($defaultFolder.'/'.$bashFile,$script);
            file_put_contents($defaultFolder.'/'.$taskName,"");

            if(is_file($defaultFolder.'/'.$taskName)){
                $contents = file_get_contents($defaultFolder.'/'.$bashFile);
                if (substr(php_uname(), 0, 7) == "Windows"){
                    $cmd="start /B sh ".str_replace('\\','/',Yii::getAlias('@home')).'/'. $defaultFolder.'/'.$bashFile;
                    //echo $cmd;
                    pclose(popen($cmd, "r"));
                    $task->command=$config['proxy_options']['command'];
                }
                else {
                    $cmd="sh ".Yii::getAlias('@home').'/'.$defaultFolder.'/'.$bashFile . " >/dev/null 2>/dev/null &";
                    shell_exec($cmd);
                    $task->command=$config['proxy_options']['command'];
                }
            }
            $task->stop_file=$defaultFolder.'/'.$taskName;
            $task->save();


        }else{
            //cron
        }
    }
    public function stop(){
        Yii::warning("bot stopped",__METHOD__);
        //The name of the folder.
        $folder = './tasks';

        $files = glob( './tasks/*');

        foreach($files as $file){
            //Make sure that this is a file and not a directory.
            if(is_file($file)){
                unlink($file);
            }
        }
    }
    public function restart(){

    }


}
