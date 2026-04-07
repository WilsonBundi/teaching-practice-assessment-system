<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $totalAssessments int */
/* @var $completedAssessments int */
/* @var $recentAssessments array */
/* @var $schoolsCount int */
/* @var $zonesCount int */
/* @var $gradesCount int */

$this->title = 'TP Office Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Register real-time update script
$this->registerJs("
    var lastUpdate = Date.now();
    var updateInterval = 30000; // 30 seconds

    function updateDashboard() {
        $.ajax({
            url: '" . \yii\helpers\Url::to(['tp-office/get-dashboard-data']) . "',
            type: 'GET',
            data: { last_update: lastUpdate },
            success: function(data) {
                if (data.updated) {
                    // Update assessment count silently
                    var currentCount = parseInt($('.bg-primary h2').text());
                    var newCount = data.totalAssessments;
                    if (currentCount !== newCount) {
                        $('.bg-primary h2').text(newCount);
                    }

                    // Update completed assessments count silently
                    var currentCompletedCount = parseInt($('.bg-secondary h2').text());
                    var newCompletedCount = data.completedAssessments;
                    if (currentCompletedCount !== newCompletedCount) {
                        $('.bg-secondary h2').text(newCompletedCount);
                    }

                    // Update recent assessments table silently
                    if (data.recentAssessmentsHtml) {
                        $('.table-responsive').html(data.recentAssessmentsHtml);
                    }

                    lastUpdate = Date.now();
                }
            },
            error: function() {
                console.log('Failed to update dashboard');
            }
        });
    }

    // Start polling
    setInterval(updateDashboard, updateInterval);

    // Initial update after 5 seconds
    setTimeout(updateDashboard, 5000);
", \yii\web\View::POS_READY);
?>

<style>
    .card.updated {
        /* Removed visual animation */
    }

    @keyframes pulse {
        /* Removed pulse animation */
    }

    .real-time-indicator {
        /* Removed real-time indicator styles */
    }

    .real-time-indicator.show {
        /* Removed show animation */
    }

    @keyframes slideIn {
        /* Removed slide animation */
    }
</style>

<div class="tp-office-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Assessments</h5>
                    <h2><?= $totalAssessments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed Assessments</h5>
                    <h2><?= $completedAssessments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Schools</h5>
                    <h2><?= $schoolsCount ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Zones</h5>
                    <h2><?= $zonesCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
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
                    <?= Html::a('<i class="fas fa-cogs"></i> Master Data', ['/tp-office/master-data'], ['class' => 'btn btn-primary']) ?>
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
                    <div class="table-responsive">
                        <?= $this->render('_recent_assessments_table', ['recentAssessments' => $recentAssessments]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>