<?php

namespace app\controllers;

use app\models\LearningArea;
use app\models\LearningAreaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LearningAreaController implements the CRUD actions for LearningArea model.
 */
class LearningAreaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all LearningArea models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LearningAreaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LearningArea model.
     * @param int $learning_area_id Learning Area ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($learning_area_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($learning_area_id),
        ]);
    }

    /**
     * Creates a new LearningArea model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LearningArea();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'learning_area_id' => $model->learning_area_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LearningArea model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $learning_area_id Learning Area ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($learning_area_id)
    {
        $model = $this->findModel($learning_area_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'learning_area_id' => $model->learning_area_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LearningArea model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $learning_area_id Learning Area ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($learning_area_id)
    {
        $this->findModel($learning_area_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LearningArea model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $learning_area_id Learning Area ID
     * @return LearningArea the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($learning_area_id)
    {
        if (($model = LearningArea::findOne(['learning_area_id' => $learning_area_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
