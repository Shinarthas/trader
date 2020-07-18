<?php

use yii\db\Migration;

/**
 * Class m200529_120603_sort_currency
 */
class m200529_120603_sort_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $usdt_remapped=\common\models\CurrencyToUsd::getUsdtRatesRemapped();

        $currencies=\common\models\Currency::find()->all();

        foreach ($currencies as $currency){
            $currency->decimals=2;
            if(isset($usdt_remapped[$currency->symbol])){

                if($usdt_remapped[$currency->symbol]['rate']>10)
                    $currency->decimals=4;
                if($usdt_remapped[$currency->symbol]['rate']>100)
                    $currency->decimals=5;
                if($usdt_remapped[$currency->symbol]['rate']>1000)
                    $currency->decimals=7;

            }
            $currency->save();
        }
		
        $usdt_remapped=\common\models\CurrencyToUsd::getUsdtRatesRemapped();

        $currencies=\common\models\Currency::find()->all();

        foreach ($currencies as $currency){
            $currency->sort_order=0;
            if(isset($usdt_remapped[$currency->symbol])){
                $currency->sort_order=intval($usdt_remapped[$currency->symbol]['rate']);

            }
            $currency->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200529_120603_sort_currency cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_120603_sort_currency cannot be reverted.\n";

        return false;
    }
    */
}
