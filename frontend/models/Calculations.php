<?php

namespace frontend\models;
use backend\models\User;
use Yii;

/**
 * This is the model class for table "calculations".
 *
 * @property int $id
 * @property int $user_id
 * @property float $business_sale_price
 * @property float $owner_basis
 * @property float $ordinary_gain
 * @property float $other_ordinary_income
 * @property float $other_capital_gain
 * @property float $charitable_giving
 * @property float $tax_credits
 * @property float $opportunity_zone
 * @property float $rate_of_return
 * @property float $cash_needed
 * @property int $age
 * @property string $filing_status
 * @property float $estimated_future_income
 * @property string $date
 *
 * @property User $user
 */
class Calculations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calculations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'filing_status'], 'required'],
            [['user_id', 'age'], 'integer'],
            [['business_sale_price', 'owner_basis', 'ordinary_gain', 'other_ordinary_income', 'other_capital_gain', 'charitable_giving', 'tax_credits', 'opportunity_zone', 'rate_of_return', 'cash_needed', 'estimated_future_income'], 'number'],
            [['date'], 'safe'],
            [['filing_status'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'business_sale_price' => 'Business Sale Price',
            'owner_basis' => 'Owner Basis',
            'ordinary_gain' => 'Ordinary Gain',
            'other_ordinary_income' => 'Other Ordinary Income',
            'other_capital_gain' => 'Other Capital Gain',
            'charitable_giving' => 'Charitable Giving',
            'tax_credits' => 'Tax Credits',
            'opportunity_zone' => 'Opportunity Zone',
            'rate_of_return' => 'Rate Of Return',
            'cash_needed' => 'Cash Needed',
            'age' => 'Age',
            'filing_status' => 'Filing Status',
            'estimated_future_income' => 'Estimated Future Income',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
