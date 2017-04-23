<?php
//This file contains functions that fetch data from the database and display it.

require_once 'connection.php';

//Function for printing all topics found in the database.
function printTopics(){
	//Opening connection to the database using the function connectToDB().
	$conn = connectToDB();
	//Creating the SQL-statement for fetching data from the database.
	$sql = "SELECT t_id, t_name, username FROM topic";
	$result = $conn->query($sql);

	//Here we echo some html content to be displayed on the webpage.
	echo "<h1 class='text-center'>Results</h1>";
	echo "<table class='table'>";
	
	//Checking if the query returned any results.
	if ($result->num_rows > 0) {
		
		//We go through the results of the sql with a while loop and echo the contents to a html table.
		while($row = $result->fetch_assoc()) {
			//Here we check if the user is logged in
			if(isset($_SESSION["userN"])){
				
				echoTopics($row, $_SESSION["userN"]);
				
			}else if(isset($_COOKIE["username"])){
				
				echoTopics($row, $_COOKIE["username"]);
				
			}else{
				echo "<tr>
						<td class='text-center'>
							<h3>
								<a href='?t=".$row["t_id"]."'>".$row["t_name"] ."</a>
							</h3>
							<p>By ".$row["username"]."</p>
						</td>
					</tr>";
			}
				
				
		}
		//If the user has logged in we create a form for creating new topics. It the user hasn't logged in we kindly ask them to do so.
		echo "<form action='functions.php' method='post'>";

		if(isset($_SESSION['userN']) && isset($_SESSION['userP'])){
			
			echoTopicsForm();
			
		}else if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
			
			echoTopicsForm();
			
		}else{
			echo "<tr>
					<td class='text-center'>
						<p>Please login to create topics.</p>
					</td>
				</tr>";
		}
		
	//If no results are found in the database the else-statement is executed.
	} else {
		echo "<tr>
					<td class='text-center'>
						<h3>
							No topics found
						</h3>
					</td>
			</tr>";
		echo "<form action='functions.php' method='post'>";
		
		if(isset($_SESSION['userN']) && isset($_SESSION['userP'])){
			
			echoTopicsForm();
			
		}else if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
			
			echoTopicsForm();
			
		}else{
			echo "<tr>
					<td class='text-center'>
						<p>Please login to create topics.</p>
					</td>
				</tr>";
		}
	}
	echo "</table>
			</form>";
}

//This function prints a spesific topic including its name, description and creator.
function printTopic($id){
	//Opening connection to the database using the function connectToDB().
	$conn = connectToDB();
	//Creating the SQL-statement for fetching data from the database.
	$sql = $conn->prepare("SELECT t_name, username, t_desc FROM topic WHERE t_id = ?");
	$sql->bind_param("i", $id);
	$row_id = 0;

	//Executing the query
	$sql->execute();
	$result = $sql->get_result();

	if ($result->num_rows > 0) {
		// output data of each row
		echo "<table class='table'>";
		while($row = $result->fetch_assoc()) {
			//Checking if the user is logged in, if so we add elements that allow them to edit the description of their topic.
			if(isset($_SESSION["userN"])){
				
				echoTopic($row, $_SESSION["userN"], $_GET["t"], $row_id);
				
			}else if(isset($_COOKIE["username"])){
				
				echoTopic($row, $_COOKIE["username"], $_GET["t"], $row_id);
				
			}else{
				echo "<tr>
						<td class='vertical-align text-right'>
							<b>".$row["username"]."</b>
						</td>
						<td class='text-center'>
							<h1>".$row["t_name"] ."</h1><br>
							<h3 class='textLines'>".$row["t_desc"]."</h3>
						</td>
					</tr>";
			}	
		}
		echo "</table>";
	} else {
		echo "0 results";
	}
	//After echoing the topic, we query for the comments made to the topic.
	printPosts($id);
}

