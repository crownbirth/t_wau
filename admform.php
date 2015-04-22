<?php 
if (!isset($_SESSION)) {
  session_start();
}

define ('MAX_FILE_SIZE', 2048 * 1536);
define('UPLOAD_DIR', '../images/student/');

require_once('../Connections/tams.php');
require_once('../param/param.php');
require_once('../functions/function.php');

$MM_authorizedUsers = "11";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
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

        $past_school1 = "";
        $past_school2 = "";
        $past_school3 = "";
        
mysql_select_db($database_tams, $tams);
    $query = sprintf("SELECT * FROM session ORDER BY sesid DESC LIMIT 1 ");
    $session= mysql_query($query, $tams) or die(mysql_error());
    $row_session = mysql_fetch_assoc($session);
    $totalRows_session = mysql_num_rows($session);
    
mysql_select_db($database_tams, $tams);
$query_rschk = sprintf("SELECT * 
                                FROM prospective  
                                WHERE jambregid=%s",
                                GetSQLValueString(getSessionValue('MM_Username'), "text"));
$rschk = mysql_query($query_rschk, $tams) or die(mysql_error());
$row_rschk = mysql_fetch_assoc($rschk);
$totalRows_rschk = mysql_num_rows($rschk);

if($row_rschk['formpayment'] == 'No' ) {
	header('Location: prospective_payment/index.php');
}

if($row_rschk['admtype'] == 'DE' ) {
	header('Location: admform1.php');
}

if($row_rschk['formsubmit'] == 'Yes' ) {
	header('Location: viewform.php');
}

mysql_select_db($database_tams, $tams);
$query_rssit1 = sprintf("SELECT * 
						FROM olevel o 
						JOIN olevelresult l ON o.olevelid = l.olevelid 
						JOIN subject s ON l.subject = s.subjid 
						JOIN grade g ON l.grade = g.grdid 
						WHERE o.jambregid=%s
						AND sitting='first'",
						GetSQLValueString($row_rschk['jambregid'], "text"));
$rssit1 = mysql_query($query_rssit1, $tams) or die(mysql_error());
$row_rssit1 = mysql_fetch_assoc($rssit1);
$totalRows_rssit1 = mysql_num_rows($rssit1);

mysql_select_db($database_tams, $tams);
$query_rssit2 = sprintf("SELECT * 
                                FROM olevel o 
                                JOIN olevelresult l ON o.olevelid = l.olevelid 
                                JOIN subject s ON l.subject = s.subjid 
                                JOIN grade g ON l.grade = g.grdid 
                                WHERE o.jambregid=%s
                                AND sitting='second'",
                                GetSQLValueString($row_rschk['jambregid'], "text"));

$rssit2 = mysql_query($query_rssit2, $tams) or die(mysql_error());
$row_rssit2 = mysql_fetch_assoc($rssit2);
$totalRows_rssit2 = mysql_num_rows($rssit2);
$lga = '';
$msg = '';

