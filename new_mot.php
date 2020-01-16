<?

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['ADD_MOT'])) {
	die();
}


echo "<div id=wrapper>";
echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT forename, surname FROM members WHERE member_uid = ".$_SESSION["user"];
$officer = $conn->query($sql)->fetch_object();
$officer_name = $officer->forename." ".$officer->surname;

$sql = "SELECT * FROM droids WHERE droid_uid = ".$_REQUEST['droid_uid'];
$droid = $conn->query($sql)->fetch_object();

$sql = "SELECT * FROM mot_sections WHERE club_uid = ".$droid->club_uid;
$sections = $conn->query($sql);


if ($_REQUEST['new_mot'] != "" && ($_SESSION['permissions'] & $perms['ADD_MOT'])) {
    if ($_REQUEST['mot_type'] == "Retest") {
	    $sql = "SELECT * FROM mot WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY date DESC LIMIT 1";
	    $mot_result = $conn->query($sql);
	    if ($mot_result->num_rows > 0) {
		    $row = $mot_result->fetch_assoc();
		    $_REQUEST['date'] = $row['date'];
	    }
    }

    $sql = "INSERT INTO mot(droid_uid, date, location, mot_type, approved, user) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $_REQUEST['droid_uid'], $_REQUEST['date'], $_REQUEST['location'], $_REQUEST['mot_type'], $_REQUEST['approved'], $_SESSION['user']);
    $stmt->execute();
    $mot_uid = $stmt->insert_id;
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    } elseif ($config->site_options & $options['SEND_EMAILS']) {
	    # Get some MOT details for emails
	    $sql = "SELECT * FROM members WHERE member_uid = ".$_SESSION["user"];
            $officer = $conn->query($sql)->fetch_object();
            $sql = "SELECT * FROM droids WHERE droid_uid=".$_REQUEST['droid_uid'];
            $droid = $conn->query($sql)->fetch_object();
            $sql = "SELECT * FROM members WHERE member_uid=$droid->member_uid";
            $member = $conn->query($sql)->fetch_object();
            $mot_head_email = array();
	    $mot_head_email[] = $officer->email;
	    $mot_head_email[] = $config->email_mot;
	    $mot_head_email[] = $config->email_treasurer;
	    $to = implode(',', $mot_head_email);
            # Approved, email peeps
            $subject = "UK R2 Builders MOT - MOT Submitted";
	    $message = "An MOT has been submitted by ".$officer->forename." ".$officer->surname." and an email to the droid owner with";
	    $message .= "instructions on paying any PLI due<br />";
            $message .= "<br />";
            $message .= "<a href=\"".$config->site_base."/droid.php?droid_uid=".$_REQUEST['droid_uid']."\">".$config->site_base."/droid.php?droid_uid=".$_REQUEST['droid_uid']."</a><br />";
	    $message .= "<br />";
	    $message .= "<ul><li>Member: ".$member->forename." ".$member->surname."</li>";
	    $message .= "<li>MOT Location: ".$_REQUEST['location']."</li>";
	    $message .= "<li>Droid Status: ".$_REQUEST['approved']."</li>";
	    $message .= "<li>MOT Type ".$_REQUEST['mot_type']."</li></ul>";
            $headers = "From: R2 Builders MOT <".$config->from_email.">"."\r\n"."X-Mailer: PHP/".phpversion()."\r\n";
	    $headers .= "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            # $success = mail($to, $subject, $message, $headers);
	    echo "<br />Email sent to MOT officers ".$success;

	    # Send email to owner
	    $subject = "UK R2 Builders MOT - MOT Submitted";
	    $message = "Hello ".$member->forename.",<br />";
	    $message .= "<br />";
	    $message .= "An MOT for your droid has been submitted by ".$officer->forename." ".$officer->surname."<br />";
	    $message .= "<br />";
	    $message .= "<ul><li>MOT Location: ".$_REQUEST['location']."</li>";
            $message .= "<li>Droid Status: ".$_REQUEST['approved']."</li>";
            $message .= "<li>MOT Type ".$_REQUEST['mot_type']."</li>";
	    $message .= "</ul>";
	    if (($_REQUEST['approved'] == "Yes") || ($_REQUEST['approved'] == "Advisory") || ($_REQUEST['approved'] == "WIP")) {
		    if (($_REQUEST['approved'] == "Yes") || ($_REQUEST['approved'] == "WIP")) {
		        $message .= "Congratulations on your droid passing its MOT. To be covered by the group's PLI, please make sure<br />";
		        $message .= "to send in your payment. You are covered by the PLI at the point payment is received and cleared by the PLI officer.<br />";
		    } else {
			$message .= "Congratulations on the results of your droids MOT. The MOT has been submitted with an Advisory associated<br />";
			$message .= "with it. The MOT officer should have gone through the advisories and advised you of timescales to fix the issues.<br />";
			$message .= "You are covered by PLI at the point payment is received and cleared by the PLI officer. Failure to rectify the<br />";
			$message .= "outstanding advisories within the agreed timescale, or failure to contact an MOT officer to explain why you cannot<br />";
			$message .= "effect the fix before the timescale expired will render you PLI void until such a time that you surrender your droid<br />";
			$message .= "for a full MOT.<br />";
		    }
		    $message .= "<br />";
		    if ($droid->primary_droid == "Yes") {
			    $message .= "Cost for primary droid is £".$config->primary_cost.". The best way to pay this is to log into the MOT site and use the Paypal<br />";
			    $message .= "button there. This will automatically update your PLI status, and also give you access to all the info we hold about your droid<br />";
			    $message .= "and more. The site is here: <a href=\"".$config->site_base."\">MOT Site</a><br />";
			    $message .= "Or, you can click the link here and pay that way, tho it will be a manual process to update your records which may take a few days. <br />";
			    $message .= "<a href=\"".$config->paypal_link."/".$config->primary_cost."\">".$config->paypal_link."/".$config->primary_cost."</a><br />";
	            } else {
			    $message .= "As you already have a droid, cost for an additional droid is £".$config->other_cost.". The link below will take you directly to the paypal payment page<br />";
			    $message .= "<a href=\"".$config->paypal_link."/".$config->other_cost."\">".$config->paypal_link."/".$config->other_cost."</a><br />";
	            }
		    $message .= "Or, if you prefer not to follow the links, you can send it via paypal to ".$config->paypal_email."<br />";
	    }
	    # $success = mail($member->email, $subject, $message, $headers);
	    echo "<br />Email sent to droid owner ".$success;

	    //$sql = "UPDATE members SET pli_active='' WHERE member_uid = $droid->member_uid";
	    //$conn->query($sql);

    }

    # Save the details of the MOT
    $sql = "SELECT * FROM mot_lines WHERE club_uid = ".$droid->club_uid;
    $lines = $conn->query($sql);
    while ($line = $lines->fetch_object()) {
	    $sql = "INSERT INTO mot_details(mot_uid, mot_test, mot_test_result) VALUES(?, ?, ?)";
	    $stmt = $conn->prepare($sql);
	    $stmt->bind_param("iss", $mot_uid, $line->test_name, $_REQUEST[$line->test_name]);
            $stmt->execute();
    }


    $stmt->close();

    if ($_REQUEST['new_comment'] != "") {
        $sql = "INSERT INTO mot_comments(mot_uid, comment, added_by) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $mot_id, $_REQUEST['new_comment'], $_SESSION['user']);
        $stmt->execute();
        $stmt->close();
    }

    echo "<br />";
    echo "<a href=droid.php?droid_uid=".$_REQUEST['droid_uid'].">Back to droid</a>";
    echo "<br />";

}

