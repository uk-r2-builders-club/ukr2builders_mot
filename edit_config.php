<?

include "includes/header.php";

if (!($_SESSION['permissions'] & $perms['EDIT_CONFIG'])) {
	echo "<h1>Permission Denied</h1>";
} else {

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


if ($_REQUEST['update'] != "") {
	$sql = "SELECT name FROM site_config";
	$result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
		if ($row['name'] != "site_options") {
		    $sql = "UPDATE site_config SET value=? WHERE name=?";
		    $stmt = $conn->prepare($sql);
		    $stmt->bind_param("ss", $_REQUEST[$row['name']], $row['name']); 
		    $stmt->execute();
		} else {
   	            $new_options = 0;
                    for ($i=0 ; $i < $_REQUEST['num_options'] ; $i++ ) {
                        $option = strval(2**$i);
                        if ($_REQUEST[$option] == "on") {
                            $new_options = $new_options + 2**$i;
                        }
                    }
		    $sql = "UPDATE site_config SET value=? WHERE name=\"site_options\"";
		    $stmt = $conn->prepare($sql);
		    $stmt->bind_param("s", $new_options);
		    $stmt->execute();
		}
	}

        # Reread new config values into $config
        $sql = "SELECT * FROM site_config";
        $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
            $lines[$row['name']] = $row['value'];
        }
        $config = (object) $lines;

}

echo "<div id=main>";


echo "<h2>Config</h2>";
echo "<form>";
echo "<table class=config>";
$sql = "SELECT * FROM site_config";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
	if ($row['name'] != "site_options") {
	    echo "<tr>";
	    echo "<td>".$row['description']."</td>";
	    echo "<td><input type=text size=50 name=".$row['name']." value=\"".$row['value']."\"></td>";
	    echo "</tr>";
	}
}
echo "<tr><th>Site Options:</th>";
echo "<td>";
echo "<table><tr>";
$sql = "SELECT * FROM site_options";
$options = $conn->query($sql);
while($row = $options->fetch_assoc()) {
    echo "<th class=rotate_permission><div><span>".$row['name']."</span></div></th>";
    $num_options++;
}
echo "</tr><tr>";
for ($i=0 ; $i < $num_options ; $i++) {
    $option = 2**$i;
    echo "<td class=permission><input type=checkbox name=$option ";
    echo ($config->site_options & $option) ? "checked" : "";
    echo "></td>";
}
echo "</tr></table>";
echo "<input type=hidden name=num_options value=".$num_options.">";
echo "</td></tr>";

echo "</table>";
echo "<input type=submit name=update value=Update>";
echo "</form>";

}
echo "</div>";
include "includes/footer.php";
?>

