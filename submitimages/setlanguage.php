<?php

  include('config.php');

  // Error checking is done inside common.php for real pages, so we can make
  // this a very minimal page without worrying too much. Also, it wouldn't
  // make much sense to do much checking anyway, since they can easily edit a
  // cookie that is in their browser.
  
  // If we have a language passed in, set it in the cookie
  if (isset($_GET['language']))
  {
    // Store the language cookie for 30 days.
    setcookie('language', $_GET['language'], gmmktime()+(60*60*24*30), '/');
  }
  
  // Redirect back to the index page.
  header('Location: '.$config['paths']['baseURL']);

?>
