<?

include "includes/header.php";

if($_SESSION['role'] == "user") {
	die();
}

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['email'] != "") {
    $sql = "INSERT INTO members(forename, surname, email, county, postcode, created_on, created_by, username) VALUES (?,?,?,?,? NOW(), ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssis", $_REQUEST['forename'], $_REQUEST['surname'], $_REQUEST['email'], $_REQUEST['county'], $_REQUEST['postcode'], $_SESSION['user'], $_REQUEST['username']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
    $stmt->close();
}

echo "<form>";
echo "<ul>";
echo " <li>Forename: <input type=text name=forename size=50></li>";
echo " <li>Surname: <input type=text name=surname size=50></li>";
echo " <li>Email: <input type=email name=email size=50></li>";
echo " <li>County: <input type=text name=county size=50></li>";
echo " <li>Postcode: <input type=text name=postcode size=10></li>";
echo " <li>Forum Username: <input type=text name=username size=50></li>";
echo "</ul>";
echo "<input type=submit name=add value=Add>";

$conn->close();
?>


