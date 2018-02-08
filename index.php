<?

$timeout = 10 * 60; // 3 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();
if ( (isset($_SESSION['last_active']) && (time() > ($_SESSION['last_active']+$timeout)))
     || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
     || isset($_GET['logout']) ) {
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
    header("Location:/mot/");
    die();
}
session_regenerate_id();
$_SESSION['last_active'] = time();
$_SESSION['fingerprint'] = $fingerprint;

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
 </head>

 <body>

<?

include "config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['login'] == "Go") {
	$sql="SELECT enabled,user_uid,admin FROM users WHERE username='".$_REQUEST['username']."' AND password=PASSWORD('".$_REQUEST['password']."') LIMIT 1";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$_SESSION['user']=$row['user_uid'];
		$_SESSION['username']=$_REQUEST['username'];
		$_SESSION['enabled']=$row['enabled'];
		$_SESSION['admin']=$row['admin'];
	}

}

if ($_REQUEST['logout'] == yes) {
	session_destroy();
}

if (!isset($_SESSION['username'])) {
	echo "<form method=post>";
	echo "<h1>Login:</h1>";
	echo "Username: <input type=text name=username size=25><br />";
	echo "Password: <input type=password name=password size=25><br />";
	echo "<input type=submit name=login value=Go>";
	echo "<hr />";
	echo "<a href=topps.php>View the Topps Droids</a>";
} else {
	echo "<h1>Welcome ".$_SESSION['username']."</h1>";
	echo "<ul>";
	echo " <li><a href=members.php>List Members</a></li>";
	echo " <li><a href=list_droids.php>List Droids</a></li>";
	echo " <li><a href=topps.php>View the Topps Droids</a></li>";
	echo " <li><a href=users.php>List Users</a></li>";
	echo " <li><a href=password.php>Change Password</a></li>";
	echo " <li><a href=?logout=yes>Logout</a></li>";
	echo "</ul>";
}

$conn->close();
?>


