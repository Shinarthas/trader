<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property string|null $info
 * @property string|null $message
 * @property string|null $type
 * @property string|null $created_at
 * @property string|null $body
 * @property string|null $class
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['info', 'created_at'], 'safe'],
            [['body'], 'string'],
            [['message', 'type', 'class'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'info' => 'Info',
            'message' => 'Message',
            'type' => 'Type',
            'created_at' => 'Created At',
            'body' => 'Body',
            'class' => 'Class',
        ];
    }

    public static function log($data,$type='error',$message=''){
        $log=new Log();
        if(is_object($data))
            $data=json_decode(json_encode($data),true);


        if(isset($data[4])){
            $log->body=$data[4];
            $log->class='INTERNAL';
        }
        $log->type=$type;
        $log->message=$message;
        $log->created_at=date('Y-m-d H:i:s',time());

        $log->info=$data;
        $log->save();
        return $log->errors;
    }
}
