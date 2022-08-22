<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\CalculationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calculations-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'reference_id') ?>

    <?= $form->field($model, 'current_value') ?>

    <?= $form->field($model, 'years_of_investment') ?>

    <?php // echo $form->field($model, 'annual_return_rate') ?>

    <?php // echo $form->field($model, 'annual_withdrawal') ?>

    <?php // echo $form->field($model, 'management_fee') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
