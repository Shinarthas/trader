<style>

tr td, tr th {
	padding:2px 4px;
}
.borrowed {
	color:red;
}
</style>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<script src="/js/dateFormat.js"></script>

<div style="float:right;width:300px;margin-right:120px;">
    <div class="balances">
        <?php
        foreach($main_balance as $balance) {
            $in_orders = '';
            if($balance['locked']!=0)
                $in_orders = ' (+'.number_format($balance['locked'],$currencies[$balance['currency']]->decimals).')';
            $borrowed = '';

            if($balance['borrowed']!=0)
                $borrowed = ' <span class="borrowed">-'. number_format($balance['borrowed'],$currencies[$balance['currency']]->decimals).'</span>';
            if(($balance['borrowed']+$balance['free']+$balance['locked'])*$balance['rate']>5)
                echo '<div>'.$balance['symbol'].': '.number_format($balance['free'],$currencies[$balance['currency']]->decimals).
                    ' '.$in_orders.
                    ' '.$borrowed.'</div>';
        }
        ?>
        <p>
            <a class="btn btn-default" data-toggle="collapse" href="#other" role="button" aria-expanded="false" aria-controls="other">
                Other
            </a>
        </p>
        <div class="collapse" id="other">
            <div class="card card-body">
                <?php
				//yodjin made this script
                foreach($main_balance as $balance) {
                    $in_orders = '';
                    if($balance['locked']!=0)
                        $in_orders = ' (+'.number_format($balance['locked'],$currencies[$balance['currency']]->decimals).')';
                    $borrowed = '';

                    if($balance['borrowed']!=0)
                        $borrowed = ' <span class="borrowed">-'. number_format($balance['borrowed'],$currencies[$balance['currency']]->decimals).'</span>';
                    if(($balance['borrowed']+$balance['free']+$balance['locked'])*$balance['rate']<5)
                        echo '<div>'.$balance['symbol'].': '.number_format($balance['free'],$currencies[$balance['currency']]->decimals).
                            ' '.$in_orders.
                            ' '.$borrowed.'</div>';
                }
                ?>
            </div>
        </div>
    </div>

					
 <div class="chart1" data-account="30" data-index="1" style="width:300px;">
 			
<h1>Balance change:</h1>				
				
                                <canvas id="chart-1-<?=$account['id']?>" height="300px"></canvas>
                            </div>
							</div>
<h1>Orders list:</h1>				
				
			<table>
                <thead>
                <tr>
                    <th>id</th>
                    <th>direction</th>
                    <th>date</th>
                    <th>Currency</th>
                    <th>tokens count</th>
                    <th>rate</th>
					  <th>status</th>
					  <th>action</th>
                </tr>
                </thead>
                <tbody>
				<?
					foreach($orders as $t):
				?>
				<tr>
					<td><?=$t->id;?></td>
                        <td><?=($t->sell==1)?'<b style="color:orange">sell</b>':'<b style="color:purple;">buy</b>';?></td>

                        <td><?=date("d/m/y H:i", $t->time);?></td>
                        <td><?=$currencies[$t->currency_one]->symbol;?></td>
                        <td><?=$t->tokens_count;?></td>
                       <td><?=$t->data['fills'][0]['price'];?></td>
					    <td class="order_status_<?=$t->id;?>"><?=$t::$statuses[$t->status];?></td>
					    <td>
                            <?php if($t->status==\common\models\Order::STATUS_CREATED || $t->status==\common\models\Order::STATUS_FAILED){ ?>
                                <div class="nb-spinner" style="    float: right; display:none; margin: 15px;"></div>
                                <a class="btn-sm btn btn-danger" onclick="cancelOrder(<?=$t->id;?>)">cancel</a>
                            <?php } ?>

                        </td>
					</tr>
				<? endforeach; ?>
                              


                                </tbody><tbody>
            </tbody></table>		

<script>
    var colorsC= {
        USDT: "#0eff1a",
        BTC: "#fff236",
        ETH: "#61655d",
        TRX: "#ff3847",
        BNB: "#da9aff",
        ZEC: "#ffc69e",
        XRP: "#5494ff",
        XMR: "#ff7a39",
    };

    var graph = <?=json_encode($graph);?>
	
	window.onload = function () {
        $( ".chart1" ).each(function( index ) {
            var account_id=$(this).attr('data-account');
            var inder_index=$(this).attr('data-index');
            var canvas=$(this).find('canvas').eq(0)[0];

            var balances=[];
            var  labels=[];

            for(var i in graph){

				  balances.push(graph[i].value)
				  labels.push(dateFormat('m/d H:i',new Date(graph[i].time)))
            }
            var colors=[];
            for(var k=0;k<labels.length;k++){
                console.log(labels[k],hashCode(labels[k]),intToRGB(hashCode(labels[k])))
                if(colorsC[labels[k]]==undefined)
                    colors.push('#'+intToRGB(hashCode(labels[k]+'asd')))
                else
                    colors.push(colorsC[labels[k]])
            }
            console.log(balances);
            var data = {
                labels: labels,
                datasets: [
                    {
                        label: "Weekly Balance change history",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 5,
                        pointHitRadius: 10,
                        data: balances,
                    }
                ]
            };
            var option = {
                showLines: true
            };
            var myLineChart = Chart.Line(canvas,{
                data:data,
                options:option
            });
        });
    }
    function hashCode(str) { // java String#hashCode
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }

        return hash;
    }

    function intToRGB(i){
        var c = (i & 0x00FFFFFF)
            .toString(16)
            .toUpperCase();

        return "00000".substring(0, 6 - c.length) + c;
    }
</script>