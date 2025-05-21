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
 * Page for editing credits
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__.'/edit_form.php');
require_once(__DIR__.'/lib.php');

$creditid  = optional_param('creditid', 0, PARAM_INT);
$badgeid   = optional_param('badgeid', 0, PARAM_INT);

$heading = get_string('createcredit', 'local_credits');

if($creditid){
    $credit = $DB->get_record('local_credits', array('id' => $id), '*', MUST_EXIST);
    $heading = get_string('updatecredit', 'local_credits');
}
else if($badgeid){
    $badge = $DB->get_record('badge', array('id' => $badgeid), '*', MUST_EXIST);
    if(!$credit = $DB->get_record('local_credits', array('badgeid' => $badgeid))){
        $credit = ['badgeid' => $badgeid];
    }else{
        $heading = get_string('updatecredit', 'local_credits');
    }
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('administrationsite'));
navigation_node::override_active_url(new moodle_url('/local/credits/index.php'), true);

$mform = new edit_credit_form(null, array('creditid' => $credit->id, 'badgeid' => $credit->badgeid));
$mform->set_data($credit);

echo $OUTPUT->header();

if($formdata = $mform->get_data()){
    if($formdata->id){
        local_credits_update_credit($formdata);
        $eventdata = array (
            'context' => \context_system::instance(),
            'objectid' => $formdata->id,
            'relateduserid' => $USER->id,
        );

        $e = \local_credits\event\local_credits_updated::create($eventdata);
        $e->trigger();
    }
    else{
        if($newcreditid = local_credits_create_credit($formdata)){
            
            $eventdata = array (
                'context' => \context_system::instance(),
                'objectid' => $newcreditid,
                'relateduserid' => $USER->id,
            );
            $e = \local_credits\event\local_credits_created::create($eventdata);
            $e->trigger();
        }

    }
    echo $OUTPUT->heading(get_string('editcreditsuccessful', 'local_credits'));
    echo $OUTPUT->single_button("$CFG->wwwroot/local/credits/index.php?badgeid=$badgeid", 'View Credits');
    echo $OUTPUT->footer();

    die;

}

echo $OUTPUT->heading($heading);

echo $mform->display();

echo $OUTPUT->footer();
die;
