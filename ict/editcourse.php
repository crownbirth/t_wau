<?php require_once('../Connections/tams.php'); ?>
<?php

if (!isset($_SESSION)) {
  session_start();
}
require_once('../param/param.php');
require_once('../functions/function.php');

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

mysql_select_db($database_tams, $tams);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
    
  $updateSQL = sprintf("UPDATE course SET csid=%s, csname=%s, semester=%s,type=%s, catid=%s, deptid=%s, cscont=%s WHERE csid=%s",
                       GetSQLValueString($_POST['ncsid'], "text"),
                       GetSQLValueString($_POST['csname'], "text"),
                       GetSQLValueString($_POST['semester'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['catid'], "int"),
                       GetSQLValueString($_POST['deptid'], "int"),
                       GetSQLValueString($_POST['cscont'], "text"),
                       GetSQLValueString($_POST['csid'], "text"));

  $Result1 = mysql_query($updateSQL, $tams) or die(mysql_error());

  $updateGoTo = "srchstdnt.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_editcs = "-1";
if (isset($_GET['csid'])) {
  $colname_editcs = $_GET['csid'];
}

$query_editcs = sprintf("SELECT * FROM course WHERE csid = %s", GetSQLValueString($colname_editcs, "text"));
$editcs = mysql_query($query_editcs, $tams) or die(mysql_error());
$row_editcs = mysql_fetch_assoc($editcs);
$totalRows_editcs = mysql_num_rows($editcs);

$query_cat = "SELECT * FROM category";
$cat = mysql_query($query_cat, $tams) or die(mysql_error());
$row_cat = mysql_fetch_assoc($cat);
$totalRows_cat = mysql_num_rows($cat);

$query_dept = "SELECT deptid, deptname FROM department";
$dept = mysql_query($query_dept, $tams) or die(mysql_error());
$row_dept = mysql_fetch_assoc($dept);
$totalRows_dept = mysql_num_rows($dept);

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
	doLogout($site_root.'/ict');  
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
<link href="css/template.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="css/menulink.css" rel="stylesheet" type="text/css" />
<link href="css/footer.css" rel="stylesheet" type="text/css" />
<link href="css/sidemenu.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="container">
  <div class="header">
    <!-- end .header -->
</div>
  <div class="topmenu">
<?php include 'include/topmenu.php'; ?>
  </div>
  <!-- end .topmenu --> 
  
  <div class="loginuser">
  <?php include 'include/loginuser.php'; ?>
  
  <!-- end .loginuser --></div>
  <div class="pagetitle">
    <table width="600">
      <tr>
        <td><!-- InstanceBeginEditable name="pagetitle" -->Edit <?php echo $_GET['csid']?><!-- InstanceEndEditable --></td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include 'include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
    <table width="690">
      <tr>
        <td>&nbsp;
          <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
            <table align="center">
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Course Code:</td>
                <td><input type="text" name="ncsid" value="<?php echo htmlentities($row_editcs['csid'], ENT_COMPAT, 'utf-8'); ?>" size="32" <?php echo (getIctAccess()>20)?"readonly=\"readonly\"":""?> /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Course Name:</td>
                <td><input type="text" name="csname" value="<?php echo htmlentities($row_editcs['csname'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Semester:</td>
                <td><select name="semester">
                  <option value="F" <?php if (!(strcmp("F", htmlentities($row_editcs['semester'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>First</option>
                  <option value="S" <?php if (!(strcmp("S", htmlentities($row_editcs['semester'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Second</option>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Course Type:</td>
                <td>
                    <select name="type">
                     <option value="General" <?php if (!(strcmp("General", htmlentities($row_editcs['type'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>General</option>
                     <option value="College" <?php if (!(strcmp("College", htmlentities($row_editcs['type'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>College</option>
                     <option value="Departmental" <?php if (!(strcmp("Departmental", htmlentities($row_editcs['type'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Departmental</option>
                    </select>
                </td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Course Category:</td>
                <td><select name="catid">
                  <?php
do {  
?>
                  <option value="<?php echo $row_cat['catid']?>"<?php if (!(strcmp($row_cat['catid'], htmlentities($row_editcs['catid'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>><?php echo $row_cat['catname']?></option>
                  <?php
} while ($row_cat = mysql_fetch_assoc($cat));
  $rows = mysql_num_rows($cat);
  if($rows > 0) {
      mysql_data_seek($cat, 0);
	  $row_cat = mysql_fetch_assoc($cat);
  }
?>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Department:</td>
                <td><select name="deptid">
                  <?php
do {  
?>
                  <option value="<?php echo $row_dept['deptid']?>"<?php if (!(strcmp($row_dept['deptid'], htmlentities($row_editcs['deptid'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>><?php echo $row_dept['deptname']?></option>
                  <?php
} while ($row_dept = mysql_fetch_assoc($dept));
  $rows = mysql_num_rows($dept);
  if($rows > 0) {
      mysql_data_seek($dept, 0);
	  $row_dept = mysql_fetch_assoc($dept);
  }
?>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">Course Content:</td>
                <td><textarea cols="50" rows="8" name="cscont"><?php echo htmlentities($row_editcs['cscont'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap="nowrap" align="right">&nbsp;</td>
                <td><input type="submit" value="Update Course" /></td>
              </tr>
            </table>
            <input type="hidden" name="MM_update" value="form1" />
            <input type="hidden" name="csid" value="<?php echo $row_editcs['csid']; ?>" />
          </form>
        <p>&nbsp;</p></td>
      </tr>
    </table>
  <!-- InstanceEndEditable --></div>
<div class="footer">
    <p><!-- end .footer -->   
    
    <?php require 'include/footer.php'; ?>
	
   </p>
  </div>
  <!-- end .container -->
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($editcs);

mysql_free_result($cat);

mysql_free_result($dept);
?>
