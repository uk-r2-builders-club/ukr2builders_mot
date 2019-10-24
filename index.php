<?

if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

$timeout = 15 * 60; // 15 minutes
$fingerprint = md5('SECRET-SALT'.$_SERVER['HTTP_USER_AGENT']);
session_start();
if ( (isset($_SESSION['last_active']) && (time() > ($_SESSION['last_active']+$timeout)))
     || (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint']!=$fingerprint)
     || isset($_GET['logout']) ) {
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
    header("Location:/");
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
	$sql="SELECT active,email,member_uid,role,force_password,gdpr_accepted,permissions FROM members WHERE (email=? OR username=?) AND password=PASSWORD(?) LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("sss", $_REQUEST['username'], $_REQUEST['username'], $_REQUEST['password']);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$_SESSION['user']=$row['member_uid'];
		$_SESSION['username']=$row['email'];
		$_SESSION['enabled']=$row['active'];
		$_SESSION['role']=$row['role'];
		$_SESSION['force_password']=$row['force_password'];
		$_SESSION['gdpr_accepted']=$row['gdpr_accepted'];
		$_SESSION['permissions'] = $row['permissions'];
		$sql="UPDATE members SET last_login=NOW(), last_login_from='".$_ENV['REMOTE_ADDR']."' WHERE member_uid=".$row['member_uid'];
		$result = $conn->query($sql);
	}
}

if ($_REQUEST['logout'] == 'yes') {
	session_destroy();
}

if (!isset($_SESSION['username'])) {
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

</div>

<form method=post>
<h1>Login:</h1>
Use email address<br />
<table border=0>
<tr><th>Email Address:</th><td><input type=text name=username size=25></td></tr>
<tr><th>Password:</th><td><input type=password name=password size=25></td></tr>
</table>
<input type=submit name=login value=Go>
<a href=\"reset.php\">Reset password</a>
<hr />
<a href=topps.php>View the Topps Droids</a> |
<a href=stats.php>Current UK Droid statistics</a>

<?

include "includes/footer.php";

} else {
	header("Location: member.php?member_uid=".$_SESSION['user']);
        exit();
}

?>


