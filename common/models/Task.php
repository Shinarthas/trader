<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Task".
 *
 * @property int $id
 * @property int $freq
 * @property string $command
 * @property string $alias
 * @property int $status
 * @property string $name
 * @property string $activated_at
 * @property string $stop_file
 * @property string $start_type
 * @property string $current_url
 * @property int $parse_counter
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
            [['freq', 'status', 'parse_counter'], 'integer'],
            [['activated_at'], 'safe'],
            [['command', 'name', 'stop_file'], 'string', 'max' => 255],
            [['alias', 'start_type', 'current_url'], 'string', 'max' => 250],
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
            'alias' => 'Alias',
            'status' => 'Status',
            'name' => 'Name',
            'activated_at' => 'Activated At',
            'stop_file' => 'Stop File',
            'start_type' => 'Start Type',
            'current_url' => 'Current Url',
            'parse_counter' => 'Parse Counter',
        ];
    }
}
