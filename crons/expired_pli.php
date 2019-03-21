<?

include "/home/chequersavenue/r2djp.co.uk/mot/includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

# Check for expiring PLI subs to warn users
$sql="SELECT * FROM members WHERE pli_date = DATE(DATE_SUB(NOW(), INTERVAL 11 MONTH))";
$expiring = $conn->query($sql);
if ($expiring->num_rows > 0) {
    while($row = $expiring->fetch_assoc()) {
        // output data of each row
        $pli_head_email = array();
        $pli_head_email[] = $config->email_treasurer;
        $pli_head_email[] = $config->email_mot;
        $to = implode(',', $pli_head_email);
        # Email admin to say PLI is expiring
        $subject = "UK R2 Builders - PLI Expiring";
        $message = "A members PLI expires in a month\r\n";
        $message .= "\r\n";
        $message .= $config->site_base."/member.php?member_uid=".$row['member_uid']."\r\n";
        $headers = "From: R2 Builders MOT <mot@r2djp.co.uk>"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($to, $subject, $message, $headers);

	# Also email member to let them know
	$subject = "UK R2 Builders - PLI Expiring";
        $message = "Your PLI expires in a month\r\n";
        $message .= "\r\n";
        $headers = "From: R2 Builders MOT <mot@r2djp.co.uk>"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($row['email'], $subject, $message, $headers);

    }

}



$sql="SELECT * FROM members WHERE pli_date = DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR))";
$expired = $conn->query($sql);
if ($expired->num_rows > 0) {
    while($row = $expired->fetch_assoc()) {
        // output data of each row
        $pli_head_email = array();
        $pli_head_email[] = $config->email_treasurer;
        $pli_head_email[] = $config->email_mot;
        $to = implode(',', $pli_head_email);
        # Email admin to say PLI is expiring
        $subject = "UK R2 Builders - PLI Expired";
        $message = "A members PLI has expired\r\n";
        $message .= "\r\n";
        $message .= $config->site_base."/member.php?member_uid=".$row['member_uid']."\r\n";
        $headers = "From: R2 Builders MOT <mot@r2djp.co.uk>"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($to, $subject, $message, $headers);

        # Also email member to let them know
        $subject = "UK R2 Builders - PLI Expired";
        $message = "Your PLI has expired\r\n";
        $message .= "\r\n";
        $headers = "From: R2 Builders MOT <mot@r2djp.co.uk>"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($row['email'], $subject, $message, $headers);

    }

}


$conn->close();
?>


