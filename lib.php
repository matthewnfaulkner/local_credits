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
 * Library functions for local_credits plugin
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

define('CREDIT_STATUS_DISABLED', 0);
define('CREDIT_STATUS_ENABLED', 1);

/**
 * Create a new credit instance from form data
 *
 * @param stdClass $formdata data submitted from form
 * @return bool|int — true or new id of created credit
 * @throws Exception If credit already exists
 */
function local_credits_create_credit(stdClass $formdata){
    global $DB;

    $credit = $formdata;

    //add time information
    $credit->timecreated = time();
    $credit->timemodified =  $credit->timecreated;

    //check if badge already has credit
    if($DB->record_exists('local_credits', array('badgeid' => $credit->badgeid))){
        throw new exception('credit for badge already exists');
    }

    return $DB->insert_record('local_credits', $credit);
}

/**
 * Update an existing credit
 *
 * @param stdClass $formdata
 * @return bool|int — true or new id of created credit
 * @throws Exception If credit already exists
 */
function local_credits_update_credit(stdClass $formdata){
    global $DB;

    $credit = $formdata;
    $credit->timemodified = time();

    //check if badge already has credit
    if($DB->record_exists('local_credits', array('badgeid' => $credit->badgeid))){
        throw new exception('credit for badge already exists');
    }

    $DB->update_record('local_credits', $credit);

}

/**
 * Delete a credit
 *
 * @param integer $creditid
 * @return void
 * @throws dml_exception — A DML specific exception is thrown for any errors.
 */
function local_credits_delete_credit(int $creditid){
    global $DB, $USER;

    //delete credit and issued credits
    $DB->delete_records('local_credits', array('id' => $creditid));
    $DB->delete_records('local_credits_issued', array('creditid' => $creditid));
    
    $eventdata = [
        'context' => \context_system::instance(),
        'objectid' => $creditid,
        'relateduserid' => $USER->id
    ];
    $e =  \local_credits\event\local_credits_deleted::create($eventdata);
    $e->trigger();

} 

/**
 * Get Credits that match criteria
 *
 * @param integer $badgeid Id of badge, 0 gets all badges
 * @param string $sort field to sort by
 * @param string $dir direction of sort ASC or DESC
 * @param integer $page current page
 * @param integer $perpage credits per page
 * @return array — of credits indexed by first id
 */
function local_credits_get_credits(
                    int $badgeid = 0, 
                    string $sort = '', 
                    string $dir = '', 
                    int $page = 0, 
                    int $perpage = 10){
    global $DB;

    if(!$sort) {
        $sort = 'badgeid';
        $dir = 'ASC';
    }

    $selects = 'lc.id <> 0';
    $params = [];

    if($badgeid){
        $selects .= ' AND lc.badgeid = :badgeid';
        $params = [
            'badgeid' => $badgeid,
        ];
    }

    $subquery = "SELECT lc.*, GROUP_CONCAT(u.email SEPARATOR '\n') AS emails, COUNT(*) AS matchcount FROM {local_credits_issued} lc INNER JOIN 
                {user} u on lc.userid = u.id
                GROUP BY lc.creditid";

    $sql = "SELECT lc.*, b.name as badge, COALESCE(li.matchcount, 0) AS match_count, li.emails FROM {local_credits} lc
            LEFT JOIN {badge} b on lc.badgeid = b.id
            LEFT JOIN ($subquery) li ON lc.id = li.creditid
            WHERE $selects
            ORDER BY $sort $dir";

    return $DB->get_records_sql($sql, $params, $perpage * $page, $perpage);
}

/**
 * Enable/Disable credit
 *
 * @param stdClass $credit to disable
 * @return void
 */
function local_credits_disable_credit(stdClass $credit) {
    global $DB;

    $credit->enabled = $credit->enabled ? 0 : 1;

    $DB->update_record('local_credits', $credit);
}