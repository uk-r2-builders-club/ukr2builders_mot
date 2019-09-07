<?

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['ADD_MOT'])) {
	die();
}


echo "<div id=wrapper>";
echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function displayMOTRadio($field) {
	$option = "<input type=radio name=$field value=Pass>Pass";
	$option .= "<input type=radio name=$field value=Fail checked>Fail";
	$option .= "<input type=radio name=$field value=NA>NA";
	return $option;
}

if ($_REQUEST['new_mot'] != "" && ($_SESSION['permissions'] & $perms['ADD_MOT'])) {
    $fields = array('droid_uid',
	'date',
	'location',
	'mot_type',
	'struct_overall',
	'struct_left_leg',
	'struct_right_leg',
	'struct_left_foot_ankle',
	'struct_right_foot_ankle',
	'struct_left_shoulder',
	'struct_right_shoulder',
	'struct_center_foot',
	'struct_center_ankle',
	'struct_body_skirt_frame',
	'struct_dome_mech',
	'struct_dome',
	'struct_details',
	'mech_center_wheel',
	'mech_drive',
	'mech_two_three_two',
	'mech_dome',
	'mech_utility_arms',
	'mech_rear_door_skins',
	'mech_doors',
	'elec_overall',
	'elec_transmitter',
	'elec_receiver',
	'elec_feet',
	'elec_dome',
	'elec_audio',
	'elec_other',
	'gadget_danger',
	'gadget_1',
	'gadget_2',
	'gadget_3',
	'gadget_4',
	'drive_general',
	'drive_dizzy',
	'drive_boomerang',
	'drive_gnaremoob',
	'drive_eight',
	'drive_speed',
	'drive_estop',
	'drive_dome_spin',
	'drive_range',
	'approved',
	'user'
	);
    if ($_REQUEST['mot_type'] == "Retest") {
	    $sql = "SELECT * FROM mot WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY date DESC LIMIT 1";
	    $mot_result = $conn->query($sql);
	    if ($mot_result->num_rows > 0) {
		    $row = $mot_result->fetch_assoc();
		    $_REQUEST['date'] = $row['date'];
	    }
    }
    $sql = "INSERT INTO mot(";
    $x=0;
    $bind_params_type="";
    $bind_params= array();
    $bind_params[] = & $bind_params_type;
    while ($x < (sizeof($fields)-1) ) {
            $sql .= $fields[$x].",";
	    $bind_params[] = & $_REQUEST[$fields[$x]];
	    $x++;
    }
    $sql .= $fields[$x].") VALUES (";
    $x=0;
    while ($x < (sizeof($fields)-1) ) {
	    $sql .= "?,";
	    $bind_params_type .= "s";
	    $x++;
    }
    $sql .= "?)";
    $bind_params_type .= "s";
    $bind_params[] = & $_REQUEST[$fields[$x]];

    $stmt = $conn->prepare($sql);
    call_user_func_array(array($stmt, 'bind_param'), $bind_params);
    $stmt->execute();
    $mot_id = $stmt->insert_id;
    if ($stmt->error != "") {
            printf("<br />Error code: %s.\n", $stmt->sqlstate);
            printf("<br />Error code: %s.\n", $stmt->error);
    } elseif ($config->site_options & $options['SEND_EMAILS']) {
	    # Get some MOT details for emails
	    $sql = "SELECT * FROM members WHERE member_uid = ".$_SESSION["user"];
            $officer = $conn->query($sql)->fetch_object();
            $sql = "SELECT * FROM droids WHERE droid_uid=".$_REQUEST['droid_uid'];
            $droid = $conn->query($sql)->fetch_object();
            $sql = "SELECT * FROM members WHERE member_uid=$droid->member_uid";
            $member = $conn->query($sql)->fetch_object();
            $mot_head_email = array();
	    $mot_head_email[] = $officer->email;
	    $mot_head_email[] = $config->email_mot;
	    $mot_head_email[] = $config->email_treasurer;
	    $to = implode(',', $mot_head_email);
            # Approved, email peeps
            $subject = "UK R2 Builders MOT - MOT Submitted";
	    $message = "An MOT has been submitted by ".$officer->forename." ".$officer->surname." and an email to the droid owner with";
	    $message .= "instructions on paying any PLI due<br />";
            $message .= "<br />";
            $message .= "<a href=\"".$config->site_base."/droid.php?droid_uid=".$_REQUEST['droid_uid']."\">".$config->site_base."/droid.php?droid_uid=".$_REQUEST['droid_uid']."</a><br />";
	    $message .= "<br />";
	    $message .= "<ul><li>Member: ".$member->forename." ".$member->surname."</li>";
	    $message .= "<li>MOT Location: ".$_REQUEST['location']."</li>";
	    $message .= "<li>Droid Status: ".$_REQUEST['approved']."</li>";
	    $message .= "<li>MOT Type ".$_REQUEST['mot_type']."</li></ul>";
            $headers = "From: R2 Builders MOT <".$config->from_email.">"."\r\n"."X-Mailer: PHP/".phpversion()."\r\n";
	    $headers .= "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $success = mail($to, $subject, $message, $headers);
	    echo "<br />Email sent to MOT officers ".$success;

	    # Send email to owner
	    $subject = "UK R2 Builders MOT - MOT Submitted";
	    $message = "Hello ".$member->forename.",<br />";
	    $message .= "<br />";
	    $message .= "An MOT for your droid has been submitted by ".$officer->forename." ".$officer->surname."<br />";
	    $message .= "<br />";
	    $message .= "<ul><li>MOT Location: ".$_REQUEST['location']."</li>";
            $message .= "<li>Droid Status: ".$_REQUEST['approved']."</li>";
            $message .= "<li>MOT Type ".$_REQUEST['mot_type']."</li>";
	    $message .= "</ul>";
	    if (($_REQUEST['approved'] == "Yes") || ($_REQUEST['approved'] == "Advisory") || ($_REQUEST['approved'] == "WIP")) {
		    if (($_REQUEST['approved'] == "Yes") || ($_REQUEST['approved'] == "WIP")) {
		        $message .= "Congratulations on your droid passing its MOT. To be covered by the group's PLI, please make sure<br />";
		        $message .= "to send in your payment. You are covered by the PLI at the point payment is received and cleared by the PLI officer.<br />";
		    } else {
			$message .= "Congratulations on the results of your droids MOT. The MOT has been submitted with an Advisory associated<br />";
			$message .= "with it. The MOT officer should have gone through the advisories and advised you of timescales to fix the issues.<br />";
			$message .= "You are covered by PLI at the point payment is received and cleared by the PLI officer. Failure to rectify the<br />";
			$message .= "outstanding advisories within the agreed timescale, or failure to contact an MOT officer to explain why you cannot<br />";
			$message .= "effect the fix before the timescale expired will render you PLI void until such a time that you surrender your droid<br />";
			$message .= "for a full MOT.<br />";
		    }
		    $message .= "<br />";
		    if ($droid->primary_droid == "Yes") {
			    $message .= "Cost for primary droid is £".$config->primary_cost.". The link below will take you directly to the paypal payment page<br />";
			    $message .= "<a href=\"".$config->paypal_link."/".$config->primary_cost."\">".$config->paypal_link."/".$config->primary_cost."</a><br />";
	            } else {
			    $message .= "As you already have a droid, cost for an additional droid is £".$config->other_cost.". The link below will take you directly to the paypal payment page<br />";
			    $message .= "<a href=\"".$config->paypal_link."/".$config->other_cost."\">".$config->paypal_link."/".$config->other_cost."</a><br />";
	            }
		    $message .= "Or, if you prefer not to follow the links, you can send it via paypal to ".$config->paypal_email."<br />";
	    }
	    $success = mail($member->email, $subject, $message, $headers);
	    echo "<br />Email sent to droid owner ".$success;

	    $sql = "UPDATE members SET pli_active='' WHERE member_uid = $droid->member_uid";
	    $conn->query($sql);

    }

    $stmt->close();

    if ($_REQUEST['new_comment'] != "") {
        $sql = "INSERT INTO mot_comments(mot_uid, comment, added_by) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $mot_id, $_REQUEST['new_comment'], $_SESSION['user']);
        $stmt->execute();
        $stmt->close();
    }

    echo "<br />";
    echo "<a href=droid.php?droid_uid=".$_REQUEST['droid_uid'].">Back to droid</a>";
    echo "<br />";

}

