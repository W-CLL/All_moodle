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
 * Internal library of functions for module zoom
 *
 * All the zoom specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/zoom/lib.php');
require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');

// Constants.
// Audio options.
define('ZOOM_AUDIO_TELEPHONY', 'telephony');
define('ZOOM_AUDIO_VOIP', 'voip');
define('ZOOM_AUDIO_BOTH', 'both');
// Meeting types.
define('ZOOM_INSTANT_MEETING', 1);
define('ZOOM_SCHEDULED_MEETING', 2);
define('ZOOM_RECURRING_MEETING', 3);
define('ZOOM_SCHEDULED_WEBINAR', 5);
define('ZOOM_RECURRING_WEBINAR', 6);
// Number of meetings per page from zoom's get user report.
define('ZOOM_DEFAULT_RECORDS_PER_CALL', 30);
define('ZOOM_MAX_RECORDS_PER_CALL', 300);
// User types. Numerical values from Zoom API.
define('ZOOM_USER_TYPE_BASIC', 1);
define('ZOOM_USER_TYPE_PRO', 2);
define('ZOOM_USER_TYPE_CORP', 3);

function zoom_get_exit_host($cid,$host_id){
    global $USER,$DB;
    $one = $DB->get_record('zoom_user_meeting', array('host_id' => $host_id));
    if($one){
        $email = $one->email;
        $cache = cache::make('mod_zoom', 'zoomid');
        $cache->set('uz|'.$cid.'|'.$USER->id, ['email'=>$email,'host_id'=>$host_id]);
        return $email;
    }else{
        return false;
    }
}
function zoom_get_usable_hostid($cid = 0,$type=true){
    global $USER,$DB;
    $cache = cache::make('mod_zoom', 'zoomid');
    if(!$cache->has('uz|'.$cid.'|'.$USER->id)){
        return '';
    }
    $has = $cache->get('uz|'.$cid.'|'.$USER->id);
    $host = $has['host_id'];
    return $host;
}
function zoom_get_usable_email($cid = 0,$type=true){
    global $USER,$DB;
    if($type){
        $cache = cache::make('mod_zoom', 'zoomid');
        if($cache->has('uz|'.$cid.'|'.$USER->id)){
            $has = $cache->get('uz|'.$cid.'|'.$USER->id);
            $email = $has['email'];
            return $email;
        }else{
            return '';
        }

    }else{
        //false 创建
        //$email = $USER->email;
        //可用邮箱
        $list     = $DB->get_records('zoom_user_meeting', array('num' => 0));
        $fielddata = [];
        foreach ($list as $k=>$v){
            if($v->host_id && $v->email){
                $fielddata[$v->host_id] = $v->email;
            }
        }
        $emails = array_values($fielddata);
        $onids = array_keys($fielddata);

        //查询以往记录
        $zmoodule = $DB->get_record('modules', array('name' => 'zoom'));
        if(!$zmoodule){
            return false;
        }
        $module_id = $zmoodule->id;

        //$oldlist = $DB->get_records('course_modules', array('module' => $module_id,'course'=>$cid),'added desc');
        $sql = 'SELECT z.id,z.course,z.host_id
          FROM {course_modules} cm
     LEFT JOIN {zoom} z ON cm.instance = z.id
         WHERE cm.module=?
               AND cm.course = ?
               ORDER BY cm.added desc';
        $params = array($module_id,$cid);
        $oldlist = $DB->get_records_sql($sql, $params);
        $oldids = array_filter(array_unique(array_column($oldlist,'host_id')));
        $rtn = array_intersect($oldids,$onids);//按时间顺序以往的可用账号
        $has = array_shift($rtn);
        if(!$has){
            //获取随机邮箱
            shuffle($onids);
            $has = array_shift($onids);
        }

        if($has){
            $email = $fielddata[$has];

            //保存到缓存中
            $cache = cache::make('mod_zoom', 'zoomid');
            $cache->set('uz|'.$cid.'|'.$USER->id, ['email'=>$email,'host_id'=>$has]);
        }else{
            $email = false;
        }
        return $email;
    }


}
function zoom_set_usable_email($cid,$zoom=null){
    //设置已使用的邮箱zoom_set_usable_email($cid,'test@m.scnu.edu.cn',$zoom);
    global $USER,$DB;

    $cache = cache::make('mod_zoom', 'zoomid');
    if(!$cache->has('uz|'.$cid.'|'.$USER->id)){
        return false;
    }
    $has = $cache->get('uz|'.$cid.'|'.$USER->id);
    $host = $has['host_id'];
    if(!$host){
        return false;
    }
    if($zoom){
        $zoomid = $zoom->id;
    }else{
        $zoomid = 0;
    }
    $one = $DB->get_record('zoom_user_meeting', array('host_id' => $host));
    if($one){
        $num = $one->num + 1;
        $DB->update_record('zoom_user_meeting', array('id' => $one->id, 'zoomid' => $zoomid, 'num' => $num));
    }
    $cache->delete('uz|'.$cid.'|'.$USER->id);//删除掉数据
    return true;
}
//判断权限：0未开始请等待，1join，2start
function zoom_get_auth_start_meeting($course,$zoom){
    global $USER,$DB;
    $userishost = 1;
    $context =context_course::instance($course->id);
    $roles = get_user_roles($context,$USER->id);
    $rids = array_column($roles,'roleid');

    if($zoom->meeting_id>0){
        require_once(__DIR__.'/../../lib/moodlelib.php');
        $service = new mod_zoom_webservice();
        $response = $service->get_meeting_webinar_info($zoom->meeting_id, $zoom->webinar,false);
        //var_dump($response);die;
        //$response->settings->in_meeting bool?
        if($response){
            if($response->status == 'waiting'){//是否到时间
                //判断会议是否开启，未开始则显示开启按钮
                $status = 0;
            }else{
                $status = 1;
            }
        }else{
            //无法获取到会议信息则重建
            $status = 0;
        }

    }else{
        //未开启
        $status = 0;
    }
    if(!empty(array_intersect($rids,[1,3,10])) || is_siteadmin()){
        //有权限
        $userishost = $status ? 1 : 2;
    }else{
        $userishost = $status ? 1 : 0;
    }
    return $userishost;
}
//预约时间段判断
function zoom_get_order_count($start,$end,$weektype=0){
    //判断预约情况
    global $DB;
    $zmoodule = $DB->get_record('modules', array('name' => 'zoom'));
    if(!$zmoodule){
        return 0;
    }
    $module_id = $zmoodule->id;

    $sql = "SELECT count(*) FROM {zoom} z
INNER JOIN {course_modules} cm ON cm.instance = z.id
WHERE deletioninprogress =0 AND cm.module=? 
 ";
    $weekday = date('w',$start);//周日0

    /*//一次性
    $sql .= " AND ( ";
    $sql .= "( timetype =0 AND ((start_time <= ? AND start_time+duration >= ?) OR (start_time <= ? AND start_time+duration >= ?)))";
    //七天循环；周日1
    $sql .= " OR ( timetype = 1 AND DAYOFWEEK(FROM_UNIXTIME(start_time,'%Y-%m-%d %H:%i:%S'))-1=".$weekday." and ((FROM_UNIXTIME(start_time,'%H:%i:%S') <= ? AND FROM_UNIXTIME(start_time+duration,'%H:%i:%S') >= ?) OR (FROM_UNIXTIME(start_time,'%H:%i:%S') <= ? AND FROM_UNIXTIME(start_time+duration,'%H:%i:%S') >= ?)))";
    $params = array($module_id,$start,$start,$end,$end,date('H:i:s',$start),date('H:i:s',$start),date('H:i:s',$end),date('H:i:s',$end));
    $sql .= ")";*/
    $timepre = "2020-01-01 ";
    $start_set = strtotime($timepre.date('H:i:s',$start));
    $end_set = strtotime($timepre.date('H:i:s',$end));
    if($weektype==1){
        //本次数据为循环数据，故获取一次性数据应通过循环判断，无论timetype的值
        $sql .= " AND ( ";
        //七天循环；周日1
        $sql .= "DAYOFWEEK(FROM_UNIXTIME(start_time,'%Y-%m-%d %H:%i:%S'))-1=".$weekday." AND (
            ( UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time,'%H:%i:%S'))) >= ? AND UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time,'%H:%i:%S'))) <= ? ) OR ( UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time,'%H:%i:%S'))) <= ? AND UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time+duration,'%H:%i:%S'))) >= ? )
        )";
        $params = array($module_id,$start_set,$end_set,$start_set,$start_set);
        $sql .= ")";
    }else{
        //本次数据为一次性数据，日期一个，无需循环判断//一次性
        $sql .= " AND ( ";
        $sql .= "( timetype =0 AND ( (start_time >= ? AND start_time <= ?) OR (start_time <= ? AND start_time+duration >= ?)) )";
        //七天循环；周日1
        $sql .= " OR ( 
        timetype = 1 AND DAYOFWEEK(FROM_UNIXTIME(start_time,'%Y-%m-%d %H:%i:%S'))-1=".$weekday." AND (
            ( UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time,'%H:%i:%S'))) >= ? AND UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time,'%H:%i:%S'))) <= ? ) OR ( UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time,'%H:%i:%S'))) <= ? AND UNIX_TIMESTAMP(CONCAT('".$timepre." ',FROM_UNIXTIME(start_time+duration,'%H:%i:%S'))) >= ? )
        )
    )";
        $params = array($module_id,$start,$end,$start,$start,$start_set,$end_set,$start_set,$start_set);
        $sql .= ")";
    }
    $count = $DB->count_records_sql($sql, $params);
    return $count;
}
//获取会议开始时间
function zoom_get_starttime($zoom){
    if($zoom->timetype==1){
        //七天循环
        $starttime = strtotime(date('Y-m-d ').date('H:i:s',$zoom->start_time));
    }else{
        //
        $starttime = $zoom->start_time;
    }
    return $starttime;
}

