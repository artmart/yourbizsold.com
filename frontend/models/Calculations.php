<?php

namespace frontend\models;

use Yii;
use backend\models\User;

/**
 * This is the model class for table "calculations".
 *
 * @property int $id
 * @property int $user_id
 * @property string $reference_id
 * @property float $current_value
 * @property int $years_of_investment
 * @property int $annual_return_rate
 * @property float $annual_withdrawal
 * @property float $management_fee
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
            [['user_id', 'reference_id', 'date'], 'required'],
            [['user_id', 'years_of_investment',  'market_history'], 'integer'],
            [['current_value', 'annual_withdrawal', 'management_fee', 'annual_return_rate'], 'number'],
            [['reference_id'], 'string', 'max' => 255],
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
            'reference_id' => 'Name', // 'Reference ID',
            'current_value' => 'Current Value',
            'years_of_investment' => 'Years Of Investment',
            'annual_return_rate' => 'Annual Return Rate',
            'annual_withdrawal' => 'Annual Withdrawal',
            'management_fee' => 'Management Fee',
            'date' => 'Date',
            'market_history' => 'S&P 500'
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
