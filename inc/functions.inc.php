<?php
	function retrieveEntries($db, $page, $url=NULL) {
		//If an entry url was supplied, load the associated entry
		if(isset($url)) {
			$sql = "SELECT id, page, title, entry
					FROM entries
					WHERE url=?
					LIMIT 1";
			$stmt = $db->prepare($sql);
			$stmt->execute(array($url));//executes a prepared statement $_GET['url']
			
			//Save the returned entry array
			$e = $stmt->fetch(); //fetches the next row from the result set
			
			//Set the fulldisp flag for a single entry
			$fulldisp = 1;
			
		} else {
			//Entry url was not supplied, load all entry info for the page
			$sql = "SELECT id, page, title, entry, url
					FROM entries
					WHERE page=?
					ORDER BY created DESC";
			$stmt = $db->prepare($sql);
			$stmt->execute(array($page));
			
			$e = NULL;
			
			while($row = $stmt->fetch()) {
				$e[] = $row;
			}
			
			//Set the fulldisp flag for multiple entries
			$fulldisp = 0;
			
			//If no entries were returned, display a default message and set the fulldisp flag to display a single entry
			if(!is_array($e)) {
				$fulldisp = 1;
				$e = array(
						'title' => 'No Entries Yet',
						'entry' => '<a href="./admin.php">Post an entry!</a>');
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