<?php require_once('../Connections/tams.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}

if (isset($_SESSION['MM_Username']) && !isset($_SESSION['stid'])) {
    doLogin(2, $_SESSION['MM_Username'], NULL, $tams, true);
}
require_once('../param/param.php');
require_once('../functions/function.php');

$MM_authorizedUsers = "10";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['MM_Username'])) && 
        (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

$acl = array(2,3,5);

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_tams, $tams);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    
    // Level column removed for inconsistency with online database
 $insertSQL = sprintf("INSERT INTO registration (stdid, sesid, status, course) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['stid'], "text"),
                       GetSQLValueString($_POST['sid'], "int"),
                       GetSQLValueString("Registered", "text"),
                       GetSQLValueString("Unregistered", "text"));

  
  $Result = mysql_query($insertSQL, $tams) or die(mysql_error());
 
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	if( isset($_POST['ref'])){
		foreach($_POST['ref'] AS $ref) {
                    $ref= htmlentities($ref);
                        
                    $insertSQL = sprintf("INSERT INTO result (stdid, csid, sesid) VALUES (%s, %s, %s)",
							   GetSQLValueString($_POST['stid'], "text"),
							   GetSQLValueString($ref, "text"),
							   GetSQLValueString($_POST['sid'], "int"));
		
                    $Result = mysql_query($insertSQL, $tams) or die(mysql_error());
		}
	}
        
	foreach($_POST['cur'] AS $cur) {
            $cur = htmlentities($cur);
		
		
            $insertSQL = sprintf("INSERT INTO result (stdid, csid, sesid) VALUES (%s, %s, %s)",
						   GetSQLValueString($_POST['stid'], "text"),
						   GetSQLValueString($cur, "text"),
						   GetSQLValueString($_POST['sid'], "int"));
	
            $Result = mysql_query($insertSQL, $tams) or die(mysql_error());
	  
	}
	
	$updateSQL = sprintf("UPDATE registration SET course = %s WHERE stdid=%s AND sesid=%s",
						   GetSQLValueString("Registered", "text"), 
                                                   GetSQLValueString($_POST['stid'], "text"), 
                                                   GetSQLValueString($_POST['sid'], "int"));
	
	  
	  $Result = mysql_query($updateSQL, $tams) or die(mysql_error());
}


$query_sess = "SELECT * FROM `session` ORDER BY sesname DESC LIMIT 0,1";
$sess = mysql_query($query_sess, $tams) or die(mysql_error());
$row_sess = mysql_fetch_assoc($sess);
$totalRows_sess = mysql_num_rows($sess);

$colname_stud = "-1";
if (isset($_SESSION['stid'])) {
  $colname_stud = $_SESSION['stid'];
}

if (isset($_GET['stid'])) {
  $colname_stud = $_GET['stid'];
}


$query_stud = sprintf("SELECT s.stdid, s.level, s.disciplinary, s.fname, s.payment, s.lname, s.level, s.progid, p.progname, d.deptname "
        . "FROM student s, programme p, department d "
        . "WHERE s.progid = p.progid "
        . "AND p.deptid = d.deptid "
        . "AND stdid = %s", GetSQLValueString($colname_stud, "text"));
$stud = mysql_query($query_stud, $tams) or die(mysql_error());
$row_stud = mysql_fetch_assoc($stud);
$totalRows_stud = mysql_num_rows($stud);

$query_rsdisp = sprintf("SELECT * FROM disciplinary WHERE stdid = %s", GetSQLValueString($colname_stud, "text"));
$rsdisp = mysql_query($query_rsdisp, $tams) or die(mysql_error());
$row_rsdisp = mysql_fetch_assoc($rsdisp);
$totalRows_rsdisp = mysql_num_rows($rsdisp);

$colname_ref = "-1";
if (isset($_GET['stid'])) {
  $colname_ref = $_GET['stid'];
}

$colname_regStatus = "-1";
if (isset($colname_stud)) {
  $colname_regStatus = $colname_stud;
}

$colname_regStatus1 = "-1";
if (isset($row_sess['sesid'])) {
  $colname_regStatus1 = $row_sess['sesid'];
}

if (isset($_GET['sid'])) {
  $colname_regStatus1 = $_GET['sid'];
}

$query_regStatus = sprintf("SELECT * FROM registration WHERE stdid = %s AND sesid = %s", 
                            GetSQLValueString($colname_regStatus, "text"), 
                            GetSQLValueString($colname_regStatus1, "int"));
