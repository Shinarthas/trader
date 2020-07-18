<?php

use yii\db\Migration;

/**
 * Class m200604_092644_notification
 */
class m200604_092644_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('notification',[
            'id'=>$this->primaryKey(),
            'body'=>$this->text(),
            'data'=>$this->json(),
            'created_at'=>$this->dateTime(),
            'is_viewed'=>$this->boolean(),
            'type'=>$this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200604_092644_notification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200604_092644_notification cannot be reverted.\n";

        return false;
    }
    */
}
