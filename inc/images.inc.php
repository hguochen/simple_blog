<?php
	class ImageHandler {
		//The folder i which to save images
		public $save_dir;
		
		//Sets the $save_dir on instantiation
		public function __construct($save_dir) {
			$this->save_dir = $save_dir;
		}
		
		/** Resizes/resamples an image uploaded via a web form
		*@param array $upload the array contained in $_FILES
		*@param bool $rename whether or not the image should be renamed
		*@return string the path to the resized uploaded file
		*/
		public function processUploadedImage($file, $rename=TRUE) {
			//Separate the uploaded file array
			list($name, $type, $tmp, $err, $size) = array_values($file);
			
			//If an error occurred, throw an exception
			if($err != UPLOAD_ERR_OK) {
				throw new Exception('An error occurred with the upload!');
				return;
			}
			//Check that the directory exists
			$this->checkSaveDir();
			
			//Rename the file if the flag is set to TRUE
			if($rename == TRUE) {
				//Retrieve information about the image
				$img_ext = $this->getImageExtension($type);
				
				$name = $this->renameFile($img_ext);
			}
			
			//Create the full path to the image for saving
			$filepath = $this->save_dir.$name;
			
			//Store the absolute path to move the image
			$absolute = $_SERVER['DOCUMENT_ROOT'].$filepath;
			
			//Save the image
			if(!move_uploaded_file($tmp, $absolute)) {
				throw new Exception("Couldn't save the uploaded file!");
			}
			return $filepath;
		}
		/**
		 * Generates a unique name for a file
		 * 
		 * Users the current timestamp and a randomly generated number to create
		 * a unique name to be used for an uploaded file.
		 * This helps prevent a new file upload from overwriting an existing file with 
		 * the same name.
		 * 
		 * @param string $ext the file extension for the upload
		 * @return string the new filename
		 */
		private function renameFile($ext) {
			/*
			 * Returns the current timestamp and a random number to avoid duplicate filenames
			 */	
			return time() .'_'.mt_rand(1000,9999).$ext;
		}
		
		/**
		 * Determines the filetype and extension of an image
		 * 
		 * @param string $type the MIME type of the image
		 * @return string the extension to be used with the file
		 */
		private function getImageExtension($type) {
			switch($type) {
				case 'image/gif':
					return '.gif';
				case 'image/jpeg':
				case 'image/pjpeg':
					return '.jpg';
				
				case 'image/png':
					return '.png';
					
				default:
					throw new Exception('File type is not recognized!');
			}
		}
		
		/** Ensures that the save directory exists
		 * Checks for the existence fo the supplied save directory,
		 * and creates the directory if it doesn't exist. Creation is recursive
		 * @param void
		 * @return void
		 * */
		private function checkSaveDir() {
			//Determine the path to check
			$path = $_SERVER['DOCUMENT_ROOT'].$this->save_dir;
			
			//Checks if the directory exists
			if(!is_dir($path)) {
				//Create the directory
				if(!mkdir($path, 0777, true)) {
					//On failure, throws an error
					throw new Exception("Can't create the directory!");
				}
			}
		}
	}
?>