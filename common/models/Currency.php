<?php

namespace common\models;

use frontend\models\Proxy;
use Yii;
use common\components\ETC;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $name
 * @property string $class
 * @property string $symbol
 * @property int $type
 * @property string $created_at
 * @property int $decimals
 * @property int|null $sort_order
 *
 * @property AccBalance[] $accBalances
 * @property TokenPairs[] $tokenPairs
 * @property TokenPairs[] $tokenPairs0
 */
class Currency extends BaseActiveRecord
{
    const TYPE_TRX = 0;
    const TYPE_TRC20 = 1;

    public static $types = [
        self::TYPE_TRX => 'TRX',
        self::TYPE_TRC20 => 'TRC 20',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'symbol', 'created_at'], 'required'],
            [['type','sort_order','decimals'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'symbol','class'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'symbol' => 'Symbol',
            'class' => 'Class',
            'type' => 'Type',
            'created_at' => 'Created At',
            'sort_order' => 'sort_order',
            'decimals' => 'decimals',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccBalances()
    {
        return $this->hasMany(AccBalance::className(), ['currency_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokenPairs()
    {
        return $this->hasMany(TokenPairs::className(), ['first' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokenPairs0()
    {
        return $this->hasMany(TokenPairs::className(), ['second' => 'id']);
    }
    public static function loadBalance($currency_id, $account) {
        $c = self::findOne($currency_id);

        if($c->type == self::TYPE_TRX)
            return self::loadTrxBalance($c, $account);
        elseif($c->type == self::TYPE_TRC20)
            return self::loadTrc20Balance($c, $account);
    }

    public static function loadTrxBalance($currency, $account) {
        $result = ETC::request(json_encode(['address'=>ETC::address2HexString($account->name)]), 'https://api.trongrid.io/wallet/getaccount');
		
        if(isset($result['balance']))
            return $result['balance']/ 10**$currency->decimals;
        else
            return -1;
    }

    public static function loadTrc20Balance($currency, $account) {
        $result = ETC::request(json_encode([
            'address'=>ETC::address2HexString($account->name),
            'contract_address'=>$currency->address,
            'function_selector'=>'balanceOf(address)',
            'parameter'=>ETC::hexTo64bitHex(substr(ETC::address2HexString($account->name),2))
        ]), false);
        if(isset($result['constant_result']))
            return (hexdec($result['constant_result'][0]))/ 10**$currency->decimals;
        else
            return -1;
    }
	
	public function getData($assoc = true)
    {
        return json_decode($this->data_json,$assoc);
    }
	 
    public function setData($data)
    {
        $this->data_json = json_encode($data);
    }

}
