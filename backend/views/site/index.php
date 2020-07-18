<?php

/* @var $this yii\web\View */

$this->title = 'Home page';
?>
<style>
h2 {
	font-size:28px;
	font-size:28px;
}
</style>

<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome!</h1>
        <h2>Margin Binance</h2>

        <p class="lead">Это домашняя страница торговой системы.</p>

        
		
    </div>

<div class="body-content">

        <div class="row">
            <div class="col-lg-3">
                <h2>Профит за 7 дней</h2>
				<p >Прибыль за неделю позволяет оценить успешность торговли.</p>
                <h1><?=number_format($profit,2)?>$</h1>


            </div>
            <div class="col-lg-3">
                <h2>Подключенных аккаунтов</h2>

                <p >Быстро узнать количество подключенных учетных записей клиентов - благодаря этой информации.</p>
 <h1><?=$accounts_count?></h1>

            </div>
            <div class="col-lg-3">
                <h2>Общая ценность, USDT</h2>

                <p >Общая стоимость всех учетных записей в системе.</p>
 <h1><?=number_format($net_worth,2)?>$</h1>

            </div>
			 <div class="col-lg-3">
                <h2>Ордеров за неделю</h2>

                <p >Насколько активно торгуют в системе.</p>

     <div class="row">
         <div class="col-xs-4">
             <h1 style="margin-top:9px "><?=intval($orders_week)?> </h1>
         </div>
         <div class="col-xs-8">
                 <span>
                   <div class="row"><h4 style="margin: 2px;margin-top:9px"><i class="fa fa-cog"></i><?=intval($orders_week*0.7)?></h4></div>
                    <div class="row"><h4 style="margin: 2px"><i class="fa fa-user"></i><?=intval($orders_week*0.3)?></h4></div>

                </span>
         </div>
     </div>




            </div>
        </div>

    </div>

</div>

