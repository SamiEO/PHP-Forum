<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include("functions.php");
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
#content{margin-left:20%;margin-right:20%;background-color:lightgrey;}
.header{background-color:darkgrey;height:50px;}
a{color:black;}
.bold:hover, a:hover{text-decoration:none;color:black;font-weight:bold;}
#login{background-color:darkgrey;padding:5px;display:none;margin-top:20px;border-radius:2px;}
textarea{width:100%;height:40px;}
button{margin-top:5px;}
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
	function showLogin(){
		if($('#login').is(":visible")){
			$('#login').slideUp();
		}else{$('#login').slideDown();}
		
	}
	
	$(document).ready(function(){
		if(window.location.href.indexOf("log=false") > -1){
			alert("Login failed: User not found");
		}else if(window.location.href.indexOf("reg=false") > -1){
			alert("Username is already taken");
		}else if(window.location.href.indexOf("TFF=false") > -1){
			alert("Please fill all text boxes when creating a new topic");
		}else if(window.location.href.indexOf("PFF=false") > -1){
			alert("Cannot create null comment");
		}else if(window.location.href.indexOf("RFF=false") > -1){
			alert("All registration fields have to be filled");
		}
	});
	function showEdit(pID, rID, tID){
		if($("#form"+rID).length){
			if($("#form"+rID).is(":visible")){
				$("#form"+rID).hide();
			}else{$("#form"+rID).show();}
			
		}else{
			$("#row"+rID).append("<br><form id='form"+rID+"' action='functions.php' method='post'><textarea name='updComm' placeholder='Updated comment'></textarea><br>"
					+"<input type='hidden' name='postID' value='"+pID+"'>"
					+"<input type='hidden' name='topicID' value='"+tID+"'>"
					+"<button type='submit' name='submit' value='updComment'>Update comment</button> "
					+"<button type='submit' name='submit' value='delComment' onClick=\"return confirm('Are you sure?')\">Delete comment</button>"
					+"</form>");
		}
		
	}
</script>
</head>
<body>
<div id="header">
	<div class="col-xs-10 header">
		<a href="index.php"><h4 class="bold">Home</h4></a>
	</div>
	<div class="col-xs-2 header">
	<?php
		if(isset($_SESSION['userN'])){
			echo "<h4 class='bold' onClick='showLogin()'>Welcome, ".$_SESSION['userN']."</h4>";
		}else if(isset($_COOKIE['username'])){
			echo "<h4 class='bold' onClick='showLogin()'>Welcome, ".$_COOKIE['username']."</h4>";
		}else{echo "<h4 class='bold' onClick='showLogin()'>Login</h4>";}
		
	?>
		<div id="login">
		<?php
			if(isset($_SESSION['userN']) || isset($_COOKIE['username'])){
				echo "
				<form action='login.php' method='post'>
					<button class='btn btn-primary' type='submit' name='submit' value='Logout' action=''>Logout</button>
				</form>";
			}else{
				echo "
				<form action='login.php' method='post'>
					<input type='text' class='form-control' name='username' placeholder='Username' >
					<input type='password' class='form-control' name='userpass' placeholder='Password' > 
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
				if(isset($_GET["t"])){
					printTopic($_GET["t"]);
				}else{printTopics();}
				 
			
			?>
		</div>
	</div>
</div>
</body>
</html>