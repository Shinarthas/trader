<?php

use yii\db\Migration;

/**
 * Class m200604_092644_notification
 */
class m200604_092644_strat2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account','strategy_id',$this->integer());
        $this->addColumn('strategy','name',$this->string());
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
