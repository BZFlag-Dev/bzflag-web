<?php // $Id: permissions.php,v 1.11 2006/08/29 22:20:48 dennismp Exp $ vim:sts=2:et:sw=2
require_once('lib/modules.php');

define('SECTION_DIR', './section/');

function section_permissions()  {
  if( isFuncAllowed('permissions') ) {
    if( isset($_REQUEST['func']) && $_REQUEST['func'] == 'roles' ) {
      section_permissions_roles();
    }
    else {
      if( count($_POST['p']) ) section_permissions_update(&$_POST['p']);
      section_permissions_makeTable();
    }
  }
  else {
    errorPage("No access.");
  }
}

function section_permissions_permissions() {
  return array('permissions' => 'Allow to change permission setup');
}

function section_permissions_roles() {
  $error = null;

  if( $_GET['action'] == 'performRename' ) {
    $newName = trim(addslashes($_GET['newname']));
    $id      = $_GET['id'];
  
    if(! is_numeric($id) ) {
      // Silent discard
    }
    elseif( ! section_permissions_validName($newName))  {
      $error = 'Rolenames may only consist of 0-9,a-z, spaces and underscores';
    }
    else {
      $res = mysql_query("
        UPDATE bzl_roles SET
          name = '$newName'
        WHERE id = '$id' 
        LIMIT 1
        ");
      if(!$res) {
        if( mysql_errno() == 1062 ) // DUPE
          $error = 'Rolename already exists. Must be unique';
        else
          $error = mysql_errno() . ':' . mysql_error();
      }
    }
  }
  elseif( $_GET['action'] == 'delete' ) {
    $id      = $_GET['id'];
    if( is_numeric($id) ) {
      $res = mysql_query("
        DELETE FROM bzl_roles 
        WHERE id = '$id' 
        LIMIT 1
        ");
      if(!$res) {
        $error = mysql_errno() . ':' . mysql_error();
      }
      else {
        // Removed permissions. 
        mysql_query("
          DELETE FROM bzl_permissions 
          WHERE role_id = '$id' 
        ");
      }
    }
  }
  elseif( $_GET['action'] == 'create' ) {
    $newName = trim(addslashes($_GET['newname']));
    if( ! section_permissions_validName($newName))  {
      $error = 'Rolenames only consist of a-z, 0-9, spaces and underscores';
    }
    else {
      $res = mysql_query("
        INSERT INTO bzl_roles (name)
        VALUES('$newName') 
        ");
      if(!$res) {
        if( mysql_errno() == 1062 ) // DUPE
          $error = 'Rolename already exists. Must be unique';
        else
          $error = mysql_errno() . ':' . mysql_error();
      }
    }
  }
  elseif( $_GET['action'] == 'performMove' ) {
    $id      = $_GET['id'];
    $to      = $_GET['to'];
    if( is_numeric($id) && is_numeric($to) ) {
      $res = mysql_query("
        UPDATE l_player  SET 
          role_id = '$to'
        WHERE
          role_id = '$id'
        ");
      if( mysql_errno() == 1062 ) // DUPE
        $error = 'Rolename already exists. Must be unique';
      else
        $error = mysql_errno() . ':' . mysql_error();
    }
  }


  $res = mysql_query( "
    SELECT r.id, r.name, count(p.role_id) as count
    FROM bzl_roles r 
    LEFT JOIN l_player p ON p.role_id = r.id
    WHERE (p.id IS NULL OR p.status != 'deleted')
    GROUP BY r.id
    ORDER BY name
  ") or die(mysql_error());
  
  if( $error ) print "<p><font color=\"red\">$error</font></p>";
  print "&nbsp;<br><table>";
  print "<tr><td>Role</td><td># of users</td></tr>";
  while($row = mysql_fetch_assoc($res)) {
    $renameLink = "index.php?link=permissions&func=roles&action=rename&id={$row['id']}";
    $deleteLink = "index.php?link=permissions&func=roles&action=delete&id={$row['id']}";
    $moveLink   = "index.php?link=permissions&func=roles&action=move&id={$row['id']}";
    $doRename =  $_GET['id'] == $row['id'] && $_GET['action'] == 'rename';
    $doMove   =  $_GET['id'] == $row['id'] && $_GET['action'] == 'move';

    print "<tr><td>";
    
    if( $doRename ) {
      print "<form method=\"get\" action=\"index.php\">";
      print "<input type=\"hidden\" name=\"link\" value=\"permissions\" />";
      print "<input type=\"hidden\" name=\"func\" value=\"roles\" />";
      print "<input type=\"hidden\" name=\"action\" value=\"performRename\" />";
      print "<input type=\"hidden\" name=\"id\" value=\"{$row['id']}\" />";
      print "<input type=\"text\" value=\"" . htmlspecialchars($row['name']) . "\" name=\"newname\"><input type=\"submit\" value=\"Ok\">";
      print "</form>";
    }
    else {
      print $row['name'];
    }
    
    print "</td><td align=\"center\">{$row['count']}</td>";

    $mandatory = in_array($row['id'], array( GUEST_PERMISSION, ADMIN_PERMISSION, NEW_USER_PERMISSION ) );

    // Commands
    print "<td>";
    print "<a href=\"$renameLink\">Rename</a> ";
    if( !$mandatory && $row['count'] == 0 ) print "<a href=\"$deleteLink\">Delete</a> ";
    if( !in_array($row['id'], array(ADMIN_PERMISSION,GUEST_PERMISSION) )) {
      if( $doMove ) {
         $roles     = array_flip(section_permissions_getRoles());    // Roles defined

        print "<form method=\"get\" action=\"index.php\">";
        print "<input type=\"hidden\" name=\"link\" value=\"permissions\" />";
        print "<input type=\"hidden\" name=\"func\" value=\"roles\" />";
        print "<input type=\"hidden\" name=\"action\" value=\"performMove\" />";
        print "<input type=\"hidden\" name=\"id\" value=\"{$row['id']}\" />";
        print "<select name=\"to\">";
        foreach($roles as $name => $id ) 
          print "<option value=\"$id\">$name</option>";
        print "</select>";
        print "<input type=\"submit\" value=\"Move\">";
        print "</form>";
      }
      else {
        if( $row['count'] ) 
          print "<a href=\"$moveLink\">Move Users</a> ";
      }
    }
    print "</td>";
    print "</tr>";
  }
  print "</table>";

  print "<p>Create new role</p>";
  print "<form method=\"get\" action=\"index.php\">";
  print "<input type=\"hidden\" name=\"link\" value=\"permissions\" />";
  print "<input type=\"hidden\" name=\"func\" value=\"roles\" />";
  print "<input type=\"hidden\" name=\"action\" value=\"create\" />";
  print "<input type=\"text\" name=\"newname\"><input type=\"submit\" value=\"Ok\">";
  print "</form>";
}

function section_permissions_update($data) {
  $data = array_keys($data);

  // Get all roles
  $roles     = array_flip(section_permissions_getRoles());    // Roles defined
  $first_id  = null;
  $ok        = true;

  foreach($data as $i) {
    list($perm,$role) = explode("@",$i);
    $perm = addslashes($perm);

    $sql = "INSERT INTO bzl_permissions (id, role_id, name) VALUES(NULL,{$roles[$role]},'$perm')";
    if(! mysql_query($sql) ) {
      print "ERROR: <b>$sql</b>: " . mysql_error() . "<br />";
    }
    else {
      // Record the ID of the first newly created setup
      if( $first_id === null ) $first_id = mysql_insert_id();

      $ok &= true;
    }
  }

  if( $ok && $first_id !== null ) {
    // Delete all rows, with ID lower than $first_id
    mysql_query("DELETE FROM bzl_permissions WHERE id < $first_id") or die(mysql_error());
  }
  else {
    // If something went wrong, do not touch anything. This might leave dupes in database
    // but it will be removed once this step have been completed successfully.
    print "Something bad happened. Please report this to the administrator.";
  }
}

function section_permissions_makeTable()  {
  $roles     = section_permissions_getRoles();    // Roles defined
  $allperm   = module_invoke_all('permissions');  // Permissions defined by modules
  $permsetup = section_permissions_getSetup();    // Current setup

  print "<p>Changes apply for future logins (users keep current setup, until their session ends).</p>";
  print "<form method=\"post\" action=\"index.php\">";
  print '<input type="hidden" name="link" value="permissions" />';
  print "<table border=1>";
  print "<tr><td colspan=2>Items</td>";
  foreach($roles as $r) 
    print "<td>$r</td>";
  print "</tr>";

  $lastmod = '';
  foreach($allperm as $mod => $perm) {
    if( is_array($perm) ) {
      foreach($perm as $permname => $permdesc) {
        if( $lastmod != $mod ) {
          print "<tr><td valign=\"top\">$mod</td>";
          $lastmod = $mod;
        }
        else {
          print "<tr><td></td>";
        }
        //print "<td><b>$permname</b>: $permdesc</td>";
        print "<td>$permdesc ($permname)</td>";
        foreach($roles as $rid => $r) {
          if( $rid == ADMIN_PERMISSION ) {
            print "<td align=\"center\">-</td>";
          }
          else {
            $fullname = "$mod::$permname@$r";
            if( isset($permsetup[$fullname])) 
              $checked = ' checked';
            else 
              $checked = '';
            print "<td align=\"center\"><input type=\"checkbox\" name=\"p[$fullname]\" $checked /></td>";
          }
        }
        print "</tr>";
      }
    }
  }
  print "<tr><td colspan=2></td><td align=\"center\" colspan=" . count($roles) . ">". htmlFormButton("Update", "commit") . "</td></tr>";
  print "</table></form>";
}

function section_permissions_getRoles() {
  // TODO caching
  $sql = "SELECT id, name FROM bzl_roles ORDER BY name";
  $res = mysql_query($sql) or die(mysql_error());
  $roles = array();
  while($row = mysql_fetch_assoc($res)) {
    $roles[$row['id']] = $row['name'];
  }

  return $roles;
}

function section_permissions_getSetup() {
  $sql =  "SELECT p.id, p.role_id, p.name pname, r.name rname ".
          "FROM bzl_roles r, bzl_permissions p ".
          "WHERE p.role_id = r.id ORDER BY r.name";
  $res = mysql_query($sql) or die(mysql_error());
  $setup = array();
  while($row = mysql_fetch_assoc($res)) {
    $setup[$row['pname'] . '@' . $row['rname']] = $row['id'];
  }
  return $setup;
}

function section_permissions_validName($str) {
  return preg_match('/^[a-z0-9_ ]+$/i', $str);
}
?>
