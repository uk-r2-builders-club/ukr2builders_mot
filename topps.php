<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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
    echo "<div class=topps>";
    while($row = $result->fetch_assoc()) {
	    $sql = "SELECT * FROM members WHERE member_uid = ".$row['member_uid'];
	    $owner = $conn->query($sql)->fetch_assoc();
	    echo "<div class=flip-container><div class=flipper>";
            echo "<div class=front><img src=data:image/jpeg;base64,".base64_encode( $row['topps_front'] )." width=240></div>";
	    echo "<div class=back><img src=data:image/jpeg;base64,".base64_encode( $row['topps_rear'] )." width=240></div>";
	    echo "</div></div>";
    }
    echo "</div>";
} else {
    echo "0 results";
}
$conn->close();

?>


