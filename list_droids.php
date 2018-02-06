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

$sql = "SELECT * FROM droids WHERE member_uid = ". $_REQUEST['member_uid'];
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>Droid</th><th>Type</th><th>Style</th><th>MOT Entries</th><th></th></tr>";
    while($row = $result->fetch_assoc()) {
        $sql = "SELECT * FROM mot WHERE droid_uid = " .$row["droid_uid"];
	$mot_count = $conn->query($sql);
        echo "<tr><td>" . $row["name"]. "</td><td>" . $row["type"]. "</td><td>" . $row["style"]. "</td><td>". $mot_count->num_rows. "</td><td><a href=droid.php?droid_uid=". $row["droid_uid"]. ">View Droid</a> | <a href=new_droid.php?member_uid=". $_REQUEST["member_uid"]. ">Add Droid</a></td></tr>";
    }
    echo "</table>";
} else {
    echo "No Droids";
}
$conn->close();
?>


