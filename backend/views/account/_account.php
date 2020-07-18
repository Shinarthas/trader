<tr>
    <td><?=$account->name;?> </td>
    <td><span style="overflow: hidden; line-height: 45px; position: relative; display: inline-block"><span class="status_icon status_1" data-status="1" style="display: block; float: left"></span></span>
    </td>
    <td><?=\common\models\Account::MARKETS[$account->market]?></td>
    <td><?=\common\models\Account::GROUPS[$account->group_id]?></td>
    <td><?=$account->deposit;?></td>
    <td><span ><?=$account->status_string;?></span></td>
    <td>admin</td>
    <td class="text-center"><a href="/account/<?=$account->id;?>/edit">disable</a></td>
    <td class="text-center"><a href="/account/<?=$account->id;?>/strategies">strategies</a><a href="/account/<?=$account->id;?>/stat" style="float:right;margin-left:20px;"><i class="fa fa-line-chart"></i></a></td>
    <!--<td class="text-center"><a href="/account/<?=$account->id;?>/stat">strategies</a></td>-->
</tr>

