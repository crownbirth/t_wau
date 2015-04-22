<?php
require_once('../Connections/tams.php');
if (!isset($_SESSION)) {
  session_start();
}
require_once('../param/param.php');
require_once('../functions/function.php');

//$MM_authorizedUsers = "11";
//$MM_donotCheckaccess = "true";
//
//// *** Restrict Access To Page: Grant or deny access to this page
//function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
//  // For security, start by assuming the visitor is NOT authorized. 
//  $isValid = False; 
//
//  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
//  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
//  if (!empty($UserName)) { 
//    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
//    // Parse the strings into arrays. 
//    $arrUsers = Explode(",", $strUsers); 
//    $arrGroups = Explode(",", $strGroups); 
//    if (in_array($UserName, $arrUsers)) { 
//      $isValid = true; 
//    } 
//    // Or, you may restrict access to only certain users based on their username. 
//    if (in_array($UserGroup, $arrGroups)) { 
//      $isValid = true; 
//    } 
//    if (($strUsers == "") && true) { 
//      $isValid = true; 
//    } 
//  } 
//  return $isValid; 
//}
//
//$MM_restrictGoTo = "index.php";
//if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
//  $MM_qsChar = "?";
//  $MM_referrer = $_SERVER['PHP_SELF'];
//  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
//  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
//  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
//  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
//  header("Location: ". $MM_restrictGoTo); 
//  exit;
//}

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
    $query = sprintf("SELECT * FROM session ORDER BY sesid DESC LIMIT 1 ");
    $session= mysql_query($query, $tams) or die(mysql_error());
    $row_session = mysql_fetch_assoc($session);
    $totalRows_session = mysql_num_rows($session);
    
    //set the new Admission session Name
    $split = explode('/',  $row_session['sesname']);
    $adm_ses_name = ($split[0]+1).'/'.($split[1]+1);

mysql_select_db($database_tams, $tams);
$query_rschk = sprintf("SELECT jambregid, sex,lname, fname, mname, admtype, formsubmit, formpayment 
						FROM prospective p 
						WHERE p.jambregid=%s",
						GetSQLValueString(getSessionValue('MM_Username'), "text"));
$rschk = mysql_query($query_rschk, $tams) or die(mysql_error());
$row_rschk = mysql_fetch_assoc($rschk);
$totalRows_rschk = mysql_num_rows($rschk);
	

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
        <td><!-- InstanceBeginEditable name="pagetitle" --><?php echo $adm_ses_name ?> Prospective  Application Instructions<!-- InstanceEndEditable --></td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include '../include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
    <table width="690">
        <tr>
            <td>
                <table class="table table-bordered">
                    <tr>
                        <td>
                            
                                <p style="font-weight: bold">
                                    <u> METHOD OF APPLICATION   </u>
                                </p>
                                   
                                <ol>
                                    <li> 
                                        All Applicants are required to create Account before proceeding with the application process.
                                        An e-mail containing the Registration information will be sent to the e-mail address provided.
                                    </li>
                                    <li>
                                        After creating an account, Applicants are required to visit any branch of Zenith Bank to pay
                                        a sum of <strong>Seven Thousand Five Hundred Naira Only</strong> (=N= 7,500.00) Application
                                        fees. Please quote your REGISTRATION NUMBER while making the payment at the Bank. Your payment will be synchronized with the portal.
                                        
                                    </li>
                                    <li>
                                        After the Bank payment, return to the portal to complete your Application Form for Admission.
                                    </li>
                                    <li>
                                        Re-login to the portal later to check your Admission Status. If Offered Admission 
                                        into any of the Academic programmes, print your Admission Letter and visit any 
                                        branch of Zenith Bank to pay for the Acceptance Fees. 
                                    </li>
                                    <li>
                                        All New Students are required to use their Registration No. to Make Payment to the Bank. Your Registration No. is your Username.
                                    </li>
                                </ol>   
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
  <!-- InstanceEndEditable --></div>
<div class="footer">
    <p><!-- end .footer -->   
    
    <?php require '../include/footer.php'; ?>
	
   </p>
  </div>
  <!-- end .container -->
</div>
</body>
<!-- InstanceEnd --></html>