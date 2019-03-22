<?

if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

if (isset($_SESSION['user']))  {
    header("Location:https://r2djp.co.uk/new_mot/password.php");
    die();
}

include "includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
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

if (isset($_REQUEST['mail'])) {
   $sql = "SELECT member_uid FROM members WHERE email=\"".$_REQUEST['mail']."\"";
   $member_uid = $conn->query($sql)->fetch_object()->member_uid;
   if ($member_uid != "" ) {
      $sql = "SELECT * FROM password_reset WHERE member_uid=$member_uid";
      $resets = $conn->query($sql);
      if ($resets->num_rows == 0) {
	      // No existing password resets....
   	      $hash = generateRandomString(64);
	      $sql = "INSERT INTO password_reset(member_uid, hash, expire) VALUES (?,?, DATE_ADD(NOW(), INTERVAL 2 HOUR))"; 
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("is", $member_uid, $hash);
	      $stmt->execute();
	      if ($stmt->sqlstate == "00000") {
		      // Send email
		      $to = $_REQUEST['mail'];
		      $subject = "UK R2 Builders MOT - Password Reset";
		      $message .= "A password reset has been requested for the user associated with this email. If you have requested this,";
		      $message .= "please follow the link below.";
                      $message .= "\r\n";
		      $message .= "If you have not requested this, please do NOT follow the link, and let a club admin know";
		      $message .= "\r\n";
                      $message .= $config->site_base."/password.php?reset=".$hash."\r\n";
                      $headers = "From: R2 Builders MOT <mot@r2djp.co.uk>"."\r\n"."X-Mailer: PHP/".phpversion();
                      mail($to, $subject, $message, $headers);
		      echo "Mail sent with reset instructions";
	      }
      } else {
	      echo "Reset request already exists<br/>";
	      $expire = strtotime($resets->fetch_object()->expire);
	      $now = time();
	      if ($now > $expire) {
		      echo "Request has expired, removing. Please submit again";
		      $sql = "DELETE FROM password_reset WHERE member_uid = ?";
                      $stmt = $conn->prepare($sql);
                      $stmt->bind_param("i", $member_uid);
                      $stmt->execute();
	      } else {
		      echo "Please check your email for your password request<br/>";
	      }

      }
   } else {
	   echo "Email address not known";
   }
	   


} else {

   echo "Please enter your email address";
   echo "<form>";
   echo "<input type=text name=mail size=30>";
   echo "<input type=Submit name=Reset>";
   echo "</form>";
   echo "An email will be sent with a link to click and reset your password. Token will expire in 2 hours";
}

$conn->close();
?>


