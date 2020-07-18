<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string|null $body
 * @property string|null $data
 * @property string|null $created_at
 * @property int|null $is_viewed
 * @property int|null $type
 */
class Notification extends \yii\db\ActiveRecord
{
    const INTERVAL=1800; //таймаут что-бы не спамить уведомления
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['body'], 'string'],
            [['data', 'created_at'], 'safe'],
            [['is_viewed', 'type'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'body' => 'Body',
            'data' => 'Data',
            'created_at' => 'Created At',
            'is_viewed' => 'Is Viewed',
            'type' => 'Type',
        ];
    }

    public static function make($message,$data=[],$type=1){
        //найдем есть ли уже такое уведомление, за последний час
        $noty=Notification::find()->where(['body'=>$message])
            ->andWhere(['>','created_at',time()-self::INTERVAL])
            ->all();

        //если за последнее время было точно такое уведовление, то ничего не делать
        if(!empty($noty))
            return;

        $notification=new Notification();
        $notification->body=$message;
        $notification->data=$data;
        $notification->is_viewed=0;
        $notification->type=$type;
        $notification->created_at=date("Y-m-d H:i:s",time());
        $notification->save();

        return $notification;
    }
}
