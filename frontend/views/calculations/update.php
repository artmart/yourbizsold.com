<?php
use yii\helpers\Html;

$this->title = 'Calculation: ' . $model->reference_id;
$this->params['breadcrumbs'][] = ['label' => 'Saved Calculations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reference_id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="calculations-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr />
    <?= $this->render('_form', ['model' => $model]) ?>
</div>