if(isset($_POST['save']) && $_POST['save'] != '') {
    
    
    
        $msg = '';
        $admission_type = "UG";
        $parts = explode('/', $row_session['sesname']);
        $part2 = $row_rschk['pstdid'];
        if(strlen($part2) < 4)
                $part2 = str_pad($part2, 4, '0', STR_PAD_LEFT);

        $formnum = (substr($parts[0], 2) +1).$admission_type.$part2;
        $updateSQL = sprintf("UPDATE prospective SET formnum=%s WHERE jambregid=%s", 
                                                GetSQLValueString($formnum, "text"), 
                                                GetSQLValueString($row_rschk['jambregid'], "text"));
        $Result = mysql_query($updateSQL, $tams) or die(mysql_error());
        
        $past_school1 = $_POST['schname1']."-".$_POST['from1']."-".$_POST['to1'];
        $past_school2 = $_POST['schname2']."-".$_POST['from2']."-".$_POST['to2'];
        $past_school3 = $_POST['schname3']."-".$_POST['from3']."-".$_POST['to3'];

         mysql_query("BEGIN", $tams);

         

            $insertSQL = sprintf("UPDATE prospective SET fname=%s, lname=%s, mname=%s, sex=%s, dob=%s, age=%s, id_num=%s, email=%s,"
                    . "phone=%s, address=%s, religion=%s, nationality=%s, other_lang=%s, sponsorname=%s, sponsorphn=%s,"
                    . "progid1=%s, progid2=%s, progmode=%s, study_mode=%s,past_school1=%s, past_school2=%s, past_school3=%s, formsubmit=%s, healthStatus=%s, sponsoradrs=%s WHERE jambregid=%s",
                    GetSQLValueString($_POST['fname'], "text"),
                    GetSQLValueString($_POST['lname'], "text"),
                    GetSQLValueString($_POST['mname'], "text"),
                    GetSQLValueString($_POST['sex'], "text"),
                    GetSQLValueString($_POST['dob'], "text"),
                    GetSQLValueString($_POST['age'], "text"),
                    GetSQLValueString($_POST['id_num'], "text"),
                    GetSQLValueString($_POST['email'], "text"),
                    GetSQLValueString($_POST['phone'], "text"),
                    GetSQLValueString($_POST['address'], "text"),
                    GetSQLValueString($_POST['religion'], "text"),
                    GetSQLValueString($_POST['nationality'], "text"),
                    GetSQLValueString($_POST['other_lang'], "text"),
                    GetSQLValueString($_POST['sponsorname'], "text"),
                    GetSQLValueString($_POST['sponsorphn'], "text"),
                    GetSQLValueString($_POST['progid1'], "text"),
                    GetSQLValueString($_POST['progid2'], "text"),
                    GetSQLValueString($_POST['progmode'], "text"),
                    GetSQLValueString($_POST['study_mode'], "text"),
                    GetSQLValueString($past_school1, "text"),
                    GetSQLValueString($past_school2, "text"),
                    GetSQLValueString($past_school3, "text"),
                    GetSQLValueString("Yes", "text"),
                    GetSQLValueString($_POST['health'], "text"),
                    GetSQLValueString($_POST['sponsoradrs'], "text"),
                    GetSQLValueString($_SESSION['MM_Username'], "text"));
         
        mysql_select_db($database_tams, $tams);
        $Result = mysql_query($insertSQL, $tams) or die(mysql_error());        

        $selectSQL = sprintf("SELECT olevelid FROM olevel WHERE jambregid=%s",
                                                   GetSQLValueString($row_rschk['jambregid'], "text"));

        mysql_select_db($database_tams, $tams);
        $rsid = mysql_query($selectSQL, $tams) or die(mysql_error());
        $row_rsid = mysql_fetch_assoc($rsid);
        $totalRows_rsid = mysql_num_rows($rsid);

        if($totalRows_rsid > 0) {
                for($i = 0; $i < $totalRows_rsid; $i++) {
                        $olevelid = $row_rsid['olevelid'];
                        mysql_query("DELETE FROM olevelresult WHERE olevelid = {$olevelid}", $tams) or die(mysql_error());
                        mysql_query("DELETE FROM olevel WHERE olevelid = {$olevelid}", $tams) or die(mysql_error());
                        $row_rsid = mysql_fetch_assoc($rsid);	
                }
        }

        $count = 1;
        if($_POST['exmyr'][1] != '')
                $count = 2;

        $pos = 'first';
        for($i = 0; $i < $count; $i++) {

                $insertSQL = sprintf("INSERT INTO olevel (jambregid, examtype, examyear, examnumber, sitting) VALUES (%s, %s, %s, %s, %s)",
                                                   GetSQLValueString(getSessionValue('MM_Username'), "text"),
                                                   GetSQLValueString($_POST['exmtyp'][$i], "text"),
                                                   GetSQLValueString($_POST['exmyr'][$i], "text"),
                                                   GetSQLValueString($_POST['examnumber'][$i], "text"),
                                                   GetSQLValueString($pos, "text"));

                mysql_select_db($database_tams, $tams);
                $Result1 = mysql_query($insertSQL, $tams) or die(mysql_error());
                $insertid = mysql_insert_id();

                for($y = 0; $y < count($_POST['subj'][$pos]); $y++) {
                        if($_POST['subj'][$pos][$y] == -1) {
                                continue;
                        }

                        $insertSQL = sprintf("INSERT INTO olevelresult (olevelid, subject, grade) VALUES (%s, %s, %s)",
                                                           GetSQLValueString($insertid, "int"),
                                                           GetSQLValueString($_POST['subj'][$pos][$y], "text"),
                                                           GetSQLValueString($_POST['grade'][$pos][$y], "text"));

                        mysql_select_db($database_tams, $tams);
                        $Result2 = mysql_query($insertSQL, $tams) or die(mysql_error());
                }

                $pos = 'second';
        }

        if($Result && $Result1 && $Result2) {

            mysql_query("COMMIT", $tams);

            $queryPstd = sprintf("SELECT formnum FROM prospective WHERE jambregid = %s",
                                            GetSQLValueString($row_rschk['jambregid'], "text"));
            $pstd = mysql_query($queryPstd, $tams) or die(mysql_error());
            $row_pstd = mysql_fetch_assoc($pstd);

            $upload = uploadFile( UPLOAD_DIR, "prospective", MAX_FILE_SIZE);	
            header('Location: viewform.php');
        }else{
            mysql_query("ROLLBACK", $tams);
        }
}
    


