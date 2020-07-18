<?php

use yii\db\Migration;

/**
 * Class m200521_105244_usdtrates
 */
class m200521_105244_usdtrates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("DROP TABLE IF EXISTS `currency_to_usd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency_to_usd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` int(11) NOT NULL,
  `rate` float NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

        $result = $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_105244_usdtrates cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_105244_usdtrates cannot be reverted.\n";

        return false;
    }
    */
}
