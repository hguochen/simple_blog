<?php 
	include_once 'inc/functions.inc.php';
	include_once 'inc/db.inc.php';
	
	//Open a database connection
	$db = new PDO(DB_INFO, DB_USER, DB_PASS);
	
	//Figure out what page is being requested(default is blog)
	//Perform basic sanitization on the variable as well
	if(isset($_GET['page'])) {
		$page = htmlentities(strip_tags($_GET['page']));
	} else {
		$page = 'blog';
	}
	
	//Determine if an entry ID was passed in the URL
	$id = (isset($_GET['id']) ? (int) $_GET['id'] : NULL);
	
	//Load the entries
	$e = retrieveEntries($db, $page, $id);
	
	//Get the fulldisp flag and remove it from the array
	$fulldisp = array_pop($e);
	
	//Sanitize the data
	$e = sanitizeData($e);
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
	//Presentation Layer
	//1. Present a list of linked entry titles if no entry ID was supplied
	//2. Present the entry title and entry if an ID was supplied
	//If the full display flag is set, show the entry
	if($fulldisp == 1) {	
?>
	<h2> <?php echo $e['title'] ?></h2>
	<p> <?php echo $e['entry'] ?></p>
	<?php if($page=='blog'): ?>
	<p class="backlink">
		<a href="./">Back to Latest Entries</a>
	</p>
	<?php endif; ?>
<?php  

} else {
	//if the full display flag is 0, format linked entry titles
	//Loop through each entry 
	foreach ($e as $entry) { 
		
?>
	
	<p>
		<a href="?id=<?php echo $entry['id']?>">
			<?php echo $entry['title'] ?>
		</a>
	</p>
<?php 
	} //end of foreach statements
} //end of else statements
?>
	
	<p class="backlink">
	<?php if($page=='blog'): ?>
		<a href="/simple_blog/?page=<?php echo $page ?>">
			Post a New Entry
		</a>
	<?php endif; ?>
	</p>

	</div>
</body>

</html>