$sql = "SELECT forename, surname FROM members WHERE member_uid = ".$_SESSION["user"];
$officer = $conn->query($sql)->fetch_object();
$officer_name = $officer->forename." ".$officer->surname;


# Comments

echo "<div class=comments>";
echo "<form>";
echo "<textarea name=new_comment rows=20 cols=30>New MOT</textarea>";
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo "<input type=hidden name=user value=".$_SESSION['user']."><br />";
echo "</div>";



echo "<div id=info>";
echo "<ul>";
echo " <li>Date Taken: <input type=date name=date value=".date('Y-m-d')."></li>";
echo " <li>Location: <input type=text name=location></li>";
echo " <li>MOT Type: <select name=mot_type><option value=Initial>Initial</option><option value=Renewal>Renewal</option><option value=Retest>Retest</option></select></li>";
echo " <li>Pass: <select name=approved><option value=Yes>Yes</option><option value=No>No</option><option value=WIP>WIP</option><option value=Advisory>Yes (Advisory)</option></select></li>";
echo " <li>MOT Officer: ".$officer_name."</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<div id=mot_test>";
echo "<div id=mot_block>";
echo "<h3>Structural</h3>";
echo "<table><tr><th width=250px>Test</th><th width=200px>Pass/Fail</th></tr>";
echo "<tr><td>Overall Structural Worry?</td><td>".displayMOTRadio(struct_overall)."</td></tr>";
echo "<tr><td>Left Leg</td><td>".displayMOTRadio(struct_left_leg)."</td></tr>";
echo "<tr><td>Left Foot/Ankle Joint</td><td>".displayMOTRadio(struct_left_foot_ankle)."</td></tr>";
echo "<tr><td>Left Shoulder Joint</td><td>".displayMOTRadio(struct_left_shoulder)."</td></tr>";
echo "<tr><td>Right Leg</td><td>".displayMOTRadio(struct_right_leg)."</td></tr>";
echo "<tr><td>Right Foot/Ankle Joint</td><td>".displayMOTRadio(struct_right_foot_ankle)."</td></tr>";
echo "<tr><td>Right Shoulder Joint</td><td>".displayMOTRadio(struct_right_shoulder)."</td></tr>";
echo "<tr><td>Center Foot/Ankle Joint</td><td>".displayMOTRadio(struct_center_foot)."</td></tr>";
echo "<tr><td>Center Leg to Body Joint</td><td>".displayMOTRadio(struct_center_ankle)."</td></tr>";
echo "<tr><td>Body/Frame/Skirt</td><td>".displayMOTRadio(struct_body_skirt_frame)."</td></tr>";
echo "<tr><td>Dome Rotation Mechanism</td><td>".displayMOTRadio(struct_dome_mech)."</td></tr>";
echo "<tr><td>Dome</td><td>".displayMOTRadio(struct_dome)."</td></tr>";
echo "<tr><td>Details</td><td>".displayMOTRadio(struct_details)."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Mechanical</h3>";
echo "<table><tr><th width=250px>Test</th><th width=200px>Pass/Fail</th></tr>";
echo "<tr><td>Center Wheel Setup</td><td>".displayMOTRadio(mech_center_wheel)."</td></tr>";
echo "<tr><td>Drive Setup</td><td>".displayMOTRadio(mech_drive)."</td></tr>";
echo "<tr><td>2-3-2? - Discuss</td><td>".displayMOTRadio(mech_two_three_two)."</td></tr>";
echo "<tr><td>Dome Spin</td><td>".displayMOTRadio(mech_dome)."</td></tr>";
echo "<tr><td>Utility Arms</td><td>".displayMOTRadio(mech_utility_arms)."</td></tr>";
echo "<tr><td>Rear Door/Skins Access</td><td>".displayMOTRadio(mech_rear_door_skins)."</td></tr>";
echo "<tr><td>Doors</td><td>".displayMOTRadio(mech_doors)."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Electrical</h3>";
echo "<table><tr><th width=250px>Test</th><th width=200px>Pass/Fail</th></tr>";
echo "<tr><td>Overall Setup</td><td>".displayMOTRadio(elec_overall)."</td></tr>";
echo "<tr><td>Control System Transmitter</td><td>".displayMOTRadio(elec_transmitter)."</td></tr>";
echo "<tr><td>Control System Receiver</td><td>".displayMOTRadio(elec_receiver)."</td></tr>";
echo "<tr><td>Feet Speed Controller and motors</td><td>".displayMOTRadio(elec_feet)."</td></tr>";
echo "<tr><td>Dome Speed Controller and motor</td><td>".displayMOTRadio(elec_dome)."</td></tr>";
echo "<tr><td>Audio</td><td>".displayMOTRadio(elec_audio)."</td></tr>";
echo "<tr><td>Other Electronics</td><td>".displayMOTRadio(elec_other)."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Gadgets</h3>";
echo "<table><tr><th width=250px>Test</th><th width=200px>Pass/Fail</th></tr>";
echo "<tr><td>Serious Safety Concern</td><td>".displayMOTRadio(gadget_danger)."</td></tr>";
echo "<tr><td>Gadget 1</td><td>".displayMOTRadio(gadget_1)."</td></tr>";
echo "<tr><td>Gadget 2</td><td>".displayMOTRadio(gadget_2)."</td></tr>";
echo "<tr><td>Gadget 3</td><td>".displayMOTRadio(gadget_3)."</td></tr>";
echo "<tr><td>Gadget 4</td><td>".displayMOTRadio(gadget_4)."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Basic Control</h3>";
echo "<table><tr><th width=250px>Test</th><th width=200px>Pass/Fail</th></tr>";
echo "<tr><td>Any Driving Issues</td><td>".displayMOTRadio(drive_general)."</td></tr>";
echo "<tr><td>Dizzy</td><td>".displayMOTRadio(drive_dizzy)."</td></tr>";
echo "<tr><td>Boomerang</td><td>".displayMOTRadio(drive_boomerang)."</td></tr>";
echo "<tr><td>Reverse Boomerang</td><td>".displayMOTRadio(drive_gnaremoob)."</td></tr>";
echo "<tr><td>Figure of 8</td><td>".displayMOTRadio(drive_eight)."</td></tr>";
echo "<tr><td>0-60 Test</td><td>".displayMOTRadio(drive_speed)."</td></tr>";
echo "<tr><td>Emergency Stop</td><td>".displayMOTRadio(drive_estop)."</td></tr>";
echo "<tr><td>Dome Spin</td><td>".displayMOTRadio(drive_dome_spin)."</td></tr>";
echo "<tr><td>Range exceed test</td><td>".displayMOTRadio(drive_range)."</td></tr>";
echo "</table>";
echo "</div>";

echo "<input type=submit value=Submit name=new_mot>";
echo "</form>";

echo "</div>";

echo "</div>";

include "includes/footer.php";
?>

