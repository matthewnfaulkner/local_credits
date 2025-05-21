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
 * local_credits Renderer
 *
 * @package   local_credits
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use moodle_url;
use html_writer;
use paging_bar;
use html_table;

defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/badges/renderer.php');

/**
 * Standard HTML output renderer for credits
 */
class local_credits_renderer extends \plugin_renderer_base {

    // Prints action icons for the badge.
    public function print_credit_table_actions($credit, $context) {
        $actions = "";

        if (has_capability('local/credits:editcredits', $context)) {
            // Activate/deactivate badge.
                $url = new \moodle_url(qualified_me());
                $url->param('creditid', $credit->id);
                $url->param('enable', true);
                $url->param('sesskey', sesskey());

                if($credit->enabled){
                    $actions .= $this->output->action_icon($url, new \pix_icon('t/show', get_string('disable', 'local_credits'))) . " ";
                }
                else{
                    $actions .= $this->output->action_icon($url, new \pix_icon('t/hide', get_string('disable', 'local_credits'))) . " ";
                }
            
        }

         // Edit badge.
         if (has_capability('local/credits:editcredits', $context) && !$credit->match_count) {
            $url = new moodle_url('/local/credits/edit.php', array('id' => $credit->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Delete badge.
        if (has_capability('local/credits:editcredits', $context)) {
            $url = new moodle_url(qualified_me(), array('creditid' => $credit->id, 'delete' => $credit->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
        }
        return $actions;
    }

        /**
     * Render a table of badges.
     *
     * @param \core_badges\output\badge_management $badges
     * @return string
     */
    protected function render_credit_management(\local_credits\output\credit_management $credits) {
        $paging = new paging_bar($credits->totalcount, $credits->page, $credits->perpage, $this->page->url, 'page');

        // New badge button.
        $htmlnew = '';
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'table table-bordered table-striped';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
             'name', $credits->sort, $credits->dir);
        $sortbybadge = $this->helper_sortable_heading(get_string('badge', 'local_credits'),
                'badge', $credits->sort, $credits->dir);
        $sortbyrecipients = $this->helper_sortable_heading(get_string('recipients', 'local_credits'),
                'match_count', $credits->sort, $credits->dir);
        $sortbyamount = $this->helper_sortable_heading(get_string('price', 'local_credits'),
                'price', $credits->sort, $credits->dir);
        $table->head = array(
                $sortbyname,
                $sortbybadge,
                $sortbyrecipients,
                $sortbyamount,
                get_string('actions')
            );
        $table->colclasses = array('name', 'enabled', 'badgename', 'recipients', 'actions');

        foreach ($credits->credits as $c) {
            $name = $c->name;
            if($c->badge){
                $badge = html_writer::link(new moodle_url('/badges/overview.php', array('id' => $c->badgeid)), $c->badge);
            }else{
                $badge = get_string('badgenolongerexists', 'local_credits');
            }
            $recipients = $c->match_count;
            $amount = $c->price . ' ' . $c->currency;
            $actions = self::print_credit_table_actions($c, $this->page->context);
            $row = array($name, $badge,$recipients, $amount, $actions);
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlnew . $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

        /**
     * Render the tertiary navigation for the page.
     *
     * @param \core_badges\output\base_action_bar $actionbar
     * @return bool|string
     */
    public function render_tertiary_navigation(\local_credits\output\standard_action_bar $actionbar) {
        return $this->render_from_template($actionbar->get_template(), $actionbar->export_for_template($this));
    }


        ////////////////////////////////////////////////////////////////////////////
    // Helper methods
    // Reused from stamps collection plugin
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders a text with icons to sort by the given column
     *
     * This is intended for table headings.
     *
     * @param string $text    The heading text
     * @param string $sortid  The column id used for sorting
     * @param string $sortby  Currently sorted by (column id)
     * @param string $sorthow Currently sorted how (ASC|DESC)
     *
     * @return string
     */
    protected function helper_sortable_heading($text, $sortid = null, $sortby = null, $sorthow = null) {
        $out = html_writer::tag('span', $text, array('class' => 'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid || $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'ASC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_asc', get_string('sortbyx', 'core', s($text)), null, array('class' => 'iconsort')));
            }
            if ($sortby !== $sortid || $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'DESC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_desc', get_string('sortbyxreverse', 'core', s($text)), null, array('class' => 'iconsort')));
            }
        }
        return $out;
    }

}
