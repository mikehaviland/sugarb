<?php

header('Access-Control-Allow-Headers: x-requested-with');
header('Access-Control-Allow-Origin: *');

$url = 'https://api.sendgrid.com/';
$user = 'azure_0ad1ceeea927dd326840306720531329@azure.com';
$pass = 'EseXD7Mf2RudAIz';
$to = 'jarratt@sugarandbronze.com';

// Define some constants
define( "RECIPIENT_NAME", "Jarratt" );
define( "RECIPIENT_EMAIL", "jarratt@sugarandbronze.com" );
define( "EMAIL_SUBJECT", "Information Request" );

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
    'api_user'  => $user,
    'api_key'   => $pass,
    'to'        => $to,
    'subject'   => $subject,
    'html'      => $comment,
    'text'      => $comment,
    'from'      => $senderEmail,
  );
$request =  $url.'api/mail.send.json';

// If all values exist, send the email
if ( $senderName && $senderEmail && $comment ) :
  $recipient = RECIPIENT_NAME . " <" . RECIPIENT_EMAIL . ">";
  $headers = "From: " . $senderName . " <" . $senderEmail . ">";
  try {
    //mail( $recipient, $subject, $comment, $headers );
    // Generate curl request
    $session = curl_init($request);
    // Tell curl to use HTTP POST
    curl_setopt ($session, CURLOPT_POST, true);
    // Tell curl that this is the body of the POST
    curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
    // Tell curl not to return headers, but do return the response
    curl_setopt($session, CURLOPT_HEADER, false);
    // Tell PHP not to use SSLv3 (instead opting for TLS)
    curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// obtain response
$response = curl_exec($session);
curl_close($session);
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