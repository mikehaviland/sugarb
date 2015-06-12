<?php

header('Access-Control-Allow-Headers: x-requested-with');
header('content-type: application/json; charset=utf-8')
header("Access-Control-Allow-Origin: *");

// Define some constants
define( "RECIPIENT_NAME", "Jarratt" );
define( "RECIPIENT_EMAIL", "jarratt@sugarandbronze.com" );
define( "EMAIL_SUBJECT", "Information Request" );
$url = 'https://api.sendgrid.com/';
$user = 'azure_0ad1ceeea927dd326840306720531329@azure.com';
$pass = 'EseXD7Mf2RudAIz';
$recipient = "jarratt@sugarandbronze.com";

// Read the form values
$success      = false;
//$xhr          = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
$xhr          = isset( $_POST['ajax'] )
              ? true
              : false;
$senderName   = isset( $_POST['senderName'] )
              ? preg_replace( "/[^\.\-\' a-zA-Z0-9]/", '', $_POST['senderName'] )
              : '';
$senderEmail  = isset( $_POST['senderEmail'] )
              ? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", '', $_POST['senderEmail'] )
              : '';
$subject      = isset( $_POST['subject'] )
              ? preg_replace( "/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/", '', $_POST['subject'] )
              : EMAIL_SUBJECT;
$comment      = isset( $_POST['comment'] )
              ? preg_replace( "/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/", '', $_POST['comment'] )
              : '';

$params = array(
  'api_user' => $user,
  'api_key' => $pass,
  'to' => $recipient,
  'subject' => $subject,
  "text" => $comment,
  "from" => $senderEmail
);

$request = $url.'api/mail.send.json';


// If all values exist, send the email
if ( $senderName && $senderEmail && $comment ) :
  //$recipient = RECIPIENT_NAME . " <" . RECIPIENT_EMAIL . ">";
  //$headers = "From: " . $senderName . " <" . $senderEmail . ">";
  try {
    $session = curl_init($request);

    curl_setopt($session, CURLOPT_POST, true);

    curl_setopt($session, CURLOPT_POSTFIELDS, $params);

    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($session);
    curl_close($session);

    print_r($response);
    echo "$response";
    $success = 'success';
  } catch (Exception $e) {
    $success = $e->getMessage();
  }
else:
  $success = 'error: incomplete data';
endif;

// Return an appropriate response to the browser
if ( $xhr ) : // AJAX Request
  echo $success;

else : // HTTP Request ?>
<!doctype html>
<html>
  <head>
    <title>Thanks!</title>
  </head>
  <body>
    <p>
    <?php
      if ( $success == 'success') :
        echo "<p>Thanks for sending your message! We'll get back to you shortly.</p>";
      else :
        echo "<p>There was a problem sending your message. Please try again.</p>";
      endif;
    ?>
    </p>
    <p>Click your browser's Back button to return to the page.</p>
  </body>
</html>
<?php endif; ?>