<?php

use yii\db\Migration;

/**
 * Class m200521_124818_account_balance
 */
class m200521_124818_account_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('account_balance',[
            'id'=>$this->primaryKey(),
            'account_id'=>$this->integer(),
            'balances'=>$this->json(),
            'status'=>$this->integer()->defaultValue(0),
            'timestamp'=>$this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_124818_account_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_124818_account_balance cannot be reverted.\n";

        return false;
    }
    */
}
