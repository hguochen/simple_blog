<?php
	function retrieveEntries($db, $page, $id=NULL) {
		//If an entry ID was supplied, load the associated entry
		if(isset($id)) {
			$sql = "SELECT title, entry
					FROM entries
					WHERE id=?
					LIMIT 1";
			$stmt = $db->prepare($sql);
			$stmt->execute(array($_GET['id']));//executes a prepared statement
			
			//Save the returned entry array
			$e = $stmt->fetch(); //fetches the next row from the result set
			
			//Set the fulldisp flag for a single entry
			$fulldisp = 1;
			
		} else {
			//Entry ID was not supplied, load all entry titles for the page
			$sql = "SELECT id, page, title, entry
					FROM entries
					WHERE page=?
					ORDER BY created DESC";
			$stmt = $db->prepare($sql);
			$stmt->execute(array($page));
			
			$e = NULL;
			
			while($row = $stmt->fetch()) {
				$e[] = $row;
			}
			//Loop through the returned results and store as an array
			/*foreach($db->query($sql) as $row) {
				$e[] = array(
						'id'=> $row['id'],
						'title'=> $row['title']);
			}*/
			//Set the fulldisp flag for multiple entries
			$fulldisp = 0;
			
			//If no entries were returned, display a default message and set the fulldisp flag to display a single entry
			if(!is_array($e)) {
				$fulldisp = 1;
				$e = array(
						'title' => 'No Entries Yet',
						'entry' => '<a href="/simple_blog/admin.php">Post an entry!</a>');
			}
		}
		
		//Add the $fulldisp flag to the end of the array
		array_push($e, $fulldisp);
		
		return $e;
	}

	function sanitizeData($data) {
		if(!is_array($data)) {
			return strip_tags($data, "<a>");
		} else {
			//if data is an array, process each element
			//call sanitizeData recursively for each array element
			return array_map('sanitizeData', $data); 
		}
	}
?>