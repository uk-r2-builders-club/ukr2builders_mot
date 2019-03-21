<?

include "includes/header.php";

echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['update'] != "") {
    if ($_REQUEST['password1'] == $_REQUEST['password2']) {
	if ($_REQUEST['gdpr'] == "on") {
            $sql = "UPDATE members SET password=PASSWORD(?), force_password=0, gdpr_accepted=1 WHERE member_uid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $_REQUEST['password1'], $_SESSION['user']);
            $stmt->execute();
	    if ($stmt->sqlstate == "00000") {
	   	 echo "Password change successful";
	    } else {
		 echo "Password change failed";
                 printf("Error: %s.\n", $stmt->sqlstate);
                 printf("Error: %s.\n", $stmt->error);
	    }
	} else {
	    echo "You must accept the Data Protection Policy";
	}
    } else {
	echo "Passwords don't match!";
    }
}

if (isset($_REQUEST['force'])) {
	echo "<h3>You must change your password in and accept the Data Protection Policy</h3>";
}
echo "<div id=info>";
echo "<form method=post>";
echo "<ul>";
echo " <li>New Password: <input type=password name=password1 size=40></li>";
echo " <li>Repeat: <input type=password name=password2 size=40></li>";
echo " <li>I have read and understood the <a href=gdpr.php>Data Protection Policy</a> <input type=checkbox name=gdpr></li>";
echo "</ul>";
echo "<input type=submit name=update value=Update>";
echo "</div>";

echo "</div>";

$conn->close();
?>


