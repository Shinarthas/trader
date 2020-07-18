<?php

use yii\db\Migration;

/**
 * Class m200604_092644_notification
 */
class m200604_092644_strat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('strategy',[
            'id'=>$this->primaryKey(),
            'created_at'=>$this->dateTime(),
        ]);
        $this->addColumn('strategy','sum_st',$this->integer()->defaultValue(1));
        $this->addColumn('strategy','sum_fix',$this->float()->defaultValue(1));
        $this->addColumn('strategy','percent_sum',$this->float()->defaultValue(1));
        $this->addColumn('strategy','shoulder',$this->float()->defaultValue(1));
        $this->addColumn('strategy','shoulder_add',$this->float()->defaultValue(1.3));
        $this->addColumn('strategy','order_open_type',$this->integer()->defaultValue(1));
        $this->addColumn('strategy','limited_order_percent_open',$this->float()->defaultValue(0.002));
        $this->addColumn('strategy','order_open_threshold_open',$this->float()->defaultValue(20));
        $this->addColumn('strategy','order_timeout_open',$this->integer()->defaultValue(20));
        $this->addColumn('strategy','shoulder_down',$this->float()->defaultValue(0.5));
        $this->addColumn('strategy','order_close_type',$this->integer()->defaultValue(1));
        $this->addColumn('strategy','limited_order_percent_close',$this->float()->defaultValue(-0.002));
        $this->addColumn('strategy','order_open_threshold_close',$this->float()->defaultValue(20));
        $this->addColumn('strategy','order_timeout_close',$this->integer()->defaultValue(20));
        $this->addColumn('strategy','pair',$this->string()->defaultValue('BTCUSDT'));

        $this->addColumn('strategy','is_downgrade',$this->boolean());

        $this->addColumn('strategy','take_profit', $this->float());
        $this->addColumn('strategy','stop_loss', $this->float());
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
