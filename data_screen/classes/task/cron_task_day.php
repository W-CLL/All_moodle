<?php

namespace block_data_screen\task;

use core\task\scheduled_task;

class cron_task_day extends scheduled_task{

    function get_name()
    {
        return get_string('crontask','block_data_screen');
    }

    /**
     * @throws \coding_exception
     * @throws \dml_exception
     */
    function execute()
    {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/data_screen/lib.php');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        OneDay();
    }

}