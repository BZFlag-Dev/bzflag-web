<?php

  define('USING_SMARTY', true);
  define('USING_QUEUEDB', true);

  include('common.php');
  
  include($config['paths']['includes']."directorydb.class.php");
  
  // Must be logged in to access the queue
  if (!$user)
  {
    header('Location: '.$config['paths']['baseURL']);
    exit;
  }
  // Must be a moderator to access the queue
  else if (!$user['moderator'])
  {
    header('Location: '.$config['paths']['baseURL']);
    exit;
  }
  
  // If they submit the form, let's process that
  if (sizeof($_POST) > 0)
  {
    //////////////////////////////////////////
    // Read user input
    //////////////////////////////////////////
    
    // Queue table primary key value
    if (isset($_POST['queueid']))
      $input['queueid'] = $_POST['queueid'];
      
    // Message to the user
    if (isset($_POST['message']))
      $input['message'] = $_POST['message'];
      
    // Action to complete (Accept or Reject)
    if (isset($_POST['action']))
      $input['action'] = $_POST['action'];
      
    //////////////////////////////////////////
    // Process input and generate any errors
    //////////////////////////////////////////
    
    $data['queueitem'] = $qdb->Queue_Fetch_ByID($input['queueid']);

    header('Content-Type: text/plain');
    
    if ($data['queueitem'])
    {
      echo "Database entry exists for this item. Contents:\n";
      print_r($data['queueitem']);
    
      if (strtolower($input['action']) == 'accept')
      {
        echo "Action selected was 'accept'.\n";
        if (file_exists($config['paths']['tmp'].$data['queueitem']['bzid'].'_'.$data['queueitem']['filename']))
        {
          $subdir = strtolower(substr($data['queueitem']['uploaderfirstname'], 0, 1).$data['queueitem']['uploaderlastname']);
          echo "Subdirectory name for this uploader will be '$subdir'\n";
          echo "Final location for file will be: ".$config['paths']['publicDirectory'].$subdir.'/'.$data['queueitem']['filename'];
          echo "\n";
          echo "Final URL for file will be: ".$config['paths']['publicURL'].$subdir.'/'.$data['queueitem']['filename'];
          echo "\n";
          
          // First, make sure our destination directory exists. If not, try to create it
          if (is_dir($config['paths']['publicDirectory'].$subdir.'/') || mkdir($config['paths']['publicDirectory'].$subdir.'/')) {
            // Attempt to rename the file into this directory
            if (rename($config['paths']['tmp'].$data['queueitem']['bzid'].'_'.$data['queueitem']['filename'], $config['paths']['publicDirectory'].$subdir.'/'.$data['queueitem']['filename']))
            {
              echo "This item was successfully moved into the public directory.\n";
              
              // Prepare the data to be added into the SQLite database
              $data['file'] = Array();              
              $data['file']['filename'] = $data['queueitem']['filename'];
              $data['file']['bzid'] = $data['queueitem']['bzid'];
              $data['file']['username'] = $data['queueitem']['username'];
              $data['file']['ipaddress'] = $data['queueitem']['ipaddress'];
              $data['file']['uploaderfirstname'] = $data['queueitem']['uploaderfirstname'];
              $data['file']['uploaderlastname'] = $data['queueitem']['uploaderlastname'];
              $data['file']['uploaderemail'] = $data['queueitem']['uploaderemail'];
              $data['file']['filemd5'] = $data['queueitem']['filemd5'];
              $data['file']['authorname'] = $data['queueitem']['authorname'];
              
              if (substr($data['queueitem']['licensename'], 0, 1) == "*")
                $data['file']['licensename'] = substr($data['queueitem']['licensename'], 1);
              else
                $data['file']['licensename'] = $data['queueitem']['licensename'];
              $data['file']['licenseurl'] = $data['queueitem']['licenseurl'];
              $data['file']['licensetext'] = $data['queueitem']['licensetext'];
              $data['file']['moderationmessage'] = $input['message'];
              $data['file']['moderator'] = $user['username'];
              
              $dirdb = new DirectoryDB($config['paths']['publicDirectory'].$subdir.'/data.sqlite3');
              if ($dirdb->AddEntry($data['file']))
              {
                if ($qdb->Queue_Delete_ByID($data['queueitem']['queueid']))
                {
                  // Send an email to the upload letting them know their image
                  // was accepted.
                  $data['email'] = Array();
                  $data['email']['username'] = $data['queueitem']['username'];
                  $data['email']['uploaderfirstname'] = $data['queueitem']['uploaderfirstname'];
                  $data['email']['uploaderlastname'] = $data['queueitem']['uploaderlastname'];
                  $data['email']['imageURL'] = $config['paths']['publicURL'].$subdir.'/'.$data['queueitem']['filename'];
                  $email['body'] = $tpl->fetch('email_approve.tpl');
                  email($data['queueitem']['uploaderemail'], $config['mail']['fromaddress'], "Image Submission Approved", $email['body']);
                }
                else
                  echo "There was an error deleting the MySQL database entry for this item.\n";
              }
              else
                echo "There was an error adding the directory database entry for this item.\n";
            }
            else
              echo "We were unable to rename the file to the destination location.\n";
          }
          else
          {
            echo "We were unable to create create the destination directory for this image.\n";
          }
        }
        else
        {
          echo "Error... The temporary file did not exist.\n";
        }
      }
      else
      {
        echo "Action selected was 'reject'.\n";
        if (!isset($input['message']) || strlen(trim($input['message'])) == 0)
        {
          echo "When rejecting an image, a message to the user must be specified.\n";
        }
        else
        {
          echo "Rejecting image with the following message to the user:\n".$input['message']."\n\n";
          
          // Try to delete the database entry
          if ($qdb->Queue_Delete_ByID($data['queueitem']['queueid']))
          {
            // Send an email to the upload letting them know their image
            // was accepted.
            $data['email'] = Array();
            $data['email']['username'] = $data['queueitem']['username'];
            $data['email']['uploaderfirstname'] = $data['queueitem']['uploaderfirstname'];
            $data['email']['uploaderlastname'] = $data['queueitem']['uploaderlastname'];
            $data['email']['filename'] = $data['queueitem']['filename'];
            $data['email']['authorname'] = $data['queueitem']['authorname'];
            $data['email']['licensename'] = $data['queueitem']['licensename'];
            $data['email']['message'] = $input['message'];
            
            $email['body'] = $tpl->fetch('email_reject.tpl');

            email($data['queueitem']['uploaderemail'], $config['mail']['fromaddress'], "Image Submission Rejected", $email['body']);
          }
          else
            echo "There was an error removing the database entry for this item.\n";
          
          // Try to delete the temporary file
          if (!unlink($config['paths']['tmp'].$data['queueitem']['bzid'].'_'.$data['queueitem']['filename']))
            echo "There was an error removing the temporary file for this item.\n";
        }
      }
    }
    else
    {
      echo "The image specified was not found in the queue.\n";
    }
    
    
    exit;
    

  }
  // We'll just be listing the queue
  else
  {
    // Handle data
    $data['queue'] = $qdb->Queue_Fetch_All();
    
    if ($data['queue'] && is_array($data['queue']) && sizeof($data['queue']) > 0)
    {
      foreach ($data['queue'] as &$item)
      {
        if (substr($item['licensename'], 0, 1) == "*")
          $item['licensename'] = "[".$lang['other']."] ".substr($item['licensename'], 1);
      }
    }
  }
  
  
  $page['title'] = 'Moderation Queue';
	$page['javascripts'] = Array('lang.js.php?lang=en', 'util.js', 'queue.js');
  
  
  // Render the page
	$tpl->display('header.tpl');
	$tpl->display('queue.tpl');
	$tpl->display('footer.tpl');

?>
