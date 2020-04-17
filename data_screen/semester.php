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
 * Semester list
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
global $DB, $OUTPUT, $PAGE;

$page       = optional_param('page', 1, PARAM_INT);
$pagesize       = optional_param('pagesize', 10, PARAM_INT);

if (!isloggedin() || isguestuser()) {
    redirect(get_login_url());
}

$url        = new \moodle_url('/blocks/data_screen/semester.php');
$context    = \context_system::instance();
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('site', 'block_data_screen'), '/admin/search.php');
$PAGE->navbar->add(get_string('plugin', 'block_data_screen'), '/admin/category.php?category=modules');
$PAGE->navbar->add(get_string('block', 'block_data_screen'), '/admin/category.php?category=blocksettings');
$PAGE->navbar->add(get_string('pluginname', 'block_data_screen'), '/blocks/data_screen/platform_overview.php');
$PAGE->navbar->add(get_string('semester', 'block_data_screen'), $url);

$renderable = new \block_data_screen\output\semester($page, $pagesize);
$renderer   = $PAGE->get_renderer('block_data_screen');

echo $OUTPUT->header();
echo $renderer->render($renderable);
echo $OUTPUT->footer();
