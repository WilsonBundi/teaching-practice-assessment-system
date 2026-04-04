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
        $totalAssessments = Assessment::find()->count();
        $recentAssessments = Assessment::find()
            ->orderBy(['assessment_date' => SORT_DESC])
            ->limit(10)
            ->all();

        $schoolsCount = School::find()->count();
        $zonesCount = Zone::find()->count();
        $gradesCount = Grade::find()->count();

        return $this->render('index', [
            'totalAssessments' => $totalAssessments,
            'recentAssessments' => $recentAssessments,
            'schoolsCount' => $schoolsCount,
            'zonesCount' => $zonesCount,
            'gradesCount' => $gradesCount,
        ]);
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
    public function actionReports()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Assessment::find()
                ->where(['or', ['archived' => 0], ['archived' => null]])
                ->with(['school', 'grades', 'examinerUser']),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['assessment_date' => SORT_DESC],
            ],
        ]);

        return $this->render('reports', [
            'dataProvider' => $dataProvider,
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
     * Manage Sub-Strands
     */
    public function actionSubstrands()
    {
        if (!Yii::$app->user->identity || Yii::$app->user->identity->role_id != 3) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Substrand::find(),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('substrands', [
            'dataProvider' => $dataProvider,
        ]);
    }
}