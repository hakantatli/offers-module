<?php

namespace app\controllers;

use Yii;
use app\models\Casino;
use app\models\Offer;
use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OfferController handles public-facing offers listing and detail views.
 */
class OfferController extends Controller
{
    /**
     * Public offers directory.
     * Displays active, non-expired offers with 20 items per page,
     * filterable by offer type and casino, with eager loading to prevent N+1 queries.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $selectedType = Yii::$app->request->get('type');
        $selectedCasino = Yii::$app->request->get('casino_id');

        // Base query: Only active offers for active casinos, non-expired (or no expiration)
        $query = Offer::find()
            ->joinWith(['casino'])
            ->where(['offer.status' => Offer::STATUS_ACTIVE])
            ->andWhere(['casino.is_active' => 1])
            ->andWhere([
                'or',
                ['offer.expires_at' => null],
                ['>', 'offer.expires_at', new Expression('NOW()')],
            ]);

        // Filter by offer type if selected
        if (!empty($selectedType) && array_key_exists($selectedType, Offer::getTypes())) {
            $query->andWhere(['offer.type' => $selectedType]);
        }

        // Filter by casino if selected
        if (!empty($selectedCasino)) {
            $query->andWhere(['offer.casino_id' => (int)$selectedCasino]);
        }

        // Count query for pagination (20 per page)
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'defaultPageSize' => 20,
            'pageSize' => 20,
        ]);

        // Eager load casino relation to guarantee zero N+1 queries on the card listing
        $offers = $query
            ->with(['casino'])
            ->orderBy([
                'casino.rating' => SORT_DESC,
                'offer.amount' => SORT_DESC,
            ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        // Get list of active casinos for filter dropdown
        $casinos = ArrayHelper::map(
            Casino::find()->where(['is_active' => 1])->orderBy(['name' => SORT_ASC])->all(),
            'id',
            'name'
        );

        return $this->render('index', [
            'offers' => $offers,
            'pagination' => $pagination,
            'selectedType' => $selectedType,
            'selectedCasino' => $selectedCasino,
            'casinos' => $casinos,
            'types' => Offer::getTypes(),
        ]);
    }

    /**
     * Public offers directory filtered by casino slug.
     *
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException if the casino is not found or inactive
     */
    public function actionCasino(string $slug): string
    {
        $casino = Casino::find()->where(['slug' => $slug, 'is_active' => 1])->one();
        if ($casino === null) {
            throw new NotFoundHttpException('The requested casino does not exist or is inactive.');
        }

        Yii::$app->request->setQueryParams(array_merge(
            Yii::$app->request->getQueryParams(),
            ['casino_id' => (string)$casino->id]
        ));

        $this->view->title = $casino->name . ' - Casino Offers';

        return $this->actionIndex();
    }

    /**
     * Displays a single offer detail page by slug.
     *
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException if the offer is not found, inactive, or expired
     */
    public function actionView(string $slug): string
    {
        $model = Offer::find()
            ->joinWith(['casino'])
            ->where(['offer.slug' => $slug])
            ->andWhere(['offer.status' => Offer::STATUS_ACTIVE])
            ->andWhere(['casino.is_active' => 1])
            ->andWhere([
                'or',
                ['offer.expires_at' => null],
                ['>', 'offer.expires_at', new Expression('NOW()')],
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('The requested offer does not exist or has expired.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
