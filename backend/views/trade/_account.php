
<div class="account_container" id="account_<?=$account->id;?>" data-status="<?=$account->status?>" data-id="<?=$account->id?>">
	<div class="row">
        <div class="col-xs-9">
			<div>
				<div class="status_icon status_<?=$account->status?>" data-status="<?=$account->status?>" style="float:left;"></div>
				<span style="line-height:45px;float:left; width:210px;">
		<?=$account->name;?>
		</span>

				<div class="account_balance">
					 <?php
					foreach($account->margin_main_balance as $balance) {
						$amount = '';
						$in_orders = '';
						if($balance['locked']!=0)
							$in_orders = ' (+'.number_format($balance['locked'],$currencies[$balance['currency']]->decimals).')';

						if($balance['amount']!=0)
							$amount = '/ '.number_format($balance['amount'],$currencies[$balance['currency']]->decimals);
						
						$borrowed = '';
						if($balance['borrowed']*$balance['rate']>0.1)
							$borrowed = ' <span class="borrowed">-'.number_format($balance['borrowed'],$currencies[$balance['currency']]->decimals).'</span>';
						
						if($balance['in_position']==1) {
							echo '<div class="margin_position">'.$balance['symbol'].': '.number_format($balance['amount'],$currencies[$balance['currency']]->decimals).
								" ".$in_orders.
								" ".$borrowed.'</div>';
						}
						else
						{
							echo '<div>'.$balance['symbol'].': '.number_format($balance['free'],$currencies[$balance['currency']]->decimals).
								" ".$in_orders.$amount.
								" ".$borrowed.'</div>';
						}
					}
					?>
				</div>
			</div>
			<div class="clearfix"></div>
			<div style="    margin: 0 10px 10px;">
				Status: <label class="status_string"><?=$account->status_string;?></label> <a href="/account/<?=$account->id;?>/stat" style="margin-left:20px;">Orders list</a>
				<div class="last-order-status">
				</div>
			</div>
        </div>
		 <div class="col-xs-3">
		 	<div class="nb-spinner" style="    float: right; display:none; margin: 15px;"></div>	

			<div style="float:right;" class="buttons">
				
				<div class="select">
					<button  class="btn btn-primary btn-md" data-id=<?=$account->id;?> style="margin: 6px;">Select</button>
				</div>
				
				<div class="deselect">
					<img src="/images/check.png" style="height:13px;">  <button class="btn btn btn-md" data-id=<?=$account->id;?> style="margin: 6px;">Deselect</button>
				</div>
				


			</div>
		 </div>
    </div>
	<div class="clearfix"></div>
</div>
