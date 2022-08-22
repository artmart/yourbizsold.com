<link rel="stylesheet" type="text/css" href="/vendor/datatables/css/jquery.dataTables.min.css">
<style>
th, td {
  text-align: center;
}
</style>
<script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>

<?php
    function calculateMarketHistory($calculation, $years){
        $table2JsonData = [];
        //reset worth to original income so we can subtract freely
        $total['worth'] = $calculation['value'];
        $total['remaining'] = $calculation['value'];

        //reset statistics values to ensure it's always recalculated
        $statistics['averageReturn'] = 0;
        //default value is 1 because 0? anything is 0 ;)
        $statistics['geometricAverage'] = 1;
        $statistics['actualAnnualizedYield'] = 0;
        $statistics['finalNetValue'] = 0;
        $statistics['totalAnnualizedYield'] = 0;
        $statistics['earnedIncome'] = $calculation['income']; // = 0;
        $statistics['lowestPrincipal'] = $calculation['value'];

        //iterate over years, workout monthly math and reduce total worth
        //this.years.forEach((year, index) => {
        foreach($years as $index=>$year){
          $year['geometricAverage'] = (1 + ($year['return'] / 100));
          $statistics['earnedIncome'] += ($years[($index == 0 ? 0 : $index -1)]['eoy'] < $years[($index == 0 ? 0 : $index -1)]['income'] ? $years[($index == 0 ? 0 : $index -1)]['eoy'] : $year['income']);
          $year['charges'] = 0;
          $year['fees'] = 0;
          $year['boy'] = 0;
          $year['eoy'] = 0;
          $year['toggled'] = false;

          // calculate statistics
          $statistics['averageReturn'] += $year['return'];
          $statistics['geometricAverage'] *= $year['geometricAverage'];
           // echo $year['return']."<br/>";
          //year.months.forEach((month) => {
          $k=0;
          foreach($year['months'] as $month){
          //for($j = 1; $j<13; $j++){
          //$month = [];
          //$month['rate'] = $year['return'] / 12;
          //$month['month'] = $j;
            
            $calculatedValue = ((1 + $month['rate'] / 100) * $total['worth']) - $year['income'] / 12;
            $charge = $month['rate'] / 100 * $calculatedValue;
            $globalFee = $calculation['fee'] / 12 / 100;
            $remaining = $calculatedValue - ($globalFee * $calculatedValue);
            $monthlyCharge = $globalFee * $calculatedValue;
            $month['income'] = $year['income'] / 12;
            $month['fee'] = $globalFee;
            //$month['value'] = $calculatedValue;
            $month['charge'] = ($monthlyCharge < 0 ? 0 : $monthlyCharge);
            $month['remaining'] = $remaining;
            $statistics['lowestPrincipal'] = ($remaining < $statistics['lowestPrincipal'] ? $remaining : $statistics['lowestPrincipal']);
            if($statistics['lowestPrincipal'] < 0) {
              $statistics['lowestPrincipal'] = 0;
            }
            $total['worth'] = $remaining;
            $total['remaining'] -= $remaining;
            $year['charges'] += $month['charge'];// floatval($month['charge']); // month.charge.toFixed(4)
            $year['months'][$k] = $month;
            $k++;
          }
          $years[$index] = $year;

          $year['fees'] = $year['fees'] + $year['charges'] + ($index>0 && $years[$index - 1] ? $years[$index - 1]['fees'] : 0);
          //echo '<br/>';
          
          //last iteration will assign final net value
          $year['boy'] = ($index>0 && $years[$index - 1] ? $years[$index - 1]['months'][11]['remaining'] : $calculation['value']);
          //if(isset($years[$index]['months'][11]['remaining'])){
            $year['eoy'] = $statistics['actualAnnualizedYield'] = $years[$index]['months'][11]['remaining'];
          //}//else{
           // $year['eoy'] = $statistics['actualAnnualizedYield'] = 0;
          //}
//exit;
          $statistics['totalAnnualizedYield'] = $year['eoy'];
          $statistics['finalNetValue'] = $year['eoy'];
                        
          if($year['eoy'] < 0){$year['eoy'] = $statistics['actualAnnualizedYield'] = 0;}
          if($year['boy'] < 0){$year['boy'] = 0;}
          
          $years[$index] = $year;
          

          
          $table2JsonData[] = [$year['year'], number_format($year['return'], 2).'%', '$'.number_format($year['income']), '$'.number_format($year['charges'], 2), '$'.number_format($year['boy'], 2), '$'.number_format($year['eoy'], 2), '$'.number_format($year['fees'], 2)]; 
        }
        
        //$statistics['averageReturn'] -= 12.917680515085073;
        //var_dump($years);
        //var_dump($statistics);

        calculateStatistics($statistics, $calculation, $table2JsonData);
      }

