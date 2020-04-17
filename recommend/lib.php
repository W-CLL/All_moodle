<?php
defined('MOODLE_INTERNAL') || die();


// function re_OneHour()
// {
//     flushAccessStatistics();
//     flushAccessTop();
// }

function re_OneDay()
{
    re_flushStatisticsCourse(); 
    re_flushCollegeTotal();
    re_flushForumTotal();
    re_flushStatisticsVisit();
}


function re_flushStatisticsCourse()
{
   //$actionRecord = statisticsLog('re_flushStatisticCourse', 0);
    // check if there was a process running
   //if (!$actionRecord) return false;

    global $DB;
    $block = $DB->get_record_sql("SELECT id FROM {block} WHERE name='associated_course'");
    $DB->execute('TRUNCATE {block_recommend_course}');

    $mods = re_get_mod_id();

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
        (SELECT GROUP_CONCAT(ra.userid) FROM {role_assignments} ra JOIN {context} ct ON ct.id=ra.contextid WHERE ct.instanceid=c.id AND ra.roleid=3) teachers,
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
                $open_times = $DB->get_record_sql("SELECT COUNT(id) counts FROM {block_course_type} WHERE typeid=?", [$type->typeid])->counts + 1;
            } else {
                $open_times = $DB->get_record_sql("SELECT id FROM {block_type} WHERE courseid=?", [$value->course_id]) ? 1 : 0;
            }
        }
        $courseTotal[$key]->open_times = isset($open_times) ? $open_times : 0;
        $courseTotal[$key]->tags = $DB->get_record_sql("SELECT GROUP_CONCAT(t.name) tags FROM {tag_instance} ti JOIN {tag} t ON t.id=ti.tagid WHERE ti.itemtype='course' AND ti.itemid=?", [$value->course_id])->tags;

        // get course cover
        $course = $DB->get_record_sql("SELECT * FROM {course} WHERE id=?", [$value->course_id]);
        $courseImage = re_get_course_image($course);
        if (!$courseImage) {
            $courseImage = re_get_course_pattern($course);
        }
        $courseTotal[$key]->img = $courseImage;
    }

    $DB->insert_records('block_recommend_course', $courseTotal);


    //statisticsLog('flushStatisticCourse', 1);
}


function re_flushCollegeTotal()
{
    // $actionRecord = statisticsLog('flushCollegeTotal', 0);
    // if (!$actionRecord) return false;

    global $DB, $PAGE;
    $DB->execute('TRUNCATE {block_recommend_college}');

    $mods = re_get_mod_id();
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
        $colleges[$key]->access_num = $DB->get_record_sql('SELECT SUM(access_num) access FROM {block_recommend_visit} WHERE type=0 AND instance_id IN (SELECT id FROM {course} WHERE category=?)', [$value->college_id])->access ?: 0;
    }

    $DB->insert_records('block_recommend_college', $colleges);

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

        $DB->insert_record('block_recommend_college', $teacherTotal);
    }

    //statisticsLog('flushCollegeTotal', 1);
}


function re_flushForumTotal()
{
    // $actionRecord = statisticsLog('flushForumTotal', 0);
    // if (!$actionRecord) return false;

    global $DB;

    $DB->execute('TRUNCATE {block_recommend_forum}');

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

    $DB->insert_records('block_recommend_forum', $forums);

    //statisticsLog('flushForumTotal', 1);
}

function re_flushStatisticsVisit()
{
    // $actionRecord = statisticsLog('flushStatisticsVisit', 0);
    // if (!$actionRecord) return false;

    global $DB, $CFG;

    $mods = $tables = [];
    $mod = $DB->get_records('modules', [], '', 'id,name');
    foreach ($mod as $value) {
        $mods['mod_' . $value->name] = $value->id;
        $tables[$value->id] = $value->name;
    }

    // check if this is the first time to run this function
    //$accessRecord = $DB->get_record_sql('SELECT MAX(end_time) end_time FROM {block_recommend_stats_log} WHERE method="flushStatisticsVisit" AND status=1');
    
    $logout = 30;
    if (1) {
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
                        'block_recommend_visit',
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

                        $DB->update_record('block_recommend_visit', $record);
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
                        'block_recommend_visit',
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
                        $DB->update_record('block_recommend_visit', $record);
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
                $DB->insert_records('block_recommend_visit', $new_records);
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
                    'block_recommend_visit',
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

                    $DB->insert_record('block_recommend_visit', $record);
                } else {
                    $role = $DB->get_record_sql('SELECT GROUP_CONCAT(ra.roleid)roleid FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE ra.userid=? AND ct.instanceid =?', [$k, $val['course_id']]);
                    $record->access_num += $val['access'];
                    $record->download += isset($val['download']) ? $val['download'] : 0;
                    $record->spend_time += isset($val['spend_time']) ? $val['spend_time'] : 0;
                    $record->finalgrade = $finalgrade;
                    $record->grademax = $grademax;
                    $record->mod_name = $tables[$val['type']];
                    $record->role = $role ? $role->roleid : 0;
                    $DB->update_record('block_recommend_visit', $record);
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
                $record = $DB->get_record_select('block_recommend_visit', ' type=? AND course_id=? AND user_id=?', [0, $key, $k], 'id,type,instance_id,access_num,spend_time,role,create_num');
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

                    $DB->insert_record('block_recommend_visit', $record);
                } else {
                    $role = $DB->get_record_sql('SELECT GROUP_CONCAT(ra.roleid)roleid FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE ra.userid=? AND ct.instanceid =?', [$k, $key]);
                    $record->access_num += $val['access'];
                    $record->spend_time += isset($val['spend_time']) ? $val['spend_time'] : 0;
                    $record->create_num += isset($val['create_num']) ? $val['create_num'] : 0;
                    $record->role = $role ? $role->roleid : 0;
                    $DB->update_record('block_recommend_visit', $record);
                }
            }
        }

    }
    //statisticsLog('flushStatisticsVisit', 1);

}




function re_get_mod_id()
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


function re_get_course_image($course)
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


function re_get_course_pattern($course)
{
    $color = re_coursecolor($course->id);
    $pattern = new \core_geopattern();
    $pattern->setColor($color);
    $pattern->patternbyid($course->id);
    return $pattern->datauri();
}

function re_coursecolor($courseid)
{
    // The colour palette is hardcoded for now. It would make sense to combine it with theme settings.
    $basecolors = ['#81ecec', '#74b9ff', '#a29bfe', '#dfe6e9', '#00b894',
        '#0984e3', '#b2bec3', '#fdcb6e', '#fd79a8', '#6c5ce7'];

    $color = $basecolors[$courseid % 10];
    return $color;
}