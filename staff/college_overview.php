<?php require_once('../Connections/tams.php'); 

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


mysql_select_db($database_tams, $tams);

$query_dept = sprintf("SELECT deptid, deptname "
                        . "FROM department "
                        . "WHERE colid = %s "
                        . "ORDER BY deptname ASC", 
                        GetSQLValueString(getSessionValue('cid'), 'int'));
$dept = mysql_query($query_dept, $tams) or die(mysql_error());
$row_dept = mysql_fetch_assoc($dept);
$totalRows_dept = mysql_num_rows($dept);

$query_deptstaff = sprintf("SELECT d.deptname, count(lectid) as `count` "
                            . "FROM lecturer l "
                            . "JOIN department d ON d.deptid = l.deptid "
                            . "WHERE d.colid = %s "
                            . "GROUP BY l.deptid "
                            . "ORDER BY d.deptname ASC", 
                            GetSQLValueString(getSessionValue('cid'), 'int'));
$deptstaff = mysql_query($query_deptstaff) or die(mysql_error());
$row_deptstaff = mysql_fetch_assoc($deptstaff);
$totalRows_deptstaff = mysql_num_rows($deptstaff);
	
$query_deptstud = sprintf("SELECT d.deptname, count(s.stdid) as `count` "
                            . "FROM student s "
                            . "JOIN programme p ON p.progid = s.progid "
                            . "JOIN department d ON d.deptid = p.deptid "                            
                            . "WHERE d.colid = %s "
                            . "GROUP BY p.deptid "
                            . "ORDER BY d.deptname ASC", 
                            GetSQLValueString(getSessionValue('cid'), 'int'));
$deptstud = mysql_query($query_deptstud) or die(mysql_error());
$row_deptstud = mysql_fetch_assoc($deptstud);
$totalRows_deptstud = mysql_num_rows($deptstud);


$query_prog = sprintf("SELECT progid, progname "
                        . "FROM programme p "
                        . "JOIN department d ON d.deptid = p.deptid "
                        . "WHERE d.colid = %s "
                        . "ORDER BY progname ASC", 
                        GetSQLValueString(getSessionValue('cid'), 'int'));
$prog = mysql_query($query_prog, $tams) or die(mysql_error());
$row_prog = mysql_fetch_assoc($prog);
$totalRows_prog = mysql_num_rows($prog);

$query_progstud = sprintf("SELECT p.progid, count(s.stdid) as `count` "
                            . "FROM student s "
                            . "JOIN programme p ON p.progid = s.progid "
                            . "JOIN department d ON d.deptid = p.deptid "
                            . "WHERE d.colid = %s "
                            . "GROUP BY p.progid "
                            . "ORDER BY p.progname ASC", 
                            GetSQLValueString(getSessionValue('cid'), 'int'));
$progstud = mysql_query($query_progstud) or die(mysql_error());
$row_progstud = mysql_fetch_assoc($progstud);
$totalRows_progstud = mysql_num_rows($progstud);

$prog_stud = array();
for($idx = 0; $idx < $totalRows_progstud; $idx++, $row_progstud = mysql_fetch_assoc($progstud)) {
    $prog_stud[$row_progstud['progid']] = $row_progstud['count'];

}

$total = 0;

