<?
include "includes/config.php";
include "includes/session.php";


if ($_SESSION['role'] == "user") {
        $_REQUEST['member_uid'] = $_SESSION['user'];
}

require('fpdf/fpdf.php');

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all the details required
//
//

$sql = "SELECT * FROM droids WHERE droid_uid = ".$_REQUEST['droid_uid'];
$result = $conn->query($sql);
$droid = $result->fetch_assoc();

$sql = "SELECT * FROM members WHERE member_uid = ".$droid['member_uid']; 
$result = $conn->query($sql);
$member = $result->fetch_assoc();

$sql = "SELECT * FROM club_config WHERE club_uid = ".$droid['club_uid'];
$result = $conn->query($sql);
$club_config = $result->fetch_assoc();

$member_image = "uploads/members/".$member['member_uid']."/mug_shot.jpg";
$droid_image = "uploads/members/".$member['member_uid']."/".$droid['droid_uid']."/photo_front.jpg";

if (!file_exists($member_image)) {
	$member_image = "images/blank_mug_shot.jpg";
}

if (!file_exists($droid_image)) {
        $droid_image = "uploads/clubs/".$droid['club_uid']."/blank_photo_front.jpg";
}

class PDF extends FPDF
{
// Page header
function Header()
{
    global $droid, $club_config, $config;
    // Logo
    $logo = "uploads/clubs/".$droid['club_uid']."/logo.png";
    $fonts = array("Arial", "Times", "Helvetica", "Courier");
    if (!in_array($club_config['font_name'], $fonts)) {
        $this->AddFont($club_config['font_name'],'',$club_config['font_name'].".php");
    }
    $this->SetFont($club_config['font_name'],'',36);
    $this->SetFillColor(102,153,204);
    $this->Cell(190,25,$this->header_text,1,0,'R', true);
    $this->Image($logo, 12, 11);
    $this->Ln(35);
}

// Page footer
function Footer()
{
    global $droid, $club_config, $config;
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont($config->default_font,'I',8);
    $this->SetFillColor(102,153,204);
    $this->Cell(0,10,$this->footer_text,1,0,'C', true);
}

function droidDetails() {
    global $droid, $club_config, $config, $member;
    $this->SetFont($club_config['font_name'],'',16);
    $this->Cell(70,7,"Builder Name",0,0);
    $this->SetFont($config->default_font,'',16);
    $this->Cell(80,7,$member['forename']." ".$member['surname'],0,1);
    $this->SetFont($club_config['font_name'],'',16);
    $this->Cell(70,7,"Build Material",0,0);
    $this->SetFont($config->default_font,'',16);
    $this->Cell(80,7,$droid['material'],0,1);
    $this->SetFont($club_config['font_name'],'',16);
    $this->Cell(70,7,"Drive Voltage",0,0);
    $this->SetFont($config->default_font,'',16);
    $this->Cell(80,7,$droid['drive_voltage'],0,1);
    $this->SetFont($club_config['font_name'],'',16);
    $this->Cell(70,7,"Battery Type",0,0);
    $this->SetFont($config->default_font,'',16);
    $this->Cell(80,7,$droid['battery'],0,1);
    $this->SetFont($club_config['font_name'],'',16);
    $this->Cell(70,7,"Sound System",0,0);
    $this->SetFont($config->default_font,'',16);
    $this->Cell(80,7,$droid['sound_system'],0,1);
}

function setHeaderText($header_text) {
    $this->header_text = $header_text;
}

function setFooterText($footer_text) {
    $this->footer_text = $footer_text;
}


} // End of class

// Instanciation of inherited class
$pdf = new PDF();
$pdf->setHeaderText($droid['name']);
$pdf->setFooterText($club_config['name']." - Droid Details - ".$droid['name']);
$pdf->setAuthor($club_config['name']);
$pdf->setCreator("Automatically generated by the UK R2D2 Builders Club MOT website");
$pdf->setTitle($member['forename']." ".$member['surname']." - ".$club_config['name']." Droid Details");
$pdf->setSubject("Droid Details");
$pdf->setKeywords("Droid Information Document");
$pdf->AddPage();
$pdf->Image($droid_image, 20, 45, 75);
$pdf->Image($member_image, 115, 45, 75);
$pdf->Ln(115);
$pdf->SetFont($config->default_font,'',16);
$pdf->droidDetails();
$pdf->Ln(10);
$pdf->SetFont($club_config['font_name'],'',16);
$pdf->Write(5, "Other Details:");
$pdf->Ln(7);
$pdf->SetFont($config->default_font,'',12);
$pdf->Write(5, $droid['notes']);
$pdf->Output('I', $member['forename']." ".$member['surname']." ".$club_config['name']." Droid Details.pdf");
?>
