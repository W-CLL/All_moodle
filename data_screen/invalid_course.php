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
 * Invalid course
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once('public/header.php');
$url = basename(__FILE__);
?>
<link rel="stylesheet" href="amd/layouts/layout4/css/atributeClass.css">
<link rel="stylesheet" href="amd/layouts/layout4/css/paging.css">
<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<?php require_once('public/page_header.php'); ?>
<!-- END HEADER -->
<div class="page-container">
<?php require_once('public/platform_leftside.php'); ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-head">
                <div class="page-title">
                    <h1><?PHP echo get_string('invalid_course', 'block_data_screen');?></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active"><?PHP echo get_string('invalid_course', 'block_data_screen');?></span>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="allCourseDiv">
                                <h4 class="allCourse">
                                    <span><?PHP echo get_string('invalid_course', 'block_data_screen');?></span>
                                    <small><?PHP echo get_string('invalid_description', 'block_data_screen');?></small>
                                    <input type="button" class="form-control pull-right download" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                                </h4>
                            </div>
                            <div class="row m-tb-15">
                                <div class="items items_one courseClasses">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('course_category', 'block_data_screen');?>：</label>
                                    <select class="form-control inputs input_one">
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="items items_two startTime">
                                    <label for="" class="labels labels_two"><?PHP echo get_string('course_start', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_two" type="date">
                                </div>

                                <div class="items items_two endTime">
                                    <label for="" class="labels labels_two"><?PHP echo get_string('course_end', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_two" type="date">
                                </div>

                                <div class="items items_one judgmentSelect">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('judgment', 'block_data_screen');?>：</label>
                                    <select class="form-control inputs input_one">
                                        <option value="" id="0"><?PHP echo get_string('all', 'block_data_screen');?></option>
                                        <option class="judgment" value="" id="1"><?PHP echo get_string('no_teacher', 'block_data_screen');?></option>
                                        <option class="judgment" value="" id="2"><?PHP echo get_string('no_activity', 'block_data_screen');?></option>
<!--                                        <option class="judgment" value="" id="3">--><?PHP //echo get_string('have_test', 'block_data_screen');?><!--</option>-->
                                    </select>
                                </div>

                                <div class="items search">
                                    <input class="form-control searchInput" type="button" value="<?PHP echo get_string('search', 'block_data_screen');?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 table-responsive-xl">
                                    <table class="table table-bordered table-striped helplessTable">
                                        <tbody>
                                        <tr>
                                            <td><?PHP echo get_string('course_id', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('course_category', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('full_name', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('short_name', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('course_start', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('course_end', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('teachers', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('judgment', 'block_data_screen');?></td>
                                        </tr>
                                        </tbody>
                                        <tbody class="helpLessContent">
                                        </tbody>
                                    </table>

                                </div>
                                <div class="col-md-12">
                                    <div id="page"></div>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once('public/page_footer.php'); ?>
<?php require_once('public/platform_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/paging.js"></script>
<script>
    $("title").html("<?PHP echo get_string('invalid_course', 'block_data_screen');?>");
    $('.download').click(function () {
        getDownload();
    });
    function getDownload() {
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_invalid_course',
            'args': {
                'pagesize': 0,
                'role': role,
                'user': "<?PHP echo $USER->id;?>"
            }
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var courseList = result.course_list;
                    var jsonData = courseList;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('full_name', 'block_data_screen');?>",
                        "<?PHP echo get_string('short_name', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_start', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_end', 'block_data_screen');?>",
                        "<?PHP echo get_string('teachers', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_category', 'block_data_screen');?>",
                        "<?PHP echo get_string('judgment', 'block_data_screen');?>"
                    ];
                    JSONToExcelConvertor(jsonData, "<?PHP echo get_string('invalid_course', 'block_data_screen');?>", title);
                } else {}
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
    getClasses();
    function getClasses(){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_get_category',
            'args': {}
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                var courseClassesHtml ='';
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    courseClassesHtml = "<option style=\"\" value=\"\"><?PHP echo get_string('all', 'block_data_screen');?></option>\n";
                    for(var i=0;i<result.length;i++){
                        var item = result[i];
                        courseClassesHtml += "<option class=\"classify\" value=\"\" id=\""+ item.id +"\">"+ item.name +"</option>"
                        $('.courseClasses select').html(courseClassesHtml);
                    }
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    getHelpLessCourse();
    function getHelpLessCourse(category_id,start_time_text,end_time_text,judgment,curPageId){
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_invalid_course',
            'args': {
                'category_id': category_id,
                'start_time': start_time_text,
                'end_time': end_time_text,
                'judgment': judgment,
                'page': curPageId,
                'pagesize':'12',
                'role': role,
                'user': '<?PHP echo $USER->id;?>'
            }
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                var contentHtml1 = '';
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var courseList = result.course_list;
                    if(!courseList){
                        courseList = [];
                    }
                    if(courseList.length >= 1){
                      for(var n=0;n<courseList.length;n++){
                        var item3 = courseList[n];
                        contentHtml1 += "<tr>\n"+
                            "<td>"+ item3.course_id +"</td>\n"+
                            "<td>"+ item3.category +"</td>\n"+
                            "<td>"+ item3.fullname +"</td>\n"+
                            "<td>"+ item3.shortname +"</td>\n"+
                            "<td>"+ item3.start_time +"</td>\n"+
                            "<td>"+ item3.end_time +"</td>\n"+
                            "<td>"+ item3.teachers +"</td>\n"+
                            "<td>"+ item3.judgment +"</td>\n"+
                            "</tr>"
                      }
                    }else{
                      contentHtml1 += "<tr>\n"+
                            "<td colspan=\"8\" style=\"text-align:center;\">暂无数据</td>\n"+
                            "</tr>"
                    }
                    
                    $('tbody.helpLessContent').html(contentHtml1);
                    
                    var helpLessPage = result.page;
                    showPage(helpLessPage,category_id,category_id,start_time_text,end_time_text,judgment);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showPage(page,category_id,start_time_text,end_time_text,judgment){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getHelpLessCourse(category_id,start_time_text,end_time_text,judgment,curPageId);
          }
      })
    }

    $('.searchInput').click(function () {
        var category_id = $('.courseClasses select option.classify:selected').attr('id');
        var start_time_text = $('.startTime input').val();
        var end_time_text = $('.endTime input').val();
        var openTimes = $('.numStart input').val();
        var judgment = $('.judgmentSelect select option.judgment:selected').attr('id');
        var curPageId = $('.current').text();
        if(curPageId == ""){
            curPageId = 1;
        }
        if (category_id == null) {
            category_id = 0;
        }
        if (start_time_text != '' && end_time_text != '') {
            var starttimestamp = new Date(start_time_text);
            var endtimestamp = new Date(end_time_text);
            if (starttimestamp > endtimestamp) {
                var data = start_time_text;
                start_time_text = end_time_text;
                end_time_text = data;
            }
        }
        if (judgment == null) {
            judgment = 0;
        }
        getHelpLessCourse(category_id, start_time_text, end_time_text, judgment, curPageId);
    });
</script>
</body>

</html>