mysql_select_db($database_tams, $tams);
$query_rschk = sprintf("SELECT * 
                                FROM prospective  
                                WHERE jambregid=%s",
                                GetSQLValueString(getSessionValue('MM_Username'), "text"));
$rschk = mysql_query($query_rschk, $tams) or die(mysql_error());
$row_rschk = mysql_fetch_assoc($rschk);
$totalRows_rschk = mysql_num_rows($rschk);


mysql_select_db($database_tams, $tams);
$query_rssit1 = sprintf("SELECT * 
						FROM olevel o 
						JOIN olevelresult l ON o.olevelid = l.olevelid 
						WHERE o.jambregid=%s
						AND sitting='first'",
						GetSQLValueString($row_rschk['jambregid'], "text"));
$rssit1 = mysql_query($query_rssit1, $tams) or die(mysql_error());
$row_rssit1 = mysql_fetch_assoc($rssit1);
$totalRows_rssit1 = mysql_num_rows($rssit1);

mysql_select_db($database_tams, $tams);
$query_rssit2 = sprintf("SELECT * 
						FROM olevel o 
						JOIN olevelresult l ON o.olevelid = l.olevelid 
						WHERE o.jambregid=%s
						AND sitting='second'",
						GetSQLValueString($row_rschk['jambregid'], "text"));

$rssit2 = mysql_query($query_rssit2, $tams) or die(mysql_error());
$row_rssit2 = mysql_fetch_assoc($rssit2);
$totalRows_rssit2 = mysql_num_rows($rssit2);

mysql_select_db($database_tams, $tams);
$query_rsprg = sprintf("SELECT progid, progname FROM programme WHERE continued = %s ",  GetSQLValueString('Yes', 'text'));
$rsprg = mysql_query($query_rsprg, $tams) or die(mysql_error());
$row_rsprg = mysql_fetch_assoc($rsprg);
$totalRows_rsprg = mysql_num_rows($rsprg);

$query_state = "SELECT* FROM state";
$state = mysql_query($query_state, $tams) or die(mysql_error());
$row_state = mysql_fetch_assoc($state);
$totalRows_state = mysql_num_rows($state);

mysql_select_db($database_tams, $tams);
$query_rssubj = "SELECT * FROM subject";
$rssubj = mysql_query($query_rssubj, $tams) or die(mysql_error());
$row_rssubj = mysql_fetch_assoc($rssubj);
$totalRows_rssubj = mysql_num_rows($rssubj);

