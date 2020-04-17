<?php

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");

class block_recommend_external extends external_api
{
    public static function Inclass_recommend_parameters()
    {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Date', VALUE_DEFAULT, 10) ,
            'userid' => new external_value(PARAM_INT, 'Date', VALUE_DEFAULT, 10) ,
        ));
    }

    public static function Inclass_recommend($id,$userid)
    {
        global $DB, $PAGE,$CFG;
        $rtn = [];
        // Check if the user has permission to the data
        $context = \context_system::instance();
        $PAGE->set_context($context);
        if (!has_capability('block/recommend:statistics', $context)) {
            throw new Exception('Permission denied');
        }
        $params = self::validate_parameters(self::Inclass_recommend_parameters(), array(
            'id' =>$id,
            'userid' =>$userid,
        ));
        require_once("$CFG->dirroot/blocks/recommend/lib.php");
        $mod = re_get_mod_id();
        
        $active = $DB->get_records_sql('SELECT * FROM {block_recommend_visit} rv
                                        JOIN {course_modules} cm ON rv.instance_id = cm.id
                                        WHERE rv.type IN  ('.$mod['activity'].')
                                        AND rv.course_id='.$id.' AND rv.user_id ='.$userid.' ORDER BY access_num DESC LIMIT 0,5');

        $resource = $DB->get_records_sql('SELECT * FROM {block_recommend_visit} rv
                                            JOIN {course_modules} cm ON rv.instance_id = cm.id
                                            WHERE rv.type IN  ('.$mod['resource'].')
                                            AND rv.course_id='.$id.' AND rv.user_id='.$userid.' ORDER BY access_num DESC LIMIT 0,5');
        //活动
        foreach($active as $key=>$ac){

            if($active[$key]->completion == 0){
                $completion = "未结束";
            }else
                $completion = "已结束";

            $active_t[$key] = [
                'name'=>$active[$key]->instance_name,
                'access_num'=>$active[$key]->access_num,
                'mod_name' =>$active[$key]->mod_name,
                'instance_id' =>$active[$key]->instance_id,
                'completion' => $completion,
            ];
        }
        
        //资源
        foreach($resource as $key=>$re){

            if($resource[$key]->completion == 0){
                $completion = "未结束";
            }else
                $completion = "已结束";
            $resource_t[$key] = [
                'name'=>$resource[$key]->instance_name,
                'access_num'=>$resource[$key]->access_num,
                'mod_name' =>$resource[$key]->mod_name,
                'instance_id' =>$resource[$key]->instance_id,
                'completion' => $completion,
            ];
        }

        $cid = $params['id'];
        $counts = $DB->get_records_sql("SELECT * FROM {block_recommend_course} WHERE course_id= $cid");

        $c= 1;
        $rtn=
        [
            'assign_num' =>$counts[$c]->assigns,
            'quiz_num' =>$counts[$c]->quiz,
            'forum_num' =>$counts[$c]->assigns,
            'totalactive_num' =>$counts[$c]->activity_num,
            'totalresource_num' =>$counts[$c]->resource_num,
        ];
        $rtn['resource'] = $resource_t;
        //print_r($rtn['resource']);
        $rtn['active'] = $active_t; 
        return $rtn;
    }
    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function Inclass_recommend_returns()
    {
        return new external_single_structure(
            array(
            
                'assign_num' => new external_value(PARAM_INT, 'Assign counts') ,
                'quiz_num' => new external_value(PARAM_INT, 'Quiz counts') ,
                'forum_num' => new external_value(PARAM_INT, 'Forums counts') ,
                'totalactive_num' => new external_value(PARAM_INT, 'Total active counts') ,
                'totalresource_num' => new external_value(PARAM_INT, 'Total resource counts'),
            'active' => new external_multiple_structure(new external_single_structure(array(
                'access_num' => new external_value(PARAM_TEXT, 'Data of access') ,
                'name' => new external_value(PARAM_TEXT, 'Instance name') ,
                'mod_name' => new external_value(PARAM_TEXT, 'Mod name') ,
                'instance_id' => new external_value(PARAM_TEXT, 'Instance id') ,
                'completion' => new external_value(PARAM_TEXT, 'Completion of modules') ,
            ))) ,
            'resource' => new external_multiple_structure(new external_single_structure(array(
                'access_num' => new external_value(PARAM_TEXT, 'Data of access') ,
                'name' => new external_value(PARAM_TEXT, 'Instance  name') ,
                'mod_name' => new external_value(PARAM_TEXT, 'Mod name') ,
                'instance_id' => new external_value(PARAM_TEXT, 'Instance id') ,
                'completion' => new external_value(PARAM_TEXT, 'Completion of modules') ,
            ))) ,
        ));
    }

    public static function get_activities_completion_status_parameters()
    {
        return new external_function_parameters(array(
            'courseid' => new external_value(PARAM_INT, 'Course ID') ,
            'userid' => new external_value(PARAM_INT, 'User ID') ,
        ));
    }
 

    public static function get_activities_completion_status($courseid, $userid)
    {
        global $CFG, $USER, $PAGE;
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->dirroot . '/lib/completionlib.php');
        $warnings = array();
        $arrayparams = array(
            'courseid' => $courseid,
            'userid' => $userid,
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

        //的防守高手
        $results = array();
        foreach ($activities as $activity) {
            // Check if current user has visibility on this activity.
            if (!$activity->uservisible) {
                continue;
            }
            // Get progress information and state (we must use get_data because it works for all user roles in course).
            $activitycompletiondata = $completion->get_data($activity, true, $user->id);
            $results[] = array(
                'cmid' => $activity->id,
                'modname' => $activity->modname,
                'name'  => $activity->name,
                'instance' => $activity->instance,
                'state' => $activitycompletiondata->completionstate,
                'timecompleted' => $activitycompletiondata->timemodified,
                'tracking' => $activity->completion,
                'overrideby' => $activitycompletiondata->overrideby
            );
        }
        $results = array(
            'statuses' => $results,
            'warnings' => $warnings
        );
        return $results;
    }

    public static function get_activities_completion_status_returns()
    {
        return new external_single_structure(array(
            'statuses' => new external_multiple_structure(new external_single_structure(array(
                'cmid' => new external_value(PARAM_INT, 'comment ID') ,
                'modname' => new external_value(PARAM_PLUGIN, 'activity module name') ,
                'name' => new external_value(PARAM_PLUGIN, 'module name') ,
                'instance' => new external_value(PARAM_INT, 'instance ID') ,
                'state' => new external_value(PARAM_INT, 'completion state value:
                                                            0 means incomplete, 1 complete,
                                                            2 complete pass, 3 complete fail') ,
                'timecompleted' => new external_value(PARAM_INT, 'timestamp for completed activity') ,
                'tracking' => new external_value(PARAM_INT, 'type of tracking:
                                                            0 means none, 1 manual, 2 automatic') ,
                'overrideby' => new external_value(PARAM_INT, 'The user id who has overriden the status, or null', VALUE_OPTIONAL) ,
            ), 'Activity'), 'List of activities status') ,
            'warnings' => new external_warnings()
        ));
    }
}
