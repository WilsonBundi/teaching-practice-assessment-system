<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Strands';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="strand-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Strand', ['/strand/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'strand_id',
            'strand_name',
            [
                'attribute' => 'learning_area_id',
                'value' => function ($model) {
                    return $model->learningArea->learning_area_name ?? 'N/A';
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return ['/strand/' . $action, 'id' => $model->strand_id];
                },
            ],
        ],
    ]); ?>
</div>