<?

include "session.php";

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
    if ($_REQUEST['password1'] == $_REQUEST['password2']) {
        $sql = "UPDATE users SET password=PASSWORD(?) WHERE user_uid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $_REQUEST['password1'], $_SESSION['user']);
        $stmt->execute();
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    } else {
	echo "Passwords don't match!";
    }
}

echo "<div id=info>";
echo "<form method=post>";
echo "<ul>";
echo " <li>New Password: <input type=password name=password1 size=40></li>";
echo " <li>Repeat: <input type=password name=password2 size=40></li>";
echo "</ul>";
echo "<input type=submit name=update value=Update>";
echo "</div>";

echo "</div>";

$conn->close();
?>


