<?php 

require_once("../../config.php");
require_once($CFG->dirroot . '/blocks/recommend/lib.php');

// re_flushStatisticsCourse();

// $re = $DB->get_records_sql("SELECT * FROM {block_recommend_course}");
// print_r($re);

// $re1 = $DB->get_records_sql("SELECT * FROM {block_recommend_college}");
// print_r($re1);
// global $CFG, $DB;

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

$active = $DB->get_records_sql('SELECT * FROM {block_recommend_visit} rv
WHERE rv.type IN  ('.$rtn['activity'].')
AND rv.course_id= 2 ORDER BY access_num DESC LIMIT 0,5');

$resource = $DB->get_records_sql('SELECT * FROM {block_recommend_visit} rv
WHERE rv.type IN  ('.$rtn['resource'].')
AND rv.course_id= 2 ORDER BY access_num DESC LIMIT 0,5');




// print_r($views);
// echo "</br></br>";

echo "活动";
print_r($active);
echo "</br></br>";
echo "资源";
print_r($resource);
echo "</br></br>";
// print_r($rtn);

$sql = "SELECT contextinstanceid as cmid, COUNT('x') AS numviews, COUNT(DISTINCT userid) AS distinctuser
FROM mdl_logstore_standard_log 
WHERE courseid = 2
AND crud = 'r'
AND contextlevel = 70
GROUP BY contextinstanceid
ORDER BY numviews";
$views = $DB->get_records_sql($sql);

$id = 2;
$allModule = get_fast_modinfo($id)->get_cms();
$a = get_fast_modinfo($id)->get_cms();
// print_r($a);
$active_tal =[];
$resource_tal =[];

// foreach($views as $v){
//     foreach ($allModule as $value) {
//         foreach ($active as $ac) {
//             if ($ac['id']==$value->id) {
//                 $active_tal[$value->id]    = [
//                 'section'   =>$value->section,
//                 'module'    =>$value->module,
//                 'name'      =>$value->get_section_info()->name ?: $value->get_section_info()->section,
//                 'instance'  =>$value->instance,
//                 'mod_name'   => $value->modname,
//                 'instance_name' => $value->name,
//                 ];
//             }
//         }
//         foreach ($resource as $re) {
//             if ($re['id']==$value->id) {
//                 $resource_tal[$value->id]    = [
//                 'section'   =>$value->section,
//                 'module'    =>$value->module,
//                 'name'      =>$value->get_section_info()->name ?: $value->get_section_info()->section,
//                 'instance'  =>$value->instance,
//                 'mod_name'   => $value->modname,
//                 'instance_name' => $value->name,
//                 ];
//             }
//         }
//     }
// }
// // print_r($mod_arr);

