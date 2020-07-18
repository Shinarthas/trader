<?php

use yii\db\Migration;

/**
 * Class m200608_182423_markets_added
 */
class m200608_182423_markets_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute("
CREATE TABLE `market` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT INTO `market` (`id`, `name`, `class`) VALUES
(1, 'Binance', 'BinanceExchange'),
(2, 'Bitfinex', 'BitfinexExchange');

ALTER TABLE `market`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `market`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200608_182423_markets_added cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200608_182423_markets_added cannot be reverted.\n";

        return false;
    }
    */
}