/**
 * Get course/cm/zoom objects from url parameters, and check for login/permissions.
 *
 * @return array Array of ($course, $cm, $zoom)
 */
function zoom_get_instance_setup() {
    global $DB;

    $id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
    $n  = optional_param('n', 0, PARAM_INT);  // ... zoom instance ID - it should be named as the first character of the module.

    if ($id) {
        $cm         = get_coursemodule_from_id('zoom', $id, 0, false, MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $zoom  = $DB->get_record('zoom', array('id' => $cm->instance), '*', MUST_EXIST);
    } else if ($n) {
        $zoom  = $DB->get_record('zoom', array('id' => $n), '*', MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $zoom->course), '*', MUST_EXIST);
        $cm         = get_coursemodule_from_instance('zoom', $zoom->id, $course->id, false, MUST_EXIST);
    } else {
        print_error(get_string('zoomerr_id_missing', 'zoom'));
    }

    require_login($course, true, $cm);

    $context = context_module::instance($cm->id);
    require_capability('mod/zoom:view', $context);

    return array($course, $cm, $zoom);
}

/**
 * Retrieves information for a meeting.
 *
 * @param int $meetingid
 * @param bool $webinar
 * @param string $hostid the host's uuid
 * @return array information about the meeting
 */
function zoom_get_sessions_for_display($meetingid, $webinar, $hostid) {
    require_once(__DIR__.'/../../lib/moodlelib.php');
    global $DB;
    $service = new mod_zoom_webservice();
    $sessions = array();
    $format = get_string('strftimedatetimeshort', 'langconfig');

    $instances = $DB->get_records('zoom_meeting_details', array('meeting_id' => $meetingid));

    foreach ($instances as $instance) {
        // The meeting uuid, not the participant's uuid.
        $uuid = $instance->uuid;
        $participantlist = zoom_get_participants_report($instance->id);
        $sessions[$uuid]['participants'] = $participantlist;
        $sessions[$uuid]['count'] = count($participantlist);
        $sessions[$uuid]['topic'] = $instance->topic;
        $sessions[$uuid]['duration'] = $instance->duration;
        $sessions[$uuid]['starttime'] = userdate($instance->start_time, $format);
        $sessions[$uuid]['endtime'] = userdate($instance->start_time + $instance->duration * 60, $format);
    }
    return $sessions;
}

