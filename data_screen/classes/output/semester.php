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
 *  Semester list
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_data_screen\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class semester implements renderable, templatable
{
    public $page;
    public $pagesize;

    public function __construct($page, $pagesize)
    {
        $this->page = $page;
        $this->pagesize = $pagesize;
    }

    public function export_for_template(renderer_base $output)
    {
        global $DB, $OUTPUT;
        
        $counts = $DB->count_records('block_data_screen_semester');
        $baseurl = new \moodle_url('/blocks/data_screen/semester.php');
        $page = ($this->page - 1) * $this->pagesize;

        $semesters  = $DB->get_records_sql("SELECT * FROM {block_data_screen_semester}", [], $page, $this->pagesize);
        $semester   = [0=>get_string('up', 'block_data_screen'), 1=>get_string('down', 'block_data_screen')];

        $semester_list = [];
        $edit = new \moodle_url('/blocks/data_screen/semester_edit.php');
        $delete = new \moodle_url('/blocks/data_screen/operation.php');
        foreach ($semesters as $value) {
            $edit->params(['id'=>$value->id]);
            $delete->params(['action'=>'delete', 'id'=>$value->id]);
            $semester_list[] = [
                'id'        => $value->id,
                'year'      => $value->year,
                'semester'  => $semester[$value->semester],
                'edit'      => $edit->out(),
                'delete'    => $delete->out()
            ];
        }

        $data = new \stdClass;
        $data->semester_list = $semester_list;
        $data->page = $OUTPUT->paging_bar($counts, $this->page, $this->pagesize, $baseurl);
        $data->add = new \moodle_url('/blocks/data_screen/semester_edit.php');

        return $data;
    }
}
