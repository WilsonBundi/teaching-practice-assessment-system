<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Learning Areas';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="learning-area-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Learning Area', ['/learning-area/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'learning_area_id',
            'learning_area_name',

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/learning-area/' . $action, 'id' => $model->learning_area_id];
                },
            ],
        ],
    ]); ?>
</div>