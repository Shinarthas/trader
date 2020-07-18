<style>
.hidden {
	display:none;
}
.group_container.minimized .list {
	display:none;
}
.group_container>h4 {
	    background: #f2f2f2;
    padding: 10px;
	    margin: 0;
		cursor:pointer;
}
.group_container {
/*	border:1px solid #eee;*/
	border-left:4px solid #eee;margin-bottom:15px;
}


.account_container.selected .deselect, .account_container:not(.selected) .select {
	display:inline-block;
}
.account_container:not(.selected) .deselect,.account_container.selected .select{
	display:none;
}
.wrap > .container {
	padding-top:35px;
}

.account_balance {
	float:left;
	margin-top:10px;
	margin-bottom:5px;
}
.account_balance>div {
	padding:3px;
}
.account_balance>div.margin_position::before {
    content: " ";
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin: 5px;
    float: left;
	background:#337ab7;
}
.borrowed {
	color:red;
}
.last-order-status .error {
	margin: 0 0 4px;
}
</style>
<?

	$groups = [
		1 => 'A',
		2 => 'B',
		3 => 'C'
	];
?>

<!-- Modals -->
<div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Purchase signal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    margin-top: -21px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group field-signal-name required">
			<label class="control-label" for="signal-name">Name</label>
			<input type="text" id="signal-name" class="form-control" value="Default signal">
		</div>
		<div class="form-group field-signal-currency_id required">
			<label class="control-label" for="signal-currency_id">Currency</label>
			<select id="signal-currency_id" class="form-control" aria-required="true" aria-invalid="false">
                <?php foreach ($currencies as $currency){ ?>
                    <option value="<?=$currency->id?>"><?=$currency->symbol?></option>
                <?php } ?>

			</select>
		</div>
		<div class="form-group field-signal-shoulder required">
			<label class="control-label" for="signal-shoulder">Shoulder</label>
			<input type="text" id="signal-shoulder" class="form-control" value="1.3">
		</div>
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="make_purchase">Make this</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="sellModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sell signal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    margin-top: -21px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
               <div class="form-group field-signal-name required">
			<label class="control-label" for="signal-name">Name</label>
			<input type="text" id="signal-name" class="form-control" value="Default signal">
		</div>
		<div class="form-group field-signal-shoulder required">
			<label class="control-label" for="signal-shoulder">Auto repay</label>
			<input type="checkbox" id="auto_repay" style="width: 50px;" class="form-control" checked>
		</div>
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="make_sell">Make this</button>
      </div>
    </div>
  </div>
</div>


<div style="position:fixed;background:white; padding:20px 0;z-index: 100;">
<h3 style="width:843px; position:relative;" >Selected accounts: <span class="selected_counter">0</span><div style="float:right;">
	<button class="btn btn-primary btn-md select_all_group" style="width:120px;" id="purchase_button" data-toggle="modal" data-target="#purchaseModal">PURCHASE</button>
	<button class="btn btn-danger btn-md select_all_group" style="width:120px;" id="sell_button" data-toggle="modal" data-target="#sellModal">SELL</button>
</div></h3>
</div>


