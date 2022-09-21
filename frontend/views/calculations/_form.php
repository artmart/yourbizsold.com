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

       //  $data = Yii::$app->getDb()->createCommand($sql)->queryAll();

    $years = "<option value=''>-- Select --</option>"; 
 //   foreach($data as $y){
    //for($c=2000; $c<=2022; $c++){
  //      $years .= '<option value='.$y['year'].' selected="selected">'.$y['year'].'</option>';
  //  }

    if($model->isNewRecord){$user_id = Yii::$app->user->id;}else{$user_id = $model->user_id;}
    echo $form->field($model, 'user_id')->hiddenInput(['value'=>$user_id])->label(false); 
    
  //  echo $form->field($model, 'market_history')->hiddenInput(['value'=>0])->label(false); 
    
    
 
    
    ?>
<div class="row">
<div class="col-md-12"> 
<h5>Business Information</h5>
<hr />
    <div class="row">
        <div class="col-md-2">    
        <?= $form->field($model, 'business_sale_price')->textInput() ?>
        </div>
        <div class="col-md-2">  
        <?= $form->field($model, 'owner_basis')->textInput() ?>
        </div>
        <div class="col-md-2">  
        <?= $form->field($model, 'ordinary_gain')->textInput() ?>
        </div>
        <div class="col-md-3">  
        <?= $form->field($model, 'other_ordinary_income')->textInput() ?>
        </div>
        <div class="col-md-2">  
        <?= $form->field($model, 'other_capital_gain')->textInput() ?>
        </div>
    </div>
<hr />
<h5>Strategy Information</h5>
<hr />
    <div class="row">    
        <div class="col-md-2">  
        <?= $form->field($model, 'charitable_giving')->textInput() ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'tax_credits')->textInput() ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'opportunity_zone')->textInput() ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'rate_of_return')->textInput() ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'cash_needed')->textInput() ?>
        </div>
    </div>
<hr />
<h5>Owner Information</h5>
<hr />
    <div class="row">    
        <div class="col-md-4">  
        <?= $form->field($model, 'age')->textInput() ?>
        </div>
        <div class="col-md-4">
        <?= $form->field($model, 'filing_status')->textInput() ?>
        </div>
        <div class="col-md-4">
        <?= $form->field($model, 'estimated_future_income')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
        <div class="form-group">
            <?= Html::submitButton('CALCULATE', ['class' => 'btn btn-green w-100', 'onClick'=>'calculate()']) ?>
        </div>
        </div>
        <div class="col-md-4">
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-green w-100', 'onClick'=>'save()']) ?>
        </div>
        </div>
        
        <div class="col-md-4">
        <div class="form-group">
            <?= Html::submitButton('PDF', ['class' => 'btn btn-green w-100', 'onClick'=>'pdf()']) ?>
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


function pdf(){
    
    $('#calculations-reference_id').val('demo');
    
    $('#calculations-current_value').val(100000);
    $('#calculations-years_of_investment').val(10);
    $('#calculations-annual_return_rate').val(5);
    $('#calculations-annual_withdrawal').val(4000);
    $('#calculations-management_fee').val(1);
    
    calculate();
    
}

</script>