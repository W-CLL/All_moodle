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
 * Lib function
 *
 * @package    block_data_screen
 * @category   external
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param $navigation
 * @param $course
 * @param $context
 * @throws coding_exception
 * @throws moodle_exception
 */
function block_data_screen_extend_navigation_course($navigation, $course, $context)
{
    global $CFG;
    $reports = $navigation->find('coursereports', navigation_node::TYPE_CONTAINER);
    if (has_capability('block/data_screen:viewpages', $context) && $reports) {
        $reportanalyticsgraphs = $reports->add(get_string('pluginname', 'block_data_screen'));
        $url = new moodle_url($CFG->wwwroot . '/blocks/data_screen/course_detail.php',
            array('id' => $course->id));
        $reportanalyticsgraphs->add($course->fullname, $url,
            navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}

/**
 * Statistics data
 */
function OneHour()
{
    flushAccessStatistics();
    flushAccessTop();
}

function OneDay()
{
    flushStatisticsCourse();
    flushStatisticsVisit();
    flushCollegeTotal();
    flushDataTotal();
    flushForumTotal();
    flushQuizAssign();
    flushUserLogin();
    flushActive7Days();
}
/**
 * Collect access data:
 *      course,student
 *
 * by cyxuan 20200303.
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function flushActive7Days(){
    global $DB, $CFG;

    $actionRecord = statisticsLog('flushActive7Days', 0);
    if (!$actionRecord) return false;

    // check if this is the first time to run this function
    $accessRecord = $DB->get_record_sql('SELECT date FROM {block_data_screen_active} ORDER BY date DESC LIMIT 1');
    if (!$accessRecord) {
        $id = 1;
        //第一次操作没有数据；获取前七天段时间的数据
        $data = [];
        for($i=7;$i>=1;$i--){
            $starttime = strtotime(date('Y-m-d',strtotime('-'.$i.' day')));//前一天凌晨
            //$endtime=strtotime(date('Y-m-d'))-1;//前一天最后1秒
            $endtime = $starttime + 86399;
            $course_sql = "SELECT COUNT(DISTINCT courseid) FROM `mdl_logstore_standard_log` WHERE timecreated>".$starttime." AND timecreated<".$endtime." AND crud='r' AND contextlevel=50 AND courseid<>1";
            $student_sql = "SELECT COUNT( DISTINCT userid) FROM `mdl_logstore_standard_log`WHERE timecreated>".$starttime." AND timecreated<".$endtime." AND courseid=0 AND anonymous=0 AND action='loggedin'";

            $one = [];
            $one['date'] = $starttime;
            $one['updated_time'] = time();
            $count = $DB->count_records_sql($course_sql);
            $one['courses'] = $count;
            $count = $DB->count_records_sql($student_sql);
            $one['students'] = $count;
            $data[] = $one;
        }
        $DB->insert_records('block_data_screen_active', $data);
    } else {
        //统计前一天的数据；
        $starttime = strtotime(date('Y-m-d',strtotime('-1 day')));//前一天凌晨
        //$endtime=strtotime(date('Y-m-d'))-1;//前一天最后1秒
        $endtime = $starttime + 86399;
        $course_sql = "SELECT COUNT(DISTINCT courseid) FROM `mdl_logstore_standard_log` WHERE timecreated>".$starttime." AND timecreated<".$endtime." AND crud='r' AND contextlevel=50 AND courseid<>1";
        $student_sql = "SELECT COUNT( DISTINCT userid) FROM `mdl_logstore_standard_log`WHERE timecreated>".$starttime." AND timecreated<".$endtime." AND courseid=0 AND anonymous=0 AND action='loggedin'";

        $data = [];
        $data['date'] = $starttime;
        $data['updated_time'] = time();
        $count = $DB->count_records_sql($course_sql);
        $data['courses'] = $count;
        $count = $DB->count_records_sql($student_sql);
        $data['students'] = $count;
        $accessRecord = $DB->get_record_sql('SELECT * FROM {block_data_screen_active} WHERE date='.$starttime.' LIMIT 1');
        if($accessRecord){
            $data['id'] = $accessRecord->id;
            $DB->update_record('block_data_screen_active', $data);
        }else{
            $DB->insert_record('block_data_screen_active', $data);
        }
    }
    statisticsLog('flushActive7Days', 1);
}
//计算日期差异
function diff_date($date1, $date2){
    if($date1>$date2){
        $startTime = strtotime($date1);
        $endTime = strtotime($date2);
    }else{
        $startTime = strtotime($date2);
        $endTime = strtotime($date1);
    }
    $diff = $startTime-$endTime;
    $day = $diff/86400;
    return intval($day);
}

/**
 * Collect access data:
 *      pv,uv,ip,access number
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function flushAccessStatistics()
{
    global $DB, $CFG;

    $actionRecord = statisticsLog('flushAccessStatistics', 0);
    if (!$actionRecord) return false;

    $logout = $CFG->block_data_screen_logout_time;

    // check if this is the first time to run this function
    $accessRecord = $DB->get_record_sql('SELECT date FROM {block_data_screen_access} ORDER BY date DESC LIMIT 1');
    if (!$accessRecord) {
        $id = 1;
        while (true) {
            $recordHour = $accessUser = [];
            $logs = $DB->get_recordset_sql('SELECT
                id,
                userid,
                ip,
                timecreated
            FROM {logstore_standard_log}
            WHERE id>=? AND id<=?
            ORDER BY timecreated LIMIT 0,500000', [$id, $id + 500000]);
            if (!$logs->valid()) break;
            foreach ($logs as $value) {
                $logHour = date('Y-m-d H', $value->timecreated);

                if ($value->userid >= 1 && $value->ip) {
                    $recordHour[$logHour]['pv'] = isset($recordHour[$logHour]['pv'])
                        ? $recordHour[$logHour]['pv'] + 1
                        : 1;
                    $recordHour[$logHour]['uv'][] = $value->userid;
                    $recordHour[$logHour]['uv'] = array_unique($recordHour[$logHour]['uv']);
                    $recordHour[$logHour]['ip'][] = $value->userid;
                    $recordHour[$logHour]['ip'] = array_unique($recordHour[$logHour]['ip']);

                    if (!isset($accessUser[$value->userid]) || (isset($accessUser[$value->userid]) && (($value->timecreated - $accessUser[$value->userid]) > (int)$logout * 60))) {
                        $accessUser[$value->userid] = $value->timecreated;
                        $recordHour[$logHour]['access_num'] = isset($recordHour[$logHour]['access_num'])
                            ? $recordHour[$logHour]['access_num'] + 1
                            : 1;
                    }
                }
            }
            $logs->close();
            $id += 500000;

            $new_hour_record = [];
            foreach ($recordHour as $key => $row) {
                $record = $DB->get_record_sql(
                    'SELECT id,pv,uv,ip,access_num,updated_time FROM {block_data_screen_access} WHERE FROM_UNIXTIME(date, "%Y-%m-%d %H")=?',
                    [$key]
                );
                if (!$record) {
                    $record = new \stdClass();
                    $record->pv = $row['pv'];
                    $record->uv = count($row['uv']);
                    $record->ip = count($row['ip']);
                    $record->date = strtotime("$key:00:00");
                    $record->updated_time = time();
                    $record->access_num = $row['access_num'];

                    $new_hour_record[] = $record;
                } else {
                    $record->pv += $row['pv'];
                    $record->uv += count($row['uv']);
                    $record->ip += count($row['ip']);
                    $record->updated_time = time();
                    $record->access_num = $row['access_num'];
                    $DB->update_record('block_data_screen_access', $record);
                }
            }
            $DB->insert_records('block_data_screen_access', $new_hour_record);
        }
    } else {
        $accessStatistics = $DB->get_recordset_sql(
                'SELECT
                    COUNT(userid>=1 OR NULL) pv,
                    COUNT(DISTINCT CASE WHEN userid>=0 THEN userid END) uv,
                    COUNT(DISTINCT ip) ip,
                    timecreated date,
                    UNIX_TIMESTAMP(NOW()) updated_time
                FROM {logstore_standard_log}
                WHERE id IN (
                    SELECT id FROM {logstore_standard_log} WHERE timecreated>?
                )
                GROUP BY FROM_UNIXTIME(timecreated, "%Y-%m-%d %H");', [$accessRecord->date]);

        $new_hour_record = [];
        foreach ($accessStatistics as $key => $value) {
            if (date('Y-m-d H', $value->date) == date('Y-m-d H', $accessRecord->date)) continue;
            $new_hour_record[] = $value;
        }
        if ($new_hour_record) {
            $DB->insert_records('block_data_screen_access', $new_hour_record);
        }

        $log = $DB->get_recordset_sql(
            'SELECT
            timecreated,
            userid
        FROM {logstore_standard_log}
        WHERE timecreated>?
        ORDER BY FROM_UNIXTIME(timecreated, "%Y-%m-%d %H"),userid,FROM_UNIXTIME(timecreated)',
            [$accessRecord->date]);

        $lastTime = 0;
        $lastUser = 0;
        foreach ($log as $key => $value) {
            if (date('Y-m-d H', $value->timecreated) != date('Y-m-d H', $lastTime) || $value->userid != $lastUser) {
                $accessRecord = $DB->get_record_sql('SELECT * FROM {block_data_screen_access} WHERE FROM_UNIXTIME(date, "%Y-%m-%d %H")=FROM_UNIXTIME(?, "%Y-%m-%d %H")', [$value->timecreated]);
                if (!$accessRecord) continue;
            }
            if (($value->userid != $lastUser || ($value->timecreated - $lastTime) > ((int)$logout * 60)) && isset($accessRecord)) {
                $accessRecord->access_num = $accessRecord->access_num + 1;
                $DB->update_record('block_data_screen_access', $accessRecord);
            }
            $lastTime = $value->timecreated;
            $lastUser = $value->userid;
        }
    }

    statisticsLog('flushAccessStatistics', 1);
}

/**
 * Collect user login times
 */
function flushUserLogin()
{
    global $DB;

    $actionRecord = statisticsLog('flushUserLogin', 0);
    if (!$actionRecord) return false;

    $accessRecord = $DB->get_record_sql('SELECT MAX(end_time) end_time FROM {block_data_screen_stats_log} WHERE method="flushUserLogin" AND status=1');
    if (!$accessRecord->end_time) {
        $DB->execute('TRUNCATE {block_data_screen_user}');

        $users = $DB->get_records_sql('SELECT id FROM {user} WHERE deleted=0 AND id <> 1');
        $users_login = [];
        foreach ($users as $user) {
            $users_login[$user->id]['user_id'] = $user->id;
            $users_login[$user->id]['login'] = $DB->count_records_sql(
                'SELECT
                  COUNT(*)
                FROM {logstore_standard_log}
                WHERE
                  crud="r"
                  AND component="core"
                  AND action="loggedin"
                  AND target="user"
                  AND courseid=0
                  AND userid=?',
                [$user->id]
            );
        }
        $DB->insert_records('block_data_screen_user', $users_login);
    } else {
        $users = $DB->get_records_sql('SELECT id FROM {user} WHERE deleted=0 AND id <> 1');
        $users_login = [];
        foreach ($users as $user) {
            $users_login[$user->id]['user_id'] = $user->id;
            $users_login[$user->id]['login'] = $DB->count_records_sql(
                'SELECT
                  COUNT(*)
                FROM {logstore_standard_log}
                WHERE
                  crud="r"
                  AND timecreated>=?
                  AND component="core"
                  AND action="loggedin"
                  AND target="user"
                  AND courseid=0
                  AND userid=?',
                [$accessRecord->end_time, $user->id]
            );
        }
        $new = [];
        foreach ($users_login as $user) {
            if ($user['login'] == 0) continue;
            $record = $DB->get_record('block_data_screen_user', ['user_id'=>$user['user_id']]);
            if ($record) {
                $record->login += $user['login'];
                $DB->update_record('block_data_screen_user', $record);
            } else {
                $new[] = $user;
            }
        }
        $DB->insert_records('block_data_screen_user', $new);
    }
    statisticsLog('flushUserLogin', 1);
}

/**
 * Maximum access record
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function flushAccessTop()
{
    $actionRecord = statisticsLog('flushAccessTop', 0);
    if (!$actionRecord) return false;

    global $DB;

    $DB->execute('TRUNCATE {block_data_screen_access_top}');

    $accessTop = [];
    $pv = $DB->get_record_sql('SELECT * FROM (SELECT SUM(pv) counts, date FROM {block_data_screen_access} GROUP BY FROM_UNIXTIME(date,"%Y-%m-%d") ORDER BY counts DESC)list LIMIT 1');
    $pv->type = 'pv';
    $pv->updated_time = time();
    $uv = $DB->get_record_sql('SELECT * FROM (SELECT SUM(uv) counts, date FROM {block_data_screen_access} GROUP BY FROM_UNIXTIME(date,"%Y-%m-%d") ORDER BY counts DESC)list LIMIT 1');
    $uv->type = 'uv';
    $uv->updated_time = time();
    $ip = $DB->get_record_sql('SELECT * FROM (SELECT SUM(ip) counts, date FROM {block_data_screen_access} GROUP BY FROM_UNIXTIME(date,"%Y-%m-%d") ORDER BY counts DESC)list LIMIT 1');
    $ip->type = 'ip';
    $ip->updated_time = time();
    $access_num = $DB->get_record_sql('SELECT * FROM (SELECT SUM(access_num) counts, date FROM {block_data_screen_access} GROUP BY FROM_UNIXTIME(date,"%Y-%m-%d") ORDER BY counts DESC)list LIMIT 1');
    $access_num->type = 'access_num';
    $access_num->updated_time = time();
    $accessTop = [$pv, $uv, $ip, $access_num];

    $DB->insert_records('block_data_screen_access_top', $accessTop);

    statisticsLog('flushAccessTop', 1);
}


/**
 * Course statistics:
 *      course id,full name,short name,created time,course's category id, course's category name,course's category path,
 *      start time,end time,summary,forums,quizs,assigns,students number,teachers number,teachers name,resource number,
 *      activity number
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function flushStatisticsCourse()
{
    $actionRecord = statisticsLog('flushStatisticCourse', 0);
    // check if there was a process running
    if (!$actionRecord) return false;

    global $DB;
    $block = $DB->get_record_sql("SELECT id FROM {block} WHERE name='associated_course'");
    $DB->execute('TRUNCATE {block_data_screen_course}');

    $mods = get_mod_id();

    $courseTotal = $DB->get_records_sql('SELECT
        c.id course_id,
        c.fullname full_name,
        c.shortname short_name,
        c.timecreated time_created,
        c.category category_id,
        cc.name category,
        cc.path,
        c.startdate start_time,
        c.enddate end_time,
        c.summary,
        (SELECT COUNT(f.id) FROM {forum} f WHERE f.course=c.id) forums,
        (SELECT COUNT(a.id) FROM {assign} a WHERE a.course=c.id) assigns,
        (SELECT COUNT(q.id) FROM {quiz} q WHERE q.course=c.id) quiz,
        (SELECT COUNT(userid) FROM {role_assignments} r JOIN {context} con ON con.id=r.contextid WHERE con.instanceid=c.id AND r.roleid=5)students,
        (SELECT COUNT(userid) FROM {role_assignments} r JOIN {context} con ON con.id=r.contextid WHERE con.instanceid=c.id AND r.roleid=3)teacher_counts,
        (SELECT GROUP_CONCAT(DISTINCT ra.userid) FROM {role_assignments} ra JOIN {context} ct ON ct.id=ra.contextid WHERE ct.instanceid=c.id AND ra.roleid=3) teachers,
        (SELECT
            COUNT(DISTINCT cm.id)
            FROM {course_modules} cm
            WHERE cm.course=c.id
            AND cm.module IN (' . $mods['resource'] . ')
            AND cm.deletioninprogress=0
        ) resource_num,
        (SELECT
            COUNT(DISTINCT cm.id)
            FROM {course_modules} cm
            WHERE cm.course=c.id
            AND cm.module IN (' . $mods['activity'] . ')
            AND cm.deletioninprogress=0
        ) activity_num
    FROM {course} c
    JOIN {course_categories} cc ON cc.id=c.category;');
    foreach ($courseTotal as $key => $value) {
        if ($block) {
            $type = $DB->get_record_sql("SELECT typeid FROM {block_course_type} WHERE courseid=?", [$value->course_id]);
            if ($type) {
                $open_times = $DB->get_record_sql("SELECT COUNT(*) counts FROM {block_course_type} WHERE typeid=?", [$type->typeid])->counts + 1;
            } else {
                $open_times = $DB->get_record_sql("SELECT id FROM {block_type} WHERE courseid=?", [$value->course_id]) ? 1 : 0;
            }
        }
        $courseTotal[$key]->open_times = isset($open_times) ? $open_times : 0;
        $courseTotal[$key]->tags = $DB->get_record_sql("SELECT GROUP_CONCAT(t.name) tags FROM {tag_instance} ti JOIN {tag} t ON t.id=ti.tagid WHERE ti.itemtype='course' AND ti.itemid=?", [$value->course_id])->tags;

        // get course cover
        $course = $DB->get_record_sql("SELECT * FROM {course} WHERE id=?", [$value->course_id]);
        $courseImage = get_course_image($course);
        if (!$courseImage) {
            $courseImage = get_course_pattern($course);
        }
        $courseTotal[$key]->img = $courseImage;
    }

    $DB->insert_records('block_data_screen_course', $courseTotal);


    statisticsLog('flushStatisticCourse', 1);
}


/**
 * Count the number of course or mod visits:
 *      access number,download number,spend time,section,grademax,finalgrade,mod name
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function flushStatisticsVisit()
{
    $actionRecord = statisticsLog('flushStatisticsVisit', 0);
    if (!$actionRecord) return false;

    global $DB, $CFG;

    $mods = $tables = [];
    $mod = $DB->get_records('modules', [], '', 'id,name');
    foreach ($mod as $value) {
        $mods['mod_' . $value->name] = $value->id;
        $tables[$value->id] = $value->name;
    }

    // check if this is the first time to run this function
    $accessRecord = $DB->get_record_sql('SELECT MAX(end_time) end_time FROM {block_data_screen_stats_log} WHERE method="flushStatisticsVisit" AND status=1');
    $logout = $CFG->block_data_screen_logout_time;
    if (!$accessRecord->end_time) {
        $id = 1;
        $accessTime = [];
        while (true) {
            $new_mods = $new_courses = [];
            $logs = $DB->get_recordset_sql('SELECT
                    id,
                    userid,
                    courseid,
                    timecreated,
                    component,
                    target,
                    action,
                    crud,
                    anonymous,
                    contextlevel,
                    contextinstanceid
                FROM {logstore_standard_log}
                WHERE id>=? AND id<? ORDER BY timecreated LIMIT 0,500000', [$id, $id + 500000]);
            if (!$logs->valid()) break;
            foreach ($logs as $value) {
                if (array_key_exists($value->component, $mods)) {
                    $new_mods[$value->contextinstanceid][$value->userid]['type'] = $mods[$value->component];
                    // access number(pv)
                    if ($value->crud == 'r' && $value->anonymous == 0 && $value->contextlevel == CONTEXT_MODULE) {
                        $new_mods[$value->contextinstanceid][$value->userid]['access'] =
                            isset($new_mods[$value->contextinstanceid][$value->userid]['access'])
                                ? $new_mods[$value->contextinstanceid][$value->userid]['access'] + 1
                                : 1;
                    }
                    // download number
                    if ($value->action == 'downloaded') {
                        $new_mods[$value->contextinstanceid][$value->userid]['download'] =
                            isset($new_mods[$value->contextinstanceid][$value->userid]['download'])
                                ? $new_mods[$value->contextinstanceid][$value->userid]['download'] + 1
                                : 1;
                    }
                    // spend time
                    if (isset($accessTime[$value->contextinstanceid][$value->userid])) {
                        $time = $value->timecreated - $accessTime[$value->contextinstanceid][$value->userid];
                        if ($time < ($logout * 60)) {
                            $new_mods[$value->contextinstanceid][$value->userid]['spend_time'] =
                                isset($new_mods[$value->contextinstanceid][$value->userid]['spend_time'])
                                    ? $new_mods[$value->contextinstanceid][$value->userid]['spend_time'] + $time
                                    : $time;
                        }
                    }
                    $new_mods[$value->contextinstanceid][$value->userid]['course_id'] = $value->courseid;
                }

                if ($value->courseid > 1 && $value->userid > 0) {
                    $new_courses[$value->courseid][$value->userid]['access'] =
                        isset($new_courses[$value->courseid][$value->userid]['access'])
                            ? $new_courses[$value->courseid][$value->userid]['access'] + 1
                            : 1;
                    // spend time
                    if (isset($accessTime[$value->courseid][$value->userid])) {
                        $time = $value->timecreated - $accessTime[$value->courseid][$value->userid];
                        if ($time < ($logout * 60)) {
                            $new_courses[$value->courseid][$value->userid]['spend_time'] =
                                isset($new_courses[$value->courseid][$value->userid]['spend_time'])
                                    ? $new_courses[$value->courseid][$value->userid]['spend_time'] + $time
                                    : $time;
                        }
                    }
                    // created
                    if ($value->target == 'course_module' && $value->action == 'created') {
                        $new_courses[$value->courseid][$value->userid]['create_num'] =
                            isset($new_courses[$value->courseid][$value->userid]['create_num'])
                                ? $new_courses[$value->courseid][$value->userid]['create_num'] + 1
                                : 1;
                    }
                }

                $accessTime[$value->courseid][$value->userid] = $value->timecreated;
            }
            $logs->close();
            $id += 500000;

            $new_records = [];
            foreach ($new_mods as $key => $value) {
                foreach ($value as $k => $val) {
                    $record = $DB->get_record_select(
                        'block_data_screen_visit',
                        ' type=? AND instance_id=? AND user_id=?',
                        [$val['type'], $key, $k],
                        'id,type,instance_id,access_num,download,spend_time,finalgrade,role'
                    );
                    $mod = $DB->get_record_sql('SELECT
                            cm.instance,
                            cs.section,
                            cm.module
                        FROM {course_modules} cm
                        JOIN {course_sections} cs ON cm.section=cs.id
                        WHERE cm.id=?', [$key]);
                    if (!$mod) continue;
                    $grade = $DB->get_record_sql(
                        'SELECT gi.id,gi.grademax FROM {grade_items} gi JOIN {modules} m ON m.name=gi.itemmodule WHERE gi.courseid=? AND gi.iteminstance=? AND m.id=?',
                        [$val['course_id'], $mod->instance, $mod->module]
                    );
                    if ($grade) {
                        $self_grade = $DB->get_record_sql('SELECT finalgrade FROM {grade_grades} WHERE itemid=? AND userid=?', [$grade->id, $k]);
                        if ($self_grade) {
                            $finalgrade = $self_grade->finalgrade;
                        } else {
                            $finalgrade = 0;
                        }
                        $grademax = $grade->grademax;
                    } else {
                        $grademax = $finalgrade = 0;
                    }
                    $role = $DB->get_record_sql('SELECT
                            GROUP_CONCAT(ra.roleid)roleid
                        FROM {role_assignments} ra
                        JOIN {context} ct ON ra.contextid=ct.id
                        WHERE ra.userid=?
                        AND ct.instanceid =?', [$k, $val['course_id']]);
                    if ($record) {
                        $record->access_num += $val['access'];
                        $record->download += isset($val['download']) ? $val['download'] : 0;
                        $record->spend_time += isset($val['spend_time']) ? $val['spend_time'] : 0;
                        $record->finalgrade = $finalgrade;
                        $record->grademax = $grademax;
                        $record->mod_name = $tables[$val['type']];
                        $record->role = $role ? $role->roleid : 0;

                        $DB->update_record('block_data_screen_visit', $record);
                    } else {
                        $instanceName = $DB->get_record_select($tables[$val['type']], ' id=?', [$mod->instance], 'name');
                        if (!$instanceName) continue;
                        $record = new \stdClass();
                        $record->user_id = $k;
                        $record->type = $val['type'];
                        $record->instance_id = $key;
                        $record->course_id = $val['course_id'];
                        $record->access_num = $val['access'];
                        $record->download = isset($val['download']) ? $val['download'] : 0;
                        $record->create_num = 0;
                        $record->spend_time = isset($val['spend_time']) ? $val['spend_time'] : 0;
                        $record->section = $mod->section;
                        $record->grademax = $grademax;
                        $record->finalgrade = $finalgrade;
                        $record->mod_name = $tables[$val['type']];
                        $record->instance_name = $instanceName ? $instanceName->name : '';
                        $record->role = $role ? $role->roleid : '0';

                        $new_records[] = $record;
                    }
                }
            }

            foreach ($new_courses as $key => $value) {
                foreach ($value as $k => $val) {
                    $record = $DB->get_record_select(
                        'block_data_screen_visit',
                        ' type=? AND course_id=? AND user_id=?',
                        [0, $key, $k],
                        'id,type,instance_id,access_num,spend_time,role,create_num'
                    );
                    $role = $DB->get_record_sql('SELECT
                        GROUP_CONCAT(ra.roleid)roleid
                    FROM {role_assignments} ra
                    JOIN {context} ct ON ra.contextid=ct.id
                    WHERE ra.userid=?
                    AND ct.instanceid =?', [$k, $key]);
                    if ($record) {
                        $record->access_num += $val['access'];
                        $record->spend_time += isset($val['spend_time']) ? $val['spend_time'] : 0;
                        $record->create_num += isset($val['create_num']) ? $val['create_num'] : 0;
                        $record->role = $role ? $role->roleid : 0;
                        $DB->update_record('block_data_screen_visit', $record);
                    } else {
                        $record = new \stdClass();
                        $record->user_id = $k;
                        $record->type = 0;
                        $record->instance_id = 0;
                        $record->course_id = $key;
                        $record->access_num = $val['access'];
                        $record->download = 0;
                        $record->create_num = isset($val['create_num']) ? $val['create_num'] : 0;
                        $record->spend_time = isset($val['spend_time']) ? $val['spend_time'] : 0;
                        $record->section = '';
                        $record->grademax = 0;
                        $record->finalgrade = 0;
                        $record->mod_name = '';
                        $record->instance_name = '';
                        $record->role = $role ? $role->roleid : '0';

                        $new_records[] = $record;
                    }
                }
            }
            if ($new_records) {
                $DB->insert_records('block_data_screen_visit', $new_records);
            }
        }

    } else {
        $logs = $DB->get_records_select(
            'logstore_standard_log',
            ' timecreated>?',
            [$accessRecord->end_time],
            'timecreated ASC',
            'id,userid,courseid,timecreated,component,action,target,contextinstanceid,crud,anonymous,contextlevel'
        );
        $new_mods = $new_courses = $accessTime = [];
        foreach ($logs as $value) {
            if (array_key_exists($value->component, $mods)) {
                // access module type
                $new_mods[$value->contextinstanceid][$value->userid]['type'] = $mods[$value->component];
                // access module number
                if ($value->crud == 'r' && $value->anonymous == 0 && $value->contextlevel == CONTEXT_MODULE) {
                    $new_mods[$value->contextinstanceid][$value->userid]['access'] =
                        isset($new_mods[$value->contextinstanceid][$value->userid]['access'])
                            ? $new_mods[$value->contextinstanceid][$value->userid]['access'] + 1
                            : 1;
                }
                // download resource number
                if ($value->action == 'downloaded') {
                    $new_mods[$value->contextinstanceid][$value->userid]['download'] =
                        isset($new_mods[$value->contextinstanceid][$value->userid]['download'])
                            ? $new_mods[$value->contextinstanceid][$value->userid]['download'] + 1
                            : 1;
                }
                // access module time
                if (isset($accessTime[$value->contextinstanceid][$value->userid])) {
                    $time = $value->timecreated - $accessTime[$value->contextinstanceid][$value->userid];
                    if ($time < ($logout * 60)) {
                        $new_mods[$value->contextinstanceid][$value->userid]['spend_time'] =
                            isset($new_mods[$value->contextinstanceid][$value->userid]['spend_time'])
                                ? $new_mods[$value->contextinstanceid][$value->userid]['spend_time'] + $time
                                : $time;
                    }
                }
                // access course id
                $new_mods[$value->contextinstanceid][$value->userid]['course_id'] = $value->courseid;
            }
            if ($value->courseid > 1 && $value->userid > 0) {
                // access course number
                $new_courses[$value->courseid][$value->userid]['access'] =
                    isset($new_courses[$value->courseid][$value->userid]['access'])
                        ? $new_courses[$value->courseid][$value->userid]['access'] + 1
                        : 1;
                // access course time
                if (isset($accessTime[$value->courseid][$value->userid])) {
                    $time = $value->timecreated - $accessTime[$value->courseid][$value->userid];
                    if ($time < ($logout * 60)) {
                        $new_courses[$value->courseid][$value->userid]['spend_time'] =
                            isset($new_mods[$value->courseid][$value->userid]['spend_time'])
                                ? $new_mods[$value->courseid][$value->userid]['spend_time'] + $time
                                : $time;
                    }
                }
                // created module number
                if ($value->target == 'course_module' && $value->action == 'created') {
                    $new_courses[$value->courseid][$value->userid]['create_num'] =
                        isset($new_courses[$value->courseid][$value->userid]['create_num'])
                            ? $new_courses[$value->courseid][$value->userid]['create_num'] + 1
                            : 1;
                }
            }

            $accessTime[$value->courseid][$value->userid] = $value->timecreated;
        }
        foreach ($new_mods as $key => $value) {
            /**
             * $key     => course_module id
             * $value   => array
             */
            foreach ($value as $k => $val) {
                /**
                 * $k   => user id
                 * $val => array('type', 'access', 'download', 'spend_time', 'course_id')
                 */
                $record = $DB->get_record_select(
                    'block_data_screen_visit',
                    ' type=? AND instance_id=? AND user_id=?'
                    , [$val['type'], $key, $k],
                    'id,type,instance_id,access_num,download,spend_time,finalgrade,role'
                );
                $mod = $DB->get_record_sql('SELECT cm.instance,cm.module,cs.section FROM {course_modules} cm JOIN {course_sections} cs ON cm.section=cs.id WHERE cm.id=?', [$key]);
                if (!$mod) continue;
                $grade = $DB->get_record_sql('SELECT gi.id,gi.grademax FROM {grade_items} gi JOIN {modules} m ON m.name=gi.itemmodule WHERE gi.courseid=? AND gi.iteminstance=? AND m.id=?', [$val['course_id'], $mod->instance, $mod->module]);
                if ($grade) {
                    $self_grade = $DB->get_record_sql('SELECT finalgrade FROM {grade_grades} WHERE itemid=? AND userid=?', [$grade->id, $k]);
                    if ($self_grade) {
                        $finalgrade = $self_grade->finalgrade;
                    } else {
                        $finalgrade = 0;
                    }
                    $grademax = $grade->grademax;
                } else {
                    $grademax = $finalgrade = 0;
                }
                if (!$record) {
                    $role = $DB->get_record_sql('SELECT GROUP_CONCAT(ra.roleid)roleid FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE ra.userid=? AND ct.instanceid =?', [$k, $val['course_id']]);
                    $instanceName = $DB->get_record_select($tables[$val['type']], ' id=?', [$mod->instance], 'name');
                    if (!$instanceName) continue;
                    $record = new \stdClass();
                    $record->user_id = $k;
                    $record->type = $val['type'];
                    $record->instance_id = $key;
                    $record->access_num = $val['access'];
                    $record->download = isset($val['download']) ? $val['download'] : 0;
                    $record->spend_time = isset($val['spend_time']) ? $val['spend_time'] : 0;
                    $record->section = $mod->section;
                    $record->grademax = $grademax;
                    $record->finalgrade = $finalgrade;
                    $record->mod_name = $tables[$val['type']];
                    $record->instance_name = $instanceName ? $instanceName->name : '';
                    $record->role = $role ? $role->roleid : 0;
                    $record->course_id = $val['course_id'];
                    $record->create_num = 0;

                    $DB->insert_record('block_data_screen_visit', $record);
                } else {
                    $role = $DB->get_record_sql('SELECT GROUP_CONCAT(ra.roleid)roleid FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE ra.userid=? AND ct.instanceid =?', [$k, $val['course_id']]);
                    $record->access_num += $val['access'];
                    $record->download += isset($val['download']) ? $val['download'] : 0;
                    $record->spend_time += isset($val['spend_time']) ? $val['spend_time'] : 0;
                    $record->finalgrade = $finalgrade;
                    $record->grademax = $grademax;
                    $record->mod_name = $tables[$val['type']];
                    $record->role = $role ? $role->roleid : 0;
                    $DB->update_record('block_data_screen_visit', $record);
                }
            }
        }
        foreach ($new_courses as $key => $value) {
            /**
             * $key     => course id
             * $value   => array
             */
            foreach ($value as $k => $val) {
                /**
                 * $k   => user id
                 * $val => array('access', 'spend_time', 'create_num')
                 */
                $record = $DB->get_record_select('block_data_screen_visit', ' type=? AND course_id=? AND user_id=?', [0, $key, $k], 'id,type,instance_id,access_num,spend_time,role,create_num');
                if (!$record) {
                    $role = $DB->get_record_sql('SELECT GROUP_CONCAT(ra.roleid)roleid FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE ra.userid=? AND ct.instanceid =?', [$k, $key]);
                    $record = new \stdClass();
                    $record->user_id = $k;
                    $record->type = 0;
                    $record->instance_id = 0;
                    $record->course_id = $key;
                    $record->access_num = $val['access'];
                    $record->download = 0;
                    $record->create_num = isset($val['create_num']) ? $val['create_num'] : 0;
                    $record->spend_time = isset($val['spend_time']) ? $val['spend_time'] : 0;
                    $record->section = '';
                    $record->grademax = 0;
                    $record->finalgrade = 0;
                    $record->mod_name = '';
                    $record->instance_name = '';
                    $record->role = $role ? $role->roleid : '0';

                    $DB->insert_record('block_data_screen_visit', $record);
                } else {
                    $role = $DB->get_record_sql('SELECT GROUP_CONCAT(ra.roleid)roleid FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE ra.userid=? AND ct.instanceid =?', [$k, $key]);
                    $record->access_num += $val['access'];
                    $record->spend_time += isset($val['spend_time']) ? $val['spend_time'] : 0;
                    $record->create_num += isset($val['create_num']) ? $val['create_num'] : 0;
                    $record->role = $role ? $role->roleid : 0;
                    $DB->update_record('block_data_screen_visit', $record);
                }
            }
        }

    }
    statisticsLog('flushStatisticsVisit', 1);

}


