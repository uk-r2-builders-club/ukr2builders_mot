<?

$timeout = 10 * 60; // 3 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();
if ( (isset($_SESSION['last_active']) && (time() > ($_SESSION['last_active']+$timeout)))
     || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
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

//echo basename($_SERVER['PHP_SELF']);

//if (($_SESSION['role'] == "user") && (basename($_SERVER['PHP_SELF']) != "member.php")) {
//    header("Location:member.php?member_uid=".$user);
 //   die();
//}

?>
