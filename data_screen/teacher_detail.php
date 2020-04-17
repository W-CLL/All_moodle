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
 * Teacher detail
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
                    <h1></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="teacher_list.php"><?PHP echo get_string('teacher_profile', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active teahName"></span>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered scl">
                        <div class="row m-tb-15 Option">
                            <div class="items items_two startTime">
                                <label for="" class="labels labels_one"><?PHP echo get_string('start_time', 'block_data_screen');?>：</label>
                                <input class="form-control inputs input_one" type="date">
                            </div>

                            <div class="items items_two endTime">
                                <label for="" class="labels labels_one"><?PHP echo get_string('end_time', 'block_data_screen');?>：</label>
                                <input class="form-control inputs input_one" type="date">
                            </div>

                            <div class="items search search_scl">
                                <input class="form-control searchInput" type="button" value="<?PHP echo get_string('search', 'block_data_screen');?>">
                            </div>

                            <div class="items download mt-0">
                                <input type="button" class="form-control pull-right schDownload" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- BEGIN PAGE BASE CONTENT -->
            <div class="row">
                <div class="col-xs-1-5 ">
                    <div class="dashboard-stat2 bordered bg">
                        <div class="display totalNum ">
                            <div class="number">
                                <h3 class="font-green-sharp color-red">
                                    <span data-value="600">0</span>
                                </h3>
                                <small class=""><?PHP echo get_string('course_total', 'block_data_screen');?></small>
                                <img class="teacher_icons" src="amd/layouts/layout4/img/teacher_class_icon@2x.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5">
                    <div class="dashboard-stat2 bordered bg">
                        <div class="display totalNum">
                            <div class="number">
                                <h3 class="font-red-haze color-purple">
                                    <span data-value="600">0</span>
                                </h3>
                                <small class=""><?PHP echo get_string('student_total', 'block_data_screen');?></small>
                                <img class="teacher_icons" src="amd/layouts/layout4/img/teacher_student_icon@2x.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5">
                    <div class="dashboard-stat2 bordered bg">
                        <div class="display totalNum">
                            <div class="number">
                                <h3 class="font-blue-sharp color-blue">
                                    <span data-value="600"></span>
                                </h3>
                                <small class=""><?PHP echo get_string('max_opening_num', 'block_data_screen');?></small>
                                <img class="teacher_icons" src="amd/layouts/layout4/img/teacher_round_icon@2x.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5">
                    <div class="dashboard-stat2 bordered bg">
                        <div class="display totalNum">
                            <div class="number">
                                <h3 class="font-purple-soft color-green">
                                    <span data-value="600"></span>
                                </h3>
                                <small class=""><?PHP echo get_string('avg_spend_time', 'block_data_screen');?></small>
                                <img class="teacher_icons" src="amd/layouts/layout4/img/teacher_time_icon@2x.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5">
                    <div class="dashboard-stat2 bordered bg">
                        <div class="display totalNum">
                            <div class="number">
                                <h3 class="font-pink-soft color-yellow">
                                    <span data-value="600"></span>
                                </h3>
                                <small class=""><?PHP echo get_string('avg_activity_resource', 'block_data_screen');?></small>
                                <img class="teacher_icons " src="amd/layouts/layout4/img/teacher_resource_icon@2x.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-xs-12 col-sm-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject bold uppercase font-dark"><?PHP echo get_string('all_course', 'block_data_screen');?></span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row teacherTwoCourse">
                            </div>
                            <div class="row">
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
<!-- END CONTAINER -->
<?php require_once('public/page_footer.php'); ?>
<?php require_once('public/platform_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/paging.js"></script>
<script>
    getTeaherTwo();
    function getTeaherTwo(start_time_text,end_time_text,curPageId){
        var teaOneUrl = window.location.search;
        var teahId = teaOneUrl.slice(1).split('=')[1];
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_teacher_detail',
            'args': {
                'id':teahId,
                'start_time':start_time_text,
                'end_time':end_time_text,
                'page':curPageId,
                'pagesize':12,
            }
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
              var error = result[0].error;
              if(error == true){
                var exception = result[0].exception;
                var errorMsg = exception.message;
                alert(errorMsg);
              }else{
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var teacher = result.teacher;
                    $('.page-title h1').text(teacher.name);
                    $('.teahName').text(teacher.name);
                    $('title').text(teacher.name);
                    $('.font-green-sharp span').attr('data-value',teacher.course_num).counterUp();
                    $('.font-red-haze span').attr('data-value',teacher.student_num).counterUp();
                    $('.font-blue-sharp span').attr('data-value',teacher.open_times).counterUp();
                    $('.font-purple-soft span').attr('data-value',teacher.spendtime_avg).counterUp();
                    $('.font-pink-soft span').attr('data-value',teacher.recourse_avg).counterUp();

                    var course_list = result.course_list;
                    showTeahList(course_list);
                    var page = result.page;
                    showPage(start_time_text,end_time_text,page);
                }
              }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showTeahList(course_list){
        var html = "";
        if(!course_list){
            course_list=[];
        }
        for(var i=0;i<course_list.length;i++){
            var item = course_list[i];
            html += "<div class=\"col-md-2 col-md-2 col-sm-12 col-xs-12 courseItem\" id=\""+ item.course_id +"\">\n" +
                "<div class=\"classItem\">\n" +
                "<div class=\"coursePic\">\n" +
                "<a href=\"course_detail.php?id=" + item.course_id + "\"><img class=\"course_img\" src=\"" + item.img + "\" alt=\"\"></a> \n" +
                "<div class=\"stuNum\">\n" +
                "<span class=\"icon\"></span>\n" +
                "<span class=\"stuNumText\">" + item.student_num + "</span>\n" +
                "</div>\n" +
                "</div>\n" +
                "<div class=\"itemText teachItemText\">\n" +
                "<p class=\"teahItemName\" id=\"" + item.course_id +
                "\"><a class=\"teacherA\" href=\"course_detail.php?id=" + item.course_id + "\">" + item.fullname +
                "</a></p>\n" +
                "</div>\n" +
                "</div>\n" +
                "</div>"
        }
        $('.teacherTwoCourse').html(html);
        $('.courseItem').click(function(){
            var courseId = $(this).attr('id');
            window.location.href = "course_detail.php?id=" + courseId;
        });
    }
    function showPage(start_time_text,end_time_text,page){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getTeaherTwo(start_time_text,end_time_text,curPageId);
          }
      })
    }
  
    $('.searchInput').click(function () {
        var start_time_text = $('.startTime input').val();
        var end_time_text = $('.endTime input').val();
        var curPageId = $('.pagination .active').text();
        if (start_time_text == '') {
            start_time_text = '0';
        }
        if (end_time_text == '') {
            end_time_text = '0';
        }
        if (start_time_text > end_time_text) {
            var data = start_time_text
            var data1 = end_time_text
            start_time_text = data1
            end_time_text = data
        }
        getTeaherTwo(start_time_text, end_time_text, curPageId);
    });

    $('.schDownload').click(function () {
        getDownload();
    });

    function getDownload() {
        var teaOneUrl = window.location.search;
        var teahId = teaOneUrl.slice(1).split('=')[1];
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_teacher_detail',
            'args': {
                'id':teahId,
                'pagesize':0,
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
                    var course_list = result.course_list;
                    var jsonData = course_list;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('full_name', 'block_data_screen');?>",
                        "<?PHP echo get_string('student_num', 'block_data_screen');?>"
                    ]
                    var filter = ['img']
                    var filename = $('title').text();
                    JSONToExcelConvertor(jsonData, filename, title, filter);
                } else {
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
</script>
</body>

</html>