/**
 * Statistics of courses offered by each college:
 *      college id,idnumber,path,name,course number, students number,teacher number,resource number,activity number,
 *      type,updatedd time,access number
 *
 * It runs after flushStatisticsVisit method
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function flushCollegeTotal()
{
    $actionRecord = statisticsLog('flushCollegeTotal', 0);
    if (!$actionRecord) return false;

    global $DB, $PAGE;
    $DB->execute('TRUNCATE {block_data_screen_college}');

    $mods = get_mod_id();
    $colleges = $DB->get_records_sql('SELECT
        cc.id college_id,
        cc.idnumber,
        cc.path,
        cc.name,
        (SELECT COUNT(*) FROM {course} c JOIN {course_categories} cate ON cate.id=c.category WHERE c.id <> 1 AND SUBSTRING_INDEX(cate.path, "/", -1)=cc.id) course_num,
        (SELECT COUNT(distinct ra.userid)
            FROM {role_assignments} ra
            JOIN {context} ct ON ct.id=ra.contextid
            JOIN {course} c ON c.id=ct.instanceid
            JOIN {course_categories} cate ON cate.id=c.category
            WHERE SUBSTRING_INDEX(cate.path, "/", -1)=cc.id
            AND ra.roleid=5
        ) student_num,
        (SELECT COUNT(DISTINCT ra.userid)
            FROM {role_assignments} ra
            JOIN {context} ct ON ct.id=ra.contextid
            JOIN {course} c ON c.id=ct.instanceid
            JOIN {course_categories} cate ON cate.id=c.category
            WHERE SUBSTRING_INDEX(cate.path, "/", -1)=cc.id
            AND ra.roleid=3
        ) teacher_num,
        (SELECT
            COUNT(DISTINCT cm.id)
            FROM {course_modules} cm
            JOIN {course} c ON c.id=cm.course
            JOIN {course_categories} cate ON cate.id=c.category
            WHERE SUBSTRING_INDEX(cate.path, "/", -1)=cc.id
            AND cm.module IN (' . $mods['resource'] . ')
        ) resource_num,
        (SELECT
            COUNT(DISTINCT cm.id)
            FROM {course_modules} cm
            JOIN {course} c ON c.id=cm.course
            JOIN {course_categories} cate ON cate.id=c.category
            WHERE SUBSTRING_INDEX(cate.path, "/", -1)=cc.id
            AND cm.module IN (' . $mods['activity'] . ')
        ) activity_num,
        1 type,
        UNIX_TIMESTAMP(NOW()) updated_time
    FROM {course_categories} cc;');

    foreach ($colleges as $key => $value) {
        $colleges[$key]->access_num = $DB->get_record_sql('SELECT SUM(access_num) access FROM {block_data_screen_visit} WHERE type=0 AND instance_id IN (SELECT id FROM {course} WHERE category=?)', [$value->college_id])->access ?: 0;
    }

    $DB->insert_records('block_data_screen_college', $colleges);

    // Statistics teacher data
    $teacher = $DB->get_records_sql('SELECT DISTINCT u.id FROM {user} u JOIN {role_assignments} ra ON ra.userid=u.id AND ra.roleid=3');

    foreach ($teacher as $key => $value) {
        $teacherTotal = $DB->get_record_sql('SELECT
                ra.userid teacher_id,
                u.firstname name,
                u.department dept,
                COUNT(DISTINCT c.id) course_num,
                (SELECT
                    COUNT(DISTINCT ra.userid)
                    FROM {role_assignments} ra
                    WHERE ra.contextid IN (SELECT r.contextid FROM {role_assignments} r JOIN {user} user ON user.id=r.userid WHERE r.roleid=3 AND user.id=u.id)
                    AND ra.roleid=5
                ) student_num,
                (SELECT
                    COUNT(cm.id)
                    FROM {course_modules} cm
                    WHERE cm.module IN (' . $mods['resource'] . ')
                    AND cm.course IN (SELECT t.instanceid FROM {role_assignments} r JOIN {context} t ON t.id=r.contextid WHERE r.roleid=3 AND r.userid=u.id)
                ) resource_num,
                (SELECT
                    COUNT(cm.id)
                    FROM {course_modules} cm
                    WHERE cm.module IN (' . $mods['activity'] . ')
                    AND cm.course IN (SELECT t.instanceid FROM {role_assignments} r JOIN {context} t ON t.id=r.contextid WHERE r.roleid=3 AND r.userid=u.id)
                ) activity_num,
                0 type,
                UNIX_TIMESTAMP(NOW()) updated_time,
                u.timecreated
            FROM {user} u
            JOIN {role_assignments} ra ON ra.userid=u.id
            JOIN {context} ct ON ct.id=ra.contextid
            JOIN {course} c ON c.id=ct.instanceid
            WHERE u.id=?
            AND ra.roleid=3;', [$value->id]);

        // get user avatar
        $user = $DB->get_record_sql("SELECT * FROM {user} WHERE id=?", [$value->id]);
        $userpicture = new \user_picture($user);
        $userpicture->size = 1; // Size f1.
        $teacherTotal->url = $userpicture->get_url($PAGE)->out(false);

        $DB->insert_record('block_data_screen_college', $teacherTotal);
    }

    statisticsLog('flushCollegeTotal', 1);
}


/**
 * Platform overview:
 *      effective course number,course number,number of courses per capita,students number,teacher number,updated time
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function flushDataTotal()
{
    $actionRecord = statisticsLog('flushDataTotal', 0);
    if (!$actionRecord) return false;

    global $DB;

    $DB->execute('TRUNCATE {block_data_screen_platform}');
    // All
    $studentCounts = $DB->get_record_sql('SELECT COUNT(DISTINCT userid) counts FROM {role_assignments} WHERE roleid=5')->counts;
    $teacherCounts = $DB->get_record_sql('SELECT COUNT(*) counts FROM {block_data_screen_college} WHERE type=0')->counts;
    // $beChosen = $DB->get_record_sql('SELECT COUNT(DISTINCT c.id) counts FROM {course} c JOIN {context} ct ON ct.instanceid=c.id JOIN {role_assignments} ra ON ra.contextid=ct.id WHERE ra.roleid=5')->counts;
    $beChosen = $DB->get_record_sql("select round(avg(a.counts),0) as num from (SELECT COUNT(userid) as counts FROM `mdl_user_enrolments` GROUP BY userid) as a")->num ?: 0;

    $test = '%' . get_string('test', 'block_data_screen') . '%';
    $dataTotal = $DB->get_record_sql('SELECT
            (SELECT COUNT(*)
              FROM {block_data_screen_course}
              WHERE full_name NOT LIKE ?
              AND short_name NOT LIKE ?
              AND teacher_counts>0
              AND (resource_num+activity_num)>0
            ) effective_num,
            SUM(course_num) course_num,
            ? teacher_num,
            ? student_num,
            ? percourse_num,
            UNIX_TIMESTAMP(NOW()) updated_time
         FROM {block_data_screen_college}
         WHERE type=1;', [$test, $test, $teacherCounts, $studentCounts, $beChosen]);

    $DB->insert_record('block_data_screen_platform', $dataTotal);
    // Nearly a month
    $month = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
    $monthStu = $DB->get_record_sql('SELECT COUNT(DISTINCT userid) counts FROM {role_assignments} WHERE roleid=5 AND timemodified > ?', [$month])->counts ?: 0;
    $monthTeach = $DB->get_record_sql('SELECT COUNT(*) counts FROM {block_data_screen_college} WHERE type=0 AND timecreated > ?', [$month])->counts ?: 0;
    // $monthChosen = $DB->get_record_sql('SELECT COUNT(DISTINCT c.id) counts FROM {course} c JOIN {context} ct ON ct.instanceid=c.id JOIN {role_assignments} ra ON ra.contextid=ct.id WHERE ra.roleid=5 AND ra.timemodified > ?', [$month])->counts ?: 0;
    $monthChosen = $DB->get_record_sql("select round(avg(a.counts),0) as num from (SELECT COUNT(userid) as counts FROM `mdl_user_enrolments` WHERE timecreated > ? GROUP BY userid) as a", [$month])->num ?: 0;
    // $avg = (int)$monthChosen ? round((int)$monthChosen / (int)$monthStu) : 0;
    $dataTotal = $DB->get_record_sql('SELECT
            (SELECT COUNT(*)
              FROM {block_data_screen_course}
              WHERE full_name NOT LIKE ?
              AND short_name NOT LIKE ?
              AND teacher_counts>0
              AND time_created>?
              AND (resource_num+activity_num)>0
            ) effective_num,
            (SELECT COUNT(*) FROM {course} WHERE timecreated > ? AND id <> 1) course_num,
            ? teacher_num,
            ? student_num,
            ? percourse_num,
            UNIX_TIMESTAMP(NOW()) updated_time
         FROM {block_data_screen_college}', [$test, $test, $month, $month, $monthTeach, $monthStu, $monthChosen]);

    $DB->insert_record('block_data_screen_platform', $dataTotal);

    statisticsLog('flushDataTotal', 1);
}


/**
 * Collect forum data:
 *      form,name,course,teacher posts,reply teacher,student posts,reply student
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function flushForumTotal()
{
    $actionRecord = statisticsLog('flushForumTotal', 0);
    if (!$actionRecord) return false;

    global $DB;

    $DB->execute('TRUNCATE {block_data_screen_forum}');

    $forums = $DB->get_records_sql('SELECT
        f.id forum,
        f.name,
        f.course,
        (SELECT COUNT(fd.id) FROM {forum_discussions} fd JOIN {context} ct ON ct.instanceid=fd.course JOIN {role_assignments} ra ON ra.contextid=ct.id WHERE fd.forum=f.id AND fd.userid=ra.userid AND ra.roleid=3) teacher_posts,
        (SELECT COUNT(fp.id) FROM {forum_posts} fp JOIN {forum_discussions} fd ON fd.id=fp.discussion JOIN {context} ct ON ct.instanceid=fd.course JOIN {role_assignments} ra ON ra.contextid=ct.id WHERE ra.userid=fd.userid AND ra.roleid=3 AND fp.parent<>0 AND fd.forum=f.id) reply_teacher,
        (SELECT COUNT(fd.id) FROM {forum_discussions} fd JOIN {context} ct ON ct.instanceid=fd.course JOIN {role_assignments} ra ON ra.contextid=ct.id WHERE fd.forum=f.id AND fd.userid=ra.userid AND ra.roleid=5) student_posts,
        (SELECT COUNT(fp.id) FROM {forum_posts} fp JOIN {forum_discussions} fd ON fd.id=fp.discussion JOIN {context} ct ON ct.instanceid=fd.course JOIN {role_assignments} ra ON ra.contextid=ct.id WHERE ra.userid=fd.userid AND ra.roleid=5 AND fp.parent<>0 AND fd.forum=f.id) reply_student
    FROM {forum} f;');
    $module = $DB->get_record_sql('SELECT id FROM {modules} WHERE name="forum"')->id;
    foreach ($forums as $key => $value) {
        $forums[$key]->section = $DB->get_record_sql('SELECT cs.section FROM {course_modules} cm JOIN {course_sections} cs ON cm.section=cs.id WHERE cm.module=? AND cm.course=? AND cm.instance=?', [$module, $value->course, $value->forum])->section;
    }

    $DB->insert_records('block_data_screen_forum', $forums);

    statisticsLog('flushForumTotal', 1);
}


/**
 * Collect assignments and quizzes:
 *      course,activity_id,post_counts,grade_avg,type
 *
 * @return bool
 * @throws dml_exception
 */
