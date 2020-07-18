<style>
table tr th,table tr td {
	
	padding:1px 4px;
	border-bottom:1px solid #eee;
}
table tr td{
	font-size:14px;
	line-height:50px;
	
}
.plus {
	color:#0da50d;
}
.minus {
	color:red;
}
</style>

<div style="float:left;">

<h2>In process</h2>

<table style="width:700px; margin-top:25px;">
	<thead>
	<tr>
		<th>Accont</th><th>Currency</th><th>Status</th><th>Price</th><th>Progress</th><th>Action</th>
	</tr>
	</thead>
	<tr>
		<td>Test Account 10</td><td style="font-size: 16px;">BTC</td><td style="color:#a3bdd8;">Purchasing</td><td  style="font-size: 15px;">8700</td><td style="font-size: 16px;">14.44%</td><td><button class="btn btn-danger btn-md" data-id="3" >Cancel</button></td>
	</tr>
</table>


<h2 style="margin-top:50px;">In position</h2>



<table style="width:700px; margin-top:25px;">
	<thead>
	<tr>
		<th>Accont</th><th>Currency</th><th>USDT value</th><th>Start at</th><th>Profit</th><th>Action</th>
	</tr>
	</thead>
	<tr>
		<td>Test Account 2</td><td style="font-size: 16px;">BTC</td><td style="font-size: 15px;">252.55</td><td >20 May 16:40</td><td class="plus" style="font-size: 16px;font-weight:bold;">+2.24%</td><td><button class="btn btn-danger btn-md" data-id="3" >Stop</button></td>
	</tr>
	<tr>
		<td>Test Account 3</td><td style="font-size: 16px;">BTC</td><td style="font-size: 15px;">311.02</td><td >20 May 16:40</td><td class="plus" style="font-size: 16px;font-weight:bold;">+2.24%</td><td><button class="btn btn-danger btn-md" data-id="3" >Stop</button></td>
	</tr>
	<tr>
		<td>Test Account 4</td><td style="font-size: 16px;">BTC</td><td style="font-size: 15px;">1677.14</td><td >20 May 16:40</td><td class="plus" style="font-size: 16px;font-weight:bold;">+2.24%</td><td><button class="btn btn-danger btn-md" data-id="3" >Stop</button></td>
	</tr>
	<tr>
		<td>Test Account 5</td><td style="font-size: 16px;">ETH</td><td style="font-size: 15px;">1244.99</td><td >25 May 20:03</td><td class="minus" style="font-size: 16px;font-weight:bold;">-1.05%</td><td><button class="btn btn-danger btn-md" data-id="3" >Stop</button></td>
	</tr>
	<tr>
		<td>Test Account 6</td><td style="font-size: 16px;">ETH</td><td style="font-size: 15px;">6321.11</td><td >25 May 20:03</td><td class="minus" style="font-size: 16px;font-weight:bold;">-1.05%</td><td><button class="btn btn-danger btn-md" data-id="3" >Stop</button></td>
	</tr>
</table>

</div>

<div style="float:left; padding:50px 0  0 70px ;width:400px;">
	<h2>Total info</h2>
	<table style="width:100%;">
		<tr>
			<td>Accounts in process</td><td style="font-size:18px;font-weight:bold;text-align:center;">1</td>
		</tr>
		<tr>
			<td>Accounts in position</td><td style="font-size:18px;font-weight:bold;text-align:center;">5</td>
		</tr>
		<tr>
			<td>Money in orders</td><td style="font-size:18px;font-weight:bold;text-align:center;">8992.44$</td>
		</tr>
		<tr>
			
			<td>Profit</td><td style="font-size:18px;font-weight:bold;text-align:center;">+345.$</td>
		</tr>
		
	</table>
	
	<button class="btn btn-danger btn-md" data-id="3" style="width:100%;margin-top:34px;" >Stop ALL</button>
</div>