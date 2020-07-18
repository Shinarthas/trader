<?php

use yii\db\Migration;

/**
 * Class m200527_124517_order
 */
class m200527_124517_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `sell` int(11) NOT NULL,
  `tokens_count` decimal(15,6) NOT NULL,
  `rate` decimal(15,6) NOT NULL,
  `progress` int(11) NOT NULL,
  `data_json` text NOT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `market_order_id` varchar(255) DEFAULT NULL,
  `canceled` int(11) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `loaded_at` int(11) NOT NULL,
  `start_rate` decimal(50,10) DEFAULT NULL,
  `currency_one` int(11) NOT NULL,
  `currency_two` int(11) NOT NULL,
  `is_user` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3072 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;");

        $result = $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200527_124517_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200527_124517_order cannot be reverted.\n";

        return false;
    }
    */
}
