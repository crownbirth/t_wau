<?php require_once('../Connections/tams.php'); ?>
<?php 
require_once('../param/param.php');
if (!isset($_SESSION)) {
  session_start();
}

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

$filter = "";
$colname_sess = "-1";
if (isset($_GET['sid'])) {
  $colname_sess = $_GET['sid'];
  $filter = "WHERE sesid=".$colname_sess;
}

$deptid = getSessionValue('did');

mysql_select_db($database_tams, $tams);
$query_sess = sprintf("SELECT * FROM `session` %s ORDER BY sesname DESC LIMIT 0,1", 
					GetSQLValueString($filter, "undefined", $filter));
$sess = mysql_query($query_sess, $tams) or die(mysql_error());
$row_sess = mysql_fetch_assoc($sess);
$totalRows_sess = mysql_num_rows($sess);

$colname_stud = "-1";
if ( getSessionValue('stid') != NULL ) {
  $colname_stud = getSessionValue('stid');
}

$query_payment = sprintf("SELECT status "
        . "FROM payhistory "
        . "WHERE stdid = %s AND sesid = %s AND status = 'paid'", 
        GetSQLValueString($colname_stud, "text"), 
        GetSQLValueString($row_sess['sesid'], "int"));
$payment = mysql_query($query_payment, $tams) or die(mysql_error());
$row_payment = mysql_fetch_assoc($payment);
$totalRows_payment = mysql_num_rows($payment);

if($totalRows_payment < 1) {
    echo 'You cannot print your examination pass!';
    exit;
}
    
$image_url = '../images/student/profile.png';
$image = array("../images/student/{$colname_stud}.jpg", "../images/student/{$colname_stud}.png");
if(realpath("{$image[0]}")) {
    $image_url = $image[0];
}elseif(realpath("{$image[1]}")){
    $image_url = $image[1];
}
    
    
$query_info = sprintf("SELECT s.stdid, s.fname, s.sex, l.title, l.fname as lfname, l.lname as llname, s.lname, s.level, s.progid, c.coltitle, p.progname, d.deptname "
        . "FROM student s,college c, programme p, department d, lecturer l "
        . "WHERE s.progid = p.progid "
        . "AND p.deptid = d.deptid "
        . "AND l.deptid = d.deptid "
        . "AND d.colid = c.colid "
        . "AND l.access = 3 "
        . "AND stdid = %s", 
        GetSQLValueString($colname_stud, "text"));
$info = mysql_query($query_info, $tams) or die(mysql_error());
$row_info = mysql_fetch_assoc($info);
$totalRows_info = mysql_num_rows($info);

$colname_regStatus1 = "-1";
if (isset($row_sess['sesid'])) {
  $colname_regStatus1 = $row_sess['sesid'];
}

mysql_select_db($database_tams, $tams);
$query_course = sprintf("SELECT c.semester, r.csid, c.csname, d.status, d.unit "
        . "FROM result r, course c, department_course d "
        . "WHERE r.stdid = %s "
        . "AND c.csid = r.csid "
        . "AND d.csid = r.csid "
        . "AND c.semester = 'F' "
        . "AND r.sesid = %s "
        . "AND d.progid = %s "
        . "ORDER BY c.semester ASC", 
        GetSQLValueString($colname_stud, "text"), 
        GetSQLValueString($row_sess['sesid'], "int"), 
        GetSQLValueString(getSessionValue('pid'), "int"));
$course = mysql_query($query_course, $tams) or die(mysql_error());
$row_course = mysql_fetch_assoc($course);
$totalRows_course = mysql_num_rows($course);

$curs ='';
$totalUnit = 0;
if($totalRows_course > 0) {
    $i = 1;
    do{
        $totalUnit += $row_course['unit'];
        $curs .='
            <tr>
                <td>'.$i++.'</td>
                <td>'.$row_course['csid'].'</td>
                <td>'.$row_course['csname'].'</td>
                <td>'.substr($row_course['status'], 0, 1).'</td>
                <td>'.$row_course['unit'].'</td>
            </tr>';
    }while ($row_course = mysql_fetch_assoc($course));
    
    $curs .= '
            <tr>
                <td colspan="4" align="right">Total Unit</td>
                <td>'.$totalUnit.'</td>
            </tr>';
}else {
    $curs = '<tr>
                <td align="center" colspan="5">You do not have any course registered for this semester !</td>
            </tr>';
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
	doLogout( $site_root );  
}