mysql_select_db($database_tams, $tams);
$query_rsgrd = "SELECT * FROM grade";
$rsgrd = mysql_query($query_rsgrd, $tams) or die(mysql_error());
$row_rsgrd = mysql_fetch_assoc($rsgrd);
$totalRows_rsgrd = mysql_num_rows($rsgrd);
 


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
	doLogout( $site_root.'/prospective' );   
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- InstanceBeginEditable name="doctitle" -->
<?php require('../param/site.php'); ?>
<title><?php echo $university ?> </title>
<!-- InstanceEndEditable -->
<link href="../css/template.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../scripts/jquery.js"></script>
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="../css/menulink.css" rel="stylesheet" type="text/css" />
<link href="../css/footer.css" rel="stylesheet" type="text/css" />
<link href="../css/sidemenu.css" rel="stylesheet" type="text/css" />
<script>
    $(function(){
        function toggleLga(action) {
            var lga = $('#lga');
            
            if(action === 'hide') {
                lga.children('.og').hide();
                lga.children('.others').show();
            }else {
                lga.children('.og').show();
                lga.children('.others').hide();
            }
        }
        
        if($('#soforig').val() == 27) {
            toggleLga('show');
        }else {
            toggleLga('hide');
        }
        
        
        $('#soforig').change(function(){
            var stid = $(this).val();
            if(stid == 27){
                toggleLga('show');                
            }else{
                toggleLga('hide');
                
            }
        });
        
    });
        
