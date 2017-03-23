<?php
if(isset($_REQUEST['submit'])){
	
	switch($_REQUEST['submit']){
		case 'newTopic':
			if(null != trim(filter_var($_POST['newTop'], FILTER_SANITIZE_STRING)) && null != trim(filter_var($_POST['newDesc'], FILTER_SANITIZE_STRING))){
				addContent($_POST['newTop'], $_POST['newDesc'], "t");
			}else{
				//TFF = TopicFieldsFilled
				header("Location: index.php?TFF=false");
			}
			break;
		case 'newComment':
			if(null != trim(filter_var($_POST['newComm'], FILTER_SANITIZE_STRING))){
				addContent($_POST['topicID'], $_POST['newComm'], "c");
			}else{
				//PFF = PostFieldsFilled
				header("Location: index.php?t=".$_POST['topicID']."&PFF=false");
			}
			break;
		case 'updComment':
			if(null != trim(filter_var($_POST['updComm'], FILTER_SANITIZE_STRING))){
				updContent($_POST['topicID'], $_POST['updComm'], $_POST['postID']);
			}else{
				//PFF = PostFieldsFilled
				header("Location: index.php?t=".$_POST['topicID']."&PFF=false");
			}
			break;
	}
}


function connectToDB(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db = "forum";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $db);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}

function printTopics(){
	$conn = connectToDB();
	
	$sql = "SELECT t_id, t_name, username FROM topic";
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		echo "<h1>Results</h1>";
		echo "<form action='functions.php' method='post'><table class='table'>";
		while($row = $result->fetch_assoc()) {
			echo "<tr><th><h3><a href='?t=".$row["t_id"]."'>".$row["t_name"] ."</a></h3> ".$row["username"]."</th></tr>";
		}
		if(isset($_SESSION['userN']) && isset($_SESSION['userP']) || isset($_COOKIE['username']) && isset($_COOKIE['password'])){
			echo "<tr><td><input type='text' name='newTop' placeholder='New topic' required></tr></td>";
			echo "<tr><td><textarea name='newDesc' placeholder='New topic' maxlength='40' required></textarea>
					<br><button type='submit' name='submit' value='newTopic'>Submit</button></td></tr>
					";
		}else{echo "<tr><td>Please login to create topics.</tr></td>";}
		echo "</table></form>";
	} else {
		echo "0 results";
	}
}

function printTopic($id){
	$conn = connectToDB();
	
	$sql = "SELECT t_name, username, t_desc FROM topic WHERE t_id ='".$id."'";
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		echo "<table class='table'>";
		while($row = $result->fetch_assoc()) {
			echo "<tr><th><h1>".$row["t_name"] ."</h1> ".$row["username"]."</th><th><h3>".$row["t_desc"]."</h3></th></tr>";
		}
		echo "</table>";
	} else {
		echo "0 results";
	}
	printPosts($id);
}

function printPosts($id){
	$conn = connectToDB();
	
	$sql = "SELECT p_id ,username, p_comment FROM post WHERE t_id = '".$id."'";
	
	$result = $conn->query($sql);
	$row_id = 0;
	
	if ($result->num_rows > 0) {
		// output data of each row
		echo "<form action='functions.php' method='post'><table class='table'>";
		while($row = $result->fetch_assoc()) {
			$row_id++;
			if($row["username"] == $_SESSION["userN"] || $row["username"] == $_COOKIE["username"]){
				echo "<tr><td><b>".$row["username"] ."</b><br><a><p id='edit".$row["p_id"]."' onClick='test(".$row["p_id"].",".$row_id.",".$_GET["t"].")'>Edit".$row["p_id"]."</p></a></td><td  id='row".$row_id."'>".$row["p_comment"]."</td></tr>";
			}else{
				echo "<tr><td><b>".$row["username"] ."</b></td><td>".$row["p_comment"]."</td></tr>";
			}
		}
		if(isset($_SESSION['userN']) && isset($_SESSION['userP']) || isset($_COOKIE['username']) && isset($_COOKIE['password'])){
			echo "<tr><td><textarea name='newComm' placeholder='New Comment'></textarea>
					<input type='hidden' name='topicID' value='".$_GET['t']."'>
					<br><button type='submit' name='submit' value='newComment'>Comment</button></td></tr>
					";
		}else{echo "<tr><td>Please login to comment.</tr></td>";}
		echo "</table></form>";
	} else {
		echo "<form action='functions.php' method='post'><table class='table'>";
		
		if(isset($_SESSION['userN']) && isset($_SESSION['userP']) || isset($_COOKIE['username']) && isset($_COOKIE['password'])){
			echo "<tr><td><textarea name='newComm' placeholder='New Comment'></textarea>
					<input type='hidden' name='topicID' value='".$_GET['t']."'>
					<br><button type='submit' name='submit' value='newComment'>Comment</button></td></tr>
					";
		}else{echo "<tr><td>Please login to comment.</tr></td>";}
		echo "</table></form>";
	}
}