/**
 * Determine if a zoom meeting is in progress, is available, and/or is finished.
 *
 * @param stdClass $zoom
 * @return array Array of booleans: [in progress, available, finished].
 */
function zoom_get_state($zoom) {
    $config = get_config('mod_zoom');
    $now = time();

    if($zoom->timetype==1){
        $start = strtotime(date('Y-m-d ').date('H:i:s',$zoom->start_time));
        $daydiff = date('z',$zoom->start_time)>date('z') ? 0 : 1;
    }else{
        $start = $zoom->start_time;
        $daydiff = 1;
    }
    $firstavailable = $start - ($config->firstabletojoin * 60);
    $lastavailable = $start + $zoom->duration;

    $inprogress = ($firstavailable <= $now && $now <= $lastavailable && $daydiff);

    $available = $zoom->recurring || $inprogress;

    $finished = !$zoom->recurring && $now > $lastavailable;

    return array($inprogress, $available, $finished,$lastavailable);
}

/**
 * Get the Zoom id of the currently logged-in user.
 *
 * @param boolean $required If true, will error if the user doesn't have a Zoom account.
 * @return string
 */
function zoom_get_user_id($required = true) {
    global $USER;

    $cache = cache::make('mod_zoom', 'zoomid');
    if (!($zoomuserid = $cache->get($USER->id))) {
        $zoomuserid = false;
        $service = new mod_zoom_webservice();
        try {
            $email = $USER->email;
            //$email = zoom_get_usable_email();
            $zoomuser = $service->get_user($email);
            if ($zoomuser !== false) {
                $zoomuserid = $zoomuser->id;
            }
        } catch (moodle_exception $error) {
            if ($required) {
                throw $error;
            } else {
                $zoomuserid = $zoomuser->id;
            }
        }
        $cache->set($USER->id, $zoomuserid);
    }

    return $zoomuserid;
}

