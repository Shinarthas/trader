

function orderInfo(account_id) {
    $.ajax({
        url: "/order/list",
        data: {account_id:account_id},
        success: function(result){
            $("#div1").html(result);
        }
    });
}

var notys=[];
setInterval(notifications, 5000);
function notifications() {
    if(window.location.pathname=='/site/login'){
        console.log('site/login');
        return;
    }
    console.log('request noty')
    $.ajax({
        url: "/notification",
        cache: false,
        success: function(json){
            for(var i=0;i<json.length;i++){
                var is_continue=0
                //проверим показано ли это уведовление
                for (var j=0;j<notys.length;j++){
                    if(notys[j].options.notification_id==json[i]['id']){
                        is_continue=1;
                        break
                    }
                }
                if(is_continue)
                    continue;
                var n=new Noty({
                    notification_id:json[i]['id'],
                    text: json[i]['body'],
                }).on('onClose',noty_click).show();
                notys.push(n);
                console.log(n);
            }
        }
    });
}
function noty_click() {
    $.ajax({
        url: "/notification/mark_as_read",
        data:{id:this.options.notification_id},
        cache: false,
        success: function(json){
            console.log(json)
        }
    });
}

function cancelOrder(id) {

    $(this.event.target).css('display','none');
    $(this.event.target).parent().find('.nb-spinner').css('display','block');
    var element=this.event.target;
    $.ajax({
        url: "/order/cancel",
        data:{id:id},
        cache: false,
        success: function(json){
            new Noty({
                text: json.status,
            }).show();
            console.log('.order_status_'+id);
            if(json.status=='CANCELED'){
                $(element).parent().find('.nb-spinner').css('display','none');
                $('.order_status_'+id).text('canceled by system')
            }else{
                $(element).parent().find('.nb-spinner').css('display','none');
                $('.order_status_'+id).text('error')
            }
        }
    });
}

function selectStrategy(account_id,strategy_id) {
    $('.strategy').removeClass('active-strategy');
    $.ajax({
        url: "/account/"+account_id+"/strategies",
        type:"POST",
        data:{'strategy_id':strategy_id},
        cache: false,
        success: function(json){
            new Noty({
                text: json.status,
            }).show();
            $('.strategy_'+strategy_id).addClass('active-strategy')
        }
    });

}
notifications();