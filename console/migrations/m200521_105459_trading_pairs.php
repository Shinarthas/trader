<?php

use yii\db\Migration;

/**
 * Class m200521_105459_trading_pairs
 */
class m200521_105459_trading_pairs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("DROP TABLE IF EXISTS `global_pair`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_pair` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trading_pair` varchar(255) DEFAULT NULL,
  `bid` float DEFAULT NULL,
  `bids` float DEFAULT NULL,
  `ask` float DEFAULT NULL,
  `asks` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` float DEFAULT NULL,
  `price_change` double DEFAULT NULL,
  `price_change_percent` double DEFAULT NULL,
  `weighted_avg_price` double DEFAULT NULL,
  `last_price` double DEFAULT NULL,
  `last_qty` double DEFAULT NULL,
  `open_price` double DEFAULT NULL,
  `high_price` double DEFAULT NULL,
  `low_price` double DEFAULT NULL,
  `volume` double DEFAULT NULL,
  `volume_24h_change` double DEFAULT NULL,
  `quote_volume` double DEFAULT NULL,
  `quote_volume_24h_change` double DEFAULT NULL,
  `prediction` float DEFAULT NULL,
  `currency_group` int(11) DEFAULT '1',
  `result` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-post-trading_pair` (`trading_pair`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

        $result = $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_105459_trading_pairs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_105459_trading_pairs cannot be reverted.\n";

        return false;
    }
    */
}
