<?

include "includes/header.php";
include "includes/image_upload.php";

if (!($_SESSION['permissions'] & $perms['VIEW_MEMBERS'])) {
        $_REQUEST['member_uid'] = $_SESSION['user'];
}

echo "<script src=\"https://www.w3schools.com/lib/w3.js\"></script>";
echo "<div id=main class=\"member-container\">";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function formatMilliseconds($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%02u:%02u.%03u';
    $time = sprintf($format, $minutes, $seconds, $milliseconds);
    //return rtrim($time, '0');
    return $time;
}

# Image uploads
if (($_REQUEST['upload'] != "") && ($_SESSION['permissions'] & $perms['EDIT_MEMBERS'])) {
        $imagename=$_FILES[$_REQUEST['upload']]["name"];
        list($orig_width, $orig_height) = getimagesize($_FILES[$_REQUEST['upload']]['tmp_name']);
	$width = 640; // max size of image to upload
	$path = "uploads/members/".$_REQUEST['member_uid']."/mug_shot.jpg";
        $height = (($orig_height * $width) / $orig_width);

        imagejpeg($thumb, $path, 75);

        $exif = exif_read_data($_FILES[$_REQUEST['upload']]['tmp_name']);
        if( isset($exif['Orientation']) )
            $orientation = $exif['Orientation'];
        elseif( isset($exif['IFD0']['Orientation']) )
            $orientation = $exif['IFD0']['Orientation'];
        else
            $orientation = 0;
        echo "Orientation: $orientation<br/>";
        $img = imagecreatefromjpeg($_FILES[$_REQUEST['upload']]['tmp_name']);
        switch($orientation)
        {
            case 3: // 180 rotate left
            $newimg = imagerotate($img, 180, -1);
            break;

            case 6: // 90 rotate right
            $newimg = imagerotate($img, -90, -1);
            break;

            case 8:    // 90 rotate left
            $newimg = imagerotate($img, 90, -1);
            break;

            case 0:    // 90 rotate left
            $newimg = imagerotate($img, 0, 0);
            break;

	    default:
	    $newimg = $img;
	    break;
        }
	$thumb = imagecreatetruecolor($width, $height);
	imagecopyresampled($thumb, $newimg, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
	if (!file_exists("uploads/members/".$_REQUEST['member_uid'])) {
		mkdir("uploads/members/".$_REQUEST['member_uid']);
	}
	imagejpeg($thumb, $path, 75);
}


if (($_REQUEST['delete_mug'] == 1) && ($_SESSION['permissions'] & $perms['DELETE_IMAGES'])) {
	unlink("uploads/members/".$_REQUEST['member_uid']."/mug_shot.jpg");
	echo "Image deleted";
}


if (($_REQUEST['update'] != "") && ($_SESSION['permissions'] & $perms['EDIT_MEMBERS'])) {
    $longitude = $_REQUEST['longitude'];
    $latitude = $_REQUEST['latitude'];
    $sql = "SELECT pli_date FROM members WHERE member_uid=".$_REQUEST['member_uid'];
    $original_pli = $conn->query($sql)->fetch_object()->pli_date;
    // echo "Original PLI date = ".$original_pli;
    if (($_REQUEST['latitude'] == "" ) && ( $_REQUEST['postcode'] != "" )) {
   	$address = str_replace(' ','+',$_REQUEST["postcode"]);
	$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.$config->google_map_api.'&address='.$address.'&sensor=false');
        $output= json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;
    }
    if ($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS']) {
	$new_perms = 0;
        for ($i=0 ; $i < $_REQUEST['num_perms'] ; $i++ ) {
             $perm = strval(2**$i);
	     if ($_REQUEST[$perm] == "on") {
		     $new_perms = $new_perms + 2**$i;
	     }

	}
	$perms_sql = "UPDATE members SET permissions=".$new_perms." WHERE member_uid=".$_REQUEST['member_uid'];
	$conn->query($perms_sql);
    }
    $sql = "UPDATE members SET email=?, county=?, postcode=?, latitude=?, longitude=?, pli_date=?, pli_active=?, active=?, username=? WHERE member_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $_REQUEST['email'], $_REQUEST['county'], $_REQUEST['postcode'], $latitude, $longitude, $_REQUEST['pli_date'], $_REQUEST['pli_active'], $_REQUEST['active'], $_REQUEST['username'], $_REQUEST['member_uid']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
    if (($original_pli != $_REQUEST['pli_date']) && ($config->site_options & $options['SEND_EMAILS'])) {
	    # PLI Change, send email
   	    # Get some MOT details for emails
	    $sql = "SELECT * FROM members WHERE member_uid = ".$_SESSION["user"];
            $officer = $conn->query($sql)->fetch_object();
            $sql = "SELECT * FROM members WHERE member_uid=".$_REQUEST['member_uid'];
            $member = $conn->query($sql)->fetch_object();
            $mot_head_email = array();
	    $mot_head_email[] = $officer->email;
	    $mot_head_email[] = $config->email_treasurer;
	    $mot_head_email[] = $member->email;
	    $to = implode(',', $mot_head_email);
            # Approved, email peeps
            $subject = "UK R2 Builders MOT - PLI Updated";
            $message = "Your PLI date has been updated by ".$officer->forename." ".$officer->surname."\r\n";
            $message .= "\r\n";
	    $message .= "Member: ".$member->forename." ".$member->surname."\r\n";
	    $message .= "PLI Date: ".$_REQUEST['pli_date']."\r\n";
            $headers = "From: R2 Builders MOT <".$config->from_email.">"."\r\n"."X-Mailer: PHP/".phpversion();
            $success = mail($to, $subject, $message, $headers);

    }
}

