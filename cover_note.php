<?
include "includes/session.php";
include "includes/config.php";


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
$sql = "SELECT * FROM members WHERE member_uid = ". $_REQUEST['member_uid'];
$result = $conn->query($sql);
$member = $result->fetch_assoc();

$sql = "SELECT * FROM pli_cover_details";
$result = $conn->query($sql);
$pli = $result->fetch_assoc();

$searchArray = array("%member%", "%email%", "%pli_start%", "%pli_expire%", "%contact1%", "%contact2%");
$replaceArray = array($member['forename']." ".$member['surname'], $member['email'], $member['pli_date'], gmdate("Y-m-d", strtotime($member['pli_date']." +1 year")), $pli['contact1'], $pli['contact2']);
$details = str_replace($searchArray, $replaceArray, $pli['details']);
$body = str_replace($searchArray, $replaceArray, $pli['body']);

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->SetFont('Arial','B',18);
    $this->SetFillColor(102,153,204);
    $this->Cell(190,25,$this->header_text,1,0,'R', true);
    $this->Image('images/MOT_Banner.png', 12, 11);
    $this->Ln(35);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    $this->SetFillColor(102,153,204);
    $this->Cell(0,10,$this->footer_text,1,0,'C', true);
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
$pdf->setHeaderText($pli['header_text']);
$pdf->setFooterText($pli['footer_text']);
$pdf->setAuthor("UK R2D2 Builders Club");
$pdf->setCreator("Automatically generated by the UK R2D2 Builders Club MOT website");
$pdf->setTitle($member['forename']." ".$member['surname']." PLI Cover Details");
$pdf->setSubject("PLI Cover Details");
$pdf->setKeywords("UK R2D2 Builders Club PLI Public Liability Insurance");
$pdf->AddPage();
$pdf->SetFont('Arial','',16);
$pdf->Write(7, $details);
$pdf->Ln(10);
$pdf->SetFont('Arial','',12);
$pdf->Write(5, $body);
$pdf->Output();
?>
