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
 * College detail
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
                    <a href="college_list.php"><?PHP echo get_string('college_profile', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active schCollegeName"></span>
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
            <div class="row">
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered ">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-red-haze">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><?PHP echo get_string('course_num', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                  <span style="width: 0%;" class="progress-bar progress-bar-success red-haze">
                    <span class="sr-only"></span>
                  </span>
                            </div>
                            <div class="status">
                                <div class="status-title red-status"><?PHP echo get_string('high_avg', 'block_data_screen');?></div>
                                <div class="status-number red-haze-status"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-yellow-soft">
                                    <span data-value="600"></span>
                                </h3>
                                <small><?PHP echo get_string('teacher_num', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-map-marker"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                  <span style="width: 0%;" class="progress-bar progress-bar-success yellow-soft">
                    <span class="sr-only"></span>
                  </span>
                            </div>
                            <div class="status">
                                <div class="status-title yellow-status"><?PHP echo get_string('low_avg', 'block_data_screen');?></div>
                                <div class="status-number yellow-soft-status"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-purple-soft">
                                    <span data-value="600"></span>
                                </h3>
                                <small><?PHP echo get_string('student_num', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                  <span style="width: 0%;" class="progress-bar progress-bar-success purple-soft">
                    <span class="sr-only"></span>
                  </span>
                            </div>
                            <div class="status">
                                <div class="status-title purple-status"></div>
                                <div class="status-number purple-soft-status"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-blue-sharp">
                                    <span data-value="600"></span>
                                </h3>
                                <small><?PHP echo get_string('resource_num', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                  <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                    <span class="sr-only">45% grow</span>
                  </span>
                            </div>
                            <div class="status">
                                <div class="status-title blue-status"></div>
                                <div class="status-number blue-sharp-status"> 45% </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-green-sharp">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><?PHP echo get_string('activity_num', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="icon-pie-chart"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                  <span style="width: 76%;" class="progress-bar progress-bar-success green-sharp">
                    <span class="sr-only">76% progress</span>
                  </span>
                            </div>
                            <div class="status">
                                <div class="status-title green-status"></div>
                                <div class="status-number green-sharp-status"> 76% </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-pink-soft">
                                    <span data-value="600"></span>
                                </h3>
                                <small><?PHP echo get_string('access_num', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-map-marker"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                  <span style="width: 57%;" class="progress-bar progress-bar-success pink-soft">
                    <span class="sr-only">56% change</span>
                  </span>
                            </div>
                            <div class="status">
                                <div class="status-title pink-status"></div>
                                <div class="status-number pink-soft-status"> 57% </div>
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
                            <div class="row courseList">
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
<?php require_once('public/page_footer.php'); ?>
<?php require_once('public/platform_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/paging.js"></script>
<script>
    getSchProfileTwo();
    function getSchProfileTwo(start_time_text, end_time_text, curPageId) {
        var role = $("li.active").val();
        var oneUrl = window.location.search;
        var catId = oneUrl.slice(1).split('=')[1];
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_college_detail',
            'args': {
                'catid': catId,
                'start_time': start_time_text,
                'end_time': end_time_text,
                'page': curPageId,
                'pagesize': 12,
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
              var error = result[0].error;
              if(error == true){
                var exception = result[0].exception;
                var errorMsg = exception.message;
                alert(errorMsg);
              }else{
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var college = result.college;
                    $('.page-title h1').text(college.name);
                    $('.schCollegeName').text(college.name);
                    $('title').text(college.name);
                    $('.font-red-haze span').attr('data-value', college.course_num).counterUp();
                    $('.font-yellow-soft span').attr('data-value', college.teacher_num).counterUp();
                    $('.font-purple-soft span').attr('data-value', college.student_num).counterUp();
                    $('.font-blue-sharp span').attr('data-value', college.resource_num).counterUp();
                    $('.font-green-sharp span').attr('data-value', college.activity_num).counterUp();
                    $('.font-pink-soft span').attr('data-value', college.access_num).counterUp();
                    if (parseInt(college.course_avg) >= 0) {
                        $('.red-status').text("<?PHP echo get_string('high_avg', 'block_data_screen');?>");
                    } else {
                        $('.red-status').text("<?PHP echo get_string('low_avg', 'block_data_screen');?>");
                    }
                    if (parseInt(college.teacher_avg) >= 0) {
                        $('.yellow-status').text("<?PHP echo get_string('high_avg', 'block_data_screen');?>");
                    } else {
                        $('.yellow-status').text("<?PHP echo get_string('low_avg', 'block_data_screen');?>");
                    }
                    if (parseInt(college.student_avg) >= 0) {
                        $('.purple-status').text("<?PHP echo get_string('high_avg', 'block_data_screen');?>");
                    } else {
                        $('.purple-status').text("<?PHP echo get_string('low_avg', 'block_data_screen');?>");
                    }
                    if (parseInt(college.resource_avg) >= 0) {
                        $('.blue-status').text("<?PHP echo get_string('high_avg', 'block_data_screen');?>");
                    } else {
                        $('.blue-status').text("<?PHP echo get_string('low_avg', 'block_data_screen');?>");
                    }
                    if (parseInt(college.activity_avg) >= 0) {
                        $('.green-status').text("<?PHP echo get_string('high_avg', 'block_data_screen');?>");
                    } else {
                        $('.green-status').text("<?PHP echo get_string('low_avg', 'block_data_screen');?>");
                    }
                    if (parseInt(college.access_avg) >= 0) {
                        $('.pink-status').text("<?PHP echo get_string('high_avg', 'block_data_screen');?>");
                    } else {
                        $('.pink-status').text("<?PHP echo get_string('low_avg', 'block_data_screen');?>");
                    }
                    var courseAvg = Math.abs(parseInt(college.course_avg));
                    var teacherAvg = Math.abs(parseInt(college.teacher_avg));
                    var studentAvg = Math.abs(parseInt(college.student_avg));
                    var resourseAvg = Math.abs(parseInt(college.resource_avg));
                    var activityAvg = Math.abs(parseInt(college.activity_avg));
                    var accessAvg = Math.abs(parseInt(college.access_avg));
                    $('.red-haze').css('width', courseAvg);
                    $('.red-haze-status').text(courseAvg + "%");
                    $('.yellow-soft').css('width', teacherAvg);
                    $('.yellow-soft-status').text(teacherAvg + "%");
                    $('.purple-soft').css('width', studentAvg);
                    $('.purple-soft-status').text(studentAvg + "%");
                    $('.blue-sharp').css('width', resourseAvg);
                    $('.blue-sharp-status').text(resourseAvg + "%");
                    $('.green-sharp').css('width', activityAvg);
                    $('.green-sharp-status').text(activityAvg + "%");
                    $('.pink-soft').css('width', accessAvg);
                    $('.pink-soft-status').text(accessAvg + "%");
                    var course_list = result.course_list;
                    showCourse(course_list);
                    var page = result.page;
                    showPage(start_time_text, end_time_text, curPageId, page);
                }
              }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showCourse(course_list) {
        var html = "";
        if (!course_list) {
            course_list = [];
        }
        for (var i = 0; i < course_list.length; i++) {
            var item = course_list[i];
            html += "<div class=\"col-md-2 courseItem\" id=\""+ item.course_id +"\">\n" +
                "<div class=\"classItem\">\n" +
                "<div class=\"coursePic\">\n" +
                "<a href=\" # \"><img class=\"course_img\" src=\"" + item.img + "\" alt=\"\"></a> \n" +
                "<div class=\"stuNum\">\n" +
                "<span class=\"icon\"></span>\n" +
                "<span class=\"stuNumText\">" + item.student_num + "</span>\n" +
                "</div>\n" +
                "</div>\n" +
                "<div class=\"itemText\">\n" +
                "<p class=\"itemName\" id=\"" + item.course_id + "\"><a href=\"course_detail.php?id=" + item.course_id +
                "\">" + item.fullname + "</a></p>\n" +
                "<p class=\"courseTeacher\"><span class=\"jiaoshi\"><?PHP echo get_string('teacher', 'block_data_screen');?>：</span><span>" + item.teachers + "</span> </p>\n" +
                "</div>\n" +
                "</div>\n" +
                "</div>"

        }
        $('.courseList').html(html);
        $('.courseItem').click(function(){
            var courseId = $(this).attr('id');
            window.location.href= "course_detail.php?id=" + courseId;
        });
    }
    function showPage(start_time_text, end_time_text, curPageId, page){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getSchProfileTwo(start_time_text, end_time_text, curPageId);
          }
      })
    }

    $('.searchInput').click(function () {
        var start_time_text = $('.startTime input').val();
        var end_time_text = $('.endTime input').val();
        var curPageId = $('.current').text();
        if(curPageId == ""){
            curPageId = 1;
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
        getSchProfileTwo(start_time_text, end_time_text, curPageId);
    });

    $('.schDownload').click(function () {
        getDownload();
    });

    function getDownload() {
        var oneUrl = window.location.search;
        var catId = oneUrl.slice(1).split('=')[1];
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_college_detail',
            'args': {
                'catid': catId,
                'pagesize': 0,
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
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var courseList = result.course_list;

                    var jsonData = courseList;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('full_name', 'block_data_screen');?>",
                        "<?PHP echo get_string('student_num', 'block_data_screen');?>",
                        "<?PHP echo get_string('teacher', 'block_data_screen');?>"
                    ];
                    var filter = ['img'];
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
