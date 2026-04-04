<?php
use yii\helpers\Html;

$this->title = 'System Reports';
$this->params['breadcrumbs'][] = ['label' => 'Department Chair Profile', 'url' => ['/department-chair/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="system-reports">
    <div class="container-fluid">
        <h1><?= Html::encode($this->title) ?></h1>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['totalAssessments'] ?></h3>
                        <p>Total Assessments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['completedAssessments'] ?></h3>
                        <p>Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['pendingAssessments'] ?></h3>
                        <p>In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['totalSupervisors'] ?></h3>
                        <p>Supervisors</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Grade Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Grade Level</th>
                                        <th>Count</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $levels = ['EE' => 'Exceeds Expectations', 'ME' => 'Meets Expectations', 'AE' => 'Approaching Expectations', 'BE' => 'Below Expectations'];
                                    $distribution = [];
                                    foreach ($gradeDistribution as $item) {
                                        $distribution[$item->level] = $item->count;
                                    }
                                    ?>
                                    <?php foreach ($levels as $level => $description): ?>
                                        <tr>
                                            <td><span class="badge badge-primary"><?= $level ?></span></td>
                                            <td><?= $distribution[$level] ?? 0 ?></td>
                                            <td><?= $description ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">System Summary</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Schools</span>
                                <strong><?= $stats['totalSchools'] ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Zone Coordinators</span>
                                <strong><?= $stats['totalZoneCoordinators'] ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>TP Office Users</span>
                                <strong><?= $stats['totalTpOffice'] ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Grades Entered</span>
                                <strong><?= $stats['totalGrades'] ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    <?= Html::a('<i class="fas fa-list"></i> Monitor Assessments', ['/department-chair/monitor-assessments'], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Profile', ['/department-chair/profile'], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
