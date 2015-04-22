<?php  
if (!isset($_SESSION)) {
  session_start();
}

require_once('../Connections/tams.php');
require_once('../param/param.php');
require_once('../functions/function.php');

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
	doLogout( $site_root.'/prospective' );   
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
        <td><!-- InstanceBeginEditable name="pagetitle" -->Account Creation Successful<!-- InstanceEndEditable --></td>
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
            <p>Congratulation....</p>
            <p>
                Your login Profile has been created successfully 
                and your login details have been sent to the E- mail 
                Address provided. Please note the following:
            </p>
            <ul>
                <li>Registration No. = <?php echo $_SESSION['newacct']['jambregid']?> </li>
                <li>Password = <?php echo $_SESSION['newacct']['lname']?></li>
                <li>Login As = Prospective Student</li>
            </ul>
            <p>
                Please, proceed to any branch of Zenith Bank Payment Code
            </p>
            <p>
                Account Name : The West African Union University <br/>
                Account No : XXXXXXXXXXX <br />
                Application Fees: N7,500.00 <br />
                Application Payment Code : <?php echo $_SESSION['newacct']['payment_code']?> 
            </p>
            <p>After making your payment at the Bank, return to the Portal to complete your Application.</p>
            <p>
                <a href="../login.php">Click Here</a> 
                to Login and proceed with your Application
            </p>
            
        </td>
        
        <?php 
            $fname=  $_SESSION['newacct']['fname'];
            $jambreg= $_SESSION['newacct']['jambregid'];
            $lname=  $_SESSION['newacct']['lname'];
            $mail_to = $_SESSION['newacct']['email'];
            $ses = $_SESSION['newacct']['session']; 
            $subject = "WAUU-TAMS: New Account Information";
            $sender="The West Afican Union University ";
            
            $message= "  Dear {$fname} {$lname},\n <p>Congratulations ...</p>
                        <p>
                            Your Account Profile  for {$ses} Application for Admission to {$sender} has been created successfully 
                            and your login details are provided below:
                        </p>
                        <ul>
                            <li>Username = {$jambreg}  </li>
                            <li>Password = {$lname}</li>
                            <li>Login As = Prospective Student</li>
                        </ul>
                        <p>
                            Please, proceed to any branch of Zenith Bank to make 
                            Payment for your Application fee quoting your Username as your Registration Number 
                        </p>
                        <p>
                            Account Name : The West African Union University <br/>
                            Account No : XXXXXXXXXXX <br />
                            Application Fees: N7,500.00 <br /> 
                            Application Payment Code: {$_SESSION['newacct']['payment_code']} 
                        </p>
                        <p>After making your payment at the Bank, return to the Portal to complete your Application.</p>
                        <p>
                            <a href='http://thewauu.com/tams/prospective/index.php'>Click Here</a> 
                            to Login and proceed with your Application
                        </p>";
             
           		
            $body = $message;
            @mail($mail_to, $subject,$message,$sender);
            unset($_SESSION['newacct']);
        ?>
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