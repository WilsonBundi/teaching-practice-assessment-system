<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\Assessment;
use app\models\Users;
use app\models\School;
use app\models\Zone;
use app\models\Grade;
use app\models\LearningArea;
use app\models\Strand;
use app\models\Substrand;

/**
 * TP Office Controller - Handles TP Office user operations
 * View reports, download reports, archive records, manage master data
 */
class TpOfficeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Authenticated users only
                    ],
                ],
            ],
        ];
    }

    /**
     * TP Office Dashboard
     */
    public function actionIndex()
    {
        // Check if user has TP Office role
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) { // Assuming 3 is TP Office role
            throw new \yii\web\ForbiddenHttpException('Access denied. TP Office access required.');
        }

        // Get assessment statistics
        $totalAssessments = Assessment::find()->where(['is not', 'validated_by', null])->count(); // Only validated assessments
        $completedAssessments = Assessment::find()->where(['not', ['overall_level' => null]])->count();
        $recentAssessments = Assessment::find()
            ->orderBy(['assessment_date' => SORT_DESC])
            ->limit(10)
            ->all();

        $schoolsCount = School::find()->count();
        $zonesCount = Zone::find()->count();
        $gradesCount = Grade::find()->count();

        return $this->render('index', [
            'totalAssessments' => $totalAssessments,
            'completedAssessments' => $completedAssessments,
            'recentAssessments' => $recentAssessments,
            'schoolsCount' => $schoolsCount,
            'zonesCount' => $zonesCount,
            'gradesCount' => $gradesCount,
        ]);
    }

    /**
     * Get dashboard data for real-time updates (AJAX)
     */
    public function actionGetDashboardData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $lastUpdate = Yii::$app->request->get('last_update', 0);
        $currentTime = time();

        // Get current assessment count
        $totalAssessments = Assessment::find()->where(['is not', 'validated_by', null])->count(); // Only validated assessments
        $completedAssessments = Assessment::find()->where(['not', ['overall_level' => null]])->count();

        // Check if there are new assessments since last update
        $newAssessmentsCount = Assessment::find()
            ->where(['is not', 'validated_by', null]) // Only check validated assessments
            ->andWhere(['>', 'assessment_date', date('Y-m-d H:i:s', $lastUpdate/1000)])
            ->count();

        $updated = $newAssessmentsCount > 0;

        // Get recent assessments HTML if updated
        $recentAssessmentsHtml = null;
        if ($updated) {
            $recentAssessments = Assessment::find()
                ->where(['is not', 'validated_by', null]) // Only validated assessments
                ->orderBy(['assessment_date' => SORT_DESC])
                ->limit(10)
                ->all();

            $recentAssessmentsHtml = $this->renderPartial('_recent_assessments_table', [
                'recentAssessments' => $recentAssessments
            ]);
        }

        return [
            'updated' => $updated,
            'totalAssessments' => $totalAssessments,
            'completedAssessments' => $completedAssessments,
            'recentAssessmentsHtml' => $recentAssessmentsHtml,
            'timestamp' => $currentTime * 1000
        ];
    }

    /**
     * Get Reports Data for AJAX updates
     */
    public function actionGetReportsData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $lastUpdate = Yii::$app->request->get('last_update', 0);
        $status = Yii::$app->request->get('status', 'all');
        $currentTime = time();

        // Build query based on status - only validated assessments
        $query = Assessment::find()
            ->where(['is not', 'validated_by', null]);

        // Filter by status
        if ($status === 'completed') {
            $query->andWhere(['not', ['overall_level' => null]]);
        } elseif ($status === 'pending') {
            $query->andWhere(['is', 'overall_level', null]);
        }

        // Get current assessment count for this filter
        $totalAssessments = $query->count();

        // Check if there are new assessments since last update for this filter
        $newAssessmentsCount = $query
            ->andWhere(['>', 'assessment_date', date('Y-m-d H:i:s', $lastUpdate/1000)])
            ->count();

        $updated = $newAssessmentsCount > 0;

        // Get reports table HTML if updated
        $reportsTableHtml = null;
        if ($updated) {
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => Assessment::find()
                    ->where(['is not', 'validated_by', null])
                    ->with(['school', 'grades', 'examinerUser'])
                    ->andWhere($status === 'completed' ? ['not', ['overall_level' => null]] :
                              ($status === 'pending' ? ['is', 'overall_level', null] : [])),
                'pagination' => ['pageSize' => 20],
                'sort' => [
                    'defaultOrder' => ['assessment_date' => SORT_DESC],
                ],
            ]);

            $reportsTableHtml = $this->renderPartial('_reports_table', [
                'dataProvider' => $dataProvider
            ]);
        }

        return [
            'updated' => $updated,
            'totalAssessments' => $totalAssessments,
            'reportsTableHtml' => $reportsTableHtml,
            'timestamp' => $currentTime * 1000
        ];
    }

    /**
     * View Assessment Details
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $model = \app\models\Assessment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Download Reports
     */
    public function actionDownloadReport($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $assessment = Assessment::findOne($id);
        if (!$assessment) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Generate PDF report using Dompdf
        $content = $this->renderPartial('_report_pdf', ['assessment' => $assessment]);

        $dompdf = new \Dompdf\Dompdf([ 'isPhpEnabled' => true ]);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream($assessment->student_reg_no . '_Assessment_Report.pdf', [
            'Attachment' => true
        ]);
    }

    /**
     * Archive Assessment Records
     */
    public function actionArchive($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $assessment = Assessment::findOne($id);
        if (!$assessment) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Mark as archived using archived status (0=active, 1=archived)
        $assessment->archived = 1;
        $assessment->archived_at = date('Y-m-d H:i:s');
        $assessment->save(false);

        Yii::$app->session->setFlash('success', 'Assessment record archived successfully.');
        return $this->redirect(['reports']);
    }

    /**
     * Unarchive Assessment Records
     */
    public function actionUnarchive($id)
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $assessment = Assessment::find()->where(['assessment_id' => $id])->one();
        if (!$assessment) {
            throw new \yii\web\NotFoundHttpException('Assessment not found.');
        }

        // Mark as unarchived
        $assessment->archived = 0;
        $assessment->archived_at = null;
        $assessment->save(false);

        Yii::$app->session->setFlash('success', 'Assessment record restored successfully.');
        return $this->redirect(['archived-records']);
    }

    /**
     * View Archived Assessment Records
     */
    public function actionArchivedRecords()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Assessment::find()
                ->where(['archived' => 1])
                ->with(['school', 'grades', 'examinerUser']),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['archived_at' => SORT_DESC],
            ],
        ]);

        return $this->render('archived-records', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View Assessment Reports
     */
    public function actionReports($status = 'all')
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $query = Assessment::find()
            ->where(['is not', 'validated_by', null]) // Only show validated assessments
            ->with(['school', 'grades', 'examinerUser']);

        // Additional filter by status
        if ($status === 'completed') {
            $query->andWhere(['not', ['overall_level' => null]]);
        } elseif ($status === 'pending') {
            $query->andWhere(['is', 'overall_level', null]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['assessment_date' => SORT_DESC],
            ],
        ]);

        return $this->render('reports', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Manage Schools
     */
    public function actionSchools()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\School::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('schools', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Zones
     */
    public function actionZones()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Zone::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('zones', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Grades
     */
    public function actionGrades()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Grade::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('grades', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Learning Areas
     */
    public function actionLearningAreas()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\LearningArea::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('learning-areas', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Strands
     */
    public function actionStrands()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Strand::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('strands', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Manage Master Data
     */
    public function actionMasterData()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $stats = [
            'schools' => \app\models\School::find()->count(),
            'zones' => \app\models\Zone::find()->count(),
            'grades' => \app\models\Grade::find()->count(),
            'learningAreas' => \app\models\LearningArea::find()->count(),
            'strands' => \app\models\Strand::find()->count(),
            'substrands' => \app\models\Substrand::find()->count(),
        ];

        return $this->render('master-data', [
            'stats' => $stats,
        ]);
    }
}