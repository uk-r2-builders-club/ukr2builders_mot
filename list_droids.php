<?

include "session.php";

?>
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

if ($_REQUEST['member_uid'] == "" ) {
    $sql = "SELECT * FROM droids";
} else {
    $sql = "SELECT * FROM droids WHERE member_uid = ". $_REQUEST['member_uid'];
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    if ($_REQUEST['member_uid'] == "" ) {
	    echo "<tr><th>Valid MOT</th><th>Droid</th><th>Owner</th><th>Owner PLI</th><th>Type</th><th>Style</th><th>Tier Two</th><th></th></tr>";
    } else {
	    echo "<tr><th>Valid MOT</th><th>Droid</th><th>Type</th><th>Style</th><th>Tier Two</th><th></th></tr>";
    }
    while($row = $result->fetch_assoc()) {
        $sql = "SELECT * FROM mot WHERE approved='Yes' AND droid_uid = " .$row["droid_uid"]. " AND date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
	$mot_result = $conn->query($sql);
	echo "<tr>";
	if ($mot_result->num_rows > 0) {
	    echo "<td bgcolor=green>Valid</td>";
        } else { 
	    echo "<td bgcolor=red>Not Valid</td>";
	}	
	echo "<td>" . $row["name"]. "</td>";
	if ($_REQUEST['member_uid'] == "" ) {
	    $sql = "SELECT * FROM members WHERE member_uid = ".$row['member_uid'];
	    $owner = $conn->query($sql)->fetch_assoc();
	    echo "<td><a href=member.php?member_uid=" . $owner["member_uid"].">".$owner["forename"]. " " . $owner["surname"]. "</a></td>";
	    if (strtotime($owner[pli_date]) > strtotime('-1 year')) {
                echo "<td bgcolor=green>Yes</td>";
            } else {
                echo "<td bgcolor=red>No</td>";
            }
	}
	echo "<td>" . $row["type"]. "</td><td>" . $row["style"]. "</td><td align=center>". $row['tier_two']. "</td><td><a href=droid.php?droid_uid=". $row["droid_uid"]. ">View Droid</a></td>";
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No Droids";
}
$conn->close();
echo "<hr /><a href=new_droid.php?member_uid=". $_REQUEST["member_uid"]. ">Add Droid</a>"
?>


