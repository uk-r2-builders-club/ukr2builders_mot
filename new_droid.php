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

if ($_REQUEST['name'] != "") {
    $sql = "INSERT INTO droids(member_uid, name, type, style, radio_controlled, transmitter_type, material, weight, battery, drive_voltage, sound_system, value, date_added) VALUES (?,?,?,?,?,?,?,?,?,?,?,?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssisssssss", $member_uid, $name, $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['radio_controlled'], $_REQUEST['transmitter_type'],
                                $_REQUEST['material'], $_REQUEST['weight'], $_REQUEST['battery'], $_REQUEST['drive_voltage'], $_REQUEST['sound_system'], $_REQUEST['value']);
    $member_uid=$_REQUEST['member_uid'];
    $name=$_REQUEST['name'];
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

echo "<form>";
echo "<input type=hidden name=member_uid value=".$_REQUEST['member_uid'].">";
echo "<h2>Name: <input type=text name=name size=50></h2>";
echo "<ul>";
echo " <li>Type: <select name=type><option value=R2>R2</option><option value=R3>R3</option><option value=R4>R4</option><option value=R5>R5</option><option value=R6>R6</option><option value=R1>R1</option><option value=R0>R0</option><option value=BB>BB</option><option value=other>Other</option></select></li>";
echo " <li>Style: <input type=text name=style size=50></li>";
echo " <li>Radio Controlled?: <select name=radio_controlled><option value=Yes>Yes</option><option value=No>No</option></select></li>";
echo " <li>Transmitter Type: <input type=text name=transmitter_type size=50></li>";
echo " <li>Material: <input type=text name=material size=50></li>";
echo " <li>Approx Weight: <input type=text name=weight size=10></li>";
echo " <li>Battery Type: <input type=text name=battery size=10></li>";
echo " <li>Drive Voltage: <input type=text name=drive_voltage size=10></li>";
echo " <li>Sound System: <input type=text name=sound_system size=50></li>";
echo " <li>Approx Value: <input type=text name=value size=10></li>";
echo "</ul>";
echo "<input type=submit name=add value=Add>";

$conn->close();
?>


