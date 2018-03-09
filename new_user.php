<?

include "includes/session.php";

if ($_SESSION['admin'] != 1) {
    header("Location:/mot");
    die();
}

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
 </head>

 <body>

<?

include "includes/menu.php";
include "includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

if ($_REQUEST['email'] != "") {
    $sql = "INSERT INTO users(username, name, email, password, created_on, created_by, enabled, admin) VALUES (?,?,?,PASSWORD(?), NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $_REQUEST['username'], $_REQUEST['name'], $_REQUEST['email'], $password, $_SESSION['user'], $enabled, $admin);
    $password = randomPassword();
    if ($_REQUEST['enabled'] == "on" ) {
	    $enabled = 1;
    } else {
	    $enabled = 0;
    }
    if ($_REQUEST['admin'] == "on" ) {
            $admin = 1;
    } else {
            $admin = 0;
    }
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
    if ($stmt->error == "") {
	    $subject = "UK R2 Builders MOT - User Creation";
	    $message = "A new user has been created\r\n";
	    $message .= "\r\n";
	    $message .= "Username: ".$_REQUEST['username']."\r\n";
	    $message .= "Password: ".$password."\r\n";
	    $message .= "\r\n";
	    $message .= "Please log in and change at the earliest opportunity.\r\n";
	    $headers = "From: webmaster@r2djp.co.uk"."\r\n"."X-Mailer: PHP/".phpversion();
	    mail($_REQUEST['email'], $subject, $message, $headers);

    }
    $stmt->close();
}

echo "<form>";
echo "<ul>";
echo " <li>Username: <input type=text name=username size=50></li>";
echo " <li>Name: <input type=text name=name size=50></li>";
echo " <li>Email: <input type=email name=email size=50></li>";
echo " <li>Enabled: <input type=checkbox name=enabled checked></li>";
echo " <li>Admin: <input type=checkbox name=admin>";
echo "</ul>";
echo "<input type=submit name=add value=Add>";

$conn->close();
?>


