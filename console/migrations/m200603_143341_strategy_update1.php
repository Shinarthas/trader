<?php

use yii\db\Migration;

/**
 * Class m200603_143341_strategy_update1
 */
class m200603_143341_strategy_update1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order','is_downgrade', $this->boolean());
        $this->addColumn('order', 'local_max',$this->float());

        $this->addColumn('account','is_downgrade',$this->boolean());

        $this->addColumn('account','take_profit', $this->float());
        $this->addColumn('account','stop_loss', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_143341_strategy_update1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_143341_strategy_update1 cannot be reverted.\n";

        return false;
    }
    */
}
