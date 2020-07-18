<?php

use yii\db\Migration;

/**
 * Class m200528_100702_log
 */
class m200528_100702_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info` json DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `body` mediumtext,
  `class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;");

        $result = $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200528_100702_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200528_100702_log cannot be reverted.\n";

        return false;
    }
    */
}
