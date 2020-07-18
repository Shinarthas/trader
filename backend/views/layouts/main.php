<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
\backend\assets\MainAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php

    $noti_list=[];
    $count_unread=\common\models\Notification::find()->where(['is_viewed'=>0])->count();
    $notifications=\common\models\Notification::find()->orderBy('id desc')->limit(10)->all();
    foreach ($notifications as $noty){
        $noti_list[]=['label'=>$this->render("_notification", ['notification'=>$noty])];
    }


    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $profileItems=[['label'=>'profile','url'=>['/site/profile']]];
    $profileItems[] = '<li>'
        . Html::beginForm(['/site/logout'], 'post')
        . Html::submitButton(
            'Logout (' . Yii::$app->user->identity->username . ')',
            ['class' => 'btn btn-link logout']
        )
        . Html::endForm()
        . '</li>';
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
		$menuItems = [
            ['label' => 'Demo', 'url' => ['/trade']],
			['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'Trading', 'url' => ['/trade2/trade']],
			/*['label' => 'Trading v2', 'items' => [
					['label' => 'Make order', 'url' => ['/trade2']],
					['label' => 'In work', 'url' => ['/trade2/in-work']],
				]],*/
			['label' => 'Players', 'url' => ['/account']],
			['label' => 'Strategies', 'url' => ['/strategy']],
			['label' => 'Logs', 'url' => ['/log/index']],
			['label' => "<i style='font-size: 20px;margin-left:10px;' class=\"fa fa-bell\"></i> <span class=\"label label-danger pull-left\" style='margin-left:10px;'>$count_unread</span>", 'items' => $noti_list,],
            ['label' => '<img src="https://www.bsn.eu/wp-content/uploads/2016/12/user-icon-image-placeholder-300-grey.jpg" class="img-thumb user-placeholder"> Profile', 'items' => $profileItems],
        ];

    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer" style="display:none;">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right">Powered by <a href="https://lab3m.com/">Lab3M</a></p>
    </div>
</footer>
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
