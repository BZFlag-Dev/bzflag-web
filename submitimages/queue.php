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
                if (!$qdb->Queue_Delete_ByID($data['queueitem']['queueid']))
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
          if (!$qdb->Queue_Delete_ByID($data['queueitem']['queueid']))
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

// Temporary holding place for the old code


/*


//The approve image function
if($_GET['page'] == 'approve' and $_SESSION['admin'] == true){
	
	$javascript = '<script language="Javascript" type="text/javascript">
		function ShowDenyForm(image_number){
			document.getElementById(image_number).style.display = "block";
		}
	</script>';

	$text = '<p>Here are all the images that the minions want hosted, '.$_SESSION['image_user'].'. They are at your discretion. Click on the thumbnails for the life sized image.</p>
	<table border="0"><tr>';
	$file = file('imageswaiting.txt');
	$i = 0;
	foreach($file as $image){
		$image_info = explode('|', rtrim($image));
		
		$callsign = $image_info[0];
		$filename = $image_info[1];
		$license = $image_info[2];
		$email = $image_info[3];
		$fname = $image_info[4];
		$lname = $image_info[5];

		if($i % 3 == 0) $text .= '</tr><tr>';
		
		$text .= "<td valign='center'><a href='getimage.php?image=$filename' target='_blank'><img src='getthumb.php?image=$filename' border=0></a><br><b>$callsign</b> wants this image.<br>
		<b>Folder</b>: ".strtolower(substr($fname, 0, 1)).strtolower($lname)."<br>
		<b>Filename</b>: $filename<br><br>
		<b>Author</b>: $fname $lname<br>
		<b>Email</b>: $email<br>
		<b>License</b>: $license<br>
		<input type='button' name='approve' value='Approve it!' onClick=\"javascript:window.location='index.php?page=approveimage&image=".urlencode($filename)."&callsign=$callsign'\" class='formelement'><br>
		<input type='button' name='deny' value='denied.' onClick='javascript:ShowDenyForm($i)' class='formcancel'>
		<div id='$i' style='display:none;'>
		<form method='post' action='index.php?page=denyimage'>
			<input type='hidden' name='image' value='$filename'>
			<input type='hidden' name='email' value='$email'>
			<input type='hidden' name='author' value='$callsign'>
			Reason for denial?<br>
			<textarea name='reason' cols='22' rows='4' class='formelementred'></textarea><br>
			<input type='submit' name='submit' value='Delete Image. Inform User.' class='formcancel'>
		</form>
		</div></td>";
		
		$i++;
	}
	$text .= '</tr></table>';
}


//For denying the image and crushing the user's soul.
if($_GET['page'] == 'denyimage' and $_SESSION['admin'] == true){
	if(unlink('./image_holding/'.$_POST['image']))
		$text = '<p>Congrats! You crushed the soul of somebody by deleting their image from the server.</p>';
	else
		$text = '<b><font color="red">Something went wrong! The file could not be deleted from ./image_holding/'.$_GET['image'].' . You will have to delete it yourself.</font></b><br>';
	
	$images = file('imageswaiting.txt');
	$finalfile = '';
	foreach($images as $line){
		$image_info = explode('|', rtrim($line));
		
		//If this is the image that's being denied, let's take it out of the waiting file
		if($image_info[1] == $_POST['image']) continue;
		else $finalfile .= $line;
	}
	$fp = fopen('imageswaiting.txt', 'w');
	fwrite($fp, $finalfile);
	fclose($fp);
	
	$email_body = "{$_SESSION['image_user']} has rejected your image ({$_POST['image']}), {$_POST['author']}.\nReason: ";
	$email_body .= stripslashes($_POST['reason']);
	
	mail($_POST['email'], 'Your image was denied.', $email_body, 'From: '.$config['mail']['fromaddress']);
}


//For actually approving the images.
if($_GET['page'] == 'approveimage' and $_SESSION['admin'] == true){

	//Get the acutal username of the user
	$file = file('imageswaiting.txt');
	foreach($file as $line){
		$image_detail = explode('|', rtrim($line));
		
		$email = $image_detail[3];
		$fname = $image_detail[4];
		$lname = $image_detail[5];
		$dname = strtolower(substr($fname, 0, 1)).strtolower($lname);
	}
	
	//If the user's directory isn't there, create it, and move the appropriate files into it.
	if(!file_exists("../$dname")){
		if(mkdir("../$dname", 0777)) $text .= '<p>New directory created for user!</p>';
		if(copy('./image_holding/index.php', "../$dname/index.php")) $text .= '<p>New directory set up for user!</p>';
		
		$fp = fopen("../$dname/author.txt", 'w');
		fwrite($fp, "Licenses for $fname $lname's images.\n\n");
		fclose($fp);
	}
	
	if(copy('./image_holding/'.$_GET['image'], "../$dname/{$_GET['image']}")){
		$text .= '<p>Congrats! The image was approved and copied sucessfully.</p>';
		
		$images = file('imageswaiting.txt');
		$finalfile = '';
		foreach($images as $line){
			$image_info = explode('|', rtrim($line));
			
			//If this is the image that's being approved, let's take it out of the waiting file. And snatch the license while we're at it.
			if($image_info[1] == $_GET['image']){
				$image_license = $image_info[2];
				continue;
			}else{
				$finalfile .= $line;
			}
		}
		$fp = fopen('imageswaiting.txt', 'w');
		fwrite($fp, $finalfile);
		fclose($fp);
		
		//Find the author.txt file in their directory and add the license to it.
		$fp = fopen("../$dname/author.txt", 'a');
		fwrite($fp, "License for {$_GET['image']}: $image_license\n\n");
		fclose($fp);
		
		$filepath = explode('/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
		//$filepath = explode('/', str_replace('\\', '/', $_SERVER['PATH_TRANSLATED']));
		array_pop($filepath);
		@array_pop($filepath);
		$filepath = implode('/', $filepath);

		$email_body = 'Congratulations, '.$fname.' '.$lname.', your image, '.$_GET['image'].' is now hosted at http://'.urldecode($_SERVER['HTTP_HOST']).urldecode($path).'/'.$dname.'/'.$_GET['image'];
		
		mail($email, 'BZFlag Image Submission System', $email_body, 'From: '.$config['mail']['fromaddress']);
		
		
		if(unlink('./image_holding/'.$_GET['image']))
			$text .= '<p>Congrats! The image was deleted from the temporary holding folder.</p>';
		else
			$text .= '<b><font color="red">Something went wrong! The image could not be deleted from the holding folder. Please delete ./image_holding/'.$_GET['image'].' manually. Everything else worked and the image has been approved.</font></b><br>';
	}else
		$text = '<b><font color="red">Something went wrong! The image could not be copied. Check the filesystem to make sure permissions allow the PHP deamon to copy files to ../'.$_GET['image'].' . Thank you and goodbye.</font></b>';
}

*/

?>
