<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OfferSearch represents the model behind the search form of `app\models\Offer`.
 */
class OfferSearch extends Offer
{
    /**
     * @var string|null Casino name for sorting and searching
     */
    public $casino_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'casino_id'], 'integer'],
            [['title', 'slug', 'type', 'status', 'casino_name', 'amount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     * Uses eager loading with joinWith to prevent N+1 queries.
     *
     * @param array<string, mixed> $params
     * @return ActiveDataProvider
     */
    public function search(array $params = []): ActiveDataProvider
    {
        $query = Offer::find()->joinWith(['casino']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'title',
                    'type',
                    'amount',
                    'status',
                    'expires_at',
                    'created_at',
                    'casino_name' => [
                        'asc' => ['casino.name' => SORT_ASC],
                        'desc' => ['casino.name' => SORT_DESC],
                        'label' => 'Casino',
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'offer.id' => $this->id,
            'offer.casino_id' => $this->casino_id,
            'offer.type' => $this->type,
            'offer.status' => $this->status,
        ]);

        // Support operator filters for amount (e.g. >=100, >50, <=500, 250)
        if (!empty($this->amount)) {
            $trimmed = trim((string)$this->amount);
            if (preg_match('/^(>=|<=|>|<|=)?\s*([0-9]+(?:\.[0-9]+)?)$/', $trimmed, $matches)) {
                $operator = $matches[1] ?: '=';
                $value = (float)$matches[2];
                $query->andWhere([$operator, 'offer.amount', $value]);
            }
        }

        $query->andFilterWhere(['like', 'offer.title', $this->title])
            ->andFilterWhere(['like', 'offer.slug', $this->slug])
            ->andFilterWhere(['like', 'casino.name', $this->casino_name]);

        return $dataProvider;
    }
}
