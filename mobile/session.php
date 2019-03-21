<?

$timeout = 10 * 60; // 3 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();
if ( 
      (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
     || isset($_GET['logout']) ) {
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
}
session_regenerate_id();
$_SESSION['last_active'] = time();
$_SESSION['fingerprint'] = $fingerprint;

if (!isset($_SESSION['user'])) {
    header("Location:https://r2djp.co.uk/new_mot/");
    die();
}

if (isset($_SESSION['user']) && basename($_SERVER['PHP_SELF']) != "password.php") {
	// Create connection
        $conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
	// Check to make sure they don't need to change their password
	$sql = "SELECT * FROM members WHERE member_uid = ". $_SESSION['user'];
        $result = $conn->query($sql);
        $member = $result->fetch_assoc();
	if ($member['force_password'] != 0 || $member['gdpr_accepted'] == 0) {
		header("Location:https://r2djp.co.uk/new_mot/password.php?force");
		die();
	}


}


//echo basename($_SERVER['PHP_SELF']);

//if (($_SESSION['role'] == "user") && (basename($_SERVER['PHP_SELF']) != "member.php")) {
//    header("Location:member.php?member_uid=".$user);
 //   die();
//}

?>
