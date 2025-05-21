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
 * Edit form for credits
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/local/subscriptions/lib.php');
/**
 * Create/Edit a credit
 *
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_credit_form extends moodleform {

        
    function definition () {
        global $DB;

        $mform = $this->_form;

        $edit = $this->_customdata['creditid'];
        $badgeid = $this->_customdata['badgeid'];
        
        $mform->addElement('header', 'adduserheader', get_string('adduserheader', 'auth_apoa'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="50" size="25"');
        $mform->setType('name', PARAM_ALPHANUM);
        $mform->addRule('name', get_string('missingname'), 'required', null, 'client');

        $mform->addElement('checkbox', 'enabled', get_string('creditenabled', 'local_credits'));
        $mform->setDefault('enabled', 1);

        $badgessql = "SELECT b.id, b.name FROM {badge} b
                      LEFT JOIN {badge_issued} bi on b.id = bi.badgeid
                      WHERE bi.badgeid IS NULL OR bi.badgeid = :badgeid
                      ";
        $params = [
            'badgeid' => $badgeid
        ];

        $badgeoptions = $DB->get_records_sql_menu($badgessql, $params);
        $badgeoptions[0] = '';
        $mform->addElement('autocomplete', 'badgeid', get_string('badge', 'local_credits'), $badgeoptions);
        $mform->setDefault('badgeid', 0);
        $mform->addRule('badgeid', null, 'required', null, 'client');
        $mform->addHelpButton('badgeid', 'badge', 'local_credits');

        $mform->addElement('text', 'price', get_string('price', 'local_credits'), 'maxlength="4"');
        $mform->setType('price', PARAM_INT);
        $mform->addRule('price', get_string('missingprice', 'local_credits'), 'required' , null, 'client');

        $possiblecurrencies = $this->get_possible_currencies();

        $mform->addElement('select', 'currency', get_string('currency', 'local_credits'), $possiblecurrencies);
        $mform->setDefault('currency', 'USD');
        $mform->setType('currency', PARAM_ALPHA);
        $mform->addRule('currency', get_string('missingcurrency', 'local_credits'), 'required' , null, 'client');

        $mform->addElement('text', 'maxissues', get_string('maxissues', 'local_credits'), 'maxlength="10"');
        $mform->setType('maxissues', PARAM_INT);
        $mform->setDefault('maxissues', 0);
        $mform->addHelpButton('maxissues', 'maxissues', 'local_credits');


        if ($edit == 0) {
            $btnstring = get_string('createcredit', 'local_credits');
        } else {
            $btnstring = get_string('updatecredit', 'local_credits');
        }

        $this->add_action_buttons(true, $btnstring);
    }


        /**
     * Returns the list of currencies that the payment subsystem supports and therefore we can work with.
     *
     * @return array[currencycode => currencyname]
     */
    public function get_possible_currencies(): array {
        $codes = \core_payment\helper::get_supported_currencies();

        $currencies = [];
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }

        uasort($currencies, function($a, $b) {
            return strcmp($a, $b);
        });

        return $currencies;
    }
    
    function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);

        $badgeid = $data['badgeid'];

        if($DB->record_exists('badge_issued', array('badgeid' => $badgeid))){
            $errors['badgeid'] = get_string('badgealreadyissued', 'local_credits');
        }

        if($credit = $DB->get_record('local_credits', array('badgeid' => $badgeid))) {
            if($data['id'] != $credit->id){
                $errors['badgeid'] = get_string('creditforbadgealreadyexists', 'local_credits');
            }
        }

        if($DB->record_exists('local_credits_issued', array('creditid' => $data['id']))){
            $errors['name'] = get_string('cannoteditissuedcredit', 'local_credits');
        }


        return $errors;
    }
}



