<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/recommend/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/filelib.php');
// error_reporting(E_ALL ^ E_NOTICE);
class block_recommend extends block_list {
    function init() {
        $this->title = get_string('recommend', 'block_recommend');
    }
    function has_config() {
        return true;
    }
    function get_content() {

        global $CFG, $USER, $DB, $OUTPUT, $PAGE;
        if ($this->content !== NULL) {
            return $this->content;
        }

        $course = $this->page->course;
      
        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $course = $this->page->course;
        if ($course->id == 1) {
            $this->content->items[] =  '你好';
            // html_writer::tag('a', $course->fullname, array('href'=>new \moodle_url('/blocks/data_screen/course_detail.php', ['id'=>$course->id])));
        } else {
            $this->content->items[] = html_writer::tag('a', get_string('recommend', 'block_recommend'), array('href' => new moodle_url('/blocks/recommend/recommend_center.php',array('id'=>$course->id,'userid'=>$USER->id))));
        }

        return $this->content;
    }
}
