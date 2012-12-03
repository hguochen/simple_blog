<?php
	function retrieveEntries($db, $id=NULL) {
		//If an entry ID was supplied, load the associated entry
		if(isset($id)) {
			//Load specified entry
		} else {
			//Entry ID was not supplied, load all entry titles
			$sql = "SELECT id, title
			FROM entries
			ORDER BY created DESC";
			//Loop through the returned results and store as an array
			foreach($db->prepare($sql) as $row) {
				$e[] = array(
						'id'=> $row['id'],
						'title'=> $row['title']);
			}
			//Set the fulldisp flag for multiple entries
			$fulldisp = 0;
		}
	}
?>