include("../mpdf/mpdf.php");
$mpdf=new mPDF('c','A4-L','','',10,10,5,5,5,5); 
$stylesheet = file_get_contents('../css/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet,1);

$html .='
<div style="text-align:center; width:100%; font-size: 20pt">
    
    <div style="float:left; width:45%;">
        <table width="90%" 
        style="border-bottom: 1px solid #999999; vertical-align: top; font-family: serif; font-size: 12pt; color: #000088;">
            <tr>
                <td width="28%" align="left"><img src="../images/logo.jpg" width="144" height="110" /></td>
                <td width="72%" align="center">
                    <div style="font-weight: bold;">
                        <h2 style="font-size: 13pt">'.$university.'</h2>
                        <h3 style="font-size: 8pt">'.$university_address.'</h3>    
                        <h3>ACADEMIC AFFAIRS DIVISION</h3>
                        <h4>'.$row_sess['sesname'].' First Semester</h4>
                        <h3 style="font-size: 11pt">EXAMINATION CLEARANCE CERTIFICATE</h3>    
                    </div>
                </td>
            </tr>
        </table>
      <table width="532">
            <tr>
                <td width="26%" align ="left"><img src="'.$image_url.'" width="132" height="147"//></td>
                <td width="74%">
                    <table width="385" valign="bottom" style="font-size: 10pt;">
                        <tr>
                            <td width="146" align ="left"><strong>Matric No :</strong></td>
                            <td width="199" align ="left">'.$row_info['stdid'].'</td>
                        </tr>
                        <tr>
                            <td width="146" align ="left"><strong>Full Name :</strong> </td>
                            <td width="199" align ="left">'.$row_info['lname'].' '.$row_info['fname'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Sex :</strong></td>
                            <td align ="left">'.$row_info['sex'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Level :</strong></td>
                            <td align ="left">'.$row_info['level'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>College :</strong></td>
                            <td align ="left">'.$row_info['coltitle'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Department :</strong></td>
                            <td align ="left">'.$row_info['deptname'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Programme :</strong></td>
                            <td align ="left">'.$row_info['progname'].'</td>
                        </tr>
                    </table> 
                </td>
            </tr>
            
        </table> 
        <table width="500" align="center" class="table table-bordered table-condensed" style="font-size:8pt">
        <thead>
            <tr>
                <th colspan="5" align="center"> COURSES REGISTERED </th>
            </tr>
            <tr>
                <th width="36">S/N</th>
                <th width="80">COURSE CODE</th>
                <th width="278">COURSE NAME</th>
                <th width="52">STATUS</th>
                <th width="30">UNIT</th> 
            </tr>
        </thead>
        <tbody>';
            
        $html .= $curs;
            
        $html .= '</tbody>
        
        </table> 
        <table style="font-size: 10pt;">
            <tr>
                <td colspan="3" >
                    I Certify that the above mention is a Bonafide student and
                    registered for the courses listed above,
                    Please allow him/her for the examination
                </td>
            </tr>
            </tr>
            <tr>
                <td width="40%" align="center">                
                <p>&nbsp;</p>
                        _______________________________<br/>
                        Sign/Date<br/>
                        
                </td>
                <td width="19%">
                  
                </td>
                <td width="40%" align="center">
                <p>&nbsp;</p>
                _______________________________<br/>';
        
        $html .= $row_info['title'].' '.$row_info['llname'].', '.$row_info['lfname']. '<br/>
                        HOD</td>   
            </tr>
              <tr>
                <td colspan="3" align="center">
                (File copy)
                    
                </td>
              </tr>
        </table>
    </div>
    
    <div style="float:right; width:45%;">
        <table width="90%" 
        style="border-bottom: 1px solid #999999; vertical-align: top; font-family: serif; font-size: 12pt; color: #000088;">
            <tr>
                <td width="28%" align="left"><img src="../images/logo.jpg" width="144" height="110" /></td>
                <td width="72%" align="center">
                    <div style="font-weight: bold;">
                        <h2 style="font-size: 13pt">'.$university.'</h2>
                        <h3 style="font-size: 8pt">'.$university_address.'</h3>    
                        <h3>ACADEMIC AFFAIRS DIVISION</h3>
                        <h4>'.$row_sess['sesname'].' First Semester</h4>
                        <h3 style="font-size: 11pt">EXAMINATION CLEARANCE CERTIFICATE</h3>    
                    </div>
                </td>
            </tr>
        </table>
      <table width="532">
            <tr>
                <td width="26%" align ="left"><img src="'.$image_url.'" width="132" height="147"/></td>
                <td width="74%">
                    <table width="385" valign="bottom" style="font-size: 10pt;">
                        <tr>
                            <td width="146" align ="left"><strong>Matric No :</strong></td>
                            <td width="199" align ="left">'.$row_info['stdid'].'</td>
                        </tr>
                        <tr>
                            <td width="146" align ="left"><strong>Full Name :</strong> </td>
                            <td width="199" align ="left">'.$row_info['lname'].' '.$row_info['fname'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Sex :</strong></td>
                            <td align ="left">'.$row_info['sex'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Level :</strong></td>
                            <td align ="left">'.$row_info['level'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>College :</strong></td>
                            <td align ="left">'.$row_info['coltitle'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Department :</strong></td>
                            <td align ="left">'.$row_info['deptname'].'</td>
                        </tr>
                        <tr>
                            <td align ="left"><strong>Programme :</strong></td>
                            <td align ="left">'.$row_info['progname'].'</td>
                        </tr>
                    </table> 
                </td>
            </tr>
            
        </table> 
        <table width="500" align="center" class="table table-bordered table-condensed" style="font-size:8pt">
        <thead>
            <tr>
                <th colspan="5" align="center"> COURSES REGISTERED </th>
            </tr>
            <tr>
                <th width="36">S/N</th>
                <th width="80">COURSE CODE</th>
                <th width="278">COURSE NAME</th>
                <th width="52">STATUS</th>
                <th width="30">UNIT</th> 
            </tr>
        </thead>
        <tbody>';
            
        $html .= $curs;
        
        $html .= '</tbody>
        
        </table> 
        <table style="font-size: 10pt;">
            <tr>
                <td colspan="3" >
                    I Certify that the above mention is a Bonafide student and
                    registered for the courses listed above,
                    Please allow him/her for the examination
                </td>
            </tr>
            <tr>
                <td width="40%" align="center">
                <p>&nbsp;</p>
                        _______________________________<br/>
                        Sign/Date<br/>
                        
                </td>
                <td width="19%">
                  
                </td>
                <td width="40%" align="center">
                <p>&nbsp;</p>
                _______________________________<br/>';
        
        $html .= $row_info['title'].' '.$row_info['llname'].', '.$row_info['lfname']. '<br/>
                        HOD</td>   
            </tr>
              <tr>
                <td colspan="3" align="center">
                (Student\'s copy)
                    
                </td>
              </tr>
        </table>
    </div>
    
    <div style="clear:both"></div>
</div>';

$mpdf->WriteHTML($html);

$mpdf->Output('Exam Pass.pdf', 'I');
exit;