if (($_REQUEST['achievement'] == "Add") && ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) ) {
    $sql = "INSERT into members_achievements(achievement_uid, member_uid, notes, added_by) VALUES(".$_REQUEST['achievement_uid'].", ".$_REQUEST['member_uid'].", '".addslashes($_REQUEST['notes'])."', ".$_SESSION['user'].")";
    $result=$conn->query($sql);
}

if (($_REQUEST['event'] == "Add") && ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) ) {
    $sql = "INSERT into members_events(member_uid, event_uid, added_by, spotter) VALUES(".$_REQUEST['member_uid'].", '".addslashes($_REQUEST['event_uid'])."', ".$_SESSION['user'].", '".$_REQUEST['spotter']."')";
    $result=$conn->query($sql);
}


$sql = "SELECT * FROM members WHERE member_uid = ". $_REQUEST['member_uid'];
$result = $conn->query($sql);
$member = $result->fetch_assoc();
$sql = "SELECT forename,surname FROM members WHERE member_uid = ".$member["created_by"];
$officer = $conn->query($sql)->fetch_object();
$officer_name = $officer->forename." ".$officer->surname;
$sql = "SELECT * FROM droids WHERE member_uid = ". $_REQUEST['member_uid']. " AND active='on'";
$droids = $conn->query($sql);
$valid_mot = 0;

################################################
# Droid list
echo "<div class=\"Droid-List\">";

echo "<table class=droid_list id=droid_list>";
echo "<tr><th colspan=7>Droid info</th></tr>";

if ($droids->num_rows > 0) {
    // output data of each row
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(1)')\">Valid MOT</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(2)')\">Droid</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(3)')\">Primary</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(4)')\">Type</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(5)')\">Style</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(8)')\">Tier Two</th>";
    echo "<th></th>";
    echo "</tr>";
    while($row = $droids->fetch_assoc()) {
	# Pull the latest MOT for the droid that is a pass
        $sql = "SELECT * FROM mot WHERE (approved='Yes' OR approved='WIP' OR approved='Advisory') AND droid_uid = " .$row["droid_uid"]. " AND date >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY date DESC LIMIT 1";
	$mot_result = $conn->query($sql);
	echo "<tr class=\"item\">";
	if ($mot_result->num_rows > 0) {
	    $mot_details=$mot_result->fetch_object();
	    if ($mot_details->approved == "Yes") {
	        echo "<td bgcolor=green><a href=mot.php?mot_uid=".$mot_details->mot_uid.">Valid (".$mot_details->date.")</a></td>";
		$valid_mot = 1;
	    } elseif ($mot_details->approved == "WIP") {
		echo "<td bgcolor=blue><a href=mot.php?mot_uid=".$mot_details->mot_uid.">WIP (".$mot_details->date.")</a></td>";
            } else {
		echo "<td bgcolor=orange><a href=mot.php?mot_uid=".$mot_details->mot_uid.">Advisory (".$mot_details->date.")</a></td>";
		$valid_mot = 1;
            }
	} elseif (!$club_config[$row['club_uid']]['options'] & $club_options['MOT']) {
	    echo "<td></td>";
        } else {
	    echo "<td bgcolor=red>Not Valid</td>";
	}
	echo "<td>" . $row["name"]. "</td>";
	echo "<td>" . $row["primary_droid"]."</td>";
	echo "<td>" . $row["type"]. "</td>";
	echo "<td>" . $row["style"]. "</td>";
	echo "<td align=center>". $row['tier_two']. "</td>";
	echo "<td><a href=droid.php?droid_uid=". $row["droid_uid"]. ">View Droid</a></td>";
	echo "</tr>";
    }
} else {
        echo "<tr><td colspan=7>No Droids</td></tr>";
}
echo "</table>";
if ($_SESSION['role'] != "user" ) {
    echo "<a href=new_droid.php?member_uid=". $_REQUEST["member_uid"]. ">Add Droid</a>";
}
echo "</div>";


