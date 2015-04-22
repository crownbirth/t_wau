<?php require_once('../../Connections/tams.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "1";
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE college SET colname=%s, colcode=%s, coltitle=%s, remark=%s WHERE colid=%s",
                       GetSQLValueString($_POST['colname'], "text"),
                       GetSQLValueString($_POST['colcode'], "text"),
                       GetSQLValueString($_POST['coltitle'], "text"),
                       GetSQLValueString($_POST['remark'], "text"),
                       GetSQLValueString($_POST['colid'], "int"));

  mysql_select_db($database_tams, $tams);
  $Result1 = mysql_query($updateSQL, $tams) or die(mysql_error());

  $updateGoTo = "college.php";
  if( $Result1 )
  	$updateGoTo = ( isset( $_GET['success'] ) ) ? $updateGoTo : $updateGoTo."?success";
  else
	$updateGoTo = ( isset( $_GET['error'] ) ) ? $updateGoTo : $updateGoTo."?error";
	
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_editcol = "-1";
if (isset($_GET['cid'])) {
  $colname_editcol = $_GET['cid'];
}
mysql_select_db($database_tams, $tams);
$query_editcol = sprintf("SELECT * FROM college WHERE colid = %s", GetSQLValueString($colname_editcol, "int"));
$editcol = mysql_query($query_editcol, $tams) or die(mysql_error());
$row_editcol = mysql_fetch_assoc($editcol);
$totalRows_editcol = mysql_num_rows($editcol);

$maxRows_college = 10;
$pageNum_college = 0;
if (isset($_GET['pageNum_college'])) {
  $pageNum_college = $_GET['pageNum_college'];
}
$startRow_college = $pageNum_college * $maxRows_college;

mysql_select_db($database_tams, $tams);
$query_college = "SELECT colid, colname, colcode FROM college";
$query_limit_college = sprintf("%s LIMIT %d, %d", $query_college, $startRow_college, $maxRows_college);
$college = mysql_query($query_limit_college, $tams) or die(mysql_error());
$row_college = mysql_fetch_assoc($college);

if (isset($_GET['totalRows_college'])) {
  $totalRows_college = $_GET['totalRows_college'];
} else {
  $all_college = mysql_query($query_college);
  $totalRows_college = mysql_num_rows($all_college);
}
$totalPages_college = ceil($totalRows_college/$maxRows_college)-1;
?>
<?php 
require_once('../../param/param.php');
require_once('../../functions/function.php');

$sub = false;
$path = pathinfo( $_SERVER['SCRIPT_FILENAME']);

if( $path['dirname'] != $_SERVER['DOCUMENT_ROOT'].$site_root )
	$sub = true;
	

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true"))
	doLogout( $site_root );

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
        <td><!-- InstanceBeginEditable name="pagetitle" -->Edit <?php echo $row_editcol['colname'];?><!-- InstanceEndEditable --></td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include '../../include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
    <table width="690">
      <tr>
        <td>
        	<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
              <table align="center">
              <tr valign="baseline">
                  <td colspan="2">
					  <?php 					  		
                       		statusMsg();
                      ?>
                      <br/>
                  </td>
                </tr>
                
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">College Name:</td>
                  <td><input type="text" name="colname" value="<?php echo htmlentities($row_editcol['colname'], ENT_COMPAT, 'utf-8'); ?>" size="50" /></td>
                </tr>
                <br/>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">College Code:</td>
                  <td><?php echo htmlentities($row_editcol['colcode'], ENT_COMPAT, 'utf-8'); ?></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right">College Title:</td>
                  <td><input type="text" name="coltitle" value="<?php echo htmlentities($row_editcol['coltitle'], ENT_COMPAT, 'utf-8'); ?>" size="15" /></td>
                </tr>
                <tr valign="baseline">
                  <td nowrap="nowrap" align="right" valign="top">Remark:</td>
                  <td><textarea name="remark" cols="50" rows="5"><?php echo htmlentities($row_editcol['remark'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
                </tr>
                <tr valign="baseline">
                  <td colspan="2" align="center" nowrap="nowrap"><input type="submit" value="Update College" /></td>
                </tr>
              </table>
              <input type="hidden" name="colcode" value="<?php echo htmlentities($row_editcol['colcode'], ENT_COMPAT, 'utf-8'); ?>" size="32" />
              <input type="hidden" name="colid" value="<?php echo $row_editcol['colid']; ?>" />
              <input type="hidden" name="MM_update" value="form1" />
            </form>
        </td>
      </tr>
      <tr>
      	<td>All <?php echo $college_name;?> in University</td>
      </tr>
      <tr>
      	<td>
       	  <table width="670" border="0">
        	  <tr>
        	    <td width="54">Code</td>
        	    <td width="330">Name</td>
        	    <td width="123">&nbsp;</td>
        	    <td width="68">&nbsp;</td>
        	    <td width="73">&nbsp;</td>
      	    </tr>
              <?php if ($totalRows_college > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <tr>
      <td><?php echo $row_college['colcode']; ?></td>
      <td><?php echo $row_college['colname']; ?></td>
      <td><a href="../department/?cid=<?php echo $row_college['colid']?>">Add Department</a></td>
      <td><a href="college.php?cid=<?php echo $row_college['colid']?>">Edit</a></td>
      <td>Delete</td>
    </tr>
    <?php } while ($row_college = mysql_fetch_assoc($college)); ?>
                <?php } // Show if recordset not empty ?>
          </table></td>
      </tr>
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
<?php
mysql_free_result($editcol);

mysql_free_result($college);
?>
