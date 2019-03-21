<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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

?>

<h3>Data Protection Policy: UK R2D2 Builders Club</h3>
<h4>1) Definitions</h4>
<ol>
<li>Personal data is information about a person which is identifiable as being about them. It can be stored electronically or on paper, and includes images and audio recordings as well as written information.</li>
<li>Data protection is about how we, as an organisation, ensure we protect the rights and privacy of individuals, and comply with the law, when collecting, storing, using, amending, sharing, destroying or deleting personal data.</li>
</ol>
<h4>2) Responsibility</h4>
<ol>
<li>Overall and final responsibility for data protection lies with the management committee, who are responsible for overseeing activities and ensuring this policy is upheld.</li>
<li>All volunteers are responsible for observing this policy, and related procedures, in all areas of their work for the club.</li>
</ol>
<h4>3) Overall policy statement</h4>
<ol>
<li>The UK R2D2 Builders Club needs to keep personal data about its committee and members in order to carry out club activities.</li>
<li>We will collect, store, use, amend, share, destroy or delete personal data only in ways which protect people&#8217;s privacy and comply with the General Data Protection Regulation (GDPR) and other relevant legislation.</li>
<li>We will only collect, store and use the minimum amount of data that we need for clear purposes, and will not collect, store or use data we do not need.</li>
<li>We will only collect, store and use data for:
<ul>
<li>purposes for which the individual has given explicit consent, or</li>
<li>purposes that are in our our group&#8217;s legitimate interests, or</li>
<li>contracts with the individual whose data it is, or</li>
<li>to comply with legal obligations, or</li>
<li>to protect someone&#8217;s life, or</li>
<li>to perform public tasks.</li>
</ul>
</li>
<li>We will provide individuals with details of the data we have about them when requested by the relevant individual.</li>
<li>We will delete data if requested by the relevant individual, unless we need to keep it for legal reasons.</li>
<li>We will endeavor to keep personal data up-to-date and accurate.</li>
<li>We will store personal data securely.</li>
<li>We will keep clear records of the purposes of collecting and holding specific data, to ensure it is only used for these purposes.</li>
<li>We will not share personal data with third parties without the explicit consent of the relevant individual, unless legally required to do so.</li>
<li>We will endeavour not to have data breaches. In the event of a data breach, we will endeavour to rectify the breach by getting any lost or shared data back. We will evaluate our processes and understand how to avoid it happening again. Serious data breaches which may risk someone&#8217;s personal rights or freedoms will be reported to the Information Commissioner&#8217;s Offic within 72 hours, and to the individual concerned.</li>
<li>To uphold this policy, we will maintain a set of data protection procedures for our committee and volunteers to follow.</li>
</ol>
<h4>4) Data Stored</h4>
<p>The following data will be stored for club use:</p>
<ul>
<li>Full Name</li>
<li>Email address</li>
<li>County and Post Code</li>
<li>PLI Cover details</li>
<li>Basic droid information (build type, weight, controller, etc.)</li>
<li>Photo of member, and reference shots of any droids</li>
<li>Other club information:
<ul>
<li>Club achievements</li>
<li>Events attended</li>
<li>Driving course runs</li>
</ul>
</ul>
<p>Any grid references stored will be to the first part of the postcode only, to allow for better planning of events</p>
<h4>5) Data Access</h4>
<p>The following people will have access to the data:</p>
<ul>
<li>Committee members</li>
<li>Nominated MOT officers</li>
<li>Other members will be able to see their own details only</li>
</ul>
<h4>6) Review</h4>
<p>This policy will be reviewed every two years</p>
<hr />
<h3>UK R2D2 Builders Club - Data protection procedures</h3>
<h4>1) Introduction</h4>
<ol>
<li>UK R2D2 Builders Club has a data protection policy which is reviewed regularly. In order to help us uphold the policy, we have created the following procedures which outline ways in which we collect, store, use, amend, share, destroy and delete personal data.</li>
<li>These procedures cover the main, regular ways we collect and use personal data. We may from time to time collect and use data in ways not covered here. In these cases we will ensure our Data Protection Policy is upheld.</li>
</ol>
<h4>2) General procedures</h4>
<ol>
<li>Data will be stored securely. When it is stored electronically, it will be kept in password protected files. When it is stored online in a third party website (e.g. Google Drive) we will ensure the third party comply with the GDPR. When it is stored on paper it will be filed carefully in a locked filing cabinet.</li>
<li>When we no longer need data, or when someone has asked for their data to be deleted, it will be deleted securely. We will ensure that data is permanently deleted from computers, and that paper data is shredded.</li>
<li>We will keep records of consent given for us to collect, use and store data. These records will be stored securely.</li>
</ol>
<h4>3) Mailing list</h4>
<ol>
<li>We will keep members email addresses for reminders and security notices about their MOT and PLI cover only. 
<li>When people sign up to the list we will explain how their details will be used, how they will be stored, and that they may ask to be removed from the list at any time. We will ask them to give separate consent to receive publicity and fundraising messages, and will only send them messages which they have expressly consented to receive.</li>
<li>We will not use the mailing list in any way that the individuals on it have not explicitly consented to.</li>
</ol>
<h3>4) Contacting committee members</h3>
<ol>
<li>The committee need to be in contact with one another in order to run the organisation effectively and ensure its legal obligations are met.</li>
<li>Committee contact details will be shared among the committee.</li>
<li>Committee members will not share each other&#8217;s contact details with anyone outside of the committee, or use them for anything other than the UK R2D2 Builders Club business, without explicit consent.</li>
</ol>
<h4>8) Review</h4>
<p>These procedures will be reviewed every two years</p>
<p>&nbsp;</p>


<?

$conn->close();

?>


