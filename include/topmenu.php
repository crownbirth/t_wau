<link href="../css/menulink.css" rel="stylesheet" type="text/css">
<?php 

$link = /*( getAccess() != 1 ) ?*/ "/".$site_root ;/*: "/".$site_root."admin";*/
?>

<table width="900" height="20" border="1" cellpadding="5" class="ttable">
  <tr class="menulink">
    <td width="100" align="center" bgcolor="#016233"><a href="http://thewauu.com" class="top">Home</a></td>
    <?php if(getLogin() == 'on') {?>
    <td width="100" align="center" bgcolor="#016233"><a href="<?php echo $link."/"?>complaint.php" class="top">Complaint</a></td>
    <?php }?>
    <td width="426" align="right" bgcolor="#016233">
	
		<?php  
		
			$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
			if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
				$logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
			}
			
			if( getLogin() == "off" )
				echo "<a href=\"".$link."/login.php\" class=\"top\">Login</a>";
			else
				echo "<a href=\"$logoutAction \" class=\"top\">Logout</a>";
				
		?>
    </td>
  </tr>
</table>
