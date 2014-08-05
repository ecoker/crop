<?php
/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip — done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function getFiles($dir) {
	if (is_dir($dir)) {
		if ($handle = opendir($dir)) {
			$links = array();
			while (false !== ($file = readdir($handle))) {
				$pos = strpos($file, '.');
				if ((strlen($file) > 4) && ($pos > 1)) {
					$fileLoc = $dir.$file;
					list($width, $height, $type) = getimagesize($fileLoc);
					$ratio = $width / $height;
					$links[] = $dir.$file;
				}
			}
			return $links;
		}
	} else {
		return 'not directory: '.$dir;
	}
}

// function sendEmail($to, $subject, $message, $from = false) {
// 	echo $from.'<br>';
// 	echo $to.'<br>';
// 	if ($from) {
// 		$from_name = substr($from, 0, strpos($from, '@'));
// 		$from_email = $from;
// 	} else {
// 		$from_name = 'Glenn LaBarre';
// 		$from_email = 'glabarre@brownshoe.com';
// 	}
// 	$headers  = "MIME-Version: 1.0\n";
// 	$headers .= "Content-type: text/html; charset=iso-8859-1\n";
// 	$headers .= "X-Priority: 3\n";
// 	$headers .= "X-MSMail-Priority: Normal\n";
// 	$headers .= "X-Mailer: PHP/" . phpversion()."\n";
// 	$headers .= "From: \"".$from_name."\" <".$from_email.">\n";
// 	$headers .= "Reply-To: \"".$from_name."\" <".$from_email.">\n";
// 	echo $headers;
// 	if (stripos($message, '<html>') < 0) {
// 		$message = '<html><body>'.$message.'</body></html>';
// 	}
// 	if (mail($to, $subject, $message, $headers)) {
// 		return true;
// 	} else {
// 		return false;
// 	}
// }

?>