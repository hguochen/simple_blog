<?php
	//Start the session
	session_start();
	
//include the functions so you can create a URL
include_once 'functions.inc.php';
//include the image handling class
include_once 'images.inc.php';

if($_SERVER['REQUEST_METHOD']=='POST'
		&& $_POST['submit']=='Save Entry'
		&& !empty($_POST['page'])
		&& !empty($_POST['title'])
		&& !empty($_POST['entry'])) {
	
	//Create a URL to save in the database
	$url = makeUrl($_POST['title']);
	
	if(isset($_FILES['image']['tmp_name'])) {
		try {
			//Instantiate the class and set a save path
			$img = new ImageHandler("/simple_blog/images/");
			
			//Process the file and store the returned path
			$img_path = $img->processUploadedImage($_FILES['image']);
		}
		catch(Exception $e) {
			//If an error occurred, output your custom error message
			die($e->getMessage());
		}
	} else {
		//Avoids a notice if no image was uploaded
		$img_path = NULL;
	}
	
	//Outputs the saved image path
	echo "Image Path: ", $img_path, "<br />";
	exit;
	
	//Include database credentials and connect to the database
	include_once 'db.inc.php';
	$db = new PDO(DB_INFO, DB_USER, DB_PASS);
	
	//Edit an existing entry
	if(!empty($_POST['id'])) {
		$sql = "UPDATE entries
				SET title=?, image=?, entry=?, url=?
				WHERE id=?
				LIMIT 1";
		$stmt = $db->prepare($sql);
		$stmt->execute(
				array(
					$_POST['title'],
					$img_path,
					$_POST['entry'],
					$url,
					$_POST['id']
				)
			);
		$stmt->closeCursor();
	} else { 
	//Create a new entry
		//Save the entry into the database
		$sql = "INSERT INTO entries 
				(page,title,image,entry,url) 
				VALUES(?,?,?,?,?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($_POST['page'],$_POST['title'],$img_path,$_POST['entry'],$url));
		$stmt->closeCursor();//close the cursor, enable the statement to be executed again
	}
	//Sanitize the information for use in the success URL
	$page = htmlentities(strip_tags($_POST['page']));
	
	//Get the ID of the query we just saved
	$id_obj = $db->query("SELECT LAST_INSERT_ID()");
	$id = $id_obj->fetch();
	$id_obj->closeCursor();
	
	//If a comment is being posted, handle it here
	if($_SERVER['REQUEST_METHOD']=='POST' && $_POST['submit'] == 'Post Comment') {
		//Include and instantiate the Comments class
		include_once 'comments.inc.php';
		$comments = new Comments();
		
		//Save the comment
		$comments->saveComment($_POST);
		
			//If available, store the entry the user came from
			if(isset($_SERVER['HTTP_REFERER'])) {
				$loc = $_SERVER['HTTP_REFERER'];
			} else {
				$loc = '../';
			}
			
			//Send the user back to the entry
			header('Location: '.$loc);
			exit;
	}
	
	//If the delete link is clicked on a comment, confirm it here
	if($_GET['action' == 'comment_delete']) {
		//Include and instantiate the Comments class
		include_once 'comments.inc.php';
		$comments = new Comments();
		echo $comments->confirmDelete($_GET['id']);
		exit;
	}
	
	//If the confirmDelete() form was submitted, handle it here
	if($_SERVER['REQUEST_METHOD']=='POST' && $_POST['action'] =='comment_delete') {
		//If set, store the entry from which we came
		$loc = isset($_POST['url']) ? $_POST['url'] : '../';
		
		//If the user clicked "Yes", continue with deletion
		if($_POST['confirm'] =="Yes") {
			//Include and instantiate the Comments class
			include_once 'comments.inc.php';
			$comments = new Comments();
			
			//Delete the comment and return to the entry
			if($comments->deleteComment($_POST['id'])) {
				header('Location: '.$loc);
				exit;
			}
			
			//If deleting fails, output an error message
			else {
				exit('Could not delete the comment.');
			}
		} else {
			//If the user clicked "No", do nothing and return to the entry
			header('Location: '.$loc);
			exit;
		}
		
	}
	
	//If a user is trying to log in, check it here
	if($_SERVER['REQUEST_METHOD']=='POST'
			&& $_POST['action']=='login'
			&& !empty($_POST['username'])
			&& !empty($_POST['password'])) {
		//Include database credentials and connect to the database
		include_once 'db.inc.php';
		$db = new PDO(DB_INFO,DB_USER,DB_PASS);
		$sql = "SELECT COUNT(*) AS num_users
				FROM admin
				WHERE username=?
				AND password=SHA1(?)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($_POST['username'], $_POST['password']));
		$response = $stmt->fetch();
		if($response['num_users'] > 0) {
			$_SESSION['loggedin'] = 1;
		} else {
			$_SESSION['loggedin'] = NULL;
		}
		header('Location: /simple_blog/');
		exit;
	}
	
	//If an admin is being created, save it here
	if($_SERVER['REQUEST_METHOD']== 'POST'
			&& $_POST['action']=='createuser'
			&& !empty($_POST['username'])
			&& !empty($_POST['password'])) {
		//Include database credentials and connect to the database
		include_once 'db.inc.php';
		$db = new PDO(DB_INFO,DB_USER,DB_PASS);
		$sql = "INSERT INTO admin 
				(username, password)
				VALUES(?, SHA1(?))";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($_POST['username'], $_POST['password']));
		header('Location: /simple_blog/');
		exit;
	} 
	
	//If the user has chosen to log out, process it here
	if($_GET['action']=='logout') {
		session_destroy();
		header('Location: ../');
		exit;
	}
	//Send the user to the new entry
	header('Location: /simple_blog/'.$page.'/'.$url);
	exit;
	
} else {
	unset($_SESSION['c_name'], $_SESSION['c_email'], $_SESSION['c_comment'], $_SESSION['error']);
	//If both conditions aren't met, sends the user back to the main page
	header('Location: /simple_blog/');
	exit;
}

?>