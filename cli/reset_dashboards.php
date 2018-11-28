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
 * @package   local_reset_dashboard
 * @author    Matt Porritt (mattp@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir.'/adminlib.php');


cli_writeln(date("D M j Y G:i:s T", time())  . " - started resetting dashboards for all users ... ");

$resetter = new \local_reset_dashboard\resetter();
$resetter->reset_dashboard_for_all_users(0);

cli_writeln(date("D M j Y G:i:s T", time()) . " - completed resetting dashboards for all users.");

exit(0);
