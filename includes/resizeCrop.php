<?php
//find the size of the borders
$img = false;
$b_top = 0;
$b_btm = 0;
$b_lft = 0;
$b_rt = 0;

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

	return @imagecreatefromjpeg(str_ireplace($view, 'is', $fileLoc));

}

function checkRatio() {
	global $b_top, $b_btm, $b_lft, $b_rt, $img;
	$width = imagesx($img) - ($b_lft + $b_rt);
	$height = imagesy($img) - ($b_top + $b_btm);
	$ratio = $width / $height;
	return $ratio;
}

function altImage($fileLoc) {

	$testFile = preg_replace('/ia|ib|il/i', 'if', $fileLoc);
	if (fileExists($testFile) && strpos($testFile, 'if') > 0) return $testFile;
	
	$testFile = preg_replace('/ia|if|ib/i', 'il', $fileLoc);
	if (fileExists($testFile) && strpos($testFile, 'il') > 0) return $testFile;

	$testFile = preg_replace('/ib|if|il/i', 'ia', $fileLoc);
	if (fileExists($testFile) && strpos($testFile, 'ia') > 0) return $testFile;

}

/* SORT ARRAY */
function compare_heights($a, $b) { 
    if($a->height == $b->height) return 0;
  	return ($a->height < $b->height) ? -1 : 1;
}
function compare_widths($a, $b) { 
    if($a->width == $b->width) return 0;
  	return ($a->width > $b->width) ? -1 : 1;
}

function preferredImage($view_exps, $img_array, $side_view) {
	$found = false;
	foreach($view_exps as $exp) {
		foreach ($img_array as $o) {
			if (preg_match($exp, $o->loc)) {
				$h_ratio = $o->height / $side_view->height;
				$w_ratio = $o->width / $side_view->width;
				if ($h_ratio > .825 && $w_ratio > .825) {
					$side_view = $o;
					$found = true;
					break;
				}
			}	
		}
		if ($found) break;
	}
	return $side_view;
}

/* IMAGE RODEO */
function imageRodeo($fileLoc, $view) {
	global $b_top, $b_btm, $b_lft, $b_rt, $img;
	$views = array('ia', 'ib', 'ic', 'id', 'if', 'ii', 'ij', 'ik', 'il');
	$m = $view . ' // ' . checkRatio() . '<br>' . '<img src="' . $fileLoc . '" /><br>';
	
	$img_array = array();
	
	foreach($views as $vw) {
		$testLoc = str_ireplace($view, $vw, $fileLoc);
		if ($vw == 'il' && fileExists( str_ireplace('.jpg', '.gif', $testLoc))) $testLoc = str_ireplace('.jpg', '.gif', $testLoc);
		if (fileExists( $testLoc )) {
			if (strpos($testLoc, '.gif')) $img = @imagecreatefromgif( $testLoc );
			else $img = @imagecreatefromjpeg( $testLoc );
			$colorArray = array('0xFFFFFF', '0xFEFEFE', '0xFEFEFC');
			$b_top = $b_btm = $b_lft = $b_rt = 0;
			while ( count($colorArray) < 16 ) {
				$stopColors = checkPixels($img, $colorArray);
				$colorArray[] = $stopColors['btm'];
				$colorArray[] = $stopColors['top'];
			}
			$testRatio[$vw] = checkRatio();
			$img_width = imagesx($img)  - ($b_lft + $b_rt);
			$img_height = imagesy($img)  - ($b_top + $b_btm);
			$m .= $vw . ' | Width: ' . $img_width . ' | Height: ' . $img_height . ' | Ratio: ' . checkRatio() . '<br>' . '<img src="' . $testLoc . '" /><br>';
			if (imagesx($img) > 200) {
				$img_array[] = (object) array('width'=>$img_width, 'height'=>$img_height, 'ratio'=>checkRatio(), 'loc'=>$testLoc);
			}
		} else {
			$testRatio[$vw] = false;
		}
	}
	
	usort($img_array, 'compare_heights');	
	if (count($img_array) >= 5) $img_array = array_slice($img_array, 2);

	usort($img_array, 'compare_widths');
	// echo '<pre>';
	// print_r($img_array);
	// echo '</pre>';

	$side_view = preferredImage(array('/_ib/i', '/_if/i', '/_ia/i'), $img_array, array_shift($img_array));

	// echo '<img src="' . $side_view->loc . '">';
	// die();

	return $side_view->loc;
}

/* ===== WHITE SPACE CROP ===== */
function cropWhiteSpace($fileLoc, $rVal, $view, $brand) {
	global $b_top, $b_btm, $b_lft, $b_rt, $img;
	if (stripos($fileLoc, '.gif') > 0) {
		$fileLoc = str_ireplace('.gif', '.jpg', $fileLoc);
	}
	
	$fileLoc = findThatFile($fileLoc, $view); // also sets $img variable

	$colorArray = array('0xFFFFFF', '0xFEFEFE', '0xFEFEFC');
	while ( count($colorArray) < 16 ) {
		$stopColors = checkPixels($img, $colorArray);
		$colorArray[] = $stopColors['btm'];
		$colorArray[] = $stopColors['top'];
	}

	/* THIS LOGIC IS SUPER SPOTTY - NEEDS TO BE BRAND SPECIFIC... */
	$brands = array('skechers', 'skecherscali', 'skechersperformance', 'skecherswork', 'kswiss');
	if (checkRatio() < 1.5 && in_array($brand, $brands)) {
		/* CALL IN THE CLOWNS */
		$fileLoc = imageRodeo($fileLoc, $view);
		// echo '<img src="' . $fileLoc . '">';
		// die();
		$img = @imagecreatefromjpeg( $fileLoc );
		$colorArray = array('0xFFFFFF', '0xFEFEFE', '0xFEFEFC');
		$b_top = $b_btm = $b_lft = $b_rt = 0;
		while ( count($colorArray) < 16 ) {
			$stopColors = checkPixels($img, $colorArray);
			$colorArray[] = $stopColors['btm'];
			$colorArray[] = $stopColors['top'];
		}
	}

	/* IMAGICK VERSION */
	$width = imagesx($img); // don't resize the width to preserve ratios
	$height = imagesy($img)-($b_top+$b_btm); // remove top and bottom white space
	$picture = new Imagick($fileLoc);
	$picture->cropImage($width, $height, $b_lft, $b_top);
	$picture->thumbnailImage($rVal, imagesx($img), true);
	return $picture;
}
?>