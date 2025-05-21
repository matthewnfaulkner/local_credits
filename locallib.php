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
 * Local functions for local_credits plugin
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



defined('MOODLE_INTERNAL') || die();

use local_shopping_cart\shopping_cart_credits;

/**
 * Event observer for local_credits.
 *
 * @package    local_credits
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_credits_observer {

    /**
     * On badge awarded, award any un issued associated credits
     *
     * @param \core\event\badge_awarded $event
     * @return void
     */
    public static function badge_awarded(\core\event\badge_awarded $event){
        global $DB;
        
        $data = $event->get_data();

        $badgeid = $data['objectid'];
        $userid = $data['relateduserid'];

        $sql = "SELECT lc.* FROM {local_credits} lc LEFT JOIN 
                {local_credits_issued} li ON lc.id = li.creditid
                WHERE (li.userid <> :userid OR li.userid IS NULL) AND lc.enabled = :isenabled
                AND lc.badgeid = :badgeid";
        
        $params = [
            'userid' => $userid,
            'isenabled' => 1,
            'badgeid' => $badgeid
        ];

        if($credit = $DB->get_record_sql($sql, $params)){
            shopping_cart_credits::add_credit($userid, $credit->price, $credit->currency);

            $creditissued = [
                'userid' => $userid,
                'creditid' => $credit->id,
                'timecreated' => time()
            ];

            $id = $DB->insert_record('local_credits_issued', $creditissued);

            $eventdata = [
                'context' => \context_system::instance(),
                'objectid' => $id,
                'relateduserid' => $userid
            ];

            $e =  \local_credits\event\local_credits_awarded::create($eventdata);

            $e->trigger();
        }

    }

    /**
     * On badge deleted disable any associated credits.
     *
     * @param \core\event\badge_deleted $event
     * @return void
     */
    public static function badge_deleted(\core\event\badge_deleted  $event){
        global $DB;

        $data = $event->get_data();

        $badgeid = $data['objectid'];
        $userid = $data['relateduserid'];

        if($credit = $DB->get_record('local_credits', array('badgeid' => $badgeid))){
            $credit->enabled = 0;
            $DB->update_record('local_credits', $credit);

            $eventdata = [
                'context' => \context_system::instance(),
                'objectid' => $credit->id,
                'relateduserid' => $userid
            ];

            $e = \local_credits\event\local_credits_updated::create($eventdata);

            $e->trigger();
        }
    }

}