$regStatus = mysql_query($query_regStatus, $tams) or die(mysql_error());
$row_regStatus = mysql_fetch_assoc($regStatus);
$totalRows_regStatus = mysql_num_rows($regStatus);

$colname_regsess = "-1";
if (isset($row_sess['sesid'])) {
  $colname_regsess = $row_sess['sesid'];
}
if (isset($_GET['sid'])) {
  $colname_regsess = $_GET['sid'];
}

$query_regsess = sprintf("SELECT s.* "
        . "FROM session s, registration r "
        . "WHERE r.sesid = s.sesid "
        . "AND r.status=%s AND r.stdid=%s "
        . "ORDER BY sesname DESC", 
                            GetSQLValueString("Registered", "text"), 
                            GetSQLValueString($colname_stud, "text"));
$regsess = mysql_query($query_regsess, $tams) or die(mysql_error());
$row_regsess = mysql_fetch_assoc($regsess);
$totalRows_regsess = mysql_num_rows($regsess);

$query_course = sprintf("SELECT r.csid, c.semester, c.csname, d.status, d.unit "
        . "FROM result r, course c, department_course d "
        . "WHERE r.cleared = true "
        . "AND r.stdid = %s "
        . "AND c.csid = r.csid "
        . "AND d.csid = r.csid "
        . "AND r.sesid = %s "
        . "AND d.progid=%s "
        . "ORDER BY r.csid, c.semester ASC", 
                            GetSQLValueString($colname_regStatus, "text"), 
                            GetSQLValueString($colname_regsess, "int"), 
                            GetSQLValueString($row_stud['progid'], "int"));
$course = mysql_query($query_course, $tams) or die(mysql_error());
$row_course = mysql_fetch_assoc($course);
$totalRows_course = mysql_num_rows($course);

$query_paid = sprintf("SELECT * FROM payhistory WHERE stdid = %s AND sesid = %s AND status = %s", 
                            GetSQLValueString($colname_regStatus, "text"), 
                            GetSQLValueString($colname_regsess, "int"), 
                            GetSQLValueString('paid', "text"));
$paid = mysql_query($query_paid, $tams) or die(mysql_error());
$row_paid = mysql_fetch_assoc($paid);
$totalRows_paid = mysql_num_rows($paid);

$query_crslist = sprintf("SELECT csid FROM result WHERE stdid = %s AND sesid = %s", 
                            GetSQLValueString($colname_regStatus, "text"), 
                            GetSQLValueString($colname_regsess, "int"));
$crslist = mysql_query($query_crslist, $tams) or die(mysql_error());
$row_crslist = mysql_fetch_assoc($crslist);
$totalRows_crslist = mysql_num_rows($crslist);


$regOpen = false;
if($row_sess['registration'] == 'FALSE') {
    $regOpen = true;
}

$paid = false;
if($totalRows_paid > 0)
	$paid = true;

$sesReg = false;
$row_regStatus['status'];
if($row_regStatus['status'] == "Registered" )
	$sesReg = true;

$crsReg = false;

// Ensure student is properly registered
if($row_regStatus['course'] == "Registered" && $totalRows_crslist > 0) {
	$crsReg = true;
}else {
    $updateSQL = sprintf("UPDATE registration SET course = %s WHERE stdid=%s AND sesid=%s",
						   GetSQLValueString("Unregistered", "text"), 
                                                   GetSQLValueString($colname_regStatus, "text"), 
                                                   GetSQLValueString($colname_regsess, "int"));
    $Result = mysql_query($updateSQL, $tams) or die(mysql_error());
    $row_regStatus['course'] = 'Unregistered';
}

$crsAppr = false;
if(isset($row_regStatus['approved']) && $row_regStatus['approved'] == "TRUE" )
	$crsAppr = true;