# Member details
echo "<div class=\"Member-Info\">";
echo "<h2>". $member['forename'] ." ".$member['surname'];
if ($_SESSION['permissions'] & $perms['DUMP_DATA']) {
	echo " <a href=\"id_images.php?member_uid=".$member['member_uid']."\" target=_blank>";
	echo "<img src=\"images/download.svg\" height=20 alt=\"Download ID Images\">";
	echo "</a>";
}
echo "</h2>";
if ($config->site_options & $options['PAYPAL']) {
    if (strtotime($member['pli_date']) < strtotime('-1 year') && $valid_mot == 1) {
	echo "Your PLI is due. Click here to pay it via PayPal : ";
        echo "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">";
        echo "<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">";
        echo "<input type=\"hidden\" name=\"hosted_button_id\" value=\"".$config->paypal_button."\">";
        echo "<input type=\"hidden\" name=\"custom\" value=\"".$member['member_uid']."\">";
	echo "<input type=\"hidden\" name=\"on0\" value=\"MOT Type\">";
	echo "<input type=\"hidden\" name=\"os0\" value=\"Initial/Renewal\">";
	echo "<input type=\"hidden\" name=\"currency_code\" value=\"GBP\">";
        echo "<input type=\"image\" src=\"https://www.paypalobjects.com/en_GB/i/btn/btn_paynow_SM.gif\" border=\"0\" name=\"submit\" alt=\"PayPal – The safer, easier way to pay online!\">";
        echo "<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_GB/i/scr/pixel.gif\" width=\"1\" height=\"1\">";
        echo "</form>";
    }
}
echo "<form>";
echo "<input type=hidden name=member_uid value=".$member['member_uid'].">";
echo "<table class=member>";
echo " <tr><th>email: </th><td><input type=email size=50 name=email value=".$member['email']."></td></tr>";
echo " <tr><th>County: </th><td><input type=text size=50 name=county value=\"".$member['county']."\"></td></tr>";
echo " <tr><th>Postcode: </th><td><input type=text size=50 name=postcode value=\"".$member['postcode']."\"></td></tr>";
if ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) {
    echo " <tr><th>Latitude: </th><td><input type=text size=50 name=latitude value=\"".$member['latitude']."\"></td></tr>";
    echo " <tr><th>Longitude: </th><td><input type=text size=50 name=longitude value=\"".$member['longitude']."\"></td></tr>";
}
echo " <tr><th>Forum Username: </th><td><input type=text size=50 name=username value=\"".$member['username']."\"></td></tr>";
echo " <tr><th>Created On: </th><td>".$member['created_on']."</td></tr>";
echo " <tr><th>Created By: </th><td>".$officer_name."</td></tr>";
echo " <tr><th>PLI Cover Last Paid: </th><td><input type=date name=pli_date value=".$member['pli_date'].">";
if ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) {
    echo "BID Sent <input type=checkbox name=pli_active";
    echo ($member['pli_active'] == "on") ? " checked" : "";
    echo ">";
}
if (strtotime($member['pli_date']) > strtotime('-1 year')) {
    echo "<a target=_blank href=cover_note.php?member_uid=".$member['member_uid'].">Cover Note</a>";
}
echo "</td></tr>";
echo " <tr><th>Last Updated: </th><td>".$member['last_updated']."</td></tr>";
echo " <tr><th>ID Link: </th><td><a href=\"id.php?id=".$member['badge_id']."\">".$config->site_base."/id.php?id=".$member['badge_id']."</a><br />";
echo "<img id=qr_code src=\"showImage.php?member_id=".$member['member_uid']."&type=member&name=qr_code&width=260\"></td></tr>";
echo " <tr><th>Active?: </th><td><input name=active type=checkbox";
echo ($member['active'] == "on") ? " checked" : "";
echo "></td></tr>";
if ($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS']) {
echo "<tr><th>Permission:</th>";
echo "<td>";
       echo "<table><tr>";
        $sql = "SELECT * FROM permissions";
        $permissions = $conn->query($sql);
        while($row = $permissions->fetch_assoc()) {
            echo "<th class=rotate_permission><div><span>".$row['name']."</span></div></th>";
            $num_perms++;
        }
        echo "</tr><tr>";
        for ($i=0 ; $i < $num_perms ; $i++) {
                    $permission = 2**$i;
                    echo "<td class=permission><input type=checkbox name=$permission ";
                    echo ($member['permissions'] & $permission) ? "checked" : "";
                    echo "></td>";
        }
        echo "</tr></table>";
	echo "<input type=hidden name=num_perms value=".$num_perms.">";
        echo "</td></tr>";
}
echo "</table>";
if ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) {
    echo "<input type=submit name=update value=Update>";
}
echo "</form>";
echo "</div>";

