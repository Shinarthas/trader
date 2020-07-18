<?php

use yii\db\Migration;

/**
 * Class m200601_100116_account_update_3
 */
class m200601_100116_account_update_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account','deposit',$this->float());
        $this->addColumn('account','in_position',$this->integer()->defaultValue(0));
        $this->addColumn('account','currency',$this->string()->defaultValue('BTC'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200601_100116_account_update_3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200601_100116_account_update_3 cannot be reverted.\n";

        return false;
    }
    */
}
