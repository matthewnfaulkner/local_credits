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
 * Credits Management renderder
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_credits\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');

use renderable;

/**
 * Collection of credits used at the index.php page
 *
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class credit_management implements renderable {

        /** @var string how are the data sorted */
        public $sort = 'name';

        /** @var string how are the data sorted */
        public $dir = 'ASC';
    
        /** @var int page number to display */
        public $page = 0;
    
        /** @var int number of badges to display per page */
        public $perpage = BADGE_PERPAGE;
    
        /** @var int the total number of badges to display */
        public $totalcount = null;
    
        /** @var array list of badges */
        public $credits = array();
    
        /**
         * Initializes the list of badges to display
         *
         * @param array $badges Badges to render
         */
        public function __construct($credits) {
            $this->credits = $credits;
        }
}

