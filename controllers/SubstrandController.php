<?php

namespace app\controllers;

use app\models\Substrand;
use app\models\SubstrandSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SubstrandController implements the CRUD actions for Substrand model.
 */
class SubstrandController extends Controller
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
     * Lists all Substrand models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SubstrandSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Substrand model.
     * @param int $substrand_id Substrand ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($substrand_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($substrand_id),
        ]);
    }

    /**
     * Creates a new Substrand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Substrand();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'substrand_id' => $model->substrand_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Substrand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $substrand_id Substrand ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($substrand_id)
    {
        $model = $this->findModel($substrand_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'substrand_id' => $model->substrand_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Substrand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $substrand_id Substrand ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($substrand_id)
    {
        $this->findModel($substrand_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Substrand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $substrand_id Substrand ID
     * @return Substrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($substrand_id)
    {
        if (($model = Substrand::findOne(['substrand_id' => $substrand_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
