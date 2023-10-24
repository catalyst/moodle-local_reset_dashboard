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
 * Unit tests for observer.
 *
 * @package   local_reset_dashboard
 * @author    Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_reset_observer_test extends advanced_testcase {

    /**
     * Initial set up.
     */
    protected function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot . '/my/lib.php');

        $this->resetAfterTest(true);
    }

    /**
     * Test that dash board is cleaned when a user logs in and "need_reset_dashboard" is not set.
     */
    public function test_reset_user_dashboard_on_user_loginas_when_preference_is_not_set() {
        global $DB;

        $this->setUser(0);
        $user = self::getDataGenerator()->create_user();
        $usermy = my_copy_page($user->id);

        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertNull(get_user_preferences('need_reset_dashboard', null, $user->id));
        $loginuser = clone($user);

        // First time login, the dashboard should be the default one.
        // Reset Dashboard will not run and preference is not updated.
        @complete_user_login($loginuser);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertNull(get_user_preferences('need_reset_dashboard', null, $user->id));

        // Lastlogin timestamp only is updated at the next user login (not loginas).
        // Reset Dashboard will not run and preference is not updated.
        $this->setAdminUser();
        \core\session\manager::loginas($user->id, context_system::instance());
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertNull(get_user_preferences('need_reset_dashboard', null, $user->id));

        // Second login. Reset Dashboard will run since need_reset_dashboard is not set
        @complete_user_login($loginuser);
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        unset_user_preference('need_reset_dashboard', $user->id);
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertNull(get_user_preferences('need_reset_dashboard', null, $user->id));

        // Reset Dashboard will run as the preference is not set.
        $this->setAdminUser();
        \core\session\manager::loginas($user->id, context_system::instance());
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));
    }

    /**
     * Test that dash board is not cleaned when a user logs in and "need_reset_dashboard" is set to 0.
     */
    public function test_reset_user_dashboard_on_user_loginas_when_preference_is_set_to_0() {
        global $DB;
        $this->setUser(0);
        $user = self::getDataGenerator()->create_user();
        set_user_preference('need_reset_dashboard', 0, $user->id);
        $usermy = my_copy_page($user->id);

        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));
        $loginuser = clone($user);

        //First time login, the dashboard should be the default one. Reset Dashboard will not run.
        @complete_user_login($loginuser);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Lastlogin timestamp only is updated at the next user login (not loginas).
        // Reset Dashboard will not run and preference is not updated.
        $this->setAdminUser();
        \core\session\manager::loginas($user->id, context_system::instance());
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Second login. Reset Dashboard will not run since need_reset_dashboard is to 0
        @complete_user_login($loginuser);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Reset Dashboard will not run as the preference is set to 0.
        $this->setAdminUser();
        \core\session\manager::loginas($user->id, context_system::instance());
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

    }

    /**
     * Test that dash board is cleaned when a user logs in and "need_reset_dashboard" is set to 1.
     */
    public function test_reset_user_dashboard_on_user_loginas_when_preference_is_set_to_1() {
        global $DB;
        $this->setUser(0);
        $user = self::getDataGenerator()->create_user();
        set_user_preference('need_reset_dashboard', 1, $user->id);
        $usermy = my_copy_page($user->id);

        $this->assertEquals(1,  get_user_preferences('need_reset_dashboard', null, $user->id));
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $loginuser = clone($user);

        //First time login, the dashboard should be the default one. Reset Dashboard will not run.
        @complete_user_login($loginuser);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(1,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Lastlogin timestamp only is updated at the next user login (not loginas).
        // Reset Dashboard will not run and preference is not updated.
        $this->setAdminUser();
        \core\session\manager::loginas($user->id, context_system::instance());
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(1,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Second login. Reset Dashboard will run since need_reset_dashboard is to 1
        @complete_user_login($loginuser);
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        set_user_preference('need_reset_dashboard', 1, $user->id);
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(1,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Reset Dashboard will run since the preference is set to 1.
        $this->setAdminUser();
        \core\session\manager::loginas($user->id, context_system::instance());
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));
    }

    /**
     * Test that dash board is cleaned when a user logs in and "need_reset_dashboard" is not set.
     */
    public function test_reset_user_dashboard_on_user_login_when_preference_is_not_set() {
        global $DB;

        $this->setUser(0);
        $user = self::getDataGenerator()->create_user();
        $usermy = my_copy_page($user->id);

        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertNull(get_user_preferences('need_reset_dashboard', null, $user->id));
        $loginuser = clone($user);

        //First time login, the dashboard should be the default one. Reset Dashboard will not run.
        @complete_user_login($loginuser); // Hide session header errors.
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertNull(get_user_preferences('need_reset_dashboard', null, $user->id));

        // Second login. Reset Dashboard will run since need_reset_dashboard is not set
        @complete_user_login($loginuser);
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));
    }

    /**
     * Test that dash board is not cleaned when a user logs in and "need_reset_dashboard" is set to 0.
     */
    public function test_reset_user_dashboard_on_user_login_when_preference_is_set_to_0() {
        global $DB;

        $this->setUser(0);
        $user = self::getDataGenerator()->create_user();
        set_user_preference('need_reset_dashboard', 0, $user->id);
        $usermy = my_copy_page($user->id);

        $loginuser = clone($user);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        //First time login, the dashboard should be the default one. Reset Dashboard will not run.
        @complete_user_login($loginuser); // Hide session header errors.
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Second login. Reset Dashboard will not run since need_reset_dashboard is set 0
        @complete_user_login($loginuser);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));

    }

    /**
     * Test that dash board is cleaned when a user logs in and "need_reset_dashboard" is set to 1.
     */
    public function test_reset_user_dashboard_on_user_login_when_preference_is_set_to_1() {
        global $DB;

        $this->setUser(0);
        $user = self::getDataGenerator()->create_user();
        set_user_preference('need_reset_dashboard', 1, $user->id);
        $usermy = my_copy_page($user->id);

        $loginuser = clone($user);
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(1,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // First time login, the dashboard should be the default one. Reset Dashboard will not run.
        @complete_user_login($loginuser); // Hide session header errors.
        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(1,  get_user_preferences('need_reset_dashboard', null, $user->id));

        // Second login. Reset Dashboard will run since need_reset_dashboard is set to 1
        @complete_user_login($loginuser);
        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
        $this->assertEquals(0,  get_user_preferences('need_reset_dashboard', null, $user->id));
    }
}
