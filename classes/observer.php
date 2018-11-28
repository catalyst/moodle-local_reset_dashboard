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
 * Event observers.
 *
 * @package   local_reset_dashboard
 * @author    Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_reset_dashboard;

defined('MOODLE_INTERNAL') || die();

class observer {
    /**
     * USer preference name to find out if we need to reset dashboard for a user.
     */
    const RESET_DASHBOARD_PREFERENCE = 'need_reset_dashboard';

    /**
     * Reset user dashboard on login if required.
     *
     * @param \core\event\user_loggedin $event
     */
    public static function reset_user_dashboard(\core\event\user_loggedin $event) {
        if (self::need_reset_dashboard($event->userid)) {
            $resetter = new resetter();
            if ($resetter->reset_dashboard_for_user($event->userid)) {
                set_user_preference(self::RESET_DASHBOARD_PREFERENCE, 0, $event->userid);
            }
        }
    }

    /**
     * Check if we need to reset dashboard for provided user.
     *
     * @param int $userid User ID.
     *
     * @return bool
     */
    protected static function need_reset_dashboard($userid) {
        return get_user_preferences(self::RESET_DASHBOARD_PREFERENCE, 1, $userid);
    }

}
