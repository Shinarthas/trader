<?php

use yii\db\Migration;

/**
 * Class m200521_130459_account_balance_total
 */
class m200521_130459_account_balance_total extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account_balance','total',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_130459_account_balance_total cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_130459_account_balance_total cannot be reverted.\n";

        return false;
    }
    */
}
