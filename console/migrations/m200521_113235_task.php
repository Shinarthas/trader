<?php

use yii\db\Migration;

/**
 * Class m200521_113235_task
 */
class m200521_113235_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `freq` int(11) DEFAULT NULL,
  `command` varchar(255) DEFAULT NULL,
  `alias` char(250) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `stop_file` varchar(255) DEFAULT NULL,
  `start_type` char(250) DEFAULT NULL,
  `current_url` char(250) DEFAULT NULL,
  `parse_counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

        $result = $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_113235_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_113235_task cannot be reverted.\n";

        return false;
    }
    */
}
