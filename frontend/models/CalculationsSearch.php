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
            [['id', 'user_id', 'years_of_investment', 'annual_return_rate', 'market_history'], 'integer'],
            [['reference_id'], 'safe'],
            [['current_value', 'annual_withdrawal', 'management_fee'], 'number'],
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
            'current_value' => $this->current_value,
            'years_of_investment' => $this->years_of_investment,
            'annual_return_rate' => $this->annual_return_rate,
            'annual_withdrawal' => $this->annual_withdrawal,
            'management_fee' => $this->management_fee,
            'market_history' => $this->market_history,
        ]);

        $query->andFilterWhere(['like', 'reference_id', $this->reference_id]);

        return $dataProvider;
    }
}
