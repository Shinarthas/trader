<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 29.05.2020
 * Time: 13:13
 */
?>

<div class="row">
    <table class="table table-stripped" >
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Body</th>
            <th scope="col">Message</th>
            <th scope="col">Type</th>
            <th scope="col">Date Added</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($logs as $log){?>
            <tr>
                <td ><?=$log->id?></td>
                <td class="break">
                    <div class="log-wrapper">
                        <p>
                        <span class="except" type="button" data-toggle="collapse" data-target="#collapseExample-<?=$log->id?>" aria-expanded="false" aria-controls="collapseExample">
                            <?=substr(json_encode($log->info),0,100)?>
                        </span>
                        </p>
                        <div class="collapse" id="collapseExample-<?=$log->id?>">
                            <div class="card card-body">
                                <pre><?php print_r($log->info)?></pre>
                            </div>
                        </div>
                    </div>
                    <span></span>

                </td>
                <td><?= $log->message?></td>
                <td><?= $log->type?></td>
                <td><?= $log->created_at?></td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>
<style>
    .log-wrapper{
        max-width: 30vw;
    }

    td.break{
        word-break:break-all;
    }
</style>