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
 * Platform overview
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
<?php require_once('public/page_header.php'); ?>
<!-- END HEADER -->
<div class="page-container">
    <?php require_once('public/course_leftside.php'); ?>
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
                    <span class="active"></span>
                </li>
            </ul>
            <?php require_once('public/course_nav.php'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="dashboard-stat2 bordered anaysisTable1">
                        <div class="courseTeacher">
                            <h4><?PHP echo get_string('resource_statistics', 'block_data_screen');?></h4>
                            <input type="button" class="form-control pull-right download download1" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                        </div>
                        <table class="table table-bordered activeTable" >
                            <tbody class="tableHead">
                            <tr class="active ">
                                <td><?PHP echo get_string('theme', 'block_data_screen');?></td>
                                <td><?PHP echo get_string('resource', 'block_data_screen');?></td>
                                <td><?PHP echo get_string('discussion', 'block_data_screen');?></td>
                                <td><?PHP echo get_string('assign', 'block_data_screen');?></td>
                                <td><?PHP echo get_string('quiz', 'block_data_screen');?></td>
                                <td>hvp</td>
                                <td><?PHP echo get_string('other', 'block_data_screen');?></td>
                            </tr>
                            </tbody>
                            <tbody class="scroll heightScroll1">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="dashboard-stat2 bordered anaysisTable1">
                        <div class="courseTeacher">
                            <h4><?PHP echo get_string('study_resource_statistics', 'block_data_screen');?></h4>
                            <input type="button" class="form-control pull-right download download2" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                        </div>
                        <div class="tableStatistics">
                            <table class="table table-bordered tableStatistic">
                                <tbody class="tableHead">
                                <tr class="active">
                                    <td class="fontCenter" ><?PHP echo get_string('theme', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('resource_name', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('access_user', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('access_num', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('download_num', 'block_data_screen');?></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered heightScroll2 tableStatistic1">
                                <tbody class="tableHead ">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="dashboard-stat2 bordered anaysisTable1">
                        <div class="courseTeacher">
                            <h4><?PHP echo get_string('quiz_assign_statistics', 'block_data_screen');?></h4>
                            <input type="button" class="form-control pull-right download download3" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                        </div>
                        <div class="testStatistics">
                            <table class="table table-bordered testStatistic">
                                <tbody class="tableHead">
                                <tr class="active">
                                    <td class="fontCenter"><?PHP echo get_string('theme', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('quiz_assign_name', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('post_num', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('student_num', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('avg_grade', 'block_data_screen');?></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered heightScroll3 testStatistic1">
                                <tbody class="tableHead">
                                </tbody>
                            </table>
                            <table class="table table-bordered testStatistic2">
                                <tbody class="heightScroll4">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="dashboard-stat2 bordered anaysisTable1">
                        <div class="courseTeacher">
                            <h4><?PHP echo get_string('discussion_statistics', 'block_data_screen');?></h4>
                            <input type="button" class="form-control pull-right download download4" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                        </div>
                        <div class="discussStatistics">
                            <table class="table table-bordered discussStatistic" >
                                <tbody class="tableHead">
                                <tr class="active">
                                    <td class="fontCenter"><?PHP echo get_string('theme', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('discussion_name', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('student_post', 'block_data_screen');?>、<?PHP echo get_string('return_card', 'block_data_screen');?></td>
                                    <td class="fontCenter"><?PHP echo get_string('teacher_post', 'block_data_screen');?>、<?PHP echo get_string('return_card', 'block_data_screen');?></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered scroll1 heightScroll5 discussStatistic1">
                                <tbody class="tableHead">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE BASE CONTENT -->
        </div>
        <!-- END CONTENT BODY -->
    </div>
</div>
<!-- END CONTAINER -->
<?php require_once('public/page_footer.php'); ?>
<script src="amd/layouts/layout4/scripts/jquery.table2excel.js"></script>
<?php require_once('public/course_publicjs.php'); ?>
<script>
    $(".heightScroll1").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1"});
    $(".heightScroll2").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1"});
    $(".heightScroll3").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1"});
    $(".heightScroll4").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1"});
    $(".heightScroll5").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1"});

    $('.download1').click(function () {
        $('.activeTable').table2excel({
            exclude: 'noExl',
            name: 'Excel Document Name.xlsx',
            filename: "<?PHP echo get_string('resource_statistics', 'block_data_screen');?>",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    });
    $('.download2').click(function () {
        $('.tableStatistics').table2excel({
            exclude: 'noExl',
            name: 'Excel Document Name.xlsx',
            filename: "<?PHP echo get_string('study_resource_statistics', 'block_data_screen');?>",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    });
    $('.download3').click(function () {
        $('.testStatistics').table2excel({
            exclude: 'noExl',
            name: 'Excel Document Name.xlsx',
            filename: "<?PHP echo get_string('quiz_assign_statistics', 'block_data_screen');?>",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    });
    $('.download4').click(function () {
        $('.discussStatistics').table2excel({
            exclude: 'noExl',
            name: 'Excel Document Name.xlsx',
            filename: "<?PHP echo get_string('discussion_statistics', 'block_data_screen');?>",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    });
    var teahUrl = window.location.search;
    var courseId = teahUrl.slice(1).split('=')[1];

    if (courseId != 0) {
        getActivityAanalysis(courseId);
    } else {
        $('.page-title h1').text("<?PHP echo get_string('no_course', 'block_data_screen');?>");
    }

    getActivityAanalysis(courseId);
    function getActivityAanalysis(courseId){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_activity_analysis',
            'args': {
                'id':courseId
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
                    $('.page-title h1').text(result.course_name);
                    $('span.active').text(result.course_name);
                    $('title').text(result.course_name);
                    var resourseStatistics = result.resource_statistics;
                    showResourse(resourseStatistics);
                    var studyStatistic = result.study_statistics;
                    showStudyStatistis(studyStatistic);
                    var quizStatistics = result.assign_quiz_statistics;
                    showQuizStatistics(quizStatistics);
                    var quizTotal = result.assign_quiz_total;
                    showQuizTotal(quizTotal);
                    var forumStatistics = result.forum_statistics;
                    showForumStat(forumStatistics);
                }
              }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showResourse(resourseStatistics){
        var resourseTable = '';
        if(!resourseStatistics){
            resourseStatistics = [];
        }
        for(var i=0;i<resourseStatistics.length;i++){
            var item = resourseStatistics[i];
            var itemS = item.item;
            var assign = itemS.assign;
            var assignHtml = '';
            assignHtml += "<span class=\"totalNum\">"+ assign.counts +"</span><br>\n"+
                "<span class=\"totalName\"><?PHP echo get_string('total', 'block_data_screen');?></span><br>\n";
            var forum = itemS.forum;
            var forumHtml = "";
            forumHtml += "<span class=\"totalNum\">"+ forum.counts +"</span><br>\n"+
                "<span class=\"totalName\"><?PHP echo get_string('total', 'block_data_screen');?></span><br>\n"+
                "<span class=\"resourceItem h5p\"><?PHP echo get_string('topics_num', 'block_data_screen');?>："+ forum.discussions +"</span>\n"+
                "<span class=\"resourceItem file\"><?PHP echo get_string('reply', 'block_data_screen');?>："+ forum.reply +"</span>\n";
            var other = itemS.other;
            var otherHtml = '';
            otherHtml +=    "<span class=\"totalNum\">"+ other +"</span><br>\n"+
                "<span class=\"totalName\"><?PHP echo get_string('total', 'block_data_screen');?></span><br>\n";
            var quiz = itemS.quiz;
            var quizHtml = "";
            quizHtml += "<span class=\"totalNum\">"+ quiz.counts +"</span><br>\n"+
                "<span class=\"totalName\"><?PHP echo get_string('total', 'block_data_screen');?></span><br>\n";
            var resource = itemS.resource;
            var resourceHtml = '';
            resourceHtml += "<span class=\"totalNum\">"+ resource.counts +"</span><br>\n"+
                "<span class=\"totalName\"><?PHP echo get_string('total', 'block_data_screen');?></span><br>\n"+
                "<span class=\"resourceItem file\"><?PHP echo get_string('file', 'block_data_screen');?>："+ resource.resource +"</span>\n"+
                "<span class=\"resourceItem webPage\"><?PHP echo get_string('page', 'block_data_screen');?>："+ resource.page +"</span><br>\n"+
                "<span class=\"resourceItem folder\"><?PHP echo get_string('folder', 'block_data_screen');?>："+ resource.folder +"</span>\n"+
                "<span class=\"resourceItem addressUrl\"><?PHP echo get_string('page_address', 'block_data_screen');?>："+ resource.url +"</span>\n";
            var hvp = itemS.hvp;
            var hvpHtml = '';
            hvpHtml += "<span class=\"totalNum\">"+ hvp.counts +"</span><br>\n"+
                "<span class=\"totalName\"><?PHP echo get_string('total', 'block_data_screen');?></span><br>\n";
            resourseTable += "<tr class=\"titleTr\">\n"+
                "<td class=\"titleTd\">"+ item.name +"</td>\n"+
                "<td class=\"resourceTd\">\n"+ resourceHtml +
                "</td>\n"+
                "<td>\n"+ forumHtml +
                "</td>\n"+
                "<td>\n"+ assignHtml +
                "</td>\n"+
                "<td>\n"+ quizHtml +
                "</td>\n"+
                "<td>\n"+ hvpHtml +
                "</td>\n"+
                "<td>\n"+ otherHtml +
                "</td>\n"+
                "</tr>"
        }
        $('.heightScroll1').html(resourseTable);
        var tableHeight = $('.activeTable').height();
    }
    function showStudyStatistis(studyStatistic){
        var studyStatHtml = '';
        if(!studyStatistic){
            studyStatistic = [];
        }
        for(var i=0;i<studyStatistic.length;i++){
            var itemI = studyStatistic[i];
            var itemItem = itemI.item;
            var itemJHtml = '';
            for(var j=0;j<itemItem.length;j++){
                var itemJ = itemItem[j];
                if(j >= 1){
                    itemJHtml += "<tr class=\"\">\n"+
                        "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/folder_icon@2x.png\" alt=\"\">"+ itemJ.name +"</td>\n"+
                        "<td>"+ itemJ.visiter_num +"</td>\n"+
                        "<td>"+ itemJ.access_num +"</td>\n"+
                        "<td>"+ itemJ.download_num +"</td>\n"+
                        "</tr>"
                }
            }
            if(itemItem.length == 0){
                studyStatHtml += "<tr class=\"titleTr\">\n"+
                    "<td rowspan=\"\" class=\"fontLeft\"><span>"+ itemI.name +"</span></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<tr>\n"+
                    "<td colspan=\"5\" class=\"empty\"></td>\n"+
                    "</tr>"
            }else(
                studyStatHtml +=  "<tr class=\"titleTr\">\n"+
                    "<td rowspan=\""+ itemItem.length +"\" class=\"fontLeft\"><span>"+ itemI.name +"</span></td>\n"+
                    "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/resourceblue_icon@2x.png\" alt=\"\">"+ itemItem[0].name +"</td>\n"+
                    "<td>"+ itemItem[0].visiter_num +"</td>\n"+
                    "<td>"+ itemItem[0].access_num +"</td>\n"+
                    "<td>"+ itemItem[0].download_num +"</td>\n"+
                    "</tr>\n"+ itemJHtml +
                    "<tr>\n"+
                    "<td colspan=\"5\" class=\"empty\"></td>\n"+
                    "</tr>"
            )
        }
        $('.tableStatistic1 tbody').html(studyStatHtml);
    }

    function showQuizStatistics(quizStatistics){
        var quizStatHtml = '';
        if(!quizStatistics){
            quizStatistics = [];
        }
        for(var i=0;i<quizStatistics.length;i++){
            var itemI = quizStatistics[i];
            var itemItem = itemI.item;
            var itemJHtml = '';
            for(var j=0;j<itemItem.length;j++){
                var itemJ = itemItem[j];
                if(j >= 1){
                    itemJHtml += "<tr class=\"\">\n"+
                        "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/folder_icon@2x.png\" alt=\"\">"+ itemJ.name +"</td>\n"+
                        "<td>"+ itemJ.posts +"</td>\n"+
                        "<td>"+ itemJ.students +"</td>\n"+
                        "<td>"+ itemJ.avg +"</td>\n"+
                        "</tr>"
                }
            }
            if(itemItem.length == 0){
                quizStatHtml += "<tr class=\"titleTr\">\n"+
                    "<td rowspan=\"\" class=\"fontLeft\"><span>"+ itemI.name +"</span></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<tr>\n"+
                    "<td colspan=\"5\" class=\"empty\"></td>\n"+
                    "</tr>"
            }else{
                quizStatHtml +=  "<tr class=\"titleTr\">\n"+
                    "<td rowspan=\""+ itemItem.length +"\" class=\"fontLeft\"><span>"+ itemI.name +"</span></td>\n"+
                    "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/resourceblue_icon@2x.png\" alt=\"\">"+ itemItem[0].name +"</td>\n"+
                    "<td>"+ itemItem[0].posts +"</td>\n"+
                    "<td>"+ itemItem[0].students +"</td>\n"+
                    "<td>"+ itemItem[0].avg +"</td>\n"+
                    "</tr>\n"+ itemJHtml +
                    "<tr>\n"+
                    "<td colspan=\"5\" class=\"empty\"></td>\n"+
                    "</tr>"
            }
        }
        $('.testStatistic1 tbody').html(quizStatHtml);
    }

    function showQuizTotal(quizTotal){
        var quizTotalHtml = '';
        var quizTotalHtml1 = '';
        var item = quizTotal.item;
        quizTotalHtml = "<tr class=\"active\">\n"+
            "<td rowspan=\"3\" class=\"static\"><img class=\"totalStatic\" src=\"amd/layouts/layout4/img/total_icon@2x.png\" alt=\"\"> 统计</td>\n"+
            "<td class=\"fontCenter\"><?PHP echo get_string('name', 'block_data_screen');?></td>\n"+
            "<td class=\"fontCenter\"><?PHP echo get_string('total_post', 'block_data_screen');?></td>\n"+
            "<td class=\"fontCenter\"><?PHP echo get_string('student_num', 'block_data_screen');?></td>\n"+
            "<td class=\"fontCenter\"><?PHP echo get_string('total_avg_grade', 'block_data_screen');?></td>\n"+
            "</tr>\n";
        for(var i=0;i<item.length;i++){
            var itemI = item[i];
            quizTotalHtml1 += "<tr>\n"+
                "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/activity_icon@2x.png\" alt=\"\">"+ itemI.name +"</td>\n"+
                "<td>"+ itemI.posts +"</td>\n"+
                "<td>"+ itemI.students +"</td>\n"+
                "<td>"+ itemI.avg +"</td>\n"+
                "</tr>"
        }
        $('.heightScroll4').html(quizTotalHtml + quizTotalHtml1);
    }

    function showForumStat(forumStatistics){
        var forumStatHtml = '';
        for(var i=0;i<forumStatistics.length;i++){
            var itemI = forumStatistics[i];
            var itemItem = itemI.item;
            var itemJHtml = '';
            for(var j=0;j<itemItem.length;j++){
                var itemJ = itemItem[j];
                if(j >= 1){
                    itemJHtml += "<tr class=\"\">\n"+
                        "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/folder_icon@2x.png\" alt=\"\">"+ itemJ.name +"</td>\n"+
                        "<td>"+ itemItem[1].student_posts +"/"+ itemItem[1].reply_student +"</td>\n"+
                        "<td>"+ itemItem[1].teacher_posts +"/"+ itemItem[1].reply_teacher +"</td>\n"+
                        "</tr>"
                }
            }
            if(itemItem.length == 0){
                forumStatHtml += "<tr class=\"titleTr\">\n"+
                    "<td rowspan=\"\" class=\"fontLeft\"><span>"+ itemI.name +"</span></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<td></td>\n"+
                    "<tr>\n"+
                    "<td colspan=\"4\" class=\"empty\"></td>\n"+
                    "</tr>";

            }else(
                forumStatHtml +=  "<tr class=\"titleTr\">\n"+
                    "<td rowspan=\""+ itemItem.length +"\" class=\"fontLeft\"><span>"+ itemI.name +"</span></td>\n"+
                    "<td class=\"blurText\"><img class=\"activity_icon\" src=\"amd/layouts/layout4/img/resourceblue_icon@2x.png\" alt=\"\">"+ itemItem[0].name +"</td>\n"+
                    "<td>"+ itemItem[0].student_posts +"、"+ itemItem[0].reply_student +"</td>\n"+
                    "<td>"+ itemItem[0].teacher_posts +"、"+ itemItem[0].reply_teacher +"</td>\n"+
                    "</tr>\n"+ itemJHtml +
                    "<tr>\n"+
                    "<td colspan=\"4\" class=\"empty\"></td>\n"+
                    "</tr>"
            )
        }
        $('.discussStatistic1 tbody').html(forumStatHtml);
    }

</script>
</body>

</html>