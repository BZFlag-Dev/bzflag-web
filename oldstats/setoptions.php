<?php

  include("config.php");

  $expire = time()+60*60*24*30;

  if ($_POST['refresh'] == "yes")
    setcookie("refresh", true, $expire);
  else
    setcookie("refresh", false, $expire);
    
  if (array_key_exists($_POST['theme'], $config['themes']))
    setcookie("theme", $_POST['theme'], $expire);
  else
    setcookie("theme", 'bluetangerine', $expire);

  header('Location: '.$_SERVER['HTTP_REFERER']);

?>
