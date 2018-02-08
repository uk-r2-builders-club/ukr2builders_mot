<?

include "session.php";

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
 </head>

 <body>

<?

include "menu.php";
include "config.php";

echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['new_comment'] != "") {
    $sql = "INSERT INTO droid_comments(droid_uid, comment, added_by) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['droid_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

if ($_REQUEST['update'] != "") {
    # $sql = "INSERT INTO droid_comments(droid_uid, comment, added_by) VALUES (?,?,?)";
    $sql = "UPDATE WHERE droid_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['droid_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}


# Comments

$sql = "SELECT * FROM droid_comments WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY added_on";
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
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo "<input type=hidden name=officer value=".$_SESSION['user']."><br />";
echo "<input type=submit value=Add>";
echo "</div>";


$sql = "SELECT * FROM droids WHERE droid_uid = ". $_REQUEST['droid_uid'];
$result = $conn->query($sql);
$droid = $result->fetch_assoc();

echo "<div id=info>";
echo "<form>";
echo "<h2>". $droid['name'] ."</h2>";
echo "<ul>";
echo " <li>Type: ".$droid['type']."</li>";
echo " <li>Style: <input type=text name=style size=50 value=\"".$droid['style']."\"></li>";
echo " <li>Radio Controlled?: ".$droid['radio_controlled']."</li>";
echo " <li>Transmitter Type: <input type=text name=transmitter_type size=50 value=\"".$droid['transmitter_type']."\"></li>";
echo " <li>Material: <input type=text name=material size=50 value=\"".$droid['material']."\"></li>";
echo " <li>Approx Weight: <input type=text name=weight size=20 value=\"".$droid['weight']."\"></li>";
echo " <li>Battery Type: <input type=text name=battery size=20 value=\"".$droid['battery']."\"></li>";
echo " <li>Drive Voltage: <input type=text name=drive_voltage size=5 value=\"".$droid['drive_voltage']."\"></li>";
echo " <li>Sound System: <input type=text name=sound_system size=50 value=\"".$droid['sound_system']."\"></li>";
echo " <li>Approx Value: <input type=text name=value size=10 value=\"".$droid['value']."\"></li>";
echo " <li>Tier 2 Approved: ".$droid['tier_two']."</li>";
echo " <li>Topps Number: <input type=text name=topps_id size=5 value=\"".$droid['topps_id']."\"></li>";
echo "</ul>";
echo "<input type=submit value=Update name=update>";
echo "</div>";

echo "<div id=images>";
echo "<img src=data:image/jpeg;base64,".base64_encode( $droid['photo_front'] )." width=240>";
echo "<img src=data:image/jpeg;base64,".base64_encode( $droid['photo_side'] )." width=240>";
echo "<img src=data:image/jpeg;base64,".base64_encode( $droid['photo_rear'] )." width=240>";
echo "</div>";


$sql = "SELECT * FROM mot WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY date DESC";
$mot_result = $conn->query($sql);

echo "<div id=mot>";
if ($mot_result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>Date</th><th>Location</th><th>Officer</th><th>Approved</th><th>Valid</th><th></th></tr>";
    while($row = $mot_result->fetch_assoc()) {
	$sql = "SELECT name FROM users WHERE user_uid = ".$row["user"];
	$officer = $conn->query($sql)->fetch_object()->name;
	if ((strtotime($row['date']) > time()-28930000) && ($row['approved'] == "Yes")) {
            echo "<tr bgcolor=green>";
        } else {
            echo "<tr bgcolor=red>";
        }
	echo "<td>" . $row["date"]. "</td><td>" . $row["location"]. "</td><td>" . $officer. "</td><td>".$row["approved"]."</td><td><a href=mot.php?mot_uid=". $row["mot_uid"]. ">View MOT</a></td>";
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No MOT";
}
echo "<a href=new_mot.php?droid_uid=".$_REQUEST['droid_uid'].">Add new MOT</a>";

echo "</div>";

echo "</div>";

$conn->close();
?>


