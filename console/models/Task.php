<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $freq
 * @property string $command
 * @property int $status
 * @property string $name
 * @property string $activated_at
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['freq', 'status'], 'integer'],
            [['activated_at'], 'safe'],
            [['command', 'name','stop_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'freq' => 'Freq',
            'command' => 'Command',
            'status' => 'Status',
            'name' => 'Name',
            'activated_at' => 'Activated At',
        ];
    }
}
