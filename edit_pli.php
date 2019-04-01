<?

include "includes/header.php";

if ($_SESSION['role'] != "admin") {
	echo "<h1>Permission Denied</h1>";
} else {

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


$sql = "SELECT * FROM pli_cover_details";
$pli = $conn->query($sql)->fetch_object();

if ($_REQUEST['update'] != "") {
    $sql = "UPDATE pli_cover_details SET details=?, body=?, contact1=?, contact2=?, footer_text=?, header_text=? WHERE uid = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $_REQUEST['details'], $_REQUEST['body'], $_REQUEST['contact1'], $_REQUEST['contact2'], $_REQUEST['footer_text'], $_REQUEST['header_text']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
    $sql = "SELECT * FROM pli_cover_details";
    $pli = $conn->query($sql)->fetch_object();


}

echo "<div class=main>";


echo "<h2>PLI Cover Details</h2>";
echo "<form>";
echo "<table class=config>";
echo "<tr><td>Header Text</td><td><input type=text size=50 name=header_text value=\"".$pli->header_text."\"></td></tr>";
echo "<tr><td>Footer Text</td><td><input type=text size=50 name=footer_text value=\"".$pli->footer_text."\"></td></tr>";
echo "<tr><td>Contact 1</td><td><input type=text size=50 name=contact1 value=\"".$pli->contact1."\"></td></tr>";
echo "<tr><td>Contact 2</td><td><input type=text size=50 name=contact2 value=\"".$pli->contact2."\"></td></tr>";
echo "<tr><td>Details</td><td><textarea cols=50 rows=10 name=details>".$pli->details."</textarea></td></tr>";
echo "<tr><td>Body</td><td><textarea cols=50 rows=20 name=body>".$pli->body."</textarea></td></tr>";
echo "</table>";
echo "<input type=submit name=update value=Update>";
echo "</form>";

}
?>
<hr>
<h3>Instructions</h3>
This page will update the text on the PLI cover PDF that is available for members to download. The Details and Body sections will have the following text replaced:
<ul>
 <li>%member% - This will be replaced with the members full name</li>
 <li>%email% - This will be replaced with the members email address</li>
 <li>%pli_start% - This will be replaced with the PLI start date</li>
 <li>%pli_expire% - This will be replaced with the PLI expiry date (1 year from start date)</li>
 <li>%contact1% - This will be replaced with the text in the Contact 1 box</li>
 <li>%contact2% - This will be replaced with the text in the Contact 2 box</li>
</ul>


<?

include "includes/footer.php";

?>

