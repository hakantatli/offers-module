<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CasinoSearch represents the model behind the search form of `app\models\Casino`.
 */
class CasinoSearch extends Casino
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_active'], 'integer'],
            [['name', 'slug', 'rating'], 'safe'],
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
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Casino::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'is_active' => $this->is_active,
        ]);

        // Support operator filters for rating (e.g. >2, >=4.5, <3, <=4, 4.8)
        if (!empty($this->rating)) {
            $trimmed = trim((string)$this->rating);
            if (preg_match('/^(>=|<=|>|<|=)?\s*([0-9]+(?:\.[0-9]+)?)$/', $trimmed, $matches)) {
                $operator = $matches[1] ?: '=';
                $value = (float)$matches[2];
                $query->andWhere([$operator, 'rating', $value]);
            }
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}
