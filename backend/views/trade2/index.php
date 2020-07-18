<style>
	label {
		font-weight:normal;
	}
	.form-control {
		padding:5px 10px;
		height:30px;
	}
	.form-group {
		margin-bottom:12px;
	}
</style>

<img src="/images/trade2.png" style="float:left;    margin-top: 30px;">

<div style="width:300px;float:left; padding:10px;">

	<div class="form-group field-account-group_id required">
	<h3>Currency</h3>
	<select id="account-group_id" class="form-control" name="Account[group_id]" aria-required="true" aria-invalid="false">
	<option value="1" selected="">BTC</option>
	<option value="2">ETH</option>
	<option value="3">XRP</option>
	<option value="3">BCH</option>
	<option value="3">BSV</option>
	<option value="3">LTC</option>
	<option value="3">BNB </option>
	<option value="3">EOS </option>
	<option value="3">XTZ </option>
	</select>
	</div>
	<hr>
		<h3 onclick='$("#strategy_block").toggle();' style="cursor:pointer;">Strategy</h3>
		<div id="strategy_block">
		<div class="form-group field-account-group_id required">
			<label class="control-label" for="account-group_id">Entry</label>
			<select id="account-group_id" class="form-control" name="Account[group_id]" aria-required="true" aria-invalid="false">
				<option value="1">Market Order</option>
				<option value="2" selected="">Limit Order</option>
				<option value="3">Stop-Limit Order</option>
			</select>
		</div>
		<div class="form-group field-account-access_key required">
			<label class="control-label" for="account-access_key">Buy price</label>
			<input type="text" id="account-access_key" class="form-control" name="Account[access_key]" aria-required="true" value="8700">

			<p class="help-block help-block-error"></p>
		</div>
		<div class="form-group field-account-access_key required">
			<label class="control-label" for="account-access_key">Shoulder</label>
			<input type="text" id="account-access_key" class="form-control" name="Account[access_key]" aria-required="true" value="1.3">

			<p class="help-block help-block-error"></p>
		</div>
		<div class="form-group field-account-access_key required">
			<label class="control-label" for="account-access_key">Stop loss <input type="checkbox"></label>
			<input type="text" id="account-access_key" class="form-control" name="Account[access_key]" aria-required="true" value="8">

			<p class="help-block help-block-error"></p>
		</div>
		<div class="form-group field-account-access_key required">
			<label class="control-label" for="account-access_key">Target <input type="checkbox"></label>
			<input type="text" id="account-access_key" class="form-control" name="Account[access_key]" aria-required="true" value="20">

			<p class="help-block help-block-error"></p>
		</div>
		
		
		</div>
		
		
		<hr>
		<h3 onclick='$("#accounts_block").toggle();' style="cursor:pointer;">Accounts</h3>
		<div id="accounts_block">
		<table style="width:100%;">
			<tr>
				<td>name</td><td>balance, $</td><td></td>
			</tr>
			<tr>
				<td>account 1</td><td>52.22</td><td><input type="checkbox"></td>
			</tr>
			<tr>
				<td>account BTC</td><td>12777</td><td><input type="checkbox"></td>
			</tr>
			<tr>
				<td>account Danil</td><td>744</td><td><input type="checkbox"></td>
			</tr>
			<tr>
				<td>account new</td><td>20343</td><td><input type="checkbox"></td>
			</tr>
			<tr>
				<td>account 2020</td><td>111.3</td><td><input type="checkbox"></td>
			</tr>
				
		</table>
		</div>
		<button class="btn btn-primary btn-md" data-id="3" style="width:100%;margin-top:20px;">GO</button>
</div>