<?php

class block_data_screen extends block_list{

    function init(){
        $this->title = get_string('pluginname','block_data_screen');
    }

    function has_config(){
        return true;
    }

    function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->items  = array();
        $this->content->icons  = array();

        $course = $this->page->course;
        if ($course->id != 1) {
            $this->content->items[] = html_writer::tag('a', $course->fullname, array('href'=>new \moodle_url('/blocks/data_screen/course_detail.php', ['id'=>$course->id])));
        } else {
            $this->content->items[] = html_writer::tag('a', get_string('title','block_data_screen'), array('href' => new moodle_url('/blocks/data_screen/platform_overview.php')));
        }
		
        return $this->content;
    }
}