<div style="width:100%;height:105px;"></div>
<div class="row">
    <div class="col-md-8">
        <? foreach($groups as $group_id => $name): ?>

            <div class="group_container <? if(count($accounts[$group_id])==0) echo "minimized"; ?>" id="group_<?=$group_id;?>" >
                <h4>Group <?=$name;?> <small style="float:right;line-height: 24px;">selected <span class="selected_group_counter">0</span> / <?=count($accounts[$group_id]);?> accounts</small></h4>

                <?
                if(count($accounts[$group_id])==0): ?>
                    <div class="list">
                        <p style="padding:10px;margin:0;">Group is empty</p>
                    </div>
                <? else:?>


                    <div class="list">

                        <?
                        foreach($accounts[$group_id] as $a): ?>
                            <?=$this->render("_account", ['account'=>$a,'currencies'=>$currencies]);?>
                        <? endforeach;?>

                        <a style="float:right;">
                            <button type="submit" class="btn btn-primary btn-md select_all_group" data-group_id="<?=$group_id;?>" data-status=1 style="margin: 6px;">Select all free</button><button type="submit" class="btn btn-primary btn-md select_all_group" data-group_id="<?=$group_id;?>" data-status=4 style="margin: 6px;">Select all in position</button>
                        </a>
                        <div class="clearfix"></div>
                    </div>

                <? endif; ?>

            </div>


        <? endforeach; ?>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-12">
                <div class="card stat-widget-one bg-btc">
                    <div class="card-body">
                        <h4 class="card-title">Last Orders:</h4>
                        <div style="    height: 500px; overflow-y: auto;">
                            <table class="table" style="width: 100%; margin-bottom: 0;">
                                <thead>
                                <tr>
                                    <th>Pairs</th>
                                    <th>Side</th>
                                    <th>Status</th>
                                    <th>action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <style>
                                    .sell-0{
                                        color: green;
                                    }
                                    .sell-1{
                                        color: red;
                                    }
                                </style>
                                <?php foreach ($orders as $order){ ?>
                                    <tr>

                                        <td><?=$order->currencyOne->symbol?>/<?=$order->currencyTwo->symbol?></td>
                                        <td class="sell-<?= $order->sell?>">
                                            <?= $order->sell?'SELL':'BUY'?>
                                        </td>
                                        <td class="pr-1 order_status_<?=$order->id;?>"><?= \common\models\Order::$statuses[$order->status]?></td>
                                        <td>
                                            <?php if($order->status==\common\models\Order::STATUS_CREATED || $order->status==\common\models\Order::STATUS_FAILED){ ?>
                                                <div class="nb-spinner" style="    float: right; display:none; margin: 15px;"></div>
                                                <a class="btn-sm btn btn-danger" onclick="cancelOrder(<?=$order->id;?>)">cancel</a>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


	<? 
	$c_decimals = [0];
	foreach($currencies as $c)
		$c_decimals[$c->id] = $c->decimals;
	?>
	
<script>

$(function(){
		let status_names = [
		 'inactive',
		 'available',
		 'purchasing',
		 'selling',
		 'in position',
		 'disabled',
		 'hidden',
	];
	
		
	currency_decimals = <?=json_encode($c_decimals);?>
	
	
	$(".group_container>h4").click(function(){
		$(this).parent().toggleClass("minimized");
//		$(this).parent().find(".list").toggleClass("hidden");
		
	})
	
	$(".account_container .select button").click(function(){
		id = $(this).data("id");
		$("#account_"+id).addClass("selected");
		group_counter = $("#account_"+id).parent().parent().find(".selected_group_counter").html()*1;
		group_counter++;
		$("#account_"+id).parent().parent().find(".selected_group_counter").html(group_counter);
		
		selected_counter = $(".selected_counter").html()*1;
		selected_counter++;
		$(".selected_counter").html(selected_counter);
		
		calcPosibleToBuyAndSell();
	})
	
	$(".account_container .deselect button").click(function(){
		id = $(this).data("id");
		$("#account_"+id).removeClass("selected");
		group_counter = $("#account_"+id).parent().parent().find(".selected_group_counter").html()*1;
		group_counter--;
		$("#account_"+id).parent().parent().find(".selected_group_counter").html(group_counter);
		
		selected_counter = $(".selected_counter").html()*1;
		selected_counter--;
		$(".selected_counter").html(selected_counter);
		
		calcPosibleToBuyAndSell();
	})
	
	$(".select_all_group").click(function(){
		group_id = $(this).data("group_id");
		status = $(this).data("status");

		$("#group_"+group_id+" .account_container:not(.selected)").each(function(){
			if($(this).data("status")==status)
				$(this).find(".select>button").click();
		});
	})
	
	function calcPosibleToBuyAndSell() {
		if($(".account_container.selected").length==0) {
			//$("#sell_button").addClass("disabled");
			//$("#purchase_button").addClass("disabled");
			return;
		}
		
		//$("#sell_button").removeClass("disabled");
		//$("#purchase_button").removeClass("disabled");
		$(".account_container.selected").each(function(){
			status = $(this).data("status");

//			if(status == 1)
//				$("#sell_button").addClass("disabled");
//			if(status == 4)
//				$("#purchase_button").addClass("disabled");
		})
	}
	
	$("#purchase_button,#sell_button").click(function(e){
//		if($(this).hasClass("disabled")){
//			e.preventDefault();
//			return false;
//		}
	})
	
	$("#make_purchase").click(function(){
		i=0;
		account_ids = [];
		$(".account_container.selected").each(function(){ 
			account_ids[i]= $(this).data("id");
			$(this).find(".nb-spinner").show();
			$(this).find(".buttons").hide();
			$("#account_"+$(this).data("id")+" .last-order-status").empty();
			i++;
		})
		$("#purchaseModal .close").click();
		$.ajax({
			url: "/trade/purchase",
			method: "POST",
			data: {
			    'account_ids': account_ids,
                'currency_id':$("#signal-currency_id").val(),
                'shoulder':$("#signal-shoulder").val(),
                'name':$("#signal-name").val(),
            },
			success: function(res) {
				for(let i in res) {
					updateBalance(i, res[i].balance);
					$("#account_" + i + " .last-order-status").empty();
					
					for(let j in res[i].result) {
						if(res[i].result[j].clientOrderId != null || res[i].result[j].id != null) {
							if(res[i].result[j].clientOrderId != null)
								$("#account_" + i + " .last-order-status").append("<div>" +res[i].result[j].symbol+ ": Order success!  OrderId: "+  res[i].result[j].orderId +". Status: " + res[i].result[j].status + "</div>");
							else
								$("#account_" + i + " .last-order-status").append("<div>" +res[i].result[j].symbol+ ": Order success!  OrderId: "+  res[i].result[j].id +". Exchange: " + res[i].result[j].exchange + "</div>");
						}
						else{
							if( res[i].result[j].msg != null)
								$("#account_" + i + " .last-order-status").append("<p class='error'>" +res[i].result[j].symbol+ ": Error! "+res[i].result[j].msg+". Status: " + res[i].result[j].status + "</p>");
							else
								$("#account_" + i + " .last-order-status").append("<p class='error'>" +res[i].result[j].symbol+ ": Error! "+res[i].result[j].message);
						}
					}
				}
				
				$(".nb-spinner").hide();
				$(".buttons").show();
				deselectAll();
				
				updateStatuses();
			}
		})
	})
	
	$("#make_sell").click(function(){
		i=0;
		account_ids = [];
		$(".account_container.selected").each(function(){ 
			account_ids[i]= $(this).data("id");
			$(this).find(".nb-spinner").show();
			$(this).find(".buttons").hide();
			$("#account_"+$(this).data("id")+" .last-order-status").empty();
			i++;
		})
		
		$("#sellModal .close").click();
		$.ajax({
			url: "/trade/sell",
			method: "POST",
			data: {'account_ids': account_ids},
			success: function(res) {
				//console.log(res);
				for(let i in res) {
					updateBalance(i, res[i].balance);
					$("#account_" + i + " .last-order-status").empty();
					
					for(let j in res[i].result) {
						if(res[i].result[j].clientOrderId != null || res[i].result[j].id != null) {
							if(res[i].result[j].clientOrderId != null)
								$("#account_" + i + " .last-order-status").append("<div>" +res[i].result[j].symbol+ ": Order success!  OrderId: "+  res[i].result[j].orderId +". Status: " + res[i].result[j].status + "</div>");
							else
								$("#account_" + i + " .last-order-status").append("<div>" +res[i].result[j].symbol+ ": Order success!  OrderId: "+  res[i].result[j].id +". Exchange: " + res[i].result[j].exchange + "</div>");
						}
						else{
							if( res[i].result[j].msg != null)
								$("#account_" + i + " .last-order-status").append("<p class='error'>" +res[i].result[j].symbol+ ": Error! "+res[i].result[j].msg+". Status: " + res[i].result[j].status + "</p>");
							else
								$("#account_" + i + " .last-order-status").append("<p class='error'>" +res[i].result[j].symbol+ ": Error! "+res[i].result[j].message);
						}
					}
						
					setTimeout(function(){
						 reLoadBalance(i);
					}, 2000);
				}
				
				$(".nb-spinner").hide();
				$(".buttons").show();
				deselectAll();
				updateStatuses();
			
			}
		})
	})
//	setTimeout(reLoadBalance(9),2000)
	function reLoadBalance(account_id) {
		$.ajax({
			url: "/account/reload-balance?id="+account_id,
			success: function(res) {
				updateBalance(account_id, res);
			}
		})
	}
	
function updateBalance(account_id, balances) {
		html_string = "";
		for(let i in balances) {
			b = balances[i]
			currency_id = b.currency;
			in_orders = '';
			borrowed = '';
			amount = '';

			if(b.locked !=0 && b.locked!=null)
                in_orders = ' (+' + format_number(b.locked, currency_id) + ')';
			
			if(b.borrowed*b.rate>0.1)
				borrowed = ' <span class="borrowed">-' + format_number(b.borrowed, currency_id) + '</span>';
							
			if(b.amount!=0 && b.amount!=null)
				amount = '/ ' + format_number(b.amount,currency_id);
						
			if(b.in_position == 1) 
				html_string+= '<div>' + b.symbol + ': ' + format_number(b.amount, currency_id) + in_orders + borrowed + '</div>';
			else			
				html_string+= '<div>' + b.symbol + ': ' + format_number(b.free, currency_id) + " " + in_orders + amount + borrowed + '</div>';
		}
		$("#account_"+account_id+" .account_balance").html(html_string);
	}
	
	function deselectAll() {
		$(".account_container.selected .deselect>button").each(function(){
			$(this).click();
		})
	}
	
	function updateStatuses() {
		$.ajax({
			url: "/account/info-json",
			type: "json",
			success: function(res) {
				for(i in res) {
					account = res[i];
				//	console.log(account.status);
					$("#account_"+account.id).data("status", account.status);
					icon_status = $("#account_"+account.id).find(".status_icon").data("status")
					$("#account_"+account.id).find(".status_icon").removeClass("status_"+icon_status);
					$("#account_"+account.id).find(".status_icon").addClass("status_"+account.status);
					$("#account_"+account.id).find(".status_icon").data("status", account.status)
					$("#account_"+account.id).find(".status_string").html(status_names[account.status])
				}
			}
		})
	}
	
		
	
	
	function format_number(number, currency_id) {
		val = 1;
		
		decimals = currency_decimals[currency_id];
		number=number*1;
/*
		for(i=0; i< decimals; i++)
			val*=10;
		*/
		//return Math.round(number*val)/val;
		return number.toFixed(decimals);   
	}
	

})
</script>