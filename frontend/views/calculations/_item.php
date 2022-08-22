<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>  
<tr>
  <th scope="row"><?= Html::a(Html::encode($model->reference_id), Url::toRoute(['calculations/update', 'id' => $model->id]), ['title' => $model->reference_id]); ?></th>
  <td><?= date_format(date_create($model->date),"d M Y");?></td>
</tr>