<?php
$day = $_REQUEST['dayformated'];

/*
$user_id = $_REQUEST['user'];

$sql = "SELECT t.*, tr.user_id, tr.timestamp, tr.response FROM tasks t
        Inner JOIN 
        (SELECT * FROM task_responses WHERE user_id = '$user_id' AND  DATE_FORMAT(TIMESTAMP, '%Y-%m-%d') = DATE_FORMAT('$day', '%Y-%m-%d')) tr ON tr.task_id = t.id
        WHERE t.`status` = 1";
*/ 

       
$sql = "SELECT distinct t.id, t.task_group, t.task, tr.response, tr.note, us.usr FROM tasks t 
        Inner JOIN task_responses tr ON tr.task_id = t.id 
        INNER JOIN 
        (
            SELECT tr2.task_id, GROUP_CONCAT(distinct CONCAT(u.firstname, ' ', u.lastname)) usr FROM user u
            INNER JOIN task_responses tr2 ON tr2.user_id = u.id
            WHERE DATE_FORMAT(tr2.TIMESTAMP, '%Y-%m-%d') = DATE_FORMAT('$day', '%Y-%m-%d')
            GROUP BY tr2.task_id
        ) us ON us.task_id = tr.task_id
        
        WHERE DATE_FORMAT(tr.TIMESTAMP, '%Y-%m-%d') = DATE_FORMAT('$day', '%Y-%m-%d')
        And t.`status` = 1";
        
$tasks = Yii::$app->db->createCommand($sql)->queryAll();
if(count($tasks)>0){
?>
<div class="row1">

 <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="preopening-tab" data-toggle="tab" href="#preopening" role="tab" aria-controls="preopening" aria-selected="true">Pre Opening</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="preptab-tab" data-toggle="tab" href="#preptab" role="tab" aria-controls="preptab" aria-selected="false">Prep</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="closing-tab" data-toggle="tab" href="#closing" role="tab" aria-controls="closing" aria-selected="false">Closing</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="preopening" role="tabpanel" aria-labelledby="preopening-tab">
  
    <br />
    <h2>Pre Opening</h2>     

    <table class="table table-sm">
      <thead>
        <tr><th>TASK</th><th>USERs</th></tr>
      </thead>
      <tbody>
            <?php
                foreach($tasks as $task){
                    if($task['task_group'] == '0'){ 
            ?>
            <tr>
              <td><i class="fa fa-check" aria-hidden="true"></i> <?=$task['task']?></td>
              <td><?=$task['usr']?></td>
            </tr> 
            <?php } } ?>
      </tbody>
    </table>

  </div>
  <div class="tab-pane fade" id="preptab" role="tabpanel" aria-labelledby="preptab-tab">
        <br />
        <h2>Prep</h2>   

        <table class="table table-sm">
          <thead><tr><th>TASK</th><th>USERs</th></tr></thead>
          <tbody>
                <?php
                    foreach($tasks as $task){
                        if($task['task_group'] == '1'){ 
                ?>
                <tr>
                  <td><i class="fa fa-check" aria-hidden="true"></i> <?=$task['task']?></td>
                  <td><?=$task['usr']?></td>
                </tr> 
                <?php } } ?>
          </tbody>
        </table>
  
  </div>
  <div class="tab-pane fade" id="closing" role="tabpanel" aria-labelledby="closing-tab">
        <br />
        <h2>Closing</h2>   
        
        <table class="table table-sm">
          <thead><tr><th>TASK</th><th>USERs</th></tr></thead>
          <tbody>
                <?php
                    foreach($tasks as $task){
                        if($task['task_group'] == '2'){ 
                ?>
                <tr>
                  <td><i class="fa fa-check" aria-hidden="true"></i> <?=$task['task']?></td>
                  <td><?=$task['usr']?></td>
                </tr> 
                <?php } } ?>
          </tbody>
        </table>
          
  </div>
</div>
           
</div>
<?php }else{ 
    	echo "<center><img style='height: 100%;' src='".Yii::getAlias('/img/nodata.png')."'/></center>";
} ?>