function logout(){
	setcookie("username","", time() - 3600);
	setcookie("password","", time() - 3600);
	session_unset();
	session_destroy();
	echo "Session has been destroyed.";
	header("Location: index.php");
	die();
	break;
	
}

function searchForUser($n, $p){
	$conn = connectToDB();
	
	$sql = "SELECT * FROM user WHERE username = '".$n."' AND password = '".$p."'";
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		return 1;
	} else {
		return 0;
	}
	
}

function searchForUserByUsername($n){
	$conn = connectToDB();
	
	$sql = "SELECT * FROM user WHERE username = '".$n."'";
	
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		return 1;
	} else {
		return 0;
	}
	
}

function addUser($n, $p){
		$conn = connectToDB();

		trim($n);
		trim($p);

		$insert = "INSERT INTO user  (username, password) VALUES ("."'".$n."'".","."'".$p."'".")";
		if (mysqli_query($conn, $insert)) {
			$_SESSION["userN"] = $n;
			$_SESSION["userP"] = $p;
			header("Location: index.php");
		} else {
			echo "Error: " . $insert . "<br>" . mysqli_error($conn);
		}
}

function addContent($t, $d, $i){
	$conn = connectToDB();

	trim($t);
	trim($d);
	
	if($i == t){
		if(isset($_SESSION['userN'])){
			$insert = "INSERT INTO topic  (t_name, username, t_desc) VALUES ("."'".$t."'".","."'".$_SESSION['userN']."'".","."'".$d."'".")";
		}else{$insert = "INSERT INTO topic  (t_name, username, t_desc) VALUES ("."'".$t."'".","."'".$_COOKIE['username']."'".","."'".$d."'".")";}
		
		if (mysqli_query($conn, $insert)) {
			header("Location: index.php");
		} else {
			echo "Error: " . $insert . "<br>" . mysqli_error($conn);
		}
	}else{
		if(isset($_SESSION['userN'])){
			$insert = "INSERT INTO post  (t_id, username, p_comment) VALUES ("."'".$t."'".","."'".$_SESSION['userN']."'".","."'".$d."'".")";
		}else{$insert = "INSERT INTO post  (t_id, username, p_comment) VALUES ("."'".$t."'".","."'".$_COOKIE['username']."'".","."'".$d."'".")";}
		
		if (mysqli_query($conn, $insert)) {
			header("Location: index.php?t=".$t);
		} else {
			echo "Error: " . $insert . "<br>" . mysqli_error($conn);
		}
	}
}

function updContent($tID ,$c, $pID){
	$conn = connectToDB();

	trim($c);

	$update = "UPDATE post SET p_comment = '".$c."' WHERE p_id = '".$pID."'";
	if (mysqli_query($conn, $update)) {
		header("Location: index.php?t=".$tID);
	} else {
		echo "Error: " . $update . "<br>" . mysqli_error($conn);
	}
}


?>