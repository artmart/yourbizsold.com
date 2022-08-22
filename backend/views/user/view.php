<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->firstname . " " . $model->lastname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$statuses = ['10' => 'Active', '9' => 'Inactive'];
$usergroups = ['1' => 'Administrator', '2' => 'Staff', '3' => 'Manager'];
?>
<div class="user-view">
<div class="row clearfix">
    <h1 class="col-sm-9"><?= Html::encode($this->title) ?></h1>
    <p class="col-sm-3 d-flex justify-content-end">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
<hr />
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'firstname',
            'lastname',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            'email:email',
            //'status',
            ['attribute' => 'status', 'value' =>  $statuses[$model->status]],
            ['attribute' => 'user_group', 'value' =>  $usergroups[$model->user_group]],
            'created_at:date',
            //'updated_at',
            //'verification_token',
        ],
    ]) ?>
</div>