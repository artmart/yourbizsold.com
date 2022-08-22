<?php
 
      $loading = false; 
      $showMarketHistory = false; 
      $showBreakeven = false;
      $marketHistory = ['from'=>2000, 'to'=>null];
      $total = ['worth'=>null, 'remaining'=>null];
      $calculation = ['reference'=>null, 'value'=>null, 'years'=>null, 'return'=>null, 'income'=>null, 'fee'=>null]; 
      $statistics = ['averageReturn'=>0, 'geometricAverage'=>1, 'actualAnnualizedYield'=>0, 'earnedIncome'=>0, 'lowestPrincipal'=>0, 'optimalBreakeven'=>0];
      $years = []; 
      $demo = ['reference'=>'demo', 'value'=>100000, 'years'=>10, 'return'=>5, 'income'=>4000, 'fee'=>1];
      $breakevenCurrent=0;
      $breakevenYrs=0; 
      $breakevenIndex=0;
      $breakevenIncome=[];
      $breakevenErrors=[];
    
    
      function calculateMarketHistory($calculation) {
        // reset worth to original income so we can subtract freely
        $total['worth'] = $calculation['value'];
        $total['remaining'] = $calculation['value'];

        // reset statistics values to ensure it's always recalculated
        $statistics['averageReturn'] = 0;
        // default value is 1 because 0? anything is 0 ;)
        $statistics['geometricAverage'] = 1;
        $statistics['actualAnnualizedYield'] = 0;
        $statistics['finalNetValue'] = 0;
        $statistics['totalAnnualizedYield'] = 0;
        $statistics['earnedIncome'] = 0;
        $statistics['lowestPrincipal'] = $calculation['value'];

        // iterate over years, workout monthly math and reduce total worth
        foreach($years as $index=>$year){
          $year['geometricAverage'] = (1 + ($year['return'] / 100));
          $statistics['earnedIncome'] += ($years[($index == 0 ? 0 : $index -1)]['eoy'] < $years[($index == 0 ? 0 : $index -1)]['income'] ? $years[($index == 0 ? 0 : $index -1)]['eoy'] : $year['income']);
          $year['charges'] = null;
          $year['fees'] = null;
          $year['boy'] = null;
          $year['eoy'] = null;
          $year['toggled'] = false;

          // calculate statistics
          $statistics['averageReturn'] += $year['return'];
          $statistics['geometricAverage'] *= $year['geometricAverage'];

          foreach($year['months'] as $month){
            $calculatedValue = ((1 + $month['rate'] / 100) * $total['worth']) - $year['income'] / 12;
            $charge = $month['rate'] / 100 * $calculatedValue;
            $globalFee = $calculation['fee'] / 12 / 100;
            $remaining = $calculatedValue - ($globalFee * $calculatedValue);
            $monthlyCharge = $globalFee * $calculatedValue;
            $month['income'] = $year['income'] / 12;
            $month['fee'] = $globalFee;
            $month['value'] = $calculatedValue;
            $month['charge'] = ($monthlyCharge < 0 ? 0 : $monthlyCharge);
            $month['remaining'] = remaining;
            $statistics['lowestPrincipal'] = ($remaining < $statistics['lowestPrincipal'] ? $remaining : $statistics['lowestPrincipal']);
            if($statistics['lowestPrincipal'] < 0) {
              $statistics['lowestPrincipal'] = 0;
            }
            $total['worth'] = $remaining;
            $total['remaining'] -= $remaining;
            $year['charges'] += floatval($month['charge'], 4);
          }

          $year['boy'] = $years[$index - 1] ? $years[$index - 1][$months[11]['remaining']] : $calculation['value']; //????
          $year['v'] += $year['charges'] + ($years[$index - 1] ? $years[$index - 1]['fees'] : null);
          // last iteration will assign final net value
          $year['eoy'] = $statistics['actualAnnualizedYield'] = $years[$index][$months[11]['remaining']]; //???????

          $statistics['totalAnnualizedYield'] = $year['eoy'];
          $statistics['finalNetValue'] = $year['eoy'];

          if($year['eoy'] < 0) {
            $year['eoy'] = $statistics['actualAnnualizedYield'] = 0;
          }

          if($year['boy'] < 0) {
            $year['boy'] = 0;
          }
        }

        calculateStatistics();
      }
      
     function calculateMonths() {
        // reset worth to original income so we can subtract freely
        $total['worth'] = $calculation['value'];
        $total['remaining'] = $calculation['value'];

        // reset statistics values to ensure it's always recalculated
        $statistics['averageReturn'] = 0;
        // default value is 1 because 0? anything is 0 ;)
        $statistics['geometricAverage'] = 1;
        $statistics['actualAnnualizedYield'] = 0;
        $statistics['finalNetValue'] = 0;
        $statistics['totalAnnualizedYield'] = 0;
        $statistics['earnedIncome'] = 0;
        $statistics['lowestPrincipal'] = $calculation['value'];

        // iterate over years, workout monthly math and reduce total worth
        foreach($years as $index=>$year){
          $year['geometricAverage'] = (1 + ($year['return'] / 100));
          $statistics['earnedIncome'] += ($years[($index == 0 ? 0 : $index -1)]['eoy'] < $years[($index == 0 ? 0 : $index -1)]['income'] ? $years[($index == 0 ? 0 : $index -1)]['eoy'] : $year['income']);
          $year['charges'] = null;
          $year['fees'] = null;
          $year['boy'] = null;
          $year['eoy'] = null;
          $year['toggled'] = false;
          $year['months'] = [];
          $previousRemaining = (!$years[$index - 1] ? $calculation['value'] : $years[$index - 1][$months[11]]['remaining']);//?????
          $fees = 0;

          // calculate statistics
          $statistics['averageReturn'] += $year['return'];
          $statistics['geometricAverage'] *= $year['geometricAverage'];

          // dynamic monthly values
          for($j = 1; $j < 13; $j++){
            $month = [];
            $calculatedValue = ((1 + $year['return'] / 12 / 100) * $total['worth']) - $year['income'] / 12;
            $charge = $calculation['v'] / 12 / 100 * $calculatedValue;
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
            if ($statistics['lowestPrincipal'] < 0){
              $statistics['lowestPrincipal'] = 0;
            }
            $total['worth'] = $remaining;
            $total['remaining'] -= $remaining;
            $previousRemaining = $month['remaining'];

            $year['months'][] = $month; // .push(month)
          }

          foreach($year['months'] as $month){
            $year['charges'] += floatval($month['charge'], 4);
          }

          $year['boy'] = ($years[$index - 1] ? $years[$index - 1][$months[11]]['remaining'] : $calculation['value']); //??
          $year['fees'] += $year['charges'] + ($years[$index - 1] ? $years[$index - 1]['fees'] : null);
          // last iteration will assign final net value
          $year['eoy'] = $statistics['actualAnnualizedYield'] = $years[$index][$months[11]]['remaining']; // ??

          if($year['eoy']< 0){
            $year['eoy'] = $statistics['actualAnnualizedYield'] = 0;
          }

          if($year['boy']<0){
            $year['boy'] = 0;
          }

          $statistics['totalAnnualizedYield'] = $year['eoy'];
          $statistics['finalNetValue'] = $year['eoy'];
        }

        calculateStatistics();
      },
      
     function calculateStatistics(){
        $statistics['averageReturn'] = $statistics['averageReturn'] / $calculation['years'];
        // javascript does not support "y square root of x", but supports a second argument in "x to the power of y"
        $statistics['geometricAverage'] = (pow($statistics['geometricAverage'], (1 / $calculation['years'])) - 1) * 100;
        $statistics['actualAnnualizedYield'] = (pow($statistics['actualAnnualizedYield'] / $calculation['value'], (1 / $calculation['years'])) - 1) * 100;
        $statistics['totalAnnualizedYield'] = (pow(($statistics['totalAnnualizedYield'] + $statistics['earnedIncome']) / $calculation['value'], (1 / $calculation['years'])) - 1) * 100;
        $statistics['finalNetValue'] = $statistics['finalNetValue'] + $statistics['earnedIncome'];
      },
      
    function  doTheMath(){
        $years = [];
        // worth is a duplicate of income, so that the value can be manipulated
        $total['worth'] = $calculation['value'];

        $currentYear =  date("Y"); // new Date().getFullYear()
        $yearsRange = $calculation['years'];

        if($showMarketHistory){
          $currentYear = $marketHistory['from'];

          if(!$marketHistory['to']){
            $marketHistory['to'] = $marketHistory['from'];
          }

          if($marketHistory['from'] > $marketHistory['to']) {
            $marketHistory['to'] = $marketHistory['from'];
          }

          $yearsRange = $marketHistory[to] - $marketHistory['from'];
          $calculation['years'] = $yearsRange;
        }

        // TODO - detect existing value and don't overwrite

        if(!$showMarketHistory){
          // years
          for($i = 0; $i < $yearsRange; $i++){
            $year = [];
            $year['year'] = $currentYear + $i;
            $year['return'] = $calculation['return'];
            $year['income'] = $calculation['income'];
            $year['toggled'] = false;
            $year['months'] = [];
            $years[] = $year; // .push(year)
          }

          calculateMonths();
        }

/*
        if($showMarketHistory){
          fetch('/rates')
          .then(response => response.json())
          .then(data => {
            this.years = []
            data.forEach(year => {
              if (year['year'] < this.marketHistory.from) {
                return
              }

              if (year['year'] >= this.marketHistory.to) {
                return
              }

              year.return *= 100
              year.income = this.calculation.income
              this.years.push(year)
            })
            this.calculateMarketHistory()
          })
        }
*/        
        
        
        
      }



?>