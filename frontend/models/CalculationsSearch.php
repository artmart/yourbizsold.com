<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Calculations;

/**
 * CalculationsSearch represents the model behind the search form of `frontend\models\Calculations`.
 */
class CalculationsSearch extends Calculations
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'age'], 'integer'],
            [['business_sale_price', 'owner_basis', 'ordinary_gain', 'other_ordinary_income', 'other_capital_gain', 'charitable_giving', 'tax_credits', 'opportunity_zone', 'rate_of_return', 'cash_needed', 'estimated_future_income'], 'number'],
            [['filing_status', 'date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Calculations::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'business_sale_price' => $this->business_sale_price,
            'owner_basis' => $this->owner_basis,
            'ordinary_gain' => $this->ordinary_gain,
            'other_ordinary_income' => $this->other_ordinary_income,
            'other_capital_gain' => $this->other_capital_gain,
            'charitable_giving' => $this->charitable_giving,
            'tax_credits' => $this->tax_credits,
            'opportunity_zone' => $this->opportunity_zone,
            'rate_of_return' => $this->rate_of_return,
            'cash_needed' => $this->cash_needed,
            'age' => $this->age,
            'estimated_future_income' => $this->estimated_future_income,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'filing_status', $this->filing_status]);

        return $dataProvider;
    }
}
