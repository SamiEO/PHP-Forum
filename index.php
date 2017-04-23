<!DOCTYPE html>
<html lang="en">
<?php
//Including the functions.php-file so that the page gets access to the functions it needs.
include_once('functions.php');
?>
<head>
<title>
	Forum
</title>
<meta charset="utf-8">
<meta name="viewport"content="width=device-width, initial-scale=1">
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<style>
body{background-color: #FFF0A5;}
#content{margin-left:20%;margin-right:20%;background-color: #FFB03B;border-radius:20px;}
.header{background-color: #468966;height:50px;}
a{color: #B64926;}
.bold:hover, a:hover{text-decoration:none;color:#8E2800;font-weight:bold;}
#login{background-color: #468966;padding:5px;display:none;margin-top:20px;border-radius:2px;}
textarea{width:100%;height:100px;}
button{margin-top:5px;}
.rButton{float:right;}
.bold{color:#ffffff;}
.forms{display:none;}
table > tbody > tr > td.vertical-align{vertical-align:bottom;}
table > tbody > tr > td.text-right{width:30%;}
.titleField{width:100%;}
.textLines{white-space: pre-line;}
.msg{color:white;}

<!-- CSS to change the inputfields text color -->
::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color:    lightgrey;
}
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color:    lightgrey;
}
::-moz-placeholder { /* Mozilla Firefox 19+ */
   color:    lightgrey;
}
:-ms-input-placeholder { /* Internet Explorer 10-11 */
   color:    lightgrey;
}
</style>
<script>
//JS function for displaying and hiding the login form
	function showLogin(){
		if($('#login').is(":visible")){
			$('#login').slideUp();
		}else{$('#login').slideDown();}
		
	}
//The url is checked for specific data. If spesific values are found the user is given an error message via an alert.
	$(document).ready(function(){
		if(window.location.href.indexOf("UNF") > -1){
			alert("Login failed: User not found");
		}else if(window.location.href.indexOf("UAT") > -1){
			alert("Username is already taken");
		}else if(window.location.href.indexOf("TFF=false") > -1){
			alert("Please fill all text boxes when creating a new topic");
		}else if(window.location.href.indexOf("PFF") > -1){
			alert("Please fill all inputfields.");
		}else if(window.location.href.indexOf("RFF=false") > -1){
			alert("All registration fields have to be filled");
		}else if(window.location.href.indexOf("ITL") > -1){
			alert("Input exceeds maximum length.");
		}else if(window.location.href.indexOf("ICD") > -1){
			alert("Username and password can't contain special characters..");
		}
	});
//JS function for creating or displaying the form used to update comments.
	function showEdit(pID, rID, tID){
		if($("#form"+rID).length){
			if($("#form"+rID).is(":visible")){
				$("#form"+rID).slideUp();
			}else{$("#form"+rID).slideDown();}
			
		}else{
			if(pID != -1){
				$("#row"+rID).append("<br><form id='form"+rID+"' class='forms' action='functions.php' method='post'>"
						+"<textarea name='updCont' placeholder='Updated comment'></textarea><br>"
						+"<input type='hidden' name='postID' value='"+pID+"'>"
						+"<input type='hidden' name='topicID' value='"+tID+"'>"
						+"<button type='submit' name='submit' value='updContent'>Update comment</button> "
						+"<button type='submit' name='submit' value='delContent' onClick=\"return confirm('Are you sure?')\">"
						+"Delete comment</button>"
						+"</form>");
				$("#form"+rID).slideDown();
			}else{
				$("#row"+rID).append("<br><form class='forms' id='form"+rID+"' action='functions.php' method='post'>"
						+"<textarea name='updCont' placeholder='Updated description'></textarea><br>"
//The pID has a value of -1 and is used for identifying it as different type of content.
						+"<input type='hidden' name='postID' value='"+pID+"'>"
						+"<input type='hidden' name='topicID' value='"+tID+"'>"
						+"<button type='submit' name='submit' value='updContent'>Update</button> "
						+"</form>");
				$("#form"+rID).slideDown();
			}
			
		}
		
	}
</script>
</head>
<body>
<div id="header">
	<div class="col-xs-2 header">
		<a href="index.php"><h4 class="bold text-right">Home</h4></a>
	</div>
	<div class="col-xs-8 header">
	</div>
	<div class="col-xs-2 header">
	<?php
	//If a session or a cookie is detected the website welcomes the user.
		if(isset($_SESSION['userN'])){
			echo "<h4 class='bold' onClick='showLogin()'>Welcome, ".$_SESSION['userN']."</h4>";
		}else if(isset($_COOKIE['username'])){
			echo "<h4 class='bold' onClick='showLogin()'>Welcome, ".$_COOKIE['username']."</h4>";
		}else{
			echo "<h4 class='bold' onClick='showLogin()'>Login / Register</h4>";
		}
		
	?>
		<div id="login">
		<?php
		//Here the system checks if the user has logged in. If they have, a logout button will be created instead of the login form.
			if(isset($_SESSION['userN']) || isset($_COOKIE['username'])){
				echo "
				<form action='functions.php' method='post'>";
				if(isset($_GET["t"])){
					echo "<input type='hidden' name='tID' value='".$_GET["t"]."'>";
				}
				echo "
					<button class='btn btn-primary' type='submit' name='submit' value='Logout' action=''>
						Logout
					</button>
				</form>";
			}else{
				//If username isn't found from session or cookie we create the login form.
				echo "
				<form action='functions.php' method='post'>
					<input type='text' class='form-control' name='username' placeholder='Username' maxlength='30'>
					<input type='password' class='form-control' name='userpass' placeholder='Password' maxlength='30'>";
				if(isset($_GET["t"])){
					echo "<input type='hidden' name='tID' value='".$_GET["t"]."'>";
				}
				echo "
					<p class='msg text-center'>No special characters allowed</p>
					<input class='btn btn-primary' type='submit' name='submit' value='Login' action=''>
					<input class='btn btn-primary' type='submit' name='submit' value='Register' action=''>
				</form>";
			}
			
			?>
		</div>
	</div>
</div>
<div id="content">
	<div class="row">
		<div class="col-xs-12">
			<?php
			//If the page is given a value 't' we display a single topic and its comments.
				if(isset($_GET["t"])){
					printTopic($_GET["t"]);
				//If 't' isn't found the page instead displays all topics.
				}else{printTopics();}
				 
			
			?>
		</div>
	</div>
</div>
</body>
</html>