////////////////////////////////////////////////////////////////////////////
      
     function calculateMonths($calculation, $years) {
        $table2JsonData = [];
        //reset worth to original income so we can subtract freely
        $total['worth'] = $calculation['value'];
        $total['remaining'] = $calculation['value'];

        //reset statistics values to ensure it's always recalculated
        $statistics['averageReturn'] = 0;
        //default value is 1 because 0? anything is 0 ;)
        $statistics['geometricAverage'] = 1;
        $statistics['actualAnnualizedYield'] = 0;
        $statistics['finalNetValue'] = 0;
        $statistics['totalAnnualizedYield'] = 0;
        $statistics['earnedIncome'] = $calculation['income'];
        $statistics['lowestPrincipal'] = $calculation['value'];

        //iterate over years, workout monthly math and reduce total worth
        foreach($years as $index=>$year){
          $year['geometricAverage'] = (1 + ($year['return'] / 100));
          $statistics['earnedIncome'] += ($years[($index == 0 ? 0 : $index -1)]['eoy'] < $years[($index == 0 ? 0 : $index -1)]['income'] ? $years[($index == 0 ? 0 : $index -1)]['eoy'] : $year['income']);
          $year['charges'] = 0;
          $year['fees'] = 0;
          $year['boy'] = 0;
          $year['eoy'] = 0;
          $year['toggled'] = false;
          $year['months'] = [];
          $previousRemaining = ( ($index==0||!$years[$index - 1]) ? $calculation['value'] : $years[$index - 1]['months'][11]['remaining']);//?????
          $fees = 0;

          //calculate statistics
          $statistics['averageReturn'] += $year['return'];
          $statistics['geometricAverage'] *= $year['geometricAverage'];

          //dynamic monthly values
          for($j = 1; $j<13; $j++){
            $month = [];
            $calculatedValue = ((1 + $year['return'] / 12 / 100) * $total['worth']) - $year['income'] / 12;
            $charge = $calculation['fee'] / 12 / 100 * $calculatedValue;
            $globalFee = $calculation['fee'] / 12 / 100;
            $remaining = ($calculatedValue - ($globalFee * $calculatedValue) < 0 ? 0 : $calculatedValue - ($globalFee * $calculatedValue));
            $monthlyCharge = $globalFee * $calculatedValue;

            $month['month'] = $j;
            $month['income'] = $year['income'] / 12;
            $month['fee'] = $globalFee;
            $month['interest'] = $year['return'] / 12 / 100;
            $month['value'] = $calculatedValue;
            $month['charge'] = ($monthlyCharge < 0 ? 0 : $monthlyCharge);
            $month['remaining'] = $remaining;

            $fees += $month['charge'];

            $month['previousRemaining'] = $previousRemaining;
            $month['fees'] = $fees;
            $statistics['lowestPrincipal'] = ($remaining < $statistics['lowestPrincipal'] ? $remaining : $statistics['lowestPrincipal']);
            if($statistics['lowestPrincipal']< 0){
              $statistics['lowestPrincipal'] = 0;
            }
            $total['worth'] = $remaining;
            $total['remaining'] -= $remaining;
            $previousRemaining = $month['remaining'];

            $year['months'][] = $month; // .push(month)
          }
          $years[$index] = $year;
          //var_dump($years[$index]['months'][11]['remaining']);
          //exit;
          //$years['months'][] = $year;

          foreach($year['months'] as $month){
            $year['charges'] += floatval($month['charge']);
          }

          $year['boy'] = (($index>0 && $years[$index - 1]) ? $years[$index - 1]['months'][11]['remaining'] : $calculation['value']); //??
          $year['fees'] += $year['charges'] + (($index>0 && $years[$index - 1]) ? $years[$index - 1]['fees'] : null);
          //last iteration will assign final net value
          
          //$aa = $years[$index]['months'];
          //var_dump($aa);
          //exit;
          $year['eoy'] = $statistics['actualAnnualizedYield'] = $years[$index]['months'][11]['remaining']; // ??

          if($year['eoy']< 0){
            $year['eoy'] = $statistics['actualAnnualizedYield'] = 0;
          }

          if($year['boy']<0){
            $year['boy'] = 0;
          }
           $years[$index] = $year;

           $table2JsonData[] = [$year['year'], number_format($year['return'], 2).'%', '$'.number_format($year['income']), '$'.number_format($year['charges'], 2), '$'.number_format($year['boy'], 2), '$'.number_format($year['eoy'], 2), '$'.number_format($year['fees'], 2)]; 
                        

          $statistics['totalAnnualizedYield'] = $year['eoy'];
          $statistics['finalNetValue'] = $year['eoy'];
        }

        calculateStatistics($statistics, $calculation, $table2JsonData);
        //return $table2JsonData;
        
    
      }
   
       
