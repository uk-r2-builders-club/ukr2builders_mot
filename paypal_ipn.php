<?php 

include "includes/config.php";

require('PaypalIPN.php');

use PaypalIPN;

$ipn = new PaypalIPN();

// Use the sandbox endpoint during testing.
// $ipn->useSandbox();
//

$verified = $ipn->verifyIPN();

foreach ($_POST as $key => $value) {
    $data_text .= $key . " = " . $value . "\r\n";
}

$fp = fopen('data.txt', 'a');//opens file in append mode
fwrite($fp, $data_text);

if ($verified) {
	fwrite($fp, "--------------------------------");
	fwrite($fp, "Verified");
	fwrite($fp, $_POST['custom']);
    $conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "UPDATE members SET pli_date=NOW() WHERE member_uid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_POST['custom']);
    $stmt->execute();
    if ($options['SEND_EMAILS']) {
            # PLI Change, send email
            # Get some MOT details for emails
            $sql = "SELECT * FROM members WHERE member_uid=".$_REQUEST['member_uid'];
            $member = $conn->query($sql)->fetch_object();
            $mot_head_email = array();
            $mot_head_email[] = $config->email_treasurer;
            $mot_head_email[] = $member->email;
            $to = implode(',', $mot_head_email);
            # Approved, email peeps
            $subject = "UK R2 Builders MOT - PLI Updated";
            $message = "Your PLI Payment has been received and logged in the system\r\n";
            $message .= "\r\n";
            $message .= "Member: ".$member->forename." ".$member->surname."\r\n";
            $message .= "PLI Date: ".$_REQUEST['pli_date']."\r\n";
            $headers = "From: R2 Builders MOT <".$config->from_email.">"."\r\n"."X-Mailer: PHP/".phpversion();
//            $success = mail($to, $subject, $message, $headers);
    }

}
fclose($fp);

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");

