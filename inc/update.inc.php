<?php
//4. Connect to the database
//5. Formulate a MySQL query to store the entry data
//6. Sanitize the input and store it in the entries table
//7. Obtain the unique ID for the newly created entry
//8. Send the user to the newly created entry
if($_SERVER['REQUEST_METHOD']=='POST'
		&& $_POST['submit']=='Save Entry'
		&& !empty($_POST['title'])
		&& !empty($_POST['entry'])) {
	//Continue processing information
} else {
	
	//If both conditions aren't met, sends the user back to the main page
	header('Location: ../admin.php');
	exit;
}

?>