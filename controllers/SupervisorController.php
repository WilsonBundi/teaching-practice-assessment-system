<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Assessment;
use app\models\AssessmentSearch;
use app\models\School;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * SupervisorController handles supervisor-specific operations
 */
class SupervisorController extends Controller
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
                            // Only allow supervisors (role_id = 1)
                            $user = Yii::$app->user->identity;
                            return $user && $user->role_id == 1;
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
     * Display supervisor profile
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity; // Get current logged-in supervisor

        // Get basic user info
        $supervisor = Users::findOne(['user_id' => $user->user_id]);

        // Get assessment statistics for this supervisor
        $totalAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->count();

        $pendingAssessments = Assessment::find()
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is', 'overall_level', null]) // NULL overall_level = incomplete
            ->count();

        $completedAssessments = $totalAssessments - $pendingAssessments;

        // Get unique schools where supervisor conducted assessments
        $schools = Assessment::find()
            ->select(['school_id'])
            ->where(['examiner_user_id' => $user->user_id])
            ->distinct()
            ->column();

        $schoolCount = count($schools);

        // Get unique students assessed
        $uniqueStudents = Assessment::find()
            ->select(['student_reg_no'])
            ->where(['examiner_user_id' => $user->user_id])
            ->distinct()
            ->count('DISTINCT student_reg_no');

        // Get total grades for this supervisor's assessments
        $totalGrades = \app\models\Grade::find()
            ->leftJoin('assessment', 'grade.assessment_id = assessment.assessment_id')
            ->where(['assessment.examiner_user_id' => $user->user_id])
            ->count();

        // Get unique learning areas assessed by this supervisor
        $learningAreas = Assessment::find()
            ->select(['learning_area_id'])
            ->where(['examiner_user_id' => $user->user_id])
            ->andWhere(['is not', 'learning_area_id', null])
            ->distinct()
            ->count('DISTINCT learning_area_id');

        // Setup search model for assessments
        $searchModel = new AssessmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Get the query object from the data provider and add supervisor filter
        $dataProvider->query->andWhere(['examiner_user_id' => $user->user_id])
                            ->andWhere(['or', ['archived' => 0], ['archived' => null]]);
        
        // Order by date descending and limit to recent
        $dataProvider->query->orderBy(['assessment_date' => SORT_DESC])
              ->limit(20); // Show more if searching
        
        $recentAssessments = $dataProvider->getModels();

        // Get supervisor's role name
        $role = $supervisor ? $supervisor->role : null;

        return $this->render('supervisor-profile', [
            'supervisor' => $supervisor,
            'role' => $role,
            'totalAssessments' => $totalAssessments,
            'pendingAssessments' => $pendingAssessments,
            'completedAssessments' => $completedAssessments,
            'schoolCount' => $schoolCount,
            'uniqueStudents' => $uniqueStudents,
            'totalGrades' => $totalGrades,
            'learningAreas' => $learningAreas,
            'recentAssessments' => $recentAssessments,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Edit supervisor profile
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        $supervisor = Users::findOne(['user_id' => $user->user_id]);

        if ($supervisor->load(Yii::$app->request->post()) && $supervisor->save()) {
            Yii::$app->session->setFlash('success', 'Profile updated successfully!');
            return $this->redirect(['profile']);
        }

        return $this->render('edit-supervisor-profile', [
            'model' => $supervisor,
        ]);
    }

    /**
     * Step 1: Select student for assessment
     */
    public function actionSelectStudent()
    {
        $user = Yii::$app->user->identity;
        $searchQuery = Yii::$app->request->get('search', '');
        $students = [];

        if (!empty($searchQuery)) {
            // Search for students by registration number in existing assessments
            // This shows students previously assessed (pivot on student_reg_no)
            $students = Assessment::find()
                ->distinct()
                ->select('student_reg_no')
                ->where(['ilike', 'student_reg_no', '%' . $searchQuery . '%'])
                ->column();
            
            // Also add option to create assessment for new student
            if (!in_array($searchQuery, $students)) {
                array_unshift($students, $searchQuery);
            }
        } else {
            // Show all students previously assessed
            $students = Assessment::find()
                ->distinct()
                ->select('student_reg_no')
                ->orderBy(['student_reg_no' => SORT_ASC])
                ->limit(50)
                ->column();
        }

        if (Yii::$app->request->isPost) {
            $studentRegNo = Yii::$app->request->post('student_reg_no');
            
            if (!empty($studentRegNo)) {
                // Create new assessment for this student
                $assessment = new Assessment();
                $assessment->examiner_user_id = $user->user_id;
                $assessment->student_reg_no = $studentRegNo;
                $assessment->assessment_date = date('Y-m-d');
                
                // Default school - this could be enhanced
                $school = School::find()->one();
                if ($school) {
                    $assessment->school_id = $school->school_id;
                } else {
                    Yii::$app->session->setFlash('error', 'No school found. Please contact administrator.');
                    return $this->redirect(['select-student']);
                }

                $assessment->archived = 0; // ensure new assessments are visible as in-progress

                if ($assessment->save()) {
                    Yii::$app->session->setFlash('success', 'Assessment created. Proceed to record rubric.');
                    return $this->redirect(['/assessment/view', 'assessment_id' => $assessment->assessment_id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to create assessment.');
                }
            }
        }

        return $this->render('select-student', [
            'students' => $students,
            'searchQuery' => $searchQuery,
        ]);
    }
}

