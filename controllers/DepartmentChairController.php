<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Assessment;
use app\models\AssessmentSearch;
use app\models\Grade;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * DepartmentChairController handles department chair-specific operations
 */
class DepartmentChairController extends Controller
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
                        'roles' => ['@'], // Logged in users
                        'matchCallback' => function ($rule, $action) {
                            // Only allow Department Chair (role_id = 4)
                            $user = Yii::$app->user->identity;
                            return $user && $user->role_id == 4;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'profile' => ['GET', 'POST'],
                    'edit' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * Display department chair profile
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity; // Get current logged-in department chair

        // Get basic user info
        $chair = Users::findOne(['user_id' => $user->user_id]);

        // Get comprehensive assessment statistics
        $totalAssessments = Assessment::find()->count();

        $completedAssessments = Assessment::find()
            ->where(['archived' => 0])
            ->andWhere(['not', ['overall_level' => null]])
            ->count();

        $inProgressAssessments = Assessment::find()
            ->andWhere(['or', ['archived' => 0], ['archived' => null]])
            ->andWhere(['is', 'overall_level', null])
            ->count();

        // Average scores across all assessments
        $avgScore = Assessment::find()
            ->select(['AVG(total_score)'])
            ->where(['not', ['total_score' => null]])
            ->scalar();

        // Get all schools count
        $totalSchools = \app\models\School::find()->count();

        // Get all supervisors count
        $totalSupervisors = Users::find()
            ->where(['role_id' => 1]) // Supervisor role
            ->count();

        // Setup search model for all assessments (chair monitors all)
        $searchModel = new AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Order by date descending
        $dataProvider->query->orderBy(['assessment_date' => SORT_DESC])
            ->limit(15);
        
        $recentAssessments = $dataProvider->getModels();

        // Get grade distribution
        $gradeDistribution = Grade::find()
            ->groupBy(['level'])
            ->select(['level', 'COUNT(*) as count'])
            ->all();

        // Get chair's role name
        $role = $chair ? $chair->role : null;

        return $this->render('department-chair-profile', [
            'chair' => $chair,
            'role' => $role,
            'totalAssessments' => $totalAssessments,
            'completedAssessments' => $completedAssessments,
            'inProgressAssessments' => $inProgressAssessments,
            'avgScore' => $avgScore,
            'totalSchools' => $totalSchools,
            'totalSupervisors' => $totalSupervisors,
            'recentAssessments' => $recentAssessments,
            'gradeDistribution' => $gradeDistribution,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Edit department chair profile
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        $chair = Users::findOne(['user_id' => $user->user_id]);

        if ($chair->load(Yii::$app->request->post()) && $chair->save()) {
            Yii::$app->session->setFlash('success', 'Profile updated successfully!');
            return $this->redirect(['profile']);
        }

        return $this->render('edit-department-chair-profile', [
            'model' => $chair,
        ]);
    }

    /**
     * View system reports
     */
    public function actionSystemReports()
    {
        // Get comprehensive statistics
        $stats = [
            'totalAssessments' => Assessment::find()->count(),
            'totalGrades' => Grade::find()->count(),
            'totalSchools' => \app\models\School::find()->count(),
            'totalSupervisors' => Users::find()->where(['role_id' => 1])->count(),
            'totalZoneCoordinators' => Users::find()->where(['role_id' => 2])->count(),
            'totalTpOffice' => Users::find()->where(['role_id' => 3])->count(),
            'completedAssessments' => Assessment::find()->where(['archived' => 0])->andWhere(['not', ['overall_level' => null]])->count(),
            'pendingAssessments' => Assessment::find()->andWhere(['or', ['archived' => 0], ['archived' => null]])->andWhere(['is', 'overall_level', null])->count(),
        ];

        // Grade level distribution
        $gradeDistribution = Grade::find()
            ->groupBy(['level'])
            ->select(['level', 'COUNT(*) as count'])
            ->asArray()
            ->all();

        return $this->render('system-reports', [
            'stats' => $stats,
            'gradeDistribution' => $gradeDistribution,
        ]);
    }

    /**
     * Monitor assessments
     */
    public function actionMonitorAssessments()
    {
        $searchModel = new AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Order by date and status
        $dataProvider->query->orderBy(['assessment_date' => SORT_DESC, 'archived' => SORT_ASC])
            ->limit(50);

        return $this->render('monitor-assessments', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
