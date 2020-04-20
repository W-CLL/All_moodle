<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * The main zoom configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/zoom/lib.php');
require_once($CFG->dirroot.'/mod/zoom/locallib.php');

/**
 * Module instance settings form
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_zoom_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $PAGE, $USER,$DB;
        $config = get_config('mod_zoom');
        $isnew = empty($this->_cm);

        /*if($isnew){
            $zmoodule = $DB->get_record('modules', array('name' => 'zoom'));
            if(!$zmoodule){
                return false;
            }
            $module_id = $zmoodule->id;
            $cid = $this->current->course;

            $sql = 'SELECT COUNT(*) as  num
          FROM {course_modules} cm
     LEFT JOIN {zoom} z ON cm.instance = z.id
         WHERE cm.module=?
               AND cm.course = ?
               AND cm.deletioninprogress = 0
               ORDER BY cm.added desc';
            $params = array($module_id,$cid);
            $count = $DB->count_records_sql($sql, $params);
            if($count>=1){
                notice('提示：每门课程只允许添加一个ZOOM课室',new moodle_url('/course/view.php', array('id' => $this->current->course)));
            }
        }*/
        /*$service = new mod_zoom_webservice();
        //创建时判断是否有多个zoom， 提示：每门课程只允许添加一个ZOOM课室
        $is_create = 0;
        if($isnew){
            $zmoodule = $DB->get_record('modules', array('name' => 'zoom'));
            if(!$zmoodule){
                return false;
            }
            $module_id = $zmoodule->id;
            $cid = $this->current->course;

            $sql = 'SELECT COUNT(*) as  num
          FROM {course_modules} cm
     LEFT JOIN {zoom} z ON cm.instance = z.id
         WHERE cm.module=?
               AND cm.course = ?
               AND cm.deletioninprogress = 0
               ORDER BY cm.added desc';
            $params = array($module_id,$cid);
            $count = $DB->count_records_sql($sql, $params);
            if($count>=1){
                notice('提示：每门课程只允许添加一个ZOOM课室',new moodle_url('/course/view.php', array('id' => $this->current->course)));
            }

            $is_create = 1;
        }else{
            $response = $service->get_meeting_webinar_info(1, $this->current->webinar,false);
            if(!$response){
                $is_create = 1;
            }
        }
        if ($is_create){
            //$email = $USER->email;//创建活动资源时固定email
            $email = zoom_get_usable_email($this->current->course,false);
        }else{
            //if($this->current->host_id)
            $email = zoom_get_exit_host($this->current->course,$this->current->host_id);
        }

        if(!$email){
            //当前没有可用邮箱
            notice('提示：当期无可用会议室，请等候！',new moodle_url('/course/view.php', array('id' => $this->current->course)));
        }
        $zoomuser = $service->get_user($email);
        if ($zoomuser === false) {
            // Assume user is using Zoom for the first time.
            $errstring = 'zoomerr_usernotfound';
            // After they set up their account, the user should continue to the page they were on.
            $nexturl = $PAGE->url;
            throw new moodle_exception($errstring, 'mod_zoom', $nexturl, $config->zoomurl);
        }*/

        // If updating, ensure we can get the meeting on Zoom.
        //编辑zoom，找不到会议室则重新生成
        /*if (!$isnew) {
            try {
                $service->get_meeting_webinar_info($this->current->meeting_id, $this->current->webinar);
            } catch (moodle_exception $error) {
                // If the meeting can't be found, offer to recreate the meeting on Zoom.
                if (zoom_is_meeting_gone_error($error)) {
                    $errstring = 'zoomerr_meetingnotfound';
                    $param = zoom_meetingnotfound_param($this->_cm->id);
                    $nexturl = "/mod/zoom/view.php?id=" . $this->_cm->id;
                    throw new moodle_exception($errstring, 'mod_zoom', $nexturl, $param, "meeting/get : $error");
                } else {
                    throw $error;
                }
            }
        }*/

        // Start of form definition.
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Add topic (stored in database as 'name').
        $mform->addElement('text', 'name', get_string('topic', 'zoom'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 300), 'maxlength', 300, 'client');

        // Add description ('intro' and 'introformat').
        $this->standard_intro_elements();


        //type of time
        /*$timetypeoptions = array();
        $timetypeoptions[0] = '一次性';
        $timetypeoptions[1] = '周循环';
        $mform->addElement('select', 'timetype', '时间周期类型', $timetypeoptions);
        $mform->setDefault('timetype', '0');*/



        //0时间日期选择
        // Add date/time. Validation in validation().
        $mform->addElement('date_time_selector', 'start_time', get_string('start_time', 'zoom'));
        // Disable for recurring meetings.
        $mform->disabledIf('start_time', 'recurring', 'checked');

        /*//1周期选择
        $timetypeoptions = array();
        $timetypeoptions[1] = '星期天';
        $timetypeoptions[2] = '星期一';
        $timetypeoptions[3] = '星期二';
        $timetypeoptions[4] = '星期三';
        $timetypeoptions[5] = '星期四';
        $timetypeoptions[6] = '星期五';
        $timetypeoptions[7] = '星期六';
        $mform->addElement('select', 'week', '发生于', $timetypeoptions);
        $mform->setDefault('week', '0');*/

        // Add duration.
        $mform->addElement('duration', 'duration', get_string('duration', 'zoom'), array('optional' => false));
        // Validation in validation(). Default to one hour.
        $mform->setDefault('duration', array('number' => 1, 'timeunit' => 3600));
        // Disable for recurring meetings.
        $mform->disabledIf('duration', 'recurring', 'checked');

        $timetypeoptions = array();
        $timetypeoptions[0] = '一次性';
        $timetypeoptions[1] = '每七天循环';
        $mform->addElement('select', 'timetype', '时间周期类型', $timetypeoptions);
        $mform->setDefault('timetype', '0');

        // Add recurring.
        /*$mform->addElement('advcheckbox', 'recurring', get_string('recurringmeeting', 'zoom'));
        $mform->setDefault('recurring', $config->defaultrecurring);
        $mform->addHelpButton('recurring', 'recurringmeeting', 'zoom');*/
        $mform->addElement('hidden', 'recurring', 0);

        if ($isnew) {
            // Add webinar, disabled if the user cannot create webinars.
            /*$webinarattr = null;
            if (!$service->_get_user_settings($zoomuser->id)->feature->webinar) {
                $webinarattr = array('disabled' => true, 'group' => null);
            }*/
            //$webinarattr = array('disabled' => true, 'group' => null);
            /*$webinarattr = null;

            $mform->addElement('advcheckbox', 'webinar', get_string('webinar', 'zoom'), '', $webinarattr);
            $mform->setDefault('webinar', 0);
            $mform->addHelpButton('webinar', 'webinar', 'zoom');*/
            $mform->addElement('hidden', 'webinar', 0);
        } else if ($this->current->webinar) {
            //$mform->addElement('html', get_string('webinar_already_true', 'zoom'));
        } else {
            //$mform->addElement('html', get_string('webinar_already_false', 'zoom'));
        }

        // Add password.
        $mform->addElement('passwordunmask', 'password', get_string('password', 'zoom'), array('maxlength' => '10'));
        // Check password uses valid characters.
        $regex = '/^[a-zA-Z0-9@_*-]{1,10}$/';
        $mform->addRule('password', get_string('err_password', 'mod_zoom'), 'regex', $regex, 'client');
        $mform->disabledIf('password', 'webinar', 'checked');

        // Add host/participants video (checked by default).
        $mform->addGroup(array(
            $mform->createElement('radio', 'option_host_video', '', get_string('on', 'zoom'), true),
            $mform->createElement('radio', 'option_host_video', '', get_string('off', 'zoom'), false)
        ), null, get_string('option_host_video', 'zoom'));
        $mform->setDefault('option_host_video', $config->defaulthostvideo);
        $mform->disabledIf('option_host_video', 'webinar', 'checked');

        $mform->addGroup(array(
            $mform->createElement('radio', 'option_participants_video', '', get_string('on', 'zoom'), true),
            $mform->createElement('radio', 'option_participants_video', '', get_string('off', 'zoom'), false)
        ), null, get_string('option_participants_video', 'zoom'));
        $mform->setDefault('option_participants_video', $config->defaultparticipantsvideo);
        $mform->disabledIf('option_participants_video', 'webinar', 'checked');

        // Add audio options.
        $mform->addGroup(array(
            $mform->createElement('radio', 'option_audio', '', get_string('audio_telephony', 'zoom'), ZOOM_AUDIO_TELEPHONY),
            $mform->createElement('radio', 'option_audio', '', get_string('audio_voip', 'zoom'), ZOOM_AUDIO_VOIP),
            $mform->createElement('radio', 'option_audio', '', get_string('audio_both', 'zoom'), ZOOM_AUDIO_BOTH)
        ), null, get_string('option_audio', 'zoom'));
        $mform->setDefault('option_audio', $config->defaultaudiooption);

        // Add meeting options. Make sure we pass $appendName as false
        // so the options aren't nested in a 'meetingoptions' array.
        $mform->addGroup(array(
            // Join before host.
            $mform->createElement('advcheckbox', 'option_jbh', '', get_string('option_jbh', 'zoom'))
        ), 'meetingoptions', get_string('meetingoptions', 'zoom'), null, false);
        $mform->setDefault('option_jbh', $config->defaultjoinbeforehost);
        $mform->addHelpButton('meetingoptions', 'meetingoptions', 'zoom');
        $mform->disabledIf('meetingoptions', 'webinar', 'checked');

        // Add alternative hosts.
        /*$mform->addElement('text', 'alternative_hosts', get_string('alternative_hosts', 'zoom'), array('size' => '64'));
        $mform->setType('alternative_hosts', PARAM_TEXT);
        // Set the maximum field length to 255 because that's the limit on Zoom's end.
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('alternative_hosts', 'alternative_hosts', 'zoom');*/
        $mform->addElement('hidden', 'alternative_hosts', '');

        // Add meeting id.
        $mform->addElement('hidden', 'meeting_id', -1);
        $mform->setType('meeting_id', PARAM_ALPHANUMEXT);

        // Add host id (will error if user does not have an account on Zoom).
        //$mform->addElement('hidden', 'host_id', zoom_get_user_id());
        //$mform->addElement('hidden', 'host_id', zoom_get_usable_hostid($this->current->course,true));
        $mform->addElement('hidden', 'host_id', -1);
        $mform->setType('host_id', PARAM_ALPHANUMEXT);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();
        $mform->setDefault('grade', false);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();


        /*$jsinfo = (object)array();
        $module = array(
            'name' => 'mod_lti_edit',
            'fullpath' => '/mod/zoom/mod_form.js',
            'requires' => array('base', 'io', 'querystring-stringify-simple', 'node', 'event', 'json-parse'),
            'strings' => array(
                array('addtype', 'lti'),
                ),
        );
        $PAGE->requires->js_init_call('M.mod_zoom.editor.init', array(json_encode($jsinfo)), true, $module);*/
    }

    /**
     * More validation on form data.
     * See documentation in lib/formslib.php.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $CFG;
        $errors = array();

        // Only check for scheduled meetings.
        if (empty($data['recurring']) && $data['timetype']==0) {
            // Make sure start date is in the future.
            if ($data['start_time'] < strtotime('today')) {
                $errors['start_time'] = get_string('err_start_time_past', 'zoom');
            }
            //开始时间为未来24H内
            /*if ($data['start_time'] > time()+24*3600) {
                $errors['start_time'] = '开始时间必须在24h内！';
            }*/

            // Make sure duration is positive and no more than 150 hours.
            if ($data['duration'] <= 0) {
                $errors['duration'] = get_string('err_duration_nonpositive', 'zoom');
            /*} else if ($data['duration'] > 150 * 60 * 60) {
                $errors['duration'] = get_string('err_duration_too_long', 'zoom');*/
            } else if ($data['duration'] > 4 * 60 * 60) {
                $errors['duration'] = '有效期只能是在4h内！';
            }

        }

        $start = $data['start_time'];
        $end = $data['start_time'] + $data['duration'];
        $count = zoom_get_order_count($start,$end + $data['timetype']);
        if($count>=101){
            $errors['start_time'] = '当前时间段预约人数已满！';
        }

        // Check if the listed alternative hosts are valid users on Zoom.
        /*require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');
        $service = new mod_zoom_webservice();
        $alternativehosts = explode(',', $data['alternative_hosts']);
        foreach ($alternativehosts as $alternativehost) {
            if (!($service->get_user($alternativehost))) {
                $errors['alternative_hosts'] = 'User ' . $alternativehost . ' was not found on Zoom.';
                break;
            }
        }*/

        return $errors;
    }
}

/**
 * Form to search for meeting reports.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_zoom_report_form extends moodleform {
    /**
     * Define form elements.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('date_selector', 'from', get_string('from'));

        $mform->addElement('date_selector', 'to', get_string('to'));

        $mform->addElement('submit', 'submit', get_string('go'));
    }
}
