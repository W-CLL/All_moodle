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
 * Prints a particular instance of zoom
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Login check require_login() is called in zoom_get_instance_setup();.
// @codingStandardsIgnoreLine
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/../../lib/moodlelib.php');

$config = get_config('mod_zoom');

list($course, $cm, $zoom) = zoom_get_instance_setup();
$downloadrole = zoom_get_role_download($course);
//重新生成$room,与判断

$context = context_module::instance($cm->id);
$iszoommanager = has_capability('mod/zoom:addinstance', $context);

$event = \mod_zoom\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $zoom);
$event->trigger();
// Print the page header.

$PAGE->set_url('/mod/zoom/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($zoom->name));
$PAGE->set_heading(format_string($course->fullname));




/*
$alternativehosts = array();
if (!is_null($zoom->alternative_hosts)) {
    $alternativehosts = explode(",", $zoom->alternative_hosts);
}
//$email = zoom_get_usable_email();
$email = $USER->email;//进入活动详情页面
$userishost = ($zoomuserid === $zoom->host_id || in_array($email, $alternativehosts));*/

//第一个进入会议室的老师
//根据角色判断权限
$userishost = zoom_get_auth_start_meeting($course, $zoom);


/*$service = new mod_zoom_webservice();
if($userishost==1){
    try {
        $rtn = $service->get_meeting_webinar_info($zoom->meeting_id, $zoom->webinar);
        $showrecreate = false;
    } catch (moodle_exception $error) {
        $showrecreate = zoom_is_meeting_gone_error($error);
    }
}else{
    $showrecreate = false;
}*/
$showrecreate = false;


$stryes = get_string('yes');
$strno = get_string('no');
$strstart = get_string('start_meeting', 'mod_zoom');
$strjoin = get_string('join_meeting', 'mod_zoom');
$strunavailable = get_string('unavailable', 'mod_zoom');
$strtime = get_string('meeting_time', 'mod_zoom');
$strduration = get_string('duration', 'mod_zoom');
$strpassprotect = get_string('passwordprotected', 'mod_zoom');
$strpassword = get_string('password', 'mod_zoom');
$strjoinlink = get_string('join_link', 'mod_zoom');
$strjoinbeforehost = get_string('joinbeforehost', 'mod_zoom');
$strstartvideohost = get_string('starthostjoins', 'mod_zoom');
$strstartvideopart = get_string('startpartjoins', 'mod_zoom');
$straudioopt = get_string('option_audio', 'mod_zoom');
$strstatus = get_string('status', 'mod_zoom');
$strall = get_string('allmeetings', 'mod_zoom');

// Output starts here.
echo $OUTPUT->header();

if ($showrecreate) {
    // Only show recreate/delete links in the message for users that can edit.
    if ($iszoommanager) {
        $message = get_string('zoomerr_meetingnotfound', 'mod_zoom', zoom_meetingnotfound_param($cm->id));
        $style = 'notifywarning';
    } else {
        $message = get_string('zoomerr_meetingnotfound_info', 'mod_zoom');
        $style = 'notifymessage';
    }
    echo $OUTPUT->notification($message, $style);
}

echo $OUTPUT->heading(format_string($zoom->name), 2);
if ($zoom->intro) {
    echo $OUTPUT->box(format_module_intro('zoom', $zoom, $cm->id), 'generalbox mod_introbox', 'intro');
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_view';

$table->align = array('center', 'left');
$numcolumns = 2;

list($inprogress, $available, $finished) = zoom_get_state($zoom);

if ($available) {
    if ($userishost==2) {
        $buttonhtml = html_writer::tag('button', $strstart, array('type' => 'submit', 'class' => 'btn btn-success'));
    } elseif ($userishost==1) {
        $buttonhtml = html_writer::tag('button', $strjoin, array('type' => 'submit', 'class' => 'btn btn-primary'));
    } else {
        notice('提示：ZOOM课室未开始，请等待教师进入', new moodle_url('/course/view.php', array('id' => $zoom->course)));
    }
    /*if ($userishost) {
        $buttonhtml = html_writer::tag('button', $strstart, array('type' => 'submit', 'class' => 'btn btn-success'));
    } else {
        $buttonhtml = html_writer::tag('button', $strjoin, array('type' => 'submit', 'class' => 'btn btn-primary'));
    }*/
    $aurl = new moodle_url('/mod/zoom/loadmeeting.php', array('id' => $cm->id));
    $buttonhtml .= html_writer::input_hidden_params($aurl);
    $link = html_writer::tag('form', $buttonhtml, array('action' => $aurl->out_omit_querystring(), 'target' => '_blank'));
} else {
    $link = html_writer::tag('span', $strunavailable, array('style' => 'font-size:20px'));
}

$title = new html_table_cell($link);
$title->header = true;
$title->colspan = $numcolumns;
$table->data[] = array($title);

if ($iszoommanager) {
    // Only show sessions link to users with edit capability.
    $sessionsurl = new moodle_url('/mod/zoom/report.php', array('id' => $cm->id));
    $sessionslink = html_writer::link($sessionsurl, get_string('sessions', 'mod_zoom'));
    $sessions = new html_table_cell($sessionslink);
    $sessions->colspan = $numcolumns;
    $table->data[] = array($sessions);

    // Display alternate hosts if they exist.
    if (!empty($zoom->alternative_hosts)) {
        $table->data[] = array(get_string('alternative_hosts', 'mod_zoom'), $zoom->alternative_hosts);
    }
}

//当前会议室使用情况
$total = $DB->count_records('zoom_user_meeting');
$sql = 'SELECT count(*)
          FROM {zoom_user_meeting} zum
         WHERE zum.num > 0';
$on = $DB->count_records_sql($sql);
$onstr = "总量 ".$total."个，已使用 ".$on."个";
$table->data[] = array('网络课室状态', $onstr);

//判断预约情况
$start = $zoom->start_time;
$end = $zoom->start_time + $zoom->duration;
$count = zoom_get_order_count($start, $end, $zoom->timetype);
$onstr = "该时间段已有".$count."人预约。";
if ($count>=100) {
    $onstr .= "资源不足！";
}
$table->data[] = array('网络课室本时段预约情况', html_writer::span($onstr, 'red'));

// Generate add-to-calendar button if meeting was found and isn't recurring.
if (!($showrecreate || $zoom->recurring)) {
    $icallink = new moodle_url('/mod/zoom/exportical.php', array('id' => $cm->id));
    //$calendaricon = $OUTPUT->pix_icon('i/calendar', get_string('calendariconalt', 'mod_zoom'), 'mod_zoom');
    $calendaricon = '';
    $calendarbutton = html_writer::div($calendaricon . ' ' . get_string('downloadical', 'mod_zoom'), 'btn btn-primary');
    $buttonhtml = html_writer::link((string) $icallink, $calendarbutton, array('target' => '_blank'));
    $table->data[] = array(get_string('addtocalendar', 'mod_zoom'), $buttonhtml);
}

if ($zoom->recurring) {
    $recurringmessage = new html_table_cell(get_string('recurringmeetinglong', 'mod_zoom'));
    $recurringmessage->colspan = $numcolumns;
    $table->data[] = array($recurringmessage);
} else {
    $table->data[] = array($strtime, userdate($zoom->start_time));
    $table->data[] = array($strduration, format_time($zoom->duration));
}
if ($zoom->timetype==1) {
    $table->data[] = array('时间周期类型', '每七天循环');
} else {
    $table->data[] = array('时间周期类型', '一次性');
}

if (!$zoom->webinar) {
    $haspassword = (isset($zoom->password) && $zoom->password !== '');
    $strhaspass = ($haspassword) ? $stryes : $strno;
    $table->data[] = array($strpassprotect, $strhaspass);

    if ($userishost==2 && $haspassword) {
        $table->data[] = array($strpassword, $zoom->password);
    }
}

if ($userishost==2) {
    $table->data[] = array($strjoinlink, html_writer::link($zoom->join_url, $zoom->join_url, array('target' => '_blank')));
}

if (!$zoom->webinar) {
    $strjbh = ($zoom->option_jbh) ? $stryes : $strno;
    $table->data[] = array($strjoinbeforehost, $strjbh);

    $strvideohost = ($zoom->option_host_video) ? $stryes : $strno;
    $table->data[] = array($strstartvideohost, $strvideohost);

    $strparticipantsvideo = ($zoom->option_participants_video) ? $stryes : $strno;
    $table->data[] = array($strstartvideopart, $strparticipantsvideo);
}

$table->data[] = array($straudioopt, get_string('audio_' . $zoom->option_audio, 'mod_zoom'));

if (!$zoom->recurring) {
    if (!$zoom->exists_on_zoom) {
        $status = get_string('meeting_nonexistent_on_zoom', 'mod_zoom');
    } elseif ($finished) {
        $status = get_string('meeting_finished', 'mod_zoom');
    } elseif ($inprogress) {
        $status = get_string('meeting_started', 'mod_zoom');
    } else {
        $status = get_string('meeting_not_started', 'mod_zoom');
    }

    $table->data[] = array($strstatus, $status);
}

//下载会议参与者记录
if ($downloadrole != 5) {
    $downloadlink = new moodle_url('/mod/zoom/ex_records.php', array('id' => $cm->id));
    //$calendaricon = $OUTPUT->pix_icon('i/calendar', get_string('calendariconalt', 'mod_zoom'), 'mod_zoom');
    $downloadicon = '';
    $downloadbutton = html_writer::div($downloadicon . ' ' . get_string('Now', 'mod_zoom'), 'btn btn-primary');
    $buttonhtml = html_writer::link((string) $downloadlink, $downloadbutton, array('target' => '_blank'));
    $table->data[] = array(get_string('downloadrecords', 'mod_zoom'), $buttonhtml);
}


$urlall = new moodle_url('/mod/zoom/index.php', array('id' => $course->id));
/*$linkall = html_writer::link($urlall, $strall);
$linktoall = new html_table_cell($linkall);
$linktoall->colspan = $numcolumns;
$table->data[] = array($linktoall);*/

echo html_writer::table($table);

// Finish the page.
echo $OUTPUT->footer();
