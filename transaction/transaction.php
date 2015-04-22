<?php require_once('../Connections/tams.php'); ?>
<?php require_once('lib/nusoap.php'); ?>
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

mysql_select_db($database_tams, $tams);

$server = new nusoap_server();

// Define namespace
$namespace = 'urn:transaction'; 

// Initiate WSDL configuration
$server->configureWSDL('transaction');

// Designate the WSDL namespace
$server->wsdl->schemaTargetNamespace = $namespace;

// Add complex type to handle transaction details
$server->wsdl->addComplexType(
            'transParam',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'transId' => array('name' => 'transId', 'type' => 'xsd:string'),
                'bankRef' => array('name' => 'bankRef', 'type' => 'xsd:string'),
                'payDate' => array('name' => 'payDate', 'type' => 'xsd:string')
            )
        );

// Add complex type to handle transaction information
$server->wsdl->addComplexType(
            'transInfo',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'studNum' => array('name' => 'studNum', 'type' => 'xsd:string'),
                'studName' => array('name' => 'studName', 'type' => 'xsd:string'),
                'session' => array('name' => 'session', 'type' => 'xsd:string'),
                'amount' => array('name' => 'amount', 'type' => 'xsd:int'),
                'percent' => array('name' => 'percent', 'type' => 'xsd:string'),
                'payType' => array('name' => 'payType', 'type' => 'xsd:string'),
                'error' => array('name' => 'error', 'type' => 'xsd:string'),
                'msg' => array('name' => 'msg', 'type' => 'xsd:string')
            )
        );

// Add complex type to handle transaction information
$server->wsdl->addComplexType(
            'transRet',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'error' => array('name' => 'error', 'type' => 'xsd:string'),
                'msg' => array('name' => 'msg', 'type' => 'xsd:string')
            )
        );

// Register the approveTransaction function.
$server->register("approveTransaction", 
            array('transParam' => 'tns:transParam'), 
            array('return' => 'tns:transRet'), 
            //array('return' => 'xsd:string'), 
            $namespace, 
            $namespace.'#approveTransaction', 
            'rpc', 
            'encoded'
        );

// Register the checkTransaction function.
$server->register("checkTransaction", 
            array('transId' => 'xsd:string'), 
            array('return' => 'tns:transInfo'), 
            $namespace, 
            $namespace.'#checkTransaction', 
            'rpc', 
            'encoded'
        );

/**
 * 
 * @global resource $tams
 * @param array $params
 * @return boolean
 */

function validate(&$params) {
    global $tams;
    
    // Build array containing expected parameters.
    $expected = array('transId', 'bankRef', 'payDate');
    $received = array_keys($params);
    $intersect = array_intersect($expected, $received);
    
    // Initialise return value to true. Would be false if any check fails.
    $valid['status'] = true;
    $valid['msg'] = 'The transaction does not exist on the server!';
    
    // Check if any of the values are empty.
    $emp = array_search("", array_values($params));
    
    // Compare count value to know if expected parameters are present.
    if(count($expected) !== count($intersect)) {
        $valid['status'] = false;
        $valid['msg'] = "The following parameter(s) are missing: ".implode(', ', array_diff($expected, $intersect));
        
    }elseif($emp != false) {
        // Check if any of the values are empty.
        $valid['status'] = false;
        $valid['msg'] = implode('/', $params);//"The '{$received[$emp]}' parameter has an empty value!";
        
    }else {
        // Define year and status parameter.
        $params['year'] = date('Y');
        $params['status'] = 'APPROVED';
        
        // Validate each parameter.
        foreach($expected as $e) {
            // Remove extra spaces
            $value = trim($params[$e]);
            
            switch($e) {
                case 'transId':
                    // Define valid values.
                    $validValues = array('AP', 'SC');
                    
                    $value = strtoupper($value);
                    $suffix = substr($value, -2);
                    
                    // Check if value is a valid type. Set return to false if it isn't.
                    if(!in_array($suffix, $validValues)) {
                        $valid['status'] = false;
                        $valid['msg'] = "The '{$e}' value specified is not valid!";
                    }
                    
                    $query_exist = sprintf("SELECT st.matric_no as stdid, st.status "
                                    . "FROM student s, schfee_transactions st "
                                    . "WHERE s.stdid = st.matric_no "
                                    . "AND st.reference = %s",  
                                    GetSQLValueString($params['transId'], "text"));

                    $params['type'] = $validValues[1];
                        
                    if($suffix == $validValues[0]) {
                        $query_exist = sprintf("SELECT at.can_no as stdid, at.status "
                                        . "FROM prospective p, appfee_transactions at "
                                        . "WHERE p.jambregid = at.can_no "
                                        . "AND at.reference = %s",  
                                        GetSQLValueString($params['transId'], "text"));
                                                
                        $params['type'] = $validValues[0];
                    }
                                       
                    $exist = mysql_query($query_exist, $tams) or die(mysql_error());
                    $row_exist = mysql_fetch_assoc($exist);
                    $totalRows_exist = mysql_num_rows($exist);
                    
                    if($totalRows_exist > 0) {
                        $params['stdid'] = $row_exist['stdid'];
                        $params['status'] = $row_exist['status'];
                    }else {
                        $valid['status'] = "The transaction with the ID '{$e}' value specified does not exist!";
                    }
                    
                    break;
                               
                case 'payDate':
                    break;
            }
           
            if (!$valid['status']) {
                break;
            }
        }
        
    }
    
    return $valid;
}

