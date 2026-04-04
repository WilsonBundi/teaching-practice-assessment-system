<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $totalAssessments int */
/* @var $recentAssessments array */
/* @var $schoolsCount int */
/* @var $zonesCount int */
/* @var $gradesCount int */

$this->title = 'TP Office Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tp-office-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Assessments</h5>
                    <h2><?= $totalAssessments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Schools</h5>
                    <h2><?= $schoolsCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Zones</h5>
                    <h2><?= $zonesCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Grades</h5>
                    <h2><?= $gradesCount ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Quick Actions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Assessment Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        View, download, and archive assessment records.
                    </p>
                    <?= Html::a('View Reports', ['reports'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <!-- Master Data Management -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Master Data Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Manage schools, zones, grades, learning areas, strands, and sub-strands.
                    </p>
                    <div class="btn-group-vertical">
                        <?= Html::a('Schools', ['schools'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Zones', ['zones'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Grades', ['grades'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Learning Areas', ['learning-areas'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Strands', ['strands'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Sub-Strands', ['substrands'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assessments -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Assessments</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>School</th>
                                <th>Date</th>
                                <th>Examiner</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAssessments as $assessment): ?>
                            <tr>
                                <td><?= Html::encode($assessment->student_reg_no) ?></td>
                                <td><?= Html::encode($assessment->school->school_name ?? 'N/A') ?></td>
                                <td><?= Html::encode($assessment->assessment_date) ?></td>
                                <td><?= Html::encode($assessment->examinerUser->name ?? 'N/A') ?></td>
                                <td>
                                    <?= Html::a('View', ['view', 'id' => $assessment->assessment_id], ['class' => 'btn btn-sm btn-info']) ?>
                                    <?= Html::a('Download', ['download-report', 'id' => $assessment->assessment_id], ['class' => 'btn btn-sm btn-success']) ?>
                                    <?= Html::a('Archive', ['archive', 'id' => $assessment->assessment_id], ['class' => 'btn btn-sm btn-danger', 'data-confirm' => 'Are you sure you want to archive this assessment?']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>