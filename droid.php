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
echo "</div>";


$sql = "SELECT * FROM droids WHERE droid_uid = ". $_REQUEST['droid_uid'];
$result = $conn->query($sql);
$droid = $result->fetch_assoc();

echo "<div id=info>";
echo "<h2>". $droid['name'] ."</h2>";
echo "<ul>";
echo " <li>Type: ".$droid['type']."</li>";
echo " <li>Style: ".$droid['style']."</li>";
echo " <li>Radio Controlled?: ".$droid['radio_controlled']."</li>";
echo " <li>Transmitter Type: ".$droid['transmitter_type']."</li>";
echo " <li>Material: ".$droid['material']."</li>";
echo " <li>Approx Weight: ".$droid['weight']."</li>";
echo " <li>Battery Type: ".$droid['battery']."</li>";
echo " <li>Drive Voltage: ".$droid['drive_voltage']."</li>";
echo " <li>Sound System: ".$droid['sound_system']."</li>";
echo " <li>Approx Value: ".$droid['value']."</li>";
echo "</ul>";
echo "</div>";

echo "<div id=images>";
echo "<img src=data:image/jpeg;base64,".base64_encode( $droid['photo_front'] )." width=240>";
echo "<img src=data:image/jpeg;base64,".base64_encode( $droid['photo_side'] )." width=240>";
echo "<img src=data:image/jpeg;base64,".base64_encode( $droid['photo_rear'] )." width=240>";
echo "</div>";


$sql = "SELECT * FROM mot WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY date";
$mot_result = $conn->query($sql);

echo "<div id=mot>";
if ($mot_result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>Date</th><th>Location</th><th>Officer</th><th>Approved?</th><th></th></tr>";
    while($row = $mot_result->fetch_assoc()) {
	$sql = "SELECT name FROM users WHERE user_uid = ".$row["user"];
	$officer = $conn->query($sql)->fetch_object()->name;
        echo "<tr><td>" . $row["date"]. "</td><td>" . $row["location"]. "</td><td>" . $officer. "</td><td>".$row["approved"]."</td><td><a href=mot.php?mot_uid=". $row["mot_uid"]. ">View MOT</a></td></tr>";
    }
    echo "</table>";
} else {
    echo "No MOT";
}
echo "</div>";

echo "</div>";

$conn->close();
?>