/**
 * Approves a transaction.
 * 
 * @param array $transParam
 * @return string status of transaction
 * 
 */

function approveTransaction($transParam) {
    
    global $tams;
    
    // Initiate return parameter with default values.
    $details = array();
    
    $details['error'] = 'true';
    
    $valid = validate($transParam);

    if($valid['status']) {
        
        if($transParam['status'] == 'PENDING') {
            
            mysql_query('START TRANSACTION', $tams);
            
            $table = $transParam['type'] == 'SC'? 'schfee_transactions': 'appfee_transactions';
            
            $query_approve = sprintf("UPDATE %s "
                                    . "SET status = %s, date_time = %s, ordid = %s "
                                    . "WHERE reference = %s ",  
                                    GetSQLValueString($table, "defined", $table),  
                                    GetSQLValueString('APPROVED', "text"),  
                                    GetSQLValueString($transParam['payDate'], "text"),  
                                    GetSQLValueString($transParam['bankRef'], "text"),  
                                    GetSQLValueString($transParam['transId'], "text"));
    
            $approve = mysql_query($query_approve, $tams) or die(mysql_error());            
            
            $updatePros = true;
            
            if($transParam['type'] == 'AP') {
                $query_updatePros = sprintf("UPDATE prospective "
                                        . "SET formpayment = %s "
                                        . "WHERE jambregid = %s",  
                                        GetSQLValueString('Yes', "text"),  
                                        GetSQLValueString($transParam['stdid'], "text"));

                $updatePros = mysql_query($query_updatePros, $tams) or die(mysql_error());            
            }
            
            if($approve && $updatePros) {
                mysql_query('COMMIT;', $tams);
                $details['error'] = 'false';
                $details['msg'] = 'Transaction updated successfully!';
            }else {
                mysql_query('ROLLBACK;', $tams);
                $details['msg'] = 'There was a problem updating the transaction on the server!';
            }
            
        }else {
            $details['msg'] = 'The transaction has been previously updated!';
        }
        
    }
    
    return $details;
}

/**
 * Check if a transaction exists.
 * 
 * @param string $transId
 * @return array transaction information
 * 
 */

function checkTransaction($transId) {
    
    global $tams;
    
    // Initiate return parameter with default values.
    $details = array();
    
    $details['studNum'] = NULL;    
    $details['studName'] = NULL;
    $details['session'] = NULL;
    $details['amount'] = NULL;
    $details['percent'] = NULL;
    $details['payType'] = NULL;
    $details['error'] = 'true';
    $details['msg'] = 'Transaction was not found!';
    
    if(!isset($transId) || $transId == '') {
        $details['msg'] = 'No Transaction ID supplied!';
        return $details;
    }    
       
    $transId = strtoupper($transId); 
    $suffix = substr($transId, -2);
    if(!in_array($suffix, array('AP', 'SC'))) {
        $details['msg'] = 'The Transaction ID is not valid!';
        return $details;
    }
    
    $query_exist = '';
       
    if($suffix == 'AP') {
        $query_exist = sprintf("SELECT p.jambregid as stdid, p.fname, p.lname, p.mname, at.amt, at.year, at.status, "
                            . "at.percentPaid "
                            . "FROM prospective p, appfee_transactions at "
                            . "WHERE p.jambregid = at.can_no "
                            . "AND at.reference = %s ",   
                            GetSQLValueString($transId, "text"));
        $type = 'Application Fees';
    }else {
        $query_exist = sprintf("SELECT s.stdid, s.fname, s.lname, s.mname, st.amt, st.year, st.status, st.percentPaid "
                                . "FROM student s, schfee_transactions st "
                                . "WHERE s.stdid = st.matric_no "
                                . "AND st.reference = %s ",  
                                GetSQLValueString($transId, "text"));
        $type = 'School Fees';
    }
    
    // Check if this transaction exists in school fees table.
    $exist = mysql_query($query_exist, $tams) or die(mysql_error());
    $row_exist = mysql_fetch_assoc($exist);
    $totalRows_exist = mysql_num_rows($exist);
    
    if($totalRows_exist > 0) {
        
        $details['studNum'] = $row_exist['stdid'];
        $details['studName'] = $row_exist['lname'].' '.$row_exist['fname'].' '.$row_exist['mname'];
        $details['session'] = $row_exist['year'].'/'.($row_exist['year']+1);
        $details['amount'] = intval(str_replace(',', '', substr($row_exist['amt'], 3)));
        $details['percent'] = $row_exist['percentPaid'];
        $details['payType'] = $type;
        $details['error'] = 'false';
        $details['msg'] = $row_exist['status'] == 'PENDING'? 
                                    'Transaction information was found!': 'Transaction seems to be completed!';
        
//        if($row_exist['type'] == 'Application Fees') {
//            $ses_parts = explode('/', $details['session']);
//            $ses_parts[0] = intval($ses_parts[0]) + 1;
//            $ses_parts[1] = intval($ses_parts[1]) + 1;
//            $details['session'] = "{$ses_parts[0]}/{$ses_parts[1]}";
//        }
    }  
    
    return $details;
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);