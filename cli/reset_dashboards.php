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
 * This simple CLI script goes through the site and recalculates
 * grades for all courses (default) or a single course
 *
 */
define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions.
require_once($CFG->dirroot . '/my/lib.php');
require_once("$CFG->libdir/blocklib.php");

// Raise the time limit.
core_php_time_limit::raise();

// Increase the memory limit.
raise_memory_limit(MEMORY_EXTRA);

mtrace("Resetting all users dashboards");

$private = MY_PAGE_PRIVATE;
$pagetype = 'my-index';

global $DB;

// Find all the user pages and all block instances in them.
$sql = "SELECT bi.id
        FROM {my_pages} p
        JOIN {context} ctx ON ctx.instanceid = p.userid AND ctx.contextlevel = :usercontextlevel
        JOIN {block_instances} bi ON bi.parentcontextid = ctx.id AND
            bi.pagetypepattern = :pagetypepattern AND
            (bi.subpagepattern IS NULL OR bi.subpagepattern = " . $DB->sql_concat("''", 'p.id') . ")
        WHERE p.private = :private";

$params = array('private' => $private,
    'usercontextlevel' => CONTEXT_USER,
    'pagetypepattern' => $pagetype);

error_log($sql);

$blockids = $DB->get_fieldset_sql($sql, $params);
echo "Found: ". count($blockids) . " block IDs \n";

mtrace("Deleting block instances");
// Delete the block instances.
if (!empty($blockids)) {
    blocks_delete_instances($blockids);
}

// Finally delete the pages.
mtrace("Deleting pages");
$DB->delete_records_select('my_pages', 'userid IS NOT NULL AND private = :private', ['private' => $private]);


// Trigger dashboard has been reset event.
$eventparams = array(
    'context' => context_system::instance(),
    'other' => array(
        'private' => $private,
        'pagetype' => $pagetype,
    ),
);
$event = \core\event\dashboards_reset::create($eventparams);
$event->trigger();

// This was moved from the upgrade script. It is safe to do it after.
mtrace("upgrading block positions");
upgrade_block_positions();

exit(0); // 0 means success.
