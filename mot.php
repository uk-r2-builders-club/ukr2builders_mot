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

if ($_REQUEST['new_comment'] != "") {
    $sql = "INSERT INTO mot_comments(mot_uid, comment, added_by) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['mot_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

# Comments

$sql = "SELECT * FROM mot_comments WHERE mot_uid = " .$_REQUEST["mot_uid"] ." ORDER BY added_on";
$comments_result = $conn->query($sql);

echo "<div id=comments>";
if ($comments_result->num_rows > 0) {
    // output data of each row
    echo "<div id=comment>";
    while($row = $comments_result->fetch_assoc()) {
        $sql = "SELECT name FROM users WHERE user_uid = ".$row["added_by"];
        $officer = $conn->query($sql)->fetch_object()->name;
        echo "<div id=officer>$officer</div>";
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
echo "<input type=hidden name=officer value=0><br />";
echo "<input type=submit value=Add>";
echo "</div>";



$sql = "SELECT * FROM mot WHERE mot_uid = ". $_REQUEST['mot_uid'];
$result = $conn->query($sql);
$mot = $result->fetch_assoc();
$sql = "SELECT name FROM users WHERE user_uid = ".$mot["user"];
$officer = $conn->query($sql)->fetch_object()->name;


echo "<div id=info>";
echo "<ul>";
echo " <li>Date Taken: ".$mot['date']."</li>";
echo " <li>Location: ".$mot['location']."</li>";
echo " <li>Club Approval: ".$mot['approval']."</li>";
echo " <li>MOT Renewal: ".$mot['annual_mot']."</li>";
echo " <li>Pass/Fail: ".$mot['approved']."</li>";
echo " <li>MOT Officer: ".$officer."</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<div id=mot_test>";
echo "<div id=mot_block>";
echo "<h3>Structural</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Overall Structural Worry?</td><td>".$mot['struct_overall']."</td></tr>";
echo "<tr><td>Left Leg</td><td>".$mot['struct_left_leg']."</td></tr>";
echo "<tr><td>Left Foot/Ankle Joint</td><td>".$mot['struct_left_foot_ankle']."</td></tr>";
echo "<tr><td>Left Shoulder Joint</td><td>".$mot['struct_left_shoulder']."</td></tr>";
echo "<tr><td>Right Leg</td><td>".$mot['struct_right_leg']."</td></tr>";
echo "<tr><td>Right Foot/Ankle Joint</td><td>".$mot['struct_right_foot_ankle']."</td></tr>";
echo "<tr><td>Right Shoulder Joint</td><td>".$mot['struct_right_shoulder']."</td></tr>";
echo "<tr><td>Center Foot/Ankle Joint</td><td>".$mot['struct_center_foot']."</td></tr>";
echo "<tr><td>Center Leg to Body Joint</td><td>".$mot['struct_center_ankle']."</td></tr>";
echo "<tr><td>Body/Frame/Skirt</td><td>".$mot['struct_body_skirt_frame']."</td></tr>";
echo "<tr><td>Dome Rotation Mechanism</td><td>".$mot['struct_dome_mech']."</td></tr>";
echo "<tr><td>Dome</td><td>".$mot['struct_dome']."</td></tr>";
echo "<tr><td>Details</td><td>".$mot['struct_details']."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Mechanical</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Center Wheel Setup</td><td>".$mot['mech_center_wheel']."</td></tr>";
echo "<tr><td>Drive Setup</td><td>".$mot['mech_drive']."</td></tr>";
echo "<tr><td>2-3-2? - Discuss</td><td>".$mot['mech_two_three_two']."</td></tr>";
echo "<tr><td>Dome Spin</td><td>".$mot['mech_dome']."</td></tr>";
echo "<tr><td>Utility Arms</td><td>".$mot['mech_utility_arms']."</td></tr>";
echo "<tr><td>Rear Door/Skins Access</td><td>".$mot['mech_rear_door_skins']."</td></tr>";
echo "<tr><td>Doors</td><td>".$mot['mech_doors']."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Electrical</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Overall Setup</td><td>".$mot['elec_overall']."</td></tr>";
echo "<tr><td>Control System Transmitter</td><td>".$mot['elec_transmitter']."</td></tr>";
echo "<tr><td>Control System Receiver</td><td>".$mot['elec_receiver']."</td></tr>";
echo "<tr><td>Feet Speed Controller and motors</td><td>".$mot['elec_feet']."</td></tr>";
echo "<tr><td>Dome Speed Controller and motor</td><td>".$mot['elec_dome']."</td></tr>";
echo "<tr><td>Audio</td><td>".$mot['elec_audio']."</td></tr>";
echo "<tr><td>Other Electronics</td><td>".$mot['elec_other']."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Gadgets</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Serious Safety Concern</td><td>".$mot['gadget_danger']."</td></tr>";
echo "<tr><td>Gadget 1</td><td>".$mot['gadget_1']."</td></tr>";
echo "<tr><td>Gadget 2</td><td>".$mot['gadget_2']."</td></tr>";
echo "<tr><td>Gadget 3</td><td>".$mot['gadget_3']."</td></tr>";
echo "<tr><td>Gadget 4</td><td>".$mot['gadget_4']."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=mot_block>";
echo "<h3>Basic Control</h3>";
echo "<table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
echo "<tr><td>Any Driving Issues</td><td>".$mot['drive_general']."</td></tr>";
echo "<tr><td>Dizzy</td><td>".$mot['drive_dizzy']."</td></tr>";
echo "<tr><td>Boomerang</td><td>".$mot['drive_boomerang']."</td></tr>";
echo "<tr><td>Reverse Boomerang</td><td>".$mot['drive_gnaremoob']."</td></tr>";
echo "<tr><td>Figure of 8</td><td>".$mot['drive_eight']."</td></tr>";
echo "<tr><td>0-60 Test</td><td>".$mot['drive_speed']."</td></tr>";
echo "<tr><td>Emergency Stop</td><td>".$mot['drive_estop']."</td></tr>";
echo "<tr><td>Dome Spin</td><td>".$mot['drive_dome_spin']."</td></tr>";
echo "<tr><td>Range exceed test</td><td>".$mot['drive_range']."</td></tr>";
echo "</table>";
echo "</div>";


echo "</div>";

$conn->close();
?>
</div>

