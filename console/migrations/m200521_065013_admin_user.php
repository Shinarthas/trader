<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m200521_065013_admin_user
 */
class m200521_065013_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$admin  = new User;
		$admin->username = 'admin';
        $admin->email = 'admin';
        $admin->setPassword('oxmvb3mpouiwtmN11n');
        $admin->generateAuthKey();
        $admin->generateEmailVerificationToken();
		$admin->status = User::STATUS_ACTIVE;
        return $admin->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_065013_admin_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_065013_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
