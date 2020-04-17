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
 *  Page of semester edit
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('block_data_screen_semester_form.php');

if (!isloggedin() || isguestuser()) {
    redirect(get_login_url());
}

$id = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/blocks/data_screen/semester_edit.php', ['id'=>$id]);
$context = context_system::instance();

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add(get_string('site', 'block_data_screen'), new \moodle_url('/admin/search.php'));
$PAGE->navbar->add(get_string('plugin', 'block_data_screen'), new \moodle_url('/admin/category.php', ['category'=>'modules']));
$PAGE->navbar->add(get_string('block', 'block_data_screen'), new \moodle_url('/admin/category.php', ['category'=>'blocksettings']));
$PAGE->navbar->add(get_string('pluginname', 'block_data_screen'), new \moodle_url('/blocks/data_screen/semester.php'));
$PAGE->navbar->add(get_string('semester_setting', 'block_data_screen'), $url);

$mform = new block_data_screen_semester_form($url);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/data_screen/semester.php'));
    exit;
} elseif ($data = $mform->get_data()) {
    if ($id) {
        $semester               = $DB->get_record('block_data_screen_semester', ['id'=>$id]);
        $semester->semester     = $data->semester;
        $semester->year         = $data->year;
        $semester->start_time   = $data->start_time;
        $semester->end_time     = $data->end_time;

        $result = $DB->update_record('block_data_screen_semester', $semester);
    } else {
        $semester               = new \stdClass();
        $semester->year         = $data->year;
        $semester->semester     = $data->semester;
        $semester->start_time   = $data->start_time;
        $semester->end_time     = $data->end_time;

        $result = $DB->insert_record("block_data_screen_semester", $semester);
    }
    if ($result) {
        redirect(new moodle_url('/blocks/data_screen/semester.php'));
        exit;
    } else {
        notice(get_string('action_failed', 'block_data_screen'), new moodle_url('/blocks/data_screen/semester.php'));
        exit;
    }
} else {
    if ($id) {
        $semester = $DB->get_record('block_data_screen_semester', ['id'=>$id]);
        $mform->set_data($semester);
    }
}

echo $OUTPUT->header();
echo $mform->render();
echo $OUTPUT->footer();