$query_RsCsReg = sprintf("SELECT d.deptid, d.deptname, count( r.stdid ) AS `count`
                            FROM registration r
                            RIGHT JOIN student s ON r.stdid = s.stdid
                            JOIN programme p ON p.progid = s.progid
                            JOIN department d ON d.deptid = p.deptid 							
                            WHERE r.status = 'Registered' 
                            GROUP BY p.deptid 
                            ORDER BY d.deptname");
$RsCsReg = mysql_query($query_RsCsReg) or die(mysql_error());
$row_RsCsReg = mysql_fetch_assoc($RsCsReg);
$totalRows_RsCsReg = mysql_num_rows($RsCsReg);

$reg = array();
for($idx = 0; $idx < $totalRows_RsCsReg; $idx++, $row_RsCsReg = mysql_fetch_assoc($RsCsReg)) {
	$reg[$row_RsCsReg['deptid']] = $row_RsCsReg['count'];

}
	
	
mysql_select_db($database_tams, $tams);
$query_RsCsRegAprv = sprintf("SELECT d.deptid, d.deptname, count( r.stdid ) AS `count`
							FROM registration r
							RIGHT JOIN student s ON r.stdid = s.stdid
							JOIN programme p ON p.progid = s.progid
							JOIN department d ON d.deptid = p.deptid 
							WHERE r.approved = 'TRUE' 
							GROUP BY p.deptid 
							ORDER BY d.deptname");
$RsCsRegAprv = mysql_query($query_RsCsRegAprv) or die(mysql_error());
$row_RsCsRegAprv = mysql_fetch_assoc($RsCsRegAprv);
$totalRows_RsCsRegAprv = mysql_num_rows($RsCsRegAprv);

$cleared = array();
$totalCleard = 0;
for($idx = 0; $idx < $totalRows_RsCsRegAprv; $idx++, $row_RsCsRegAprv = mysql_fetch_assoc($RsCsRegAprv)) {
    $cleared[$row_RsCsRegAprv['deptid']] = $row_RsCsRegAprv['count'];
    $totalCleard = ($totalCleard + $cleared[$row_RsCsRegAprv['deptid']]);
	
}

$query_RsColCsReg = sprintf("SELECT c.colid, c.colname,  count( r.stdid ) AS `count`
                                FROM registration r
                                RIGHT JOIN student s ON r.stdid = s.stdid
                                JOIN programme p ON p.progid = s.progid
                                JOIN department d ON d.deptid = p.deptid 
                                JOIN college c ON c.colid = d.colid
                                WHERE r.status = 'Registered' 
                                GROUP BY c.colid 
                                ORDER BY c.colname");
$RsColCsReg = mysql_query($query_RsColCsReg) or die(mysql_error());
$row_RsColCsReg = mysql_fetch_assoc($RsColCsReg);
$totalRows_RsColCsReg = mysql_num_rows($RsColCsReg);

$progreg = array();
for($idx = 0; $idx < $totalRows_RsColCsReg; $idx++, $row_RsColCsReg = mysql_fetch_assoc($RsColCsReg)) {
	$progreg[$row_RsColCsReg['colid']] = $row_RsColCsReg['count'];

}

mysql_select_db($database_tams, $tams);
$query_RsColCsRegAprv = sprintf("SELECT c.colid, d.deptid, d.deptname, count( r.stdid ) AS `count`
                                    FROM registration r
                                    RIGHT JOIN student s ON r.stdid = s.stdid
                                    JOIN programme p ON p.progid = s.progid
                                    JOIN department d ON d.deptid = p.deptid
                                    JOIN college c ON c.colid = d.colid
                                    WHERE r.approved = 'TRUE' 
                                    GROUP BY c.colid 
                                    ORDER BY c.colname");
$RsColCsRegAprv = mysql_query($query_RsColCsRegAprv) or die(mysql_error());
$row_RsColCsRegAprv = mysql_fetch_assoc($RsColCsRegAprv);
$totalRows_RsColCsRegAprv = mysql_num_rows($RsColCsRegAprv);

$progcleared = array();

for($idx = 0; $idx < $totalRows_RsColCsRegAprv; $idx++, $row_RsColCsRegAprv = mysql_fetch_assoc($RsColCsRegAprv)) {
	 $progcleared[$row_RsColCsRegAprv['colid']] = $row_RsColCsRegAprv['count'];	
	
}

mysql_select_db($database_tams, $tams);
$query_DeptPay = sprintf("SELECT d.deptid, d.deptname, count( ph.stdid ) AS `count`
                            FROM payhistory ph
                            RIGHT JOIN student s ON ph.stdid = s.stdid
                            JOIN programme p ON p.progid = s.progid
                            JOIN department d ON d.deptid = p.deptid
                            JOIN college c ON c.colid = d.colid
                            WHERE ph.status = 'paid' 
                            GROUP BY d.deptid 
                            ORDER BY d.deptname");
$DeptPay = mysql_query($query_DeptPay) or die(mysql_error());
$row_DeptPay = mysql_fetch_assoc($DeptPay);
$totalRows_DeptPay = mysql_num_rows($DeptPay);

$deptpaid = array();

for($idx = 0; $idx < $totalRows_DeptPay; $idx++, $row_DeptPay = mysql_fetch_assoc($DeptPay)) {
	 $deptpaid[$row_DeptPay['deptid']] = $row_DeptPay['count'];
		
}


mysql_select_db($database_tams, $tams);
$query_ColPay = sprintf("SELECT c.colid, c.colname, count( ph.stdid ) AS `count`
                            FROM payhistory ph
                            RIGHT JOIN student s ON ph.stdid = s.stdid
                            JOIN programme p ON p.progid = s.progid
                            JOIN department d ON d.deptid = p.deptid
                            JOIN college c ON c.colid = d.colid
                            WHERE ph.status = 'paid' 
                            GROUP BY c.colid 
                            ORDER BY c.colname");
$ColPay = mysql_query($query_ColPay) or die(mysql_error());
$row_ColPay = mysql_fetch_assoc($ColPay);
$totalRows_ColPay = mysql_num_rows($ColPay);

$progpaid = array();

for($idx = 0; $idx < $totalRows_ColPay; $idx++, $row_ColPay = mysql_fetch_assoc($ColPay)) {
	 $progpaid[$row_ColPay['colid']] = $row_ColPay['count'];
		
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
	doLogout($site_root);  
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <!-- InstanceBegin template="/Templates/icttemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<?php require('../param/site.php'); ?>
<title><?php echo $university ?> </title>
<!-- InstanceEndEditable -->
<link href="../css/template.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script src="../SpryAssets/SpryCollapsiblePanel.js" type="text/javascript"></script>
<link href="../SpryAssets/SpryCollapsiblePanel.css" rel="stylesheet" type="text/css" />
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
        <td><!-- InstanceBeginEditable name="pagetitle" -->Overview<!-- InstanceEndEditable --></td>
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
        <div id="CollapsiblePanel1" class="CollapsiblePanel" >
          <div class="CollapsiblePanelTab" tabindex="0">Department</div>
          <div class="CollapsiblePanelContent">
          		<fieldset>
  <legend>Department</legend>
          <p>&nbsp;</p>
          <table width="632" border="0" align="center" class="table table-condensed table-striped" >
          <thead>
            <tr>
              <th width="44" align="center" >S/n</th>
              <th width="220" align="center">Departments</th>
              <th width="50" align="center">Staff</th>
              <th width="50" align="center">Student </th>
              <th width="50" align="center">Registered</th>
              <th width="50" align="center">Cleared</th>
              <th width="50" align="center">Paid</th>
            </tr>
            </thead> 
            <?php 
                $i = 1;
                $totalstaff= 0;
                $totalstudent = 0;
                $totalReg = 0;
                $totalCleard = 0;
                
                do { 

                     $totalstaff =($totalstaff + $row_deptstaff['count']); 
                     $totalstudent = ($totalstudent + $row_deptstud['count']);
                     $totalReg = ($totalReg + $row_RsCsReg['count']);

            ?>

              <tr align="center" >
                <td width="44"><?php echo $i;?></td>
                <td width="220"><?php echo $row_dept['deptname']; ?></td>
                <td width="50">
                    <a target="_blank" href="stafflist.php?did=<?php echo $row_dept['deptid']?>">
                        <?php echo $row_deptstaff['count']?>
                    </a>
                </td>
                <td width="50">
                    <a target="_blank" href="studentlist.php?did=<?php echo $row_dept['deptid']?>">
                        <?php echo $row_deptstud['count'] ?>
                    </a>
                </td>
                <td width="50">
                    <a target="_blank" href="studentlist.php?action=reg&did=<?php echo $row_dept['deptid']?>">
                        <?php echo isset($reg[$row_dept['deptid']])? $reg[$row_dept['deptid']]: 0; ?>
                    </a>
                </td>
                <td  width="50">
                    <a target="_blank" href="studentlist.php?action=clear&did=<?php echo $row_dept['deptid']?>">
                        <?php echo isset($cleared[$row_dept['deptid']])? $cleared[$row_dept['deptid']]: 0?>
                    </a>
                </td>
                <td  width="50">
                    <a target="_blank" href="studentlist.php?action=paid&did=<?php echo $row_dept['deptid']?>">
                        <?php echo isset( $deptpaid[$row_dept['deptid']])?  $deptpaid[$row_dept['deptid']]: 0?>
                    </a>
                </td>
              </tr>
              
              <?php 
			
                        $i++;

                        $row_deptstud = mysql_fetch_assoc($deptstud);
                        $row_RsCsRegAprv = mysql_fetch_assoc($RsCsRegAprv);
                        $row_deptstaff = mysql_fetch_assoc($deptstaff); 
                } while ($row_dept = mysql_fetch_assoc($dept)); 
                ?>
            <tr>
              <th width="44" align="center"><strong>Total </strong></th>
              <th width="220" align="center"><?php echo $totalRows_dept?></th>
              <th width="50" align="center"><?php echo $totalstaff?></th>
              <th width="50" align="center"><?php echo $totalstudent?></th>
              <th width="50" align="center"><?php echo array_sum($reg); ?></th>
              <th width="50" align="center"><?php echo array_sum($cleared) ?></th>
              <th width="50" align="center"><?php echo array_sum( $deptpaid)?></th>
          </tr>
          </table>
          <p>&nbsp;</p>
      </fieldset>
       
          </div>
        </div>
        <p>&nbsp;</p><p>&nbsp;</p>
        <div id="CollapsiblePanel2" class="CollapsiblePanel">
          <div class="CollapsiblePanelTab" tabindex="0">Programme</div>
          <div class="CollapsiblePanelContent">
          <fieldset>
  <legend>Programme</legend>
          <p>&nbsp;</p>
          <table width="632" border="0" align="center" class="table table-condensed table-striped" >
          <thead>
            <tr>
              <th width="44" align="center" >S/n</th>
              <th width="220" align="center">Programme</th>
              <th width="50" align="center">Student </th>
              <th width="50" align="center">Registered</th>
              <th width="50" align="center">Cleared</th>
              <th width="50" align="center">Paid</th>
            </tr>
            </thead>
            <?php $i = 1;

                do { 

            ?>
          
              <tr align="center" >
                <td width="44"><?php echo $i;?></td>
                <td width="220"><?php echo $row_prog['progname']?></td>                
                <td width="50">
                    <a target="_blank" href="studentlist.php?pid=<?php echo $row_prog['progid']?>">
                        <?php echo isset($prog_stud[$row_prog['progid']])? $prog_stud[$row_prog['progid']]: 0?>
                    </a>
                </td>
                <td width="50">
                    <a target="_blank" href="studentlist.php?action=reg&pid=<?php echo $row_prog['progid']?>">
                        <?php echo isset($progreg[$row_prog['progid']])? $progreg[$row_prog['progid']]: 0; ?>
                    </a>
                </td>
                <td width="50">
                    <a target="_blank" href="studentlist.php?action=clear&pid=<?php echo $row_prog['progid']?>">
                        <?php echo isset($progcleared[$row_prog['progid']])? $progcleared[$row_prog['progid']]: 0?>
                    </a>
                </td>
                <td width="50">
                    <a target="_blank" href="studentlist.php?action=paid&pid=<?php echo $row_prog['progid']?>">
                        <?php echo isset($progpaid[$row_prog['progid']])? $progpaid[$row_prog['progid']]: 0?>
                    </a>
                </td>
              </tr>
              
              <?php 
			
                        $i++;
                        $row_RsColCsRegAprv = mysql_fetch_assoc($RsColCsRegAprv);
                        $row_progstud  = mysql_fetch_assoc($progstud);
                    } while ($row_prog = mysql_fetch_assoc($prog)); 
                ?>
            <tr>
              <th width="44" align="center"><strong>Total </strong></th>
              <th width="220" align="center"><?php echo $totalRows_prog?></th>
              <th width="50" align="center"><?php echo array_sum($prog_stud)?></th>
              <th width="50" align="center"><?php echo array_sum($progreg); ?></th>
              <th width="50" align="center"><?php echo array_sum($progcleared); ?></th>
              <th width="50" align="center"><?php echo array_sum($progpaid); ?></th>
          </tr>
          </table>
          <p>&nbsp;</p>
      </fieldset>
          </div>
        </div></td>
      </tr>
    </table>
    <script type="text/javascript">
        var CollapsiblePanel1 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel1", {contentIsOpen:false});
        var CollapsiblePanel2 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel2",{contentIsOpen:false});
    </script>
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
<?php
mysql_free_result($dept);
?>
