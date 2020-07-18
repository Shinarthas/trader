<div onclick="mark_as_read(<?=$notification->id?>)">

    <i style="color: <?=$notification->is_viewed?'grey':'green'?>" class="fa fa-circle"></i>
    <?= $notification->body ?>
</div>
