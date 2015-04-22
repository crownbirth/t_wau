<?php 
require_once('../../Connections/tams.php');
if (!isset($_SESSION)) {
  session_start();
}

require_once('../../param/param.php');
require_once('../../functions/function.php');

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
$query = sprintf("SELECT * FROM session ORDER BY sesid DESC LIMIT 1");
$session= mysql_query($query, $tams) or die(mysql_error());
$row_session = mysql_fetch_assoc($session);
$totalRows_session = mysql_num_rows($session);

echo $query_chkTrans = sprintf("SELECT * "
                    . "FROM appfee_transactions "
                    . "WHERE can_no = %s", 
                    GetSQLValueString($_SESSION['MM_Username'], "text"));
$chkTrans = mysql_query($query_chkTrans, $tams) or die(mysql_error());
$row_chkTrans = mysql_fetch_assoc($chkTrans);
var_dump($row_chkTrans);
//set the new Admission session Name
$adm_ses_name = "{$row_chkTrans['year']}/".($row_chkTrans['year'] + 1);

if(isset($_GET['doLogout']) && $_GET['doLogout'] == "true") {
    doLogout($site_root.'/prospective');   
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<?php require('../../param/site.php'); ?>
<title><?php echo $university ?> </title>
<!-- InstanceEndEditable -->
<link href="../../css/template.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="../../css/menulink.css" rel="stylesheet" type="text/css" />
<link href="../../css/footer.css" rel="stylesheet" type="text/css" />
<link href="../../css/sidemenu.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="container">
  <div class="header">
    <!-- end .header -->
</div>
  <div class="topmenu">
<?php include '../../include/topmenu.php'; ?>
  </div>
  <!-- end .topmenu --> 
  
  <div class="loginuser">
  <?php include '../../include/loginuser.php'; ?>
  
  <!-- end .loginuser --></div>
  <div class="pagetitle">
    <table width="600">
      <tr>
        <td><!-- InstanceBeginEditable name="pagetitle" -->Payment Instruction <!-- InstanceEndEditable --></td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include '../../include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
      <table width="690" class="table">
          <?php if($row_chkTrans['status'] == 'APPROVED') {?>
            <tr>
                <td colspan="2">
                    <p>You have successfully paid your application fee. 
                        Please <a href="../admform.php">click here</a> to continue with your application.
                    </p>
                    
                </td> 
            </tr>
          <?php }else {?>
            <tr>
                <td colspan="2">
                    <p>You are required to pay an Application fee 
                       to any branch of Zenith Bank before 
                       completing your Application form for the <?php echo $adm_ses_name?> 
                       admission into <?php echo $university?> .
                    </p>
                    <p>
                        <ul>
                            <li>Bank : Zenith Bank (Any Branch)</li>
                            <li>Account No : XXXXXXXXXX </li>
                            <li>Application Fee : <?php echo $row_chkTrans['amt'] ?></li>
                            <li>Application Payment Code : <?php echo $row_chkTrans['reference'] ?> </li>
                        </ul>
                    </p>
                    <p>
                        After making your payment, kindly return to the portal 
                        to login with your Registration No as Username and
                        Surname as password to complete your Application process 
                    </p>
                </td> 
            </tr>
            
          <?php }?>
        </table>
  <!-- InstanceEndEditable --></div>
<div class="footer">
    <p><!-- end .footer -->   
    
    <?php require '../../include/footer.php'; ?>
	
   </p>
  </div>
  <!-- end .container -->
</div>
</body>
<!-- InstanceEnd --></html>