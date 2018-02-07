<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
 </head>

 <body>

<?

include "menu.php";
include "config.php";

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

if ($_REQUEST['new_mot'] != "") {
    $fields = array('droid_uid',
	'date',
	'location',
	'approval',
	'annual_mot',
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
    printf("Error code: %s.\n", $stmt->sqlstate);
    printf("Error code: %s.\n", $stmt->error);

    $stmt->close();

    if ($_REQUEST['new_comment'] != "") {
        $sql = "INSERT INTO mot_comments(mot_uid, comment, added_by) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $mot_id, $_REQUEST['new_comment'], $_REQUEST['user']);
        $stmt->execute();
        $stmt->close();
    }
}


# Comments

echo "<div id=comments>";
echo "<form>";
echo "<textarea name=new_comment rows=20 cols=30>New MOT</textarea>";
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo "<input type=hidden name=user value=0><br />";
echo "</div>";



echo "<div id=info>";
echo "<ul>";
echo " <li>Date Taken: <input type=date name=date value=".date('Y-m-d')."></li>";
echo " <li>Location: <input type=text name=location></li>";
echo " <li>Club Approval: <select name=approval><option value=Yes>Yes</option><option value=No>No</option></select></li>";
echo " <li>MOT Renewal: <select name=annual_mot><option value=Yes>Yes</option><option value=No>No</option></select></li>";
echo " <li>Pass/Fail: <select name=approved><option value=Yes>Yes</option><option value=No>No</option><option value=WIP>WIP</option></select></li>";
echo " <li>MOT Officer: ".$officer."</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<div id=mot_test>";
echo "<div id=mot_block>";
echo "<h3>Structural</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
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
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
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
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
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
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Serious Safety Concern</td><td>".displayMOTRadio(gadget_danger)."</td></tr>";
echo "<tr><td>Gadget 1</td><td>".displayMOTRadio(gadget_1)."</td></tr>";
echo "<tr><td>Gadget 2</td><td>".displayMOTRadio(gadget_2)."</td></tr>";
echo "<tr><td>Gadget 3</td><td>".displayMOTRadio(gadget_3)."</td></tr>";
echo "<tr><td>Gadget 4</td><td>".displayMOTRadio(gadget_4)."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Basic Control</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
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

$conn->close();
?>
</div>

