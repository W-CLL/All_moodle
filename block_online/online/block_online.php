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
 * Online users block.
 *
 * @package    block_online
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
class block_online extends block_base {
    function init() {
        $this->title = get_string('pluginname','block_online');
    }

    function has_config() {
        return true;
    }

    function get_content() {

        global $USER, $CFG, $DB, $OUTPUT;
        if ($this->content !== NULL) {
            return $this->content;
        }
        if (!has_capability('block/online:viewlist', $this->page->context)) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $now = time();
        $timetoshowusers = 600; //Seconds default

        if (isset($CFG->block_online_timetosee)) {
            $timetoshowusers = $CFG->block_online_timetosee * 60;
        }

        $minutes  = floor($timetoshowusers/60);
        $periodminutes = get_string('periodnminutes', 'block_online', $minutes);

        $courseid = $this->page->course->id;
        $instanceid = $this->page->context->instanceid;

        //小于十分钟
        $params = "($now - ul.timeaccess) < $timetoshowusers AND ul.courseid = $courseid ";

        $param = "($now - u.lastaccess) < $timetoshowusers";
        //整个平台的在线人数
        $p_sql = "SELECT count(DISTINCT(u.id)) FROM mdl_user u  
                    JOIN mdl_role_assignments ra on u.id = ra.userid
                    JOIN {context} ct ON ct.id=ra.contextid WHERE  $param  ";

        //整个平台的学生在线人数
        $ps_sql = "SELECT count(DISTINCT(u.id)) FROM mdl_user u  
                    JOIN mdl_role_assignments ra on u.id = ra.userid
                    JOIN {context} ct ON ct.id=ra.contextid 
                    WHERE  $param AND ra.roleid = 5 ";

        //单个课程的在线人数
        $c_sql = "SELECT count(DISTINCT(ul.userid)) FROM mdl_user_lastaccess ul 
                    JOIN mdl_role_assignments ra on ul.userid =ra.userid
                    JOIN {context} ct ON ct.id=ra.contextid
                    WHERE $params ";
                    
        //单个课程的学生在线人数
        $cs_sql = "SELECT count(DISTINCT(ul.userid)) FROM mdl_user_lastaccess ul 
                    JOIN mdl_role_assignments ra on ul.userid = ra.userid
                    JOIN {context} ct ON ct.id=ra.contextid 
                    WHERE  $params AND ra.roleid = 5 AND ct.instanceid = $instanceid";

        if ($courseid == 1) {
            $total_num = $DB->count_records_sql($p_sql);
            $student_num = $DB->count_records_sql($ps_sql);
        }else {
            $total_num = $DB->count_records_sql($c_sql);
            $student_num = $DB->count_records_sql($cs_sql);
         }
         
         $teacher_num = $total_num - $student_num;
        
         if ($total_num === 0) {
            $total_num = get_string('nouser', 'block_online');
        } elseif ($total_num === 1) {
            $total_num = get_string('numuser', 'block_online', $total_num);
        } else {
            $total_num = get_string('numusers', 'block_online', $total_num);
        }
        
        $this->content->text = '<div class="info">'.$total_num.' ('.$periodminutes.')</div>';
      
        if($courseid == 1){
            $this->content->text .='<div class ="show">'.get_string('full_station','block_online').'</br></div>';
        }else
            $this->content->text .= '<div class ="show">'.get_string('single_course','block_online').'</br></div>';

        $this->content->text .=get_string('teacher_c','block_online',$teacher_num).'</br>';
        $this->content->text .=get_string('student_c','block_online',$student_num);

        return $this->content;
    }
}


