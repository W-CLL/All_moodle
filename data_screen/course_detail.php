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
 * Course detail
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once('public/header.php');
$url = basename(__FILE__);
?>
<link href="amd/layouts/layout4/css/teachingSituation.css" rel="stylesheet" type="text/css" />
<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<?php require_once("public/page_header.php"); ?>
<!-- END HEADER -->
<div class="page-container">
    <?php require_once("public/course_leftside.php"); ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-head">
                <div class="page-title">
                    <h1></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href=""><?PHP echo get_string('course', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active courseName"></span>
                </li>
            </ul>
            <?php require_once('public/course_nav.php'); ?>
            <div class="course-baseinfo bg-white">
                <div class="row">
                    <div class="col-md-3">
                        <div class="course-image" id="course_img"></div>
                    </div>
                    <div class="col-md-9">
                        <div class="course-baseinfoms">
                            <h1 class="course-name" id="course-name"></h1>
                            <a class="course-linkbtn btn blue btn-outline" href="/course/view.php?id=<?PHP echo $_GET['id']?>" target="_blank"><?PHP echo get_string('view_course', 'block_data_screen');?></a>
                            <div class="course-baseinfoms-desc" id="course-desc"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="course-teachers portlet light bordered my-10">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject font-green-haze bold uppercase"><?PHP echo get_string('course_teacher', 'block_data_screen');?></span></div>
                        </div>
                        <div class="portlet-body util-btn-margin-bottom-5">
                            <div class="teacherList"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="course-totaldata portlet light bordered my-10">
                        <div class="portlet-title">
                            <div class="caption"><span class="caption-subject font-green-haze bold uppercase"><?PHP echo get_string('basic_statistics', 'block_data_screen');?></span></div>
                        </div>
                        <div class="portlet-body util-btn-margin-bottom-5">
                            <ul class="course-totaldata-list row">
                                <li class="col-md-4 mb-15">
                                    <div class="item">
                                        <div class="data-type">
                                            <i class="customicon customicon-40 customicon-resourse"></i>
                                            <span class="data-type-name"><?PHP echo get_string( 'resource_num', 'block_data_screen');?></span>
                                        </div>
                                        <div class="data-value" id="resource_num"></div>
                                    </div>
                                </li>
                                <li class="col-md-4 mb-15">
                                    <div class="item">
                                        <div class="data-type">
                                            <i class="customicon customicon-40 customicon-activity"></i>
                                            <span class="data-type-name"><?PHP echo get_string( 'activity_num', 'block_data_screen');?></span>
                                        </div>
                                        <div class="data-value" id="activity_num"></div>
                                    </div>
                                </li>
                                <li class="col-md-4 mb-15">
                                    <div class="item">
                                        <div class="data-type">
                                            <i class="customicon customicon-40 customicon-student"></i>
                                            <span class="data-type-name"><?PHP echo get_string( 'student_num', 'block_data_screen');?></span>
                                        </div>
                                        <div class="data-value" id="students_num"></div>
                                    </div>
                                </li>
                                <li class="col-md-4 mb-15">
                                    <div class="item">
                                        <div class="data-type">
                                            <i class="customicon customicon-40 customicon-forum"></i>
                                            <span class="data-type-name"><?PHP echo get_string( 'forum_num', 'block_data_screen');?></span>
                                        </div>
                                        <div class="data-value" id="forums_num"></div>
                                    </div>
                                </li>
                                <li class="col-md-4 mb-15">
                                    <div class="item">
                                        <div class="data-type">
                                            <i class="customicon customicon-40 customicon-assign"></i>
                                            <span class="data-type-name"><?PHP echo get_string( 'assign_num', 'block_data_screen');?></span>
                                        </div>
                                        <div class="data-value" id="assigns_num"></div>
                                    </div>
                                </li>
                                <li class="col-md-4 mb-15">
                                    <div class="item">
                                        <div class="data-type">
                                            <i class="customicon customicon-40 customicon-quiz"></i>
                                            <span class="data-type-name"><?PHP echo get_string( 'quiz_num', 'block_data_screen');?></span>
                                        </div>
                                        <div class="data-value" id="quizs_num"></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once("public/page_footer.php"); ?>
<?php require_once("public/course_publicjs.php"); ?>
<script>
    var courseID = window.location.search.slice(1).split('=')[1];

    if(courseID != 0){
        getTeaherSituDetail(courseID);
        $(".teacherList, #course-desc").niceScroll({cursorborder:"#C5CCD5",cursorcolor:"#C5CCD5"});
    }else{
        $('.page-title h1').text("<?PHP echo get_string('no_course', 'block_data_screen');?>");
    }
    function getTeaherSituDetail(courseID){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_course_detail',
            'args': {
                'id':courseID,
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
                    var course = result.course;
                    for(let item in course){
                        if(item.indexOf('num') > 0){
                            $('#' + item).text(course[item]);
                        }else if(item=='fullname'){
                            $('.page-title h1, .courseName, #course-name').text(course[item]);
                            $("title").html(course[item]);
                        }else if(item=='summary'){
                            $('#course-desc').text(course[item]);
                        }else{
                            $('#course_img').css('background-image', 'url(' + course[item] + ')');
                        }
                    }
                    var teacher_list = result.teacher_list;
                    var teacherListHtml = [];
                    for (var k = 0; k < teacher_list.length; k++) {
                        var itemK = teacher_list[k];
                        if(itemK.dept==''){itemK.dept='/'}
                        teacherListHtml += "<div class=\"teacherDiv mb-15\" id=\""+ itemK.id +"\">\n" +
                            "<div class=\"media\">\n" +
                            "<div class=\"media-body\">\n" +
                            "<h4 class=\"media-heading\" id=\"" + itemK.id + "\"><a href=\"\">" + itemK.name + "</a></h4>\n" +
                            "<div class=\"department\">" + itemK.dept + "</div>\n" +
                            "</div>\n" +
                            "<div class=\"media-right\">\n" +
                            "<img class=\"media-object\" src=\"" + itemK.url + "\" alt=\"\">\n" +
                            "</div>\n" +
                            "</div>\n" +
                            "</div>"
                        $('.teacherList').html(teacherListHtml);
                        $('.teacherDiv').click(function(){
                            var tescherId = $(this).attr('id');
                            window.location.href="teacher_detail.php?id=" + tescherId;
                        });
                    }
                }
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