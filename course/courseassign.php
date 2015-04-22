<?php require_once('../Connections/tams.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "2,3";
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

$prog = "";
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $updated_entries = array();
    $deptid = getSessionValue('did');

    // Check for removed entries, process them if there are.
    if(isset($_POST['removed_entries']) && !empty($_POST['removed_entries'])) {
        $removedCodes = array_unique($_POST['removed_entries']);
        //array_key;
        $removedEntries = implode('\',\'', $removedCodes);
        $removeSQL = sprintf("DELETE FROM department_course "
                                . "WHERE csid IN ('%s') "
                                . "AND deptid = %s",
                               GetSQLValueString($removedEntries, "defined", $removedEntries),
                               GetSQLValueString($deptid, "int"));

        $Result = mysql_query($removeSQL, $tams) or die(mysql_error());
    }
    
    // Check for updated entries, process them if there are.
    if(isset($_POST['updated_entries']) && !empty($_POST['updated_entries'])) {
        $updated_entries = $_POST['updated_entries'];
        $updatedCodes = array_unique($_POST['updated_entries']);
        
        foreach($updatedCodes as $u) {
            $index = array_search($u, $_POST['courses']);
            
            $updateSQL = sprintf("UPDATE department_course "
                                    . "SET `status` = %s, unit = %s "
                                    . "WHERE csid = %s "
                                    . "AND deptid = %s",
                                   GetSQLValueString($_POST['status'][$index], "text"),
                                   GetSQLValueString($_POST['unit'][$index], "int"),
                                   GetSQLValueString($u, "text"),
                                   GetSQLValueString($deptid, "int"));

            $Result = mysql_query($updateSQL, $tams) or die(mysql_error());
            
        }
        
    }
    
    // Check for new entries, process them if there are.
    if(isset($_POST['courses']) && !empty($_POST['courses'])) {
        $newCodes = $_POST['courses'];
        $registeredCourses = array();
        $progid = '1';
        
        foreach($newCodes as $idx => $n) {
            if(!in_array($n, $updated_entries)) {
                
                $course = htmlentities($n);
                $dbEntry = sprintf("(%s, %s, %s, %s, %s)", 
                                GetSQLValueString($progid, "int"),
                                GetSQLValueString($deptid, "int"),
                                GetSQLValueString($course, "text"), 
                                GetSQLValueString($_POST['status'][$idx], "text"),
                                GetSQLValueString($_POST['unit'][$idx], 'int'));

                array_push($registeredCourses, $dbEntry);               
                
            }
        }
        
        if(count($registeredCourses) > 0) {
            $finalCourses = implode(',', $registeredCourses);
            $insertSQL = sprintf("INSERT INTO department_course (progid, deptid, csid, status, unit) VALUES %s;",
                                    GetSQLValueString($finalCourses, "defined", $finalCourses));

            $Result = mysql_query($insertSQL, $tams) or die(mysql_error());
        }
    }
}

$colname_dept = "-1";
if ( getSessionValue('cid') != NULL ) {
  $colname_dept = getSessionValue('cid');
}

$query_dept = sprintf("SELECT deptid, deptname FROM department WHERE colid = %s", GetSQLValueString($colname_dept, "int"));
$dept = mysql_query($query_dept, $tams) or die(mysql_error());
$row_dept = mysql_fetch_assoc($dept);
$totalRows_dept = mysql_num_rows($dept);

$colname_courses = "-1";
if (isset($row_dept['deptid'])) {
  $colname_courses = $row_dept['deptid'];
}

if (isset($_GET['did'])) {
  $colname_courses = $_GET['did'];
}

if ( getAccess() == 3 ) {
  $colname_courses = getSessionValue('did');
}

/**
    Used on the registration view
*/
$query_suggestion = sprintf("SELECT csid, csname "
                        . "FROM course c "
                        . "WHERE csid NOT IN ( SELECT csid "
                        . "FROM department_course "
                        . "WHERE deptid = %s)",
                        GetSQLValueString($colname_courses, "int"),
                        GetSQLValueString($colname_courses, "int"));
