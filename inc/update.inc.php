<?php
//6. Sanitize the input and store it in the entries table
if($_SERVER['REQUEST_METHOD']=='POST'
		&& $_POST['submit']=='Save Entry'
		&& !empty($_POST['title'])
		&& !empty($_POST['entry'])) {
	
	//Include database credentials and connect to the database
	include_once 'db.inc.php';
	$db = new PDO(DB_INFO, DB_USER, DB_PASS);
	
	//Save the entry into the database
	$sql = "INSERT INTO entries 
			(title,entry) 
			VALUES(?,?)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($_POST['title'],$_POST['entry']));
	$stmt->closeCursor();//close the cursor, enable the statement to be executed again
	
	//Get the ID of the query we just saved
	$id_obj = $db->query("SELECT LAST_INSERT_ID()");
	$id = $id_obj->fetch();
	$id_obj->closeCursor();
	
	//Send the user to the new entry
	header('Location: ../admin.php?id='.$id[0]);
	exit;
	
} else {
	
	//If both conditions aren't met, sends the user back to the main page
	header('Location: ../admin.php');//go up one folder
	exit;
}

?>