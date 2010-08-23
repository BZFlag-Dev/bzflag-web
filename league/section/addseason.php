<?php // $Id: addseason.php,v 1.3 2006/08/31 22:29:55 dennismp Exp $ vim:sts=2:et 

define (DUP_CHECK_DAYS, 3);
define (DAY_SECS, 60*60*24);

define (PERM_ADDSEASON,       'add_season');
define (PERM_CHANGESEASON,    'change_season');
define (PERM_DELETESEASON,    'delete_season');
define (PERM_FINISHSEASON,    'finish_season');
define (PERM_RECALCSEASON,    'finish_season');
define (PERM_ACTIVATESEASON,  'activate_season');

//    The score is calculated as:
//    win * POINTS_WIN + draw * POINTS_DRAW + lost * POINTS_LOSS
//    This field is also used in QUERY_RANKINGORDER to determin
//    the ranking order of the league.
define (FIELD_SCORE,          '(ts.won * s.points_win + ts.draw * s.points_draw + ts.lost * s.points_lost)');


//    The best team is first the one with the best overall score,
//    which is calculated out of the wins, lost and draw fields
//    Note that the actual score is never saved, but calculated
//    from the values in config.php (see FIELD_SCORE).
//    For two teams with the same score, the next value considered
//    is their zelo improvement during the season. Meaning that 
//    those teams with the hardest matches will win.
//    If this is the same (very unlikely) then the numbers of matches
//    won, draws and as the last, the total amount of matches.
//    NOTE: if you change this, also change in season.php
define (QUERY_RANKINGORDER,   'ORDER BY score desc, won desc, draw desc, zelo desc');

//    Most active team is simply teh team with the most matches.
define (QUERY_ACTIVEORDER,    'ORDER BY matches desc');


/*** args:
state:  null or 0  : initial
        1 : post request
        2 : post, already warned  (this should be post i/o get !!)
teama, teamb, scorea, scoreb, dte, tme valued if state==1 or 2
season_id: valued if this is an edit of an existing match record
del: submit / delete match
***/

//__________________________________________________________________
//
//                                   section_addseason_permissions()
//__________________________________________________________________

function section_addseason_permissions() 
{
  return array(
    PERM_ADDSEASON      => 'Define Seasons',
    PERM_CHANGESEASON   => 'Change Season',
    PERM_DELETESEASON   => 'Delete Season',
    PERM_FINISHSEASON   => 'Finish Season',
    PERM_ACTIVATESEASON => 'Activate Season',
  );
}

//__________________________________________________________________
//
//                              section_addseason_checkMaintenance()
//__________________________________________________________________

function section_addseason_checkMaintenance()
{
  if (!($row  = mysql_fetch_object (sqlQuery("select text from bzl_siteconfig where name='maintenance'"))))
    return;
  $last = strtotime($row->text);
  if ($last<0 || (mktime() - $last) > 24*60*60){
require_once ('lib/maintfuncs.php');
// it's time to run maintenance ...
site_maintenance ('invoked during defining seasons', false );
echo '<BR><font size=-1>(Site Maintenance performed)</font><BR>';
}
}

//__________________________________________________________________
//
//                                               section_addseason()
//__________________________________________________________________

