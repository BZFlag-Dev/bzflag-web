<?php // $Id: required.php,v 1.3 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

function section_required (){
  require_once ('lib/formedit.php');

  $se = new FormEdit ();
  $se->trimAll();
  $se->stripAll();

  if (($id = $_SESSION['playerid']) <= 0)
    return errorPage ("You're not supposed to be here!");

  if (!$se->checkRequired (array ('link')))
    return errorPage ('missing argument');

  echo '<BR>';

  $query="select id, country from l_player where id=$id";
  $se->setDataRow (mysql_fetch_assoc (sqlQuery ($query)));

  if ($se->f_ok_x){
    section_required_validate($se);
    if (!$se->isError()){
      section_required_doSubmit($se, $id);
      $se->setNextState(FESTATE_INITIAL);
      $se->setDataRow (mysql_fetch_assoc (sqlQuery ($query)));
      echo "<BR><DIV class=feedback><CENTER>Thank you.  You will not be asked for this information again.</div>";
      unset ($_SESSION['required']);
      return;
    }
  }
  $se->setNextState (FESTATE_SUBMIT);
  section_required_presentEditForm ($se);
}




function section_required_presentEditForm (&$se){
  echo "<CENTER><BR><div class=feedback>Before you may continue, you must fill out the following required 
    information:</div><BR>";
  $se->formStart (array (link, id), 'ppedit');
  $se->formRow ('<HR>');
  $se->formDescript ('Please enter your location below:<BR><BR>', ST_FORMDESC);
  $se->formSelector ('country', 'Country', 'select name, numcode from bzl_countries order by name', array('-- PLEASE SELECT --'=>-1), null, ST_FORMREQ );
  echo '<tr><td align=center colspan=2><BR>'. htmlFormButton ('Submit', 'f_ok_x') . '&nbsp;&nbsp;' .'</td></tr>';
  $se->formEnd();
}



function section_required_validate (&$se){
  if ($se->country < 0) 
    $se->setError ('country', "MUST SELECT A COUNTRY!");
  if ($se->country == 0) 
    $se->setError ('country', "Nice try, Please select your REAL Country!");
}


function section_required_doSubmit (&$se, $id){
  sqlQuery ("UPDATE l_player SET country=$se->country WHERE id=$id");
  return true;
}

?>