# Comments

echo "<div class=comments>";
echo "<form>";
echo "Comments:";
echo "<textarea name=new_comment rows=20 cols=30></textarea>";
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo "<input type=hidden name=user value=".$_SESSION['user']."><br />";
echo "</div>";



echo "<div id=info>";
echo "<ul>";
echo " <li>Date Taken: <input type=date name=date value=".date('Y-m-d')."></li>";
echo " <li>Location: <input type=text name=location></li>";
echo " <li>MOT Type: <select name=mot_type><option value=Initial>Initial</option><option value=Renewal>Renewal</option><option value=Retest>Retest</option></select></li>";
echo " <li>Pass: <select name=approved><option value=Yes>Yes</option><option value=No>No</option><option value=WIP>WIP</option><option value=Advisory>Yes (Advisory)</option></select></li>";
echo " <li>MOT Officer: ".$officer_name."</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<div id=mot_test>";

# Blocks
if ($sections->num_rows > 0) {
	while($row = $sections->fetch_object()) {
		echo "<div id=mot_block>";
		echo "<h3>".$row->section_description."</h3>";
		echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
	        $sql = "SELECT * FROM mot_lines WHERE club_uid = ".$droid->club_uid." AND test_section = '".$row->section_name."'";
		$lines = $conn->query($sql);
		while ($line = $lines->fetch_object()) {
			echo "<tr>";
			echo "<td>";
			echo $line->test_description;
			echo "</td>";
			echo "<td>";
		        echo "<input type=radio name=$line->test_name value=Pass>Pass";
			echo "<input type=radio name=$line->test_name value=Fail checked>Fail";
			echo "<input type=radio name=$line->test_name value=NA>NA";

			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
	}
} else {
	echo "No sections defined for this club";
}

echo "<input type=submit value=Submit name=new_mot>";
echo "</form>";

echo "</div>";

echo "</div>";

include "includes/footer.php";
?>

