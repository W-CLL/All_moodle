<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Recycle meetings
 *
 * @package     mod_zoom
 * @copyright   2019 ckf <m15220982078@163.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_zoom\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/zoom/locallib.php');

/**
 * Scheduled task to recycle meetings
 *
 * @package     mod_zoom
 * @copyright   2019 ckf <m15220982078@163.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recycle_meetings extends \core\task\scheduled_task {
    /**
     * Returns name of task.
     *
     * @return string
     */
    public function get_name() {
        return 'Recycle meetings';
    }

    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');
        require_once($CFG->dirroot.'/course/lib.php');

        ini_set('max_execution_time', '0');

        $service = new \mod_zoom_webservice();

        try {
            $users  = $service->list_users();
        } catch (\moodle_exception $e) {
            mtrace('Failed to get user list: ' . $e->getMessage());
            return 0;
        }
        $count  = 0;
        // $module = $DB->get_field('modules', 'id', ['name'=>'zoom']);
        foreach ($users as $user) {
            $temp   = [];
            $zoomid = '';
            //if (preg_match('/^zoom/', $user->email, $temp) || $user->email == '1140670017@qq.com') {
            if (preg_match('/^zoom/', $user->email, $temp)) {
                # Gets a list of user meetings
                try {
                    $meetings   = $service->list_meetings($user->id, false);
                } catch (\moodle_exception $e) {
                    mtrace("Failed to get user $user->id meeting list: " . $e->getMessage());
                    continue;
                }
                $user_meetings  = count($meetings);

                foreach ($meetings as $meeting) {
                    // try {
                    //     $meeting_info   = $service->get_meeting_webinar_info($meeting->id, false);
                    // } catch (\moodle_exception $e) {
                    //     mtrace("Failed to obtain meeting $meeting->id details: " . $e->getMessage());
                    //     continue;
                    // }
                    $expiration_time    = strtotime($meeting->start_time) + ($meeting->duration * 60);
                    $zoom               = $DB->get_record('zoom', ['meeting_id'=>$meeting->id]);

                    # Delete meeting that are not started
                    if ($expiration_time < time()) {
                        try {
                            // if ($zoom) {
                            //     # Delete zoom meeting and moodle activity
                            //     $cm = $DB->get_record('course_modules', ['instance'=>$zoom->id, 'module'=>$module]);
                            //     course_delete_module($cm->id);
                            // } else {
                            //     # Delete only zoom meeting when there are no moodle activity records locally
                            //     $service->delete_meeting($meeting_info->id, false);
                            // }
                            $service->delete_meeting($meeting->id, false);
                        } catch (\moodle_exception $e) {
                            mtrace("Meeting $meeting->id deletion failed!");
                            continue;
                        }
                        $count += 1;
                        $user_meetings -= 1;
                    }
                    if ($zoom) $zoomid = $zoom->id;
                }

                # Insert or update record
                $user_meeting_info = $DB->get_record('zoom_user_meeting', ['email'=>$user->email]);
                if ($user_meeting_info) {
                    $user_meeting_info->num     = $user_meetings;
                    $user_meeting_info->zoomid  = $zoomid;
                    $DB->update_record('zoom_user_meeting', $user_meeting_info);
                } else {
                    $user_meeting_info = new \stdClass();
                    $user_meeting_info->email   = $user->email;
                    $user_meeting_info->num     = $user_meetings;
                    $user_meeting_info->host_id = $user->id;
                    $user_meeting_info->zoomid  = $zoomid;
                    $user_meeting_info->coursename  = '';
                    $user_meeting_info->snum    = 0;
                    $user_meeting_info->uname   = '';
                    $DB->insert_record('zoom_user_meeting', $user_meeting_info);
                }
            }
        }
        mtrace("$count meeting deleted");
    }
}