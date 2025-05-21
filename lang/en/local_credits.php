<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_credits
 * @category    string
 * @copyright   2022 Matthew<matthewfaulkner@apoaevents.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['credits:editcredits'] = 'Edit Credits';
$string['pluginname'] = 'Local Credits';

$string['managebadgecredits'] = 'Managing Credits for badge: {$a}';
$string['managebadgecredits_help'] = 'Credits can be created to offer 
    additional rewards to a user when they receive a badge.
    The reward is in the form of a cash credit that can be used on future purchases.
    Only one credit per badge is permitted.\n
    Once a badge has been awarded you cannot edit or add a credit, 
    so be sure to create both badge and credit before enabling the badge.\n
    Once a credit has been awarded you cannot edit it.\n
    A user can only receive a credit once, even if they somehow earn the same badge twice.';
$string['manageallcredits'] = 'Manage All Credits';
$string['manageallcredits_help'] = 'Credits can be created to offer 
additional rewards to a user when they receive a badge.
The reward is in the form of a cash credit that can be used on future purchases.
Only one credit per badge is permitted.\n
Once a badge has been awarded you cannot edit or add a credit, 
so be sure to create both badge and credit before enabling the badge.\n
Once a credit has been awarded you cannot edit it.\n
A user can only receive a credit once, even if they somehow earn the same badge twice.';
$string['managecredits'] = 'Manage Credits';

$string['badge'] = 'Badge';
$string['badge_help'] = 'Users earning this badge will also receve this credit.';
$string['recipients'] = 'Recipients';
$string['price'] = 'Credit Amount';
$string['enable'] = 'Enable Credit';
$string['disable'] = 'Disable Credit';
$string['creditenabled'] = 'Enable Credit';
$string['currency'] = 'Currency';
$string['maxissues'] = 'Maximum Issues';
$string['maxissues_help'] = 'Limit the amount of times this credit can be issued.';
$string['newcredit'] = 'Add New Credit';
$string['createcredit'] = 'Create new credit';
$string['updatecredit'] = 'Updating credit';
$string['nocredits'] = 'No credits found.';
$string['editcreditsuccessful'] = "Credit Successfully Updated";

$string['delcredit'] = 'Delete Credit: {$a}';
$string['deletehelp'] = 'Are you sure you want to delete this credit?\n Deleting a credit also deletes any record of it being issued.\n 
It might be better to simply disable the credit, this way any record of the credit being issued is kept.';
$string['delconfirm'] = 'Confirm Delete Credit';
