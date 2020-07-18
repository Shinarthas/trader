<?php


namespace common\assets;


use common\models\Log;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Exception;
use yii\di\Instance;
use yii\helpers\VarDumper;
use yii\log\LogRuntimeException;
use yii\log\Target;

class CustomTarget extends Target
{




    /**
     * Stores log messages to DB.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws Exception
     * @throws LogRuntimeException
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            if($category!='application'
                && strpos($category,'yii\log\Dispatcher')===false
                && strpos($category,'yii\debug\Module')===false)
                Log::log([$level,$category,$timestamp,$this->getMessagePrefix($message),$text]);
        }
    }
}