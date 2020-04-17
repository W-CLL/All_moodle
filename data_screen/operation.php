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
 * Render add/edit pages, or perform actions.
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
global $DB, $OUTPUT, $PAGE;

if (!isloggedin() || isguestuser()) {
    redirect(get_login_url());
}

$action     = optional_param('action', '', PARAM_TEXT);
$id         = optional_param('id', 0, PARAM_INT);

$context    = \context_system::instance();
$PAGE->set_context($context);

switch ($action) {
    case 'delete':
        $url = new \moodle_url('/blocks/data_screen/operation.php', ['action'=>'delPost', 'id'=>$id]);
        notice(get_string('confirm', 'block_data_screen'), $url);
        break;

    case 'delPost':
        $DB->delete_records("block_data_screen_semester", ['id'=>$id]);

        $url = new \moodle_url('/blocks/data_screen/semester.php');
        redirect($url);
        break;
    default:
        $url    = new \moodle_url('/blocks/data_screen/semester.php');
        redirect($url);
}
