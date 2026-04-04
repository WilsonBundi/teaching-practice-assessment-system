<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Zones';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="zone-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Zone', ['/zone/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'zone_id',
            'zone_name',

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/zone/' . $action, 'id' => $model->zone_id];
                },
            ],
        ],
    ]); ?>
</div>