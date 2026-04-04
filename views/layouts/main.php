<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$this->registerCss('
    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    
    #header .navbar {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        letter-spacing: 0.3px;
    }
    
    #header .nav-link {
        transition: color 0.3s ease, border-bottom 0.3s ease;
        padding: 0.75rem 1rem !important;
        position: relative;
    }
    
    #header .nav-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: white;
        transition: width 0.3s ease, left 0.3s ease;
    }
    
    #header .nav-link:hover::after {
        width: 100%;
        left: 0;
    }
    
    #main {
        padding-top: 90px;
    }
    
    @media (max-width: 576px) {
        #main {
            padding-top: 75px;
        }
    }
');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top', 'style' => 'background: linear-gradient(90deg, #5B9BD5 0%, #2E75B6 100%) !important;']
    ]);
    $navItems = [];
    
    if (!Yii::$app->user->isGuest) {
        // Supervisor Menu (role_id = 1)
        if (Yii::$app->user->identity->role_id == 1) {
            $navItems[] = [
                'label' => 'Assessments',
                'items' => [
                    ['label' => 'Create Assessment', 'url' => ['/assessment/create']],
                    ['label' => 'My Assessments', 'url' => ['/users/profile']],
                    ['label' => 'Assessment Search', 'url' => ['/assessment/index']],
                ],
            ];
        }
        
        // TP Office Menu (role_id = 3)
        if (Yii::$app->user->identity->role_id == 3) {
            $navItems[] = [
                'label' => 'TP Office',
                'items' => [
                    ['label' => 'Dashboard', 'url' => ['/tp-office/index']],
                    ['label' => 'Reports', 'url' => ['/tp-office/reports']],
                    ['label' => 'Master Data', 'items' => [
                        ['label' => 'Schools', 'url' => ['/tp-office/schools']],
                        ['label' => 'Zones', 'url' => ['/tp-office/zones']],
                        ['label' => 'Grades', 'url' => ['/tp-office/grades']],
                        ['label' => 'Learning Areas', 'url' => ['/tp-office/learning-areas']],
                        ['label' => 'Strands', 'url' => ['/tp-office/strands']],
                        ['label' => 'Sub-Strands', 'url' => ['/tp-office/substrands']],
                    ]],
                ],
            ];
        }
    }
    
    // Add login/logout
    $navItems[] = Yii::$app->user->isGuest
        ? ['label' => 'Login', 'url' => ['/site/index']]
        : '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'nav-link btn btn-link logout', 'style' => 'text-decoration: none;']
            )
            . Html::endForm()
            . '</li>';
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'encodeLabels' => false,
        'items' => $navItems
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-dark text-light border-top border-secondary">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> TP Assessment System. All rights reserved.</small>
    </div>
</footer>

<?php 
// Include AI Chatbot Widget for logged-in users
if (!Yii::$app->user->isGuest) {
    echo $this->render('@app/views/layouts/chat-widget');
}
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
