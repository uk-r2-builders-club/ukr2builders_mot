<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
 </head>

 <body>

<?

include "includes/menu.php";
include "includes/config.php";

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
    echo "<table class=topps>";
    echo "<tr><th colspan=2>Details</th><th>Front</th><th>Back</th></tr>";
    while($row = $result->fetch_assoc()) {
	    $sql = "SELECT * FROM members WHERE member_uid = ".$row['member_uid'];
	    $owner = $conn->query($sql)->fetch_assoc();
	    echo "<tr>";
	    echo "<td>Droid Name: </td><td>".$row['name']."</td>";
	    echo "<td rowspan=3><img src=data:image/jpeg;base64,".base64_encode( $row['topps_front'] )." width=240></td>";
	    echo "<td rowspan=3><img src=data:image/jpeg;base64,".base64_encode( $row['topps_rear'] )." width=240></td>";
	    echo "</tr>";
	    echo "<tr><td>Owner: </td><td>".$owner['forename']." ".$owner['surname']."</td></tr>";
	    echo "<tr><td>Topps Number: </td><td>".$row['topps_id']."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();

?>