</script>
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
        <td><!-- InstanceBeginEditable name="pagetitle" -->Post UTME Application Form<!-- InstanceEndEditable --></td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include '../include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
      <table  align="center" class="table">
      <tr>
       <td height="680">
           <form action="<?php echo $editFormAction?>" method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-signin" >
          <p>&nbsp; </p>
          <table width="666"  ><tr><td width="585">
          <fieldset >
            <legend>Bio Data</legend>
            <table  width="320" border="0"  class="table table-hover table-striped table-bordered">
              <tr>
                    <td width="169" align="left" valign="top">First Name :</td>
                    <td width="442" align="left" valign="top">
                        <input type="text" name="fname" id="fname" value="<?php echo $row_rschk['fname']?>" required="required"/>
                    </td>
              </tr>
              <tr>
                    <td valign="top" align="left">Middle Name : </td>
                    <td align="left" valign="top" >
                        <input type="text" name="mname" id="mname" value="<?php echo $row_rschk['mname']?>" required="required"/>
                    </td>
              </tr>
              <tr>
                    <td valign="top" align="left">Last Name : </td>
                    <td align="left" valign="top">
                        <input type="text" name="lname" id="lname" value="<?php echo $row_rschk['lname']?>" required="required"/>
                    </td>
              </tr>
              <tr>
                <td valign="top" align="left">Sex :</td>
                <td align="left" valign="top">
                    <select name="sex" id="sex" required="required">
                        <option value="">--Choose--</option>
                        <option value="M" <?php if('M' == $row_rschk['Sex'])echo 'selected'?>>Male</option>
                        <option value="F" <?php if('F' == $row_rschk['Sex'])echo 'selected'?>>Female</option>
                    </select>
                </td>
              </tr>
              
              <tr>
                    <td valign="top" align="left">Date of Birth :</td>
                    <td align="left" valign="top">
                        <input type="text" name="dob" id="dob" value="<?php echo $row_rschk['DoB']?>" required="required"/>
                        <span style="color: #999999"> yyyy-mm-dd</span>
                    </td>
              </tr>
              <tr>
                <td valign="top" align="left">Age :</td>
                <td align="left" valign="top">
                    <input type="text" name="age" id="age" value="<?php echo $row_rschk['age']?>" required="required"/>
                </td>
              </tr
              <tr>
                <td align="left" valign="top">Health Status : </td>
                <td align="left" valign="top">
                    <select name="health" id="health" required="required">
                        <option value="">--Choose--</option>
                        <option value="Fit" <?php if('Fit' == $row_rschk['healthStatus'])echo 'selected'?>>Fit</option>
                        <option value="Disable" <?php if('Disable' == $row_rschk['healthStatus'])echo 'selected'?>>Disable</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td align="left" valign="top">Passport  : </td>
                <td align="left" valign="top">
                    <input type="file" name="filename" id="image" />
                </td>
              </tr>
              <tr>
                <td align="left" valign="top">&nbsp;</td>
                <td align="left" valign="top">&nbsp;</td>
              </tr>
            </table>
            <p>&nbsp;</p>
        </fieldset>
                </td>
            </tr>
                <tr>
                    <td>
                        <fieldset>
                            <legend>Personal Data</legend>
                            <table class="table table-hover table-striped table-bordered">
                                <tr>
                                    <td>National ID No.(if any)</td>
                                    <td><input type="text" name="id_num" value="<?php echo $row_rschk['id_num']?>"/></td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">E-mail Address : </td>
                                    <td align="left" valign="top">
                                        <input type="text" name="email" id="email" value="<?php echo $row_rschk['email']?>" size="50" required="required"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">Phone No : </td>
                                    <td align="left" valign="top">
                                        <input type="text" name="phone" id="phone" value="<?php echo $row_rschk['phone'];?>" required="required"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top">Address : </td>
                                    <td align="left" valign="top">
                                        <textarea name="address" id="address" cols="35" rows="5" required="required"><?php echo $row_rschk['address']?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top">Religion : </td>
                                    <td align="left" valign="top">
                                        <select name="religion" id="religion">
                                            <option value="">--Choose--</option>
                                            <option value="Christianity" <?php if('Christianity' == $row_rschk['Religion'])echo 'selected'?>>Christianity</option>
                                            <option value="Islam" <?php if('Islam' == $row_rschk['Religion'])echo 'selected'?>>Islam</option>
                                        </select>
                                    </td>
                                </tr>  
                                <tr>
                                    <td align="left" valign="top">Nationality :</td>
                                    <td align="left" valign="top">
                                       
                                        <select name="nationality" id="soforig" required="required">
                                            <option value="">Choose</option>
                                            <option value="Nigeria" <?php if('Nigeria' == $row_rschk['nationality'])echo 'selected'?>>Nigeria</option>
                                            <option value="Benin" <?php if('Benin' == $row_rschk['nationality'])echo 'selected'?>>Benin</option>
                                            <option value="Others" <?php if('Others' == $row_rschk['nationality'])echo 'selected'?>>Others</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Foreign Lang.</td>
                                    <td>
                                        <select name="other_lang" required="required">
                                            <option value="">-Choose-</option>
                                            <option value="Yes" <?php if('Yes' == $row_rschk['other_lang'])echo 'selected'?>>Yes</option>
                                            <option value="No" <?php if('No' == $row_rschk['other_lang'])echo 'selected'?>>No</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">Sponsor's Name : </td>
                                    <td align="left" valign="top">
                                        <input type="text" name="sponsorname" id="sponsorname" value="<?php echo $row_rschk['sponsorname']?>" required="required"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">Sponsor's Phone : </td>
                                    <td align="left" valign="top">
                                        <input type="text" name="sponsorphn" id="sponsorphn" value="<?php echo $row_rschk['sponsorphn']?>" required="required"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="left">Sponsor's Address : </td>
                                    <td align="left" valign="top"> 
                                        <textarea name='sponsoradrs' cols="35" rows="5" required="required"><?php echo $row_rschk['sponsoradrs']?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </fieldset> 
                    </td>
                </tr>
          </table>
          
          <table width="670"><tr><td width="651">
          <fieldset>
            <legend>Academic Data</legend>
            <table width="622" border="0" align="left">
                <tr>
                    <td colspan="4">
                        <table width="320" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="4">SCHOOL ATTENDED WITH DATES</th>
                                </tr>
                                <tr>
                                    <th>S/N</th>
                                    <th>School Name</th>
                                    <th>From</th>
                                    <th>To</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                               
                                $pschl1 =  explode("-", $row_rschk['past_school1']);
                                $pschl2 =  explode("-", $row_rschk['past_school2']);
                                $pschl3 =  explode("-", $row_rschk['past_school3']);
                                ?>
                                <tr>
                                    <td>1</td>
                                    <td><input type="text"name="schname1" required="required" size="40" value="<?php echo $pschl1[0]?>"/></td>
                                    <td><input type="text"name="from1" required="required" placeholder="YYYY" size="5" value="<?php echo $pschl1[1]?>"/></td>
                                    <td><input type="text"name="to1" required="required" placeholder="YYYY" size="5" value="<?php echo $pschl1[2]?>"/></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><input type="text" name="schname2" required="required" size="40" value="<?php echo $pschl2[0]?>"/></td>
                                    <td><input type="text" name="from2"  placeholder="YYYY" size="5" value="<?php echo $pschl2[1]?>"/></td>
                                    <td><input type="text" name="to2"  placeholder="YYYY" size="5" value="<?php echo $pschl2[2]?>"/></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><input type="text" name="schname3" size="40" value="<?php echo $pschl3[0]?>"/></td>
                                    <td><input type="text" name="from3"  placeholder="YYYY" size="5" value="<?php echo $pschl3[1]?>"/></td>
                                    <td><input type="text" name="to3"  placeholder="YYYY" size="5" value="<?php echo $pschl3[2]?>"/></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p>&nbsp;</p>
                        <table width="320" class="table table-hover table-striped table-bordered">
                            <tr>
                                <th colspan="2"> Programme Choices</th>
                            </tr>
                            <tr>
                                <td>1st Choice of Programme : </td>
                                <td align="left">  
                                    <select name="progid1" id="progid1" style="width: 400px" required="required" >
                                        <option value="-1">--Choose programme--</option>
                                        <?php
                                                        do {  
                                                        ?>
                                        <option value="<?php echo $row_rsprg['progid']?>" <?php if($row_rsprg['progid'] == $row_rschk['progid1'])echo 'selected'?>><?php echo $row_rsprg['progname']?></option>
                                        <?php
                                          } while ($row_rsprg = mysql_fetch_assoc($rsprg));
                                            $rows = mysql_num_rows($rsprg);
                                            if($rows > 0) {
                                                mysql_data_seek($rsprg, 0);
                                                    $row_rsprg = mysql_fetch_assoc($rsprg);
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td> 2nd Choice of programme :</td>
                                <td align="left">  
                                    <select name="progid2" id="progid2" style="width: 400px" required="required">
                                        <option value="-1">--Choose programme--</option>
                                        <?php
                                          do {  
                                          ?>
                                        <option value="<?php echo $row_rsprg['progid']?>" <?php if($row_rsprg['progid'] == $row_rschk['progid2'])echo 'selected'?>><?php echo $row_rsprg['progname']?></option>
                                        <?php
                                            } while ($row_rsprg = mysql_fetch_assoc($rsprg));
                                              $rows = mysql_num_rows($rsprg);
                                              if($rows > 0) {
                                                    mysql_data_seek($rsprg, 0);
                                                    $row_rsprg = mysql_fetch_assoc($rsprg);
                                            }
                                        ?>
                                    </select>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td> Programme Mode :</td>
                                <td>
                                    <select name="progmode" required="required">
                                        <option value="">-Choose-</option>
                                        <option value="Diploma">Diploma</option>
                                        <option value="Degree">Degree</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td> Study Mode :</td>
                                <td>
                                    <select name="study_mode" required="required">
                                        <option value="">-Choose-</option>
                                        <option value="Full-time">Full-Time</option>
                                        <option value="Part-Time">Part-Time</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <p>&nbsp;</p>
                    </td>
                </tr>
                   
                <tr>
                  <td><strong>O'Level 1st Sitting </strong></td>
                  <td><strong> O'Level 2nd Sitting </strong></td>
                  </tr>
                <tr>
                  <td><table width="309" border="0" class="table table-hover table-striped table-bordered">
                  <tr>
                      <td>Exam No : </td>
                      <td>
                          <input name="examnumber[]" type="text" id="examnumber" size="10" value="<?php echo $row_rssit1['examnumber'];?>" required="required"/>
                      </td>
                  </tr>
                  <tr>
                    <td width="155">Exam Type : </td>
                    <td width="133"> 
                    <select name="exmtyp[]" id="exmtyp" style="width: 85px">
                      <option value="-1">--Choose--</option>
                      <option value="WASCE(MAY/JUNE)" <?php if("WASCE(MAY/JUNE)" == $row_rssit1['examtype'])echo 'selected'?>>WASCE(MAY/JUNE)</option>
                      <option value="WASCE(Private)" <?php if("WASCE(Private)" == $row_rssit1['examtype'])echo 'selected'?>>WASCE(Private)</option>
                      <option value="NECO" <?php if("NECO" == $row_rssit1['examtype'])echo 'selected'?>>NECO</option>
                      <option value="NECO(Private)" <?php if("NECO(Private)" == $row_rssit1['examtype'])echo 'selected'?>>NECO(Private)</option>
                    </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Exam Year : </td>
                    <td>
                        <input name="exmyr[]" type="text" id="exmyr" size="8" value="<?php echo $row_rssit1['examyear']?>" maxlength="4" required="required"/>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Subject </strong></td>
                    <td><strong>Grade</strong></td>
                  </tr>
                  <?php for($i=0;$i<$totalRows_rssit1;$i++){?>
                  <tr>
                    <td>
                        <select name="subj[first][]" id="subj['name'][]">
                      <option value="-1">--Choose--</option>
                      <?php
					  do {  
					  ?>
                      <option value="<?php echo $row_rssubj['subjid']?>" <?php if($row_rssubj['subjid'] == $row_rssit1['subject'])echo 'selected'?>><?php echo $row_rssubj['subjname']?></option>
                      <?php
					  } while ($row_rssubj = mysql_fetch_assoc($rssubj));
						$rows = mysql_num_rows($rssubj);
						if($rows > 0) {
							mysql_data_seek($rssubj, 0);
							$row_rssubj = mysql_fetch_assoc($rssubj);
						}
					  ?>
                    </select>
                    </td>
                    <td> 
                        <select name="grade[first][]" id="subj['grade'][]" required="required">
                      <option value="-1">grade</option>
                      <?php
                        do {  
                        ?>
                      <option value="<?php echo $row_rsgrd['grdid']?>" <?php if($row_rsgrd['grdid'] == $row_rssit1['grade'])echo 'selected'?>><?php echo $row_rsgrd['grdname']?></option>
                      <?php
                        } while ($row_rsgrd = mysql_fetch_assoc($rsgrd));
                          $rows = mysql_num_rows($rsgrd);
                          if($rows > 0) {
                              mysql_data_seek($rsgrd, 0);
                                  $row_rsgrd = mysql_fetch_assoc($rsgrd);
                          }
                        ?>
                    </select></td>
                  </tr>
                  <?php $row_rssit1 = mysql_fetch_assoc($rssit1);}?>
                  
                  <?php for($i=0;$i<9-$totalRows_rssit1;$i++){?>
                  <tr>
                    <td>
                        <select name="subj[first][]" id="subj['name'][]" required="required">
                            <option value="-1">--Choose--</option>
                            <?php
                            do {  
                            ?>
                            <option value="<?php echo $row_rssubj['subjid']?>"><?php echo $row_rssubj['subjname']?></option>
                            <?php
                            } while ($row_rssubj = mysql_fetch_assoc($rssubj));
                                  $rows = mysql_num_rows($rssubj);
                                  if($rows > 0) {
                                          mysql_data_seek($rssubj, 0);
                                          $row_rssubj = mysql_fetch_assoc($rssubj);
                                  }
                            ?>
                        </select>
                    </td>
                    <td><select name="grade[first][]" id="subj['grade'][]">
                      <option value="-1">grade</option>
                      <?php
do {  
?>
                      <option value="<?php echo $row_rsgrd['grdid']?>"><?php echo $row_rsgrd['grdname']?></option>
                      <?php
} while ($row_rsgrd = mysql_fetch_assoc($rsgrd));
  $rows = mysql_num_rows($rsgrd);
  if($rows > 0) {
      mysql_data_seek($rsgrd, 0);
	  $row_rsgrd = mysql_fetch_assoc($rsgrd);
  }
?>
                    </select></td>
                  </tr>
                  <?php }?>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
                <td><table width="309" border="0" class="table table-hover table-striped table-bordered">
                <tr>
                	<td>Exam No : </td>
                    <td><input name="examnumber[]" type="text" id="examnumber" size="10" value="<?php echo $row_rssit2['examnumber']?>"/></td>
                </tr>
                  <tr>
                    <td width="155">Exam Type : </td>
                    <td width="133">
                        <select name="exmtyp[]" id="exmtyp2" style="width: 85px">
                            <option value="-1">--Choose--</option>
                            <option value="WASCE(MAY/JUNE)" <?php if("WASCE(MAY/JUNE)" == $row_rssit2['examtype'])echo 'selected'?>>WASCE(MAY/JUNE)</option>
                            <option value="WASCE(Private)" <?php if("WASCE(Private)" == $row_rssit2['examtype'])echo 'selected'?>>WASCE(Private)</option>
                            <option value="NECO" <?php if("NECO" == $row_rssit2['examtype'])echo 'selected'?>>NECO</option>
                            <option value="NECO(Private)" <?php if("NECO(Private)" == $row_rssit2['examtype'])echo 'selected'?>>NECO(Private)</option>
                        </select>
                    </td>
                  </tr>
                  <tr>
                        <td>Exam Year : </td>
                        <td><input name="exmyr[]" type="text" id="exmyr2" size="8" value="<?php echo $row_rssit2['examyear']?>" maxlength="4"/></td>
                  </tr>
                  <tr>
                    <td><strong>Subject </strong></td>
                    <td><strong>Grade</strong></td>
                  </tr>
                  <?php for($i=0;$i<$totalRows_rssit2;$i++){?>
                  <tr>
                      <td><select name="subj[second][]" id="subj2['name'][]">
                      <option value="-1">--Choose-</option>
                      <?php
					  do {  
					  ?>
                      <option value="<?php echo $row_rssubj['subjid']?>" <?php if($row_rssubj['subjid'] == $row_rssit2['subject'])echo 'selected'?>><?php echo $row_rssubj['subjname']?></option>
                      <?php
					  } while ($row_rssubj = mysql_fetch_assoc($rssubj));
						$rows = mysql_num_rows($rssubj);
						if($rows > 0) {
							mysql_data_seek($rssubj, 0);
							$row_rssubj = mysql_fetch_assoc($rssubj);
						}
					  ?>
                    </select></td>
                    <td><select name="grade[second][]" id="subj2['grade'][]">
                      <option value="-1">grade</option>
                      <?php
do {  
?>
                      <option value="<?php echo $row_rsgrd['grdid']?>" <?php if($row_rsgrd['grdid'] == $row_rssit2['grade'])echo 'selected'?>><?php echo $row_rsgrd['grdname']?></option>
                      <?php
} while ($row_rsgrd = mysql_fetch_assoc($rsgrd));
  $rows = mysql_num_rows($rsgrd);
  if($rows > 0) {
      mysql_data_seek($rsgrd, 0);
	  $row_rsgrd = mysql_fetch_assoc($rsgrd);
  }
?>
                    </select></td>
                  </tr>
                  <?php $row_rssit2 = mysql_fetch_assoc($rssit2);}?>
                  
                  
                  <?php for($i=0;$i<9-$totalRows_rssit2;$i++){?>
                  <tr>
                    <td><select name="subj[second][]" id="subj2['name'][]">
                      <option value="-1">--Choose-</option>
                      <?php
					  do {  
					  ?>
                      <option value="<?php echo $row_rssubj['subjid']?>"><?php echo $row_rssubj['subjname']?></option>
                      <?php
					  } while ($row_rssubj = mysql_fetch_assoc($rssubj));
						$rows = mysql_num_rows($rssubj);
						if($rows > 0) {
							mysql_data_seek($rssubj, 0);
							$row_rssubj = mysql_fetch_assoc($rssubj);
						}
					  ?>
                    </select></td>
                    <td><select name="grade[second][]" id="subj2['grade'][]">
                      <option value="-1">grade</option>
                      <?php
                            do {  
                            ?>
                            <option value="<?php echo $row_rsgrd['grdid']?>"><?php echo $row_rsgrd['grdname']?></option>
                            <?php
                            } while ($row_rsgrd = mysql_fetch_assoc($rsgrd));
                              $rows = mysql_num_rows($rsgrd);
                              if($rows > 0) {
                                  mysql_data_seek($rsgrd, 0);
                                      $row_rsgrd = mysql_fetch_assoc($rsgrd);
                              }
                            ?>
                    </select></td>
                  </tr>
                  <?php }?>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            <p>&nbsp;</p>
          </fieldset>           
           </td></tr></table>
          <table width="250" border="0" align="center">
              <input type="hidden" name="access" value="11"/>
           <p>&nbsp;</p>
            <tr align="center">
              <!--<td><input type="submit" name="submit" id="submit" value="Submit Form"  class="btn btn-primary"/></td>-->
              <td><input type="submit" name="save" id="save" value="Submit Application"  class="btn btn-primary"/></td>
            </tr>
          </table>
        </form></td>
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