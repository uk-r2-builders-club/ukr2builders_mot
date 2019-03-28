<?

$timeout = 10 * 60; // 3 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();
if ( (isset($_SESSION['last_active']) && (time() > ($_SESSION['last_active']+$timeout)))
     || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
     || isset($_GET['logout']) ) {
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
    header("Location:".$config->site_base);
    die();
}
session_regenerate_id();
$_SESSION['last_active'] = time();
$_SESSION['fingerprint'] = $fingerprint;

// If the is no current user session and they aren't trying to reset their password
// redirect to the login page
if (!isset($_SESSION['user']) && basename($_SERVER['PHP_SELF']) != "password.php") {
        header("Location:".$config->site_base);
        die();
}

if (!isset($_SESSION['user']) && !isset($_REQUEST['reset'])) {
        header("Location:".$config->site_base);
        die();
}


// Check to see if the logged in user needs to change their password, if 
// they aren't on the password page.
if (isset($_SESSION['user']) && basename($_SERVER['PHP_SELF']) != "password.php") {
	// Create connection
        //$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
        // Check connection
        //if ($conn->connect_error) {
        //    die("Connection failed: " . $conn->connect_error);
        //}
	// Check to make sure they don't need to change their password
	//$sql = "SELECT * FROM members WHERE member_uid = ". $_SESSION['user'];
        //$result = $conn->query($sql);
        //$member = $result->fetch_assoc();
	if ($_SESSION['force_password'] != 0 || $_SESSION['gdpr_accepted'] == 0) {
		header("Location:".$config->site_base."/password.php?force");
		die();
	}
}


//echo basename($_SERVER['PHP_SELF']);

//if (($_SESSION['role'] == "user") && (basename($_SERVER['PHP_SELF']) != "member.php")) {
//    header("Location:member.php?member_uid=".$user);
 //   die();
//}

?>
