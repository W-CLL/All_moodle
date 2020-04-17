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
 * header file
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
global $DB, $USER, $PAGE, $CFG;

if (!isloggedin() || isguestuser()) {
    redirect(get_login_url());
}

$context    = \context_system::instance();
$PAGE->set_context($context);
// get user avatar
$userpicture        = new \user_picture($USER);
$userpicture->size  = 1; // Size f1.
$avatar = $userpicture->get_url($PAGE)->out(false);
$url = new \moodle_url('/');

// Check access permissions.
$systemcontext = context_system::instance();
// Get some basic data we are going to need.
$roles = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);
$default_role = 0;
if (is_siteadmin()) {
    foreach ($roles as $value) {
        if ($default_role) {
            $default_role = $default_role > $value->sortorder ? $value->sortorder : $default_role;
        } else {
            $default_role = $value->sortorder;
        }
        $role[] = [
            'id' => $value->id,
            'name' => $value->localname,
            'sort' => $value->sortorder
        ];
    }
} else {
    $role_arr = $DB->get_records_sql('SELECT DISTINCT roleid,userid FROM {role_assignments} WHERE userid=?', [$USER->id]);
    foreach ($role_arr as $value) {
        foreach ($roles as $val) {
            if ($value->roleid==$val->id) {
                if ($default_role) {
                    $default_role = $default_role > $val->sortorder ? $val->sortorder : $default_role;
                } else {
                    $default_role = $val->sortorder;
                }
                $role[] = [
                    'id' => $val->id,
                    'name' => $val->localname,
                    'sort' => $val->sortorder
                ];
            }
        }
    }
}
$sort = array_column($role, 'id');
array_multisort($sort, SORT_ASC, $role);
if (isset($_SESSION['block_data_screen_role_sort'])) {
    $default_role = $_SESSION['block_data_screen_role_sort'];
}

$mod_hvp    = $DB->get_record('modules', ['name'=>'hvp']);
$display    = $CFG->block_data_screen_display;
$zoom       = $CFG->block_data_screen_zoom;
$network_teach = $CFG->block_data_screen_netwok_teach;
$copyright  = $CFG->block_data_screen_copyright;
$site       = get_site();
$site_page  = ['platform_overview.php','attribute_course.php','active_7days.php','invalid_course.php', 'access_analysis.php', 'college_list.php', 'teacher_list.php', 'college_detail.php', 'teacher_detail.php', 'day_active.php'];
$course_page = ['course_detail.php','activity_analysis.php','person_list.php','person_detail.php', 'course_teachinfo.php'];
$zoom_page  = ['zoom.php'];
$network_page = ['one.php', 'two.php'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Preview page of Metronic Admin Theme #4 for statistics, charts, recent events and reports"
          name="description" />
    <meta content="" name="author" />
    <link href="amd/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="amd/layouts/layout4/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/layouts/layout4/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="amd/layouts/layout4/css/custom.min.css" rel="stylesheet" type="text/css" />
</head>