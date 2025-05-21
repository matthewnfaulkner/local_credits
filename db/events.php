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
 * Event definitions for local_credits plugin
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => '\core\event\badge_awarded',
        'includefile' => '/local/credits/locallib.php',
        'callback' => 'local_credits_observer::badge_awarded',
    ),
    array(
        'eventname' => '\core\event\badge_deleted',
        'includefile' => '/local/credits/locallib.php',
        'callback' => 'local_credits_observer::badge_deleted',
    ),
);