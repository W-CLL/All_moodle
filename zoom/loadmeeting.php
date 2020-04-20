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
 * Load zoom meeting and assign grade to the user join the meeting.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $PAGE, $USER,$DB;
$config = get_config('mod_zoom');
// Course_module ID.
$id = required_param('id', PARAM_INT);
if ($id) {
    $cm         = get_coursemodule_from_id('zoom', $id, 0, false, MUST_EXIST);
    $course     = get_course($cm->course);
    $zoom  = $DB->get_record('zoom', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID');
}


require_login($course, true, $cm);
//$userishost = (zoom_get_user_id(false) == $zoom->host_id);
//第一个进入会议室的老师
$userishost = zoom_get_auth_start_meeting($course,$zoom);
$service = new mod_zoom_webservice();
if($userishost==2){
    //生成会议室
    if($zoom->meeting_id==-1){
        //首次生成
        $status = 1;
    }else{
        //已有记录，判断会议是否有效，有效则更新设置，无效则重新创建
        $resonse = $service->get_meeting_webinar_info($zoom->meeting_id, $zoom->webinar,false);
        if($resonse){
            //更新
            $status=2;
        }else{
            //重新生成
            $status = 1;
        }
    }
    if($status==1){
        $email = zoom_get_usable_email($zoom->course,false);
        if(!$email){
            //当前没有可用邮箱
            notice('提示：当期无可用会议室，请等候！',new moodle_url('/course/view.php', array('id' => $zoom->course)));
        }
        $zoomuser = $service->get_user($email);
        if ($zoomuser === false) {
            // Assume user is using Zoom for the first time.
            $errstring = 'zoomerr_usernotfound';
            // After they set up their account, the user should continue to the page they were on.
            $nexturl = $PAGE->url;
            throw new moodle_exception($errstring, 'mod_zoom', $nexturl, $config->zoomurl);
        }
        $zoom->host_id = zoom_get_usable_hostid($zoom->course,true);

        $start = $zoom->start_time;
        $zoom->start_time = zoom_get_starttime($zoom);
        $response = $service->create_meeting($zoom);
        $zoom = populate_zoom_from_response($zoom, $response);
        $zoom->start_time =$start;//周期性则需要恢复
        $DB->update_record('zoom', $zoom);
        //清除设置
        zoom_set_usable_email($zoom->course,$zoom);

    }else{
        //会议有可能具有周期性
        $zoom->start_time = zoom_get_starttime($zoom);
        $service->update_meeting($zoom);
        /*$cache = cache::make('mod_zoom', 'zoomid');
        if($cache->get('zoomid|'.$zoom->id)){
            //修改过
            try {
                $service->update_meeting($zoom);
                $cache->delete('zoomid|'.$zoom->id);
            } catch (moodle_exception $error) {
                throw $error;
            }
        }*/
    }
}

//更新会议室账号记录
if($userishost==2){
    $userishost = true;

    //更新会议室使用情况
    $total = user_get_total_participants($course->id, 0, 0,5);
    $user_meeting_info = $DB->get_record('zoom_user_meeting', ['host_id' => $zoom->host_id]);
    $user_meeting_info->uname = $USER->lastname.$USER->firstname;
    $user_meeting_info->coursename = $course->shortname;
    $user_meeting_info->snum = $total;
    $DB->update_record('zoom_user_meeting',$user_meeting_info);
}else{
    $userishost = false;
}


$context = context_module::instance($cm->id);
$PAGE->set_context($context);

require_capability('mod/zoom:view', $context);
if ($userishost) {
    $nexturl = new moodle_url($zoom->start_url);
} else {
    // Check whether user had a grade. If no, then assign full credits to him or her.
    $gradelist = grade_get_grades($course->id, 'mod', 'zoom', $cm->instance, $USER->id);

    // Assign full credits for user who has no grade yet, if this meeting is gradable (i.e. the grade type is not "None").
    if (!empty($gradelist->items) && empty($gradelist->items[0]->grades[$USER->id]->grade)) {
        $grademax = $gradelist->items[0]->grademax;
        $grades = array('rawgrade' => $grademax,
                        'userid' => $USER->id,
                        'usermodified' => $USER->id,
                        'dategraded' => '',
                        'feedbackformat' => '',
                        'feedback' => '');

        zoom_grade_item_update($zoom, $grades);
    }

    $nexturl = new moodle_url($zoom->join_url, array('uname' => fullname($USER)));
}

// Record user's clicking join.
\mod_zoom\event\join_meeting_button_clicked::create(array('context' => $context, 'objectid' => $zoom->id, 'other' =>
        array('cmid' => $id, 'meetingid' => (int) $zoom->meeting_id, 'userishost' => $userishost)))->trigger();
redirect($nexturl);
