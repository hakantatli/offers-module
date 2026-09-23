<?php

namespace app\controllers;

use Yii;
use app\models\Casino;
use app\models\Offer;
use app\models\OfferSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OfferAdminController implements the CRUD actions for Offer model behind authentication.
 */
class OfferAdminController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Offer models with filtering, sorting and pagination.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $casinos = ArrayHelper::map(Casino::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'casinos' => $casinos,
        ]);
    }

    /**
     * Displays a single Offer model.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Offer model.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Offer();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', "Offer '{$model->title}' was created successfully.");
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
            $model->status = Offer::STATUS_ACTIVE;
        }

        $casinos = ArrayHelper::map(Casino::find()->where(['is_active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        return $this->render('create', [
            'model' => $model,
            'casinos' => $casinos,
        ]);
    }

    /**
     * Updates an existing Offer model.
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Offer '{$model->title}' was updated successfully.");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $casinos = ArrayHelper::map(Casino::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        return $this->render('update', [
            'model' => $model,
            'casinos' => $casinos,
        ]);
    }

    /**
     * Deletes an existing Offer model.
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $title = $model->title;
        $model->delete();

        Yii::$app->session->setFlash('info', "Offer '{$title}' was deleted.");
        return $this->redirect(['index']);
    }

    /**
     * Finds the Offer model based on its primary key value.
     *
     * @param int $id
     * @return Offer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Offer::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested offer does not exist.');
    }
}
