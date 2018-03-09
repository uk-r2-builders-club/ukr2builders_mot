<?

include "/home/chequersavenue/r2djp.co.uk/mot/includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

# Check for expiring MOTs to warn users
$sql="SELECT * FROM mot WHERE date = DATE(DATE_SUB(NOW(), INTERVAL 11 MONTH))";
$expiring = $conn->query($sql);
if ($expiring->num_rows > 0) {
    while($row = $expiring->fetch_assoc()) {
        // output data of each row
        $pli_head_email = array();
        $pli_head_email[] = $config->email_mot;
        $to = implode(',', $pli_head_email);
        # Email admin to say PLI is expiring
        $subject = "UK R2 Builders - MOT Expiring";
        $message = "A droids MOT expires in a month\r\n";
        $message .= "\r\n";
        $message .= $config->site_base."/droid.php?droid_uid=".$row['droid_uid']."\r\n";
        $headers = "From: webmaster@r2djp.co.uk"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($to, $subject, $message, $headers);

	# Also email member to let them know
	$sql = "SELECT * FROM droids WHERE droid_uid = ".$row['droid_uid'];
	$droid = $conn->query($sql)->fetch_assoc();
        $sql = "SELECT * FROM members WHERE member_uid = ".$droid["member_uid"];
        $owner = $conn->query($sql)->fetch_assoc();

	$subject = "UK R2 Builders - ".$droid['name']." MOT Expiring";
        $message = "Your droid's MOT expires in a month\r\n";
        $message .= "\r\n";
        $headers = "From: webmaster@r2djp.co.uk"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($owner['email'], $subject, $message, $headers);

    }

}

$sql="SELECT * FROM mot WHERE date = DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR))";
$expiring = $conn->query($sql);
if ($expiring->num_rows > 0) {
    while($row = $expiring->fetch_assoc()) {
        // output data of each row
        $pli_head_email = array();
        $pli_head_email[] = $config->email_mot;
        $to = implode(',', $pli_head_email);
        # Email admin to say PLI is expiring
        $subject = "UK R2 Builders - MOT Expired";
        $message = "A droids MOT expired\r\n";
        $message .= "\r\n";
        $message .= $config->site_base."/droid.php?droid_uid=".$row['droid_uid']."\r\n";
        $headers = "From: webmaster@r2djp.co.uk"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($to, $subject, $message, $headers);

        # Also email member to let them know
        $sql = "SELECT * FROM droids WHERE droid_uid = ".$row['droid_uid'];
        $droid = $conn->query($sql)->fetch_assoc();
        $sql = "SELECT * FROM members WHERE member_uid = ".$droid["member_uid"];
        $owner = $conn->query($sql)->fetch_assoc();

        $subject = "UK R2 Builders - ".$droid['name']." MOT Expired";
        $message = "Your droid's MOT has expired\r\n";
        $message .= "\r\n";
        $headers = "From: webmaster@r2djp.co.uk"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($owner['email'], $subject, $message, $headers);

    }

}



$conn->close();
?>


