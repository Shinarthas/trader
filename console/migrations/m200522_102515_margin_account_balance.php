<?php

use yii\db\Migration;

/**
 * Class m200522_102515_margin_account_balance
 */
class m200522_102515_margin_account_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account_balance','balances_margin',$this->json());
        $this->addColumn('account_balance','total_margin',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_102515_margin_account_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_102515_margin_account_balance cannot be reverted.\n";

        return false;
    }
    */
}
