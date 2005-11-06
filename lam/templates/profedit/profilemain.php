<?php
/*
$Id$

  This code is part of LDAP Account Manager (http://www.sourceforge.net/projects/lam)
  Copyright (C) 2003  Roland Gruber

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/**
* This is the main window of the profile editor.
*
* @package profiles
* @author Roland Gruber
*/

/** helper functions for profiles */
include_once("../../lib/profiles.inc");
/** access to LDAP server */
include_once("../../lib/ldap.inc");
/** access to configuration options */
include_once("../../lib/config.inc");

// start session
session_save_path("../../sess");
@session_start();

setlanguage();

$profileClasses = array(
	array('scope' => 'user', 'title' => _('User profiles'), 'profiles' => ""),
	array('scope' => 'group', 'title' => _('Group profiles'), 'profiles' => ""),
	array('scope' => 'host', 'title' => _('Host profiles'), 'profiles' => "")
);

// check if user is logged in, if not go to login
if (!$_SESSION['ldap'] || !$_SESSION['ldap']->server()) {
	metaRefresh("../login.php");
	exit;
}

// on abort go back to main page
if (isset($_POST['abort'])) {
	metaRefresh("../tools.php");
	exit;
}
// check if user has pressed submit button
elseif (isset($_POST['submit'])) {
	// forward to other profile pages
	if ($_POST['submit']) {
		for ($i = 0; $i < sizeof($profileClasses); $i++) {
			// create new profile
			if ($_POST['profile'] == ("new" . $profileClasses[$i]['scope'])) {
				metaRefresh("profilepage.php?type=" . $profileClasses[$i]['scope']);
			}
			// edit profile
			elseif($_POST['profile'] == ("edit" . $profileClasses[$i]['scope'])) {
				metaRefresh("profilepage.php?type=" . $profileClasses[$i]['scope'] .
					"&amp;edit=" . $_POST['e_' . $profileClasses[$i]['scope']]);
			}
			// delete profile
			elseif($_POST['profile'] == ("del" . $profileClasses[$i]['scope'])) {
				metaRefresh("profiledelete.php?type=" . $profileClasses[$i]['scope'] .
					"&amp;del=" . $_POST['d_' . $profileClasses[$i]['scope']]);
			}
		}
	}
	exit;
}

// get list of profiles for each account type
for ($i = 0; $i < sizeof($profileClasses); $i++) {
	$profileList = getAccountProfiles($profileClasses[$i]['scope']);
	$profiles = "";
	for ($l = 0; $l < sizeof($profileList); $l++) {
		$profiles = $profiles . "<option>" . $profileList[$l] . "</option>\n";
	}
	$profileClasses[$i]['profiles'] = $profiles;
}

echo $_SESSION['header'];


echo "<title>LDAP Account Manager</title>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../../style/layout.css\">\n";
echo "</head>\n";
echo "<body>\n";

echo "<p></p>\n";

echo "<form action=\"profilemain.php\" method=\"post\">\n";

for ($i = 0; $i < sizeof($profileClasses); $i++) {

	echo "<fieldset>\n";
	echo "<legend>\n";
	echo "<b>" . $profileClasses[$i]['title'] . "</b>\n";
	echo "</legend>\n";
	echo "<table border=0>\n";
	
	// new profile
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type=\"radio\" name=\"profile\" value=\"new" . $profileClasses[$i]['scope'] . "\">\n";
	echo "</td>\n";
	echo "<td colspan=2>" . _("Create a new profile") . "</td>\n";
	echo "</tr>\n";
	
	// edit profile
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type=\"radio\" name=\"profile\" value=\"edit" . $profileClasses[$i]['scope'] . "\">\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<select name=\"e_" . $profileClasses[$i]['scope'] . "\" size=1>\n";
	echo $profileClasses[$i]['profiles'];
	echo "</select>\n";
	echo "</td>\n";
	echo "<td>" . _("Edit profile") . "</td>\n";
	echo "</tr>\n";
	
	// delete profile
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type=\"radio\" name=\"profile\" value=\"del" . $profileClasses[$i]['scope'] . "\">\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "<select name=\"d_" . $profileClasses[$i]['scope'] . "\" size=1>\n";
	echo $profileClasses[$i]['profiles'];
	echo "</select>\n";
	echo "</td>\n";
	echo "<td>" . _("Delete profile") . "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</fieldset>\n";
	
	echo "<p></p>\n";
	
}


echo "<p>\n";
echo "<input type=\"submit\" name=\"submit\" value=\"" . _("Submit") . "\">\n";
echo "<input type=\"submit\" name=\"abort\" value=\"" . _("Abort") . "\">\n";
echo "</p>\n";

echo "</form>\n";
echo "</body>\n";
echo "</html>\n";

?>
