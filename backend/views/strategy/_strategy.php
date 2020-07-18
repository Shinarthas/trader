<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 12.06.2020
 * Time: 17:39
 */
?>
<div class="strategy strategy_<?=$strategy->id?>">
    <?php  foreach (\yii\helpers\ArrayHelper::toArray($strategy)  as $key=>$value){ ?>
        <p><?=$key?>:<b><?=$value?></b></p>

    <?php  } ?>
    <a href="/strategy/<?=$strategy->id?>/edit">Edit</a>
</div>

