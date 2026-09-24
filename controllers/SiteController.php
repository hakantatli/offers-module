<?php

namespace app\controllers;

use Yii;
use app\models\Casino;
use app\models\LoginForm;
use app\models\Offer;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * SiteController handles authentication and generic site actions.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return Response
     */
    public function actionIndex()
    {
        return $this->redirect(['offer/index']);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['casino/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(['casino/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Generates dynamic sitemap.xml listing active casinos and active non-expired offers with lastmod.
     * Draft and expired offers are excluded.
     *
     * @return Response
     */
    public function actionSitemap(): Response
    {
        /** @var Casino[] $casinos */
        $casinos = Casino::find()
            ->where(['is_active' => 1])
            ->with([
                'offers' => function ($query) {
                    $query->where(['status' => Offer::STATUS_ACTIVE])
                        ->andWhere([
                            'or',
                            ['expires_at' => null],
                            ['>', 'expires_at', new Expression('NOW()')],
                        ])
                        ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC]);
                },
            ])
            ->orderBy(['rating' => SORT_DESC, 'name' => SORT_ASC])
            ->all();

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');
        $response->content = $this->renderPartial('sitemap', [
            'casinos' => $casinos,
        ]);

        return $response;
    }
}
