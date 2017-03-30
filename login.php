<?php
session_start();
include('functions.php');

if(isset($_POST['username']) && isset($_POST['userpass'])){
	$uname = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$upass = password_hash(filter_var($_POST['userpass'], FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
}

switch($_REQUEST['submit']){
	case 'Login':
		if(searchForUser($uname, $_POST['userpass']) == 1){
			$_SESSION["userN"] = $uname;
			$_SESSION["userP"] = $upass;
			setcookie("username", $_SESSION["userN"],time() + (86400));
			setcookie("password", $_SESSION["userP"],time() + (86400));
			
			header("Location: index.php");
			die();
		}else{
			//The line below can be used to find out the hashed version of a password. Used for turning old unhashed passwords into hashed ones.
			//echo "This is uname: ".$uname." and this is upass: ".$upass;
			header("Location: index.php?log=false");
			die();
		}
		break;
	case 'Register':
		if(searchForUserByUsername($uname) == 0){
			if($uname == "" || $uname == null || $upass == "" || $upass == null){
				header("Location: index.php?RFF=false");
			}else{addUser($uname, $upass);}
			
		}else{
			header("Location: index.php?reg=false");
		}
		break;
	
	case 'Logout':
		logout();
	
}



?>