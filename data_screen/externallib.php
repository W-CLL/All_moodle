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
 * External  block_data_screen API
 *
 * @package    block_data_screen
 * @category   external
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");


class block_data_screen_external extends external_api
{
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function platform_overview_parameters()
    {
        return new external_function_parameters([]);
    }

    /**
     * Platform overview
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function platform_overview()
    {
        global $DB, $PAGE;

        $rtn = [];

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $month = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
        $lastmonth = date("Y-m-t", strtotime("-1 month", time()));

        $header         = $DB->get_record('block_data_screen_platform', ['id' => 1]);
        $nearlymonth    = $DB->get_record('block_data_screen_platform', ['id' => 2]);
        $pv             = $DB->get_record_sql('SELECT SUM(pv)pv FROM {block_data_screen_access} WHERE date > ?', [$month])->pv;
        $last_pv        = $DB->get_record_sql('SELECT SUM(pv)pv FROM {block_data_screen_access} WHERE date > ? AND date < ?', [$lastmonth, $month])->pv;
        $class          = $DB->count_records_sql("SELECT COUNT(DISTINCT courseid) FROM `mdl_logstore_standard_log`WHERE timecreated>? AND crud='r' AND contextlevel=50 AND courseid<>1", [time()-(10*60)]);
        $student        = $DB->count_records_sql("SELECT COUNT( DISTINCT userid) FROM `mdl_logstore_standard_log`WHERE timecreated>? AND courseid=0 AND anonymous=0 AND action='loggedin' ", [time()-(120*60)]);

        $rtn['platform'] = [
            'course_num'    => $header->course_num ?: 0,
            'course_add'    => $header->course_num ? round($nearlymonth->course_num / $header->course_num, 4) * 100 . "%" : '',
            'teacher_num'   => $header->teacher_num ?: 0,
            'teacher_add'   => $header->teacher_num ? round($nearlymonth->teacher_num / $header->teacher_num, 4) * 100 . "%" : '',
            'student_num'   => $header->student_num ?: 0,
            'student_add'   => $header->student_num ? round($nearlymonth->student_num / $header->student_num, 4) * 100 . "%" : '',
            'effective_num' => $header->effective_num ?: 0,
            'effective_add' => $header->effective_num ? round($nearlymonth->effective_num / $header->effective_num, 4) * 100 . "%" : '',
            'percourse_num' => $header->percourse_num ?: 0,
            'percourse_add' => $header->percourse_num ? round($nearlymonth->percourse_num / $header->percourse_num, 4) * 100 . "%" : '',
            'pv_num'            => $pv ?: 0,
            'pv_add'        => $last_pv ? round($pv / $last_pv, 4) * 100 . "%" : '',
            'realclass_num'         => $class,
            'realclass_add'         => 0,
            'realstudent_num'       => $student,
            'realstudent_add'       => 0,
        ];

        $semester       = $DB->get_records_sql('SELECT * from {block_data_screen_semester} ORDER BY start_time ASC LIMIT 10');
        $semester_arr   = [0=>get_string('up', 'block_data_screen'), 1=>get_string('down', 'block_data_screen')];
        $rtn['effective']   = [];
        $rtn['pv']          = [];
        foreach ($semester as $value) {
            $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM {block_data_screen_course} WHERE start_time>? AND start_time<?", [$value->start_time, $value->end_time])->counts;
            $pv     = $DB->get_record_sql('SELECT SUM(pv)pv FROM {block_data_screen_access} WHERE date>? AND date<?', [$value->start_time, $value->end_time])->pv;
            $title  = $value->year . " " . $semester_arr[$value->semester];
            $rtn['effective'][] = [
                'title'     => $title,
                'counts'    => $counts ?: 0,
            ];
            $rtn['pv'][] = [
                'title' => $title,
                'pv'    => $pv ?: 0,
            ];
        }
        return $rtn;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function platform_overview_returns()
    {
        return new external_single_structure(array(
            'platform' => new external_single_structure(array(
                'course_num'    => new external_value(PARAM_INT, 'Course counts'),
                'course_add'    => new external_value(PARAM_TEXT, 'The course increase in this month'),
                'teacher_num'   => new external_value(PARAM_INT, 'Teacher counts'),
                'teacher_add'   => new external_value(PARAM_TEXT, 'The teacher increase in this month'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'student_add'   => new external_value(PARAM_TEXT, 'The student increase in this month'),
                'effective_num' => new external_value(PARAM_INT, 'Effective course counts'),
                'effective_add' => new external_value(PARAM_TEXT, 'The effective course increase in this month'),
                'percourse_num' => new external_value(PARAM_FLOAT, 'Average course enrollment'),
                'percourse_add' => new external_value(PARAM_TEXT, 'The average course increase in this month'),
                'pv_num'            => new external_value(PARAM_INT, 'Monthly visits'),
                'pv_add'        => new external_value(PARAM_TEXT, 'The Monthly visits increase in this month'),
                'realclass_num'         => new external_value(PARAM_INT, 'Platform real-time online courses'),
                'realclass_add'         => new external_value(PARAM_INT, 'Platform real-time online courses increase in this month'),
                'realstudent_num'       => new external_value(PARAM_INT, 'Platform real-time learning number'),
                'realstudent_add'       => new external_value(PARAM_INT, 'Platform real-time learning number increase in this month'),
            ), 'Platform overview'),
            'effective'  => new external_multiple_structure(new external_single_structure(array(
                'title'     => new external_value(PARAM_TEXT, 'Semester'),
                'counts'    => new external_value(PARAM_INT, 'Course counts'),
            ))),
            'pv' => new external_multiple_structure(new external_single_structure(array(
                'title' => new external_value(PARAM_TEXT, 'Semester'),
                'pv'    => new external_value(PARAM_INT, 'Page view counts'),
            ))),
        ));
    }

    public static function online_courses_parameters()
    {
        return new external_function_parameters(array(
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT, 10),
        ));
    }

    public static function online_courses($page, $pagesize)
    {
        global $DB, $PAGE, $CFG;
        require_once($CFG->dirroot . '/blocks/data_screen/lib.php');
        $rtn = ['data'=>[]];

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::online_courses_parameters(), array(
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
        ));

        $counts = $DB->count_records_sql("SELECT COUNT(DISTINCT courseid) FROM `mdl_logstore_standard_log` WHERE timecreated>? AND crud='r' AND contextlevel=50 AND courseid<>1", [time()-(10*60)]);
        if ($params['pagesize']) {
            $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . ','. $params['pagesize'];
        }
        $courses = $DB->get_records_sql("SELECT DISTINCT courseid FROM `mdl_logstore_standard_log` WHERE timecreated>? AND crud='r' AND contextlevel=50 AND courseid<>1 $sql", [time()-(10*60)]);
        foreach ($courses as $courseid) {
            $course = get_course($courseid->courseid);
            $rtn['data'][] = [
                'id'            => $course->id,
                'fullname'      => $course->fullname,
                'img'           => get_course_image($course) ?: get_course_pattern($course),
            ];
        }

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    public static function online_courses_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'    => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'    => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'data'  => new external_multiple_structure(new external_single_structure(array(
                'id'            => new external_value(PARAM_INT, 'Course id'),
                'fullname'      => new external_value(PARAM_TEXT, 'Course full name'),
                'img'           => new external_value(PARAM_TEXT, 'Course image address'),
            ))),
        ));
    }

    public static function online_learning_parameters()
    {
        return new external_function_parameters(array(
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT, 10),
        ));
    }

    public static function online_learning($page, $pagesize)
    {
        global $DB, $PAGE;
        $rtn = ['data'=>[]];

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::online_learning_parameters(), array(
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
        ));

        $counts = $DB->count_records_sql("SELECT COUNT( DISTINCT userid) FROM `mdl_logstore_standard_log`WHERE timecreated>? AND courseid=0 AND anonymous=0 AND action='loggedin' AND userid<>1", [time()-(120*60)]);
        if ($params['pagesize']) {
            $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . ','. $params['pagesize'];
        }
        $users = $DB->get_records_sql("SELECT DISTINCT userid,timecreated FROM `mdl_logstore_standard_log`WHERE timecreated>? AND courseid=0 AND anonymous=0 AND action='loggedin' AND userid<>1 $sql", [time()-(120*60)]);
        foreach ($users as $user) {
            $user_info = $DB->get_record('user', ['id'=>$user->userid], 'id,firstname,email');
            $rtn['data'][] = [
                'id'        => $user_info->id,
                'firstname' => $user_info->firstname,
                'email'     => $user_info->email,
                'login'     => date('Y-m-d H:i:s', $user->timecreated),
            ];
        }

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    public static function online_learning_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'    => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'    => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'data'  => new external_multiple_structure(new external_single_structure(array(
                'id'        => new external_value(PARAM_INT, 'User id'),
                'firstname' => new external_value(PARAM_TEXT, 'User first name'),
                'email'     => new external_value(PARAM_TEXT, 'User email address'),
                'login'     => new external_value(PARAM_TEXT, 'User login time'),
            ))),
        ));
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function attribute_course_parameters()
    {
        return new external_function_parameters(array(
            'course_pro'    => new external_value(PARAM_INT, 'Course project tag id', VALUE_DEFAULT, 0),
            'course_att'    => new external_value(PARAM_INT, 'Course attribute tag id', VALUE_DEFAULT, 0),
            'start_time'    => new external_value(PARAM_TEXT, 'Start time', VALUE_DEFAULT, ''),
            'end_time'      => new external_value(PARAM_TEXT, 'End time', VALUE_DEFAULT, ''),
            'open_times'    => new external_value(PARAM_INT, 'Number of courses offered', VALUE_DEFAULT, -1),
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT, 10),
        ));
    }

    /**
     * Attribute course
     *
     * @param $course_pro
     * @param $course_att
     * @param $start_time
     * @param $end_time
     * @param $open_times
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function attribute_course($course_pro, $course_att, $start_time, $end_time, $open_times, $page, $pagesize)
    {
        global $DB, $PAGE;

        $rtn = ['data'=>[]];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::attribute_course_parameters(), array(
            'course_pro'    => $course_pro,
            'course_att'    => $course_att,
            'start_time'    => strtotime($start_time),
            'end_time'      => strtotime($end_time),
            'open_times'    => $open_times,
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
        ));

        // tag
        $tag        = $DB->get_records_sql('SELECT id,name FROM {tag}');
        $tag_name   = [];
        foreach ($tag as $value) {
            $tag_name[$value->id] = $value->name;
        }

        $sql = '';
        if ($params['start_time'] && $params['end_time']) {
            $sql =  " AND start_time>=" . $params['start_time'] . " AND end_time<=" .$params['end_time'];
        } else {
            if ($params['start_time']) {
                $sql = " AND start_time>=" . $params['start_time'];
            } elseif ($params['end_time']) {
                $sql .= " AND end_time<=" . $params['end_time'];
            }
        }
        // open number
        if ($params['open_times'] >= 0) {
            $sql .= " AND open_times=" . $params['open_times'];
        }
        // $sql = " WHERE full_name LIKE '%" . get_string('test', 'block_data_screen') . "%' OR short_name LIKE '%" . get_string('test', 'block_data_screen') . "%' OR teacher_counts=0 OR (resource_num+activity_num)<2";
        $sql = " WHERE teacher_counts>0 AND (resource_num+activity_num)>1". $sql;

        if ($params['course_att'] && $params['course_pro']) {
            $pro = $tag_name[$params['course_pro']] ?: 'invalid_tags';
            $att = $tag_name[$params['course_att']] ?: 'invalid_tags';
            $sql = $sql . " AND tags LIKE '%" . $pro . "%' AND tags LIKE '%" . $att . "%'";
        } elseif ($params['course_pro']) {
            $pro = $tag_name[$params['course_pro']] ?: 'invalid_tags';
            $sql = $sql . " AND tags LIKE '%" . $pro . "%'";
        } elseif ($params['course_att']) {
            $att = $tag_name[$params['course_att']] ?: 'invalid_tags';
            $sql = $sql . " AND tags LIKE '%" . $att . "%'";
        }
        $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM {block_data_screen_course}" . $sql)->counts;
        if ($params['pagesize']) {
            $sql = $sql . " LIMIT " . (($params['page']-1)*$params['pagesize']) . ','. $params['pagesize'];
        }

        $course_list = $DB->get_records_sql("SELECT * FROM {block_data_screen_course}" . $sql);

        foreach ($course_list as $value) {
            $teacher = $DB->get_record_sql('SELECT GROUP_CONCAT(firstname)teacher FROM {user} WHERE id IN ('. $value->teachers . ')')->teacher;
            $rtn['data'][] = [
                'course_id'     => $value->course_id,
                'fullname'      => $value->full_name,
                'shortname'     => $value->short_name,
                'start_time'    => date('Y-m-d', $value->start_time),
                'end_time'      => date('Y-m-d', $value->end_time),
                'teachers'      => $teacher,
                'open_times'    => $value->open_times,
                'tags'          => $value->tags ?: ''
            ];
        }

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function attribute_course_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'    => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'    => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'data'  => new external_multiple_structure(new external_single_structure(array(
                'course_id'     => new external_value(PARAM_INT, 'Course id'),
                'fullname'      => new external_value(PARAM_TEXT, 'Course fullname'),
                'shortname'     => new external_value(PARAM_TEXT, 'Course shortname'),
                'start_time'    => new external_value(PARAM_TEXT, 'Course start time'),
                'end_time'      => new external_value(PARAM_TEXT, 'Course end time'),
                'teachers'      => new external_value(PARAM_TEXT, 'Teachers name'),
                'open_times'    => new external_value(PARAM_INT, 'Open times'),
                'tags'          => new external_value(PARAM_TEXT, 'Course tags'),
            ))),
        ));
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_tags_parameters()
    {
        return new external_function_parameters([]);
    }

    /**
     * Get tags
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_tags()
    {
        global $DB, $PAGE;

        $rtn = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $project    = $DB->get_records_sql("SELECT tag.id,tag.name FROM {tag} tag JOIN {tag_coll} coll ON coll.id=tag.tagcollid WHERE coll.name=? AND tag.name IN (SELECT t.name FROM {tag} t JOIN {tag_coll} tc ON tc.id=t.tagcollid WHERE tc.name=?)", [get_string('tag', 'block_data_screen'), get_string('project', 'block_data_screen')]);
        $attribute  = $DB->get_records_sql("SELECT tag.id,tag.name FROM {tag} tag JOIN {tag_coll} coll ON coll.id=tag.tagcollid WHERE coll.name=? AND tag.name IN (SELECT t.name FROM {tag} t JOIN {tag_coll} tc ON tc.id=t.tagcollid WHERE tc.name=?)", [get_string('tag', 'block_data_screen'), get_string('attribute', 'block_data_screen')]);
        foreach ($project as $value) {
            $rtn['course_pro'][] = [
                'id'    => $value->id,
                'name'  => $value->name
            ];
        }
        $rtn['course_pro'] = isset($rtn['course_pro']) ? $rtn['course_pro'] : [];
        foreach ($attribute as $value) {
            $rtn['course_att'][] = [
                'id'    => $value->id,
                'name'  => $value->name
            ];
        }
        $rtn['course_att'] = isset($rtn['course_att']) ? $rtn['course_att'] : [];

        return $rtn;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function get_tags_returns()
    {
        return new external_single_structure(array(
            'course_pro'  => new external_multiple_structure(new external_single_structure(array(
                'id'    => new external_value(PARAM_INT, 'Tag id'),
                'name'  => new external_value(PARAM_TEXT, 'Tag name'),
            ))),
            'course_att'  => new external_multiple_structure(new external_single_structure(array(
                'id'    => new external_value(PARAM_INT, 'Tag id'),
                'name'  => new external_value(PARAM_TEXT, 'Tag name'),
            ))),
        ));
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function invalid_course_parameters()
    {
        return new external_function_parameters(array(
            'category_id'   => new external_value(PARAM_INT, 'Category id', VALUE_DEFAULT, 0),
            'start_time'    => new external_value(PARAM_TEXT, 'Course start time', VALUE_DEFAULT, ''),
            'end_time'      => new external_value(PARAM_TEXT, 'Course end time', VALUE_DEFAULT, ''),
            'judgment'      => new external_value(PARAM_INT, 'Judgment', VALUE_DEFAULT, 0),
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT, 10),
            'user'          => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
            'role'          => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0)
        ));
    }

    /**
     * Invalid course
     *
     * @param $category_id
     * @param $start_time
     * @param $end_time
     * @param $judgment
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function invalid_course($category_id, $start_time, $end_time, $judgment, $page, $pagesize, $user, $role)
    {
        global $DB, $PAGE;

        $rtn['course_list'] = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::invalid_course_parameters(), array(
            'category_id'   => $category_id,
            'start_time'    => strtotime($start_time),
            'end_time'      => strtotime($end_time),
            'judgment'      => $judgment,
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
            'user'          => $user,
            'role'          => $role
        ));

        $sql = '';
        if ($params['start_time'] && $params['end_time']) {
            $sql = " start_time>=" . $params['start_time'] . " AND end_time<=" . $params['end_time'];
        } else {
            if ($params['start_time']) {
                $sql = " start_time>=" . $params['start_time'];
            } elseif ($params['end_time']) {
                $sql .= $sql ? " AND end_time<=" . $params['end_time'] : " end_time<=" . $params['end_time'];
            }
        }
        if ($params['category_id']) {
            $sql .= $sql ? " AND category_id=" . $params['category_id'] : " category_id=" . $params['category_id'];
        }
        switch ($params['judgment']) {
            case 1:
                $sql2 = " WHERE teacher_counts=0";
                break;
            case 2:
                $sql2 = " WHERE (resource_num+activity_num)<2";
                break;
            default:
                $sql2 = " WHERE teacher_counts=0 OR (resource_num+activity_num)<2";
                break;
        }

        switch ($params['role']) {
            case 1:
                if ($sql) {
                    $sql = " WHERE" . $sql;
                }
                $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM (SELECT id,full_name,category_id,short_name,teacher_counts,activity_num,resource_num FROM {block_data_screen_course}" . $sql . ")s" . $sql2)->counts;
                if ($params['pagesize']) {
                    $sql2 = $sql2 . " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $courses = $DB->get_records_sql("SELECT * FROM (SELECT id,full_name,category_id,short_name,teacher_counts,activity_num,resource_num,course_id,category,start_time,end_time,teachers FROM {block_data_screen_course}" . $sql . ")s" . $sql2);
                break;
            case 5:
                $courses = [];
                $counts = 0;
                break;
            case 6:
                $courses = [];
                $counts = 0;
                break;
            default:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid<>5 AND userid=?', [$params['user']])->course_id ?: 0;
                $sql = $sql ? $sql . ' AND course_id IN (' . $course_id . ')' : ' course_id IN (' . $course_id . ')';
                if ($sql) {
                    $sql = " WHERE" . $sql;
                }
                $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM (SELECT id,full_name,category_id,short_name,teacher_counts,activity_num,resource_num FROM {block_data_screen_course}" . $sql . ")s" . $sql2)->counts;
                if ($params['pagesize']) {
                    $sql2 = $sql2 . " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $courses = $DB->get_records_sql("SELECT * FROM (SELECT id,full_name,category_id,short_name,teacher_counts,activity_num,resource_num,course_id,category,start_time,end_time,teachers FROM {block_data_screen_course}" . $sql . ")s" . $sql2);
                break;
        }

        $judgment_arr = [
            1 => get_string('no_teacher', 'block_data_screen'),
            2 => get_string('no_activity', 'block_data_screen'),
        ];
        foreach ($courses as $value) {
            $teacher = $value->teachers ? $DB->get_record_sql('SELECT GROUP_CONCAT(firstname)teacher FROM {user} WHERE id IN ('. $value->teachers . ')')->teacher : '';
            $tmp        = [];
            if ($value->teacher_counts==0) {
                $tmp[] = $judgment_arr[1];
            }
            if (($value->resource_num+$value->activity_num)<=1) {
                $tmp[] = $judgment_arr[2];
            }

            $rtn['course_list'][] = [
                'course_id'     => $value->course_id,
                'fullname'      => $value->full_name,
                'shortname'     => $value->short_name,
                'start_time'    => date('Y-m-d', $value->start_time),
                'end_time'      => date('Y-m-d', $value->end_time),
                'category'      => $value->category,
                'teachers'      => $teacher,
                'judgment'      => implode(',', $tmp),
            ];
        }

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];
        return $rtn;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function invalid_course_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'    => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'    => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'course_list'  => new external_multiple_structure(new external_single_structure(array(
                'course_id'     => new external_value(PARAM_INT, 'Course id'),
                'fullname'      => new external_value(PARAM_TEXT, 'Course fullname'),
                'shortname'     => new external_value(PARAM_TEXT, 'Course shortname'),
                'start_time'    => new external_value(PARAM_TEXT, 'Course start time'),
                'end_time'      => new external_value(PARAM_TEXT, 'Course end time'),
                'teachers'      => new external_value(PARAM_TEXT, 'Teachers name'),
                'category'      => new external_value(PARAM_TEXT, 'Course category'),
                'judgment'      => new external_value(PARAM_TEXT, 'Judgment'),
            ))),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_category_parameters()
    {
        return new external_function_parameters([]);
    }

    /**
     * Get course categories list
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_category()
    {
        global $DB, $PAGE;

        $rtn = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $categories = $DB->get_records_sql("SELECT id,name FROM {course_categories}");
        foreach ($categories as $value) {
            $rtn[] = [
                'id'    => $value->id,
                'name'  => $value->name
            ];
        }
        return $rtn;
    }

    /**
     * Return description of method result value
     */
    public static function get_category_returns()
    {
        return new external_multiple_structure(new external_single_structure(array(
            'id'    => new external_value(PARAM_INT, 'Category id'),
            'name'  => new external_value(PARAM_TEXT, 'Category name'),
        )));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function access_table_parameters()
    {
        return new external_function_parameters(array(
            'start_time'    => new external_value(PARAM_TEXT, 'Course start time', VALUE_DEFAULT, ''),
            'end_time'      => new external_value(PARAM_TEXT, 'Course end time', VALUE_DEFAULT, '')
        ));
    }

    /**
     * Access the analysis table data
     *
     * @param $start_time
     * @param $end_time
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function access_table($start_time, $end_time)
    {
        global $DB, $PAGE;

        $rtn = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::access_table_parameters(), array('start_time'=>strtotime($start_time),'end_time'=>strtotime($end_time)));

        $today      = $DB->get_record_sql("SELECT SUM(pv) pv,SUM(uv) uv,SUM(ip) ip,SUM(access_num) access_num FROM {block_data_screen_access} WHERE FROM_UNIXTIME(date, '%Y-%m-%d')=DATE_SUB(curdate(),INTERVAL 0 DAY)");
        $yesterday  = $DB->get_record_sql("SELECT SUM(pv) pv,SUM(uv) uv,SUM(ip) ip,SUM(access_num) access_num FROM {block_data_screen_access} WHERE FROM_UNIXTIME(date, '%Y-%m-%d')=DATE_SUB(curdate(),INTERVAL 1 DAY)");
        $top        = $DB->get_records_sql("SELECT * FROM {block_data_screen_access_top}");

        $sql = '';
        if ($params['start_time'] && $params['end_time']) {
            $sql = " date>=" . $params['start_time'] . " AND date<=" . $params['end_time'];
        } else {
            if ($params['start_time']) {
                $sql = " date>=" . $params['start_time'];
            } elseif ($params['end_time']) {
                $sql = " date<=" . $params['end_time'];
            }
        }
        if ($sql) {
            $sql  = " WHERE" . $sql ;
        }
        $total          = $DB->get_record_sql("SELECT SUM(pv)pv,SUM(uv)uv,SUM(ip)ip,SUM(access_num)access_num FROM {block_data_screen_access}" . $sql);

        $rtn['today']       = ['pv'=>$today->pv ?: 0, 'uv'=>$today->uv ?: 0, 'ip'=>$today->ip ?: 0, 'access_num'=>$today->access_num ?: 0];
        $rtn['yesterday']   = ['pv'=>$yesterday->pv ?: 0, 'uv'=>$yesterday->uv ?: 0, 'ip'=>$yesterday->ip ?: 0, 'access_num'=>$yesterday->access_num ?: 0];
        $rtn['top']         = ['pv'=>0, 'uv'=>0, 'ip'=>0, 'access_num'=>0];
        foreach ($top as $value) {
            $rtn['top'][$value->type] = $value->counts ?: 0;
        }
        $rtn['total']       = ['pv'=>$total->pv ?: 0, 'uv'=>$total->uv ?: 0, 'ip'=>$total->ip ?: 0, 'access_num'=>$total->access_num ?: 0];

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function access_table_returns()
    {
        return new external_single_structure(array(
            'today' => new external_single_structure(array(
                'pv'            => new external_value(PARAM_INT, 'Page view'),
                'uv'            => new external_value(PARAM_INT, 'Unique Visitor'),
                'ip'            => new external_value(PARAM_INT, 'Internet Protocol'),
                'access_num'    => new external_value(PARAM_INT, 'Access number'),
            ), 'Today access statistics'),
            'yesterday' => new external_single_structure(array(
                'pv'            => new external_value(PARAM_INT, 'Page view'),
                'uv'            => new external_value(PARAM_INT, 'Unique Visitor'),
                'ip'            => new external_value(PARAM_INT, 'Internet Protocol'),
                'access_num'    => new external_value(PARAM_INT, 'Access number'),
            ), 'Yesterday access statistics'),
            'top' => new external_single_structure(array(
                'pv'            => new external_value(PARAM_INT, 'Page view'),
                'uv'            => new external_value(PARAM_INT, 'Unique Visitor'),
                'ip'            => new external_value(PARAM_INT, 'Internet Protocol'),
                'access_num'    => new external_value(PARAM_INT, 'Access number'),
            ), 'Record high'),
            'total' => new external_single_structure(array(
                'pv'            => new external_value(PARAM_INT, 'Page view'),
                'uv'            => new external_value(PARAM_INT, 'Unique Visitor'),
                'ip'            => new external_value(PARAM_INT, 'Internet Protocol'),
                'access_num'    => new external_value(PARAM_INT, 'Access number'),
            ), 'History of the cumulative'),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function access_chart_parameters()
    {
        return new external_function_parameters(array(
            'date' => new external_value(PARAM_TEXT, 'Target date', VALUE_DEFAULT, '')
        ));
    }

    /**
     * Data of the line chart
     *
     * @param $date
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function access_chart($date)
    {
        global $DB, $PAGE;

        $rtn = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::access_chart_parameters(), array('date'=>$date));

        $sql = $params['date'] ? "'" . $params['date'] . "'" : "DATE_SUB(curdate(),INTERVAL 0 DAY)";

        $datas = $DB->get_records_sql("SELECT pv,uv,ip,access_num,FROM_UNIXTIME(date,'%H')hour FROM {block_data_screen_access} WHERE FROM_UNIXTIME(date, '%Y-%m-%d')=" . $sql . " ORDER BY date");
        for ($i=0;$i<=24;$i++) {
            foreach ($datas as $value) {
                if ($i==$value->hour) {
                    $rtn[$i] = [
                        'pv'            => $value->pv,
                        'uv'            => $value->uv,
                        'ip'            => $value->ip,
                        'access_num'    => $value->access_num,
                        'hour'          => (int)$value->hour
                    ];
                }
            }
            if (!isset($rtn[$i])) {
                $rtn[$i] = ['pv'=>0,'uv'=>0,'ip'=>0,'access_num'=>0,'hour'=>$i];
            }
        }

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return
     */
    public static function access_chart_returns()
    {
        return new external_multiple_structure(new external_single_structure(array(
            'pv'            => new external_value(PARAM_INT, 'Page view'),
            'uv'            => new external_value(PARAM_INT, 'Unique Visitor'),
            'ip'            => new external_value(PARAM_INT, 'Internet Protocol'),
            'access_num'    => new external_value(PARAM_INT, 'Access number'),
            'hour'          => new external_value(PARAM_INT, 'Hour'),
        )));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function college_list_parameters()
    {
        return new external_function_parameters(array(
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 15),
            'start_time'    => new external_value(PARAM_TEXT, 'Start time', VALUE_DEFAULT, ''),
            'end_time'      => new external_value(PARAM_TEXT, 'End time', VALUE_DEFAULT, ''),
            'name'          => new external_value(PARAM_TEXT, 'College name', VALUE_DEFAULT, ''),
            'role'          => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0),
            'user'          => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0)
        ));
    }

    /**
     * Get college list
     *
     * @param $page
     * @param $pagesize
     * @param $start_time
     * @param $end_time
     * @param $name
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function college_list($page, $pagesize, $start_time, $end_time, $name, $role, $user)
    {
        global $DB, $PAGE, $CFG;

        $rtn['college_list'] = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::college_list_parameters(), array(
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
            'start_time'    => strtotime($start_time),
            'end_time'      => strtotime($end_time),
            'name'          => $name,
            'role'          => $role,
            'user'          => $user
        ));

        $sql = '';
        if ($params['start_time'] && $params['end_time']) {
            $sql = " AND start_time>=" . $params['start_time'] . " AND end_time<=" . $params['end_time'];
        } else {
            if ($params['start_time']) {
                $sql = " AND start_time>=" . $params['start_time'];
            } elseif ($params['end_time']) {
                $sql = " AND end_time<=" . $params['end_time'];
            }
        }
        $where = '';
        if ($params['name']) {
            $collegeid  = $DB->get_record_sql("SELECT id FROM {block_data_screen_college} WHERE name=?", [$params['name']])->id;
            $where      = " AND path='/" . $collegeid . "'";
        }

        switch ($params['role']) {
            case 1:
                $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM  {block_data_screen_college} WHERE type=1" . $where)->counts;
                if ($params['pagesize']) {
                    $where = $where . " LIMIT ".(($params['page']-1)*$params['pagesize']) . "," .$params['pagesize'];
                }
                $colleges = $DB->get_recordset_sql("SELECT idnumber,name,course_num,student_num,teacher_num,college_id FROM {block_data_screen_college} WHERE type=1" . $where);
                break;
            case 6:
                $counts = 0;
                $colleges = [];
                break;
            case 5:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid=5 AND userid=?', [$params['user']])->course_id ?: 0;
                $categories = $DB->get_record_sql('SELECT GROUP_CONCAT(category) categories FROM {course} WHERE id IN ('. $course_id . ')')->categories ?: 0;
                $where = $where . ' AND college_id IN (' . $categories . ')';
                $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM  {block_data_screen_college} WHERE type=1" . $where)->counts;
                if ($params['pagesize']) {
                    $where = $where . " LIMIT ".(($params['page']-1)*$params['pagesize']) . "," .$params['pagesize'];
                }
                $colleges = $DB->get_recordset_sql("SELECT idnumber,name,course_num,student_num,teacher_num,college_id FROM {block_data_screen_college} WHERE type=1" . $where);
                break;
            case $CFG->block_data_screen_edu_role:
                $instances = $DB->get_records_sql("SELECT ct.instanceid FROM `mdl_role_assignments` ra JOIN `mdl_context` ct ON ct.id=ra.contextid WHERE ct.contextlevel=40 AND ra.userid=? AND ra.roleid=?", [$params['user'], $CFG->block_data_screen_edu_role]);
                $counts = 0;
                $colleges = [];
                foreach ($instances as $instance) {
                    $category = $DB->get_record('course_categories', ['id'=>$instance->instanceid]);
                    if ($category) {
                        $counts += $DB->get_record_sql("SELECT COUNT(id) counts FROM  {block_data_screen_college} WHERE type=1 AND (college_id=$category->id OR path LIKE '$category->path/%')" . $where)->counts;
                        //var_dump("SELECT idnumber,name,course_num,student_num,teacher_num,college_id FROM {block_data_screen_college} WHERE type=1 AND (college_id=$category->id OR path LIKE '$category->path/%')" . $where);die;
                        $colleges_arr = $DB->get_records_sql("SELECT college_id,idnumber,name,course_num,student_num,teacher_num FROM {block_data_screen_college} WHERE type=1 AND (college_id=$category->id OR path LIKE '$category->path/%')");
                        $colleges = array_merge($colleges, $colleges_arr);
                    }
                }
                if ($params['pagesize']) {
                    $params['pagesize'] = $counts;
                    $params['page'] = 1;
                }
                break;
            default:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid<>5 AND userid=?', [$params['user']])->course_id ?: 0;
                $categories = $DB->get_record_sql('SELECT GROUP_CONCAT(category) categories FROM {course} WHERE id IN ('. $course_id . ')')->categories ?: 0;
                $where = $where . ' AND college_id IN (' . $categories . ')';
                $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM  {block_data_screen_college} WHERE type=1" . $where)->counts;
                if ($params['pagesize']) {
                    $where = $where . " LIMIT ".(($params['page']-1)*$params['pagesize']) . "," .$params['pagesize'];
                }
                $colleges = $DB->get_recordset_sql("SELECT idnumber,name,course_num,student_num,teacher_num,college_id FROM {block_data_screen_college} WHERE type=1" . $where);
                break;
        }
        foreach ($colleges as $value) {
            $coursesid  = $DB->get_record_sql("SELECT GROUP_CONCAT(course_id) coursesid FROM {block_data_screen_course} WHERE path LIKE '%/" . $value->college_id . "'" . $sql)->coursesid ?: 0;
            $college    = $DB->get_record_sql("SELECT GROUP_CONCAT(teachers)teachers, COUNT(id) courses FROM {block_data_screen_course} WHERE path LIKE '%/" . $value->college_id . "'" . $sql);
            $students   = $DB->get_record_sql("SELECT COUNT(DISTINCT r.userid) counts FROM {role_assignments} r JOIN {context} con ON con.id=r.contextid WHERE con.instanceid IN (" . $coursesid . ") AND r.roleid=5")->counts;
            $rtn['college_list'][] = [
                'id'            => $value->college_id,
                'idnumber'      => $value->idnumber,
                'name'          => $value->name,
                'course_num'    => $college->courses,
                'student_num'   => $students,
                'teacher_num'   => $college->teachers ? count(array_unique(explode(',', $college->teachers))) : 0
            ];
        }

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];
        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function college_list_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'    => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'    => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'college_list'  => new external_multiple_structure(new external_single_structure(array(
                'id'            => new external_value(PARAM_INT, 'College id'),
                'idnumber'      => new external_value(PARAM_TEXT, 'College idnumber'),
                'name'          => new external_value(PARAM_TEXT, 'college name'),
                'course_num'    => new external_value(PARAM_INT, 'Course counts'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'teacher_num'   => new external_value(PARAM_TEXT, 'Teachers counts'),
            ))),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function college_detail_parameters()
    {
        return new external_function_parameters(array(
            'catid'         => new external_value(PARAM_INT, 'Category id'),
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 12),
            'start_time'    => new external_value(PARAM_TEXT, 'Start time', VALUE_DEFAULT, ''),
            'end_time'      => new external_value(PARAM_TEXT, 'End time', VALUE_DEFAULT, ''),
            'role'          => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0),
            'user'          => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0)
        ));
    }

    /**
     * College detail
     *
     * @param $catid
     * @param $page
     * @param $pagesize
     * @param $start_time
     * @param $end_time
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function college_detail($catid, $page, $pagesize, $start_time, $end_time, $role, $user)
    {
        global $DB, $PAGE;

        $rtn['course_list'] = [];

        $context = \context_coursecat::instance($catid);
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::college_detail_parameters(), array(
            'catid'         => $catid,
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
            'start_time'    => strtotime($start_time),
            'end_time'      => strtotime($end_time),
            'role'          => $role,
            'user'          => $user
        ));

        $sql = '';
        if ($params['start_time'] && $params['end_time']) {
            $sql = " AND c.start_time>=" . $params['start_time'] . " AND c.end_time<=" . $params['end_time'];
        } else {
            if ($params['start_time']) {
                $sql = " AND c.start_time>=" . $params['start_time'];
            } elseif ($params['end_time']) {
                $sql = " AND c.end_time<=" . $params['end_time'];
            }
        }


        switch ($params['role']) {
            case 1:
                $counts = $DB->get_record_sql("SELECT COUNT(id)counts FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                if ($params['pagesize']) {
                    $sql .= " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $courses    = $DB->get_records_sql("SELECT course_id,teachers,course_id,full_name,students,img FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                break;
            case 6:
                $counts = 0;
                $courses = [];
                break;
            case 5:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid=5 AND userid=?', [$params['user']])->course_id ?: 0;
                $sql = $sql . ' AND course_id IN (' . $course_id . ')';
                $counts = $DB->get_record_sql("SELECT COUNT(id)counts FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                if ($params['pagesize']) {
                    $sql .= " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $courses    = $DB->get_records_sql("SELECT course_id,teachers,course_id,full_name,students,img FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                break;
            case $CFG->block_data_screen_edu_role:
                $counts = $DB->get_record_sql("SELECT COUNT(id)counts FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                if ($params['pagesize']) {
                    $sql .= " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $courses    = $DB->get_records_sql("SELECT course_id,teachers,course_id,full_name,students,img FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                break;
            default:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid<>5 AND userid=?', [$params['user']])->course_id ?: 0;
                $sql = $sql . ' AND course_id IN (' . $course_id . ')';
                $counts = $DB->get_record_sql("SELECT COUNT(id)counts FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                if ($params['pagesize']) {
                    $sql .= " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $courses    = $DB->get_records_sql("SELECT course_id,teachers,course_id,full_name,students,img FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'] . $sql);
                break;
        }

        $teachers   = '';
        foreach ($courses as $value) {
            $teacher_ids    = $value->teachers ?: 0;
            $teacher        = $DB->get_record_sql("SELECT GROUP_CONCAT(firstname)name FROM {user} WHERE id IN (" . $teacher_ids . ")")->name;
            $rtn['course_list'][] = [
                'course_id'     => $value->course_id,
                'fullname'      => $value->full_name,
                'student_num'   => $value->students,
                'teachers'      => $teacher ?: '',
                'img'           => $value->img,
            ];
            if ($value->teachers) {
                $teachers .= $teachers ? "," . $value->teachers : $value->teachers;
            }
        }

        $course_num     = $DB->get_record_sql("SELECT COUNT(id)counts FROM {block_data_screen_course} c WHERE category_id=" . $params['catid'])->counts;
        $students       = $DB->get_record_sql('SELECT student_num students FROM {block_data_screen_college} WHERE college_id=?', [$params['catid']])->students;
        $college_data   = $DB->get_record_sql('SELECT resource_num,activity_num FROM {block_data_screen_college} WHERE college_id=?', [$params['catid']]);
        $avg            = $DB->get_record_sql("SELECT AVG(course_num)courses, AVG(teacher_num)teachers, AVG(student_num)students, AVG(resource_num)resources, AVG(activity_num)activities FROM {block_data_screen_college} WHERE type=1");
        $access         = $DB->get_record_sql("SELECT SUM(access_num)access FROM {block_data_screen_visit} WHERE type=0 AND course_id IN (SELECT course_id FROM {block_data_screen_course} WHERE category_id=" . $params['catid'] .")")->access;
        $all_access     = $DB->get_record_sql('SELECT SUM(access_num)access FROM {block_data_screen_visit} WHERE type=0')->access;
        $total          = $DB->get_record_sql('SELECT COUNT(id)counts FROM {block_data_screen_college} WHERE type=1')->counts;
        $avg_access     = $all_access==0 || $total==0 ? 0 : round($all_access/$total, 2);
        $teacher        = count(array_unique(explode(',', $teachers)));
        $rtn['college'] = [
            'course_num'    => $course_num ?: 0,
            'course_avg'    => $avg->courses>=$course_num ? round(($avg->courses-$counts) / $avg->courses, 2) * -100 . "%" : round(($course_num-$avg->courses) / $avg->courses, 2) * 100 . "%",
            'teacher_num'   => $teacher ?: 0,
            'teacher_avg'   => $avg->teachers>=$teachers ? round(($avg->teachers-$teachers) / $avg->teachers, 2) * -100 . "%" : round(($teachers-$avg->teachers) / $avg->teachers, 2) * 100 . "%",
            'student_num'   => $students ?: 0,
            'student_avg'   => $avg->students>=$students ? round(($avg->students-$students) / $avg->students, 2) * -100 . "%" : round(($students-$avg->students) / $avg->students, 2) * 100 . "%",
            'resource_num'  => $college_data->resource_num ?: 0,
            'resource_avg'  => $avg->resources>=$college_data->resource_num ? round(($avg->resources-$college_data->resource_num) / $avg->resources, 2) * -100 . "%" : round(($college_data->resource_num-$avg->resources) / $avg->resources, 2) * 100 . "%",
            'activity_num'  => $college_data->activity_num ?: 0,
            'activity_avg'  => $avg->activities>=$college_data->activity_num ? round(($avg->activities-$college_data->activity_num) / $avg->activities, 2) * -100 . "%" : round(($college_data->activity_num-$avg->activities) / $avg->activities, 2) * 100 . "%",
            'access_num'    => $access ?: 0,
            'access_avg'    => $avg_access>=$access ? round(($avg_access-$access) / $avg_access, 2) * -100 . "%" : round(($access-$avg_access) / $avg_access, 2) * 100 . "%",
        ];
        $college = $DB->get_record_select('block_data_screen_college', ' college_id=?', [$params['catid']], 'name');
        if (!$college) {
            throw new moodle_exception('empty_college');
            exit(0);
        }
        $rtn['college']['name'] = $college->name;

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts->counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    /**
     * Return description o method result value
     *
     * @return external_single_structure
     */
    public static function college_detail_returns()
    {
        return new external_single_structure(array(
            'course_list'  => new external_multiple_structure(new external_single_structure(array(
                'course_id'     => new external_value(PARAM_INT, 'Course ID'),
                'fullname'      => new external_value(PARAM_TEXT, 'Course full name'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'teachers'      => new external_value(PARAM_TEXT, 'Teachers'),
                'img'           => new external_value(PARAM_TEXT, 'Course image url'),
            ))),
            'college' => new external_single_structure(array(
                'course_num'    => new external_value(PARAM_INT, 'Course counts'),
                'course_avg'    => new external_value(PARAM_TEXT, 'More than the average'),
                'teacher_num'   => new external_value(PARAM_INT, 'Teacher counts'),
                'teacher_avg'   => new external_value(PARAM_TEXT, 'More than the average'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'student_avg'   => new external_value(PARAM_TEXT, 'More than the average'),
                'resource_num'  => new external_value(PARAM_INT, 'Resource counts'),
                'resource_avg'  => new external_value(PARAM_TEXT, 'More than the average'),
                'activity_num'  => new external_value(PARAM_INT, 'Activity counts'),
                'activity_avg'  => new external_value(PARAM_TEXT, 'More than the average'),
                'access_num'    => new external_value(PARAM_INT, 'Access counts'),
                'access_avg'    => new external_value(PARAM_TEXT, 'More than the average'),
                'name'          => new external_value(PARAM_TEXT, 'College name'),
            ), 'College information'),
            'page' => new external_single_structure(array(
                'max_page'  => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'  => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function teacher_list_parameters()
    {
        return new external_function_parameters(array(
            'dept'      => new external_value(PARAM_TEXT, 'Department name', VALUE_DEFAULT, ''),
            'name'      => new external_value(PARAM_TEXT, 'Teacher name', VALUE_DEFAULT, ''),
            'page'      => new external_value(PARAM_INT, 'Page numebr', VALUE_DEFAULT, 1),
            'pagesize'  => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 10),
            'role'      => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0),
            'user'      => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0)
        ));
    }

    /**
     * Get teacher list
     *
     * @param $dept
     * @param $name
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function teacher_list($dept, $name, $page, $pagesize, $role, $user)
    {
        global $DB, $PAGE;

        $rtn['teacher_list'] = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::teacher_list_parameters(), array(
            'dept'      => $dept,
            'name'      => $name,
            'page'      => $page<=0 ? 1 : $page,
            'pagesize'  => $pagesize<0 ? 1 : $pagesize,
            'role'      => $role,
            'user'      => $user
        ));

        $sql = '';
        if ($params['name']) {
            $sql = " AND name='" . $params['name'] . "'";
        }
        if ($params['dept']) {
            $sql .= " AND dept='" . $params['dept'] . "'";
        }

        switch ($params['role']) {
            case 1:
                $counts = $DB->get_record_sql("SELECT COUNT(id) counts FROM {block_data_screen_college} WHERE type=0" . $sql)->counts;
                if ($params['pagesize']) {
                    $sql = $sql . " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $teachers = $DB->get_records_sql("SELECT teacher_id,name,course_num,student_num,resource_num,activity_num,url,dept FROM {block_data_screen_college} WHERE type=0" . $sql);
                break;
            case 6:
                $counts = 0;
                $teachers = [];
                break;
            case 5:
                $counts = 0;
                $teachers = [];
                break;
            default:
                $teachers = $DB->get_records_sql("SELECT teacher_id,name,course_num,student_num,resource_num,activity_num,url,dept FROM {block_data_screen_college} WHERE type=0 AND teacher_id=?" . $sql, [$params['user']]);
                $counts = $teachers ? 1 : 0;
                break;
        }

        foreach ($teachers as $value) {
            $rtn['teacher_list'][] = [
                'teacher_id'    => $value->teacher_id,
                'name'          => $value->name,
                'url'           => $value->url,
                'dept'          => $value->dept,
                'course_num'    => $value->course_num,
                'student_num'   => $value->student_num,
                'res_avt_num'   => $value->resource_num + $value->activity_num
            ];
        }

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function teacher_list_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'  => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'  => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'teacher_list'  => new external_multiple_structure(new external_single_structure(array(
                'teacher_id'    => new external_value(PARAM_INT, 'Teacher ID'),
                'name'          => new external_value(PARAM_TEXT, 'Teacher full name'),
                'url'           => new external_value(PARAM_TEXT, 'Teacher picture url'),
                'dept'          => new external_value(PARAM_TEXT, 'Department name'),
                'course_num'    => new external_value(PARAM_INT, 'Course counts'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'res_avt_num'   => new external_value(PARAM_INT, 'Total of resource and activity'),
            ))),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_dept_parameters()
    {
        return new external_function_parameters([]);
    }

    /**
     * Get department list
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_dept()
    {
        global $DB, $PAGE;

        $rtn = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $depts = $DB->get_records_sql("SELECT DISTINCT department FROM {user}");
        foreach ($depts as $value) {
            if (!$value->department) {
                continue;
            }
            $rtn[] = $value->department;
        }
        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_multiple_structure
     */
    public static function get_dept_returns()
    {
        return new external_multiple_structure(
            new external_value(PARAM_TEXT, 'Department name')
        );
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function teacher_detail_parameters()
    {
        return new external_function_parameters(array(
            'start_time'    => new external_value(PARAM_TEXT, 'Course start time', VALUE_DEFAULT, 0),
            'end_time'      => new external_value(PARAM_TEXT, 'Course end time', VALUE_DEFAULT, 0),
            'id'            => new external_value(PARAM_INT, 'Teacher id'),
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 12),
        ));
    }

    /**
     * Teacher curriculum information
     *
     * @param $start_time
     * @param $end_time
     * @param $id
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function teacher_detail($start_time, $end_time, $id, $page, $pagesize)
    {
        global $DB, $PAGE;

        $rtn = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::teacher_detail_parameters(), array(
            'start_time'    => strtotime($start_time),
            'end_time'      => strtotime($end_time),
            'id'            => $id,
            'page'          => $page<=0 ? 1 : $page,
            'pagesize'      => $pagesize<0 ? 1 : $pagesize,
        ));

        $sql = '';
        if ($params['start_time'] && $params['end_time']) {
            $sql = " AND end_time<=" . $params['end_time'];
        } else {
            if ($params['start_time']) {
                $sql = " AND start_time>=" . $params['start_time'];
            } elseif ($params['end_time']) {
                $sql = " AND end_time<=" . $params['end_time'];
            }
        }

        $teachers = $DB->get_records_sql('SELECT course_id,teachers FROM {block_data_screen_course}');
        $course_id = [0];
        foreach ($teachers as $value) {
            if (in_array($params['id'], explode(',', $value->teachers))) {
                $course_id[] = $value->course_id;
            }
        }

        $course_ids = implode(',', $course_id);
        $counts     = $DB->get_record_sql("SELECT COUNT(id) counts FROM {block_data_screen_course} WHERE course_id IN (" . $course_ids . ")" . $sql)->counts;
        if ($params['pagesize']) {
            $sql = $sql . " LIMIT " . (($params['page']-1) * $params['pagesize']) . "," . $params['pagesize'];
        }
        $courses    = $DB->get_records_sql("SELECT course_id,full_name,img,students FROM {block_data_screen_course} WHERE course_id IN (" . $course_ids . ")" . $sql);
        $teacher   = $DB->get_record_sql("SELECT student_num,name FROM {block_data_screen_college} WHERE teacher_id=?", [$params['id']]);
        if (!$teacher) {
            throw new moodle_exception('empty_teacher');
        }

        $rtn['course_list'] = [];
        foreach ($courses as $value) {
            $rtn['course_list'][] = [
                'course_id'     => $value->course_id,
                'fullname'      => $value->full_name,
                'student_num'   => $value->students,
                'img'           => $value->img,
            ];
        }

        $course_data = $DB->get_record_sql('SELECT MAX(open_times)open_times,SUM(resource_num)+SUM(activity_num) resources FROM {block_data_screen_course} WHERE course_id IN (' . $course_ids . ')');
        $spendtime = $DB->get_record_sql('SELECT SUM(spend_time)spendtime FROM {block_data_screen_visit} WHERE type=0 AND course_id IN (' . $course_ids . ')')->spendtime;
        $course_count = $DB->get_record_sql("SELECT COUNT(id) counts FROM {block_data_screen_course} WHERE course_id IN (" . $course_ids . ")")->counts;
        $rtn['teacher'] = [
            'name'          => $teacher->name,
            'course_num'    => count($course_id),
            'student_num'   => (int)$teacher->student_num,
            'open_times'    => $course_data->open_times,
            'spendtime_avg' => round(($spendtime/$course_count)/(60*60)),
            'recourse_avg'  => round($course_data->resources/$course_count),
        ];

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function teacher_detail_returns()
    {
        return new external_single_structure(array(
            'course_list'  => new external_multiple_structure(new external_single_structure(array(
                'course_id'     => new external_value(PARAM_INT, 'Course ID'),
                'fullname'      => new external_value(PARAM_TEXT, 'Course full name'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'img'           => new external_value(PARAM_TEXT, 'Course image url'),
            ))),
            'teacher' => new external_single_structure(array(
                'name'          => new external_value(PARAM_TEXT, 'Teacher name'),
                'course_num'    => new external_value(PARAM_INT, 'Course counts'),
                'student_num'   => new external_value(PARAM_INT, 'Student counts'),
                'open_times'    => new external_value(PARAM_INT, 'Max open times'),
                'spendtime_avg' => new external_value(PARAM_FLOAT, 'Average spend time'),
                'recourse_avg'  => new external_value(PARAM_FLOAT, 'Average number of active resources'),
            ), 'Teacher information'),
            'page' => new external_single_structure(array(
                'max_page'  => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'  => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_semester_parameters()
    {
        return new external_function_parameters(array(
            'user' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
            'role'   => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0)
        ));
    }

    /**
     * Get semester list
     *
     * @param $user
     * @param $role
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_semester($user, $role)
    {
        global $DB, $PAGE, $CFG;
        $rtn = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::get_semester_parameters(), ['user'=>$user, 'role'=>$role]);

        $semester = $DB->get_records_sql("SELECT * FROM {block_data_screen_semester} ORDER BY start_time");
        $semester_arr = [0=>get_string('up', 'block_data_screen'), 1=>get_string('down', 'block_data_screen')];

        switch ($params['role']) {
            case 1:
                $courses = $DB->get_records_sql("SELECT course_id id,full_name,start_time,end_time FROM {block_data_screen_course}");
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($courses as $val) {
                        if ($val->start_time>$value->start_time && $val->end_time == 0 && $val->start_time<$value->end_time) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        } elseif ($val->start_time>$value->start_time && $val->end_time<$value->end_time && $val->end_time != 0) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        }
                    }
                }
                break;
            case $CFG->block_data_screen_edu_role:
                $instances = $DB->get_records_sql("SELECT ct.instanceid FROM `mdl_role_assignments` ra JOIN `mdl_context` ct ON ct.id=ra.contextid WHERE ct.contextlevel=40 AND ra.userid=? AND ra.roleid=?", [$params['user'], $CFG->block_data_screen_edu_role]);
                $courses = [];
                foreach ($instances as $instance) {
                    $category = $DB->get_record('course_categories', ['id'=>$instance->instanceid]);
                    if ($category) {
                        $course_arr = $DB->get_records_sql("SELECT course_id id,full_name,start_time,end_time FROM {block_data_screen_course} WHERE category_id=$category->id OR path LIKE '$category->path/%'");
                        $courses = array_merge($courses, $course_arr);
                    }
                }
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($courses as $val) {
                        if ($val->start_time>$value->start_time && $val->end_time == 0 && $val->start_time<$value->end_time) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        } elseif ($val->start_time>$value->start_time && $val->end_time<$value->end_time && $val->end_time != 0) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        }
                    }
                }
                break;
            case 5:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid=5 AND userid=?', [$params['user']])->course_id ?: 0;
                $courses = $DB->get_records_sql('SELECT course_id id,full_name,start_time,end_time FROM {block_data_screen_course} WHERE course_id IN (' . $course_id . ')');
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($courses as $val) {
                        if ($val->start_time>$value->start_time && $val->end_time == 0 && $val->start_time<$value->end_time) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        } elseif ($val->start_time>$value->start_time && $val->end_time<$value->end_time && $val->end_time != 0) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        }
                    }
                }
                break;
            case 6:
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                }
                break;
            default:
                $course_id = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE roleid<>5 AND userid=?', [$params['user']])->course_id ?: 0;
                $courses = $DB->get_records_sql('SELECT course_id id,full_name,start_time,end_time FROM {block_data_screen_course} WHERE course_id IN (' . $course_id . ')');
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($courses as $val) {
                        if ($val->start_time>$value->start_time && $val->end_time == 0 && $val->start_time<$value->end_time) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        } elseif ($val->start_time>$value->start_time && $val->end_time<$value->end_time && $val->end_time != 0) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $val->id,
                                'fullname'  => $val->full_name,
                            ];
                        }
                    }
                }
                break;
        }

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_multiple_structure
     */
    public static function get_semester_returns()
    {
        return new external_multiple_structure(new external_single_structure(array(
            'semester'    => new external_value(PARAM_TEXT, 'Semester'),
            'course_list' => new external_multiple_structure(new external_single_structure(array(
                'id'        => new external_value(PARAM_INT, 'Course ID'),
                'fullname'  => new external_value(PARAM_TEXT, 'Course full name'),
            ))),
        )));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function course_detail_parameters()
    {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Course ID'),
        ));
    }

    /**
     * Get course statistics data
     *
     * @param $id
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function course_detail($id)
    {
        global $DB, $PAGE;

        $params = self::validate_parameters(self::course_detail_parameters(), array('id'=>$id));

        $rtn = [];

        $context = \context_course::instance($id);
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $course_sql  = 'SELECT ';
        $course_sql .= 'full_name,summary,img,resource_num,forums,assigns,quiz,students,teachers,students,activity_num ';
        $course_sql .= 'FROM {block_data_screen_course} ';
        $course_sql .= 'WHERE course_id=?';
        $course = $DB->get_record_sql($course_sql, [$params['id']]);
        if (!$course) {
            throw new moodle_exception('empty_course', 'block_data_screen');
        }
        $rtn['course'] = [
            'fullname'      => $course->full_name,
            'summary'       => strip_tags($course->summary),
            'img'           => $course->img,
            'resource_num'  => $course->resource_num,
            'activity_num'  => $course->activity_num,
            'students_num'  => $course->students,
            'forums_num'    => $course->forums,
            'assigns_num'   => $course->assigns,
            'quizs_num'     => $course->quiz
        ];
        $course->teachers = $course->teachers ?: 0;
        $teachers_sql  = 'SELECT ';
        $teachers_sql .= 'teacher_id,name,dept,url ';
        $teachers_sql .= 'FROM {block_data_screen_college} ';
        $teachers_sql .= "WHERE teacher_id IN ($course->teachers) AND type=0";
        $teachers = $DB->get_records_sql($teachers_sql);
        foreach ($teachers as $value) {
            $rtn['teacher_list'][] = [
                'id'    => $value->teacher_id,
                'name'  => $value->name,
                'dept'  => $value->dept,
                'url'   => $value->url
            ];
        }
        if (!isset($rtn['teacher_list'])) {
            $rtn['teacher_list'] = [];
        }

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function course_detail_returns()
    {
        return new external_single_structure(array(
            'course' => new external_single_structure(array(
                'fullname'      => new external_value(PARAM_TEXT, 'Course full name'),
                'summary'       => new external_value(PARAM_TEXT, 'Course summary'),
                'img'           => new external_value(PARAM_TEXT, 'Course cover'),
                'resource_num'  => new external_value(PARAM_INT, 'Course resource counts'),
                'activity_num'  => new external_value(PARAM_INT, 'Course activity counts'),
                'students_num'      => new external_value(PARAM_INT, 'Student number'),
                'forums_num'        => new external_value(PARAM_INT, 'Course forum counts'),
                'assigns_num'       => new external_value(PARAM_INT, 'Course assign counts'),
                'quizs_num'         => new external_value(PARAM_INT, 'Course quiz counts')
            ), 'Course information'),
            'teacher_list' => new external_multiple_structure(new external_single_structure(array(
                'id'    => new external_value(PARAM_INT, 'Teacher id'),
                'name'  => new external_value(PARAM_TEXT, 'Teacher name'),
                'dept'  => new external_value(PARAM_TEXT, 'Department'),
                'url'   => new external_value(PARAM_TEXT, 'Avatar')
            )), 'Teacher list', VALUE_DEFAULT, []),
        ));
    }


    public static function course_teachinfo_parameters()
    {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Course ID'),
        ));
    }

    public static function course_teachinfo($id)
    {
        global $DB, $PAGE;

        $params = self::validate_parameters(self::course_detail_parameters(), array('id'=>$id));

        $rtn = [];

        $context = \context_course::instance($id);
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $allModule = get_fast_modinfo($params['id'])->get_cms();

        $mod_arr = $cmId = [];
        foreach ($allModule as $value) {
            $cmId[]                 = $value->id;
            $mod_arr[$value->id]    = [
                'section'   =>$value->section,
                'module'    =>$value->module,
                'name'      =>$value->get_section_info()->name ?: $value->get_section_info()->section,
                'instance'  =>$value->instance,
                'mod_name'   => $value->modname,
                'instance_name' => $value->name,
            ];
        }

        $course = $DB->get_record_sql("SELECT full_name,students FROM {block_data_screen_course} WHERE course_id=?", [$params['id']]);
        if (!$course) {
            throw new moodle_exception('empty_course', 'block_data_screen');
        }
        $rtn['course'] = [
            'fullname'      => $course->full_name,
        ];

        $enableCompletion = $DB->get_record('course', ['id'=>$params['id']], 'enablecompletion');

        $sections = $DB->get_records_sql("SELECT id,section,name FROM {course_sections} WHERE course=?", [$params['id']]);
        foreach ($sections as $key => $value) {
            // According to the final results of the statistics
            //$modules = $DB->get_records_sql("SELECT instance_id,instance_name,mod_name,SUM(access_num)access_num,COUNT(DISTINCT CASE WHEN finalgrade>0 THEN user_id END)completions FROM {block_data_screen_visit} WHERE course_id=? AND section=? AND type>0 GROUP BY instance_id", [$params['id'], $value->section]);
            $modules = $DB->get_records_sql("SELECT instance_id,instance_name,mod_name,SUM(access_num)access_num FROM {block_data_screen_visit} WHERE course_id=? AND section=? AND type>0 GROUP BY instance_id", [$params['id'], $value->section]);
            if (!$value->name) {
                switch ($value->section) {
                    case 0:
                        $value->name = get_string('conventional', 'block_data_screen');
                        break;
                    default:
                        $value->name = get_string('theme', 'block_data_screen') . $value->section;
                        break;
                }
            }
            $rtn['section'][$key]['name'] = $value->name ?: '';
            $rtn['section'][$key]['item'] = [];
            foreach ($modules as $val) {
                if (in_array($val->instance_id, $cmId)) {
                    // $completions = $DB->count_records('course_modules_completion', ['coursemoduleid'=>$val->instance_id, 'completionstate'=>1]);
                    if ($enableCompletion->enablecompletion == 1) {
                        $grade = $DB->get_record(
                            'grade_items',
                            [
                                'courseid'=> $params['id'],
                                'itemtype'=>'mod',
                                'itemmodule'=>$val->mod_name,
                                'iteminstance' => $mod_arr[$val->instance_id]['instance']
                            ]
                        );
                        if ($grade) {
                            $completions = $DB->count_records_sql('
                                SELECT
                                    COUNT(ra.id)
                                FROM {role_assignments} ra
                                JOIN {course_modules_completion} cmc ON ra.userid=cmc.userid
                                WHERE ra.contextid=?
                                AND ra.roleid=5
                                AND cmc.coursemoduleid=?
                                AND cmc.completionstate=1', [$context->id, $val->instance_id]);

                            $completion = $completions ? round(($completions/$course->students) * 100, 2) . "%" : "0";
                        } else {
                            $completion = '--';
                        }
                    } else {
                        $completion = '--';
                    }

                    $rtn['section'][$key]['item'][] = [
                        'name'          => $val->instance_name,
                        'completion'    => $completion,
                        'access'        => $val->access_num ?: 0
                    ];
                    unset($cmId[array_search($val->instance_id, $cmId)]);
                }
            }
        }
        foreach ($cmId as $value) {
            // $name = $DB->get_record_select($tables[$mod_arr[$value]['module']], ' id=?', [$mod_arr[$value]['instance']], 'name');
            if ($enableCompletion->enablecompletion == 1) {
                $grade = $DB->get_record(
                    'grade_items',
                    [
                        'courseid'  => $params['id'],
                        'itemtype'  =>'mod',
                        'itemmodule'=> $mod_arr[$value]['mod_name'],
                        'iteminstance' => $mod_arr[$value]['instance']
                    ]
                );
            } else {
                $grade = '';
            }
            if (!isset($rtn['section'][$mod_arr[$value]['section']])) {
                $rtn['section'][$mod_arr[$value]['section']]['name'] = $mod_arr[$value]['modname'];
            }
            $rtn['section'][$mod_arr[$value]['section']]['item'][] = [
                'name' => $mod_arr[$value]['instance_name'],
                'completion' => $grade ? '0' : '--',
                'access' => 0
            ];
        }
// print_r($rtn);die;
        return $rtn;
    }

    public static function course_teachinfo_returns()
    {
        return new external_single_structure(array(
            'course' => new external_single_structure(array(
                'fullname'      => new external_value(PARAM_TEXT, 'Course full name'),
            ), 'Course information'),
            'section' => new external_multiple_structure(new external_single_structure(array(
                'name'  => new external_value(PARAM_TEXT, 'Section title'),
                'item'  => new external_multiple_structure(new external_single_structure(array(
                    'name'          => new external_value(PARAM_TEXT, 'Resource or activity name'),
                    'completion'    => new external_value(PARAM_TEXT, 'Resource or activity completion status'),
                    'access'        => new external_value(PARAM_TEXT, 'Resource or activity visit counts'),
                ))),
            )))
        ));
    }

    /**
     * Return descripiton of method parameters
     *
     * @return external_function_parameters
     */
    public static function activity_analysis_parameters()
    {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Course ID'),
        ));
    }

    /**
     * Activity analysis
     *
     * @param $id
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function activity_analysis($id)
    {
        global $DB, $PAGE, $CFG;

        $rtn = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::activity_analysis_parameters(), array('id'=>$id));
        require_once("$CFG->dirroot/blocks/data_screen/lib.php");
        $mod_ids = get_mod_id();
        if (!is_array($mod_ids['resource'])) {
            $resource_arr   = explode(',', $mod_ids['resource']);
        } else {
            $resource_arr = $mod_ids['resource'];
        }
        $course         = $DB->get_record_sql('SELECT full_name,students FROM {block_data_screen_course} WHERE course_id=?', [$params['id']]);
        $sections       = $DB->get_records_sql("SELECT id,section,name FROM {course_sections} WHERE course=?", [$params['id']]);
        $forums         = $DB->get_records_sql('SELECT id,name,teacher_posts,reply_teacher,student_posts,reply_student,section FROM {block_data_screen_forum} WHERE course=?', [$params['id']]);
        $post_counts = $reply_counts = 0;
        foreach ($forums as $forum) {
            $post_counts    += $forum->teacher_posts + $forum->student_posts;
            $reply_counts   += $forum->reply_teacher + $forum->reply_student;
        }
        $assign_quiz    = $DB->get_records_sql('SELECT id,post_counts,grade_avg,activity_id,type FROM {block_data_screen_quiz} WHERE course=?', [$params['id']]);
        $grades         = [];
        $assign_counts = $assign_posts = $quiz_counts = $quiz_posts = 0;
        foreach ($assign_quiz as $grade) {
            $grades[$grade->activity_id] = [
                'posts' => $grade->post_counts,
                'avg'   => $grade->grade_avg,
            ];
            if ($grade->type==0 && $grade->post_counts!=0) {
                $assign_counts  = $assign_counts + $grade->grade_avg;
                $assign_posts   = $assign_posts + $grade->post_counts;
            }
            if ($grade->type==1 && $grade->post_counts!=0) {
                $quiz_counts  = $quiz_counts + $grade->grade_avg;
                $quiz_posts   = $quiz_posts + $grade->post_counts;
            }
        }
        $total = ['assign'=>0, 'quiz'=>0];
        $mod_hvp    = $DB->get_record('modules', ['name'=>'hvp']);
        foreach ($sections as $key => $section) {
            if ($section->section == 0) {
                $section_name = get_string('conventional', 'block_data_screen');
            } else {
                $section_name = get_string('theme', 'block_data_screen') . $section->section;
            }
            $rtn['resource_statistics'][$section->section]['name']       = $section->name ?: $section_name;
            $rtn['forum_statistics'][$section->section]['name']          = $rtn['resource_statistics'][$section->section]['name'];
            $rtn['study_statistics'][$section->section]['name']          = $rtn['resource_statistics'][$section->section]['name'];
            $rtn['assign_quiz_statistics'][$section->section]['name']    = $rtn['resource_statistics'][$section->section]['name'];
            $rtn['resource_statistics'][$section->section]['item']       = ['other'=>0,'forum'=>[],'assign'=>[],'quiz'=>[],'hvp'=>[],'resource'=>[]];
            $rtn['forum_statistics'][$section->section]['item']          = [];
            $rtn['study_statistics'][$section->section]['item']          = [];
            $rtn['assign_quiz_statistics'][$section->section]['item']    = [];


            //$counts = $DB->get_recordset_sql('SELECT COUNT(DISTINCT instance_id) counts,mod_name FROM {block_data_screen_visit} WHERE course_id=? AND section=? AND type>0 GROUP BY type', [$params['id'], $section->section]);
            $counts = $DB->get_recordset_sql(
                'SELECT
                    COUNT( cm.id ) counts,
                    m.name mod_name
                FROM
                    {course_modules} cm
                    JOIN {modules} m ON cm.module = m.id
                    JOIN {course_sections} cs ON cm.section = cs.id
                WHERE
                    cm.course = ?
                    AND cs.section = ?
                GROUP BY
                    cm.module',
                [$params['id'], $section->section]
            );
            foreach ($counts as $module) {
                switch ($module->mod_name) {
                    case 'forum':
                        $post_counts = $reply_counts =0;
                        foreach ($forums as $forum) {
                            if ($forum->section==$section->section) {
                                $post_counts    += $forum->teacher_posts + $forum->student_posts;
                                $reply_counts   += $forum->reply_teacher + $forum->reply_student;
                                $rtn['forum_statistics'][$section->section]['item'][] = [
                                    'name' => $forum->name,
                                    'student_posts' => $forum->student_posts,
                                    'reply_student' => $forum->reply_student,
                                    'teacher_posts' => $forum->teacher_posts,
                                    'reply_teacher' => $forum->reply_teacher,
                                ];
                            }
                        }
                        $rtn['resource_statistics'][$section->section]['item']['forum'] = [
                            'name'          => get_string('pluginname', $module->mod_name),
                            'counts'        => $module->counts,
                            'discussions'   => $post_counts,
                            'reply'         => $reply_counts
                        ];
                        break;
                    case 'assign':
                        $rtn['resource_statistics'][$section->section]['item']['assign'] = [
                            'name'          => get_string('pluginname', $module->mod_name),
                            'counts'        => $module->counts,
                        ];
                        break;
                    case 'quiz':
                        $rtn['resource_statistics'][$section->section]['item']['quiz'] = [
                            'name'          => get_string('pluginname', $module->mod_name),
                            'counts'        => $module->counts,
                        ];
                        break;
                    case 'resource':
                        $rtn['resource_statistics'][$section->section]['item']['resource']['resource'] = $module->counts;
                        $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] = $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] ? $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] + $module->counts : $module->counts;
                        break;
                    case 'url':
                        $rtn['resource_statistics'][$section->section]['item']['resource']['url'] = $module->counts;
                        $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] = $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] ? $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] + $module->counts : $module->counts;
                        break;
                    case 'folder':
                        $rtn['resource_statistics'][$section->section]['item']['resource']['folder'] = $module->counts;
                        $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] = $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] ? $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] + $module->counts : $module->counts;
                        break;
                    case 'hvp':
                        $rtn['resource_statistics'][$section->section]['item']['hvp'] = [
                            'name'          => 'hvp',
                            'counts'        => $module->counts,
                        ];
                        break;
                    case 'page':
                        $rtn['resource_statistics'][$section->section]['item']['resource']['page'] = $module->counts;
                        $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] = $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] ? $rtn['resource_statistics'][$section->section]['item']['resource']['counts'] + $module->counts : $module->counts;
                        break;
                    default:
                        $rtn['resource_statistics'][$section->section]['item']['other'] = $rtn['resource_statistics'][$section->section]['item']['other'] ? $rtn['resource_statistics'][$section->section]['item']['other'] + $module->counts : $module->counts;
                        break;
                }
            }

            $modules = $DB->get_recordset_sql('SELECT type resource_type,instance_name resource_name,mod_name module_name,instance_id resource_id,SUM(download) download_num,SUM(access_num)access_num,SUM(spend_time) spendtime,COUNT(DISTINCT user_id) visitor_num FROM {block_data_screen_visit} WHERE course_id=? AND section=? AND type>0 GROUP BY instance_id', [$params['id'], $section->section]);
            foreach ($modules as $module) {
                if (in_array($module->resource_type, $resource_arr)) {
                    // study resource statistics
                    $rtn['study_statistics'][$section->section]['item'][] = [
                        'name'          => $module->resource_name,
                        'visiter_num'   => $module->visitor_num,
                        'access_num'    => $module->access_num,
                        'download_num'  => $module->download_num,
                    ];
                } elseif ($module->module_name=='assign' || $module->module_name=='quiz') {
                    // assign and quiz statistics
                    $total[$module->module_name] += 1;
                    $rtn['assign_quiz_statistics'][$section->section]['item'][] = [
                        'name'      => $module->resource_name,
                        'posts'     => $grades[$module->resource_id]['posts'] ?: 0,
                        'avg'       => $grades[$module->resource_id]['posts'] ? round((int)$grades[$module->resource_id]['avg'], 2) : 0,
                        'students'  => $course->students ?: 0
                    ];
                }
            }
        }
        $rtn['assign_quiz_total'] = [
            'name' => get_string('statistics', 'block_data_screen'),
            'item' => [
                [
                    'name'      => get_string('assign', 'block_data_screen'),
                    'posts'     => $assign_posts,
                    'avg'       => $assign_counts ? round($assign_counts/$total['assign'], 2) : 0,
                    'students'  => $course->students ?: 0
                ],
                [
                    'name'      => get_string('quiz', 'block_data_screen'),
                    'posts'     => $quiz_posts,
                    'avg'       => $quiz_counts ? round($quiz_counts/$total['quiz'], 2) : 0,
                    'students'  => $course->students ?: 0
                ]
            ]
        ];
        $rtn['course_name'] = $course->full_name ?: '';

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_single_structure
     * @throws coding_exception
     */
    public static function activity_analysis_returns()
    {
        return new external_single_structure(array(
            'resource_statistics' => new external_multiple_structure(new external_single_structure(array(
                'name' => new external_value(PARAM_TEXT, 'Topic name'),
                'item' => new external_single_structure(array(
                    'assign' => new external_single_structure(array(
                        'name'          => new external_value(PARAM_TEXT, 'Assign', VALUE_DEFAULT, get_string('pluginname', 'assign')),
                        'counts'        => new external_value(PARAM_INT, 'Assign counts', VALUE_DEFAULT, 0),
                    ), 'Assign', VALUE_DEFAULT, []),
                    'quiz' => new external_single_structure(array(
                        'name'          => new external_value(PARAM_TEXT, 'Quiz', VALUE_DEFAULT, get_string('pluginname', 'quiz')),
                        'counts'        => new external_value(PARAM_INT, 'Quiz counts', VALUE_DEFAULT, 0),
                    ), 'Quiz', VALUE_DEFAULT, []),
                    'hvp' => new external_single_structure(array(
                        'name'          => new external_value(PARAM_TEXT, 'HVP', VALUE_DEFAULT, 'hvp'),
                        'counts'        => new external_value(PARAM_INT, 'HVP counts', VALUE_DEFAULT, 0),
                    ), 'HVP', VALUE_DEFAULT, []),
                    'other' =>  new external_value(PARAM_INT, 'Other', VALUE_DEFAULT, 0),
                    'forum' => new external_single_structure(array(
                        'name'          => new external_value(PARAM_TEXT, 'Module name', VALUE_DEFAULT, get_string('pluginname', 'forum')),
                        'counts'        => new external_value(PARAM_INT, 'Module counts', VALUE_DEFAULT, 0),
                        'discussions'   => new external_value(PARAM_INT, 'Discussions counts', VALUE_DEFAULT, 0),
                        'reply'         => new external_value(PARAM_INT, 'Reply counts', VALUE_DEFAULT, 0)
                    ), 'Forum', VALUE_DEFAULT, []),
                    'resource' => new external_single_structure(array(
                        'resource'      => new external_value(PARAM_INT, 'Resource counts', VALUE_DEFAULT, 0),
                        'url'           => new external_value(PARAM_INT, 'Url counts', VALUE_DEFAULT, 0),
                        'page'          => new external_value(PARAM_INT, 'Page counts', VALUE_DEFAULT, 0),
                        'folder'        => new external_value(PARAM_INT, 'Folder counts', VALUE_DEFAULT, 0),
                        'counts'        => new external_value(PARAM_INT, 'Total', VALUE_DEFAULT, 0),
                    ), 'Resource', VALUE_DEFAULT, []),
                )),
            ), 'Resource statistics data')),
            'study_statistics' => new external_multiple_structure(new external_single_structure(array(
                'name' => new external_value(PARAM_TEXT, 'Topic name'),
                'item' => new external_multiple_structure(new external_single_structure(array(
                    'name'          => new external_value(PARAM_TEXT, 'Resource name'),
                    'visiter_num'   => new external_value(PARAM_INT, 'Visiter counts'),
                    'access_num'    => new external_value(PARAM_INT, 'Access counts'),
                    'download_num'  => new external_value(PARAM_INT, 'Download counts'),
                ))),
            ), 'Study statistics data')),
            'assign_quiz_statistics' => new external_multiple_structure(new external_single_structure(array(
                'name' => new external_value(PARAM_TEXT, 'Topic name'),
                'item' => new external_multiple_structure(new external_single_structure(array(
                    'name'      => new external_value(PARAM_TEXT, 'Assign or quiz name'),
                    'posts'     => new external_value(PARAM_TEXT, 'Submit number'),
                    'avg'       => new external_value(PARAM_TEXT, 'Average'),
                    'students'  => new external_value(PARAM_INT, 'Students'),
                ))),
            ), 'Assign and quiz statistics data')),
            'assign_quiz_total' => new external_single_structure(array(
                'name' => new external_value(PARAM_TEXT, 'Topic name'),
                'item' => new external_multiple_structure(new external_single_structure(array(
                    'name'      => new external_value(PARAM_TEXT, 'Name'),
                    'posts'     => new external_value(PARAM_TEXT, 'Posts counts'),
                    'avg'       => new external_value(PARAM_TEXT, 'Average'),
                    'students'  => new external_value(PARAM_TEXT, 'Students'),
                ))),
            ), 'Assign or quiz total'),
            'forum_statistics' => new external_multiple_structure(new external_single_structure(array(
                'name' => new external_value(PARAM_TEXT, 'Topic name'),
                'item' => new external_multiple_structure(new external_single_structure(array(
                    'name'          => new external_value(PARAM_TEXT, 'Forum name'),
                    'student_posts' => new external_value(PARAM_INT, 'Student post'),
                    'reply_student' => new external_value(PARAM_INT, 'Reply student'),
                    'teacher_posts' => new external_value(PARAM_INT, 'Teacher post'),
                    'reply_teacher' => new external_value(PARAM_INT, 'Reply teacher'),
                ))),
            ), 'Forum statistics data')),
            'course_name'   => new external_value(PARAM_TEXT, 'Course name')
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function personal_analysis_parameters()
    {
        return new external_function_parameters(array(
            'role' => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0),
            'name' => new external_value(PARAM_TEXT, 'User first name', VALUE_DEFAULT, ''),
            'course' => new external_value(PARAM_INT, 'Course ID'),
            'cur_role' => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0),
            'user'     => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
            'page'          => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1),
            'pagesize'      => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 12),
        ));
    }

    /**
     * Personal analysis
     *
     * @param $role
     * @param $name
     * @param $course
     * @param $cur_role
     * @param $user
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function personal_analysis($role, $name, $course, $cur_role, $user, $page, $pagesize)
    {
        global $DB, $PAGE;

        $rtn = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::personal_analysis_parameters(), array(
            'role'      => $role,
            'name'      => $name,
            'course'    => $course,
            'cur_role'  => $cur_role,
            'user'      => $user,
            'page'      => $page<=0 ? 1 : $page,
            'pagesize'  => $pagesize<0 ? 1 : $pagesize,
        ));

        $where1 = '';
        if ($params['name']) {
            $where1 = ' AND firstname="' . $params['name'] . '"';
        }

        $course    = $DB->get_record_select('block_data_screen_course', ' course_id=?', [$params['course']], 'full_name');
        $contextid = $DB->get_record_select('context', ' contextlevel=50 AND instanceid=?', [$params['course']], 'id')->id;

        switch ($params['cur_role']) {
            case 5:
                if ($params['role']) {
                    $courseUsers = $DB->get_records_select('role_assignments', ' contextid=? AND roleid=? AND userid=?', [$contextid, $params['role'], $params['user']], 'userid,roleid');
                } else {
                    $courseUsers = $DB->get_records_select('role_assignments', ' contextid=? AND roleid=5 AND userid=?', [$contextid, $params['user']], 'userid,roleid');
                }
                break;
            case 6:
                $courseUsers = [];
                break;
            default:
                if ($params['role']) {
                    $courseUsers = $DB->get_records_select('role_assignments', ' contextid=? AND roleid=?', [$contextid, $params['role']], 'userid,roleid');
                } else {
                    $courseUsers = $DB->get_records_select('role_assignments', ' contextid=?', [$contextid], 'userid,roleid');
                }
                break;
        }

        $userids    = [0];
        $role       = [];
        foreach ($courseUsers as $value) {
            $userids[]              = $value->userid;
            $role[$value->userid]  = $value->roleid;
        }
        $userids = implode(',', $userids);
        $counts  = $DB->count_records_sql('SELECT COUNT(id) FROM {user} WHERE id IN (' . $userids . ')' . $where1);
        if ($params['pagesize']) {
            $where1 = $where1 . " LIMIT " . (($params['page']-1) * $params['pagesize']) . "," . $params['pagesize'];
        }
        $users = $DB->get_records_sql('SELECT * FROM {user} WHERE id IN (' . $userids . ')' . $where1);
        if ($course) {
            foreach ($users as $value) {
                $courseUser = $DB->get_record_sql(
                    'SELECT spend_time FROM {block_data_screen_visit} WHERE user_id=? AND course_id=? AND type=0',
                    [$value->id, $params['course']]
                );
                // $grade = $DB->get_record_sql(
                //     'SELECT SUM(finalgrade)finalgrade FROM {block_data_screen_visit} WHERE course_id=? AND user_id=? AND type<>0',
                //     [$params['course'], $value->id]
                // );
                $grade = $DB->get_record_sql(
                    'SELECT finalgrade FROM mdl_grade_grades gg JOIN mdl_grade_items gi ON gi.id=gg.itemid WHERE gi.courseid=? AND gi.itemtype="course" AND gg.userid=?',
                    [$params['course'], $value->id]
                );

                $login = $DB->get_record('block_data_screen_user', ['user_id'=>$value->id]);
                $completion_status = self::get_activities_completion_status($params['course'], $value->id);
                $has_completion = 0;
                foreach ($completion_status['statuses'] as $key => $row) {
                    if ($row['state'] == 1) {
                        $has_completion += 1;
                    }
                }
                if ($has_completion) {
                    $hadCompleteNum = count($completion_status['statuses']) ? $has_completion / count($completion_status['statuses']) : 0;
                    $hadCompleteRate = round((($hadCompleteNum*100)/100) * 100) . "%";
                } else {
                    $hadCompleteRate = "0%";
                }

                $userpicture = new \user_picture($value);
                $userpicture->size = 1; // Size f1.
                $rtn['course_list'][] = [
                    'id'        => $value->id,
                    'avatar'    => $userpicture->get_url($PAGE)->out(false),
                    'name'      => $value->firstname,
                    'dept'      => $value->department,
                    'idnumber'  => $value->username,
                    'role'      => $role[$value->id],
                    'spend_time'=> $courseUser->spend_time ? round($courseUser->spend_time / (60*60), 2) : 0,
                    'login'     => $login->login ?: 0,
                    'grade'     => $grade ? (int)$grade->finalgrade : 0,
                    'completion_rate' => $hadCompleteRate
                ];
            }
        } else {
            $rtn['course_list'] = [];
        }
        $rtn['course'] = $course->full_name ?: '';

        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_multiple_structure
     */
    public static function personal_analysis_returns()
    {
        return new external_single_structure(array(
            'course_list' => new external_multiple_structure(new external_single_structure(array(
                'id'        => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, ''),
                'avatar'    => new external_value(PARAM_TEXT, 'Avatar', VALUE_DEFAULT, ''),
                'name'      => new external_value(PARAM_TEXT, 'User name', VALUE_DEFAULT, ''),
                'dept'      => new external_value(PARAM_TEXT, 'Department', VALUE_DEFAULT, ''),
                'idnumber'  => new external_value(PARAM_TEXT, 'ID number', VALUE_DEFAULT, ''),
                'role'      => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, ''),
                'spend_time'=> new external_value(PARAM_TEXT, 'Spend time', VALUE_DEFAULT, ''),
                'login'     => new external_value(PARAM_INT, 'Login times'),
                'grade'     => new external_value(PARAM_INT, 'Grade'),
                'completion_rate' => new external_value(PARAM_TEXT, 'Completion rate')
            )), 'Course list', VALUE_DEFAULT, []),
            'course' => new external_value(PARAM_TEXT, 'Course full name', VALUE_DEFAULT, ''),
            'page' => new external_single_structure(array(
                'max_page'  => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'  => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
        ));
    }

    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function personal_detail_parameters()
    {
        return new external_function_parameters(array(
            'role'      => new external_value(PARAM_INT, 'Role ID'),
            'id'        => new external_value(PARAM_INT, 'User ID'),
            'course'    => new external_value(PARAM_INT, 'Course ID'),
        ));
    }

    /**
     * Personal detail
     *
     * @param $role
     * @param $id
     * @param $course
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function personal_detail($role, $id, $course)
    {
        global $DB,$PAGE;

        $rtn = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::personal_detail_parameters(), array(
            'role'  => $role,
            'id'    => $id,
            'course'=> $course
        ));

        $allModule  = $DB->get_records_sql(
            'SELECT
                      cm.id,
                      cm.module,
                      cm.instance,
                      cs.section,
                      cs.name
                  FROM {course_modules} cm
                  JOIN {course_sections} cs ON cm.section=cs.id
                  WHERE cm.course=? AND cm.deletioninprogress=0',
            [$params['course']]
        );
        $mod_arr    = $cmId = [];
        foreach ($allModule as $value) {
            $cmId[] = $value->id;
            $mod_arr[$value->id] = ['section'=>$value->section, 'module'=>$value->module, 'name'=>$value->name, 'instance'=>$value->instance];
        }
        $mods   = $DB->get_records_sql('SELECT id,name FROM {modules}');
        $tables = [];
        foreach ($mods as $value) {
            $tables[$value->id] = $value->name;
        }

        $sections = $DB->get_records_sql("SELECT section,name FROM {course_sections} WHERE course=?", [$params['course']]);
        $systemcontext = context_system::instance();

        // Get some basic data we are going to need.
        $roles = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);

        foreach ($roles as $value) {
            $roles[$value->id] = $value->localname;
        }

        $course     = $DB->get_record_select('block_data_screen_course', 'course_id=?', [$params['course']], 'full_name');
        //$students   = $DB->get_record_sql('SELECT students FROM {block_data_screen_course} WHERE course_id=?', [$params['course']])->students;
        $user       = $DB->get_record_sql('SELECT id,firstname,lastname,department FROM {user} WHERE id=?', [$params['id']]);
        $courseUser = $DB->get_record_sql('SELECT role,create_num created,spend_time FROM {block_data_screen_visit} WHERE user_id=? AND course_id=? AND type=0', [$params['id'], $params['course']]);
        $login      = $DB->get_record_sql('SELECT login FROM {block_data_screen_user} WHERE user_id=?', [$params['id']]);
        $userpicture = new \user_picture($user);
        $userpicture->size = 1; // Size f1.

        $rtn['user'] = [
            'course_name'   => $course->full_name,
            'avatar'        => $userpicture->get_url($PAGE)->out(false),
            'name'          => $user->lastname.$user->firstname,
            'department'    => $user->department,
            'role'          => $roles[$params['role']],
            'login'         => $login->login ?: 0,
            'spendtime'     => $courseUser->spend_time ? round($courseUser->spend_time / (60*60), 2) : 0,
        ];

        $completion = 0;
        switch ($params['role']) {
            case 5:
                $modules = $DB->get_records_sql('SELECT instance_id,instance_name,mod_name,section,access_num,grademax,finalgrade FROM {block_data_screen_visit} WHERE course_id=? AND user_id=? AND type>0', [$params['course'], $params['id']]);
                foreach ($sections as $key => $value) {
                    if ($value->section == 0) {
                        $section_name = get_string('conventional', 'block_data_screen');
                    } else {
                        $section_name = get_string('theme', 'block_data_screen') . $value->section;
                    }
                    $rtn['section'][$key]['name'] = $value->name ?: $section_name;
                    $rtn['section'][$key]['item'] = [];
                    foreach ($modules as $val) {
                        if ($val->section==$value->section) {
                            if ($val->grademax) {
                                $grade = $val->finalgrade ? get_string('finished', 'block_data_screen') : get_string('unfinished', 'block_data_screen');
                            } else {
                                $mod_max_grade = $DB->get_record_sql(
                                    'SELECT * FROM {grade_items} WHERE courseid=? AND itemmodule=? AND iteminstance=?',
                                    [$params['course'], $val->mod_name, $mod_arr[$val->instance_id]['instance']]
                                );
                                if ($mod_max_grade) {
                                    $grade = get_string('unfinished', 'block_data_screen');
                                } else {
                                    $grade = '--';
                                }
                            }
                            $rtn['section'][$key]['item'][] = [
                                'name'          => $val->instance_name,
                                'completion'    => $grade,
                                'access'        => $val->access_num ?: 0
                            ];
                            if (in_array($val->instance_id, $cmId)) {
                                unset($cmId[array_search($val->instance_id, $cmId)]);
                            }
                        }
                    }
                }
                foreach ($cmId as $value) {
                    $name = $DB->get_record_select($tables[$mod_arr[$value]['module']], ' id=?', [$mod_arr[$value]['instance']], 'name');
                    $grade = $DB->get_record(
                        'grade_items',
                        [
                            'courseid'=>$params['course'],
                            'itemtype'=>'mod',
                            'itemmodule'=>$tables[$mod_arr[$value]['module']],
                            'iteminstance' => $mod_arr[$value]['instance']
                        ]
                    );
                    $rtn['section'][$mod_arr[$value]['section']]['item'][] = [
                        'name' => $name->name,
                        'completion' => $grade ? get_string('unfinished', 'block_data_screen') : '--',
                        'access' => 0
                    ];
                }
                foreach ($modules as $val) {
                    $completion += $val->grademax ? $val->finalgrade : 0;
                }
                break;
            default:
                $modules = $DB->get_records_sql('SELECT instance_id,mod_name,instance_name resource_name,SUM(access_num) access_num,COUNT(DISTINCT CASE WHEN finalgrade>0 THEN user_id END) completions,section num FROM {block_data_screen_visit} WHERE course_id=? AND type>0 GROUP BY instance_id', [$params['course']]);
                foreach ($sections as $key => $value) {
                    if ($value->section == 0) {
                        $section_name = get_string('conventional', 'block_data_screen');
                    } else {
                        $section_name = get_string('theme', 'block_data_screen') . $value->section;
                    }
                    $rtn['section'][$key]['name'] = $value->name ?: $section_name;
                    $rtn['section'][$key]['item'] = [];
                    foreach ($modules as $val) {
                        if ($val->num==$value->section) {
                            if ($val->grademax) {
                                $grade = $val->finalgrade ? get_string('finished', 'block_data_screen') : get_string('unfinished', 'block_data_screen');
                            } else {
                                $mod_max_grade = $DB->get_record_sql(
                                    'SELECT * FROM {grade_items} WHERE courseid=? AND itemmodule=? AND iteminstance=?',
                                    [$params['course'], $val->mod_name, $mod_arr[$val->instance_id]['instance']]
                                );
                                if ($mod_max_grade) {
                                    $grade = get_string('unfinished', 'block_data_screen');
                                } else {
                                    $grade = '--';
                                }
                            }
                            $rtn['section'][$key]['item'][] = [
                                'name'          => $val->resource_name,
                                'completion'    => $grade,
                                'access'        => $val->access_num ?: 0
                            ];
                        }
                        if (in_array($val->instance_id, $cmId)) {
                            unset($cmId[array_search($val->instance_id, $cmId)]);
                        }
                    }
                }
                foreach ($cmId as $value) {
                    $name = $DB->get_record_select($tables[$mod_arr[$value]['module']], ' id=?', [$mod_arr[$value]['instance']], 'name');
                    $grade = $DB->get_record(
                        'grade_items',
                        [
                            'courseid'=>$params['course'],
                            'itemtype'=>'mod',
                            'itemmodule'=>$tables[$mod_arr[$value]['module']],
                            'iteminstance' => $mod_arr[$value]['instance']
                        ]
                    );
                    $rtn['section'][$mod_arr[$value]['section']]['item'][] = [
                        'name' => $name->name,
                        'completion' => $grade ? get_string('unfinished', 'block_data_screen') : '--',
                        'access' => 0
                    ];
                }
                break;
        }
        if ($params['role']==5) {
            $rtn['user']['completionORcreated'] = $completion ?: 0;
        } else {
            $rtn['user']['completionORcreated'] = $courseUser->created ?: 0;
        }
        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return
     */
    public static function personal_detail_returns()
    {
        return new external_single_structure(array(
            'user' => new external_single_structure(array(
                'course_name'   => new external_value(PARAM_TEXT, 'Course full name'),
                'avatar'        => new external_value(PARAM_TEXT, 'User avatar url'),
                'name'          => new external_value(PARAM_TEXT, 'User name'),
                'department'    => new external_value(PARAM_TEXT, 'Department'),
                'role'          => new external_value(PARAM_TEXT, 'Role'),
                'login'         => new external_value(PARAM_INT, 'Login counts'),
                'spendtime'     => new external_value(PARAM_TEXT, 'Spend time'),
                'completionORcreated'    => new external_value(PARAM_TEXT, 'Course grade or Create mod number'),
            )),
            'section' => new external_multiple_structure(new external_single_structure(array(
                'name'  => new external_value(PARAM_TEXT, 'Section title'),
                'item'  => new external_multiple_structure(new external_single_structure(array(
                    'name'          => new external_value(PARAM_TEXT, 'Resource or activity name'),
                    'completion'    => new external_value(PARAM_TEXT, 'Resource or activity completion status'),
                    'access'        => new external_value(PARAM_TEXT, 'Resource or activity visit counts'),
                ))),
            ))),
        ));
    }


    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_role_parameters()
    {
        return new external_function_parameters([]);
    }

    /**
     * Get role
     *
     * @param $userid
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_role()
    {
        global $DB, $PAGE;

        $rtn = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        // Get some basic data we are going to need.
        $roles = role_fix_names(get_all_roles(), $context, ROLENAME_ORIGINAL);

        foreach ($roles as $value) {
            $rtn[] = [
                'id' => $value->id,
                'name' => $value->localname
            ];
        }

        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return external_multiple_structure
     */
    public static function get_role_returns()
    {
        return new external_multiple_structure(new external_single_structure(array(
            'id'    => new external_value(PARAM_INT, 'Role ID'),
            'name'  => new external_value(PARAM_TEXT, 'Role name')
        )));
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_activities_completion_status_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid'   => new external_value(PARAM_INT, 'User ID'),
            )
        );
    }

    /**
     * Get Activities completion status
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array of activities progress and warnings
     * @throws moodle_exception
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_activities_completion_status($courseid, $userid)
    {
        global $CFG, $USER, $PAGE;
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->dirroot . '/lib/completionlib.php');

        $warnings = array();
        $arrayparams = array(
            'courseid' => $courseid,
            'userid'   => $userid,
        );

        $params = self::validate_parameters(self::get_activities_completion_status_parameters(), $arrayparams);

        $course = get_course($params['courseid']);
        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        $context = context_course::instance($course->id);
        $PAGE->set_context($context);
        self::validate_context($context);

        // Check that current user have permissions to see this user's activities.
        if ($user->id != $USER->id) {
            // require_capability('report/progress:view', $context);
            if (!groups_user_groups_visible($course, $user->id)) {
                // We are not in the same group!
                throw new moodle_exception('accessdenied', 'admin');
            }
        }

        $completion = new completion_info($course);
        $activities = $completion->get_activities();

        $results = array();
        foreach ($activities as $activity) {

            // Check if current user has visibility on this activity.
            if (!$activity->uservisible) {
                continue;
            }

            // Get progress information and state (we must use get_data because it works for all user roles in course).
            $activitycompletiondata = $completion->get_data($activity, true, $user->id);

            $results[] = array(
                'cmid'          => $activity->id,
                'modname'       => $activity->modname,
                'instance'      => $activity->instance,
                'state'         => $activitycompletiondata->completionstate,
                'timecompleted' => $activitycompletiondata->timemodified,
                'tracking'      => $activity->completion,
                'overrideby'    => $activitycompletiondata->overrideby
            );
        }

        $results = array(
            'statuses' => $results,
            'warnings' => $warnings
        );
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_activities_completion_status_returns()
    {
        return new external_single_structure(
            array(
                'statuses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'cmid'          => new external_value(PARAM_INT, 'comment ID'),
                            'modname'       => new external_value(PARAM_PLUGIN, 'activity module name'),
                            'instance'      => new external_value(PARAM_INT, 'instance ID'),
                            'state'         => new external_value(PARAM_INT, 'completion state value:
                                                                    0 means incomplete, 1 complete,
                                                                    2 complete pass, 3 complete fail'),
                            'timecompleted' => new external_value(PARAM_INT, 'timestamp for completed activity'),
                            'tracking'      => new external_value(PARAM_INT, 'type of tracking:
                                                                    0 means none, 1 manual, 2 automatic'),
                            'overrideby' => new external_value(
                                PARAM_INT,
                                'The user id who has overriden the status, or null',
                                VALUE_OPTIONAL
                            ),
                        ),
                        'Activity'
                    ),
                    'List of activities status'
                ),
                'warnings' => new external_warnings()
            )
        );
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function set_role_parameters()
    {
        return new external_function_parameters(array(
            'role' => new external_value(PARAM_INT, 'Role ID')
        ));
    }

    /**
     * Set role sort
     *
     * @param $role
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function set_role($role)
    {
        global $DB, $PAGE;

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::set_role_parameters(), ['role'=>$role]);
        $sort = $DB->get_record_sql('SELECT sortorder FROM {role} WHERE id=?', [$params['role']]);

        if ($sort) {
            $_SESSION['block_data_screen_role_sort'] = $sort->sortorder;
        } else {
            return ['status'=> 'Fail'];
        }

        return ['status'=> 'Success'];
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function set_role_returns()
    {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status')
        ]);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function search_course_parameters()
    {
        return new external_function_parameters(array(
            'name' => new external_value(PARAM_TEXT, 'Course name'),
            'user' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
            'role' => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0)
        ));
    }

    /**
     * Search course
     *
     * @param $name
     * @param $user
     * @param $role
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function search_course($name, $user, $role)
    {
        global $DB, $PAGE, $CFG;

        $rtn = [];

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::search_course_parameters(), ['name'=>$name, 'user'=>$user, 'role'=>$role]);

        $course = $DB->get_record_sql('SELECT course_id,full_name,catetgory_id,path FROM {block_data_screen_course} WHERE full_name=?', [$params['name']]);
        switch ($params['role']) {
            case 1:
                if ($course) {
                    $rtn = [
                        'id' => $course->course_id,
                        'fullname' => $course->full_name
                    ];
                }
                break;
            case $CFG->block_data_screen_edu_role:
                $instances = $DB->get_records_sql("SELECT ct.instanceid FROM `mdl_role_assignments` ra JOIN `mdl_context` ct ON ct.id=ra.contextid WHERE ct.contextlevel=40 AND ra.userid=? AND ra.roleid=?", [$params['user'], $CFG->block_data_screen_edu_role]);
                $categories = [];
                foreach ($instances as $instance) {
                    $category = $DB->get_record_sql("SELECT id,path FROM {course_categories} WHERE id=?", [$instance->instanceid]);
                    if ($category) {
                        $subcategories = $DB->get_records_sql("SELECT college_id FROM {block_data_screen_college} WHERE college_id=$category->id OR path LIKE '$category->path/%'");
                        foreach ($subcategories as $sub) {
                            $categories[] = $sub->college_id;
                        }
                    }
                }
                if (in_array($course->catetgory_id, $categories)) {
                    $rtn = [
                        'id' => $course->course_id,
                        'fullname' => $course->full_name
                    ];
                }
                break;
            case 6:
                break;
            default:
                $course_id  = $DB->get_record_sql('SELECT GROUP_CONCAT(DISTINCT ct.instanceid)course_id FROM {role_assignments} ra JOIN {context} ct ON ra.contextid=ct.id WHERE userid=?', [$params['user']])->course_id ?: 0;
                if ($course && in_array($course->course_id, explode(',', $course_id))) {
                    $rtn = [
                        'id' => $course->course_id,
                        'fullname' => $course->full_name
                    ];
                }
                break;
        }

        return $rtn;
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function search_course_returns()
    {
        return new external_single_structure(array(
            'id' => new external_value(PARAM_INT, 'Course ID', VALUE_DEFAULT, 0),
            'fullname' => new external_value(PARAM_TEXT, 'Full name', VALUE_DEFAULT, '')
        ), 'Course', VALUE_DEFAULT, []);
    }


    public static function search_courses_parameters()
    {
        return new external_function_parameters(array(
            'name' => new external_value(PARAM_TEXT, 'Course name', VALUE_DEFAULT, ''),
            'user' => new external_value(PARAM_INT, 'User ID', VALUE_DEFAULT, 0),
            'role' => new external_value(PARAM_INT, 'Role ID', VALUE_DEFAULT, 0)
        ));
    }

    public function search_courses($name, $user, $role)
    {
        global $DB, $PAGE, $CFG;

        $rtn = [];

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::search_course_parameters(), ['name'=>$name, 'user'=>$user, 'role'=>$role]);

        $semester = $DB->get_records_sql("SELECT * FROM {block_data_screen_semester} ORDER BY start_time");
        $semester_arr = [0=>get_string('up', 'block_data_screen'), 1=>get_string('down', 'block_data_screen')];
        $sql_like = $DB->sql_like('full_name', ':name');
        switch ($params['role']) {
            case 1:
                $courses = $DB->get_records_sql('SELECT course_id,full_name,start_time,end_time FROM {block_data_screen_course} WHERE '.$sql_like, ['name'=>'%'.$DB->sql_like_escape($params['name']).'%']);
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($courses as $course) {
                        if ($course->start_time>$value->start_time && $course->end_time == 0 && $course->start_time<$value->end_time) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $course->course_id,
                                'fullname'  => $course->full_name,
                            ];
                        } elseif ($course->start_time>$value->start_time && $course->end_time<$value->end_time && $course->end_time != 0) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $course->course_id,
                                'fullname'  => $course->full_name,
                            ];
                        }
                    }
                }
                break;
            case $CFG->block_data_screen_edu_role:
                $instances = $DB->get_records_sql("SELECT ct.instanceid FROM `mdl_role_assignments` ra JOIN `mdl_context` ct ON ct.id=ra.contextid WHERE ct.contextlevel=40 AND ra.userid=? AND ra.roleid=?", [$params['user'], $CFG->block_data_screen_edu_role]);
                $courses = [];
                foreach ($instances as $instance) {
                    $category = $DB->get_record('course_categories', ['id'=>$instance->instanceid]);
                    if ($category) {
                        $course_arr = $DB->get_records_sql("SELECT course_id id,full_name,start_time,end_time FROM {block_data_screen_course} WHERE (category_id=$category->id OR path LIKE '$category->path/%') AND $sql_like", ['name'=>'%'.$DB->sql_like_escape($params['name']).'%']);
                        $courses = array_merge($courses, $course_arr);
                    }
                }
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($courses as $course) {
                        if ($course->start_time>$value->start_time && $course->end_time == 0 && $course->start_time<$value->end_time) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $course->course_id,
                                'fullname'  => $course->full_name,
                            ];
                        } elseif ($course->start_time>$value->start_time && $course->end_time<$value->end_time && $course->end_time != 0) {
                            $rtn[$key]['course_list'][] = [
                                'id'        => $course->course_id,
                                'fullname'  => $course->full_name,
                            ];
                        }
                    }
                }
                break;
            case 6:
                break;
            default:
                $userscourses = enrol_get_users_courses($params['user'], false, '*');
                foreach ($semester as $key => $value) {
                    $rtn[$key]['semester'] = $value->year . " " . $semester_arr[$value->semester];
                    $rtn[$key]['course_list'] = [];
                    foreach ($userscourses as $course) {
                        $num = substr_count($course->fullname, $params['name']);
                        if ($num>0) {
                            if ($course->start_time>$value->start_time && $course->end_time == 0 && $course->start_time<$value->end_time) {
                                $rtn[$key]['course_list'][] = [
                                    'id'        => $course->id,
                                    'fullname'  => $course->full_name,
                                ];
                            } elseif ($course->start_time>$value->start_time && $course->end_time<$value->end_time && $course->end_time != 0) {
                                $rtn[$key]['course_list'][] = [
                                    'id'        => $course->id,
                                    'fullname'  => $course->full_name,
                                ];
                            }
                        } else {
                            continue;
                        }
                    }
                }
                break;
        }

        return $rtn;
    }

    public static function search_courses_returns()
    {
        return new external_multiple_structure(new external_single_structure(array(
            'semester'    => new external_value(PARAM_TEXT, 'Semester'),
            'course_list' => new external_multiple_structure(new external_single_structure(array(
                'id'        => new external_value(PARAM_INT, 'Course ID'),
                'fullname'  => new external_value(PARAM_TEXT, 'Course full name'),
            ))),
        )));
    }


    public static function network_teach_parameters()
    {
        return new external_function_parameters(array());
    }

    public static function network_teach()
    {
        global $DB, $PAGE;

        $rtn = [];

        // Check if the user has permission to the data
        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $category_id = 63;

        # Online courses
        $courses_sql = "SELECT ";
        $courses_sql .= "COUNT(*) courses,";
        $courses_sql .= "SUM(students) students,";
        $courses_sql .= "SUM(resource_num) resource_num,";
        $courses_sql .= "SUM(forums) forums,";
        $courses_sql .= "SUM(assigns) assigns,";
        $courses_sql .= "SUM(quiz) quiz ";
        $courses_sql .= "FROM {block_data_screen_course} WHERE ";
        $courses_sql .= "(category_id=$category_id OR path LIKE '/$category_id/%') AND ";
        $courses_sql .= "teacher_counts<>0 AND (activity_num>1 OR resource_num<>0)";
        $course      = $DB->get_record_sql($courses_sql);
        # Multi teacher courses
        $multi_sql = "SELECT ";
        $multi_sql .= "COUNT(*) courses ";
        $multi_sql .= "FROM {block_data_screen_course} WHERE ";
        $multi_sql .= "(category_id=$category_id OR path LIKE '/$category_id/%') AND ";
        $multi_sql .= "teacher_counts>1 AND (activity_num>1 OR resource_num<>0)";
        $multi_teacher_courses = $DB->count_records_sql($multi_sql);
        # Teachers
        $teacher_sql = "SELECT ";
        $teacher_sql .= "DISTINCT teachers ";
        $teacher_sql .= "FROM {block_data_screen_course} WHERE ";
        $teacher_sql .= "(category_id=$category_id OR path LIKE '/$category_id/%') AND ";
        $teacher_sql .= "teacher_counts<>0 AND (activity_num>1 OR resource_num<>0)";
        $teachers = $DB->get_records_sql($teacher_sql);
        $teacher_arr = [];
        foreach ($teachers as $teacher) {
            $temp = explode(',', $teacher->teachers);
            $teacher_arr = array_merge($teacher_arr, $temp);
        }

        # Total statistics
        $rtn['total_statistics'] = [
            'course_num'    => $course->courses,
            'teacher_num'   => count(array_unique($teacher_arr)),
            'student_num'   => $course->students,
            'resource_num'  => $course->resource_num,
            'forums_num'    => $course->forums,
            'assigns_num'   => $course->assigns,
            'quiz_num'      => $course->quiz,
            'multi_teacher_courses_num' => $multi_teacher_courses,
        ];

        $rtn['top_student_courses'] = $rtn['top_activity_courses'] = [];
        # Student num top 5
        $top5_student_sql       = "SELECT ";
        $top5_student_sql       .= "course_id,students,short_name,teachers ";
        $top5_student_sql       .= "FROM {block_data_screen_course} WHERE ";
        $top5_student_sql       .= "(category_id=$category_id OR path LIKE '/$category_id/%') AND ";
        $top5_student_sql       .= "teacher_counts<>0 AND (activity_num>1 OR resource_num<>0)";
        $top5_student_sql       .= "ORDER BY students DESC LIMIT 5";
        $top5_student_courses   = $DB->get_records_sql($top5_student_sql);
        foreach ($top5_student_courses as $course) {
            $teachers = $DB->get_record_sql("SELECT GROUP_CONCAT(firstname) teachers FROM {user} WHERE id IN ($course->teachers)");
            $rtn['top_student_courses'][] = [
                'course_id'     => $course->course_id,
                'student_num'   => $course->students,
                'short_name'    => $course->short_name,
                'teachers'      => $teachers->teachers,
            ];
        }
        # Activity num top 5
        $top5_activity_sql = "SELECT ";
        $top5_activity_sql .= "course_id,(activity_num+resource_num) as num,teachers,short_name ";
        $top5_activity_sql .= "FROM {block_data_screen_course} WHERE ";
        $top5_activity_sql .= "(category_id=$category_id OR path LIKE '/$category_id/%') AND ";
        $top5_activity_sql .= "teacher_counts<>0 AND (activity_num>1 OR resource_num<>0) ";
        $top5_activity_sql .= "ORDER BY num DESC LIMIT 5";
        $top5_activity_courses = $DB->get_records_sql($top5_activity_sql);
        foreach ($top5_activity_courses as $course) {
            $teachers = $DB->get_record_sql("SELECT GROUP_CONCAT(firstname) teachers FROM {user} WHERE id IN ($course->teachers)");
            $rtn['top_activity_courses'][] = [
                'course_id'     => $course->course_id,
                'short_name'    => $course->short_name,
                'teachers'      => $teachers->teachers,
                'activity_resource_num'   => $course->num,
            ];
        }

        return $rtn;
    }

    public static function network_teach_returns()
    {
        return new external_single_structure(array(
            'total_statistics' => new external_single_structure(array(
                'course_num'    => new external_value(PARAM_INT, 'Number of online courses'),
                'teacher_num'   => new external_value(PARAM_INT, 'Number of teachers'),
                'student_num'   => new external_value(PARAM_INT, 'Number of students'),
                'resource_num'  => new external_value(PARAM_INT, 'Number of resources'),
                'forums_num'    => new external_value(PARAM_INT, 'Number of forums'),
                'assigns_num'   => new external_value(PARAM_INT, 'Number of assigns'),
                'quiz_num'      => new external_value(PARAM_INT, 'Number of quizs'),
                'multi_teacher_courses_num'  => new external_value(PARAM_INT, 'Number of multi teacher courses'),
            )),
            'top_student_courses' => new external_multiple_structure(new external_single_structure(array(
                'course_id'     => new external_value(PARAM_INT, 'Course ID'),
                'student_num'   => new external_value(PARAM_INT, 'Number of students'),
                'short_name'    => new external_value(PARAM_TEXT, 'Course short name'),
                'teachers'      => new external_value(PARAM_TEXT, 'Course teachers'),
            ))),
            'top_activity_courses' => new external_multiple_structure(new external_single_structure(array(
                'course_id'     => new external_value(PARAM_INT, 'Course ID'),
                'short_name'    => new external_value(PARAM_TEXT, 'Course short name'),
                'teachers'      => new external_value(PARAM_TEXT, 'Course teachers'),
                'activity_resource_num'   => new external_value(PARAM_INT, 'Number of resources and activities'),
            ))),
            'top_activity_courses'
        ));
    }

    // cyx
    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function zoom_parameters()
    {
        return new external_function_parameters(array(
            'type'      => new external_value(PARAM_INT, 'type', VALUE_DEFAULT, 1),
            'page'      => new external_value(PARAM_INT, 'Page numebr', VALUE_DEFAULT, 1),
            'pagesize'  => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 10),
        ));
    }
    /**
     * Get teacher list
     *
     * @param $dept
     * @param $name
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function zoom($type, $page, $pagesize)
    {
        global $DB, $PAGE;

        $rtn['zoom'] = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::zoom_parameters(), array(
            'type'      => $type,
            'page'      => $page<=0 ? 1 : $page,
            'pagesize'  => $pagesize<0 ? 1 : $pagesize,
        ));

        //先获取数据统计
        $zmoodule = $DB->get_record('modules', array('name' => 'zoom'));
        if ($zmoodule) {
            $module_id = $zmoodule->id;
        } else {
            $module_id = 0;
        }
        //1.zoom总数
        $countarr = [];
        $countarr['zoom_total'] = $DB->count_records('zoom_user_meeting');
        $sql = 'SELECT count(*) FROM {zoom_user_meeting} zum WHERE zum.num > 0';
        $countarr['zoom_using'] = $DB->count_records_sql($sql);
        $countarr['zoom_free'] = $DB->count_records('zoom_user_meeting', ['num'=>0]);
        //预约未被使用数
        $sql = "SELECT count(*) FROM {zoom} z
            INNER JOIN {course_modules} cm ON z.id=cm.instance AND deletioninprogress=0 AND cm.module=".$module_id."
            WHERE (timetype =0 AND start_time>=UNIX_TIMESTAMP(NOW()) )
            OR (timetype =1)";
        $countarr['zoom_mod_order'] = $DB->count_records_sql($sql);
        //已使用
        $sql = "SELECT count(*) FROM {zoom} z
            INNER JOIN {course_modules} cm ON z.id=cm.instance AND deletioninprogress=0 AND cm.module=".$module_id."
            WHERE (timetype =0 AND start_time+duration<=UNIX_TIMESTAMP(NOW()))";
        $countarr['zoom_mod_using'] = $DB->count_records_sql($sql);

        $rtn['countarr'] = $countarr;
        return $rtn;
    }
    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function zoom_returns()
    {
        return new external_single_structure(array(
            'countarr'=> new external_single_structure(array(
                'zoom_total'  => new external_value(PARAM_INT, 'zoom_total counts '),
                'zoom_using'  => new external_value(PARAM_INT, 'zoom_using counts '),
                'zoom_free'  => new external_value(PARAM_INT, 'zoom_free counts '),
                'zoom_mod_order'  => new external_value(PARAM_INT, 'zoom_mod_order counts '),
                'zoom_mod_using'  => new external_value(PARAM_INT, 'zoom_mod_using counts '),
            ), 'count information'),
        ));
    }

    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function zoomuser_parameters()
    {
        return new external_function_parameters(array(
            'type'      => new external_value(PARAM_INT, 'type', VALUE_DEFAULT, 1),
            'page'      => new external_value(PARAM_INT, 'Page numebr', VALUE_DEFAULT, 1),
            'pagesize'  => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 10),
            'role'      => new external_value(PARAM_INT, 'User role', VALUE_DEFAULT, 5),
        ));
    }
    /**
     * Get teacher list
     *
     * @param $dept
     * @param $name
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function zoomuser($type, $page, $pagesize, $role)
    {
        global $DB, $PAGE;

        $rtn['zoom'] = [];

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::zoomuser_parameters(), array(
            'type'      => $type,
            'page'      => $page<=0 ? 1 : $page,
            'pagesize'  => $pagesize<0 ? 1 : $pagesize,
            'role'      => $role,
        ));

        $list = [];
        $counts = 0;
        if (in_array($role, [1,15])) {
            switch ($type) {
                case 1:
                    $counts = $DB->count_records('zoom_user_meeting');
                    $sql = "";
                    if ($params['pagesize']) {
                        $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                    }
                    $list = $DB->get_records_sql("SELECT * FROM {zoom_user_meeting} zum" . $sql);
                    break;
                case 2:
                    $sql = 'SELECT count(*) FROM {zoom_user_meeting} zum WHERE zum.num > 0';
                    $counts = $DB->count_records_sql($sql);
                    $sql = "";
                    if ($params['pagesize']) {
                        $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                    }
                    $list = $DB->get_records_sql("SELECT * FROM {zoom_user_meeting} zum WHERE zum.num > 0" . $sql);
                    break;
                case 3:
                    $counts = $DB->count_records('zoom_user_meeting', ['num'=>0]);
                    $sql = "";
                    if ($params['pagesize']) {
                        $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                    }
                    $list = $DB->get_records_sql("SELECT * FROM {zoom_user_meeting} zum WHERE zum.num = 0" . $sql);
                    break;
                default:
                    break;
            }
        }
        foreach ($list as $key => $row) {
            $zoom       = $DB->get_record('zoom', ['id'=>$row->zoomid]);
            $course     = $DB->get_record('course', ['id'=>$zoom->course]);
            $category   = $DB->get_record('course_categories', ['id'=>$course->category]);
            $list[$key]->join_url   = $zoom ? $zoom->join_url : '-';
            $list[$key]->start_time = $zoom ? date('H:i:s', $zoom->start_time) : '-';
            $list[$key]->category   = $category ? $category->name : '-';
        }
        $rtn['list'] = $list;
        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }
    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function zoomuser_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'  => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'  => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'list'  => new external_multiple_structure(new external_single_structure(array(
                'id'        => new external_value(PARAM_INT, 'ROOM USER MEETING ID'),
                'category'  => new external_value(PARAM_TEXT, 'Course category'),
                'num'       => new external_value(PARAM_INT, 'EMAIL'),
                'coursename'=> new external_value(PARAM_TEXT, 'EMAIL'),
                'snum'      => new external_value(PARAM_INT, 'EMAIL'),
                'uname'     => new external_value(PARAM_TEXT, 'EMAIL'),
                'join_url'  => new external_value(PARAM_TEXT, 'Join url'),
                'start_time'=> new external_value(PARAM_TEXT, 'Start time'),
            ))),
        ));
    }

    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function zoommod_parameters()
    {
        return new external_function_parameters(array(
            'type'      => new external_value(PARAM_INT, 'type', VALUE_DEFAULT, 1),
            'page'      => new external_value(PARAM_INT, 'Page numebr', VALUE_DEFAULT, 1),
            'pagesize'  => new external_value(PARAM_INT, 'Pagesize', VALUE_DEFAULT, 10),
        ));
    }
    /**
     * Get teacher list
     *
     * @param $dept
     * @param $name
     * @param $page
     * @param $pagesize
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function zoommod($type, $page, $pagesize)
    {
        global $DB, $PAGE,$CFG;
        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }

        $params = self::validate_parameters(self::zoommod_parameters(), array(
            'type'      => $type,
            'page'      => $page<=0 ? 1 : $page,
            'pagesize'  => $pagesize<0 ? 1 : $pagesize,
        ));
        $zmoodule = $DB->get_record('modules', array('name' => 'zoom'));
        if ($zmoodule) {
            $module_id = $zmoodule->id;
        } else {
            $module_id = 0;
        }
        $list = [];
        $counts = 0;
        switch ($type) {
            case 4:
                $sql = "SELECT count(*) FROM {zoom} z
                    INNER JOIN {course_modules} cm ON z.id=cm.instance AND deletioninprogress=0 AND cm.module=".$module_id."
                    WHERE (timetype =0 AND start_time>=UNIX_TIMESTAMP(NOW()) )
                    OR (timetype =1)";
                $counts = $DB->count_records_sql($sql);
                $sql = '';
                if ($params['pagesize']) {
                    $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $sql = "SELECT z.* FROM {zoom} z
                    INNER JOIN {course_modules} cm ON z.id=cm.instance AND deletioninprogress=0 AND cm.module=".$module_id."
                    WHERE (timetype =0 AND start_time>=UNIX_TIMESTAMP(NOW()) )
                    OR (timetype =1) ORDER BY z.start_time ASC" . $sql;
                $list = $DB->get_records_sql($sql);
                break;
            case 5:
                $sql = "SELECT count(*) FROM {zoom} z
                    INNER JOIN {course_modules} cm ON z.id=cm.instance AND deletioninprogress=0 AND cm.module=".$module_id."
                    WHERE (timetype =0 AND start_time+duration<=UNIX_TIMESTAMP(NOW()))";
                $counts = $DB->count_records_sql($sql);
                $sql = '';
                if ($params['pagesize']) {
                    $sql = " LIMIT " . (($params['page']-1)*$params['pagesize']) . "," . $params['pagesize'];
                }
                $sql = "SELECT z.* FROM {zoom} z
                    INNER JOIN {course_modules} cm ON z.id=cm.instance AND deletioninprogress=0 AND cm.module=".$module_id."
                    WHERE (timetype =0 AND start_time+duration<=UNIX_TIMESTAMP(NOW())) ORDER BY z.start_time DESC" . $sql;
                $list = $DB->get_records_sql($sql);
                break;
            default:
                break;
        }
        if (!empty($list)) {
            require_once($CFG->dirroot.'/user/lib.php');
            $cids = array_filter(array_unique(array_column($list, 'course')));
            $course_list = [];
            if (count($cids)>0) {
                $course_list = $DB->get_records_sql("SELECT * FROM {block_data_screen_course} WHERE course_id in( ".implode(',', $cids)." )");
            }

            $clist = [];
            foreach ($course_list as $val) {
                $clist[$val->course_id] = $val;
            }
            foreach ($list as $key=> $val) {
                $one = [];
                $one['id']=$val->id;
                $one['zoomname']=$val->name;
                $one['course']=$val->course;
                if (isset($clist[$val->course])) {
                    $one['coursename']= $clist[$val->course]->short_name;
                    $teacher = $DB->get_record_sql('SELECT GROUP_CONCAT(firstname) teacher FROM {user} WHERE id IN ('. $clist[$val->course]->teachers . ')')->teacher;
                    $one['teachers']=$teacher;
                } else {
                    $one['coursename']= '';
                    $one['teachers']='';
                }

                //$one['snum'] = 0;
                $one['snum'] = user_get_total_participants($val->course, 0, 0, 5);
                $one['start_time']=date('Y-m-d H:i:s', $val->start_time);
                $one['duration']=$val->duration;

                if ($val->timetype==1) {
                    //其他循环
                    $one['status'] = '预约中';
                    $one['timetype']='每七天循环';
                } else {
                    $one['timetype']='一次性';
                    if ($val->start_time + $val->duration <time()) {
                        $one['status'] = '已使用';
                    } else {
                        $one['status'] = '预约中';
                    }
                }

                $list[$key] = $one;
            }
        }
        $rtn['counts'] = $type;
        $rtn['list'] = $list;
        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];

        return $rtn;
    }
    /**
     * Return description of method result value
     *
     * @return external_single_structure
     */
    public static function zoommod_returns()
    {
        return new external_single_structure(array(
            'page' => new external_single_structure(array(
                'max_page'  => new external_value(PARAM_INT, 'The max page number'),
                'cur_page'  => new external_value(PARAM_INT, 'The current page number'),
            ), 'Page information'),
            'counts'=>new external_value(PARAM_INT, 'course ID'),
            'list'  => new external_multiple_structure(new external_single_structure(array(
                'id'    => new external_value(PARAM_INT, 'zoommod ID'),
                'zoomname'    => new external_value(PARAM_TEXT, 'zoom name'),
                'course'    => new external_value(PARAM_INT, 'course ID'),
                'coursename'    => new external_value(PARAM_TEXT, 'course name'),
                'teachers'    => new external_value(PARAM_TEXT, 'teachers'),
                'snum'    => new external_value(PARAM_INT, 'the number od student'),
                'start_time'    => new external_value(PARAM_TEXT, 'the date od start'),
                'duration'    => new external_value(PARAM_INT, 'duration'),
                'timetype'    => new external_value(PARAM_TEXT, 'timetype'),
                'status'    => new external_value(PARAM_TEXT, 'status'),
            ))),
        ));
    }


    //by cyxuan 20200303
    /**
     * Return description of method parameters
     *
     * @return external_function_parameters
     */
    public static function active_7days_parameters()
    {
        return new external_function_parameters(array(
            'date' => new external_value(PARAM_TEXT, 'Target date', VALUE_DEFAULT, '')
        ));
    }

    /**
     * Data of the line chart
     *
     * @param $date
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function active_7days($date)
    {
        global $DB, $PAGE,$CFG;

        $rtn = [];

        $context    = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::active_7days_parameters(), array('date'=>$date));


        $endtime = strtotime($date)-1;
        $starttime = strtotime($date) - 86400*7;
        $datas = $DB->get_records_sql("SELECT * FROM {block_data_screen_active} WHERE `date`>=".$starttime." AND `date`<=".$endtime." ORDER BY date");
        $list = [];
        if ($datas) {
            require_once($CFG->dirroot . '/blocks/data_screen/lib.php');
            foreach ($datas as $val) {
                $mydate = date('Y-m-d', $val->date);
                $diff = diff_date($date, $mydate);
                $list[$diff] = ['courses'=>$val->courses,'students'=>$val->students,'date'=>$mydate];
            }
        }

        for ($i=1;$i<7;$i++) {
            if (!isset($list[$i])) {
                $mydate = date('Y-m-d', strtotime($date) - 86400*$i);
                $list[$i] = ['courses'=>0,'students'=>0,'date'=>$mydate];
            }
        }
        krsort($list);
        $list = array_values($list);
        $rtn['list'] = $list;
        print_r($rtn);die;
        return $rtn;
    }

    /**
     * Return description of method result value
     *
     * @return
     */
    public static function active_7days_returns()
    {
        return new external_single_structure(array(
            'list'  => new external_multiple_structure(new external_single_structure(array(
                'courses'    => new external_value(PARAM_INT, 'courses'),
                'students'    => new external_value(PARAM_INT, 'students'),
                'date'    => new external_value(PARAM_TEXT, 'date'),
            ))),
        ));
    }


    public static function day_active_parameters()
    {
        return new external_function_parameters(array(
                        'page' => new external_value(PARAM_INT, 'Target page number', VALUE_DEFAULT, 1) ,
                        'pagesize' => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT, 10) ,
                        'date' => new external_value(PARAM_INT, 'Date', VALUE_DEFAULT, 10) ,
                    ));
    }


    public static function day_active($page, $pagesize, $date)
    {
        global $DB, $PAGE, $CFG;
        require_once($CFG->dirroot . '/blocks/data_screen/lib.php');

        // Check if the user has permission to the data

        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/data_screen:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::day_active_parameters(), array(
                        'page' => $page <= 0 ? 1 : $page,
                        'pagesize' => $pagesize < 0 ? 1 : $pagesize,
                        'date' => $date,
                    ));
        $starttime = $date - 1;
        $endtime = $date + 86399;
        $no_teacher = get_string('no_teacher', 'block_data_screen');

        if ($params['pagesize']) {
            $param = " LIMIT " . (($params['page'] - 1) * $params['pagesize']+1) . ',' . $params['pagesize'];
        }

        $counts = $DB->count_records_sql("SELECT COUNT(DISTINCT courseid) FROM {logstore_standard_log}
                                            WHERE timecreated>".$starttime." AND timecreated<".$endtime." AND crud='r'
                                            AND contextlevel=50 AND courseid<>1 ");

        $courses = $DB->get_records_sql("SELECT DISTINCT(courseid),userid FROM {logstore_standard_log}
                                        WHERE timecreated>".$starttime." AND timecreated<".$endtime." AND crud='r'
                                        AND contextlevel=50 AND courseid<>1 ORDER BY courseid $param");
        $rtn = ['data' => []];

        foreach ($courses as $c) {
            $categories  =$DB->get_records_sql("SELECT c.id,cc.name FROM {course}  c
                                                JOIN {course_categories} cc ON cc.id = c.category
                                                WHERE c.id =  $c->courseid");

            $tea_cn = get_course_teacher($c->courseid);
            $stu_counts = get_course_stu($c->courseid);
            $real_stu_counts = get_course_realstu($starttime, $endtime, $c->courseid);

            $cid =  $tea_cn[$c->courseid]->id;

            if ($tea_cn[$cid]->teachers== null) {
                $tea_cn[$cid]->teachers = $no_teacher;
            }
            $rtn['data'][] =
                                [
                                    'id'        =>$c->courseid,
                                    'category' => $categories[$cid]->name,
                                    'fullname' => $tea_cn[$cid]->fullname,
                                    'teachername' =>$tea_cn[$cid]->teachers,
                                    'students' => $stu_counts,
                                    'real_stu' => $real_stu_counts,
                                ];
        }
        $rtn['page']['max_page'] = $counts && $params['pagesize'] ? ceil($counts / $params['pagesize']) : 1;
        $rtn['page']['cur_page'] = $params['page'];
        return $rtn;
    }

    public static function day_active_returns()
    {
        return new external_single_structure(array(
                        'page' => new external_single_structure(array(
                            'max_page' => new external_value(PARAM_INT, 'The max page number') ,
                            'cur_page' => new external_value(PARAM_INT, 'The current page number') ,
                        ), 'Page information') ,
                        'data' => new external_multiple_structure(new external_single_structure(array(
                            'id' => new external_value(PARAM_INT, 'courseid') ,
                            'category' => new external_value(PARAM_TEXT, 'category') ,
                            'fullname' => new external_value(PARAM_TEXT, 'Course full name') ,
                            'teachername' => new external_value(PARAM_TEXT, 'Course teacher') ,
                            'students' => new external_value(PARAM_TEXT, 'Course total students') ,
                            'real_stu' => new external_value(PARAM_TEXT, 'Course real students') ,
                        ))) ,
                    ));
    }
}
