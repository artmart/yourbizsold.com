<?php
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Signup';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
<div class="mt-5 offset-lg-3 col-lg-6">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'firstname')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'lastname')->textInput(/*['autofocus' => true]*/) ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>            
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-4">{image}</div><div class="col-lg-8">{input}</div></div>',
                ]) ?>

                <div class="form-group">
                <div class="row">
                 <div class="col-lg-3">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                 </div>
                </div>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>


