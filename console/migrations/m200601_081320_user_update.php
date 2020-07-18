<?php

use yii\db\Migration;

/**
 * Class m200601_081320_user_update
 */
class m200601_081320_user_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account','sum_st',$this->integer()->defaultValue(1));
        $this->addColumn('account','sum_fix',$this->float()->defaultValue(1));
        $this->addColumn('account','percent_sum',$this->float()->defaultValue(1));
        $this->addColumn('account','shoulder',$this->float()->defaultValue(1));
        $this->addColumn('account','shoulder_add',$this->float()->defaultValue(1.3));
        $this->addColumn('account','order_open_type',$this->integer()->defaultValue(1));
        $this->addColumn('account','limited_order_percent_open',$this->float()->defaultValue(0.002));
        $this->addColumn('account','order_open_threshold_open',$this->float()->defaultValue(20));
        $this->addColumn('account','order_timeout_open',$this->integer()->defaultValue(20));
        $this->addColumn('account','shoulder_down',$this->float()->defaultValue(0.5));
        $this->addColumn('account','order_close_type',$this->integer()->defaultValue(1));
        $this->addColumn('account','limited_order_percent_close',$this->float()->defaultValue(-0.002));
        $this->addColumn('account','order_open_threshold_close',$this->float()->defaultValue(20));
        $this->addColumn('account','order_timeout_close',$this->integer()->defaultValue(20));
        $this->addColumn('account','currency',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200601_081320_user_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200601_081320_user_update cannot be reverted.\n";

        return false;
    }
    */
}
