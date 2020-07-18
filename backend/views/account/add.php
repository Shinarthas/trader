<?
use yii\bootstrap\ActiveForm;

$groups = [
1 => 'Small',
2 => 'Big',
3 => 'Other'
]
?>


<h3 style="text-align:left;padding:15px ;font-size:28px;">
<? if($edit): ?>
Edit account:
<? else: ?>
New account: 
<? endif; ?>

</h3>


<div class="col-md-6">




<?php $form = ActiveForm::begin(); ?>


	<?= $form->field($account, 'name')->textInput(['autofocus' => true]) ?>
	<?= $form->field($account, 'deposit') ?>
    <?= $form->field($account, 'market')->dropDownList(\common\models\Account::MARKETS) ?>
	<?= $form->field($account, 'group_id')->dropDownList($groups) ?>

    <?php if($account->access_key==''){ ?>
	<?= $form->field($account, 'access_key') ?>
	<?= $form->field($account, 'secret_key') ?>
    <?php } ?>
    <hr>
    <?= $form->field($account, 'in_position') ?>
    <?= $form->field($account, 'currency') ?>

<div class="form-group">
<label class="control-label">Status:</label>
<?=$account->status_string;?>
</div>

<? if($error != ''):?>
	<p style="color:red;"><?=$error;?></p>
<? endif; ?>

<button type="submit" class="btn btn-primary btn-md" name="save"><? if($edit): ?>
Сохранить
<? else: ?>
Добавить и верифицировать
<? endif; ?></button>

<? if($edit): ?>
<button type="submit" class="btn btn-primary btn-md" name="verify">
Verify
</button>

<br><br>
    <div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Дополнительно
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-md" name="loan">
                        Loan 10usdt
                    </button>
                    <button type="submit" class="btn btn-primary btn-md" name="repay">
                        Repay 10usdt
                    </button>
                </div>
            </div>
        </div>

    </div>

<? endif; ?>
<?php ActiveForm::end(); ?>
	</div>

<br><br><br><br>