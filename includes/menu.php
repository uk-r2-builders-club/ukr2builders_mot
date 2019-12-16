<script>
/* Set the width of the side navigation to 250px and the left margin of the page content to 250px */
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

/* Set the width of the side navigation to 0 and the left margin of the page content to 0 */
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
}
</script>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
<?
        echo "<ul>";
        echo " <li><a href='member.php?member_uid=".$_SESSION['user']."'>Your Profile</a></li>";
        if ($config->site_options & $options['EVENTS']) echo " <li><a href=events.php>Events</a></li>";
        if ($config->site_options & $options['DRIVING_COURSE']) echo " <li><a target='_blank' href=leaderboard.php>Droid Leaderboard</a></li>";
        if ($config->site_options & $options['TOPPS']) echo " <li><a target='_blank' href=topps.php>Topps Droids</a></li>";
        echo " <li><a target='_blank' href=stats.php>Droid statistics</a></li>";
        echo " <li><a target='_blank' href=gdpr.php>Privacy Policy</a></li>";
        echo " <li><a target='_blank' href=https://github.com/uk-r2-builders-club/ukr2builders_mot/wiki/Members-Manual>Instruction Manual</a></li>";
        echo " <li><a href=password.php>Change Password</a></li>";
        echo " <li><a href=?logout=yes>Logout</a></li>";
        echo "</ul>";
	echo "<hr>";
	if ($_SESSION['permissions'] > 0 ) {
            echo " <ul>";
            if ($_SESSION['permissions'] & $perms['VIEW_MEMBERS']) echo " <li><a href=members.php>List Members</a></li>";
            if ($_SESSION['permissions'] & $perms['VIEW_DROIDS']) echo " <li><a href=list_droids.php>List Droids</a></li>";
            if ($_SESSION['permissions'] & $perms['VIEW_MAP']) echo " <li><a href=map.php>Members Map</a></li>";
            if ($_SESSION['permissions'] & $perms['EDIT_CONFIG']) echo " <li><a href=edit_config.php>Edit Config</a></li>";
            if ($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS']) echo " <li><a href=edit_permissions.php>Edit Permissions</a></li>";
            if ($_SESSION['permissions'] & $perms['DUMP_DATA']) echo " <li><a target='_blank' href=dump_id.php>Dump ID info</a></li>";
            if (($_SESSION['permissions'] & $perms['EDIT_PLI']) && ($config->site_options & $options['INSURANCE'])) echo " <li><a href=edit_pli.php>Edit PLI</a></li>";
            if (($_SESSION['permissions'] & $perms['EDIT_ACHIEVEMENTS']) && ($config->site_options & $options['ACHIEVEMENTS'])) echo " <li><a href=achievements.php>Edit Achievements</a></li>";
            echo " </ul>";
        }


?>
</div>

<!-- Use any element to open the sidenav -->
<span onclick="openNav()"><font face=StarWars><< Main Menu</font></span>

<!-- Add all page content inside this div if you want the side nav to push page content to the right (not used if you only want the sidenav to sit on top of the page -->

<hr>

