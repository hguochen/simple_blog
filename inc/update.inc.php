<?php
	
//include the functions so you can create a URL
include_once 'functions.inc.php';

if($_SERVER['REQUEST_METHOD']=='POST'
		&& $_POST['submit']=='Save Entry'
		&& !empty($_POST['page'])
		&& !empty($_POST['title'])
		&& !empty($_POST['entry'])) {
	
	//Create a URL to save in the database
	$url = makeUrl($_POST['title']);
	
	//Include database credentials and connect to the database
	include_once 'db.inc.php';
	$db = new PDO(DB_INFO, DB_USER, DB_PASS);
	
	//Save the entry into the database
	$sql = "INSERT INTO entries 
			(page,title,entry,url) 
			VALUES(?,?,?,?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($_POST['page'],$_POST['title'],$_POST['entry'],$url));
	$stmt->closeCursor();//close the cursor, enable the statement to be executed again
	
	//Sanitize the information for use in the success URL
	$page = htmlentities(strip_tags($_POST['page']));
	
	//Get the ID of the query we just saved
	$id_obj = $db->query("SELECT LAST_INSERT_ID()");
	$id = $id_obj->fetch();
	$id_obj->closeCursor();
	
	//Send the user to the new entry
	header('Location: /simple_blog/'.$page.'/'.$url);
	exit;
	
} else {
	
	//If both conditions aren't met, sends the user back to the main page
	header('Location: ../');//go up one folder
	exit;
}

?>