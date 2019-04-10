<?

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['EDIT_DROIDS'])) {
	die();
}

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['name'] != "") {
    $sql = "INSERT INTO droids(member_uid, name, primary_droid, type, style, radio_controlled, transmitter_type, material, weight, battery, drive_voltage, sound_system, value, topps_id, date_added) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssssss", $member_uid, $name, $_REQUEST['primary_droid'], $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['radio_controlled'], $_REQUEST['transmitter_type'],
                                $_REQUEST['material'], $_REQUEST['weight'], $_REQUEST['battery'], $_REQUEST['drive_voltage'], $_REQUEST['sound_system'], $_REQUEST['value'], $_REQUEST['topps_id']);
    $member_uid=$_REQUEST['member_uid'];
    $name=$_REQUEST['name'];
    $stmt->execute();
    $droid_uid = $stmt->insert_id;
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
    if ($stmt->error == "") {
        $sql = "SELECT forename,surname FROM members WHERE member_uid = ".$_SESSION["user"];
        $officer = $conn->query($sql)->fetch_object();
        $officer_name = $officer->forename." ".$officer->surname;
        $mot_head_email = array();
        $mot_head_email[] = $officer->email;
        $mot_head_email[] = $config->email_mot;
	$mot_head_email[] = $config->email_treasurer;
        $to = implode(',', $mot_head_email);
        # Approved, email peeps
        $subject = "UK R2 Builders MOT - New Droid";
        $message = "A droid has been added by ".$officer->forename." ".$officer->surname."\r\n";
        $message .= "\r\n";
        $message .= $config->site_base."/droid.php?droid_uid=".$droid_uid."\r\n";
        $headers = "From: R2 Builders MOT <".$config->from_email.">"."\r\n"."X-Mailer: PHP/".phpversion();
        mail($to, $subject, $message, $headers);
    }

    $stmt->close();
}

echo "<form>";
echo "<input type=hidden name=member_uid value=".$_REQUEST['member_uid'].">";
echo "<h2>Droid Name: <input type=text name=name size=50></h2>";
echo "<ul>";
echo " <li>Primary Droid: <select name=primary_droid><option value=Yes selected>Yes</option><option value=No>No</option></select></li>";
echo " <li>Type: <select name=type><option value=R2>R2</option><option value=R3>R3</option><option value=R4>R4</option><option value=R5>R5</option><option value=R6>R6</option><option value=R1>R1</option><option value=R0>R0</option><option value=BB>BB</option><option value=C1>C1</option><option value=other>Other</option></select></li>";
echo " <li>Style: <input type=text name=style size=50></li>";
echo " <li>Radio Controlled?: <select name=radio_controlled><option value=Yes>Yes</option><option value=No selected>No</option></select></li>";
echo " <li>Transmitter Type: <input type=text name=transmitter_type size=50></li>";
echo " <li>Material: <input type=text name=material size=50></li>";
echo " <li>Approx Weight: <input type=text name=weight size=10></li>";
echo " <li>Battery Type: <input type=text name=battery size=10></li>";
echo " <li>Drive Voltage: <input type=text name=drive_voltage size=10></li>";
echo " <li>Sound System: <input type=text name=sound_system size=50></li>";
echo " <li>Approx Value: <input type=text name=value size=10></li>";
echo " <li>Topps Number: <input type=text name=topps_id size=10></li>";
echo "</ul>";
echo "<input type=submit name=add value=Add>";

include "includes/footer.php";
?>


