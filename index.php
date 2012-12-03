<?php 
	include_once 'inc/functions.inc.php';
	include_once 'inc/db.inc.php';
	
	//Open a database connection
	$db = new PDO(DB_INFO, DB_USER, DB_PASS);
	
	//Determine if an entry ID was passed in the URL
	$id = (isset($_GET['id']) ? (int) $_GET['id'] : NULL);
	
	//Load the entries
	$e = retrieveEntries($db, $id);
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="Content-Type"
		content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<title> Simple Blog </title>
</head>

<body>
	<h1> Simple Blog Application </h1>
	<div id="entries">
<?php
	//Database layer
	//1. Connect to the database
	//2. Retrieve all entry titles and IDs if no entry ID was supplied
	//3. Retrieve an entry title and entry if an ID was supplied
	//Business Layer
	//1. Sanitize the data to prepare it for display
	//Presentation Layer
	//1. Present a list of linked entry titles if no entry ID was supplied
	//2. Present the entry title and entry if an ID was supplied
	
?>
	<p class="backlink">
		<a href="admin.php">Post a New Entry</a>
	</p>

	</div>
</body>

</html>