<?php
use yii\bootstrap\ActiveForm;
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 01.06.2020
 * Time: 13:14
 */

?>

<div class="row">
    <?php foreach ($strategies as $s){ ?>
        <div class="col-md-3">
            <?=$this->render('/strategy/_strategy',['strategy'=>$s])?>
            <button type="button" onclick="selectStrategy(<?=$account->id?>,<?=$s->id?>)">select</button>
        </div>

    <?php } ?>
</div>
