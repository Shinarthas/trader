<div style="position:relative;" >

    <h1><i class="fa fa-users"></i>Players</h1>
<div class="row">
    <div class="col-md-12">
        <span style="overflow: hidden; line-height: 45px; position: relative; display: inline-block"><span class="status_icon status_0" data-status="0" style="display: block; float: left"></span>неактивен - 1</span>
        <span style="overflow: hidden; line-height: 45px; position: relative; display: inline-block"><span class="status_icon status_1" data-status="1" style="display: block; float: left"></span>доступен - 1</span>
        <span style="overflow: hidden; line-height: 45px; position: relative; display: inline-block"><span class="status_icon status_5" data-status="5" style="display: block; float: left"></span>отключен - 1</span>
        <span style="overflow: hidden; line-height: 45px; position: relative; display: inline-block; color:white">скрыт - 1</span>
        <span style="overflow: hidden; line-height: 45px; position: relative; display: inline-block"><span class="status_icon status_4" data-status="4" style="display: block; float: left"></span>в позиции - 1</span>


    </div>
</div>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
    <div class="col-md-2"></div>
</div>
<a style="position:absolute;right:0;top:12px;" href="/account/add"><button class="btn btn-primary btn-md" data-id="3" style="margin: 6px;">Добавить аккаунт</button></a>



</div>
<div class="row">
    <div style="padding-top:0.5px;" class="list">
        <table class="table table-striped">
            <thead>
            <tr>
                <td>Игрок</td>
                <td>статус</td>
                <td>Биржа</td>
                <td>Группа</td>
                <td>Депозит</td>
                <td>Активен</td>
                <td>Владелец</td>
                <td></td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            <? foreach($accounts as $a): ?>
                <?=$this->render("_account", ['account'=>$a]);?>
            <? endforeach; ?>
            </tbody>
        </table>


    </div>

</div>
<!-- Modal -->
<div id="strategies" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Strategies</h4>
            </div>
            <div class="modal-body">

            </div>
        </div>

    </div>
</div>

<script>
    function editStrategies(account_id) {
        $.ajax({
            url: "/account/"+account_id+"/strategies",
            success: function($resp){
                $('#strategies').modal('show');
                $('#strategies .modal-body').html($resp);
            }
        });

    }
</script>