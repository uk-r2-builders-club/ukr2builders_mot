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

if ($_REQUEST['email'] != "") {
    $sql = "INSERT INTO members(forename, surname, email, created_on, created_by) VALUES (?,?,?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $_REQUEST['forename'], $_REQUEST['surname'], $_REQUEST['email'], $user);
    $user = 1;
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
echo "</ul>";
echo "<input type=submit name=add value=Add>";

$conn->close();
?>