//This function prints all comments with a spesific topicID
function printPosts($id){
	//Opening connection to the database using the function connectToDB().
	$conn = connectToDB();
	//Creating the SQL-statement for fetching data from the database.
	$sql = $conn->prepare("SELECT p_id ,username, p_comment FROM post WHERE t_id = ?");
	$sql->bind_param("i", $id);

	//Executing the SQL-query
	$sql->execute();
	$result = $sql->get_result();
	$row_id = 1;
	if ($result->num_rows > 0) {
		// output data of each row
		echo "<form action='functions.php' method='post'>
				<table class='table'>";
		while($row = $result->fetch_assoc()) {
			//We use the $row_id for creating unique ids for all rows we create.
			$row_id++;
			if(isset($_SESSION["userN"])){
				
				//If the user has logged in we add an edit option for comments made by them.
				echoPosts($row, $_SESSION["userN"], $_GET["t"], $row_id);
				
			}else if(isset($_COOKIE["username"])){
				
				//If the user has logged in we add an edit option for comments made by them.
				echoPosts($row, $_COOKIE["username"], $_GET["t"], $row_id);
				
			}else{
				echo "<tr>
						<td class='text-right'>
							<b>".$row["username"] ."</b>
						</td>
						<td class='text-center textLines'>
							<p>".$row["p_comment"]."</p>
						</td>
					</tr>";
			}
		}
	}
	//After displaying all comments we add an form that allows logged in users to add new comments to the topics.
	echo "<form action='functions.php' method='post'>
			<table class='table'>";
	if(isset($_SESSION['userN']) && isset($_SESSION['userP'])){
		
		echoPostsForm($_GET['t']);
		
	}else if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
		
		echoPostsForm($_GET['t']);
		
	}else{
		echo "<tr>
				<td class='text-center'>
					<p>Please login to comment.</p>
				</td>
			</tr>";
	}
	echo "	</table>
		</form>";
}

//This function echoes all topics to the webpage.
function echoTopics($row, $name){
	if($row["username"] == $name){
		echo "<tr>
							<td class='text-center'>
								<h3>
									<a href='?t=".$row["t_id"]."'>".$row["t_name"] ."</a>
								</h3>
								<p>By  ".$row["username"]."</p>

								<form action='functions.php' method='post'>
									<input name='postID' type='hidden' value=-1>
									<input name='topicID' type='hidden' value=".$row["t_id"].">
									<button type='submit' class='rButton' name='submit' value='delContent'
										onClick=\"return confirm('Are you sure?')\">Remove
									</button>
								</form>
							</td>
						</tr>";
	}else{
		//We make the topics clickable and add the topicID to the url.
		echo "<tr>
							<td class='text-center'>
								<h3>
									<a href='?t=".$row["t_id"]."'>".$row["t_name"] ."</a>
								</h3>
								<p>By ".$row["username"]."</p>
							</td>
						</tr>";
	}
}

//This function adds a form used to create new topics.
function echoTopicsForm(){
	echo "<tr>
				<td>
					<p class='text-center'>Create new topic</p>
					<input class='titleField' type='text' name='newTop' placeholder='Topic title' required><br><br>
					<textarea name='newDesc' placeholder='Topic description' required></textarea><br>
					<button type='submit' name='submit' value='newTopic'>
						Submit
					</button>
				</td>
		</tr>";
}

//Function that is used to print a single topic.
function echoTopic($row, $name, $tID, $row_id){
	if($row["username"] == $name){
		echo "<tr>
							<td class='vertical-align text-right'>
								<b>".$row["username"]."</b>
								<a>
									<p id='edit-1' onClick='showEdit(-1,".$row_id.",".$tID.")'>Edit</p>
								</a>
							</td>
							<td id='row".$row_id."' class='text-center'>
								<h1>".$row["t_name"] ."</h1><br>
								<h3 class='textLines'>".$row["t_desc"]."</h3>
							</td>
						</tr>";
	}else{
		echo "<tr>
							<td class='vertical-align text-right'>
								<b>".$row["username"]."</b>
							</td>
							<td class='text-center'>
								<h1>".$row["t_name"] ."</h1><br>
								<h3 class='textLines'>".$row["t_desc"]."</h3>
							</td>
						</tr>";
	}
}
//Function that echoes all comments for a topic.
function echoPosts($row, $name, $tID, $row_id){
	if($row["username"] == $name){
		echo "<tr>
							<td class='text-right'>
								<b>".$row["username"] ."</b><br>
								<a>
									<p id='edit".$row["p_id"]."' onClick='showEdit(".$row["p_id"].",".$row_id.",".$tID.")'>Edit</p>
								</a>
							</td>
							<td id='row".$row_id."' class='text-center textLines'>
									<p>".$row["p_comment"]."</p>
							</td>
						</tr>";
	}else{
		echo "<tr>
							<td class='text-right'>
								<b>".$row["username"] ."</b>
							</td>
							<td class='text-center textLines'>
								<p>".$row["p_comment"]."</p>
							</td>
						</tr>";
	}
}

//This function adds a form that allows user to add comments to a topic.
function echoPostsForm($tID){
	echo "<tr>
				<td>
					<textarea name='newComm' placeholder='New Comment'></textarea>
					<input type='hidden' name='topicID' value='".$tID."'><br>
					<button type='submit' name='submit' value='newComment'>
						Comment
					</button>
				</td>
			</tr>";
}

?>