# Mug Shot
echo "<div class=\"Mug-Shot\">";
        echo "<div class=\"mug_shot_container\">";
	echo "<div class=\"mug_shot w3-cell\"><img id=mug_shot src=\"showImage.php?member_id=".$member['member_uid']."&type=member&name=mug_shot&width=240\">\r\n";
        if ($_SESSION['permissions'] & $perms['DELETE_IMAGES']) {
                echo "<a href=\"member.php?delete_mug=1&member_uid=".$member['member_uid']."\">Delete</a>\r\n";
        }
	echo "</div>\r\n";
	imageUpload('mug_shot', $member['member_uid']);
	echo "</div>";


echo "</div>";

# Achievements
if ($config->site_options & $options['ACHIEVEMENTS']) {
echo "<div class=\"Achievements\">";
echo "<h4>Achievements</h4>";
$sql = "SELECT * FROM members_achievements WHERE member_uid=".$member['member_uid'];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class=achievements_list id=achievements_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(1)')\">Name</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(2)')\">Notes</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(3)')\">Date Added</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(4)')\"></th></tr>";
    while($row = $result->fetch_assoc()) {
	$sql = "SELECT * FROM achievements WHERE achievement_uid=".$row['achievement_uid'];
	$achievement = $conn->query($sql)->fetch_assoc();
	echo "<td class=achievements_list><a href='#'>".$achievement['name']."<div class='tooltipcontainer'>";
	echo "<div class='tooltip'>".$achievement['description']."</div>";
	echo "</div></a></td>";
	echo "<td class=achievements_list>".$row['notes']."</td>";
	echo "<td class=achievements_list>".$row['date_added']."</td>";
	echo "<td class=achievements_list>";
        if ($achievement['icon'] != "") {
            echo "<img id=icon src=data:image/jpeg;base64,".base64_encode( $achievement['icon'] )." width=40>";
        } else {
            echo "";
        }
	echo "</td>";
	echo "</tr>";
    }

} else {
    echo "No achievements";
}
echo "</table>";
$sql="SELECT * FROM achievements";
$result=$conn->query($sql);

if ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) {
    echo "<form>";
    echo "Add achievement<br />";
    echo "<input type=hidden name=member_uid value=".$member['member_uid'].">";
    echo "Notes: <input type=text size=50 name=notes>";
    echo "<select name=achievement_uid>";
    while($row = $result->fetch_assoc()) {
        echo "<option value=".$row['achievement_uid'].">".$row['name']."</option>";
    }
    echo "</select>";
    echo "<input type=Submit value=Add name=achievement>";
    echo "</form>";
}
echo "</div>";
echo "<hr />";
}
# End of Achievements


