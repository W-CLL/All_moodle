<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/mod/zoom/classes/phpexcel/PHPExcel.php');
require_once($CFG->dirroot.'/mod/zoom/classes/phpexcel/PHPExcel/Writer/Excel2007.php');


global $PAGE, $USER,$DB;
$config = get_config('mod_zoom');
$id = required_param('id', PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('zoom', $id, 0, false, MUST_EXIST);
    $course     = get_course($cm->course);
    $zoom       = $DB->get_record('zoom', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID');
}

require_login($course, true, $cm);

$obj_PHPExcel = new PHPExcel();
$obj_Sheet = $obj_PHPExcel->getActiveSheet();

$obj_Sheet->setTitle('sheet1');
//设置单元格宽度
$obj_Sheet->getColumnDimension('A')->setWidth(49);
$obj_Sheet->getColumnDimension('B')->setWidth(49);
$obj_Sheet->getColumnDimension('C')->setWidth(49);
$obj_Sheet->getColumnDimension('D')->setWidth(49);
$obj_Sheet->getStyle('D')->getAlignment()->setWrapText(true);
$obj_Sheet->getDefaultRowDimension()->setRowHeight(20);

$obj_Sheet->getDefaultStyle()->getAlignment()
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$username     = get_string('username', 'mod_zoom');
$join_time    = get_string('join_time', 'mod_zoom');
$leave_time   = get_string('leave_time', 'mod_zoom');
$leave_reason = get_string('leave_reason', 'mod_zoom');
$N_leave      = get_string('n_leave', 'mod_zoom');
$N_reason     = get_string('n_reason', 'mod_zoom');

$service      = new mod_zoom_webservice();
$reponse      = json_encode($service->get_meeting_records($zoom));
$reponse      = json_decode($reponse, true);
$participants = $reponse['participants'];

if ($reponse == 0 || empty($participants)) {
    $notice = get_string('Not', 'mod_zoom');
    notice('提示：'.$notice, new moodle_url('/mod/zoom/view.php', array('id' => $cm->id)));
} 

if (!empty($participants)) {
    foreach ($participants as $p) {
    
        if (empty($p['leave_time'])) {
            $data[] = array(
                        $username => $p['user_name'],
                        $join_time => date('Y/m/d H:i:s', strtotime($p['join_time'])),
                        $leave_time =>$N_leave,);
        } else {
            $data[] = array(
                        $username => $p['user_name'],
                        $join_time => date('Y/m/d H:i:s', strtotime($p['join_time'])),
                        $leave_time =>date('Y/m/d H:i:s', strtotime($p['leave_time'])),
                        $leave_reason => $p['leave_reason']);
        }
    }
    
    $keys = array_keys($data[0]);//获取表头

    //把数据写进表格
    for ($j = 0; $j <= count($data); $j++) {
        for ($k = 1; $k <= count($data[0]); $k++) {
            $colname = PHPExcel_Cell::stringFromColumnIndex($k-1);
            $colname .= $j+1;
            if ($j == 0) {
                $value = $keys[$k-1];
            } else {
                $key = $keys[$k-1];
                $value = $data[$j-1][$key];
            }
            $obj_PHPExcel->setActiveSheetIndex(0)->setCellValue($colname, $value);
        }
    }
}
// $obj_Writer = PHPExcel_IOFactory::createWriter($obj_PHPExcel, 'Excel2007');
// zoom_browser_export('meetingrecords', false);
// $obj_Writer->save('php://output');
