<?php

namespace common\models;

use common\components\BinanceExchange;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account_balance".
 *
 * @property int $id
 * @property int|null $account_id
 * @property string|null $balances
 * @property int|null $status
 * @property string $timestamp
 * @property float|null $total
 * @property string|null $balances_margin
 * @property float|null $total_margin
 */


//Балансы запимываються раз в 3 часа, в поле balances в формате json, json долден включать
/*
 * [
 *  'currency_id'=>1 id валюты из БД,
 *  'symbol'=>BTC ее символ,
 *  'value'=>1.0 значение на счету
 *  'value_in_orders'=>0.5 значение в ордерах
 *  'usdt_rate'=>8666.4 курс этой валюты к доллару на данный момент
 *
 * ]
 */

//статус баланса 0 значит что это фексированное значение, если 1 то это временный буфер
class AccountBalance extends \yii\db\ActiveRecord
{
    const PERIOD=3600*3; //через сколько фексируем баланс
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'status'], 'integer'],
            [['balances', 'timestamp','balances_margin'], 'safe'],
            [['total','total_margin'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'balances' => 'Balances',
            'status' => 'Status',
            'timestamp' => 'Timestamp',
            'total' => 'Total',
            'balances_margin' => 'Balances Margin',
            'total_margin' => 'Total Margin',
        ];
    }


    //функция запросит текущий баланс с биржи и запишет его в БД (статика)
    public static function checkBalance(Account $account){
        //возьмем список валюты к доллару
        $usdt_remapped=CurrencyToUsd::getUsdtRatesRemapped();


		$exchanger = '\\common\\components\\'.$account->exchanger->class;
        $balances_from_market = $exchanger::getBalance($account);

		if(isset($balances_from_market['error']) OR isset($balances_from_market['balances']['error']))
			return false;
		
        //получим балансы
        $balances=AccountBalance::find()->where(['account_id'=>$account->id])->orderBy('id desc')->limit(3)->all();//трех должно хватить
		
		
        //на всякий случай проверим все ли норм с ПОСЛЕДНЕЙ буферной частью
        if(!empty($balances) && $balances[0]->status!=1){
            $balances[0]->status=1;
            $balances[0]->save();
        }
        //если уже прошло 3 часа, или записей вообще нет, создать новую
        if(count($balances)<3 || strtotime($balances[0]->timestamp)-strtotime($balances[1]->timestamp)>self::PERIOD){
            //скинем статус все по приколу
			
			if(count($balances)!=0 ) {
				foreach ($balances as $balance){
					$balance->status=0;
					$balance->save();
				}
			}
            $edit_balance=new AccountBalance();
        }else{//если 3 часа еще не прошли то просто обновить значение в буферном балансе
            $edit_balance=$balances[0];
            $edit_balance->timestamp = date("Y-m-d H:i:s",time());
        }
		$edit_balance->timestamp = date("Y-m-d H:i:s",time());
        $edit_balance->status=1;
        $edit_balance->account_id=$account->id;
		
		$exchanger::saveBalance($edit_balance, $balances_from_market, $usdt_remapped);
    }
	
	
	
    //Сокет баланс для спота
    public static function checkBalanceSocket(Account $account){
        $balances_from_market=BinanceExchange::getBalanceSocket($account);
    }
    //Сокет баланс для мардиналки
    public static function checkBalanceMarginSocket(Account $account){
        $balances_from_market=BinanceExchange::getBalanceMarginSocket($account);
    }

    //функция для изменения баланса в споте
    public static function balance_update_spot($api, $balances_resp,$account){
        //возьмем список валюты к доллару
        $usdt_remapped=CurrencyToUsd::getUsdtRatesRemapped();
        //получим балансы
        $balances=AccountBalance::find()->where(['account_id'=>$account->id])->orderBy('id desc')->limit(3)->all();//трех должно хватить
        //на всякий случай проверим все ли норм с ПОСЛЕДНЕЙ буферной частью
        if(!empty($balances) && $balances[0]->status!=1){
            $balances[0]->status=1;
            $balances[0]->save();
        }
        //если уже прошло 3 часа, или записей вообще нет, создать новую
        if(count($balances)<3 || strtotime($balances[0]->timestamp)-strtotime($balances[1]->timestamp)>self::PERIOD){
            //скинем статус все по приколу
            foreach ($balances as $balance){
                $balance->status=0;
                $balance->save();
            }
            $edit_balance=new AccountBalance();
			$edit_balance->timestamp = date("Y-m-d H:i:s",time());
        }else{//если 3 часа еще не прошли то просто обновить значение в буферном балансе
            $edit_balance=$balances[0];
        }

        $edit_balance->status=1;
        $edit_balance->account_id=$account->id;
        //запихнем балансы с маржинадки


        $spot_balances=[];
        $spot_total=0;

        foreach ($balances_resp as $symbol=>$market_balance){
            //если ничего нет то не пишем
            if( floatval($market_balance['available'])==0 &&
                floatval($market_balance['onOrder'])==0
            ){
                continue;
            }
            $spot_balance['symbol']=$symbol;
            $spot_balance['available']=$market_balance['available'];
            $spot_balance['onOrder']=$market_balance['onOrder'];
            $spot_balance['rate']=$usdt_remapped[$spot_balance['symbol']]['rate'];
            $spot_balance['currency']=$usdt_remapped[$spot_balance['symbol']]['currency'];

            $spot_total+=($spot_balance['available']+$spot_balance['onOrder'])*$spot_balance['rate'];

            $spot_balances[]=$spot_balance;
        }
     //   $edit_balance->balances=$spot_balances;
        $edit_balance->total=$spot_total;
        $edit_balance->save();
     //   print_r($edit_balance->errors);

    }
    //функция для изменения баланса маржинального кошелька
    public static function balance_update_margin($api, $balances_resp,$account){
        echo "aaa";
        //возьмем список валюты к доллару
        $usdt_remapped=CurrencyToUsd::getUsdtRatesRemapped();
        //получим балансы
        $balances=AccountBalance::find()->where(['account_id'=>$account->id])->orderBy('id desc')->limit(3)->all();//трех должно хватить
        //на всякий случай проверим все ли норм с ПОСЛЕДНЕЙ буферной частью
        if(!empty($balances) && $balances[0]->status!=1){
            $balances[0]->status=1;
            $balances[0]->save();
        }
        //если уже прошло 3 часа, или записей вообще нет, создать новую
        if(count($balances)<3 || strtotime($balances[0]->timestamp)-strtotime($balances[1]->timestamp)>self::PERIOD){
            //скинем статус все по приколу
            foreach ($balances as $balance){
                $balance->status=0;
                $balance->save();
            }
            $edit_balance=new AccountBalance();
			$edit_balance->timestamp = date("Y-m-d H:i:s",time());
        }else{//если 3 часа еще не прошли то просто обновить значение в буферном балансе
            $edit_balance=$balances[0];
        }

        $edit_balance->status=1;
        $edit_balance->account_id=$account->id;
        //запихнем балансы с маржинадки


        $margin_balances=[];
        $margin_total=0;

        foreach ($balances_resp as $market_balance_tmp){
            $market_balance=ArrayHelper::toArray($market_balance_tmp);
            //если ничего нет то не пишем
            if( floatval($market_balance['f'])==0 &&
                floatval($market_balance['l'])==0
            ){
                continue;
            }
            //нудно найти этот баланс в имеющейся БД иначе мы перезапишем информацию о долгах
            $tmp_balance=$edit_balance->balances_margin;
            foreach ($tmp_balance as $bl){
                if($bl['symbol']==$market_balance['a']){
                    //у нас есть эта валюта
                    $margin_balance=$bl;
                    echo "we found exsistng balance";
                }
            }
            $margin_balance['symbol']=$market_balance['a'];
            $margin_balance['free']=floatval($market_balance['f']);
            $margin_balance['locked']=floatval($market_balance['l']);
            $margin_balance['rate']=$usdt_remapped[$margin_balance['symbol']]['rate'];
            $margin_balance['currency']=$usdt_remapped[$margin_balance['symbol']]['currency'];

            $margin_total+=($margin_balance['free']+$margin_balance['locked']-$margin_balance['borrowed'])*$margin_balance['rate'];

            $margin_balances[]=$margin_balance;
        }
        print_r($margin_balances);
        echo "$ $margin_total";
        $edit_balance->balances_margin=$margin_balances;
        $edit_balance->total_margin=$margin_total;
        $edit_balance->save();
        print_r($edit_balance->errors);

    }
	
	public function getMargin_main_balance($limit = 0.1) {

		
		$out = [];
		foreach($this->balances_margin as $b) {
			$amount = $b['free']+$b['locked']+$b['amount'];
			$usdt_cost = $amount*$b['rate'];
			if($usdt_cost<$limit AND $b['borrowed']*$b['rate'] < $limit)
				continue;
			
			$out[] = $b;
		}
		return $out;
	}
	
	public function getAccount() {
		return $this->hasOne(Account::className(), ['id' => 'account_id']);
	}
}