$colname_ref = "d.csid = ''";
if (isset($row_stud['level']) && $row_stud['level'] > 1) {
    $colname_ref = "(d.csid LIKE '___1%'";
    for($i = 2; $i < $row_stud['level']; $i++) {
        $colname_ref .= " OR d.csid LIKE '___".$i."%'";
    }
    $colname_ref .= ")";
}
//
//$query_ref = sprintf("SELECT DISTINCT r.csid, d.status, unit 
//FROM `result` r, department_course d, student s 
//WHERE d.csid = r.csid AND r.stdid = s.stdid AND d.progid = s.progid AND tscore + escore < 40 AND r.stdid = %s AND r.sesid <> %s AND r.csid NOT IN ( SELECT csid FROM result WHERE stdid = %s AND tscore + escore > 39 AND sesid <> %s )", 
//                        GetSQLValueString($colname_stud, "text"), 
//                        GetSQLValueString($colname_regStatus1, "int"),
//                        GetSQLValueString($colname_stud, "text"), 
//                        GetSQLValueString($colname_regStatus1, "int"));
//$ref = mysql_query($query_ref, $tams) or die(mysql_error());
//$row_ref = mysql_fetch_assoc($ref);
//$totalRows_ref = mysql_num_rows($ref);

$query_ref = sprintf("SELECT d.csid, d.status, csname, d.unit "
                        . "FROM department_course d "
                        . "JOIN course c ON c.csid = d.csid "
                        . "WHERE progid = %s AND %s",
                        GetSQLValueString($row_stud['progid'], "int"),
                        GetSQLValueString($colname_ref, "defined", $colname_ref));
$ref = mysql_query($query_ref, $tams) or die(mysql_error());
$row_ref = mysql_fetch_assoc($ref);
$totalRows_ref = mysql_num_rows($ref);

$colname_cur = "-1";
if (isset($row_stud['progid'])) {
  $colname_cur = $row_stud['progid'];
}

$colname_cur1 = "-1";
if (isset($row_stud['level'])) {
  $colname_cur1 = "___".$row_stud['level']."%";
}

$query_cur = sprintf("SELECT d.csid, d.status, csname, d.unit "
        . "FROM department_course d "
        . "JOIN course c ON c.csid = d.csid "
        . "WHERE progid = %s AND d.csid LIKE %s",
                        GetSQLValueString($colname_cur, "int"),
                        GetSQLValueString($colname_cur1, "text"));
$cur = mysql_query($query_cur, $tams) or die(mysql_error());
$row_cur = mysql_fetch_assoc($cur);
$totalRows_cur = mysql_num_rows($cur);

$query_depts = sprintf("SELECT deptid, deptname "
                            . "FROM department");
$depts = mysql_query($query_depts, $tams) or die(mysql_error());
$row_depts = mysql_fetch_assoc($depts);
$totalRows_depts = mysql_num_rows($depts);

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
	doLogout( $site_root );  
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<?php require('../param/site.php'); ?>
<title><?php echo $university ?> </title>
<script type="text/javascript" src="../scripts/jquery.js"></script>
<script type="text/javascript" src="../scripts/tams.js"></script>
<!-- InstanceEndEditable -->
<link href="../css/template.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="../css/menulink.css" rel="stylesheet" type="text/css" />
<link href="../css/footer.css" rel="stylesheet" type="text/css" />
<link href="../css/sidemenu.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="container">
  <div class="header">
    <!-- end .header -->
</div>
  <div class="topmenu">