$suggestion = mysql_query($query_suggestion, $tams) or die(mysql_error());
$row_suggestion = mysql_fetch_assoc($suggestion);
$totalRows_suggestion = mysql_num_rows($suggestion);

$initialSug = array();
for($idx = 0 ; $totalRows_suggestion > $idx; $idx++, $row_suggestion = mysql_fetch_assoc($suggestion)) {
    $row_suggestion['registered'] = false;
    $row_suggestion['selected'] = false;
    $row_registered['edit'] = false;    
    $row_registered['edited'] = false;
    $initialSug[] = $row_suggestion;
}

$query_registered = sprintf("SELECT d.csid, status, csname, unit "
                        . "FROM department_course d, course c "
                        . "WHERE d.csid = c.csid "
                        . "AND d.deptid = %s",
                        GetSQLValueString($colname_courses, "int"));
$registered = mysql_query($query_registered, $tams) or die(mysql_error());
$row_registered = mysql_fetch_assoc($registered);
$totalRows_registered = mysql_num_rows($registered);

$totalRegistered = 0;
$registeredCourses = array();
for($idx = 0 ;  $idx < $totalRows_registered; $idx++, $row_registered = mysql_fetch_assoc($registered)) {    
    $totalRegistered += $row_registered['unit'];
    $row_registered['registered'] = true;
    $row_registered['selected'] = true;
    $row_registered['edit'] = false; 
    $row_registered['edited'] = false;
    $registeredCourses[] = $row_registered;
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")) {
    doLogout( $site_root );  
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" ng-app="tams">
    <!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<?php require('../param/site.php'); ?>
<title><?php echo $university ?> </title>
<script type="text/javascript" src="../scripts/jquery.js"></script>
<script type="text/javascript" src="../scripts/tams.js"></script>
<script type="text/javascript" src="../scripts/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="../scripts/angular/angular.min.js"></script>
<script type="text/javascript" src="../scripts/angular/angular-typeahead.js"></script>
<script type="text/javascript" src="../scripts/handlebars.js"></script>
<!-- InstanceEndEditable -->
<link href="../css/template.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="../css/menulink.css" rel="stylesheet" type="text/css" />
<link href="../css/footer.css" rel="stylesheet" type="text/css" />
<link href="../css/sidemenu.css" rel="stylesheet" type="text/css" />
<link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
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
        <td><!-- InstanceBeginEditable name="pagetitle" -->Assign Courses to Department<!-- InstanceEndEditable --></td>
      </tr>
    </table>
  </div>
<div class="sidebar1">
   
    <?php include '../include/sidemenu.php'; ?>
  </div> 
  <div class="content"><!-- InstanceBeginEditable name="maincontent" -->
    <table width="690">
    	
      <tr>
        <td colspan="5">
          <table align="center">
          <?php if( getAccess() == 2 ){?>
          	<tr>
          	  <td nowrap="nowrap" align="right">Department</td>
          	  <td><label for="deptid"></label>
          	    <select name="deptid" id="deptid" onchange="deptfilt(this)">
          	      <?php
                        do {  
                      ?>
          	      <option value="<?php echo $row_dept['deptid']?>" 
                          <?php if (!(strcmp($row_dept['deptid'], $colname_courses))) {echo "selected=\"selected\"";}?>>
                                  <?php echo $row_dept['deptname']?>
                      </option>
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
              <?php }?>             
            </table>
          
        <p>&nbsp;</p></td>
      </tr>
        
    <tr>
        <td colspan="5">
            <form action="<?php echo $editFormAction; ?>" ng-submit="submitAction($event)" 
                  method="post" name="form" ng-controller="CourseController">
                <table width="644">
                  <tr>
                      <td colspan="3" valign="top">
                          <div>
                              Enter Number of Courses to assign: 
                              <input type="text" size="5" ng-model="data.fields"/> 
                              <button type="button" ng-click="addFields()">Add</button>
                          </div>
                      </td>
                  </tr>
                  <tr>
                      <td colspan="3" valign="top">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3" valign="top">
                        
                        <table id="courses" border="0"  class="table table-striped table-condensed" width="100%">
                            <tr ng-show="data.courses.length > 0">
                                <td colspan="4">Type the Course Code or Title in the Textfield(s) 
                                    below and select from the SUGGESTIONS. 
                                    Use the <span style="color: red; font-weight: bolder">X</span> button 
                                    to delete courses.
                                </td>
                            </tr>
                            <tr ng-show="data.courses.length > 0">
                                <td colspan="4">&nbsp;</td>
                            </tr>
                            <tr ng-repeat="course in data.courses track by $index">
                                <td valign="center" ng-bind="course.csid">
                                </td>
                                <td valign="center" >
                                    <span ng-bind="course.csname" ng-show="course.selected"></span>
                                    <div ng-if="!course.selected" style="margin-bottom: 0">
                                        <input class="typeahead" size="100" type="text" value="" sf-typeahead 
                                               options="coursesOptions" datasets="coursesDataset" 
                                               ng-focus="setIndex($index)"

                                               placeholder="Enter course code or name"/>
                                    </div>
                                    <div ng-show="course.edit">
                                        <select ng-model="course.status">
                                            <option value="Compulsory">Compulsory</option>
                                            <option value="Required">Required</option>
                                            <option value="Elective">Elective</option>
                                        </select>

                                        <select ng-model="course.unit">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                        </select>
                                        
                                        <button type="button" ng-click="editField($index, false)">Done</button>
                                    </div>
                                    <input type="hidden" value="{{course.csid}}" 
                                           ng-disabled="!course.edited" name="courses[]"/>
                                    <input type="hidden" value="{{course.status}}" 
                                           ng-disabled="!course.edited" name="status[]"/>
                                    <input type="hidden" value="{{course.unit}}" 
                                           ng-disabled="!course.edited" name="unit[]"/>
                                </td>
                                <td valign="center" 
                                    ng-bind-template="{{course.unit}}{{course.status | first}}">
                                </td>
                                <td valign="center">
                                    <a style="color: red; font-style: normal; font-weight: bolder" ng-hide="course.removed" 
                                       ng-click="editField($index, true)" href="">
                                        <i ng-show="course.selected" class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td valign="center">
                                    <a style="color: red; font-style: normal; font-weight: bolder" ng-hide="course.removed" 
                                       ng-click="removeField($index)" href="">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" align="center">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3" align="center">
                        <input type="submit" name="submit" value="Assign Courses" 
                               ng-click="data.submit = true"/>
                    </td>
                  </tr>
                </table>
                
          <input name="updated_entries[]" ng-repeat="u in data.updatedEntries track by $index" type="hidden" value="{{u}}" />
          <input name="removed_entries[]" ng-repeat="r in data.removedEntries track by $index" type="hidden" value="{{r}}" />
          <input type="hidden" name="MM_insert" value="form1" />
             </form>

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
    
<script>
    var module = angular.module('tams', ['siyfion.sfTypeahead']);
    
    module.filter('first', function() {
        return function(input) {
            // input will be the string we pass in
            if (input)
                return input.substr(0, 1);
        }
    });
    
    module.controller('CourseController', function($scope, $timeout, $compile) {
        $scope.$on('typeahead:selected', function(evt, elem, datum, dataset) {
            $scope.processSelection(elem, datum);
        });
        
        $scope.$on('typeahead:autocompleted', function(evt, elem, datum, dataset) {
            $scope.processSelection(elem, datum);
        });
         
        $scope.data = {
            "selectedIndex": null,
            "fields": 0,
            "disabled": true,
            "pending": 0,
            "updatedEntries": [],
            "removedEntries": [],
            "courses": <?php echo json_encode($registeredCourses);?>,
            "submit": false
        };
  
        // instantiate the bloodhound suggestion engine
        var courses = new Bloodhound({
            datumTokenizer: function(d) { 
                var keyName = Bloodhound.tokenizers.whitespace(d.csname);
                var keyCode = Bloodhound.tokenizers.whitespace(d.csid);
                return keyName.concat(keyCode); 
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: <?php echo json_encode($initialSug);?>,
            limit: 10
        });

        // initialize the bloodhound suggestion engine
        courses.initialize();

        $scope.coursesDataset = {
            displayKey: 'csname',
            source: courses.ttAdapter(),
            templates: {
                empty: [
                  'There is no Course that contains that Code or Title or you are not allowed to take that Course. \n\
                        Please try another Code or Title!',
                  '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<p class="selected-{{selected}}"><strong>{{csid}}</strong> – {{csname}}</p>')
            }
        };

        $scope.clearValue = function() {
            $scope.selectedNumber = null;
        };
        
        $scope.addValue = function(datum) {
            courses.add(datum);
        };

        // Typeahead options object
        $scope.coursesOptions = {
            highlight: true
        };
  
        $scope.processSelection = function(elem, datum) {
            datum.selected = true;
            datum.edit = true;
            datum.unit = 3;
            datum.status = "Compulsory";
            
            $scope.data.pending--;
            $scope.data.courses[$scope.data.selectedIndex] = datum;
            $timeout(function(){elem.remove();}, 10);
        };
        
        $scope.addFields = function() {
            
            if($scope.data.fields > 0) {
                $scope.data.pending = $scope.data.fields;
                if($scope.data.fields > 1) {
                    for( ; $scope.data.fields !== 0; $scope.data.fields--) {
                        var emptyObj = {
                            "csid": "", 
                            "csname": "", 
                            "unit": null, 
                            "status": "", 
                            "registered": false, 
                            "selected": false,
                            "edit": false,
                            "edited": false
                        };
                        $scope.data.courses.unshift(emptyObj);
                    }
                }else {
                    var emptyObj = {
                        "csid": "", 
                        "csname": "", 
                        "unit": null, 
                        "status": "",
                        "registered": false, 
                        "selected": false,
                        "edit": false,
                        "edited": false
                    };
                    $scope.data.courses.unshift(emptyObj);
                }
                
                $scope.data.fields = 0; 
            }
        };
        
        $scope.removeField = function(index) {
            // Ask for confirmation before entry is deleted.
            if(confirm("Are you sure you want to remove this course?")) {
                
                // Retrieve entry to be processed. 
                var removed = $scope.data.courses.splice(index, 1);
                var removedEntry = removed[0];
                
                // If course has been registered previously on the server,
                // add to removedEntries array to notify server of the removal.
                if(removedEntry.registered) {
                    $scope.data.removedEntries.push(removedEntry.csid);
                }
                
                if(removedEntry.registered) {
                    $scope.addValue(removedEntry);
                }
                
                if(!removedEntry.csid) {
                    $scope.data.pending--;
                }
                
                removedEntry.selected = false;
            }
        };
        
        // Enable editing of unit and status of an entry.
        $scope.editField = function(index, edit) {
            // Retrieve entry to be edited.
            var editCourse = $scope.data.courses[index];
            
            // Check whether to edit or to finish editing.
            if(edit) {
                // Enable editing for the entry.
                editCourse.edit = true;
            }else {
                // End editing of entry.
                editCourse.edit = false;
                
                // Mark entry as edited.
                editCourse.edited = true;
                
                // If course has been registered previously on the server,
                // add to updatedEntries array to notify server of the edit.
                if(editCourse.registered) {
                    $scope.data.updatedEntries.push(editCourse.csid);
                }
            }
        };
        
        $scope.setIndex = function(index) {
            $scope.data.selectedIndex = index;
        };
        
        $scope.submitAction = function(event) {
            if($scope.data.fields > 0){
                $scope.addFields();
                event.preventDefault();
            }
            
            if($scope.data.pending > 0) {
                if(!confirm('You have empty fields, do you want to submit your form?')) 
                    event.preventDefault();
            }
        };
    });

</script>
<!-- InstanceEnd -->
</html>
<?php
mysql_free_result($dept);
?>
