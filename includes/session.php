<?

// Start the session with a timeout of 10 minutes
$timeout = 10 * 60; // 10 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();

// If the last_active time is greater than the timeout, or there is a fingerprint mismatch
// then destroy the session and force the user back to the login screen
if ( (isset($_SESSION['last_active']) && (time() > ($_SESSION['last_active']+$timeout)))
     || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
     || isset($_GET['logout']) ) {
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
    header("Location:".$config->site_base);
    die();
}

// On each page load, refresh the last_active time and fingerprint
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
	if ($_SESSION['force_password'] != 0 || $_SESSION['gdpr_accepted'] == 0) {
		header("Location:".$config->site_base."/password.php?force");
		die();
	}
}


?>
