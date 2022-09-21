<link rel="stylesheet" type="text/css" href="/vendor/datatables/css/jquery.dataTables.min.css">
<style>
th, td {
  text-align: center;
}
</style>
<script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>

<?php

//var_dump($_REQUEST['Calculations']);
//exit;

$user_id = $_REQUEST['Calculations']['user_id'];

$business_sale_price = $_REQUEST['Calculations']['business_sale_price'];
$owner_basis = $_REQUEST['Calculations']['owner_basis'];
$ordinary_gain = $_REQUEST['Calculations']['ordinary_gain'];
$other_ordinary_income = $_REQUEST['Calculations']['other_ordinary_income'];
$other_capital_gain = $_REQUEST['Calculations']['other_capital_gain'];
$charitable_giving = $_REQUEST['Calculations']['charitable_giving'];
$tax_credits = $_REQUEST['Calculations']['tax_credits'];
$opportunity_zone = $_REQUEST['Calculations']['opportunity_zone'];
$rate_of_return = $_REQUEST['Calculations']['rate_of_return'];
$cash_needed = $_REQUEST['Calculations']['cash_needed'];
$age = $_REQUEST['Calculations']['age'];
$filing_status = $_REQUEST['Calculations']['filing_status'];
$estimated_future_income = $_REQUEST['Calculations']['estimated_future_income'];


$ordinary_tax_rate_based_on_filing_status_and_amount_of_first_part_of_this_equation = 1;
$capital_gain_tax_rate_based_on_filing_status = 1;


$ordinary = ($ordinary_gain+$other_ordinary_income)*$ordinary_tax_rate_based_on_filing_status_and_amount_of_first_part_of_this_equation;
$capital_gain = ($business_sale_price-$ordinary_gain+$other_capital_gain)*$capital_gain_tax_rate_based_on_filing_status;

$total = $ordinary + $capital_gain;

$table2JsonData = [];


for($y = 1; $y<=20; $y++){
    
    $distribution_20 = $business_sale_price/20;
    $amount_nvested_20[$y] = ($y==1)?$business_sale_price:($amount_nvested_20[$y-1]-$distribution_20)*(1+$rate_of_return/100);
    
    if($y<=10){
    $distribution_10 = $business_sale_price/10;
    $amount_nvested_10[$y] = ($y==1)?$business_sale_price:($amount_nvested_10[$y-1]-$distribution_10)*(1+$rate_of_return/100);
    }else{
        $distribution_10 = 0;
        $amount_nvested_10[$y] = 0;
    }
    
    $table2JsonData[] = [$y, number_format($amount_nvested_20[$y], 2), number_format($distribution_20, 2), '', number_format($amount_nvested_10[$y], 2), number_format($distribution_10, 2)];
}
?>

        

        


<hr />
<div class="card bg-light mb-3">
  <div class="card-body">

<h5>Current Tax Owed</h5>
<div class="table-responsive">
    <table class="table table-sm table-hover table-striped" style="width: 100% !important;">            
    <tbody>
    <tr>
       <th scope="row">Ordinary</th><td><?= number_format($ordinary, 2) ?></td>
    </tr>
    <tr>
       <th scope="row">Capital Gain</th><td><?= number_format($capital_gain, 2) ?></td>
    </tr>
    <tr>
       <th scope="row">Total</th><td><?= number_format($total, 2) ?></td>
    </tr>
    </tbody>
    </table>
</div>

<h5>Deferred Sales Trust</h5>
<div class="table-responsive">
    <table id="calculations" class="table table-sm table-hover table-striped display" style="width: 100% !important;">
        <thead>
        <tr>
          <th></th>
          <th>20 Years</th>
          <th></th>
          <th></th>
          <th>10 Years</th>
          <th></th>
        </tr>
        <tr>
          <th>Year</th>
          <th>Amount Invested in Trust</th>
          <th>Distrubition</th>
          <th></th>
          <th>Amount Invested in Trust</th>
          <th>Distrubition</th>
        </tr>
        </thead>
    <tbody>
    </tbody>
    </table>
</div>
    
    
    
    
    
  </div>
</div>

<script>
$(document).ready(function(){
    $('#calculations').DataTable({
        data: <?php echo json_encode($table2JsonData); ?>,
        responsive: true,
        "ordering": false,
        "paging": false,
        "searching": false,
        "info": false,
        //"processing": true,
        //"serverSide": true,
       // "ajax": "/site/homechartajax"
    });
} );

</script>