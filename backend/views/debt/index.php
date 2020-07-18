<style>
table tr td {
	padding: 3px 6px;
	border: 1px solid #eee;
}
.debt {
	color:#ff6464;
	cursor:pointer;
}
table tr td:not(:first-child) {
	text-align:center;
}
</style>


<? 
$currencies_ids = [];
$currency_borrow_isset = [];
$account_borrow_amount = [];
$currencies_used = [];

foreach($currencies as $c)
	$currencies_ids[$c->symbol] = $c->id;

foreach($accounts as $a) {
	$main_balance = $a->last_balance->getMargin_main_balance(0);
	 foreach($main_balance as $balance) {
                    if($balance['borrowed']!=0) {
						$currency_borrow_isset[$balance['currency']] = true;
						$account_borrow_amount[$a->id][$balance['currency']] = $balance['borrowed'];
					}
                }
}
?>

<div class="modal fade" id="debtModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Confirm debt repay</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    margin-top: -21px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure?
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="make_debt_repay">Make this</button>
      </div>
    </div>
  </div>
</div>

<table>
<tr>
<td>Account / Currency</td>
<? foreach($currencies as $c) : ?><?	if($currency_borrow_isset[$c->id]): 
			$currencies_used[] = $c->id;
	?><td><?=$c->symbol; ?></td><? endif; ?><? endforeach; ?>

	<td>Actions</td>
</tr>
<? foreach($accounts as $a): ?>
	<tr>
		<td><?=$a->name;?></td>
		<? foreach($currencies_used as $currency_id): ?>
			<td class="debt" data-account_id="<?=$a->id;?>" data-currency_id="<?=$currency_id;?>" data-toggle="modal" data-target="#debtModal"><? if($account_borrow_amount[$a->id][$currency_id]!=0) { echo "-";} ?> <?=$account_borrow_amount[$a->id][$currency_id]; ?></td>
		<? endforeach; ?>
		<td><button class="btn btn-primary" data-toggle="modal"  data-target="#debtModal" data-account_id="<?=$a->id;?>">Repay all</button></td>
	</tr>
<? endforeach; ?>
</table>

<script>
let last_account_id = 0;
let last_currency_id = 0;

$(function(){
	$(".debt").click(function(){
		last_account_id = $(this).data("account_id");
		last_currency_id = $(this).data("currency_id");
	})
	
	$("#make_debt_repay").click(function(){
		$("#debtModal .close").click();
		
		$.ajax({
			url : '/debt/repay',
			method: 'POST',
			data: {
				account_id : last_account_id,
				currency_id : last_currency_id
			}
		})
		
	})
	
})
</script>