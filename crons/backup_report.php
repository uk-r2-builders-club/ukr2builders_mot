<?

include "/home/chequersavenue/r2djp.co.uk/mot/includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT members.member_uid, droids.droid_uid, members.forename, members.surname, droids.name, droids.tier_two, members.email, members.pli_date, badge_id from droids, members WHERE members.member_uid = droids.member_uid AND members.active = 'on'";


$conn->close();
?>
