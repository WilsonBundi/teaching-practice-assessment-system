<?php

/** @var yii\web\View $this */
/** @var array $stats Dashboard statistics from session */

use yii\bootstrap5\Html;
use app\components\RbacHelper;
use app\models\Assessment;

$this->title = 'Dashboard';
?>

<style>
    .dashboard-page {
        padding: 30px 0;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #5B9BD5 0%, #2E75B6 100%);
        color: white;
        padding: 40px;
        border-radius: 10px;
        margin-bottom: 40px;
        box-shadow: 0 4px 15px rgba(91, 155, 213, 0.3);
    }

    .welcome-banner h2 {
        font-size: 1.8rem;
        color: white;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .welcome-banner p {
        color: rgba(255,255,255,0.95);
        font-size: 1rem;
        margin: 0;
    }

    .dashboard-section {
        margin-bottom: 40px;
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #3498db;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #3498db;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 25px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        border-left: 4px solid #3498db;
        transition: transform 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #3498db;
        margin-bottom: 8px;
    }

    .stat-label {
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .action-card {
        display: flex;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #28a745;
        transition: all 0.3s ease;
    }

    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .action-icon {
        font-size: 2rem;
        color: #28a745;
        margin-right: 20px;
        width: 50px;
        text-align: center;
    }

    .action-content h4 {
        margin: 0 0 5px 0;
        font-weight: 600;
        color: #2c3e50;
    }

    .action-content p {
        margin: 0 0 15px 0;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .workflow-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .workflow-step {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        border-left: 4px solid #17a2b8;
        transition: all 0.3s ease;
    }

    .workflow-step:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .step-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #17a2b8;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        font-size: 0.9rem;
    }

    .step-header h4 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    .step-count {
        font-size: 2rem;
        font-weight: 800;
        color: #17a2b8;
        margin-bottom: 5px;
    }

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .module-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .module-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .module-item i {
        font-size: 2.5rem;
        color: #6c757d;
        margin-bottom: 15px;
        display: block;
    }

    .module-item h5 {
        margin: 0 0 10px 0;
        color: #2c3e50;
        font-weight: 600;
    }

    .module-item p {
        color: #6c757d;
        margin: 0 0 20px 0;
        font-size: 0.9rem;
    }

    .module-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .module-actions .btn {
        flex: 1;
    }

    .assessment-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .assessment-table .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 20px;
    }

    .assessment-table .card-header h5 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    .assessment-table .table {
        margin: 0;
    }

    .assessment-table .table th {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        color: #495057;
    }

    @media (max-width: 768px) {
        .dashboard-page {
            padding: 15px;
        }

        .dashboard-section {
            padding: 20px;
        }

        .stats-grid,
        .actions-grid,
        .workflow-grid,
        .modules-grid {
            grid-template-columns: 1fr;
        }

        .action-card,
        .workflow-step {
            flex-direction: column;
            text-align: center;
        }

        .action-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .step-header {
            justify-content: center;
        }

        .module-actions {
            flex-direction: column;
        }

        .module-actions .btn {
            margin-bottom: 5px;
        }
    }
</style>

<div class="dashboard-page">
    <!-- Welcome Banner -->
    <div class="welcome-banner" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>Welcome, <?= Html::encode(Yii::$app->user->identity->username) ?></h2>
            <p style="font-size: 1rem; margin: 10px 0 0; color: rgba(255,255,255,0.95);">
                Role: <strong><?= Html::encode(app\components\RbacHelper::getCurrentUserRole() ?: 'Guest') ?></strong>
            </p>
            <?php $currentRole = app\components\RbacHelper::getCurrentUserRole(); ?>
            <?php $roleActions = app\components\RbacHelper::getActionsForRole($currentRole); ?>
            <p style="font-size: 0.9rem; margin-top: 10px; color: rgba(255,255,255,0.95);">
                Available actions: <?= Html::encode(implode(', ', $roleActions)) ?>
            </p>
        </div>
        <div>
            <?php if (Yii::$app->user->identity->role_id == 1): // Supervisor role ?>
                <?= Html::a(
                    '<i class="fas fa-user-circle"></i> Supervisor Profile',
                    ['/supervisor/profile'],
                    ['class' => 'btn btn-light btn-sm', 'style' => 'font-weight: 600;']
                ) ?>
            <?php elseif (Yii::$app->user->identity->role_id == 2): // Zone Coordinator role ?>
                <?= Html::a(
                    '<i class="fas fa-user-check"></i> Zone Coordinator Profile',
                    ['/zone-coordinator/profile'],
                    ['class' => 'btn btn-light btn-sm', 'style' => 'font-weight: 600;']
                ) ?>
            <?php elseif (Yii::$app->user->identity->role_id == 3): // TP Office role ?>
                <?= Html::a(
                    '<i class="fas fa-building"></i> TP Office Profile',
                    ['/tp-office-coordinator/profile'],
                    ['class' => 'btn btn-light btn-sm', 'style' => 'font-weight: 600;']
                ) ?>
            <?php elseif (Yii::$app->user->identity->role_id == 4): // Department Chair role ?>
                <?= Html::a(
                    '<i class="fas fa-crown"></i> Department Chair Profile',
                    ['/department-chair/profile'],
                    ['class' => 'btn btn-light btn-sm', 'style' => 'font-weight: 600;']
                ) ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- SYSTEM OVERVIEW STATISTICS -->
    <div class="dashboard-section">
        <h3 class="section-title"><i class="fas fa-chart-bar"></i> System Overview</h3>
        <div class="stats-grid">
            <?php
            $totalAssessments = Assessment::find()->count();
            $pendingValidation = Assessment::find()
                ->andWhere(['archived' => 1])
                ->andWhere(['is', 'validated_by', null])
                ->count();
            $validatedAssessments = Assessment::find()->andWhere(['is not', 'validated_by', null])->count();
            $schoolCount = Assessment::find()->select(['school_id'])->distinct()->count('DISTINCT school_id');
            ?>
            <div class="stat-item">
                <div class="stat-value"><?= $totalAssessments ?></div>
                <div class="stat-label">Total Assessments</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $pendingValidation ?></div>
                <div class="stat-label">Pending Validation</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $validatedAssessments ?></div>
                <div class="stat-label">Validated</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $schoolCount ?></div>
                <div class="stat-label">Schools</div>
            </div>
        </div>
    </div>

    <!-- ROLE-BASED MAIN CONTENT -->
    <?php if (RbacHelper::isSupervisor()): ?>
        <!-- SUPERVISOR DASHBOARD -->
        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-plus-circle"></i> Assessment Management</h3>
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-plus-circle"></i></div>
                    <div class="action-content">
                        <h4>Create Assessment</h4>
                        <p>Start a new assessment report</p>
                        <?= Html::a('Create Now', ['/assessment/create'], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-list"></i></div>
                    <div class="action-content">
                        <h4>My Assessments</h4>
                        <p>View and manage your assessments</p>
                        <?= Html::a('View All', ['/assessment/index'], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif (RbacHelper::isZoneCoordinator()): ?>
        <!-- ZONE COORDINATOR DASHBOARD -->
        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-tasks"></i> Assessment Workflow</h3>
            <div class="workflow-grid">
                <?php
                $pendingReviews = Assessment::find()
                    ->andWhere(['archived' => 1])
                    ->andWhere(['is', 'validated_by', null])
                    ->count();
                $readyForValidation = Assessment::find()
                    ->andWhere(['archived' => 1])
                    ->andWhere(['is', 'validated_by', null])
                    ->andWhere(['is not', 'overall_level', null])
                    ->count();
                ?>
                <div class="workflow-step">
                    <div class="step-header">
                        <span class="step-number">1</span>
                        <h4>Pending Reviews</h4>
                    </div>
                    <div class="step-count"><?= $pendingReviews ?></div>
                    <p>Assessments awaiting review</p>
                    <?php if ($pendingReviews > 0): ?>
                        <?= Html::a('Review Now', ['/assessment/index'], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?php endif; ?>
                </div>
                <div class="workflow-step">
                    <div class="step-header">
                        <span class="step-number">2</span>
                        <h4>Ready for Validation</h4>
                    </div>
                    <div class="step-count"><?= $readyForValidation ?></div>
                    <p>Completed assessments ready to validate</p>
                    <?php if ($readyForValidation > 0): ?>
                        <?= Html::a('Validate All', ['/zone-coordinator/validate-all'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post', 'data-confirm' => 'Validate all ready assessments now?']) ?>
                    <?php endif; ?>
                </div>
                <div class="workflow-step">
                    <div class="step-header">
                        <span class="step-number">3</span>
                        <h4>Validated</h4>
                    </div>
                    <div class="step-count"><?= $validatedAssessments ?></div>
                    <p>Successfully validated assessments</p>
                    <?= Html::a('View History', ['/assessment/index', 'AssessmentSearch' => ['validated_by' => 'not null']], ['class' => 'btn btn-outline-success btn-sm']) ?>
                </div>
            </div>
        </div>

        <!-- RECENT ASSESSMENTS TABLE -->
        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-clock"></i> Recent Assessments</h3>
            <div class="assessment-table">
                <?php
                $recentAssessments = Assessment::find()
                    ->andWhere(['archived' => 1])
                    ->orderBy(['assessment_date' => SORT_DESC])
                    ->limit(10)
                    ->all();
                ?>
                <?php if (count($recentAssessments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>School</th>
                                    <th>Date</th>
                                    <th>Examiner</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentAssessments as $assessment): ?>
                                    <tr>
                                        <td><strong><?= Html::encode($assessment->student_reg_no) ?></strong></td>
                                        <td><?= Html::encode($assessment->school ? $assessment->school->school_name : 'N/A') ?></td>
                                        <td><?= Html::encode($assessment->assessment_date) ?></td>
                                        <td><?= Html::encode($assessment->examinerUser ? $assessment->examinerUser->name : 'N/A') ?></td>
                                        <td>
                                            <?php if ($assessment->validated_by): ?>
                                                <span class="badge bg-success">Validated</span>
                                            <?php elseif ($assessment->overall_level): ?>
                                                <span class="badge bg-info">Complete</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Incomplete</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?= Html::a('<i class="fas fa-eye"></i>', ['/zone-coordinator/review-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-info', 'title' => 'Review']) ?>
                                                <?php if (!$assessment->validated_by && $assessment->overall_level): ?>
                                                    <?= Html::a('<i class="fas fa-check"></i>', ['/zone-coordinator/validate-assessment', 'assessment_id' => $assessment->assessment_id], ['class' => 'btn btn-success', 'title' => 'Validate']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No assessments available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif (RbacHelper::isTpOffice()): ?>
        <!-- TP OFFICE DASHBOARD -->
        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-tachometer-alt"></i> TP Office Dashboard</h3>
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="action-content">
                        <h4>Assessment Dashboard</h4>
                        <p>View assessment statistics and recent assessments</p>
                        <?= Html::a('Go to Dashboard', ['/tp-office/index'], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-cogs"></i> System Administration</h3>
            <div class="modules-grid">
                <div class="module-item">
                    <i class="fas fa-school"></i>
                    <h5>Schools</h5>
                    <p>Manage school information</p>
                    <div class="module-actions">
                        <?= Html::a('View', ['/school/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Create', ['/school/create'], ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
                <div class="module-item">
                    <i class="fas fa-graduation-cap"></i>
                    <h5>Grades</h5>
                    <p>Manage grade levels</p>
                    <div class="module-actions">
                        <?= Html::a('View', ['/grade/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Create', ['/grade/create'], ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
                <div class="module-item">
                    <i class="fas fa-book"></i>
                    <h5>Learning Areas</h5>
                    <p>Manage subject areas</p>
                    <div class="module-actions">
                        <?= Html::a('View', ['/learning-area/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Create', ['/learning-area/create'], ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
                <div class="module-item">
                    <i class="fas fa-users"></i>
                    <h5>Users</h5>
                    <p>Manage system users</p>
                    <div class="module-actions">
                        <?= Html::a('View', ['/users/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Create', ['/users/create'], ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif (RbacHelper::isDepartmentChair()): ?>
        <!-- DEPARTMENT CHAIR DASHBOARD -->
        <div class="dashboard-section">
            <h3 class="section-title"><i class="fas fa-chart-line"></i> Assessment Reports</h3>
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-icon"><i class="fas fa-list"></i></div>
                    <div class="action-content">
                        <h4>View Assessments</h4>
                        <p>Monitor TP assessments and reports</p>
                        <?= Html::a('View All', ['/assessment/index'], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>