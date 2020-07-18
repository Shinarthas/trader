<?php

namespace common\models;

use app\models\Bot;
use frontend\models\Proxy;
use Yii;

/**
 * This is the model class for table "acc_balance".
 *
 * @property int $id
 * @property string $name
 * @property int $currency_id
 * @property double $value
 * @property int $timestamp
 * @property int $status
 * @property int $account_id
 *
 * @property Account $account
 * @property Currency $currency
 */
class BaseActiveRecord extends \yii\db\ActiveRecord
{
    public static $proxyStatic=[];
    public static function curlProxyRequest($url, $host, $port, $user = '', $password = '', $method = 'HTTPS')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, $port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, $method);
        curl_setopt($ch, CURLOPT_PROXY, $host);
        if ($user != '') {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $user . ':' . $password);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function getProxy(){
        $bot=new Bot();
        $config=$bot->getConfig();
        $proxyM=new Proxy();
        $proxy=$proxyM->getProxy($config['proxy_options']['type'],$config['proxy_options']['id']);

    }

    public static function getProxyStatic(){
        $bot=new Bot();
        $config=$bot->getConfig();
        $proxyM=new Proxy();
        $proxy=$proxyM->getProxy($config['proxy_options']['type'],$config['proxy_options']['id']);

    }
    public function getUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy['port']);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTPS');
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy['ip']);
        if(trim($this->proxy['username'])!='' && trim($this->proxy['password'])!=''){
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy['username'] . ':' . $this->proxy['password']);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;

    }
    public static function getUrlStatic($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, self::$proxyStatic['port']);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTPS');
        curl_setopt($ch, CURLOPT_PROXY, self::$proxyStatic['ip']);
        if(trim(self::$proxyStatic['username'])!='' && trim(self::$proxyStatic['password'])!=''){
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, self::$proxyStatic['username'] . ':' . self::$proxyStatic['password']);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;

    }
}
