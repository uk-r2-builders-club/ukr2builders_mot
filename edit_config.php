<?

include "includes/header.php";

if ($_SESSION['admin'] != 1) {
	echo "<h1>Permission Denied</h1>";
} else {

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


if ($_REQUEST['update'] != "") {
    $sql = "UPDATE config SET email_treasurer=?, email_mot=?, site_base=?, paypal_link=?, paypal_email=?, primary_cost=?, other_cost=? WHERE config_uid = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $_REQUEST['email_treasurer'], $_REQUEST['email_mot'], $_REQUEST['site_base'], $_REQUEST['paypal_link'], $_REQUEST['paypal_email'], $_REQUEST['primary_cost'], $_REQUEST['other_cost']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
    $sql = "SELECT * FROM config";
    $config = $conn->query($sql)->fetch_object();


}

echo "<div class=main>";


echo "<h2>Config</h2>";
echo "<form>";
echo "<table class=config>";
echo "<tr><td>Treasurer Email</td><td><input type=text size=50 name=email_treasurer value=\"".$config->email_treasurer."\"></td></tr>";
echo "<tr><td>MOT Email</td><td><input type=text size=50 name=email_mot value=\"".$config->email_mot."\"></td></tr>";
echo "<tr><td>Site Base URL</td><td><input type=text size=50 name=site_base value=\"".$config->site_base."\"></td></tr>";
echo "<tr><td>Paypal.me Link</td><td><input type=text size=50 name=paypal_link value=\"".$config->paypal_link."\"></td></tr>";
echo "<tr><td>Paypal Email</td><td><input type=text size=50 name=paypal_email value=\"".$config->paypal_email."\"></td></tr>";
echo "<tr><td>Main PLI Cost</td><td><input type=text size=50 name=primary_cost value=\"".$config->primary_cost."\"></td></tr>";
echo "<tr><td>Extra droid PLI Cost</td><td><input type=text size=50 name=other_cost value=\"".$config->other_cost."\"></td></tr>";
echo "</table>";
echo "<input type=submit name=update value=Update>";
echo "</form>";

}
?>

