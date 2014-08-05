<?php

/* CLEARN 'ER UP */
function clean($value) {
  if (get_magic_quotes_gpc()) $value = stripslashes($value);
  if (!is_numeric($value))  $value = addslashes($value);
  return $value;
}
array_walk($_GET,'clean');
array_walk($_POST,'clean');
array_walk($_COOKIE,'clean');

include('includes/resizeCrop.php'); /* FANCY IMAGE FUNCTIONS */
$parts = explode('/', $_SERVER[REQUEST_URI]);

$file_location = "http://www.famousfootwear.com/ProductImages/shoes_$parts[2]$parts[1]" . substr($parts[3], strpos($parts[3], '.'));
if (!fileExists($file_location) && fileExists(str_ireplace('shoes', 'ff', $file_location))) $file_location = str_ireplace('shoes', 'ff', $file_location);


if ( strpos($parts[3], '.') > 0 ) {
  $rVal =  intval(substr($parts[3], 0, strpos($parts[3], '.')));
} else {
  $rVal = intval( $parts[3] );
}

$img = cropWhiteSpace( $file_location,  $rVal, $parts[2]);

if ($img) {
  header('Content-Type: image/jpeg');
  echo $img;
} else {
  header("HTTP/1.0 404 Not Found");
}

?>