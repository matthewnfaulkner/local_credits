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

namespace local_credits\output;

use moodle_page;
use moodle_url;
use renderable;
use renderer_base;
use single_button;
use templatable;

/**
 * Class standard_action_bar - Display the action bar
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class standard_action_bar implements renderable, templatable {

    /** @var int $badgeid Id of the the badge*/
    protected $badgeid;

    /** @var bool $showmanage Whether or not to show the manage badges button. */
    protected $showmanage;

    /** @var bool $showaddbadge Whether or not to show the add badges button. */
    protected $showaddbadge;

    /** @var moodle_url $backurl BackURL to be used when the back button is required. */
    protected $backurl;

    /**
     * standard_action_bar constructor
     *
     * @param moodle_page $page The page object
     * @param int $type The type of badge we are operating with
     * @param bool $showmanage Whether or not to show the manage badges button
     * @param bool $showaddbadge Whether or not to show the add badges button
     * @param moodle_url|null $backurl The backurl to be used
     */
    public function __construct(int $badgeid = 0, bool $showmanage = true,
            $showaddbadge = true, ?moodle_url $backurl = null) {

        
        $this->badgeid = $badgeid;
        $this->showmanage = $showmanage;
        $this->showaddbadge = $showaddbadge;
        $this->backurl = $backurl;
    }

    /**
     * The template that this tertiary nav should use.
     *
     * @return string
     */
    public function get_template(): string {
        return 'local_credits/manage_credits';
    }

    /**
     * Export the action bar
     *
     * @param renderer_base $output
     * @return array The buttons to be rendered
     */
    public function export_for_template(renderer_base $output): array {
        global $PAGE;

        $buttons = [];
        if ($this->backurl) {
            $buttons[] = new single_button($this->backurl, get_string('back'), 'get');
        }

        $params = ['badgeid' => $this->badgeid];

        if ($PAGE->context->contextlevel == CONTEXT_COURSE) {
            $params['id'] = $PAGE->context->instanceid;
        }

        if ($this->showmanage) {
            $buttons[] = new single_button(new moodle_url('/local_credits/index.php', $params),
                get_string('managecredits', 'local_credits'), 'get');
        }

        if ($this->showaddbadge && has_capability('local/credits:editcredits', $PAGE->context)) {
            $buttons[] = new single_button(new moodle_url('/local/credits/edit.php', $params),
                get_string('newcredit', 'local_Credits'), 'post', single_button::BUTTON_PRIMARY);
        }

        foreach ($buttons as $key => $button) {
            $buttons[$key] = $button->export_for_template($output);
        }

        $data = ['buttons' => $buttons];

        return $data;
    }
}