# Official Events
if ($config->site_options & $options['EVENTS']) {
echo "<div class=\"Events\">";
echo "<h4>Official Events</h4>";
$sql = "SELECT * FROM members_events, events WHERE events.event_uid = members_events.event_uid AND member_uid=".$member['member_uid']." ORDER BY events.date";
$result = $conn->query($sql);
$charity_raised = 0;
if ($result->num_rows > 0) {
    echo "<table class=events_list id=events_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(1)')\">Date</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(2)')\">Details</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(3)')\">Spotter</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(4)')\">Charity Raised</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(5)')\">Links</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<td class=events_list>".$row['date']."</td>";
        echo "<td class=events_list>".$row['name']."</td>";
	echo "<td class=events_list>";
	if ($row['spotter'] == "on") 
		echo "Yes";
	echo "</td>";
	echo "<td class=events_list>£".$row['charity_raised']."</td>";
        echo " <td class=events_list>";
        if ($row['forum_link'] != "") {
                echo "<a href=\"".$row['forum_link']."\" target=\"_blank\">F</a> ";
        }
        if ($row['report_link'] != "") {
                echo "<a href=\"".$row['report_link']."\" target=\"_blank\">R</a>";
        }
        echo " </td>";
	echo "</tr>";
	$charity_raised = $charity_raised + $row['charity_raised'];
    }
    echo "<tr><td colspan=2></td><th>Total</th><td>£".$charity_raised."</td></tr>";

} else {
    echo "No events";
}
echo "</table>";
$sql="SELECT * FROM events WHERE date < NOW() ORDER BY date DESC";
$result=$conn->query($sql);

if ($_SESSION['permissions'] & $perms['EDIT_MEMBERS']) {
    echo "<form>";
    echo "Add event<br />";
    echo "<input type=hidden name=member_uid value=".$member['member_uid'].">";
    echo "<select name=event_uid>";
    while($row = $result->fetch_assoc()) {
        echo "<option value=".$row['event_uid'].">(".$row['date'].") ".$row['name']."</option>";
    }
    echo "</select>";
    echo "Spotter: <input type=checkbox name=spotter>";
    echo "<input type=Submit value=Add name=event>";
    echo "</form>";
}
echo "</div>";
echo "<hr />";
}
# End of Official Events


# Course runs
if ($config->site_options & $options['DRIVING_COURSE']) {
echo "<div class=\"Course-Times\">";
echo "<h4>Driving Course Runs</h4>";
$sql = "SELECT * FROM course_runs WHERE member_uid=".$member['member_uid']." ORDER BY final_time ASC";
$runs = $conn->query($sql);
if ($runs->num_rows > 0) {
    echo "<table class=course_list id=course_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(1)')\">Run date</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(2)')\">Droid</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(3)')\">First Half</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(4)')\">Second Half</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(5)')\">Clock Time</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(6)')\">Penalties</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(7)')\">Final Time</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(7)')\"></th></tr>";
    while($row = $runs->fetch_assoc()) {

        echo "<tr class=course_list>";
        echo "<td>".substr($row['run_timestamp'],0,10)."</td>";
        $sql = "SELECT name FROM droids WHERE droid_uid = ".$row['droid_uid'];
        $droid_name = $conn->query($sql)->fetch_assoc();
        echo "<td>".$droid_name['name']."</td>";
        if ($row['first_half'] == 0) {
                echo "<td></td>";
        } else {
                echo "<td>".formatMilliseconds($row['first_half'])."</td>";
        }
        if ($row['second_half'] == 0) {
                echo "<td></td>";
        } else {
                echo "<td>".formatMilliseconds($row['second_half'])."</td>";
        }
        echo "<td>".formatMilliseconds($row['clock_time'])."</td>";

        echo "<td>".$row['num_penalties']."</td>";
        echo "<td>".formatMilliseconds($row['final_time'])."</td>";


        echo "<td>";
        if ($row['dribble'] == "1") {
                echo "<img src=images/soccer-ball.png>";
        }
        echo "</td>";
        echo "</tr>";
    }


} else {
    echo "No runs";
}
echo "</table>";
echo "</div>";
}
# End of Course Runs



echo "</div>";

include "includes/footer.php";

?>