<?php include '../include/topmenu.php'; ?>
  </div>
  <!-- end .topmenu --> 
  
  <div class="loginuser">
  <?php include '../include/loginuser.php'; ?>
  
  <!-- end .loginuser --></div>
  <div class="pagetitle">
    <table width="600">
      <tr>
        <td><!-- InstanceBeginEditable name="pagetitle" -->
            Course Registration
                <?php if( isset($_GET['stid']) )echo " for ".$row_stud['lname'].", ".$row_stud['fname'];?>
            <!-- InstanceEndEditable -->
        </td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include '../include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
    <table width="690">
    <?php 
        if($regOpen) {
            if($row_stud['disciplinary']== 'False'){
                if($paid) {
    ?>
      <?php if(!$sesReg){?>
      	<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1"> 
        
          <tr>
              <td>Please register for the session to proceed with course registration!</td>
          </tr>
          <tr>
            <td align="center"><input type="submit" name="submit" id="submit" value="Register" /></td>
          </tr>
          <input name="stid" type="hidden" value="<?php echo $colname_stud?>" />
          <input name="lvl" type="hidden" value="<?php echo $row_stud['level']?>" />
          <input name="sid" type="hidden" value="<?php echo $row_sess['sesid']?>" />
          <input type="hidden" name="MM_insert" value="form1" />
        </form>
      <?php }elseif($sesReg && !$crsReg){?>
      <tr>
        <td>
            
            <form action="<?php echo $editFormAction; ?>" method="post" name="form" id="form">
                <table width="644">
                  <tr>
                    <td colspan="3" valign="top" style="color:red">
                    	Please check your Department Handbook for courses to register. 
                        Courses with course code ending with alphabet 'n' are mainly for 100 - 200 level students. 
                    </td>
                  <tr>
                  <tr>
                    <td colspan="3" valign="top">
                    	
                    </td>
                  </tr>
                    <td colspan="3" valign="top">
                    	Max Unit Allowed: <span id="max"><?php echo $row_sess['tnumax']?></span><br/>
                        Min Unit Allowed: <span id="min"><?php echo $row_sess['tnumin']?></span><br/>
                        Registered Units: <span id="reg">0</span><br/>
                        Remaining Units: <span id="rem"><?php echo $row_sess['tnumax']?></span><br/>
                    </td>
                  </tr>
                  <?php if($row_stud['level'] > 3) {?>
                  <tr>
                    <td colspan="3" valign="top">                        
                    	<fieldset id="extra-field">
                            <button data-toggle="modal" href="#modal-1" class="btn btn-primary btn-medium">
                                Add Extra Courses
                            </button>
                        	<legend>Extra Courses</legend>                       
                              
                              
                        </fieldset>
                    </td>
                  </tr>
                  <?php }?>
                  <tr>
                    <td colspan="3" valign="top">                        
                    	<fieldset id="ref-field">
                            <button data-toggle="modal" href="#modal-2" class="btn btn-primary btn-medium">
                                Add References
                            </button>
                        	<legend>Carry Over</legend>                       
                              
                              
                        </fieldset>
                    </td>
                  </tr>
                    <tr>
                    <td colspan="3" valign="top">
                    	
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" valign="top">
                    	<fieldset>
                        	<legend>Current</legend>
                             <?php if ($totalRows_cur > 0) { // Show if recordset not empty ?>
                            <?php do{?>
                            <div class="cbox">
                                <p style="float:left;"><?php echo $row_cur['csid']?></p>
                                <p style="float:left;"><?php echo $row_cur['csname']?> 
                                    (<span><?php echo $row_cur['unit']?></span> Units, <?php echo $row_cur['status']?>)
                                </p>
                                <p style="float:right;">
                                    <input type="checkbox" name="cur[]" value="<?php echo $row_cur['csid']?>"/>
                                </p>
                                <div style="clear:both;"></div>
                            </div>
                             <?php }while( $row_cur = mysql_fetch_assoc($cur) );?>
                              <?php }else{
								  	echo "";
								  } // Show if recordset not empty ?>

                        </fieldset></td>
                  </tr>
                  <tr>
                    <td colspan="3" align="center">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3" align="center">
                    <input type="submit" name="submit" id="submit" value="Register Courses" disabled/>
                    </td>
                  </tr>
                </table>
                
          <input name="stid" type="hidden" value="<?php echo $colname_stud?>" />
          <input name="sid" type="hidden" value="<?php echo $row_sess['sesid']?>" />
          <input type="hidden" name="MM_insert" value="form2" />
             </form>
		</td>
      </tr>
      <?php }elseif(($sesReg && $crsReg && $crsAppr) || in_array(getAccess(),$acl)){?>      
      <tr>
      	<td>
       	  <table border="0" align="center">
              <tr>
                <td colspan="2" align="right">
                    <a href="courseform.php<?php echo "?sid=".$colname_regsess;?>" target="_new">Print Form</a>
                    <select name="sesid" onchange="sesfilt(this)">
                        <?php
                        do {  
                        ?>
                              <option value="<?php echo $row_regsess['sesid']?>" 
                                  <?php if (!(strcmp($row_regsess['sesid'], $colname_regsess))) {
                                          echo "selected=\"selected\"";                                      
                                      } 
                                  ?>>
                                  <?php echo $row_regsess['sesname']?>
                              </option>
                                                  <?php
                        } while ($row_regsess = mysql_fetch_assoc($regsess));
                          $rows = mysql_num_rows($regsess);
                          if($rows > 0) {
                              mysql_data_seek($regsess, 0);
                              $row_regsess = mysql_fetch_assoc($regsess);
                          }
                        ?>
                    </select>
                </td>
              </tr> 
              <tr>
                  <td colspan="2" width="100" align="right">
                  </td>
              </tr>             
              <tr>
                <td colspan="2">
                <table width="680" border="0" id="ctable">
                  <tr>
                    <th width="100" align="center">COURSE CODE</th>
                    <th width="410" align="center">COURSE NAME</th>
                    <th width="80" align="center">STATUS</th>
                    <th width="30">UNIT</th>
                    <th width="70" align="center">SEMESTER</th>
                  </tr>
                  <?php 
                        $tunits = 0;
                        if ($totalRows_course > 0) { // Show if recordset not empty 
                  ?>
                  <?php
                        do { 
                  ?>
                  <tr>
                        <td><div align="center"><?php echo $row_course['csid']; ?></div></td>
                        <td><?php echo $row_course['csname']; ?></td>
                        <td><div align="center"><?php echo $row_course['status']; ?></div></td>
                        <td><div align="center"><?php echo $row_course['unit'];$tunits += $row_course['unit'];?></div></td>
                        <td>
                            <div align="center">
                                <?php echo (strtolower($row_course['semester']) == "f")? "First": "Second" ;?>
                            </div>
                        </td>
                  </tr>
                  <?php } while ($row_course = mysql_fetch_assoc($course)); ?>
                    
                  <?php } // Show if recordset not empty ?>
                <tr>
                    <td colspan="3" align="right" >Total Units</td>
                      <td align="center"><?php echo $tunits;?></td>
                        <td></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
        </td>
      </tr>
      <?php }else{
      ?>
        <tr>
            <td>Your course form is awaiting approval by your course adviser. Check back later!</td>
        </tr>
      <?php    
      }?>
    <?php     
            }else {
                echo "You are required to pay for the session before registration! "
                . "<a href='http://portal.tasued.edu.ng/regular_students/schfee/main/status.php' target='_Blank'>Click here</a>"
                . " to proceed to payment.";
            }

          }else {
              echo "You are on a disciplinary action<strong>  {$row_rsdisp['status']}</strong> as at <strong> "
              . "{$row_rsdisp['login']} </strong> Kindlly  contact the Registrar's Office for advice and necessary action" ;
          }
      }else {
          echo "Registration for this session {$row_sess['sesname']} is closed!";
      }
    ?>
    </table>
  <!-- InstanceEndEditable -->
  <div aria-hidden="true" 
       aria-labelledby="myModalLabel" 
       role="dialog" tabindex="-1" 
       class="modal hide fade" 
       id="modal-2">
      <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">X</button>
          <h3 id="myModalLabel">Select Courses</h3>
      </div>
      <div class="modal-body">
          <form>
              <fieldset>
                    <legend>Carry Over</legend>
                    <?php if ($totalRows_ref > 0) { // Show if recordset not empty ?>
                    <?php do{?>
                      <div class="cbox">
                        <p style="float:left;"><?php echo $row_ref['csid']?></p>
                        <p style="float:left; width:320px"><?php echo $row_ref['csname']?> (<span><?php echo $row_ref['unit']?></span> Units, <?php echo $row_ref['status']?>)</p>
                        <p style="float:right;"><input type="checkbox" name="ref[]" value="<?php echo $row_ref['csid']?>"/></p>                    
                        <div style="clear:both;"></div>
                      </div>
                      <?php }while( $row_ref = mysql_fetch_assoc($ref) );?>
                      <?php }else{
                            echo "There are no carry over courses.";
                      } // Show if recordset not empty ?>
            </fieldset>
          </form>
      </div>
      <div class="modal-footer">
          <button class="btn" data-dismiss="modal">Close</button>
          <button class="btn btn-primary" id="add-courses">Add Courses</button>
      </div>
      
  </div>
  
  <div aria-hidden="true" 
         aria-labelledby="myModalLabel" 
         role="dialog" tabindex="-1" 
         class="modal hide fade" 
         id="modal-1">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">X</button>
            <h3 id="myModalLabel">Select Extra Courses</h3>
        </div>
        <div class="modal-body">
            <p style="text-align">
                <select id="extra_depts">
                    <option value="-1">Select a Department</option>
                    <?php 
                        if($totalRows_depts > 1) {
                            for($idx = 0; $idx < $totalRows_depts; $idx++, $row_depts = mysql_fetch_assoc($depts)) {
                    ?>
                    <option value="<?php echo $row_depts['deptid']?>"><?php echo $row_depts['deptname']?></option>
                    <?php
                            }
                        }
                    ?>
                </select>
            </p>
            <p id="loading" class='hide'>Loading Courses...</p>
            <form>
                <fieldset>
                      <legend>Extra Courses</legend>
                      <div id='crslist'></div>
              </fieldset>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Close</button>
            <button class="btn btn-primary" id="extra-courses">Add Courses</button>
        </div>

    </div> 
  </div>
