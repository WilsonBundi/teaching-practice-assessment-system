<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Assessment Reports';
$this->params['breadcrumbs'][] = ['label' => 'TP Office Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="assessment-reports">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back to Dashboard', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'assessment_id',
            'student_reg_no',
            [
                'attribute' => 'school_id',
                'value' => function ($model) {
                    return $model->school->school_name ?? 'N/A';
                },
            ],
            'assessment_date',
            [
                'attribute' => 'examiner_user_id',
                'value' => function ($model) {
                    return $model->examinerUser->name ?? 'N/A';
                },
            ],
            'overall_level',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {download} {archive}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('View', ['view', 'id' => $model->assessment_id], ['class' => 'btn btn-sm btn-info']);
                    },
                    'download' => function ($url, $model, $key) {
                        return Html::a('Download', ['download-report', 'id' => $model->assessment_id], ['class' => 'btn btn-sm btn-success']);
                    },
                    'archive' => function ($url, $model, $key) {
                        return Html::a('Archive', $url, [
                            'class' => 'btn btn-sm btn-danger',
                            'data-confirm' => 'Are you sure you want to archive this assessment?',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>