<?php
//This file contains functions related to the user-functionality of the site.

require_once 'connection.php';
require_once 'editDB.php';
//This functions hashes the password given by the user and forwards it to the next function.
//Received values are username, password and an integer which is used to tell which function to call next.
function loginRegisterSwitch($uname, $p, $f, $tID){
	//The password is hashed using the password_hash()-function.
	$upass = password_hash(filter_var($p, FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
	
	//Checking if the username or password contains any special characters.
	if(!ctype_alnum($uname) ||!ctype_alnum($p)){
		//If special characters are detected the user is redirected back to the previous page and is given an errormessage.
		if($tID == -1){
			header("Location: index.php?ICD");
			die();
		}else{
			header("Location: index.php?ICD&t=".$tID);
			die();
		}
		
	}
	
	//Here we make sure that the given username isn't too long for the database.
	if(mb_strlen($uname) > 30 || mb_strlen($p) > 30){
		//If the username is too long we redirect the user back to the previous page with an error message.
		//ITL = InputTooLong
		if($tID == -1){
			header("Location: index.php?ITL");
			die();
		}else{
			header("Location: index.php?ITL&t=".$tID);
			die();
		}
	//If the inputs pass the if-statement the script calls a specific function depending on the value of '$f'
	}else{
		if($f == 1){
			login($uname, $upass, $tID);
		}else{
			register($uname, $upass, $tID);
		}
	}
}
//This function is used to pass the users login credentials to the session as well as the cookies.
function login($uname, $upass, $tID){
	//If the searchForUser function returns 1, new session and a cookie are created.
	//Instead of giving the searchForUser()-function the hashed password
	//	we give it the raw password so that it can be used for the password_verify()-function.
	if(searchForUser($uname, $_POST['userpass']) == 1){
		
		//If the function return '1' we add the login credentials to session and the cookie.
		$_SESSION["userN"] = $uname;
		$_SESSION["userP"] = $upass;
		
		setcookie("username", $uname,time() + (86400), '/');
		setcookie("password", $upass,time() + (86400), '/');
		
		//After setting the values we return to the previous page.
		if($tID == -1){
			header("Location: index.php");
			die();
		}else{
			header("Location: index.php?t=".$tID);
			die();
		}
	}else{
		//If the function returns 0 the user is redirected back to the frontpage with an errormessage.
		//UNF = UserNotFound
		if($tID == -1){
			header("Location: index.php?UNF");
			die();
		}else{
			header("Location: index.php?UNF&t=".$tID);
			die();
		}
	}
}

//This function call function searchForUserByUsername() to check if the username is available and then either calls the addUser()-function
// or return the user to the frontpage with an errormessage.
function register($uname, $upass, $tID){
	//If the function return 0 the username is available and addUser() can be called.
	if(searchForUserByUsername($uname) == 0){

		//The function to add a new user to the database is called
		addUser($uname, $upass, $tID);
	
	}else{
		//UAT = UsernameAlreadyTaken
		if($tID == -1){
			header("Location: index.php?UAT");
			die();
		}else{
			header("Location: index.php?UAT&t=".$tID);
			die();
		}
		
	}
}

//This function simply deleted cookies and destroys sessions. The user is also redirected to the frontpage.
function logout($tID){
	setcookie("username","", time() - 3600, '/');
	setcookie("password","", time() - 3600, '/');
	
	session_unset();
	session_destroy();
	
	if($tID == -1){
		header("Location: index.php");
		die();
	}else{
		header("Location: index.php?t=".$tID);
		die();
	}
}


//This function is used for login functionality.
function searchForUser($n, $p){
	//Here we call the function connectToDB to make the necessary connection to the database.
	$conn = connectToDB();

	//Here is the prepared statement for fetching a password from table 'user' by specific username.
	$sql = $conn->prepare("SELECT password FROM user WHERE username = ?");
	$sql->bind_param("s", $n);

	//The query is executed.
	$sql->execute();
	$result = $sql->get_result();
	$row = $result->fetch_assoc();

	//If the password given by the user matches the one found in the database, the function returns 1.
	if ($result->num_rows > 0) {
		if(password_verify($p, $row["password"])){
			return 1;
		}
	} else {
		return 0;
	}
}
//This function is used to check if an username is already taken.
function searchForUserByUsername($n){
	//Here we call the function connectToDB to make the necessary connection to the database.
	$conn = connectToDB();
	
	//Here is the prepared statement which checks if table 'user' contains a specified username.
	$sql = $conn->prepare("SELECT * FROM user WHERE username = ?");
	$sql->bind_param("s", $n);
	
	$sql->execute();
	$result = $sql->get_result();
	//$result = $conn->query($sql);
	
	//If there are no results the function returns 1 otherwise it returns 0
	if ($result->num_rows > 0) {
		return 1;
	} else {
		return 0;
	}
}
?>