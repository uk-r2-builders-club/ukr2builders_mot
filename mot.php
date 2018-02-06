<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
 </head>

 <body>

<?

include "menu.php";
include "config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM mot WHERE mot_uid = ". $_REQUEST['mot_uid'];
$result = $conn->query($sql);
$mot = $result->fetch_assoc();
$sql = "SELECT name FROM users WHERE user_uid = ".$mot["user"];
$officer = $conn->query($sql)->fetch_object()->name;


echo "<h2>". $mot['name'] ."</h2>";
echo "<ul>";
echo " <li>Date Taken: ".$mot['date']."</li>";
echo " <li>Location: ".$mot['location']."</li>";
echo " <li>Club Approval: ".$mot['approval']."</li>";
echo " <li>MOT Renewal: ".$mot['annual_mot']."</li>";
echo " <li>Pass/Fail: ".$mot['approved']."</li>";
echo " <li>MOT Officer: ".$officer."</li>";
echo "</ul>";


$conn->close();
?>


