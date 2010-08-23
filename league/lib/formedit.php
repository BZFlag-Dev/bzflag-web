<?php // $Id: formedit.php,v 1.4 2005/04/20 17:30:45 menotume Exp $ vim:sts=2:et:sw=2

// TODO: Serial numbers (dup check)
//      edit states / confirmation stage
//      button panel


/***
  'reserved' CGI vars:  edt_st (edit state)   
      edt_can
      edt_del
***/

define (DISALLOWCHARS, "<>'\"\\");


define (FESTATE_INITIAL, 0);
define (FESTATE_PRESENT, 1);
define (FESTATE_SUBMIT,  2);
define (FESTATE_CONFIRM, 3);

class FormEdit {
  var $edt_snck;
  var $edt_can;
  var $edt_del;
  var $edt_st;      // CGI ('next' state) initial, present, submit, confirm

  var $helpEnabled = false;   
  var $row=array();     // original 'sql' data
  var $varList=array();

var $lcl_state;     // current state
var $function;      // display only, edit, add, delete
var $lcl_errmsgs = array();



  // CONSTRUCTOR ...
  function FormEdit (){
    $this->edt_snck = $_SESSION['seqnum'];
    $this->setThisVars($_GET);
    $this->setThisVars($_POST);
    if (!isset ($this->edt_st))
      $this->lcl_state = $this->edt_st = FESTATE_INITIAL;
    else 
      $this->lcl_state = $this->edt_st;

  }


  function setDataRow ($row){
    $this->row = $row;
    if ($this->edt_st!=FESTATE_CONFIRM && $this->edt_st!=FESTATE_SUBMIT)
      $this->setThisVars($row);
  }


  // returns false if any of the args are not present
  function checkRequired ($reqArgs){
    for ($x=0; $x<count($reqArgs); $x++){
      if ( ! $this->$reqArgs[$x]  )
        return false;
    }
    return true;
  }


  function revert ($name){
    $this->$name = $this->row[$name];
  }


  function setThisVars ($a){
    $k = array_keys ($a);
    for ($x=0; $x<count($k); $x++){
      $this->$k[$x] = $a[ $k[$x] ]; 
      $this->varList[] = $k[$x];
    }
  }


  function getCurrentState (){
    return $this->lcl_state;
  }

  function getNextState (){
    return $this->edt_st;
  }


  function setCurrentState ($s){
    $this->lcl_state = $s;
  }

  function setNextState ($s){
    $this->edt_st = $s;
  }

  function isError ( ){
    if (count($this->lcl_errmsgs) > 0)
      return true;
    return false;
  }

  function setError ($name, $msg){
    $this->lcl_errmsgs[$name] = $msg;
  }

  function fieldsChanged ($list){
    $a = explode (",", $list);
    for ($x=0; $x<count($a); $x++){
      $n = trim($a[$x]);    
      if (isset($this->$n) && $this->$n != $this->row[$n]){
        $v = addslashes($this->$n);
        if ($s)
          $s .= ", $n='$v'";
        else 
          $s = "$n='$v'";
      }
    }
    return $s;  
  }
  
  function isChanged ($name){
    if (isset($this->$name) && $this->$name != $this->row[$name])
      return true;  
    return false; 
  }

  function oldVal ($name){
    return $this->row[$name];
  }

  function trimAll (){
    $x = count($this->varList);
    while (--$x>=0){
      $n = $this->varList[$x];
      $this->$n = trim ($this->$n);   
    }
  }

  function stripAll (){
    $x = count($this->varList);
    while (--$x>=0){
      $n = $this->varList[$x];
      $this->$n = stripslashes ($this->$n);   
    }
  }

  function feedback ($s){
    return "<CENTER><TABLE align=center class='feedback'><TR><TD>$s<HR></td></tr></table>";
  }

  
/****
  function formHelp ($width, $height, $style){
    $this->helpEnabled=true;
    echo '<script language="JavaScript" type="text/javascript">
      function helpOn(htxt){
        document.getElementById("helptext").innerHTML = htxt;
      }
      function helpOff(){
        document.getElementById("helptext").innerHTML="Select an edit field to display help here.";
      }
      </script>';

    echo "<TABLE class='$style' height=$height width=$width><TR><TD valign=top> 
      <SPAN id='helptext'>Select an edit field to display help here.</SPAN> 
      </td></tr></table>";

//onFocus=\"helpOn('Test help for <b>callsign</b> here')\"
//          onBlur=\"helpOff()\"

  }
***/

  function _fieldLabel ($name, $label){
    if ($this->lcl_errmsgs[$name])
      return "<div class=errtxt><nobr><b>$label:&nbsp;</b></nobr></div>";
    else
      return "<nobr><b>$label:&nbsp;</b></nobr>";
  }
  