function section_addseason()
{
require_once('lib/common.php');
$vars = array('season_id','startdate','enddate','state','func');
foreach($vars as $var)
$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

//- debug only
if (false)
{
echo '<BR>DEBUG<BR>';
echo 'state=' . $state . '<BR>';
echo 'season_id=' . $season_id . '<BR>';
echo 'func=' . $func . '<BR>';
//echo 'start=' . $startdate. ' - ' . strtotime($startdate) . '<BR>';
//echo 'end=' . $enddate . ' - ' . strtotime($enddate) . '<BR>';
//echo 'last=' . section_addseason_getSeasonEnd() . '<BR>';
}

//- catch the kids
if (!isFuncAllowed (PERM_ADDSEASON))
return errorPage ('You are not authorized to define seasons');
if ($season_id)
{
if ($func=="delete" && !isFuncAllowed (PERM_DELETESEASON))
  return errorPage ('You are not authorized to delete a season');
if ($func =="edit" && !isFuncAllowed (PERM_CHANGESEASON))
  return errorPage ('You are not authorized to edit a season');
if ($func =="activate" && !isFuncAllowed (PERM_ACTIVATESEASON))
  return errorPage ('You are not authorized to activate a season');
if ($func =="finish" && !isFuncAllowed (PERM_FINISHSEASON))
  return errorPage ('You are not authorized to finish a season');
if ($func =="recalc" && !isFuncAllowed (PERM_RECALCSEASON))
  return errorPage ('You are not authorized to have a season recalculated');
} 

echo '<BR><CENTER>';
$objSeason = null;
if ($season_id)
{
$objSeason = sqlQuerySingle("select * from l_season where id = '$season_id'");
if($startdate == '')
  $startdate = $objSeason->startdate;
if($enddate == '')
  $enddate = $objSeason->enddate;
echo "<h3>" . $func . " season: " . $objSeason->startdate . " - " . $objSeason->enddate . "</h3>";
}

if($state==1 || $state==2) 
{

if (!$season_id)
{
  echo "<h3>adding season: " . $startdate . " - " . $enddate . "</h3>";
}
  
//- some mental checks
if (strtotime($startdate) == -1)
  section_addseason_dispErr('Start date ' . $startdate . ' is not a proper date!');
else if (strtotime($enddate) == -1)
  section_addseason_dispErr('End date ' . $enddate. ' is not a proper date!');
else if (strtotime($enddate) <= strtotime($startdate))
  section_addseason_dispErr('Start date is later than end date, are you mental?');
  
//__________________________________________________________________
//
// Ask if you are allright
//__________________________________________________________________

else if ($state==1)
{
  //__________________________________________________________DELETE
  if ($func == "delete")
  {
    // TODO: should check if season can be deleted
    section_addseason_dispSeason ("Are you sure you <BR>want to DELETE <BR>this season?",$startdate, $enddate);
    section_addseason_confirmForm ($func,$season_id,$startdate, $enddate);
    section_addseason_dispAllSeasons($state,$season_id);
    return;
  }
  //__________________________________________________________ACTIVATE
  else if ($func == "activate")
  {
    // TODO: should check if season can be activates
    section_addseason_dispSeason ("Are you sure you <BR>want to ACTIVATE <BR>this season?",$startdate, $enddate);
    section_addseason_confirmForm ($func,$season_id,$startdate, $enddate);
    section_addseason_dispAllSeasons($state,$season_id);
    return;
  }
  //__________________________________________________________FINISH
  else if ($func == "finish")
  {
    // TODO: should check if season can be finished
    if (!$objSeason)
      section_addseason_dispErr('No valid season selected!');
    else if ($objSeason->active != "yes")
      section_addseason_dispErr('Selected season is not active!');
    else
    {
      $warns=array();
      $days_left = section_addseason_diff_time2days(nowDate(),$enddate);
      if ($days_left > 0)
      {
        if ($days_left > 1)
          $warns[] = 'There are still ' . $days_left . ' left in the season!';
      }
      else
      {
        if ($days_left < -1)
          $warns[] = 'You are already ' . (-$days_left) . ' late, so hurry!';
      }
      section_addseason_dispWarns ($warns);
      section_addseason_dispSeason ("Are you sure you <BR>want to FINISH <BR>this season?",$startdate, $enddate);
      section_addseason_confirmForm ($func,$season_id,$startdate, $enddate);
      section_addseason_dispAllSeasons($state,$season_id);
      return;
    }
  }
  //__________________________________________________________RECALC
  else if ($func == "recalc")
  {
    // TODO: should check if season can be finished
    if (!$objSeason)
      section_addseason_dispErr('No valid season selected!');
    else if ($objSeason->active == "yes")
      section_addseason_dispErr('Selected season is active!');
    else if ($objSeason->finished == "no")
      section_addseason_dispErr('Selected season is not finished!');
    else if ($objSeason->dirty != "yes")
      section_addseason_dispErr('The selected season does not need to be recalculated!');
    else
    {
      section_addseason_dispSeason ("Are you sure you <BR>want to RECALCULATE <BR>this season?",$startdate, $enddate);
      section_addseason_confirmForm ($func,$season_id,$startdate, $enddate);
      section_addseason_dispAllSeasons($state,$season_id);
      return;
    }      
  }
  //__________________________________________________________ADD-EDIT
  else //if ($func == "add" || $func == "edit")
  {
    if (strtotime($startdate) < strtotime(nowDate()))
      section_addseason_dispErr('Cannot start a season in the past!');
    else if (!section_addseason_checkDate($startdate,$season_id))
    {}
    else if (!section_addseason_checkDate($enddate,$season_id))
    {}
    else if (strtotime($enddate) < strtotime(nowDateTime()))
    {
      section_addseason_dispErr("Date " . $enddate . " is in the past!");
    }
    else
    {
      $warns=array();
      #if ((strtotime($startdate) - section_addseason_getSeasonEnd()) > 24*60*60*3)
          #  $warns[] = 'The season starts ' . section_addseason_time2days(strtotime($startdate) - section_addseason_getSeasonEnd()) . ' after the last season ends.';
          #if (section_addseason_diff_time2days($startdate,$enddate) < (DEFAULT_SEASON_LENGTH - 5))
          #  $warns[] = 'The season is shorter than normally, usually it is ' . DEFAULT_SEASON_LENGTH . ' days.';
          #if (section_addseason_diff_time2days($startdate,$enddate) > (DEFAULT_SEASON_LENGTH + 5))
          #  $warns[] = 'The season is longer than normally, usually it is ' . DEFAULT_SEASON_LENGTH . ' days.';        
          section_addseason_dispWarns ($warns);
          if ($season_id)
            section_addseason_dispSeason ("Are you sure you <BR>want to CHANGE <BR>this season?",$startdate, $enddate);
          else
            section_addseason_dispSeason ("Are you sure you <BR>want to ADD <BR>this season?",$startdate, $enddate);
          section_addseason_confirmForm ($func,$season_id,$startdate, $enddate);
          section_addseason_dispAllSeasons($state,$season_id);
          return;
        }
      }
    }

    //__________________________________________________________________
    //
    // Perfom the action
    //__________________________________________________________________
    
    else if($state==2)
    {
      $s_playerid = $_SESSION['playerid'];

      //__________________________________________________________DELETE
      if ($season_id && $func == "delete")
      {
        if (!$objSeason)
          section_addseason_dispErr('No valid season selected!');
        #else if ($objSeason->active != "yes")
        #  section_addseason_dispErr('Selected season is not active!');
        else
        {
          sqlQuery("delete from l_season 
                    where 
                      id = $season_id");
          section_addseason_dispAllSeasons($state,$season_id);
          return;
        }
      }
      //____________________________________________________________EDIT
      else if ($season_id && $func == "edit")
      {
        if (!$objSeason)
          section_addseason_dispErr('No valid season selected!');
        else if ($objSeason->active != "yes")
          section_addseason_dispErr('Selected season is not active!');
        else
        {
          sqlQuery("update l_season 
                    set
                      startdate = '$startdate',
                      enddate   = '$enddate',
                      fdate     = '$enddate',
                      idchange  = $s_playerid
                    where 
                      id = $season_id");
          echo "<div>done - check error messages</div>";
          section_addseason_dispAllSeasons($state,$season_id);
          return;
        }
      }
      //_____________________________________________________________ADD
      else if ($func == "add")
      {
        // add a season                
        sqlQuery("insert into l_season (identer,startdate, enddate,fdate,seasontype,points_win,points_draw,points_lost) 
                              values   ($s_playerid,'$startdate', '$enddate','$enddate','league',".POINTS_WIN.",".POINTS_DRAW.",".POINTS_LOSS.")");
        echo "<div>done - check error messages</div>";
        section_addseason_dispAllSeasons($state,$season_id);
        return;
      }
      //________________________________________________________ACTIVATE
      else if ($season_id && $func == "activate")
      {        
        if (!$objSeason)
          section_addseason_dispErr('No valid season selected!');
        else if ($objSeason->active == "yes")
          section_addseason_dispErr('Selected season is already active!');
        else
        {
          section_addseason_activateSeason($objSeason);
          echo "<div>done - check error messages</div>";
          section_addseason_dispAllSeasons($state,$season_id);
          return;
        }
      }
      //__________________________________________________________FINISH
      else if ($season_id && $func == "finish")
      {
        if (!$objSeason)
          section_addseason_dispErr('No valid season selected!');
        else if ($objSeason->active != "yes")
          section_addseason_dispErr('Selected season is not active!');
        else
        {
          section_addseason_finishSeason($objSeason);
          echo "<div>done - check error messages</div>";
          section_addseason_dispAllSeasons($state,$season_id);
          return;
       }
      }
      //__________________________________________________________RECALC
      else if ($season_id && $func == "recalc")
      {
        if (!$objSeason)
          section_addseason_dispErr('No valid season selected!');
        else if ($objSeason->finished != "yes")
          section_addseason_dispErr('Selected season is not finished!');
        else if ($objSeason->dirty != "yes")
          section_addseason_dispErr('Selected season does not require recalculation!');
        else
        {
          section_addseason_finishSeason($objSeason,1);
          echo "<div>done - check error messages</div>";
          section_addseason_dispAllSeasons($state,$season_id);
          return;
       }
      }
      //___________________________________________________________ERROR
      else
      {
        section_addseason_dispErr('Internal error');
        return;
      }
    }
    else
      return;
    
    $season_id = null;
    $startdate = '';
    $enddate = '';
    $state = 0;
    $func = null;
  }
  else
  {
    if (!$season_id)
    {
      echo "<h3>add season</h3>";
    }
    $state = 0;
  }


  if ($season_id){    // this is an edit of an existing match
  }
  section_addseason_dispForm ($season_id, $startdate,$enddate,$state,$func);
  section_addseason_dispAllSeasons($state,$season_id);
}


//________________________________________________________________________________________
//
// DISPLAY
//________________________________________________________________________________________


//__________________________________________________________________
//
//                                      section_addseason_dispForm()
//__________________________________________________________________

function section_addseason_dispForm($season_id, $startdate,$enddate,$state,$func)
{
  // initialize values ....
  
  if (!$season_id)
    $func = 'add';
  
  // $startdate - start date
  //  [check:menotume] is this ok like that?  
  if ($startdate == '')
  {
    $startdate = time2string(section_addseason_getNewSeasonStart());
  }
  
  // $enddate - end date
  if ($enddate == '')
    $enddate = section_addseason_addDays($startdate,DEFAULT_SEASON_LENGTH);


  // enter form follows ....
  echo "<center><div style=\"border-width:3px;border-color:red\">";
  echo   "<TABLE width=400>";
  echo   "  <TR><TD colspan=6>";
  /*echo   'The new season may not start earlier than today.
          The default length of a season is ' . DEFAULT_SEASON_LENGTH . ' days.
          Pause between leagues should be ' . DEFAULT_SEASONPAUSE_LENGTH . ' days.
         ';*/
  echo   "  </TD></TR>";
  echo     "<form method=post action=\"index.php\">
             <TR><TD>
              <input type=hidden name=link value=addseason>
              <input type=hidden name=state value=1>
              <input type=hidden name=season_id value=$season_id>
              <input type=hidden name=func value=$func>";
  echo        snFormInit ();
  echo      "</td>";


  echo        "<TD>Start Date: <input type=text name=startdate size=10 maxlength=10 value=\"$startdate\"></td>";
  echo        "<TD>End Date: <input type=text name=enddate size=10 maxlength=10 value=\"$enddate\"></nobr> </td>";
  if ($season_id)
    echo     '<td align=center colspan=2>' . htmlFormButton('Apply', '') . '</td>';
  else
    echo     '<td align=center  colspan=2>'. htmlFormButton ('Add', '').'</td>';
  echo   "</tr></table></div>";
}

//__________________________________________________________________
//
//                                   section_addseason_confirmForm()
//__________________________________________________________________

function section_addseason_confirmForm($func,$season_id,$startdate, $enddate)
{
  // display confirm / cancel form      
  echo "<TABLE cellpadding=5><TR><TD>
      <form method=post action=\"index.php\">
      <input type=hidden name=link value=addseason>
      <input type=hidden name=state value=2>
      <input type=hidden name=func value=\"$func\">
      <input type=hidden name=season_id value=\"$season_id\">
      <input type=hidden name=startdate value=\"$startdate\">
      <input type=hidden name=enddate value=\"$enddate\">";
      echo snForm();
      echo htmlFormButton ('CONFIRM', '');
      echo "</form>
    </td><TD>
      <form method=post action=\"index.php\">
      <input type=hidden name=link value=addseason>
      <input type=hidden name=state value=0>
      <input type=hidden name=startdate value=\"$startdate\">
      <input type=hidden name=enddate value=\"$enddate\">";
      echo snForm();
      echo htmlFormButton ('Cancel', '', CLRBUT)
      .'</form>
    </td></tr></table><BR>';
}

//__________________________________________________________________
//
//                                    section_addseason_dispSeason()
//__________________________________________________________________

function section_addseason_dispSeason($msg,$startdate, $enddate)
{
  $days = section_addseason_time2days(strtotime($enddate) - strtotime($startdate));
  $days_paused = section_addseason_time2days(strtotime($startdate) - section_addseason_getSeasonEnd());
  echo "<TABLE align=center cellspacing=10>
        <TR>
          <TD width=200 align=right><font class=feedback>$msg</font></td><td width=300>";
  echo    "<div class='outbox'>
             <TABLE border=0 cellpadding=0 cellspacing=0>
              <TR><TD>Start Date:&nbsp;</td><TD>&nbsp;$startdate&nbsp;";
  if ($startdate == nowDate())
    echo  "(today)";
  echo    "                                                 </td></tr>
              <TR><TD>End Date:&nbsp;</td><TD>&nbsp;$enddate</td></tr>
              <TR><TD>Days:&nbsp;</td><TD>&nbsp;$days</td></tr>
              <TR><TD>Season Pause:&nbsp;</td><TD>&nbsp;$days_paused</td></tr>
             </table>
          </div>";
  echo   "</td></tr></table>";
}

//__________________________________________________________________
//
//                                       section_addseason_dispErr()
//__________________________________________________________________
//TODO: Style for error box
function section_addseason_dispErr ($msg)
{
  echo "<CENTER><TABLE bgcolor=#cccccc border=1 cellpadding=5><TR><TD align=center>
        <font color=#660000 size=+1><B>ERROR<BR>$msg</b><BR>Try Again.</font></td></tr></table></center><BR>";
}

//__________________________________________________________________
//
//                                     section_addseason_dispWarns()
//__________________________________________________________________

function section_addseason_dispWarns(&$wa)
{
  if (($nw = count($wa)) <= 0)
    return;
  if ($nw > 1)
    $s="s";
  echo "<CENTER><TABLE width=60% border=1 cellpadding=3 class=\"warnbox\"><TR><TD>
        <TABLE align=center class=\"warnbox\" border=0><TR><TD colspan=2 align=center>
        <font><B style=\"text-decoration: blink\">WARNING$s:</b></font></td></tr>";
  for ($x=0; $x<$nw; $x++)
    echo "<TR style=\"warnbox\"><TD valign=top>*&nbsp;</td><TD>$wa[$x]</tr></tr>";
  echo  "</table></tr></td></table><p>";        
}


//__________________________________________________________________
//
//                                section_addseason_dispAllSeasons()
//__________________________________________________________________

function section_addseason_dispAllSeasons($state,$season_id)
{
  echo "<BR><hr>";
  if( isset($_GET['detail_level']) )
    $detail_level =  $_GET['detail_level'];
  else
    $detail_level = 1;
  if (false && $state == 0)
  {
    echo '<TABLE align=center class=insetForm><TR><TD>';
    echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR valign=middle><TD>
    <form action=\"index.php\" name=none>
    <input type=hidden name=link value=addseason>
    <input type=hidden name=state value=0>
    </td><TD>
    <select name=detail_level>";
      htmlOption (1, 'Coming Seasons', $detail_level);
      htmlOption (2, 'All Seasons', $detail_level);
    echo "</select>
    </td><TD width=15></td>";
    echo '<TD align=left>'. htmlFormButSmall ('Show me', '')
    .'</td></tr></table></td></tr></table></form>';
  }
  
  $table = sqlQuery("select s.id, s.startdate,s.enddate,s.seasontype,s.points_win,s.points_draw,s.points_lost,s.active, s.finished, s.dirty,p1.callsign enteredBy,p2.callsign changedBy
                     from (l_season s )
                     left join l_player p1 on (p1.id = s.identer )
                     left join l_player p2 on (p2.id = s.idchange )
                     where s.id > 1
                     AND   s.seasontype='league' 
                     order by startdate asc
                     ");

  echo "<div>List of seasons:</div><table align=center cellspacing=0 cellpadding=5><tr class=tabhead>
        <td>Pos.</td><td>Status</td><td>Start&nbsp;Date</td><td>End&nbsp;Date</td><td>Length</td><td>Type</td><td colspan=3>W/L/T</td><td>Entered&nbsp;By</td><td>Changed&nbsp;By</td><td></td><td></td><td></td></tr>";
  
  $rownum=0;
  $now = nowDate();
  $last = $now;
  $is_old =  true;
  $may_finish_season   = isFuncAllowed(PERM_FINISHSEASON);
  $may_change_season   = isFuncAllowed(PERM_CHANGESEASON);
  $may_delete_season   = isFuncAllowed(PERM_DELETESEASON);
  $may_activate_season = isFuncAllowed(PERM_ACTIVATESEASON);

  while($obj = mysql_fetch_object($table)) 
  {
    $time_past    = strtotime($obj->enddate) - strtotime($now);
    $time_length  = (int)section_addseason_diff_time2days($obj->startdate,$obj->enddate);
    $is_pending   = $is_old && (strtotime($obj->enddate) > strtotime($now))?true:false;
    $is_old       = strtotime($obj->enddate) < strtotime($now)?true:false;
    $active       = $obj->finished == 'yes'?"finished"
                  : ($obj->active   == 'yes'?"active"
                  : ($is_pending            ?"pending":""));
    $c = $rownum++%2?'rowodd':'roweven';

    if ($season_id == $obj->id)    
      echo    "<tr class=$c style=\"background-color:darkred;color:white; \">";
    else
      echo    "<tr class=$c>";

    echo    "<td>" . $rownum . "</td>";
    if ($is_pending)
      echo    "<td align=center><B style=\"text-decoration: blink\">" . $active . "</b></td>";
    else
      echo    "<td align=center>" . $active . "</td>";
    echo    "<td>" . $obj->startdate . "</td>";
    echo    "<td>" . $obj->enddate . "</td>";
    echo    "<td align=center>" . $time_length . "</td>";                             // Length
    echo    "<td>" . $obj->seasontype . "</td>";
    echo    "<td align=center>" . $obj->points_win . "</td>";
    echo    "<td align=center>" . $obj->points_lost . "</td>";
    echo    "<td align=center>" . $obj->points_draw . "</td>";
    echo    "<td>" . $obj->enteredBy . "</td>";
    echo    "<td>" . $obj->changedBy . "</td>";
    // buttons
    
    if ($state == 0 && $obj->finished == 'no')
    {
      #echo    "<td>" . htmlURLbutSmall ('EDIT', 'addseason', "state=0&func=edit&season_id=$obj->id", ($may_change_season && !$is_old)?ADMBUT:DISBUT) . "</td>";
      echo    "<td>" . htmlURLbutSmall ('EDIT', 'addseason', "state=0&func=edit&season_id=$obj->id", ADMBUT) . "</td>";
      #echo    "<td>" . htmlURLbutSmall ('DELETE', 'addseason', "state=1&func=delete&season_id=$obj->id", ($may_delete_season && !$is_old)?ADMBUT:DISBUT) . "</td>";
      echo    "<td>" . htmlURLbutSmall ('DELETE', 'addseason', "state=1&func=delete&season_id=$obj->id", ADMBUT) . "</td>";
      if ($may_finish_season && $obj->active == "yes")
        echo  "<td>" . htmlURLbutSmall ('FINISH', 'addseason', "state=1&func=finish&season_id=$obj->id", ADMBUT) . "</td>";
      else if($may_activate_season && ($is_pending || $is_old)  )
        echo  "<td>" . htmlURLbutSmall ('ACTIVATE', 'addseason', "state=1&func=activate&season_id=$obj->id", ADMBUT) . "</td>";
      else
        echo  "<td>" . "" . "</td>";
    }            
    else if ($state == 0 && $may_finish_season && $obj->finished == "yes" && $obj->dirty == "yes")
      echo  "<td></td><td></td><td>" . htmlURLbutSmall ('RECALC', 'addseason', "state=1&func=recalc&season_id=$obj->id", ADMBUT) . "</td>";
    else
      echo  "<td></td><td></td><td></td>";

    echo    "</tr>";
    $last = $obj->enddate;
  }
  echo "</table>";
}

//________________________________________________________________________________________
//
// THE YEAH SAYERS
//________________________________________________________________________________________


//__________________________________________________________________
//
//                                section_addseason_activateSeason()
//__________________________________________________________________
//
//  Arguments:    objSeason - a season record with all field in the 
//                            database
//
//  Returns:      none
//  Description:
//__________________________________________________________________

function section_addseason_activateSeason($objSeason)
{
  $now = nowDate();
  if (strtotime($now) > strtotime($objSeason->startdate))
    $now = $objSeason->startdate;
  $s_playerid = $_SESSION['playerid'];
  sqlQuery("update l_season 
            set
              active = 'yes',
              startdate = '$now',
              idchange = $s_playerid,
              paused = 'no'
            where 
              id = $objSeason->id");
}

//__________________________________________________________________
//
//                                  section_addseason_finishSeason()
//__________________________________________________________________
//
//  Arguments:    objSeason - a season record with all field in the 
//                            database
//                recalc    - none null if it is a recalculation of
//                            an already finished season
//
//  Returns:      none
//  Description:  Finishes off a season, calculates the winners of
//    the season etc etc.
//    How is the team order deteremid: check for QUERY_RANKINGORDER
//    The best team is first the one with the best overall score,
//    which is calculated out of the wins, lost and draw fields
//    Note that the actual score is never saved, but calculated
//    from the values in config.php (see FIELD_SCORE).
//    For two teams with the same score, the next value considered
//    is their zelo improvement during the season. Meaning that 
//    those teams with the hardest matches will win.
//    If this is the same (very unlikely) then the numbers of matches
//    won, draws and as the last, the total amount of matches.
//  
//    Once a season is finished, changing the values POINTS_LOSS,
//    POINTS_DRAW and POINTS_WIN has no more effect and is saved
//    in the l_season table.
//__________________________________________________________________

function section_addseason_finishSeason($objSeason,$recalc = null)
{
  $fdate = nowDateTime();
  $now = nowDate();
  $s_playerid = $_SESSION['playerid'];
  $ranking = array();
  $score = array();
  $most_active = null;

  //- get top 3 teams
  $res = sqlQuery("SELECT ts.team, " . FIELD_SCORE . " score FROM l_teamscore ts,l_season s 
                   WHERE s.id = $objSeason->id 
                     AND ts.season = $objSeason->id ".
                   QUERY_RANKINGORDER . " limit 3");
  while($obj = mysql_fetch_object($res)) 
  {
    $ranking[] = $obj->team;
    $score[] = $obj->score;
  }
  //- get most active team
  $res = sqlQuerySingle("SELECT team FROM l_teamscore WHERE
                         season = $objSeason->id ".
                         QUERY_ACTIVEORDER . " limit 1");
  $most_active = $res?$res->team:null;

  
  //- finish it off
  if (strtotime($fdate) > strtotime($objSeason->enddate))
  {
    $fdate = $objSeason->enddate;
    $now   = $objSeason->enddate;
  }    
  sqlQuery("update l_season set 
             enddate    = '$now',
             fdate      = '$fdate',
             active     = 'no',
             finished   = 'yes',
             dirty      = 'no',
             idchange   = $s_playerid,
             position1  = null,
             score1     = null,
             position2  = null,
             score2     = null,
             position3  = null,
             score3     = null,
             mostactive = null
             where id = $objSeason->id");  
  //- set winners
  if ($ranking[0])
    sqlQuery("update l_season set position1 = $ranking[0],score1 = $score[0] where id = $objSeason->id");  
  if ($ranking[1])
    sqlQuery("update l_season set position2 = $ranking[1],score2 = $score[1] where id = $objSeason->id");  
  if ($ranking[2])
    sqlQuery("update l_season set position3 = $ranking[2],score3 = $score[2] where id = $objSeason->id");  
  if ($most_active)
    sqlQuery("update l_season set mostactive = $most_active where id = $objSeason->id");  
  sqlQuery("update l_season set active = 'yes' where enddate = null");  
}

//________________________________________________________________________________________
//
// AUXILIARY
//________________________________________________________________________________________

function section_addseason_time2days($time)
{
  return (int)($time / (24*60*60));
}

function section_addseason_diff_time2days($timestart,$timeend)
{
  return section_addseason_time2days(strtotime($timeend) - strtotime($timestart));
}

function section_addseason_getSeasonEnd()
{
  //- returns the enddate of the last season as time (Seconds since 1970)
  $date  =null;
  {
    $res = sqlQuerySingle("select MAX(enddate) enddate from l_season where enddate > now()");
    if ($res != null && $res->enddate != null)
    {
      $date = $res->enddate;
      $date = strtotime($date);
    }
    else
    {
      $date = strtotime(nowDate())+12*60*60;
    }
  }
  return $date;
}

function section_addseason_getNewSeasonStart()
{
  //- returns the enddate of the last season as time (Seconds since 1970)
  $date  =null;
  {
    $res = sqlQuerySingle("select MAX(enddate) enddate from l_season where enddate > now()");
    if ($res != null && $res->enddate != null)
    {
      $date = $res->enddate;
      $date = section_addseason_addDays($date,DEFAULT_SEASONPAUSE_LENGTH+1);
      $date = strtotime($date);
    }
    else
    {
      $date = strtotime(nowDate())+12*60*60;
    }
  }
  return $date;
}


function section_addseason_addDays($date,$days)
{
  //- adds number of days to date
  //  date is given as string date, and is returned as string date
  return time2string(strtotime($date) + 60*60*24*($days));
}

function section_addseason_checkDate($date,$season_id = null)
{
  if ($season_id)  
    $table = sqlQuery("select * from l_season where id <> $season_id and startdate <= '$date' and enddate >= '$date'");
  else
    $table = sqlQuery("select * from l_season where startdate <= '$date' and enddate >= '$date'");
  while($obj = mysql_fetch_object($table)) 
  {
    section_addseason_dispErr("Date " . $date . " clashes with season " . $obj->startdate . " - " . $obj->enddate ."!");
    return false;
  }
  return true;
}

function time2string($time)
{
  return gmdate('Y-m-d',$time);
}
?>
