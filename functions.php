<?php

//This file is used to call functions found on other php-files. Every form calls this file.

//Including all necessary required .php-files.
require_once 'connection.php';
require_once 'content.php';
require_once 'login.php';
require_once 'editDB.php';

//Starting a session
session_start();

//All forms call the functions.php file. Here we read the value given by the submit button and call a specific function depending on the value.

//If the value 'submit' is detected we use a switch-statement to choose how to proceed.
if(isset($_REQUEST['submit'])){
	//In case the value of 'submit' is 'Login' we call a function related to the login process.
	switch($_REQUEST['submit']){
		case 'Login':
			//Sanitizing and trimming the inputs before passing them on to the functions.
			$un = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
			$up = trim(filter_var($_POST['userpass'], FILTER_SANITIZE_STRING));
			
			//Making sure the values aren't empty since it would be pointless to pass empty values to the functions.
			if(null != $un && null != $up){
				//Passing the username and password to the function. Also passing a third value which will be used to call the login()-function.
				if(isset($_POST['tID'])){
					loginRegisterSwitch($un, $up, 1, $_POST['tID']);
				}else{
					loginRegisterSwitch($un, $up, 1, -1);
				}
				
			}else{
				//If the values are empty, we redirect user back to the previous page with an errormessage.
				//PFF = PleaseFillFields
				if(isset($_POST['tID'])){
					header("Location: index.php?PFF&t=".$_POST['tID']);
				}else{
					header("Location: index.php?PFF");
				}
				
			}
			
			break;
		//In case the value of 'submit' is 'Register' we call a function related to the registering process.
		case 'Register':
			//Sanitizing and trimming the inputs before passing them on to the functions.
			$un = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
			$up = trim(filter_var($_POST['userpass'], FILTER_SANITIZE_STRING));
			
			//Making sure the values aren't empty since it would be pointless to pass empty values to the functions.
			if(null != $un && null != $up){
				if(isset($_POST['tID'])){
					loginRegisterSwitch($un, $up, 2, $_POST['tID']);
				}else{
					loginRegisterSwitch($un, $up, 2, -1);
				}
				
			}else{
				//If the values are empty, we redirect user back to the previous page with an errormessage.
				if(isset($_POST['tID'])){
					header("Location: index.php?RFF=false&t=".$_POST['tID']);
				}else{
					header("Location: index.php?RFF=false");
				}
			}
				
			break;
		//In case the value of 'submit' is 'Logout' we call the logout()-function.
		case 'Logout':
			if(isset($_POST['tID'])){
				logout($_POST['tID']);
			}else{
				logout(-1);
			}
			
			break;
		//In case the value of 'submit' is 'newTopic' we call the function which will add content to the database.
		case 'newTopic':
			//Before creating the new topic we check if the inputs are valid.
			$tt = trim(filter_var($_POST['newTop'], FILTER_SANITIZE_STRING));
			$td = trim(filter_var($_POST['newDesc'], FILTER_SANITIZE_STRING));
			if(null != $tt && null != $td){
				//The addContent function is given user inputs and an identifier for the type of content we want to add. 't' stands for topic.
				addContent($tt, $td, "t");
			}else{
				//If the inputs are invalid we redirect the user back to the previous page with a message attached to the url.
				//TFF = TopicFieldsFilled
				header("Location: index.php?TFF=false");
			}
			break;
		//In case the value of 'submit' is 'newComment' we call the function which will add content to the database.
		case 'newComment':
			//Before creating the new comment we check if the inputs are valid.
			$nc = trim(filter_var($_POST['newComm'], FILTER_SANITIZE_STRING));
			if(null != $nc){
				//Same as when new topic was created, the function is given user inputs and an identifier for the type of content. 'c' stands for comment.
				addContent($_POST['topicID'], $nc, "c");
			}else{
				//PFF = PostFieldsFilled
				header("Location: index.php?t=".$_POST['topicID']."&PFF=false");
			}
			break;
		//In case the value of 'submit' is 'updContent' we call the function which will edit existing content in the database.
		case 'updContent':
			$uc = trim(filter_var($_POST['updCont'], FILTER_SANITIZE_STRING));
			if(null != $uc && $uc != ""){
				//The function is given user inputs and an id to identify the comment we want to update.
				updContent($_POST['topicID'], $uc, $_POST['postID']);
			}else{
				//PFF = PostFieldsFilled
				header("Location: index.php?t=".$_POST['topicID']."&PFF=false");
			}
			break;
		//In case the value of 'submit' is 'delContent' we call the function which will delete content from the database.
		case 'delContent':
			//The function is given the identifier for the type of comment we want to remove and an ID of the post we want to remove.
			delContent($_POST['postID'], $_POST['topicID']);
			break;
	}
}

?>