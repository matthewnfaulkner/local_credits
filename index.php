<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Page for managing credits
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');

$creditid   = optional_param('creditid', 0, PARAM_INT);
$badgeid    = optional_param('badgeid', 0, PARAM_INT); 
$type       = optional_param('type', BADGE_TYPE_SITE, PARAM_INT); //badge type
$courseid   = optional_param('id', 0, PARAM_INT);
$page       = optional_param('page', 0, PARAM_INT); 
$sortby     = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'ASC', PARAM_ALPHA);
$delete     = optional_param('delete', 0, PARAM_INT);
$enable     = optional_param('enable', false, PARAM_BOOL);
$confirm    = optional_param('confirm', 0, PARAM_INT);

admin_externalpage_setup('local_credits_editcredits');

if (!in_array($sortby, array('name', 'status'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'ASC';
}

if ($page < 0) {
    $page = 0;
}

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

if (empty($CFG->badges_allowcoursebadges) && ($type == BADGE_TYPE_COURSE)) {
    throw new \moodle_exception('coursebadgesdisabled', 'badges');
}

$err = '';
$urlparams = array('sort' => $sortby, 'dir' => $sorthow, 'page' => $page);

//badge specific credits or all.
if($badgeid){
    $badge = $DB->get_record('badge', array('id' => $badgeid));
    $hdr = get_string('managebadgecredits', 'local_credits', $badge->name);
    $type = $badge->type;
}else{
    $hdr = get_string('manageallcredits', 'local_credits',);
}

$returnurl = new moodle_url('/local/credits/index.php', $urlparams);
$PAGE->set_url($returnurl);
$PAGE->add_body_class('limitedwidth');


$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('administrationsite'));
navigation_node::override_active_url(new moodle_url('/local/credits/index.php'), true);


if (!has_any_capability(array(
        'local/credits:editcredits'), $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($hdr);
$output = $PAGE->get_renderer('local_credits');

if($creditid) {
    $credit = $DB->get_record('local_credits', array('id' => $creditid)); 
}

if(($enable) && has_capability('local/credits:editcredits', $PAGE->context) && !is_null($credit)) {
    require_sesskey();

    //enable/disable credit
    local_credits_disable_credit($credit);
    redirect($returnurl);
}

if (($delete) && has_capability('local/credits:editcredits', $PAGE->context) && !is_null($credit)) {
    
    if (!$confirm) {
        echo $output->header();
    
        // Delete this credit?
        echo $output->heading(get_string('delcredit', 'local_credits', $credit->name));
        $deletebutton = $output->single_button(
                            new moodle_url('/local/credits/index.php', array('creditid' => $credit->id, 'delete' => $credit->id, 'confirm' => 1)),
                            get_string('delconfirm', 'local_credits'));
        echo $output->box(get_string('deletehelp', 'local_credits') . $deletebutton, 'generalbox');

        // Go back.
        echo $output->action_link($returnurl, get_string('cancel'));

        echo $output->footer();
        die();
    } else {
        require_sesskey();
        local_credits_delete_credit($delete);
        redirect($returnurl);
    }
}

$totalcount = count(local_credits_get_credits($badgeid, '', '' , 0, 0));

//get credits
$records = local_credits_get_credits($badgeid, $sortby, $sorthow, $page, BADGE_PERPAGE);

$showadd = $totalcount > 0 && $badgeid ? false : true;

echo $OUTPUT->header();
$backurl =  new moodle_url('/badges/index.php', array('type' => $type));

$actionbar = new \local_credits\output\standard_action_bar($badgeid, false, $showadd, $backurl);
echo $output->render_tertiary_navigation($actionbar);

echo $OUTPUT->heading_with_help($hdr, 'managecredits', 'local_credits');

echo $OUTPUT->box('', 'notifyproblem hide', 'check_connection');


if ($totalcount) {

    $credits             = new \local_credits\output\credit_management($records);
    $credits->sort       = $sortby;
    $credits->dir        = $sorthow;
    $credits->page       = $page;
    $credits->perpage    = BADGE_PERPAGE;
    $credits->totalcount = $totalcount;

    echo $output->render($credits);
} else {
    echo $output->notification(get_string('nocredits', 'local_credits'), 'info');
}

echo $OUTPUT->footer();
