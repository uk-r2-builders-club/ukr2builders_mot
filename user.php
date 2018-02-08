<?

include "session.php";

if ($_SESSION['admin'] != 1) {
    header("Location:/mot");
    die();
}

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
 </head>

 <body>

<?

include "menu.php";
include "config.php";

echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['update'] != "") {
    $sql = "UPDATE user SET email=?, enabled=?, admin=? WHERE user_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $_REQUEST['email'], $_REQUEST['enabled'], $_REQUEST['admin'], $_REQUEST['user_uid']);
    if ($_SESSION['admin'] != 1) {
	    $_REQUEST['admin'] = 0;
    }
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
}

$sql = "SELECT * FROM users WHERE user_uid = ". $_REQUEST['user_uid'];
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$sql = "SELECT name FROM users WHERE user_uid = ".$user["created_by"];
$officer = $conn->query($sql)->fetch_object()->name;


echo "<div id=info>";
echo "<form>";
echo "<input type=hidden name=user_uid value=".$user[user_uid].">";
echo "<h2>". $user['name']." (".$user['username'].")</h2>";
echo "<ul>";
echo " <li>email: <input type=email size=40 name=email value=".$user['email']."></li>";
echo " <li>Enabled: <input type=checkbox name=enabled> | Admin: <input type=checkbox name=admin> </li>";
echo " <li>Created On: ".$user['create_on']."</li>";
echo " <li>Created By: ".$officer."</li>";
echo "</ul>";
echo "<input type=submit name=update value=Update>";
echo "</div>";

echo "</div>";

$conn->close();
?>


