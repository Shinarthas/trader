<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 12.06.2020
 * Time: 17:38
 */?>

<div class="row">
    <?php foreach ($strategies as $s){ ?>
        <div class="col-md-3">
            <?=$this->render('_strategy',['strategy'=>$s])?>
        </div>

    <?php } ?>
</div>
