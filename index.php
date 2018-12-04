<?

if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

$timeout = 10 * 60; // 3 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();
if ( (isset($_SESSION['last_active']) && (time() > ($_SESSION['last_active']+$timeout)))
     || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
     || isset($_GET['logout']) ) {
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
    header("Location:/new_mot/");
    die();
}
session_regenerate_id();
$_SESSION['last_active'] = time();
$_SESSION['fingerprint'] = $fingerprint;

include "includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['login'] == "Go") {
	$sql="SELECT active,email,member_uid,role,admin FROM members WHERE (email='".$_REQUEST['username']."' OR username='".$_REQUEST['username']."') AND password=PASSWORD('".$_REQUEST['password']."') LIMIT 1";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$_SESSION['user']=$row['member_uid'];
		$_SESSION['username']=$row['email'];
		$_SESSION['enabled']=$row['active'];
		$_SESSION['admin']=$row['admin'];
		$_SESSION['role']=$row['role'];
		$sql="UPDATE members SET last_login=NOW(), last_login_from='".$_ENV['REMOTE_ADDR']."' WHERE member_uid=".$row['member_uid'];
		$result = $conn->query($sql);
	}
}

if ($_REQUEST['logout'] == yes) {
	session_destroy();
}

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
 </head>

 <body>

<div name=menu>
 <h2 id=banner><a id=logo href="http://astromech.info"></a></h2>
<?

if (isset($_SESSION['username'])) {
   echo "Logged in as ".$_SESSION['username'];
}

echo "</div>";

if (!isset($_SESSION['username'])) {
	echo "<form method=post>";
	echo "<h1>Login:</h1>";
	echo "Use either username or email address<br />";
	echo "Username: <input type=text name=username size=25><br />";
	echo "Password: <input type=password name=password size=25><br />";
	echo "<input type=submit name=login value=Go>";
	echo "<hr />";
	echo "<a href=topps.php>View the Topps Droids</a> | ";
	echo "<a href=stats.php>Current UK Droid statistics</a>";
} else {
	echo "<ul>";
	echo " <li><a href='member.php?member_uid=".$_SESSION['user']."'>Your Profile</a></li>";
	if ($_SESSION['role'] != 'user') {
	    echo " <li><a href=members.php>List Members</a></li>";
	    echo " <li><a href=list_droids.php>List Droids</a></li>";
	}
	if ($_SESSION['role'] == 'admin') {
		echo " <li><a href=map.php>Members Map</a></li>";
		echo " <li><a href=edit_config.php>Edit config</a></li>";
		echo " <li><a href=edit_pli.php>Edit PLI Details</a></li>";
		echo " <li><a href=achievements.php>Edit Achievements</a></li>";
	}
	echo " <li><a href=password.php>Change Password</a></li>";
	echo " <li><a href=topps.php>View the Topps Droids</a></li>";
	echo " <li><a href=stats.php>Current UK Droid statistics</a></li>";
	echo " <li><a href=?logout=yes>Logout</a></li>";
	echo "</ul>";
}

$conn->close();
?>


