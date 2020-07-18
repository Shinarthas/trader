<?php

use yii\db\Migration;
use common\models\GlobalPair;

/**
 * Class m200529_115208_fix_decimals
 */
class m200529_115208_fix_decimals extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('currency','sort_order', $this->integer()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200529_115208_fix_decimals cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_115208_fix_decimals cannot be reverted.\n";

        return false;
    }
    */
}
