<?

include "includes/config.php";

if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

if (isset($_SESSION['user']))  {
    header("Location:".$config->site_base."/password.php");
    die();
}

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
  <link rel="stylesheet" href="id.css">
 </head>

 <body>

<div id=id_badge>
<?

if (isset($_REQUEST['id'])) {
   $sql = "SELECT forename, surname, member_uid, pli_date FROM members WHERE badge_id=\"".$_REQUEST['id']."\"";
   $member = $conn->query($sql)->fetch_assoc();
   if($member) { 
       $imageData = base64_encode(file_get_contents("uploads/members/".$member['member_uid']."/mug_shot.jpg"));
       echo "<div class=\"id_badge_photo\"><img id=mug_shot width=240 src=data:image/jpeg;base64,$imageData>";
       echo "</div>";
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

