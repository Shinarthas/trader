<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "market".
 *
 * @property int $id
 * @property string $name
 * @property string $class
 */
class Market extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'market';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'class'], 'required'],
            [['name', 'class'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'class' => 'Class',
        ];
    }
}