function calculateStatistics($statistics, $calculation, $table2JsonData){
        $statistics['averageReturn'] = $statistics['averageReturn'] / $calculation['years'];
        // javascript does not support "y square root of x", but supports a second argument in "x to the power of y"
        $statistics['geometricAverage'] = (pow($statistics['geometricAverage'], (1 / $calculation['years'])) - 1) * 100;
        $statistics['actualAnnualizedYield'] = (pow($statistics['actualAnnualizedYield'] / $calculation['value'], (1 / $calculation['years'])) - 1) * 100;
        $statistics['totalAnnualizedYield'] = (pow(($statistics['totalAnnualizedYield'] + $statistics['earnedIncome']) / $calculation['value'], (1 / $calculation['years'])) - 1) * 100;
        $statistics['finalNetValue'] = $statistics['finalNetValue'] + $statistics['earnedIncome'];
        
?>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped" style="width: 100% !important;">            
            <tbody>
            <tr>
               <th scope="row">Average Returns Less Fees</th><td><?= number_format($statistics['geometricAverage'], 2) ?></td>
               <th scope="row">Lowest Account Value</th><td><?= number_format($statistics['lowestPrincipal'], 2) ?></td>
            </tr>
            <tr>
               <th scope="row">Annualized Return on Principal</th><td><?= number_format($statistics['actualAnnualizedYield'], 2) ?></td>
               <th scope="row">Annualized Return on Economic Output </th><td><?= number_format($statistics['totalAnnualizedYield'], 2) ?></td>
            </tr>
            <tr>
               <th scope="row">Total Portfolio Return</th><td><?= number_format($statistics['finalNetValue'], 2) ?></td>
               <th scope="row">Total Withdrawals</th><td><?= number_format($statistics['earnedIncome'], 2) ?></td>
            </tr>
            </tbody>
            </table>
        </div>
        
        <div class="table-responsive">
            <table id="calculations" class="table table-sm table-hover table-striped display" style="width: 100% !important;">
                <thead>
                <tr>
                  <th scope="col">Year</th>
                  <th scope="col">Annual Return</th>
                  <th scope="col">Annual Withdrawal</th>
                  <th scope="col">Mgmt. Fee</th>
                  <th scope="col">Net Value BOY</th>
                  <th scope="col">Net Value EOY</th>
                  <th scope="col">Cumulative Fees </th>
                </tr>
                </thead>
            <tbody>
            </tbody>
            </table>
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
        
        <?php
        

        //return $statistics;
      }
      
    function  doTheMath($calculation, $showMarketHistory, $marketHistory){
        $years = [];
        // worth is a duplicate of income, so that the value can be manipulated
        $total['worth'] = $calculation['value'];

        $currentYear =  date("Y"); // new Date().getFullYear()
        $yearsRange = $calculation['years'];

/*
        if($showMarketHistory){
          $currentYear = $marketHistory['from'];

          if(!$marketHistory['to']){
            $marketHistory['to'] = $marketHistory['from'];
          }

          if($marketHistory['from'] > $marketHistory['to']){
            $marketHistory['to'] = $marketHistory['from'];
          }

          $yearsRange = $marketHistory['to'] - $marketHistory['from'];
          $calculation['years'] = $yearsRange;
        }
*/
        // TODO - detect existing value and don't overwrite

        if(!$showMarketHistory){
          // years
          for($i = 0; $i < $yearsRange; $i++){
            $year = [];
            $year['year'] = $currentYear + $i;
            $year['return'] = $calculation['return'];
            $year['income'] = $calculation['income'];
            $year['toggled'] = false;
            
            $year['boy'] = 0;
            $year['eoy'] = 0;
            
            $year['geometricAverage'] = 0;
            $year['charges'] = 0;
            $year['fees'] = 0;
            
            $year['months'] = [];
            $years[] = $year; // .push(year)
          }

          calculateMonths($calculation, $years);
        }


        if($showMarketHistory){
          
          //$data =  \backend\models\Sp500rates::find();
          //$from = 2000;
          $to = $currentYear+$yearsRange;
          
         $sql = "select * from sp500_rates r
                WHERE `RETURN`<>0 AND january<>0 AND february<>0 AND march<>0 AND april<>0 AND may<>0 AND june<>0 
                AND july<>0 AND august<>0 AND september<>0 AND october<>0 AND november<>0 AND december<>0
                order by year"; 
         $data = Yii::$app->getDb()->createCommand($sql)->queryAll(); //where year>=$currentYear AND year<=$to 
         //$cnt = count($pages); 
          
          //var_dump($data);
          //exit;  
          //$dd = '[{"year":1999,"months":[{"date":"1999-12-31T00:00:00.000000Z","value":2021.401,"rate":0}],"return":0},{"year":2000,"months":[{"date":"2000-01-31T00:00:00.000000Z","value":1919.841,"rate":-5.02423813978524},{"date":"2000-02-29T00:00:00.000000Z","value":1883.499,"rate":-1.892969261516967},{"date":"2000-03-31T00:00:00.000000Z","value":2067.76,"rate":9.78290936177828},{"date":"2000-04-28T00:00:00.000000Z","value":2005.549,"rate":-3.0086180214338327},{"date":"2000-05-31T00:00:00.000000Z","value":1964.401,"rate":-2.0517075374373803},{"date":"2000-06-30T00:00:00.000000Z","value":2012.83,"rate":2.465331671079369},{"date":"2000-07-31T00:00:00.000000Z","value":1981.361,"rate":-1.563420656488617},{"date":"2000-08-31T00:00:00.000000Z","value":2104.432,"rate":6.211437491703919},{"date":"2000-09-29T00:00:00.000000Z","value":1993.332,"rate":-5.279334281174187},{"date":"2000-10-31T00:00:00.000000Z","value":1984.905,"rate":-0.4227594801066772},{"date":"2000-11-30T00:00:00.000000Z","value":1828.416,"rate":-7.883954143901093},{"date":"2000-12-29T00:00:00.000000Z","value":1837.365,"rate":0.4894400399033998}],"return":-0.0910437859682468},{"year":2001,"months":[{"date":"2001-01-31T00:00:00.000000Z","value":1902.553,"rate":3.5479069210527143},{"date":"2001-02-28T00:00:00.000000Z","value":1729.075,"rate":-9.118169112765855},{"date":"2001-03-30T00:00:00.000000Z","value":1619.537,"rate":-6.335063545537352},{"date":"2001-04-30T00:00:00.000000Z","value":1745.392,"rate":7.771048145241522},{"date":"2001-05-31T00:00:00.000000Z","value":1757.086,"rate":0.6699927580738319},{"date":"2001-06-29T00:00:00.000000Z","value":1714.321,"rate":-2.433859241949463},{"date":"2001-07-31T00:00:00.000000Z","value":1697.445,"rate":-0.9844130708309535},{"date":"2001-08-31T00:00:00.000000Z","value":1591.182,"rate":-6.260173378224323},{"date":"2001-09-28T00:00:00.000000Z","value":1462.69,"rate":-8.075254747728422},{"date":"2001-10-31T00:00:00.000000Z","value":1490.582,"rate":1.9068975654444955},{"date":"2001-11-30T00:00:00.000000Z","value":1604.919,"rate":7.670627982895283},{"date":"2001-12-31T00:00:00.000000Z","value":1618.979,"rate":0.8760566732651256}],"return":-0.11885825625284033},{"year":2002,"months":[{"date":"2002-01-31T00:00:00.000000Z","value":1595.353,"rate":-1.4593147903709536},{"date":"2002-02-28T00:00:00.000000Z","value":1564.586,"rate":-1.9285386995856157},{"date":"2002-03-29T00:00:00.000000Z","value":1623.429,"rate":3.7609310066688693},{"date":"2002-04-30T00:00:00.000000Z","value":1525.004,"rate":-6.062784390324438},{"date":"2002-05-31T00:00:00.000000Z","value":1513.769,"rate":-0.7367193790967121},{"date":"2002-06-28T00:00:00.000000Z","value":1405.943,"rate":-7.123015466692749},{"date":"2002-07-31T00:00:00.000000Z","value":1296.344,"rate":-7.795408490955879},{"date":"2002-08-30T00:00:00.000000Z","value":1304.855,"rate":0.6565386965188225},{"date":"2002-09-30T00:00:00.000000Z","value":1163.044,"rate":-10.867950845113057},{"date":"2002-10-31T00:00:00.000000Z","value":1265.411,"rate":8.801644649729496},{"date":"2002-11-29T00:00:00.000000Z","value":1339.892,"rate":5.885913746600906},{"date":"2002-12-31T00:00:00.000000Z","value":1261.176,"rate":-5.874801849701328}],"return":-0.2210053373144433},{"year":2003,"months":[{"date":"2003-01-31T00:00:00.000000Z","value":1228.138,"rate":-2.6196185147830278},{"date":"2003-02-28T00:00:00.000000Z","value":1209.711,"rate":-1.5004014206872398},{"date":"2003-03-31T00:00:00.000000Z","value":1221.456,"rate":0.9708930480089748},{"date":"2003-04-30T00:00:00.000000Z","value":1322.068,"rate":8.237054793623344},{"date":"2003-05-30T00:00:00.000000Z","value":1391.724,"rate":5.2687153762136205},{"date":"2003-06-30T00:00:00.000000Z","value":1409.478,"rate":1.2756839718220192},{"date":"2003-07-31T00:00:00.000000Z","value":1434.329,"rate":1.763135004590339},{"date":"2003-08-29T00:00:00.000000Z","value":1462.302,"rate":1.9502499077966036},{"date":"2003-09-30T00:00:00.000000Z","value":1446.773,"rate":-1.0619557382811564},{"date":"2003-10-31T00:00:00.000000Z","value":1528.616,"rate":5.656934432699543},{"date":"2003-11-28T00:00:00.000000Z","value":1542.066,"rate":0.8798808857162328},{"date":"2003-12-31T00:00:00.000000Z","value":1622.939,"rate":5.244457759914297}],"return":0.28684576934543643},{"year":2004,"months":[{"date":"2004-01-30T00:00:00.000000Z","value":1652.728,"rate":1.8354972059948125},{"date":"2004-02-27T00:00:00.000000Z","value":1675.7,"rate":1.3899443828627511},{"date":"2004-03-31T00:00:00.000000Z","value":1650.42,"rate":-1.5086232619203912},{"date":"2004-04-30T00:00:00.000000Z","value":1624.511,"rate":-1.5698428278862337},{"date":"2004-05-31T00:00:00.000000Z","value":1646.804,"rate":1.3722898767690879},{"date":"2004-06-30T00:00:00.000000Z","value":1678.826,"rate":1.9444936980964371},{"date":"2004-07-30T00:00:00.000000Z","value":1623.262,"rate":-3.3096937979278493},{"date":"2004-08-31T00:00:00.000000Z","value":1629.828,"rate":0.4044941605236829},{"date":"2004-09-30T00:00:00.000000Z","value":1647.48,"rate":1.0830590712639605},{"date":"2004-10-29T00:00:00.000000Z","value":1672.649,"rate":1.5277271954742986},{"date":"2004-11-30T00:00:00.000000Z","value":1740.327,"rate":4.0461567250511195},{"date":"2004-12-31T00:00:00.000000Z","value":1799.548,"rate":3.4028662429531806}],"return":0.10882047938955186},{"year":2005,"months":[{"date":"2005-01-31T00:00:00.000000Z","value":1755.684,"rate":-2.437500972466424},{"date":"2005-02-28T00:00:00.000000Z","value":1792.631,"rate":2.1044219802652435},{"date":"2005-03-31T00:00:00.000000Z","value":1760.887,"rate":-1.7708050346111577},{"date":"2005-04-29T00:00:00.000000Z","value":1727.49,"rate":-1.8966009743952839},{"date":"2005-05-31T00:00:00.000000Z","value":1782.457,"rate":3.1818997505050675},{"date":"2005-06-30T00:00:00.000000Z","value":1784.987,"rate":0.1419389079231621},{"date":"2005-07-29T00:00:00.000000Z","value":1851.368,"rate":3.7188506134778407},{"date":"2005-08-31T00:00:00.000000Z","value":1834.476,"rate":-0.9124063935424971},{"date":"2005-09-30T00:00:00.000000Z","value":1849.334,"rate":0.8099315553869246},{"date":"2005-10-31T00:00:00.000000Z","value":1818.504,"rate":-1.66708663767605},{"date":"2005-11-30T00:00:00.000000Z","value":1887.284,"rate":3.7822297888814234},{"date":"2005-12-30T00:00:00.000000Z","value":1887.941,"rate":0.03481193079578304}],"return":0.049119556688679615},{"year":2006,"months":[{"date":"2006-01-31T00:00:00.000000Z","value":1937.929,"rate":2.6477522337827395},{"date":"2006-02-28T00:00:00.000000Z","value":1943.188,"rate":0.2713721710134962},{"date":"2006-03-31T00:00:00.000000Z","value":1967.375,"rate":1.2447071513409895},{"date":"2006-04-28T00:00:00.000000Z","value":1993.793,"rate":1.3428044983798202},{"date":"2006-05-31T00:00:00.000000Z","value":1936.409,"rate":-2.8781322835419587},{"date":"2006-06-30T00:00:00.000000Z","value":1939.034,"rate":0.13556020448160666},{"date":"2006-07-31T00:00:00.000000Z","value":1950.995,"rate":0.6168535466629237},{"date":"2006-08-31T00:00:00.000000Z","value":1997.415,"rate":2.3792987680645012},{"date":"2006-09-29T00:00:00.000000Z","value":2048.889,"rate":2.5770308123249492},{"date":"2006-10-31T00:00:00.000000Z","value":2115.654,"rate":3.258595267972055},{"date":"2006-11-30T00:00:00.000000Z","value":2155.885,"rate":1.901586932456837},{"date":"2006-12-29T00:00:00.000000Z","value":2186.127,"rate":1.402764989783762}],"return":0.1579424357011156},{"year":2007,"months":[{"date":"2007-01-31T00:00:00.000000Z","value":2219.189,"rate":1.5123549546755584},{"date":"2007-02-28T00:00:00.000000Z","value":2175.784,"rate":-1.9558946984686543},{"date":"2007-03-30T00:00:00.000000Z","value":2200.12,"rate":1.1184933798575543},{"date":"2007-04-30T00:00:00.000000Z","value":2297.575,"rate":4.429531116484554},{"date":"2007-05-31T00:00:00.000000Z","value":2377.749,"rate":3.4895052392196106},{"date":"2007-06-29T00:00:00.000000Z","value":2338.247,"rate":-1.66131917204045},{"date":"2007-07-31T00:00:00.000000Z","value":2265.75,"rate":-3.100485107005369},{"date":"2007-08-31T00:00:00.000000Z","value":2299.714,"rate":1.499017985214607},{"date":"2007-09-28T00:00:00.000000Z","value":2385.72,"rate":3.7398563473544897},{"date":"2007-10-31T00:00:00.000000Z","value":2423.669,"rate":1.590672836711775},{"date":"2007-11-30T00:00:00.000000Z","value":2322.344,"rate":-4.180645129347283},{"date":"2007-12-31T00:00:00.000000Z","value":2306.232,"rate":-0.6937817997678195}],"return":0.05493962610589413},{"year":2008,"months":[{"date":"2008-01-31T00:00:00.000000Z","value":2167.901,"rate":-5.998138955664487},{"date":"2008-02-29T00:00:00.000000Z","value":2097.475,"rate":-3.248580078149317},{"date":"2008-03-31T00:00:00.000000Z","value":2088.418,"rate":-0.43180490828255813},{"date":"2008-04-30T00:00:00.000000Z","value":2190.131,"rate":4.870337260069562},{"date":"2008-05-30T00:00:00.000000Z","value":2218.499,"rate":1.295264986432315},{"date":"2008-06-30T00:00:00.000000Z","value":2031.471,"rate":-8.43038468802554},{"date":"2008-07-31T00:00:00.000000Z","value":2014.394,"rate":-0.8406223864381985},{"date":"2008-08-29T00:00:00.000000Z","value":2043.532,"rate":1.4464896142462607},{"date":"2008-09-30T00:00:00.000000Z","value":1861.438,"rate":-8.910748644993063},{"date":"2008-10-31T00:00:00.000000Z","value":1548.814,"rate":-16.794757601381306},{"date":"2008-11-28T00:00:00.000000Z","value":1437.679,"rate":-7.175490407498884},{"date":"2008-12-31T00:00:00.000000Z","value":1452.976,"rate":1.0640066384777072}],"return":-0.36997838899122026},{"year":2009,"months":[{"date":"2009-01-30T00:00:00.000000Z","value":1330.51,"rate":-8.428631993921442},{"date":"2009-02-27T00:00:00.000000Z","value":1188.84,"rate":-10.647796709532443},{"date":"2009-03-31T00:00:00.000000Z","value":1292.977,"rate":8.759547121563898},{"date":"2009-04-30T00:00:00.000000Z","value":1416.727,"rate":9.570935909919513},{"date":"2009-05-29T00:00:00.000000Z","value":1495.969,"rate":5.593314731772594},{"date":"2009-06-30T00:00:00.000000Z","value":1498.937,"rate":0.19839983315161192},{"date":"2009-07-31T00:00:00.000000Z","value":1612.312,"rate":7.563693470772947},{"date":"2009-08-31T00:00:00.000000Z","value":1670.523,"rate":3.610405430214499},{"date":"2009-09-30T00:00:00.000000Z","value":1732.859,"rate":3.731525995152424},{"date":"2009-10-30T00:00:00.000000Z","value":1700.668,"rate":-1.8576814385936729},{"date":"2009-11-30T00:00:00.000000Z","value":1802.68,"rate":5.9983488840855586},{"date":"2009-12-31T00:00:00.000000Z","value":1837.499,"rate":1.9315130805245389}],"return":0.26464511457863027},{"year":2010,"months":[{"date":"2010-01-29T00:00:00.000000Z","value":1771.398,"rate":-3.597335291066827},{"date":"2010-02-26T00:00:00.000000Z","value":1826.271,"rate":3.0977228155389156},{"date":"2010-03-31T00:00:00.000000Z","value":1936.477,"rate":6.034482286582886},{"date":"2010-04-30T00:00:00.000000Z","value":1967.049,"rate":1.5787432538573825},{"date":"2010-05-31T00:00:00.000000Z","value":1809.979,"rate":-7.9850578201153155},{"date":"2010-06-30T00:00:00.000000Z","value":1715.229,"rate":-5.234867365864474},{"date":"2010-07-30T00:00:00.000000Z","value":1835.404,"rate":7.0063530875468985},{"date":"2010-08-31T00:00:00.000000Z","value":2076.784,"rate":13.151327991003626},{"date":"2010-08-31T00:00:00.000000Z","value":1752.546,"rate":-15.6125047188345},{"date":"2010-09-30T00:00:00.000000Z","value":1908.951,"rate":8.924444779195525},{"date":"2010-10-29T00:00:00.000000Z","value":1981.585,"rate":3.804916941293939},{"date":"2010-11-30T00:00:00.000000Z","value":1981.839,"rate":0.012818021936979562},{"date":"2010-12-31T00:00:00.000000Z","value":2114.289,"rate":6.683186676617041}],"return":0.15063409558318136},{"year":2011,"months":[{"date":"2011-01-31T00:00:00.000000Z","value":2164.401,"rate":2.3701584788077525},{"date":"2011-02-28T00:00:00.000000Z","value":2238.551,"rate":3.4258901192524007},{"date":"2011-03-31T00:00:00.000000Z","value":2239.441,"rate":0.03975786122362024},{"date":"2011-04-29T00:00:00.000000Z","value":2305.763,"rate":2.9615426349700726},{"date":"2011-05-31T00:00:00.000000Z","value":2279.663,"rate":-1.1319463448758569},{"date":"2011-06-30T00:00:00.000000Z","value":2241.663,"rate":-1.6669130481128178},{"date":"2011-07-29T00:00:00.000000Z","value":2196.079,"rate":-2.0334903150027372},{"date":"2011-08-31T00:00:00.000000Z","value":2076.784,"rate":-5.43218162916726},{"date":"2011-09-30T00:00:00.000000Z","value":1930.789,"rate":-7.029859629118874},{"date":"2011-10-31T00:00:00.000000Z","value":2141.811,"rate":10.929314389091715},{"date":"2011-11-30T00:00:00.000000Z","value":2137.077,"rate":-0.2210279058236182},{"date":"2011-12-30T00:00:00.000000Z","value":2158.938,"rate":1.0229392764041734}],"return":0.021117737452164716},{"year":2012,"months":[{"date":"2012-01-31T00:00:00.000000Z","value":2255.691,"rate":4.481508964129574},{"date":"2012-02-29T00:00:00.000000Z","value":2353.232,"rate":4.324218166406666},{"date":"2012-03-30T00:00:00.000000Z","value":2430.675,"rate":3.2909207421962776},{"date":"2012-04-30T00:00:00.000000Z","value":2415.418,"rate":-0.6276857251586563},{"date":"2012-05-31T00:00:00.000000Z","value":2270.25,"rate":-6.010057058447032},{"date":"2012-06-29T00:00:00.000000Z","value":2363.789,"rate":4.120207025657976},{"date":"2012-07-31T00:00:00.000000Z","value":2396.62,"rate":1.3889141543513261},{"date":"2012-08-31T00:00:00.000000Z","value":2450.598,"rate":2.252255259490454},{"date":"2012-09-28T00:00:00.000000Z","value":2513.926,"rate":2.584185574296569},{"date":"2012-10-31T00:00:00.000000Z","value":2467.508,"rate":-1.846434620589477},{"date":"2012-11-30T00:00:00.000000Z","value":2481.822,"rate":0.5800994363544305},{"date":"2012-12-31T00:00:00.000000Z","value":2504.443,"rate":0.9114674622112346}],"return":0.1600347022471234},{"year":2013,"months":[{"date":"2013-01-31T00:00:00.000000Z","value":2634.161,"rate":5.179514966002401},{"date":"2013-02-28T00:00:00.000000Z","value":2669.919,"rate":1.3574720755488983},{"date":"2013-03-29T00:00:00.000000Z","value":2770.05,"rate":3.750338493414972},{"date":"2013-04-30T00:00:00.000000Z","value":2823.419,"rate":1.9266439233948631},{"date":"2013-05-31T00:00:00.000000Z","value":2889.464,"rate":2.3391852218887976},{"date":"2013-06-28T00:00:00.000000Z","value":2850.662,"rate":-1.3428788176630775},{"date":"2013-07-31T00:00:00.000000Z","value":2995.716,"rate":5.088432090510906},{"date":"2013-08-30T00:00:00.000000Z","value":2908.955,"rate":-2.89616906275495},{"date":"2013-09-30T00:00:00.000000Z","value":3000.18,"rate":3.1360058852749546},{"date":"2013-10-31T00:00:00.000000Z","value":3138.09,"rate":4.596724196548209},{"date":"2013-11-29T00:00:00.000000Z","value":3233.72,"rate":3.0473950715243916},{"date":"2013-12-31T00:00:00.000000Z","value":3315.585,"rate":2.5316044679193084}],"return":0.32388119833432016},{"year":2014,"months":[{"date":"2014-01-31T00:00:00.000000Z","value":3200.952,"rate":-3.457398920552478},{"date":"2014-02-28T00:00:00.000000Z","value":3347.376,"rate":4.574389119237026},{"date":"2014-03-31T00:00:00.000000Z","value":3375.513,"rate":0.8405688515422156},{"date":"2014-04-30T00:00:00.000000Z","value":3400.465,"rate":0.7392061591823307},{"date":"2014-05-30T00:00:00.000000Z","value":3480.288,"rate":2.3474142506980655},{"date":"2014-06-30T00:00:00.000000Z","value":3552.182,"rate":2.0657485817265524},{"date":"2014-07-31T00:00:00.000000Z","value":3503.194,"rate":-1.3790960035268398},{"date":"2014-08-29T00:00:00.000000Z","value":3643.339,"rate":4.000492122331806},{"date":"2014-09-30T00:00:00.000000Z","value":3592.246,"rate":-1.4023674437102756},{"date":"2014-10-31T00:00:00.000000Z","value":3679.988,"rate":2.4425387348193794},{"date":"2014-11-27T00:00:00.000000Z","value":3778.96,"rate":2.689465291734649},{"date":"2014-12-31T00:00:00.000000Z","value":3769.44,"rate":-0.25192116349471405}],"return":0.13688534602490965},{"year":2015,"months":[{"date":"2015-01-30T00:00:00.000000Z","value":3656.284,"rate":-3.0019313213633865},{"date":"2015-02-27T00:00:00.000000Z","value":3866.417,"rate":5.7471739066221375},{"date":"2015-03-31T00:00:00.000000Z","value":3805.271,"rate":-1.5814641824717768},{"date":"2015-04-30T00:00:00.000000Z","value":3841.776,"rate":0.9593272069190277},{"date":"2015-05-29T00:00:00.000000Z","value":3891.178389,"rate":1.2859258061896526},{"date":"2015-06-30T00:00:00.000000Z","value":3815.853,"rate":-1.9357989141011416},{"date":"2015-07-31T00:00:00.000000Z","value":3895.8,"rate":2.095127878353807},{"date":"2015-08-31T00:00:00.000000Z","value":3660.751,"rate":-6.0333949381385},{"date":"2015-09-30T00:00:00.000000Z","value":3570.171,"rate":-2.4743556718280075},{"date":"2015-10-30T00:00:00.000000Z","value":3871.33,"rate":8.435422280893548},{"date":"2015-11-30T00:00:00.000000Z","value":3882.843,"rate":0.2973913357941598},{"date":"2015-12-31T00:00:00.000000Z","value":3821.603,"rate":-1.5771948543889067}],"return":0.013838395093170341},{"year":2016,"months":[{"date":"2016-01-29T00:00:00.000000Z","value":3544.133,"rate":-7.260565788754093},{"date":"2016-02-29T00:00:00.000000Z","value":3627.059,"rate":2.339810610944923},{"date":"2016-03-31T00:00:00.000000Z","value":3873.112,"rate":6.783815758166597},{"date":"2016-04-29T00:00:00.000000Z","value":3888.127,"rate":0.3876727551385102},{"date":"2016-05-31T00:00:00.000000Z","value":3957.95,"rate":1.795800394380123},{"date":"2016-06-30T00:00:00.000000Z","value":3968.206,"rate":0.2591240414861318},{"date":"2016-07-29T00:00:00.000000Z","value":4114.508,"rate":3.6868549667028248},{"date":"2016-08-31T00:00:00.000000Z","value":4120.285,"rate":0.14040560864143004},{"date":"2016-09-30T00:00:00.000000Z","value":4121.064,"rate":0.018906459140580978},{"date":"2016-10-31T00:00:00.000000Z","value":4045.891,"rate":-1.8241162961798238},{"date":"2016-11-30T00:00:00.000000Z","value":4195.73,"rate":3.7034858329104594},{"date":"2016-12-30T00:00:00.000000Z","value":4278.664,"rate":1.9766286200494392}],"return":0.11959928857079076},{"year":2017,"months":[{"date":"2017-01-31T00:00:00.000000Z","value":4359.815,"rate":1.8966434382321182},{"date":"2017-02-28T00:00:00.000000Z","value":4532.925,"rate":3.97058132053769},{"date":"2017-03-31T00:00:00.000000Z","value":4538.213,"rate":0.11665756658227622},{"date":"2017-04-28T00:00:00.000000Z","value":4584.82,"rate":1.0269901390701648},{"date":"2017-05-31T00:00:00.000000Z","value":4649.341,"rate":1.4072744404360549},{"date":"2017-06-30T00:00:00.000000Z","value":4678.36,"rate":0.6241529713565654},{"date":"2017-07-31T00:00:00.000000Z","value":4774.56,"rate":2.056276130951886},{"date":"2017-08-31T00:00:00.000000Z","value":4789.176,"rate":0.3061224489795933},{"date":"2017-09-29T00:00:00.000000Z","value":4887.968,"rate":2.062818321982732},{"date":"2017-10-31T00:00:00.000000Z","value":5002.03,"rate":2.3335259150632766},{"date":"2017-11-30T00:00:00.000000Z","value":5155.441,"rate":3.0669748082278545},{"date":"2017-12-29T00:00:00.000000Z","value":5212.763,"rate":1.1118738435761344}],"return":0.21831557701188975},{"year":2018,"months":[{"date":"2018-01-31T00:00:00.000000Z","value":5511.214,"rate":5.725389778894609},{"date":"2018-02-28T00:00:00.000000Z","value":5308.087,"rate":-3.685703367715334},{"date":"2018-03-29T00:00:00.000000Z","value":5173.191,"rate":-2.541329861398296},{"date":"2018-04-30T00:00:00.000000Z","value":5193.041,"rate":0.3837090105507457},{"date":"2018-05-31T00:00:00.000000Z","value":5318.099,"rate":2.4081843374623872},{"date":"2018-06-29T00:00:00.000000Z","value":5350.832,"rate":0.6155018926876039},{"date":"2018-07-31T00:00:00.000000Z","value":5549.956,"rate":3.7213652007762477},{"date":"2018-08-31T00:00:00.000000Z","value":5730.803,"rate":3.258530337898179},{"date":"2018-09-28T00:00:00.000000Z","value":5763.422,"rate":0.5691872500241146},{"date":"2018-10-31T00:00:00.000000Z","value":5369.491,"rate":-6.835019195193411},{"date":"2018-11-30T00:00:00.000000Z","value":5478.913,"rate":2.037846790319591},{"date":"2018-12-31T00:00:00.000000Z","value":4984.217,"rate":-9.029090259326992}],"return":-0.043843543241847034},{"year":2019,"months":[{"date":"2019-01-31T00:00:00.000000Z","value":5383.632,"rate":8.013595716237873},{"date":"2019-02-28T00:00:00.000000Z","value":5556.49,"rate":3.210806384983229},{"date":"2019-03-29T00:00:00.000000Z","value":5664.463,"rate":1.94318715592037},{"date":"2019-04-30T00:00:00.000000Z","value":5893.815,"rate":4.04896280547689},{"date":"2019-05-31T00:00:00.000000Z","value":5519.274,"rate":-6.354814326544002},{"date":"2019-06-28T00:00:00.000000Z","value":5908.251,"rate":7.047611696755752},{"date":"2019-07-31T00:00:00.000000Z","value":5993.17,"rate":1.4372950641399598},{"date":"2019-08-30T00:00:00.000000Z","value":5898.23,"rate":-1.584136608839728},{"date":"2019-09-30T00:00:00.000000Z","value":6008.59,"rate":1.8710697955149271},{"date":"2019-10-31T00:00:00.000000Z","value":6138.73,"rate":2.165899154377314},{"date":"2019-11-29T00:00:00.000000Z","value":6361.56,"rate":3.629903905205154},{"date":"2019-12-31T00:00:00.000000Z","value":6553.57,"rate":3.01828482321946}],"return":0.3148645012847555},{"year":2020,"months":[{"date":"2020-01-31T00:00:00.000000Z","value":6551,"rate":-0.03921526740387549},{"date":"2020-02-28T00:00:00.000000Z","value":6011.73,"rate":-8.231872996489088},{"date":"2020-03-31T00:00:00.000000Z","value":5269.2,"rate":-12.351353104680342},{"date":"2020-04-30T00:00:00.000000Z","value":5944.18,"rate":12.80991421847719},{"date":"2020-05-29T00:00:00.000000Z","value":6227.81,"rate":4.771558061835265},{"date":"2020-06-30T00:00:00.000000Z","value":6351.67,"rate":1.9888211104706102},{"date":"2020-07-31T00:00:00.000000Z","value":6709.81,"rate":5.6385171143966915},{"date":"2020-08-30T00:00:00.000000Z","value":7192.11,"rate":7.187982968221149},{"date":"2020-09-29T00:00:00.000000Z","value":6918.83,"rate":-3.7997194147475426},{"date":"2020-10-30T00:00:00.000000Z","value":6734.84,"rate":-2.6592646444557744},{"date":"2020-11-30T00:00:00.000000Z","value":7472.06,"rate":10.946362497104602},{"date":"2020-12-31T00:00:00.000000Z","value":7759.35,"rate":3.8448567061827674}],"return":0.18398826898926857},{"year":2021,"months":[{"date":"2021-01-29T00:00:00.000000Z","value":7681.01,"rate":-1.0096206512143482},{"date":"2021-02-26T00:00:00.000000Z","value":7892.81,"rate":2.7574498666190976},{"date":"2021-03-31T00:00:00.000000Z","value":8238.48,"rate":4.37955557019616},{"date":"2021-04-30T00:00:00.000000Z","value":8678.16,"rate":5.336906808051978},{"date":"2021-05-28T00:00:00.000000Z","value":8738.77,"rate":0.6984199415544339},{"date":"2021-06-30T00:00:00.000000Z","value":8942.78,"rate":2.334539071288077},{"date":"2021-07-30T00:00:00.000000Z","value":9155.21,"rate":2.3754358264432085},{"date":"2021-08-31T00:00:00.000000Z","value":9433.58,"rate":3.0405637882691963},{"date":"2021-09-30T00:00:00.000000Z","value":8994.83,"rate":-4.650938456026239},{"date":"2021-10-29T00:00:00.000000Z","value":9625.02,"rate":7.006135746867926},{"date":"2021-11-30T00:00:00.000000Z","value":9558.33,"rate":-0.6928816771289803},{"date":"2021-12-31T00:00:00.000000Z","value":9986.7,"rate":4.481640621321944}],"return":0.2870536836204064},{"year":2022,"months":[{"date":"2022-01-31T00:00:00.000000Z","value":9469.92,"rate":-5.174682327495574},{"date":"2022-02-28T00:00:00.000000Z","value":9186.37,"rate":-2.9942174801898886},{"date":"2022-03-31T00:00:00.000000Z","value":9527.46,"rate":3.7130008915382007},
          //{"date":"2022-04-29T00:00:00.000000Z","value":8696.65,"rate":-8.720162561690103}],"return":-0.12917680515085073}]';
          
          //$data1 = json_decode($dd, true);
          //$data = (array)$data1;

          
            $years = [];
           //use backend\models\Sp500rates;       
           // $ret=0;
            foreach($data as $y){
                $year = [];
                
                
      ini_set("precision", 16); 
      //var_dump(1.9315130805245389);  
     /*     
    if($y['year']>=2000  && $y['year']<2022){   
            $model = new backend\models\Sp500rates();
            $model->year = $y['year'];
            $ret = $y['return'];
            $model->return = $ret;
            
            $months = $y['months'];
            
            $k =1;
            foreach($months as $m){   
                $mm = $m['rate'];
                if($k==1){$model->january = $mm;}
                if($k==2){$model->february = $mm;}
                if($k==3){$model->march = $mm;}
                if($k==4){$model->april = $mm;}
                if($k==5){$model->may = $mm;}
                if($k==6){$model->june = $mm;}
                if($k==7){$model->july = $mm;}
                if($k==8){$model->august = $mm;}
                if($k==9){$model->september = $mm;}
                if($k==10){$model->october = $mm;}
                if($k==11){$model->november = $mm;}
                if($k==12){$model->december = $mm;}
                $k++;         
            }
            $model->save();
    }

   */            
                
                
               // var_dump($year);
         // exit;
             // if ($year['year'] < $marketHistory['from']) {
              //  return
             // }

             // if ($year['year'] >= $marketHistory['to']) {
             //   return
            //  }

            if($y['year']>=$marketHistory['from'] && $y['year']<$marketHistory['to']){
            //$year['toggled'] = false;
            $year = $y;
            $year['boy'] = 0;
            $year['eoy'] = 0;
            
            $year['income'] = $calculation['income'];
            $year['geometricAverage'] = 0;
            $year['charges'] = 0;
            $year['fees'] = 0;
            
           // $year['year'] = $y['year'];
            
            $year['return'] = $y['return']*100;
            
            $year['months'][] = ['rate'=>$y['january']];
            $year['months'][] = ['rate'=>$y['february']];
            $year['months'][] = ['rate'=>$y['march']];
            $year['months'][] = ['rate'=>$y['april']];
            $year['months'][] = ['rate'=>$y['may']];
            $year['months'][] = ['rate'=>$y['june']];
            $year['months'][] = ['rate'=>$y['july']];
            $year['months'][] = ['rate'=>$y['august']];
            $year['months'][] = ['rate'=>$y['september']];
            $year['months'][] = ['rate'=>$y['october']];
            $year['months'][] = ['rate'=>$y['november']];
            $year['months'][] = ['rate'=>$y['december']];
            
           // $ret=$year['return'];
/*
             // $year['rate'] = $year['rate'];
              $year['return'] = $year['rate']; //*100;
              
              
              
              
              $year['months'] = [];
        */     
        
       // if($y['year']==2010){
        //      $year['return'] = 15.063409558318136;
        //        }
        
              
              $years[] = $year; //his.years.push(year)
             // if($y['year']==2010){
             // var_dump($year);
             // exit;
              //  }
              }
            //  echo $ret."<br/>";
              
              
            }
            //var_dump($ret);
            //var_dump($data);
            //exit;
           //  calculateMonths($calculation, $years);
            calculateMarketHistory($calculation, $years);
          }
        }
        
        
        
        

      
      
      
