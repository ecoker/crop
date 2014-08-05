<?php
require_once('includes/ses.php');
$ses = new SimpleEmailService('AKIAJAF3HBQ5UIWDV3MA', 'Ck0YiLp/kWuSGebYJ9SoyjuvtOxyYErlzvXxxi3j');

// print_r($ses->verifyEmailAddress('jwhite@brownshoe.com'));
// print_r($ses->verifyEmailAddress('glabarre@brownshoe.com'));

$m = new SimpleEmailServiceMessage();
$m->addTo('jwhite@brownshoe.com');
$m->setFrom('jwhite@brownshoe.com');
$m->setSubject('FORMER SELF');
$m->setMessageFromString('DO NOT EAT THE APPLE PIE NEXT WEEK. It disrupts the continuity between space and time.');

$result = $ses->sendEmail($m);
if ($result) {
	print_r($result);
} else {
	echo "Some sort of undefined error has occured.";
}