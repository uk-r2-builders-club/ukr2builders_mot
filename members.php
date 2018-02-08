<?

include "session.php";

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
 </head>

 <body>

<?

include "config.php";

include "menu.php";
// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM members";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>email</th><th>PLI</th><th></th></tr>";
    while($row = $result->fetch_assoc()) {
        $sql = "SELECT * FROM droids WHERE member_uid = " .$row["member_uid"];
	$droid_count = $conn->query($sql);
	echo "<tr><td>" . $row["member_uid"]. "</td><td><a href=member.php?member_uid=" . $row["member_uid"].">".$row["forename"]. " " . $row["surname"]. "</a></td><td>" . $row["email"]. "</td>";
	if (strtotime($row[pli_date]) > strtotime('-11 months')) {
	    echo "<td bgcolor=green>".$row[pli_date]."</td>";
	} elseif ((strtotime($row[pli_date]) < strtotime('-11 months')) && (strtotime($row[pli_date]) > strtotime('-1 year'))) {
	    echo "<td bgcolor=orange>".$row[pli_date]."</td>";
	} else {
	    echo "<td bgcolor=red>".$row[pli_date]."</td>";
	}
	echo "<td><a href=list_droids.php?member_uid=" .$row["member_uid"]. ">" . $droid_count->num_rows. " Droids</a></td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();
?>
<a href=new_member.php>Add new member</a>
<br />
<a href=list_droids.php>List all droids</a>


