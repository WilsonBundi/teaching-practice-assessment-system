<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Schools';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="school-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create School', ['/school/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'school_id',
            'school_name',
            'school_address',
            [
                'attribute' => 'zone_id',
                'value' => function ($model) {
                    return $model->zone->zone_name ?? 'N/A';
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/school/' . $action, 'id' => $model->school_id];
                },
            ],
        ],
    ]); ?>
</div>