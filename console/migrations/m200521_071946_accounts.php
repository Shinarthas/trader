<?php

use yii\db\Migration;

/**
 * Class m200521_071946_accounts
 */
class m200521_071946_accounts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('account', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
			'status' => $this->integer()->notNull(),
			'group_id' => $this->integer()->notNull(),
			'data_json' => $this->text()->notNull(),
			'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_071946_accounts cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_071946_accounts cannot be reverted.\n";

        return false;
    }
    */
}
