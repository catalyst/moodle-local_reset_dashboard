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
 * Class to that contain functionality to reset dasboard.
 *
 * @package   local_reset_dashboard
 * @author    Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_reset_dashboard;

defined('MOODLE_INTERNAL') || die();


class resetter implements resetter_interface {

    /**
     * Constructor.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->dirroot . '/my/lib.php');
    }

    /**
     * @inheritdoc
     */
    public function reset_dashboard_for_user($userid) {
        try {
            $result = my_reset_page($userid);
        } catch (\Exception $e) {
            $result = false;
        }

        return !empty($result);
    }

    /**
     * @inheritdoc
     */
    public function reset_dashboard_for_all_users($batchsize = 5000) {
        try {
            my_reset_page_for_all_users();
            $result = true;
        } catch (\Exception $e) {
            $result = false;
        }

        return !empty($result);
    }

}