<div class="footer">
    <p><!-- end .footer -->   
    
    <?php require '../include/footer.php'; ?>
	
   </p>
  </div>
  
  <!-- end .container -->
</div>
</body>
<script src="../scripts/bootstrap-modal.js"></script>
<script type="text/javascript">
    $(function() {
        $('#add-courses, #extra-courses').click(function() { 
            var totalUnits = 0;
            var count = 0;
            var rem =  parseInt($('#rem').text());
            var reg =  parseInt($('#reg').text());
            var modal;
            var container;
            var filtered = '';
           
            if($(this).attr('id') == 'add-courses') {
                modal = '#modal-2';
                container = '#ref-field';
                $(container +' input[type=checkbox]:checked').parent().parent().find('span').each(function() {
                    totalUnits += parseInt($(this).text());                    
                });
                
                $('#reg').text(reg - totalUnits);
                $('#rem').text(rem + totalUnits);
                totalUnits = 0;
                $(container).children('div').remove();
            }else {
                modal = '#modal-1';
                container = '#extra-field';
                
            }
            
            var checked = $(modal +' input[type=checkbox]:checked').parent().parent();
            
            if(checked.size() < 1) {
                alert('You have not selected any course!');
                return;
            }   
            
            filtered = $(checked).filter(function() {
                var span = $(this).find('span');
                var value = parseInt(span.text());
                var check = value + totalUnits;
                if(check > rem) {
                    return false;
                }
                
                totalUnits += value;
                return true;           
            });
            
            if($(this).attr('id') == 'extra-courses') {
                $('#extra-field').append(checked);
                $('#crslist input[type=checkbox]:checked').attr('checked', false);
                $('#reg').text(reg + totalUnits);
                $('#rem').text(rem - totalUnits);
            }
                        
            $(container).append(filtered);
            $(modal).modal('hide');
        });
        
//        // Add selected courses to course assignment list
//        $('#extra-courses').click(function() { 
//            var checked = $('#crslist input[type=checkbox]:checked').parent().parent();
//            var content = checked.clone(true, true);
//
//            if(checked.size() < 1) {
//                alert('You have not selected any course!');
//                return;
//            }                
//
//            $('#extra-field').append(checked);
//            $('#crslist input[type=checkbox]:checked').attr('checked', false);
//            $('#modal-1').modal('hide');
//        });
            
        // Get courses for the selected department
        $('#extra_depts').change(function() {
            var id = $(this).val();
            
            if(id == '-1')
                return;

            $('#loading').fadeIn();
            $.post(
                '../course/getprogcourses.php', 
                {new: id, cur: 0, ref: 1},
                function(response) {
                    $('#loading').fadeOut('fast');
                    var res = JSON.parse(response);
                    var crslist = '';
                    var container = $('#crslist');
                    
                    if(res.status === 'success') {                            
                        var crs = res.courses;  
                        if(crs.length > 0) {
                            for(var i = 0; i < crs.length; i++) {
                                crslist += "<div class='cbox'>";
                                crslist += "<p style='float:left;'>"+crs[i].csid+"</p>";
                                crslist += "<p style='float:left; width:320px'>"+crs[i].csname+"(<span>"+crs[i].unit+"</span> Units, "+crs[i].status+")</p>";
                                crslist += "<p style='float:right;'><input type='checkbox' name='ref[]' value='"+crs[i].csid+"'/></p>";
                                crslist += "<div style='clear:both;'></div></div>";
                            }
                        }else {
                            crslist = "<div>"+res.msg+"</div>";
                        }

                    }else {
                        crslist = '<div>There are no courses to display!</div>';
                        alert(res.msg);
                    }

                    container.empty().append(crslist);
                }
            ); 
        });
    });
</script>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($stud);

mysql_free_result($ref);

mysql_free_result($cur);

mysql_free_result($sess);

mysql_free_result($regStatus);

mysql_free_result($regsess);

mysql_free_result($course);
