<?php

use yii\db\Migration;

/**
 * Class m200601_084402_user_update2
 */
class m200601_084402_user_update2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account','market',$this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200601_084402_user_update2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200601_084402_user_update2 cannot be reverted.\n";

        return false;
    }
    */
}
