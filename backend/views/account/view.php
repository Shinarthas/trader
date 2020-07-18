<?
/* @var $this yii\web\View */
/* @var $a common\models\Account */
/* @var $account_types array */

	use common\components\ApiRequest;
use yii\web\View;

?>
<style>
table tr>th {
	padding:4px 10px;
	text-align:center;
}
table tr>* {
	border:1px solid #555;
	padding:4px;
}
</style>

 <?=$a->label;?>
<?
	$currencies = [];
	$req = ApiRequest::statistics('v1/acc-balance/index', ['account_id'=>$a->id, 'limit'=>24]);
	$data = [];
	foreach($req->data as $timestamp=>$balances) {
	    foreach ($balances->balances as $balance){
            $currencies[$balance->name] = 1;
            $data[$balance->timestamp/120][$balance->name] = [$balance->value, $balance->value_in_orders];
        }
        $currencies["USD"]=1;
        $data[$timestamp/120]['USD'] = [$balances->in_usd];

    }
?>
<form method="POST">
    <input type="hidden" name="id" value="<?=$a->id?>">
    <?php $account_index=array_search($a->type,array_column($account_types,'id')) ?>
    <?php foreach ($account_types[$account_index]['json_fields'] as $ap){ ?>
        <p><?=$ap?>: <input name="data[<?=$ap?>]"></p>
    <?php }?>
    <p>Account label: <input name="label"  value="<?=$a->label?>"></p>
    <p>Account name: <input name="name" value="<?=$a->name?>"></p>
    <p>Account password: <input name="password"></p>
<p>Check balance:
    <select name="check_balance">
        <option value="1" <?=$a->check_balance? 'selected':''?>>Enabled</option>
        <option value="0" <?=!$a->check_balance? 'selected':''?>>Disabled</option>
    </select>
</p>


<p>
    <button class="btn-primary btn">Update Balance</button>
</p>
</form>


<br>
<br>
<br>
<table>
	<tr>
		<th rowspan = 2>time</th>
		<? foreach($currencies as $currency_name => $unused): ?>
        <?php if($currency_name=='USD') continue; ?>
			<th colspan = 3><?=$currency_name?></th>
		<? endforeach;?>
	</tr>
	<tr>
		<? foreach($currencies as $currency_name => $unused): ?>

            <?php if($currency_name=='USD') continue; ?>
			<th>balance</th>
			<th>orders</th>
			<th>total</th>
		<? endforeach;?>
        <td>Total Usd value</td>
	</tr>
	<? foreach($data as $time=>$balance): ?>
		<tr>
			<td><?=date("d H:i",$time*120)?></td>
			<? foreach($currencies as $currency_name => $unused): ?>
                <?php if($currency_name=='USD') continue; ?>
				<td><?=$balance[$currency_name][0]?></td>
				<td><?=$balance[$currency_name][1]?></td>
				<td><?=$balance[$currency_name][0]+$balance[$currency_name][1];?></td>
			<? endforeach; ?>
            <td><?=$balance['USD'][0]?></td>
		</tr>
	<? endforeach;?>
</table>