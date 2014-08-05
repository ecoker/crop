<?php
//find the size of the borders
$altImg = false;
$img    = false;
$b_top  = 0;
$b_btm  = 0;
$b_lft  = 0;
$b_rt   = 0;
$width  = 0;
$height = 0;

function checkPixels($img, $colorArray) {
	global $b_top, $b_btm, $b_lft, $b_rt;
	$stopColors = array();

	//top
	for(; $b_top < imagesy($img); ++$b_top) {
	  for($x = 0; $x < imagesx($img); ++$x) {
	    if(!in_array(imagecolorat($img, $x, $b_top), $colorArray)) {
	       $stopColors['top'] = imagecolorat($img, $x, $b_top);
	       break 2; //out of the 'top' loop
	    }
	  }
	}
	
	//bottom
	for(; $b_btm < imagesy($img); ++$b_btm) {
	  for($x = 0; $x < imagesx($img); ++$x) {
	    if(!in_array(imagecolorat($img, $x, imagesy($img) - $b_btm-1), $colorArray)) {
	       $stopColors['btm'] = imagecolorat($img, $x, imagesy($img) - $b_btm-1);
	       break 2; //out of the 'bottom' loop
	    }
	  }
	}
	
	//left
	for(; $b_lft < imagesx($img); ++$b_lft) {
	  for($y = 0; $y < imagesy($img); ++$y) {
	    if(!in_array(imagecolorat($img, $b_lft, $y), $colorArray)) {
	       $stopColors['lft'] = imagecolorat($img, $x, $b_lft);
	       break 2; //out of the 'left' loop
	    }
	  }
	}
	
	//right
	for(; $b_rt < imagesx($img); ++$b_rt) {
	  for($y = 0; $y < imagesy($img); ++$y) {
	    if(!in_array(imagecolorat($img, imagesx($img) - $b_rt-1, $y), $colorArray)) {
	       $stopColors['rt'] = imagecolorat($img, $x, $b_rt);
	       break 2; //out of the 'right' loop
	    }
	  }
	}

	return $stopColors;

}

/* ===== START THE TURKEY HUNT ===== */
function fileExists($fileLoc) {
	$ch = curl_init( $fileLoc );
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($retcode == 200) return true;
	return false;
}

function findThatFile($fileLoc, $view) {
	global $img;

	if (stripos($fileLoc, '.gif') > 0) {
		$fileLoc = str_ireplace('.gif', '.jpg', $fileLoc);
	}

	if ( fileExists($fileLoc) ) {
		$img = @imagecreatefromjpeg($fileLoc);
		return $fileLoc;
	}

	if (fileExists(str_ireplace($view, 'if', $fileLoc))) {
		$img = @imagecreatefromjpeg(str_ireplace($view, 'if', $fileLoc));
		if (imagesx($img) > 300) {
			return str_ireplace($view, 'if', $fileLoc);
		}
	}

	if (fileExists(str_ireplace($view, 'il', $fileLoc))) {
		$img = @imagecreatefromjpeg(str_ireplace($view, 'il', $fileLoc));
		if (imagesx($img) > 300) {
			return str_ireplace($view, 'il', $fileLoc);
		}
	}

	if (fileExists(str_ireplace($view, 'ia', $fileLoc))) {
		// IT'S EXPENSIVE, BUT WE NEED TO KNOW THE SIZE
		$img = @imagecreatefromjpeg(str_ireplace($view, 'ia', $fileLoc));
		if (imagesx($img) > 300) {
			return str_ireplace($view, 'ia', $fileLoc);
		}
	}

	$img = @imagecreatefromjpeg(str_ireplace($view, 'is', $fileLoc));
	return str_ireplace($view, 'is', $fileLoc);

}

function checkRatio($alt=false) {
	global $b_top, $b_btm, $b_lft, $b_rt, $img, $altImg;
	
	if ($alt) {
		$width  = imagesx($altImg) - ($b_lft + $b_rt);
		$height = imagesy($altImg) - ($b_top + $b_btm);
	} else {
		$width  = imagesx($img) - ($b_lft + $b_rt);
		$height = imagesy($img) - ($b_top + $b_btm);
	}
	$ratio = $width / $height;
	return $ratio;
}

function altImage($fileLoc) {
	global $altImg;

	$testFile = preg_replace('/ia|ib|il/i', 'if', $fileLoc);
	if (fileExists($testFile) && strpos($testFile, 'if') > 0) {
		$altImg = @imagecreatefromjpeg($testFile);
		return $testFile;
	}
	
	$testFile = preg_replace('/ia|if|ib/i', 'il', $fileLoc);
	if (fileExists($testFile) && strpos($testFile, 'il') > 0) {
		$altImg = @imagecreatefromjpeg($testFile);
		return $testFile;
	}

	$testFile = preg_replace('/ib|if|il/i', 'ia', $fileLoc);
	if (fileExists($testFile) && strpos($testFile, 'ia') > 0) {
		$altImg = @imagecreatefromjpeg($testFile);
		return $testFile;
	}

}

/* ===== WHITE SPACE CROP ===== */
function cropWhiteSpace($fileLoc, $rVal, $view) {
	global $b_top, $b_btm, $b_lft, $b_rt, $img, $width, $height, $altImg;
	
	$fileLoc = findThatFile($fileLoc, $view); // also sets $img variable

	$colorArray = array('0xFFFFFF', '0xFEFEFE', '0xFEFEFC');
	
	while ( count($colorArray) < 16 ) {
		$stopColors = checkPixels($img, $colorArray);
		$colorArray[] = $stopColors['btm'];
		$colorArray[] = $stopColors['top'];
	}

	$ratio = checkRatio();

	if ($ratio < 1.75 && $ratio > 1.15) {
		/* HEIGHT TO WIDTH DOES NOT MATCH STANDARD - let's try another one */
		$altLoc = altImage($fileLoc);

		/* ARCHIVE ORIGINAL CROPS IF WE NEED TO REVERT */
		$crops = array('top' => $b_top, 'rt' => $b_rt, 'bot' => $b_btm, 'lft' => $b_lft);

		/* RESET VALUES */
		$colorArray = array('0xFFFFFF', '0xFEFEFE', '0xFEFEFC');
		$b_top = $b_rt = $b_btm = $b_lft = 0;

		while ( count($colorArray) < 16 ) {
			$stopColors = checkPixels($altImg, $colorArray);
			$colorArray[] = $stopColors['btm'];
			$colorArray[] = $stopColors['top'];
		}

		if (checkRatio(true) < 1.75) {
			/* RETURN TO DEFAULT IMAGE CROPS */
			$b_top = $crops['top'];
			$b_rt  = $crops['rt'];
			$b_btm = $crops['btm'];
			$b_lft = $crops['lft'];
		} else {
			$fileLoc = $altLoc;
			$img = $altImg;
		}
	}

	// header('Content-Type: image/jpeg');
	// imagejpeg($img);
	// die();

	$width = imagesx($img); // don't resize the width to preserve ratios
	$height = imagesy($img)-($b_top+$b_btm); // remove top and bottom white space

	/* IMAGICK VERSION */
	$picture = new Imagick($fileLoc);
	$picture->cropImage($width, $height, $b_lft, $b_top);
	$picture->thumbnailImage($rVal, imagesx($img), true);
	return $picture;

}

?>