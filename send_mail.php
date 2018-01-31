<?php

$webmaster_email = "mirp.julia@gmail.com";

$feedback_page = "contact.html";
$error_page = "error_message.html";
$thankyou_page = "thank_you.html";

/*$email_address = $_REQUEST['email_address'] ;
$comments = $_REQUEST['comments'] ;
$first_name = $_REQUEST['first_name'] ;*/
$email_address = $_POST['email_address'] ;
$comments = $_POST['comments'] ;
$first_name = $_POST['first_name'] ;
$msg = "First Name: ".$first_name."\r\n"."Email: ".$email_address."\r\n"."Comments: ".$comments ;
/*The following function checks for email injection.
Specifically, it checks for carriage returns - typically used by spammers to inject a CC list.*/
function isInjected($str) {
  $injections = array('(\n+)',
  '(\r+)',
  '(\t+)',
  '(%0A+)',
  '(%0D+)',
  '(%08+)',
  '(%09+)'
  );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str)) {
    return true;
  }
  else {
    return false;
  }
}
// If the user tries to access this script directly, redirect them to the feedback form,
if (!isset($_POST['email_address'])) {
header( "Location: $feedback_page" );
}
// If the form fields are empty, redirect to the error page.
elseif (empty($first_name) || empty($email_address)) {
header( "Location: $error_page" );
}
/* If email injection is detected, redirect to the error page.*/
elseif ( isInjected($email_address) || isInjected($first_name)  || isInjected($comments) ) {
header( "Location: $error_page" );
}
// If we passed all previous tests, send the email then redirect to the thank you page.
else {

  mail( "$webmaster_email", "Feedback Form Personal Page", $msg );

  header( "Location: $thankyou_page" );
}
?>