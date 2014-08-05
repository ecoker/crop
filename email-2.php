<?php
 require_once "Mail.php";
 
 $from = "brownshoed2c@gmail.com";
 $to = "Ehren Coker <ecoker@brownshoe.com>";
 $subject = "Hi!";
 $body = "Google Send Test";
 
$host = "ssl://smtp.gmail.com";
$port = "465";
$username = "brownshoed2c@gmail.com";  //<> give errors
$password = "shoescom8300";
 
 $headers = array ('From' => $from,
   'To' => $to,
   'Subject' => $subject);
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
     'username' => $username,
     'password' => $password));
 
 $mail = $smtp->send($to, $headers, $body);
 
 if (PEAR::isError($mail)) {
   echo("<p>" . $mail->getMessage() . "</p>");
  } else {
   echo("<p>Message successfully sent!</p>");
  }
 ?>


<?php

//require_once "Mail.php";

$from = "<mrcoker@gmail.com>";
$to = "<ecoker@brownshoe.com>";
$subject = "Hi!";
$body = "Hi,\n\nHow are you?";

$host = "ssl://smtp.gmail.com";
$port = "465";
$username = "brownshoed2c@gmail.com";  //<> give errors
$password = "shoescom8300";

$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'port' => $port,
    'auth' => true,
    'username' => $username,
    'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }

?>