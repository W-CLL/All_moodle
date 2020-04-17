<?php

namespace block_recommend\task;

use core\task\scheduled_task;

class cron_task_day extends scheduled_task{

    function get_name()
    {
        return get_string('re_crontask','block_recommend');
    }

    /**
     * @throws \coding_exception
     * @throws \dml_exception
     */
    function execute()
    {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/recommend/lib.php');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        re_OneDay();
    }

}