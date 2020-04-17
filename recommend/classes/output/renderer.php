<?php

namespace block_data_screen\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

class renderer extends plugin_renderer_base{
    function render_semester($semester)
    {
        return $this->render_from_template('block_data_screen/semester', $semester->export_for_template($this));
    }
}
