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

<div id=id_badge>
<?

if (isset($_REQUEST['id'])) {
   $sql = "SELECT member_uid, mug_shot, pli_date FROM members WHERE badge_id=\"".$_REQUEST['id']."\"";
   $member = $conn->query($sql)->fetch_assoc();
   if($member) { 
       if ($member['mug_shot'] != "") {
           echo "<div class=\"id_badge_photo\"><img id=mug_shot src=data:image/jpeg;base64,".base64_encode( $member['mug_shot'] )." width=240>";
           echo "</div>";
       }
       echo "PLI Status: ";
       if ( strtotime($member['pli_date'])+31556952 > strtotime('now') ) {
	       echo "<font color=green>ACTIVE</font>";
       } else {
	       echo "<font color=red>INACTIVE</font>";
       }
   } else {
	   echo "No such ID";
   }

}

$conn->close();
?>
</div>