  function formText ($name, $label, $dispWidth, $maxWidth, $style=null){
    $cl=$style?" class='$style'":'';
    echo '<TR><TD align=right>'. $this->_fieldLabel($name, $label) ."</td>
      <TD><INPUT type=text name=$name 
        size=$dispWidth maxlength=$maxWidth value='{$this->$name}'$cl></td></tr>\n";
  }

  function formTextArea ($name, $label, $cols, $rows, $style=null){
    $cl=$style?" class='$style'":'';
    echo "<TR><TD align=right valign=top>". $this->_fieldLabel($name, $label) ."</td><TD><textarea name=$name 
        cols=$cols rows=$rows$cl>{$this->$name}</textarea></td></tr>";
  }

  function formPassword ($name, $label, $dispWidth, $maxWidth, $style=null){
    $cl=$style?" class='$style'":'';
    echo "<TR><TD align=right>". $this->_fieldLabel($name, $label) ."</td><TD><INPUT type=password name=$name 
        size=$dispWidth maxlength=$maxWidth value='{$this->$name}'$cl></td></tr>\n";
  }


  function formCheckbox ($name, $label, $trueVal, $falseVal, $alt='', $style=null){
    echo "<input type=hidden name=$name value=$falseVal>";
    $cl=$style?" class='$style'":'';
    $ck=$this->$name==$trueVal?' checked="checked"':'';
    echo "<TR><TD align=right>". $this->_fieldLabel($name, $label) ."</td><TD>
    <input type=checkbox name=$name value=\"$trueVal\" $cl$ck>&nbsp;$alt
      </td></tr>";
  }



  function formSelector ($name, $label, $sqlQuery, $array=null, $alt=null, $style=null){
    $cl=$style?" class='$style'":'';
    echo '<TR><TD align=right>'. $this->_fieldLabel($name, $label) ."</td><TD><select name=\"$name\"$cl>";
    if ($array){
      $k = array_keys ($array);
      for ($x=0; $x<count($k); $x++){
      $val = $array[$k[$x]];
        $ss = strcasecmp ($this->$name, $val) ? '' : 'selected';
        echo "<option value=\"$val\"$ss>{$k[$x]}</option>";
      }
    }
    if ($sqlQuery){
      $set = sqlQuery ($sqlQuery);
      while ($row=mysql_fetch_array ($set)){
        $ss = $this->$name==$row[1]?'selected':'';
        echo "<option value=\"$row[1]\"$ss>$row[0]</option>";
      }
    }
    echo '</select>';
    if ($alt)
      echo"&nbsp;&nbsp;$alt";
    echo '</td></tr>';
  }


  function formStart ($hiddens, $formname=null, $formstyle=null){
    // display errors if any ...
    if (($ne = count($this->lcl_errmsgs)) > 0){
      echo "<CENTER><TABLE width=80% class=errbox cellpadding=5>
        <TR><TD class=errtitle align=center>Please fix the following error(s):<BR></td></tr>
        <TR><TD>";
      $k = array_keys ($this->lcl_errmsgs);
      for ($x=0; $x<$ne; $x++)
        echo "<LI class=errlist> {$this->lcl_errmsgs[$k[$x]]} ";
      echo "</td></tr></table><BR><BR>";
    }

    echo "<table class='$formstyle' align=center border=0 cellspacing=0 cellpadding=1>";
    echo "<form method=post name='$formname'>
      <input type=hidden name=\"edt_snck\" value=\"$this->edt_snck\">
      <input type=hidden name=edt_st value='$this->edt_st'>\n";
    for ($x=0; $x<count($hiddens); $x++)
      echo "<input type=hidden name=$hiddens[$x] value='{$this->$hiddens[$x]}'>\n";
  }


  function formRow ($text, $align='center'){
    echo "<TR><TD colspan=2 align=$align>$text</td></tr>";
  }
  
  function formDescript ($text, $style=null){
    echo "<TR><TD></td><TD class=$style>$text</td></tr>";
  }
  
  function formEnd (){
    echo "</table></form>";
  }


  function validateName ($n, $msg, $dc=null){
    if ($dc==null)
      $dc = DISALLOWCHARS;
    if ($n=='')
      return $msg . " can't be empty";
    if (stristr ($n, "delete") !== FALSE)
      return $msg ." can't contain the word delete.";
    if (strcspn($n, $dc) != strlen ($n)){
      $msg .= " can't contain any of these characters: ";
      for ($x=0; $x<strlen($dc); $x++)
        $msg .= "{$dc{$x}} ";
      return htmlentities ($msg);
    }
    return '';
  }



  // syntax check only :)   *@*.*
  function validateEmail ($e){
    if (($x = strpos ($e, '@')) < 1)
      return false;
    if (($x = strpos ($e, '.', $x+2)) < 1)
      return false;
    if ($x > strlen($e)-3)
      return false;
    return true;
  }
  
}



?>

