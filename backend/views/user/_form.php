<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="user-form">
<div class="mt-5 offset-lg-3 col-lg-6">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'status')->dropDownList(['10' => 'Active', '9' =>'Inactive'] /*, ['prompt'=>'- Select -']*/); ?>
    <?= $form->field($model, 'user_group')->dropDownList(['1' => 'Administrator', '2' => 'User'] /*, ['prompt'=>'- Select -']*/); ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>