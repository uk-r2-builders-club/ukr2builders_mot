<? 

include "includes/header.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM droids WHERE public = 'Yes' AND active = 'on' AND droid_uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_REQUEST['droid_uid']);
$stmt->execute();
$droid = $stmt->get_result()->fetch_assoc();


echo "<div class=\"grid-container bg\">";
echo "<div class=\"Main-Info\">";
echo "<h1 class=\"droid_details\">".$droid['name']."</h1>";
if ($droid['back_story'] != "" ) {
	echo "<hr>";
	echo "<h2 class=\"droid_details\">Story</h2>";
	echo "<p>".nl2br($droid['back_story'])."</p>";
}

echo "<hr>";
echo "<h2 class=\"droid_details\">Build Details</h2>";
echo "<table class=\"droid_details\">";
echo "<tr><th>Type</th><td>".$droid['type']."</td></tr>";
echo "<tr><th>Transmitter</th><td>".$droid['transmitter_type']."</td></tr>";
echo "<tr><th>Material</th><td>".$droid['material']."</td></tr>";
echo "<tr><th>Weight</th><td>".$droid['weight']."</td></tr>";
echo "<tr><th>Battery Type</th><td>".$droid['battery']."</td></tr>";
echo "<tr><th>Drive Voltage</th><td>".$droid['drive_voltage']."</td></tr>";
echo "<tr><th>Drive Type</th><td>".$droid['drive_type']."</td></tr>";
echo "<tr><th>Sound System</th><td>".$droid['sound_system']."</td></tr>";
echo "</table>";

if ($droid['notes'] != "" ) {
	echo "<hr>";
        echo "<h2 class=\"droid_details\">Builder Notes</h2>";
        echo "<p>".nl2br($droid['notes'])."</p>";
}

echo "</div>";

/* Photos */
echo "<div class=\"Front-Photo\">";
echo "<img id=photo_front src=\"showImage.php?droid_uid=".$droid['droid_uid']."&name=photo_front&width=480\">";
echo "</div>";
echo "<div class=\"Rear-Photo\">";
echo "<img id=photo_front src=\"showImage.php?droid_uid=".$droid['droid_uid']."&name=photo_rear&width=240\">";
echo "</div>";
echo "<div class=\"Side-Photo\">";
echo "<img id=photo_front src=\"showImage.php?droid_uid=".$droid['droid_uid']."&name=photo_side&width=240\">";
echo "</div>";
/* End of Photos */

echo "</div>";


