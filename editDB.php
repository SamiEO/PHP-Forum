<?php
//This file contains functions that either add data, remove data or edit data found in the database.


//This function is used for adding new users to the database.
function addUser($n, $p, $tID){
	//Here we call the function connectToDB to make the necessary connection to the database.
	$conn = connectToDB();

	//Here is the prepared statement for adding a new user to the database.
	$sql = $conn->prepare("INSERT INTO user  (username, password) VALUES ( ? , ? )");
	$sql->bind_param("ss", $n, $p);

	//This is where the query is executed. If an error occurs it is echoed to the page. This is expected to never happen.
	if ($sql->execute()) {

		$_SESSION["userN"] = $n;
		$_SESSION["userP"] = $p;
		
		setcookie("username", $n,time() + (86400), '/');
		setcookie("password", $p,time() + (86400), '/');
		
		if($tID == -1){
			header("Location: index.php");
		}else{
			header("Location: index.php?t=".$tID);
		}
		
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
}

//This function is used to add new comments and topics to the database.
//$t = topics name or ID, $d = topics description or a comment, $i = the identifier for what type of content we want to create.
function addContent($t, $d, $i){
	//Opening connection to the database using the function connectToDB().
	$conn = connectToDB();

	//If the identifier is 't' we are adding a new topic to the database.
	if($i == t){
		if(isset($_SESSION['userN'])){
			//Creating the prepared statements.
			$sql = $conn->prepare("INSERT INTO topic  (t_name, username, t_desc) VALUES ( ? , ? , ? )");
			$sql->bind_param("sss", $t, $_SESSION['userN'], $d);
		}else{
			//Creating the prepared statements.
			$sql = $conn->prepare("INSERT INTO topic  (t_name, username, t_desc) VALUES ( ? , ? , ? )");
			$sql->bind_param("sss", $t, $_COOKIE['username'], $d);
		}
		//Executing the query
		if ($sql->execute()) {
			header("Location: index.php");
		} else {
			echo "Error: " . $insert . "<br>" . mysqli_error($conn);
		}
	}else{
		//If the identifier is something other than 't' we are adding a comment to the database.
		if(isset($_SESSION['userN'])){
			//Creating the prepared statements.
			$sql = $conn->prepare("INSERT INTO post  (t_id, username, p_comment) VALUES ( ? , ? , ? )");
			$sql->bind_param("iss", $t, $_SESSION['userN'], $d);
		}else{
			//Creating the prepared statements.
			$sql = $conn->prepare("INSERT INTO post  (t_id, username, p_comment) VALUES ( ? , ? , ? )");
			$sql->bind_param("iss", $t, $_COOKIE['username'], $d);
		}
		//Executing the query
		if ($sql->execute()) {
			header("Location: index.php?t=".$t);
		} else {
			echo "Error: " . $insert . "<br>" . mysqli_error($conn);
		}
	}
}
//This function is used to update an already existing comment.
function updContent($tID ,$c, $pID){
	//The function uses the $tID to redirect the user back to the correct page. $c is the comment and $pID tells us which comment to update.
	//Opening connection to the database using the function connectToDB().
	$conn = connectToDB();

	if($pID != -1){
		//Creating the prepared statements.
		$sql = $conn->prepare("UPDATE post SET p_comment = ? WHERE p_id = ? ");
		$sql->bind_param("si", $c, $pID);
	}else{
		//Creating the prepared statements.
		$sql = $conn->prepare("UPDATE topic SET t_desc = ? WHERE t_id = ? ");
		$sql->bind_param("si", $c, $tID);
	}

	if ($sql->execute()) {
		header("Location: index.php?t=".$tID);
	} else {
		echo "Error: " . $update . "<br>" . mysqli_error($conn);
	}
}
//This function is used to delete content from the database.
function delContent($pID, $tID){
	//Opening connection to the database using the function connectToDB().
	$conn = connectToDB();
	//If the pID has a vlue of -1 we are removing a topic instead of a post.
	if($pID != -1){
		$sql = $conn->prepare("DELETE FROM post WHERE p_id = ?");
		$sql->bind_param("i",$pID);

		if ($sql->execute()) {
			header("Location: index.php?t=".$tID);
		} else {
			echo "Error: " . $update . "<br>" . mysqli_error($conn);
		}
	}else{
		$sql = $conn->prepare("DELETE FROM post WHERE t_id = ?");
		$sql->bind_param("i",$tID);

		if ($sql->execute()) {
			$sql = $conn->prepare("DELETE FROM topic WHERE t_id = ?");
			$sql->bind_param("i",$tID);
				
			if ($sql->execute()) {
				header("Location: index.php");
			} else {
				echo "Error: " . $update . "<br>" . mysqli_error($conn);
			}
		} else {
			echo "Error: " . $update . "<br>" . mysqli_error($conn);
		}
	}
}

?>