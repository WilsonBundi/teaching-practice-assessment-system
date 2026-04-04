<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Supervisor Profile';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/site/dashboard']];
$this->params['breadcrumbs'][] = 'Supervisor Profile';
?>

<div class="supervisor-profile">
    <div class="container-fluid mt-4">
        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-primary shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-user-tie text-primary"></i> 
                                    <?= Html::encode($supervisor ? $supervisor->name : 'Unknown') ?>
                                </h2>
                                <p class="text-muted mb-1">
                                    <strong>Role:</strong> <?= Html::encode($role && is_object($role) ? $role->role_name : 'N/A') ?>
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-success">
                                        <?= Html::encode($supervisor && is_object($supervisor) ? $supervisor->status : 'Unknown') ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?= Html::a(
                                    '<i class="fas fa-plus-circle"></i> Create Assessment',
                                    ['/supervisor/select-student'],
                                    ['class' => 'btn btn-success me-2']
                                ) ?>
                                <?= Html::a(
                                    '<i class="fas fa-edit"></i> Edit Profile',
                                    ['edit'],
                                    ['class' => 'btn btn-primary']
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-address-card"></i> Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Payroll No:</strong></td>
                                    <td><?= Html::encode($supervisor && is_object($supervisor) ? $supervisor->payroll_no ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?= Html::encode($supervisor && is_object($supervisor) ? $supervisor->username : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?= Html::encode($supervisor && is_object($supervisor) ? $supervisor->phone ?? 'N/A' : 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User ID:</strong></td>
                                    <td><?= $supervisor && is_object($supervisor) ? $supervisor->user_id : 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Assessment Statistics -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Assessment Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-primary mb-0"><?= $totalAssessments ?></h3>
                                    <p class="text-muted mb-0">Total Assessments</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-success mb-0"><?= $completedAssessments ?></h3>
                                    <p class="text-muted mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-warning mb-0"><?= $pendingAssessments ?></h3>
                                    <p class="text-muted mb-0">Pending</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-info mb-0"><?= $uniqueStudents ?></h3>
                                    <p class="text-muted mb-0">Students Assessed</p>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-secondary mb-0"><?= $totalGrades ?></h3>
                                    <p class="text-muted mb-0">Grades Recorded</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-box">
                                    <h3 class="text-primary mb-0"><?= $learningAreas ?></h3>
                                    <p class="text-muted mb-0">Learning Areas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coverage -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Coverage</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-0"><?= $schoolCount ?></h4>
                                    <p class="text-muted mb-0">Schools</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assessments -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Recent Assessments</h5>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <?php $form = \yii\widgets\ActiveForm::begin([
                                    'method' => 'get',
                                    'action' => ['/supervisor/profile'],
                                    'options' => ['class' => 'form-inline']
                                ]); ?>
                                
                                <div class="form-group me-2 mb-2">
                                    <?= $form->field($searchModel, 'student_reg_no', [
                                        'template' => '{input}',
                                        'options' => ['class' => '']
                                    ])->textInput(['placeholder' => 'Student Reg No', 'class' => 'form-control form-control-sm']) ?>
                                </div>

                                <div class="form-group me-2 mb-2">
                                    <?= $form->field($searchModel, 'overall_level', [
                                        'template' => '{input}',
                                        'options' => ['class' => '']
                                    ])->dropDownList([
                                        '' => 'All Levels',
                                        'BE' => 'BE (Beginning)',
                                        'AE' => 'AE (Approaching)',
                                        'ME' => 'ME (Meets)',
                                        'EE' => 'EE (Exceeds)'
                                    ], ['class' => 'form-control form-control-sm']) ?>
                                </div>

                                <div class="form-group me-2 mb-2">
                                    <?= $form->field($searchModel, 'assessment_date', [
                                        'template' => '{input}',
                                        'options' => ['class' => '']
                                    ])->textInput(['type' => 'date', 'class' => 'form-control form-control-sm']) ?>
                                </div>

                                <div class="form-group mb-2">
                                    <?= \yii\helpers\Html::submitButton('<i class="fas fa-search"></i> Search', ['class' => 'btn btn-primary btn-sm']) ?>
                                    <?= \yii\helpers\Html::a('<i class="fas fa-times"></i> Clear', ['/supervisor/profile'], ['class' => 'btn btn-secondary btn-sm ms-1']) ?>
                                </div>

                                <?php \yii\widgets\ActiveForm::end(); ?>
                            </div>
                        </div>
                        <?php if (!empty($recentAssessments)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student Reg No</th>
                                            <th>School</th>
                                            <th>Assessment Date</th>
                                            <th>Overall Level</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentAssessments as $assessment): ?>
                                            <tr>
                                                <td><?= Html::encode($assessment->student_reg_no) ?></td>
                                                <td><?= Html::encode($assessment->school->school_name ?? 'N/A') ?></td>
                                                <td><?= date('M d, Y', strtotime($assessment->assessment_date)) ?></td>
                                                <td>
                                                    <?php 
                                                    $level = $assessment->overall_level;
                                                    $badge = 'badge-secondary';
                                                    if ($level === 'EE') $badge = 'badge-success';
                                                    elseif ($level === 'ME') $badge = 'badge-info';
                                                    elseif ($level === 'AE') $badge = 'badge-warning';
                                                    elseif ($level === 'BE') $badge = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?= $badge ?>">
                                                        <?= Html::encode($level ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                        '<i class="fas fa-eye"></i> View',
                                                        ['/assessment/view', 'assessment_id' => $assessment->assessment_id],
                                                        ['class' => 'btn btn-sm btn-info']
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i> No assessments found yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .border-left-primary {
        border-left: 0.25rem solid #5B9BD5 !important;
    }

    .bg-primary {
        background-color: #5B9BD5 !important;
    }

    .bg-success {
        background-color: #1cc88a !important;
    }

    .bg-info {
        background-color: #36b9cc !important;
    }

    .bg-secondary {
        background-color: #858796 !important;
    }

    .bg-warning {
        background-color: #f6c23e !important;
    }

    .text-primary {
        color: #5B9BD5 !important;
    }

    .stat-box {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
    }

    .card-header {
        padding: 1rem;
        font-size: 1rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
