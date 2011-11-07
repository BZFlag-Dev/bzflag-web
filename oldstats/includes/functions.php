<?php

  function secondsToUnits($duration)
  {
    $units = Array(
      'hours' => 3600,
      'minutes' => 60,
      'seconds' => 1
    );
    
    $values = Array();
    
    foreach($units as $unit => $secondsPerUnit)
    {
      $values[$unit] = floor($duration / $secondsPerUnit);
      $duration -= $secondsPerUnit * $values[$unit];
    }
    
    return $values;
  }
  
?>
