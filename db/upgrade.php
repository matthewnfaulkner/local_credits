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
 * Plugin upgrade steps are defined here.
 *
 * @package     local_credits
 * @category    upgrade
 * @copyright   2022 Matthew<matthewfaulkner@apoaevents.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute local_credits upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_credits_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.


    if ($oldversion < 2025042401) {

        // Define table local_credits to be created.
        $table = new xmldb_table('local_credits');

        // Adding fields to table local_credits.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('price', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('currency', XMLDB_TYPE_CHAR, '3', null, null, null, 'USD');
        $table->add_field('limit', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('issues', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table local_credits.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('secondary', XMLDB_KEY_UNIQUE, ['badgeid']);

        // Conditionally launch create table for local_credits.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Credits savepoint reached.
        upgrade_plugin_savepoint(true, 2025042401, 'local', 'credits');
    }
    
    if ($oldversion < 2025042402) {

        // Define field timecreated to be added to local_credits.
        $table = new xmldb_table('local_credits');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '13', null, null, null, null, 'issues');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '13', null, null, null, null, 'timecreated');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Credits savepoint reached.
        upgrade_plugin_savepoint(true, 2025042402, 'local', 'credits');
    }

    if ($oldversion < 2025042403) {

        // Define table local_credits_issued to be created.
        $table = new xmldb_table('local_credits_issued');

        // Adding fields to table local_credits_issued.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('creditid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '13', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_credits_issued.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_credits_issued.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field enabled to be added to local_credits.
        $table = new xmldb_table('local_credits');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'badgeid');

        // Conditionally launch add field enabled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        
        // Credits savepoint reached.
        upgrade_plugin_savepoint(true, 2025042403, 'local', 'credits');
    }

    if ($oldversion < 2025042404) {

        // Define field issues to be dropped from local_credits.
        $table = new xmldb_table('local_credits');
        $field = new xmldb_field('issues');

        // Conditionally launch drop field issues.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('limit', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'currency');

        // Launch rename field maxissues.
        $dbman->rename_field($table, $field, 'maxissues');

        // Credits savepoint reached.
        upgrade_plugin_savepoint(true, 2025042404, 'local', 'credits');
    }


    return true;
}
