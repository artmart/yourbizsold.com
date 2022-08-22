<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="card bg-light mb-3">
  <div class="card-body">



<div class="calculations-form">

    <?php $form = ActiveForm::begin(); ?>

<?php 
$sql = "select * from sp500_rates r order by year"; 

      //WHERE `RETURN`<>0 AND january<>0 AND february<>0 AND march<>0 AND april<>0 AND may<>0 AND june<>0 
        //        AND july<>0 AND august<>0 AND september<>0 AND october<>0 AND november<>0 AND december<>0

         $data = Yii::$app->getDb()->createCommand($sql)->queryAll();

    $years = "<option value=''>-- Select --</option>"; 
    foreach($data as $y){
    //for($c=2000; $c<=2022; $c++){
        $years .= '<option value='.$y['year'].' selected="selected">'.$y['year'].'</option>';
    }

    // $form->field($model, 'user_id')->textInput() 
    if($model->isNewRecord){$user_id = Yii::$app->user->id;}else{$user_id = $model->user_id;}
    echo $form->field($model, 'user_id')->hiddenInput(['value'=>$user_id])->label(false); 
    
    echo $form->field($model, 'market_history')->hiddenInput(['value'=>0])->label(false); 
    
    ?>
<div class="row">
<div class="col-md-12"> 
    <div class="row">
        <div class="col-md-9">    
        <?= $form->field($model, 'reference_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">  
        <?= $form->field($model, 'current_value')->textInput() ?>
        </div>
    </div>
    <div class="row">    
        <div class="col-md-3">  
        <?= $form->field($model, 'years_of_investment')->textInput() ?>
        </div>
        <div class="col-md-3">
        <?= $form->field($model, 'annual_return_rate')->textInput() ?>
        </div>
        <div class="col-md-3">
        <?= $form->field($model, 'annual_withdrawal')->textInput() ?>
        </div>
        <div class="col-md-3">
        <?= $form->field($model, 'management_fee')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton('CALCULATE', ['class' => 'btn btn-green w-100', 'onClick'=>'calculate()']) ?>
        </div>
        </div>
        <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-green w-100', 'onClick'=>'save()']) ?>
        </div>
        </div>
        
        <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton('Demo', ['class' => 'btn btn-green w-100', 'onClick'=>'demo()']) ?>
        </div>
        </div>
        
        <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton('S&P 500', ['class' => 'btn btn-green w-100', 'onClick'=>'sandp()']) ?>
        </div>
        </div>
        
    </div>

<div class="card bg-light mb-3" id="years">
  <div class="card-body">
    <div class="row">
    <div class="col-md-6">Historical S&P 500 Return</div>
    <div class="form-group row col-md-6">
        <div class="col-md-2">From</div>
        <div class="col-md-4"> 
            <select class="form-control" name="from" id="from" onchange ="sandp()">
              <?php echo $years; ?>
            </select>
        </div>
        <div class="control-label col-sm-2">To</div>
        <div class="col-md-4">
            <select class="form-control" name="to" id="to" onchange ="sandp()">
              <?php echo $years; ?>
            </select>
        </div>
    </div>
    </div>
</div>
</div>    
     
</div>
</div>
    <?php ActiveForm::end(); ?>
</div>

  </div>
</div>




  
<div id="wait" style="display:none; z-index: 1000;" class="justify-content-center align-items-center"><img src='/img/ajaxloader.gif'/> Loading...</div>
<div class="row1"><div id="results" style="width: 100%;"></div></div>


<script>
$('#years').hide(); 

document.getElementById("w0").addEventListener("click", function(event){event.preventDefault()});
$('form#w0').submit(false);
function save(){$('form#w0').submit();}

function calculate(){
    var data = $("#w0").serialize();
    
    $.ajax({
		type: 'post',
		url: '/calculations/calculate',
		data: data,
        beforeSend: function() {
           $("#wait").css("display", "block");               
          },
		success: function (response){
		     $("#wait").css("display", "none");
		     $( '#results' ).html(response);
             //alert('Data saved successfully.');
             //$('html,body').animate({scrollTop: $("#results0").offset().top},'slow');
             //setTimeout( "$('#results0').hide();", 3000);
            // window.location.href = "/";
		}
    }); 
}


function demo(){
    
    $('#calculations-reference_id').val('demo');
    
    $('#calculations-current_value').val(100000);
    $('#calculations-years_of_investment').val(10);
    $('#calculations-annual_return_rate').val(5);
    $('#calculations-annual_withdrawal').val(4000);
    $('#calculations-management_fee').val(1);
    
    calculate();
    
}

function sandp(){
    $('#years').show();
    var years  = $('#to').val() - $('#from').val();
    if(years>0){
    
    $('#calculations-years_of_investment').val(years);
    }
    $('#calculations-market_history').val(1); 
    calculate();
    $('#calculations-market_history').val(0); 
}

function sandp1(){
    $('#years').show(); 
    //$('#years').val(0); 
    var years  = $('#to').val() - $('#from').val();
    $('#calculations-years_of_investment').val(years);
    
}



</script>