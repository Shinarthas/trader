<?php
use yii\bootstrap\ActiveForm;
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 01.06.2020
 * Time: 13:14
 */

$symbols=[
    'BTCUSDT'=>'BTCUSDT',
    'ETHBTC'=>'ETHBTC',
    'BNBBTC'=>'BNBBTC',
    'ETHUSDT'=>'ETHUSDT',
];

?>
<?php $form = ActiveForm::begin(); ?>
<div style="display: none">
    <?= $form->field($account, 'id')->hiddenInput(); ?>
</div>
<?= $form->field($account, 'sum_st')->dropDownList(\common\models\Account::SUM_ST); ?>
<?= $form->field($account, 'sum_fix') ?>
<?= $form->field($account, 'percent_sum') ?>
<?= $form->field($account, 'shoulder') ?>
<?= $form->field($account, 'shoulder_add') ?>
<?= $form->field($account, 'order_open_type')->dropDownList(\common\models\Account::ORDER_TYPE);  ?>
<?= $form->field($account, 'limited_order_percent_open') ?>
<?= $form->field($account, 'order_open_threshold_open') ?>
<?= $form->field($account, 'order_timeout_open') ?>
<?= $form->field($account, 'shoulder_down') ?>
<?= $form->field($account, 'order_close_type')->dropDownList(\common\models\Account::ORDER_TYPE);  ?>
<?= $form->field($account, 'limited_order_percent_close') ?>
<?= $form->field($account, 'order_open_threshold_close') ?>
<?= $form->field($account, 'order_timeout_close') ?>
<?= $form->field($account, 'pair')->dropDownList($symbols); ?>
<?= $form->field($account, 'is_downgrade')->dropDownList(['0'=>"Нет",'1'=>"Да"]); ?>
<?= $form->field($account, 'take_profit'); ?>
<?= $form->field($account, 'stop_loss'); ?>
<button type="submit" class="btn btn-primary btn-md" name="save">Сохранить</button>
<?php ActiveForm::end(); ?>