///////////////////////////////////////////////////////////////

//var_dump($_REQUEST);
$from = $_REQUEST['from'];
$to = $_REQUEST['to'];

$current_value = $_REQUEST['Calculations']['current_value']; 
$years_of_investment = $_REQUEST['Calculations']['years_of_investment'];
$annual_return_rate = $_REQUEST['Calculations']['annual_return_rate'];
$annual_withdrawal = $_REQUEST['Calculations']['annual_withdrawal'];
$management_fee = $_REQUEST['Calculations']['management_fee'];


    $loading = false; 
    $showMarketHistory = $_REQUEST['Calculations']['market_history']; 
    $showBreakeven = false;
    $marketHistory=['from'=>$from, 'to'=>$to];
    $total = ['worth'=>null, 'remaining'=>null];
    //$calculation = ['reference'=>null, 'value'=>null, 'years'=>null, 'return'=>null, 'income'=>null, 'fee'=>null];
    $calculation = ['reference'=>'test', 'value'=>$current_value, 'years'=>$years_of_investment, 'return'=>$annual_return_rate, 'income'=>$annual_withdrawal, 'fee'=>$management_fee]; 
     
    $statistics = ['averageReturn'=>0, 'geometricAverage'=>1, 'actualAnnualizedYield'=>0, 'earnedIncome'=>0, 'lowestPrincipal'=>0, 'optimalBreakeven'=>0];
    $years = []; 
    $demo = ['reference'=>'demo', 'value'=>100000, 'years'=>10, 'return'=>5, 'income'=>4000, 'fee'=>1];
    $breakevenCurrent=0;
    $breakevenYrs=0; 
    $breakevenIndex=0;
    $breakevenIncome=[];
    $breakevenErrors=[];
?>

<hr />
<div class="card bg-light mb-3">
  <div class="card-body">
    <?php doTheMath($calculation, $showMarketHistory, $marketHistory); ?>
  </div>
</div>