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
    $sql = "INSERT INTO droids(member_uid, name, primary_droid, type, style, radio_controlled, transmitter_type, material, weight, battery, drive_voltage, sound_system, value, date_added) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssssss", $member_uid, $name, $_REQUEST['primary_droid'], $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['radio_controlled'], $_REQUEST['transmitter_type'],
                                $_REQUEST['material'], $_REQUEST['weight'], $_REQUEST['battery'], $_REQUEST['drive_voltage'], $_REQUEST['sound_system'], $_REQUEST['value']);
    $member_uid=$_REQUEST['member_uid'];
    $name=$_REQUEST['name'];
    $stmt->execute();
    $droid_uid = $stmt->insert_id;
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    }
    if (!file_exists("uploads/members/".$row['member_uid']."/".$droid_uid)) {
        mkdir("uploads/members/".$row['member_uid']."/".$droid_uid);
    }


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
echo "<table>";
echo " <tr><td><a href='#'>Droid Name: <div class='tooltipcontainer'><div class='tooltip'>eg. R2-D2, C5-S4, etc.</div></div></a></td><td><input type=text name=name size=50></td></tr>";
echo " <tr><td><a href='#'>Primary Droid: <div class='tooltipcontainer'><div class='tooltip'>Is this their primary droid (for PLI payment reasons)</div></div></a></td><td><select name=primary_droid><option value=Yes selected>Yes</option><option value=No>No</option></select></td></tr>";
echo " <tr><td><a href='#'>Type: <div class='tooltipcontainer'><div class='tooltip'></div></div></a></td><td><select name=type><option value=R2>R2</option><option value=R3>R3</option><option value=R4>R4</option><option value=R5>R5</option><option value=R6>R6</option><option value=R1>R1</option><option value=R0>R0</option><option value=BB>BB</option><option value=C1>C1</option><option value=A-LT>A-LT</option><option value=other>Other</option></select></td></tr>";
echo " <tr><td><a href='#'>Style: <div class='tooltipcontainer'><div class='tooltip'>Is it a specific style, eg New Hope, TFA, etc.</div></div></a></td><td><input type=text name=style size=50></td></tr>";
echo " <tr><td><a href='#'>Radio Controlled?: <div class='tooltipcontainer'><div class='tooltip'>Does it use a standard RC controller?</div></div></a></td><td><select name=radio_controlled><option value=Yes>Yes</option><option value=No selected>No</option></select></td></tr>";
echo " <tr><td><a href='#'>Transmitter Type: <div class='tooltipcontainer'><div class='tooltip'>Specktrum, PS3, Xbox360, etc.</div></div></a></td><td><input type=text name=transmitter_type size=50></td></tr>";
echo " <tr><td><a href='#'>Material: <div class='tooltipcontainer'><div class='tooltip'>Main build materials</div></div></a></td><td><input type=text name=material size=50></td></tr>";
echo " <tr><td><a href='#'>Approx Weight: <div class='tooltipcontainer'><div class='tooltip'>Leave empty if you don't know, otherwise weight in kg</div></div></a></td><td><input type=text name=weight size=10></td></tr>";
echo " <tr><td><a href='#'>Battery Type: <div class='tooltipcontainer'><div class='tooltip'>SLA, Li-Ion, LiPo, etc.</div></div></a></td><td><input type=text name=battery size=10></td></tr>";
echo " <tr><td><a href='#'>Drive Voltage: <div class='tooltipcontainer'><div class='tooltip'>12V, 24V, 36V, etc.</div></div></a></td><td><input type=text name=drive_voltage size=10></td></tr>";
echo " <tr><td><a href='#'>Sound System: <div class='tooltipcontainer'><div class='tooltip'>Amp wattage, MP3 trigger, etc.</div></div></a></td><td><input type=text name=sound_system size=50></td></tr>";
echo " <tr><td><a href='#'>Approx Value: <div class='tooltipcontainer'><div class='tooltip'>Only enter if owner is OK</div></div></a></td><td><input type=text name=value size=10></td></tr>";
echo "</table>";
echo "<input type=submit name=add value=Add>";

include "includes/footer.php";
?>


