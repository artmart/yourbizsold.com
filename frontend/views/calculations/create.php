<?php
use yii\helpers\Html;

$this->title = 'Calculator';
$this->params['breadcrumbs'][] = ['label' => 'Calculations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calculations-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr />
    <?php // echo $this->render('_form', ['model' => $model]) ?>
</div>