<?php
use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('/img/logo.jpg', ['alt' => Yii::$app->name, 'height'=>'40px']), //, height: 40px;
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-light fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Calculator', 'url' => ['/calculations/create']],
        
        
        //['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        
        $menuItems[] =         [
            'label' => strtoupper(substr(Yii::$app->user->identity->firstname, 0, 1) .substr(Yii::$app->user->identity->lastname, 0, 1)),
            'items' => [
                 ['label' => 'Logout', 'url' => '/site/logout'],
                 //['label' => 'Level 1 - Dropdown B', 'url' => '#'],
            ],
        ];
        
       /* 
        
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
                'Logout (' . strtoupper(substr(Yii::$app->user->identity->firstname, 0, 1) .substr(Yii::$app->user->identity->lastname, 0, 1)).')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
            */
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ml-auto'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <div class="top"></div>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">Copyright &copy; <?= date('Y') ?> All Rights Reserved </p>
        <?php // Html::encode(Yii::$app->name) ?> <?php // date('Y') ?>

        <?php /* <p class="float-right"><?= Yii::powered() ?></p> */ ?>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
