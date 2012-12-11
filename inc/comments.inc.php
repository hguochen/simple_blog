<?php
	include_once 'db.inc.php';
	
	class Comments {
		//Our database connection
		public $db;
		
		//An array for containing the entries
		public $comments;
		
		//Upon class instantiation, open a database connection
		public function __construct() {
			//Open a database connection and store it
			$this->db = new PDO(DB_INFO,DB_USER,DB_PASS);
		}
		
		//Display a form for users to enter new comments with 
		public function showCommentForm($blog_id) {
			return <<<FORM
			<form action="/simple_blog/inc/update.inc.php"
				method="post" id="comment-form">
				<fieldset>
					<legend>Post a Comment</legend>
					<label>Name
						<input type="text" name="name" maxlength="75" />
					</label>
					<label>Email
						<input type="text" name="email" maxlength="150" />
					</label>
					<label>Comment
						<textarea rows="10" cols="45" name="comment"></textarea>
					</label>
					<input type="hidden" name="blog_id" value="$blog_id" />
					<input type="submit" name="submit" value="Post Comemnt" />
					<input type="submit" name="submit" value="Cancel" />
				</fieldset>		
			</form>
FORM;
		}
		
		//Save comments to the database
		public function saveComment($p) {
			//Sanitize the data and store in variables
			$blog_id = htmlentities(strip_tags($p['blog_id']), ENT_QUOTES);
			$name = htmlentities(strip_tags($p['name']), ENT_QUOTES);
			$email = htmlentities(strip_tags($p['email']), ENT_QUOTES);
			$comment = htmlentities(strip_tags($p['comment']), ENT_QUOTES);
			//Keep formatting of comments and remove extra whitespace
			$comment = nl2br(trim($comments));
			
			//Generate and prepare the SQL command
			$sql = "INSERT INTO comments (blog_id, name, email, comment)
					VALUES(?,?,?,?)";
			if($stmt = $this->db->prepare($sql)) {
				//Execute the command, free used memory, and return true
				$stmt->execute(array($blog_id, $name, $email, $comment));
				$stmt->closeCursor();
				return TRUE;
			} else {
				//If something went wrong, return false
				return FALSE;
			}
		}
		
		//Load all comments for a blog entry into memory
		public function retrieveComments($blog_id) {
			//Get all the comments for the entry
			$sql = "SELECT id, name, email, comment, date
					FROM comments
					WHERE blog_id=?
					ORDER BY date DESC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array($blog_id));
			
			//Loop through returned rows
			while($comment = $stmt->fetch()) {
				//Store in memory for later use
				$this->comments[] = $comment;
			}
			
			//Set up a default response if no comments exist
			if(empty($this->comments)) {
				$this->comments[] = array (
						'id' => NULL,
						'name' => NULL,
						'email' => NULL,
						'comment' => "There are no comments on this entry.",
						'date' => NULL);
			}
		}
		
		//Generates HTML markup for displaying comments
		public function showComments($blog_id) {
			//Initialize the variable in case no comments exist
			$display = NULL;
			
			//Load the comments for the entry
			$this->retrieveComments($blog_id);
			
			//Loop through the stored comments
			foreach($this->comments as $c) {
				//Prevent empty fields if no comments exist
				if(!empty($c['date']) && !empty($c['name'])) {
					//Outputs similar to: July 8, 2009 at 4:39PM
					$format = "F j, Y \a\\t g:iA";
					
					//Convert $c['date to a timestamp, then format']
					$date = date($format, strtotime($c['date']));
					
					//Generate a byline for the comment
					$byline = "<span><strong>$c[name]</strong>
								[Posted on $date]</span>";
					if(isset($_SESSION['loggedin'])
						&& $_SESSION['loggedin'] == 1) {
					
					//Generate delete link for the comment display
					$admin = "<a href=\"/simple_blog/inc/update.inc.php"
							."?action=comment_delete&id=$c[id]\""
							."class=\"admin\">delete</a>";
					} else {
						$admin = NULL;
					}
				} else {
					//If we get here, no comments exist, set $byline and $admin to NULL
					$byline = NULL;
					$admin = NULL;
				}
				//Assemble the pieces into a formatted comment
				$display .= "
						<p class=\"comment\">$byline$c[comment]$admin</p>";
			}
			//Return all the fomatted comments as a string
			return $display;
		}
		
		//Ensure the user really wants to delete the comment
		public function confirmDelete($id) {
			//Store the entry url if available
			if(isset($_SERVER['HTTP_REFERER'])) {
				$url = $_SERVER['HTTP_REFERER'];
			}
			
			//Otherwise use the default view
			else {
				$url = '../';
			}
			
			return <<<FORM
<html>
	<head>
		<title>Please Confirm Your Decision</title>
		<link rel="stylesheet" type="text/css"
			href="/simple_blog/css/default.css" />
	</head>
	<body>
		<form action="/simple_blog/inc.update.inc.php" method="post">
					<fieldset>
						<legend>Are You Sure?</legend>
						<p>
							Are you sure you want to delete this comment?
						</p>
						<input type="hidden" name="id" value="$id" />
						<input type="hidden" name="action" value="comment_delete" />
						<input type="hidden" name="url" value="$url" />
						<input type="submit" name="confirm" value="Yes" />
						<input type="submit" name="confirm" value="No" />
  					</fieldset>
		</form>
	</body>
</html>
FORM;
		}
		
		//Removes the comment corresponding to $id from the database
		public function deleteComment($id) {
			$sql = "DELETE FROM comments
					WHERE id=?
					LIMIT 1";
			if($stmt = $this->db->prepare($sql)) {
				//Execute the command, free used memory, and return true
				$stmt->execute(array($id));
				$stmt->closeCursor();
				return TRUE;
			} else {
				//If something went wrong, return false
				return FALSE;
			}
		}
	}
?>