function flushQuizAssign()
{
    $actionRecord = statisticsLog('flushQuizAssign', 0);
    if (!$actionRecord) return false;

    global $DB;

    $DB->execute('TRUNCATE {block_data_screen_quiz}');

    $modules = $DB->get_records_sql('SELECT id,name FROM {modules}');
    $table = [];
    foreach ($modules as $value) {
        $table[$value->id] = $value->name;
    }

    $resource = $DB->get_records_sql('SELECT
        cm.id,
        cm.course course_id,
        cm.module resource_type,
        cm.instance resource_id
    FROM {course_modules} cm
    WHERE cm.module IN (' . implode(',', array_keys($table)) . ')');

    $activity = new \stdClass;

    foreach ($resource as $key => $value) {
        switch ($table[$value->resource_type]) {
            case 'assign':
                $activity->post_counts = $DB->get_record_sql('SELECT COUNT(*)counts FROM {assign_submission} WHERE assignment=? AND status="submitted"', [$value->resource_id])->counts;
                $activity->grade_avg = $DB->get_record_sql('SELECT AVG(grade)avg FROM {assign_grades} WHERE assignment=? AND grade>=0', [$value->resource_id])->avg;
                $activity->activity_id = $value->id;
                $activity->type = 0;
                $activity->course = $value->course_id;
                $DB->insert_record('block_data_screen_quiz', $activity);
                break;
            case 'quiz':
                $quiz = $DB->get_record_sql('SELECT COUNT(*)counts,AVG(grade)avg FROM {quiz_grades} WHERE quiz=? AND grade>=0', [$value->resource_id]);
                $activity->post_counts = $quiz->counts;
                $activity->grade_avg = $quiz->avg;
                $activity->activity_id = $value->id;
                $activity->type = 1;
                $activity->course = $value->course_id;
                $DB->insert_record('block_data_screen_quiz', $activity);
                break;
        }
    }

    statisticsLog('flushQuizAssign', 1);
}


/**
 * Statistics log
 *
 * @param $method
 * @param $status
 * @return bool
 * @throws dml_exception
 */
function statisticsLog($method, $status)
{
    global $DB;

    $log = $DB->get_record_sql('SELECT * FROM {block_data_screen_stats_log} WHERE method=? AND status=0', [$method]);

    switch ($status) {
        case 0:
            if ($log) return false;
            $log = new \stdClass;
            $log->status = 0;
            $log->method = $method;
            $log->start_time = time();
            $DB->insert_record('block_data_screen_stats_log', $log);
            break;
        case 1:
            if (!$log) return false;
            $log->status = 1;
            $log->end_time = time();
            $DB->update_record('block_data_screen_stats_log', $log);
            break;
    }
    return true;
}


/**
 * Get modules id
 *
 * @return array
 * @throws coding_exception
 * @throws dml_exception
 */
function get_mod_id()
{
    global $CFG, $DB;

    $mod_arr = [];
    $modnames = $DB->get_records_sql('SELECT id,name FROM {modules}');
    foreach ($modnames as $mod) {
        $component = clean_param('mod_' . $mod->name, PARAM_COMPONENT);
        if (file_exists("$CFG->dirroot/mod/$mod->name/lib.php")) {
            include_once("$CFG->dirroot/mod/$mod->name/lib.php");
            $function = $component . '_supports';
            if (!function_exists($function)) {
                // Legacy non-frankenstyle function name.
                $function = $mod->name . '_supports';
            }
        }

        if ($function and function_exists($function)) {
            $supports = $function(FEATURE_MOD_ARCHETYPE);
            if (is_null($supports)) {
                // Plugin does not know - use default.
                $mod_arr['activity'][] = $mod->id;
            } else {
                $mod_arr['resource'][] = $mod->id;
            }
        }
    }
    $rtn = [
        'activity' => isset($mod_arr['activity']) ? implode(',', $mod_arr['activity']) : '0',
        'resource' => isset($mod_arr['activity']) ? implode(',', $mod_arr['resource']) : '0'
    ];
    return $rtn;
}


/**
 * Get the course image if added to course.
 *
 * @param $course
 * @return bool|string
 * @throws moodle_exception
 */
function get_course_image($course)
{
    global $CFG;
    if ($CFG->version >= 2018120303) {
        $courseinlist = new \core_course_list_element($course);
    } else {
        $courseinlist = new \course_in_list($course); // 3.5 version
    }
    foreach ($courseinlist->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $pathcomponents = [
                '/pluginfile.php',
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea() . $file->get_filepath() . $file->get_filename()
            ];
            $path = implode('/', $pathcomponents);
            return (new \moodle_url($path))->out();
        }
    }
    return false;
}


