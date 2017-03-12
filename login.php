<?php
session_start();
include('functions.php');

if(isset($_POST['username']) && isset($_POST['userpass'])){
	$uname = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$upass = filter_var($_POST['userpass'], FILTER_SANITIZE_STRING);
}

switch($_REQUEST['submit']){
	case 'Login':
		if(searchForUser($uname, $upass) == 1){
			$_SESSION["userN"] = $uname;
			$_SESSION["userP"] = $upass;
			setcookie("username", $_SESSION["userN"],time() + (86400));
			setcookie("password", $_SESSION["userP"],time() + (86400));
			
			header("Location: index.php");
			die();
		}else{
			header("Location: index.php?log=false");
			die();
		}
		break;
	case 'Register':
		if(searchForUserByUsername($uname) == 0){
			addUser($uname, $upass);
		}else{
			header("Location: index.php?reg=false");
		}
		break;
	
	case 'Logout':
		logout();
	
}



?>