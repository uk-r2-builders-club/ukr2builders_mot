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

$sql = "SELECT * from droids WHERE topps_id != 0 ORDER BY topps_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>Name</th><th>Owner</th><th>Topps Number</th><th>Front</th><th>Back</th></tr>";
    while($row = $result->fetch_assoc()) {
	    $sql = "SELECT * FROM members WHERE member_uid = ".$row['member_uid'];
	    $owner = $conn->query($sql)->fetch_assoc();
	    echo "<tr>";
	    echo "<td>".$row['name']."</td>";
	    echo "<td>".$owner['forename']." ".$owner['surname']."</td>";
	    echo "<td>".$row['topps_id']."</td>";
	    echo "<td>Picture to come</td>";
	    echo "<td>Picture to come</td>";
	    echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();

?>


