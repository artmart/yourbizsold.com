<?php
$this->title = 'Admin Dashboard';

//$user_id = Yii::$app->user->id;
//$sql = "SELECT id, firstname, lastname from user where user_group in ('2', '3')";
//$users = Yii::$app->db->createCommand($sql)->queryAll();
?>
<div class="site-index">
    <div class="body-content">

        <div class="row1">
         <br />
        <h2>Admin Dashboard</h2>   
        <hr />
                  
        <form id="results_form">
        <div class="row">
        <div class="col-lg-3">
        <div class="form-group">
        <!--<input type="text" name="day" id="day" value="" class="form-control">
        <input type="hidden" id="dayformated" name="dayformated" value="">-->
        </div>
        </div>

        
        <!--  <div class="col-lg-2">
          <button type="submit" class="btn btn-primary" onclick="results()">Filter</button> 
          </div>-->
        </div>
        </form>  
        
        <hr />
        <div id="wait" style="display:none; z-index: 1000;" class="justify-content-center align-items-center"> <img src='/img/ajaxloader.gif'/> Loading...</div>
        <div class="row"><div id="results" style="width: 100%;"></div></div>          
                 
        </div>

    </div>
</div>

<script>


$("#results_form").submit(function(){return false;});
	
function results(){

//var atLeastOneIsChecked = $('.check1:checked').length; 
//if(atLeastOneIsChecked==0){
 //   alert('Please select at least an one checkbox.');
//}else{

var data = $("#results_form").serialize();

$.ajax({
		type: 'post',
		url: '/admin/site/results',
		data: data,
        beforeSend: function(){$("#wait").css("display", "block");},
		success: function (response){
		     $("#wait").css("display", "none");
		     $( '#results' ).html(response);
             $('html,body').animate({scrollTop: $("#results").offset().top},'slow');
             //setTimeout( "$('#results').hide();", 4000);
		}
    }); 
}
</script>