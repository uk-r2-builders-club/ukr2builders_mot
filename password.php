<?

include "includes/header.php";

echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$allow_reset = 0;
$member_reset = 0;

if (isset($_SESSION['user'])) {
	$allow_reset = 1;
	$member_reset = $_SESSION['user'];
} elseif (isset($_REQUEST['reset'])) {
	echo "<h3>Password Reset</h3>";
        $reset_code = $_REQUEST['reset'];
        $sql = "SELECT * FROM password_reset WHERE hash = '".$reset_code."'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
                // Code accepted
		$reset_row = $result->fetch_assoc();
		$expire = strtotime($reset_row['expire']);
		$member_reset = $reset_row['member_uid'];
	        $now = time();
	        if ($now > $expire) {
		      echo "Request has expired. Please submit again";
		      $sql = "DELETE FROM password_reset WHERE member_uid = ?";
                      $stmt = $conn->prepare($sql);
                      $stmt->bind_param("i", $member_reset);
                      $stmt->execute();
	        } else {
		      echo "Resetting password<br/>";
		      $allow_reset = 1;
	        }
        } else {
                echo "That reset code is not recognised";
	}
}


if ($_REQUEST['update'] != "" && $allow_reset == 1) {
    if ($_REQUEST['password1'] == $_REQUEST['password2']) {
	if ($_REQUEST['gdpr'] == "on") {
            $sql = "UPDATE members SET password=PASSWORD(?), force_password=0, gdpr_accepted=1 WHERE member_uid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $_REQUEST['password1'], $member_reset);
            $stmt->execute();
	    if ($stmt->sqlstate == "00000") {
	   	 echo "Password change successful";
	    } else {
		 echo "Password change failed";
                 printf("Error: %s.\n", $stmt->sqlstate);
                 printf("Error: %s.\n", $stmt->error);
	    }
	    $sql = "DELETE FROM password_reset WHERE member_uid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $member_reset);
            $stmt->execute();
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
echo "<table border=0>";
echo " <tr><th>New Password:</th><td><input type=password name=password1 size=40></td></tr>";
echo " <tr><th>Repeat:</th><td><input type=password name=password2 size=40></td></tr>";
echo " <tr><th colspan=2>I have read and understood the <a href=gdpr.php>Data Protection Policy</a> <input type=checkbox name=gdpr></th></tr>";
echo "</table>";
echo " <input type=hidden name=reset value=".$_REQUEST['reset'].">";
echo "<input type=submit name=update value=Update>";
echo "</form>";
echo "</div>";

echo "</div>";

$conn->close();
?>


