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
 *  Unit tests for resetter class.
 *
 * @package   local_reset_dashboard
 * @author    Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_reset_dashboard\resetter;

defined('MOODLE_INTERNAL') || die();

class local_reset_dashboard_resetter_test extends advanced_testcase {

    /**
     * Test resetter.
     *
     * @var \local_reset_dashboard\resetter
     */
    protected $resetter;

    /**
     * Initial set up.
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/my/lib.php');

        $this->resetAfterTest(true);
        $this->resetter = new resetter();
    }

    /**
     * Test reset dashboard for a user.
     */
    public function test_reset_dashboard_for_user() {
        global $DB;
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);

        $usermy = my_copy_page($user->id);

        $this->assertEquals(1, $DB->count_records('my_pages', ['id' => $usermy->id]));

        $this->resetter->reset_dashboard_for_user($user->id);

        $this->assertEquals(0, $DB->count_records('my_pages', ['id' => $usermy->id]));
    }

    /**
     * Test the dashboard reset event gets triggered.
     */
    public function test_dashboard_reset_event_triggers_when_reset_dashboard_for_user() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $sink = $this->redirectEvents();

        $result = $this->resetter->reset_dashboard_for_user($user->id);

        $events = $sink->get_events();
        $event = reset($events);

        $this->assertTrue($result);
        $this->assertInstanceOf('\core\event\dashboard_reset', $event);
        $this->assertEquals($user->id, $event->userid);
        $this->assertEquals(MY_PAGE_PRIVATE, $event->other['private']);
        $this->assertEquals('my-index', $event->other['pagetype']);
        $this->assertDebuggingNotCalled();
    }

}
