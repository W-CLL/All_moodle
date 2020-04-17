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
 * Edit semester form.
 *
 * @package     block_data_screen
 * @copyright   2019 ckf <m15220982078@163.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class block_data_screen_semester_form extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;

        $mform->addElement('text', 'year', get_string('year', 'block_data_screen'), ['id'=>'year']);
        $mform->setType('year', PARAM_TEXT);
        $mform->addHelpButton('year', 'year_example', 'block_data_screen');

        $mform->addElement('select', 'semester', get_string('semester', 'block_data_screen'), [
            0 => get_string('up', 'block_data_screen'),
            1 => get_string('down', 'block_data_screen'),
        ],['id'=>'semester']);
        $mform->setType('type', PARAM_INT);

        $mform->addElement('date_time_selector', 'start_time', get_string('start_time', 'block_data_screen'));
        $mform->addElement('date_time_selector', 'end_time', get_string('end_time', 'block_data_screen'));

        $this->add_action_buttons(true, get_string('Submit', 'block_data_screen'));
    }
}