/**
 * Get the course pattern datauri.
 *
 * The datauri is an encoded svg that can be passed as a url.
 * @param object $course
 * @return string datauri
 */
function get_course_pattern($course)
{
    $color = coursecolor($course->id);
    $pattern = new \core_geopattern();
    $pattern->setColor($color);
    $pattern->patternbyid($course->id);
    return $pattern->datauri();
}


/**
 * Get the course color.
 *
 * @param int $courseid
 * @return string hex color code.
 */
function coursecolor($courseid)
{
    // The colour palette is hardcoded for now. It would make sense to combine it with theme settings.
    $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894',
        '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];

    $color = $basecolors[$courseid % 10];
    return $color;
}

function get_course_teacher($courseid){
    global $DB;
    return $DB->get_records_sql("SELECT  c.id,c.fullname, (SELECT GROUP_CONCAT(u.firstname SEPARATOR',')
                                FROM mdl_role_assignments ra 
                                JOIN mdl_context ct ON ct.id=ra.contextid 
                                JOIN mdl_user u on u.id=ra.userid 
                                WHERE ct.instanceid=c.id AND ra.roleid = 3)   teachers
                                FROM mdl_course c   WHERE  c.id  = $courseid");
}

function get_course_stu($courseid){

    global $DB;
    return $DB->count_records_sql("SELECT COUNT(userid) FROM {role_assignments} r 
                                    JOIN {context} con ON con.id=r.contextid 
                                    WHERE con.instanceid=$courseid AND r.roleid=5");

}

function get_course_realstu($starttime,$endtime,$courseid){

    global $DB;
    return $DB->count_records_sql("SELECT COUNT(DISTINCT userid) FROM {logstore_standard_log}
                                    WHERE  timecreated>" . $starttime . " AND timecreated<" . $endtime . " 
                                    AND courseid=$courseid
                                    AND anonymous=0 AND action='viewed' AND userid<>1");
}