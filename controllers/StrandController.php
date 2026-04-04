<?php

namespace app\controllers;

use app\models\Strand;
use app\models\StrandSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StrandController implements the CRUD actions for Strand model.
 */
class StrandController extends Controller
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
     * Lists all Strand models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StrandSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Strand model.
     * @param int $strand_id Strand ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($strand_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($strand_id),
        ]);
    }

    /**
     * Creates a new Strand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Strand();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'strand_id' => $model->strand_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Strand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $strand_id Strand ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($strand_id)
    {
        $model = $this->findModel($strand_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'strand_id' => $model->strand_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Strand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $strand_id Strand ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($strand_id)
    {
        $this->findModel($strand_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Strand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $strand_id Strand ID
     * @return Strand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($strand_id)
    {
        if (($model = Strand::findOne(['strand_id' => $strand_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
