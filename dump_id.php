<?

include "includes/config.php";
include "includes/session.php";


if (!($_SESSION['permissions'] & $perms['DUMP_DATA'])) {
	echo "<h1>Permission Denied</h1>";
} else {

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = 'SELECT droids.name, droids.droid_uid, 
        CONCAT(members.forename, " ", members.surname) AS member_name, members.member_uid, members.badge_id 
        FROM droids, members where droids.member_uid = members.member_uid 
        ORDER BY members.surname;';

$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
	echo $row['name'];
	echo ",";
	echo $row['droid_uid'];
	echo ",";
	echo $row['member_name'];
        echo ",";
        echo $row['member_uid'];
	echo ",";
	echo $row['badge_id'];
	echo "\n";
}

} 
?>

