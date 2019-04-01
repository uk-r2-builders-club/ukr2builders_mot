<?

include "includes/header.php";

echo "<div id=wrapper>";
echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function colouriseMOT($mot_result, $field) {
	if ($mot_result[$field] == "Pass") {
		$s = "<p class=mot_pass>Pass</p>";
	} elseif ($mot_result[$field] == "NA") {
		$s = "<p class=mot_na>NA</p>";
	} else {
		$s = "<p class=mot_fail>Fail</p>";
	}
	return $s;
}

if ($_REQUEST['new_comment'] != "" && ($_SESSION['permissions'] & $perms['ADD_MOT'])) {
    $sql = "INSERT INTO mot_comments(mot_uid, comment, added_by) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['mot_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

if (($_REQUEST['delete'] == "yes") && ($_SESSION['permissions'] & $perms['ADD_MOT'])) {
	echo "Deleting MOT record";
	$sql = "DELETE FROM mot WHERE mot_uid=".$_REQUEST['mot_uid'];
	$result = $conn->query($sql);
	$sql = "DELETE FROM mot_comments where mot_uid=".$_REQUEST['mot_uid'];
	$result = $conn->query($sql);

}

# Comments

$sql = "SELECT * FROM mot_comments WHERE mot_uid = " .$_REQUEST["mot_uid"] ." ORDER BY added_on";
$comments_result = $conn->query($sql);

echo "<div class=comments>";
if ($comments_result->num_rows > 0) {
    // output data of each row
    echo "<div id=comment>";
    while($row = $comments_result->fetch_assoc()) {
        $sql = "SELECT forename,surname FROM members WHERE member_uid = ".$row["added_by"];
        $officer = $conn->query($sql)->fetch_object();
        $officer_name = $officer->forename." ".$officer->surname;
        echo "<div id=officer>$officer_name</div>";
        echo "<div id=time>".$row['added_on']."</div>";
        echo "<div id=text>".$row['comment']."</div>";
    }
    echo "</div>";
} else {
    echo "No Comments";
}

echo "<form>";
echo "<textarea name=new_comment>New comment</textarea>";
echo "<input type=hidden name=mot_uid value=".$_REQUEST['mot_uid'].">";
echo "<input type=hidden name=officer value=".$_SESSION['user']."><br />";
echo "<input type=submit value=Add>";
echo "</div>";



$sql = "SELECT * FROM mot WHERE mot_uid = ". $_REQUEST['mot_uid'];
$result = $conn->query($sql);
$mot = $result->fetch_assoc();
$sql = "SELECT forename,surname FROM members WHERE member_uid = ".$mot["user"];
$officer = $conn->query($sql)->fetch_object();
$officer_name = $officer->forename." ".$officer->surname;
$sql = "SELECT * FROM droids WHERE droid_uid = ".$mot["droid_uid"];
$droid = $conn->query($sql)->fetch_object();
$sql = "SELECT * FROM members WHERE member_uid = $droid->member_uid";
$member = $conn->query($sql)->fetch_object();

echo "<div id=info>";
echo "<ul>";
echo " <li>Owner: <a href=member.php?member_uid=$member->member_uid>$member->forename $member->surname</a></li>";
echo " <li>Droid: <a href=droid.php?droid_uid=".$mot["droid_uid"].">$droid->name</a></li>";
echo " <li>Date Taken: ".$mot['date']."</li>";
echo " <li>Location: ".$mot['location']."</li>";
echo " <li>MOT type: ".$mot['mot_type']."</li>";
echo " <li>Pass/Fail: ".$mot['approved']."</li>";
echo " <li>MOT Officer: ".$officer_name."</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<div id=mot_test>";
echo "<div id=mot_block>";
echo "<h3>Structural</h3>";
echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Overall Structural Worry?</td><td>".colouriseMOT($mot, 'struct_overall')."</td></tr>";
echo "<tr><td>Left Leg</td><td>".colouriseMOT($mot, 'struct_left_leg')."</td></tr>";
echo "<tr><td>Left Foot/Ankle Joint</td><td>".colouriseMOT($mot, 'struct_left_foot_ankle')."</td></tr>";
echo "<tr><td>Left Shoulder Joint</td><td>".colouriseMOT($mot, 'struct_left_shoulder')."</td></tr>";
echo "<tr><td>Right Leg</td><td>".colouriseMOT($mot, 'struct_right_leg')."</td></tr>";
echo "<tr><td>Right Foot/Ankle Joint</td><td>".colouriseMOT($mot, 'struct_right_foot_ankle')."</td></tr>";
echo "<tr><td>Right Shoulder Joint</td><td>".colouriseMOT($mot, 'struct_right_shoulder')."</td></tr>";
echo "<tr><td>Center Foot/Ankle Joint</td><td>".colouriseMOT($mot, 'struct_center_foot')."</td></tr>";
echo "<tr><td>Center Leg to Body Joint</td><td>".colouriseMOT($mot, 'struct_center_ankle')."</td></tr>";
echo "<tr><td>Body/Frame/Skirt</td><td>".colouriseMOT($mot, 'struct_body_skirt_frame')."</td></tr>";
echo "<tr><td>Dome Rotation Mechanism</td><td>".colouriseMOT($mot, 'struct_dome_mech')."</td></tr>";
echo "<tr><td>Dome</td><td>".colouriseMOT($mot, 'struct_dome')."</td></tr>";
echo "<tr><td>Details</td><td>".colouriseMOT($mot, 'struct_details')."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Mechanical</h3>";
echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Center Wheel Setup</td><td>".colouriseMOT($mot, 'mech_center_wheel')."</td></tr>";
echo "<tr><td>Drive Setup</td><td>".colouriseMOT($mot, 'mech_drive')."</td></tr>";
echo "<tr><td>2-3-2? - Discuss</td><td>".colouriseMOT($mot, 'mech_two_three_two')."</td></tr>";
echo "<tr><td>Dome Spin</td><td>".colouriseMOT($mot, 'mech_dome')."</td></tr>";
echo "<tr><td>Utility Arms</td><td>".colouriseMOT($mot, 'mech_utility_arms')."</td></tr>";
echo "<tr><td>Rear Door/Skins Access</td><td>".colouriseMOT($mot, 'mech_rear_door_skins')."</td></tr>";
echo "<tr><td>Doors</td><td>".colouriseMOT($mot, 'mech_doors')."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Electrical</h3>";
echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Overall Setup</td><td>".colouriseMOT($mot, 'elec_overall')."</td></tr>";
echo "<tr><td>Control System Transmitter</td><td>".colouriseMOT($mot, 'elec_transmitter')."</td></tr>";
echo "<tr><td>Control System Receiver</td><td>".colouriseMOT($mot, 'elec_receiver')."</td></tr>";
echo "<tr><td>Feet Speed Controller and motors</td><td>".colouriseMOT($mot, 'elec_feet')."</td></tr>";
echo "<tr><td>Dome Speed Controller and motor</td><td>".colouriseMOT($mot, 'elec_dome')."</td></tr>";
echo "<tr><td>Audio</td><td>".colouriseMOT($mot, 'elec_audio')."</td></tr>";
echo "<tr><td>Other Electronics</td><td>".colouriseMOT($mot, 'elec_other')."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Gadgets</h3>";
echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Serious Safety Concern</td><td>".colouriseMOT($mot, 'gadget_danger')."</td></tr>";
echo "<tr><td>Gadget 1</td><td>".colouriseMOT($mot, 'gadget_1')."</td></tr>";
echo "<tr><td>Gadget 2</td><td>".colouriseMOT($mot, 'gadget_2')."</td></tr>";
echo "<tr><td>Gadget 3</td><td>".colouriseMOT($mot, 'gadget_3')."</td></tr>";
echo "<tr><td>Gadget 4</td><td>".colouriseMOT($mot, 'gadget_4')."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Basic Control</h3>";
echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Any Driving Issues</td><td>".colouriseMOT($mot, 'drive_general')."</td></tr>";
echo "<tr><td>Dizzy</td><td>".colouriseMOT($mot, 'drive_dizzy')."</td></tr>";
echo "<tr><td>Boomerang</td><td>".colouriseMOT($mot, 'drive_boomerang')."</td></tr>";
echo "<tr><td>Reverse Boomerang</td><td>".colouriseMOT($mot, 'drive_gnaremoob')."</td></tr>";
echo "<tr><td>Figure of 8</td><td>".colouriseMOT($mot, 'drive_eight')."</td></tr>";
echo "<tr><td>0-60 Test</td><td>".colouriseMOT($mot, 'drive_speed')."</td></tr>";
echo "<tr><td>Emergency Stop</td><td>".colouriseMOT($mot, 'drive_estop')."</td></tr>";
echo "<tr><td>Dome Spin</td><td>".colouriseMOT($mot, 'drive_dome_spin')."</td></tr>";
echo "<tr><td>Range exceed test</td><td>".colouriseMOT($mot, 'drive_range')."</td></tr>";
echo "</table>";
echo "</div>";


echo "</div>";

echo "</div>";

include "includes/footer.php";
?>

