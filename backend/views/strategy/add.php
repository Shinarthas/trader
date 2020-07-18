<?php
use yii\widgets\ActiveForm;

$s=\common\models\Currency::find()
    ->select('id, symbol')
    ->orderBy('sort_order desc')
    ->asArray()->limit(10)->all();
$symbols=[];
foreach ($s as $ss){
    $symbols[$ss['id']]=$ss['symbol'];
}
?>

<?php $form = ActiveForm::begin(); ?>
<div style="display: none">
    <?= $form->field($strategy, 'id')->hiddenInput(); ?>
</div>
<?= $form->field($strategy, 'name'); ?>
<?= $form->field($strategy, 'sum_st')->dropDownList(\common\models\Strategy::SUM_ST); ?>
<?= $form->field($strategy, 'sum_fix') ?>
<?= $form->field($strategy, 'percent_sum') ?>
<?= $form->field($strategy, 'shoulder') ?>
<?= $form->field($strategy, 'shoulder_add') ?>
<?= $form->field($strategy, 'order_open_type')->dropDownList(\common\models\Strategy::ORDER_TYPE);  ?>
<?= $form->field($strategy, 'limited_order_percent_open') ?>
<?= $form->field($strategy, 'order_open_threshold_open') ?>
<?= $form->field($strategy, 'order_timeout_open') ?>
<?= $form->field($strategy, 'shoulder_down') ?>
<?= $form->field($strategy, 'order_close_type')->dropDownList(\common\models\Strategy::ORDER_TYPE);  ?>
<?= $form->field($strategy, 'limited_order_percent_close') ?>
<?= $form->field($strategy, 'order_open_threshold_close') ?>
<?= $form->field($strategy, 'order_timeout_close') ?>
<?= $form->field($strategy, 'pair')->dropDownList($symbols); ?>
<?= $form->field($strategy, 'is_downgrade')->dropDownList(['0'=>"Нет",'1'=>"Да"]); ?>
<?= $form->field($strategy, 'take_profit'); ?>
<?= $form->field($strategy, 'stop_loss'); ?>
<button type="submit" class="btn btn-primary btn-md" name="save">Сохранить</button>
<?php ActiveForm::end(); ?>
