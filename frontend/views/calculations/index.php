<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;

use yii\widgets\ListView;
use yii\widgets\Pjax;
use frontend\models\Calculations;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calculations-index">
<div class="container d-inline-block">

<?php //Html::img('/img/dashboard.png', ['alt' => '', 'height'=>'400px']);?>

    <h1 class="d-flex justify-content-center"><?= Html::encode($this->title) ?></h1>

</div>
<hr />
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<h4 class="float-left">Recent Activity</h4>
<table class="table table-striped table-responsive-xl">
  <thead>
    <tr class="table-success">
      <th scope="col">Name</th>
      <th scope="col">Date</th>
    </tr>
  </thead>
  <tbody>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        
  /*  'itemView' => function ($model, $key, $index, $widget) {
        //return $this->render('_list_item',['model' => $model]);

        // or just do some echo
         return $model->reference_id;
    },
    */
    'itemView' => '_item',
    'pager' => [
        'firstPageLabel' => 'first',
        'lastPageLabel' => 'last',
        'nextPageLabel' => 'next',
        'prevPageLabel' => 'previous',
        'maxButtonCount' => 3,
    ],
    'layout' =>"{items}\n{pager}", //\n{summary}
        //'filterModel' => $searchModel,
        //'tableOptions' => ['class' => 'table table-striped table-success table-responsive-xl'],
       /* 'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'user_id',
            'reference_id',
            //'current_value',
            //'years_of_investment',
            //'annual_return_rate',
            //'annual_withdrawal',
            //'management_fee',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Calculations $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
               //  'template' => '{update} {delete}',
            ],
        ],*/
    ]); ?>
  </tbody>
</table>
    <?php Pjax::end(); ?>
</div>
<hr />
<p class="d-flex justify-content-center">
    <?= Html::a('New Calculation', ['create'], ['class' => 'btn btn-green']) ?>
</p>