/**
 * Check if the error indicates that a meeting is gone.
 *
 * @param string $error
 * @return bool
 */
function zoom_is_meeting_gone_error($error) {
    // If the meeting's owner/user cannot be found, we consider the meeting to be gone.
    return strpos($error, 'not found') !== false || zoom_is_user_not_found_error($error);
}

/**
 * Check if the error indicates that a user is not found or does not belong to the current account.
 *
 * @param string $error
 * @return bool
 */
function zoom_is_user_not_found_error($error) {
    return strpos($error, 'not exist') !== false || strpos($error, 'not belong to this account') !== false
        || strpos($error, 'not found on this account') !== false;
}

/**
 * Return the string parameter for zoomerr_meetingnotfound.
 *
 * @param string $cmid
 * @return stdClass
 */
function zoom_meetingnotfound_param($cmid) {
    // Provide links to recreate and delete.
    $recreate = new moodle_url('/mod/zoom/recreate.php', array('id' => $cmid, 'sesskey' => sesskey()));
    $delete = new moodle_url('/course/mod.php', array('delete' => $cmid, 'sesskey' => sesskey()));

    // Convert links to strings and pass as error parameter.
    $param = new stdClass();
    $param->recreate = $recreate->out();
    $param->delete = $delete->out();

    return $param;
}

/**
 * Get the data of each user for the participants report.
 * @param string $detailsid The meeting ID that you want to get the participants report for.
 * @return array The user data as an array of records (array of arrays).
 */
function zoom_get_participants_report($detailsid) {
    global $DB;
    $service = new mod_zoom_webservice();
    $sql = 'SELECT zmp.id,
                   zmp.name,
                   zmp.userid,
                   zmp.user_email,
                   zmp.join_time,
                   zmp.leave_time,
                   zmp.duration,
                   zmp.attentiveness_score,
                   zmp.uuid
              FROM {zoom_meeting_participants} zmp
             WHERE zmp.detailsid = :detailsid
    ';
    $params = [
        'detailsid' => $detailsid
    ];
    $participants = $DB->get_records_sql($sql, $params);
    return $participants;
}

function zoom_get_role_download($course)
{

    global $USER,$DB;
    $context =context_course::instance($course->id);
    $roles = get_user_roles($context, $USER->id);
    $rids = array_column($roles, 'roleid');
    foreach($rids as $rid){
        return $rid;
    }
    
}
