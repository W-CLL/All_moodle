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
 * Person detail
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
<link rel="stylesheet" href="amd/layouts/layout4/css/person.css">
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
                    <h1> </h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href=""><?PHP echo get_string('course', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active"> </span>
                </li>
            </ul>
            <?php require_once('public/course_nav.php'); ?>
            <div class="row">
                <div class="col-md-3 ">
                    <div class="dashboard-stat2 bordered left">
                        <div class="img"><img class="head" src="amd/layouts/layout4/img/studenthead_icon@2x.png" alt=""></div>
                        <div class="textLeft">
                            <p class="stuName"><a href=""> </a></p>
                            <p class="department">  </p>
                        </div>
                        <div class="progressDiv">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:">
                                </div>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('course_completion_progress', 'block_data_screen');?></div>
                                <div class="status-number greenStatus">  </div>
                            </div>
                        </div>
                        <div class="leftNum">
                            <ul>
                                <li class="stuItem">
                                    <p class="stuNum roleName"><?PHP echo get_string('student', 'block_data_screen');?></p>
                                    <p class="kind">
                                        <span class="icon oneIcon"></span>
                                        <span class="textTitle"><?PHP echo get_string('role', 'block_data_screen');?></span>
                                    </p>
                                </li>
                                <li class="shu"></li>
                                <li class="stuItem">
                                    <p class="stuNum grade colorRed">90</p>
                                    <p class="kind">
                                        <span class="icon twoIcon"></span>
                                        <span class="textTitle gradeText"><?PHP echo get_string('grade', 'block_data_screen');?></span>
                                    </p>
                                </li>

                                <li class="stuItem">
                                    <p class="stuNum loginTime"></p>
                                    <p class="kind">
                                        <span class="icon threeIcon"></span>
                                        <span class="textTitle "><?PHP echo get_string('login', 'block_data_screen');?></span>
                                    </p>
                                </li>
                                <li class="shu"></li>
                                <li class="stuItem">
                                    <p class="stuNum spendTime"></p>
                                    <p class="kind">
                                        <span class="icon fourIcon"></span>
                                        <span class="textTitle"><?PHP echo get_string('spend_time', 'block_data_screen');?></span>
                                    </p>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 pl-0">
                    <div class="dashboard-stat2 bordered pt-0">
                        <div class="row">
                            <div class="com-md-12 complete">
                                <h5><?PHP echo get_string('course_completion', 'block_data_screen');?></h5>
                            </div>
                        </div>

                        <div class="row sectionDiv">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTAINER -->
<?php require_once('public/page_footer.php'); ?>
<?php require_once('public/course_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/common.js"></script>
<script>
    var personAnalysisUrl = window.location.search;
    var personArr = personAnalysisUrl.slice(1).split('&');
    var courseId = personArr[0].split('=')[1];
    var userId = personArr[1].split('=')[1];
    var roleId = personArr[2].split('=')[1];

    if(courseId != 0){
        getPersonDetail(courseId, userId, roleId);
    }else{
        $('.page-title h1').text("<?PHP echo get_string('no_course', 'block_data_screen');?>");
    }
    getPersonDetail(courseId,userId,roleId);
    function getPersonDetail(courseId,userId,roleId){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_personal_detail',
            'args': {
                'course':courseId,
                'role':roleId,
                'id':userId
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
                    var user = result.user;
                    $('.page-title h1').text(user.course_name);
                    $("title").html(user.course_name)
                    $('span.active').text(user.course_name);
                    $('.head').attr('src',user.avatar);
                    $('.stuName').text(user.name);
                    $('.department').text(user.department);
                    $('.roleName').text(user.role);
                    if(user.role == "<?PHP echo get_string('teacher', 'block_data_screen');?>"){
                        $('.grade').text(user.completionORcreated);
                        $('.gradeText').text("<?PHP echo get_string('create_activity_num', 'block_data_screen');?>");
                    }else{
                        $('.grade').text(user.completionORcreated);
                    }
                    $('.loginTime').text(user.login);
                    $('.spendTime').text(user.spendtime + "h");
                    var section = result.section;
                    showSection(section);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showSection(section){
        var html = "";
        if(!section){
            section=[];
        }
        for(var i=0;i<section.length;i++){
            var sectionClass = 'col-md-6 col-sm-12 col-xs-12';
            var itemI = section[i];
            var currItem = itemI.item;
            // if(section[0].item.length > section[1].item.length){
            //     if((i+1)%3 == 0){
            //         sectionClass += ' fr';
            //     }
            // }else{
            //     if((i+1)%2 == 0){
            //         sectionClass += ' fr';
            //     }
            // }
            html += "<div class=\""+ sectionClass +"\">\n"+
                "<div class=\"dashboard-stat2 bordered tableDiv\">\n"+
                "<div class=\"courseTeacher\">\n"+
                "<h4>"+ itemI.name +"</h4>\n"+
                "</div>\n"+
                "<table class=\"table table-hover common\">\n"+
                "<thead class=\"thead-item\">\n"+
                "<tr>\n"+
                "<td class=\"fontleft fontColor thead-td-1\"><?PHP echo get_string('course_activity', 'block_data_screen');?></td>\n"+
                "<td class=\"fontColor textCenter thead-td-2\"><?PHP echo get_string('student_completion', 'block_data_screen');?></td>\n"+
                "<td class=\"fontColor textCenter thead-td-3\"><?PHP echo get_string('pv', 'block_data_screen');?></td>\n"+
                "</tr>\n"+
                "</thead>\n"+
                "<tbody class=\"sectionItem\">\n";
            if(currItem.length >=1){
                for(var n=0;n<currItem.length;n++){
                    var itemN = currItem[n];
                    html +=   "<tr>\n"+
                        "<td class=\"fontleft \"><p title=\""+ itemN.name +"\"><img class=\"tdIcon\" src=\"amd/layouts/layout4/img/activity_icon1@2x.png\" alt=\"\">"+ itemN.name +"</p></td>\n"+
                        "<td class=\"textCenter\">"+ itemN.completion +"</td>\n"+
                        "<td class=\"textCenter\">"+ itemN.access +"</td>\n"+
                        "</tr>\n"
                }
            } else {
                html += "<tr>\n" +
                    "<td class=\"fontleft textCenter oneTd\" colspan=\"3\"><?PHP echo get_string('no_content', 'block_data_screen');?></td>\n" +
                    "<td></td>\n" +
                    "<td></td>\n" +
                    "</tr>\n"
            }

            html +=           "</tbody>\n"+
                "</table>\n"+
                "</div>\n"+
                "</div>"
            $('.sectionDiv').html(html);
            //scroll
            $(".sectionItem").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1",boxzoom:true}); // First scrollable DIV
        }
    }

    getProcess(courseId,userId);
    function getProcess(courseId,userId){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_get_activities_completion_status',
            'args': {
                'courseid':courseId,
                'userid':userId
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
                    var statuses = result.statuses;
                    var total = statuses.length;
                    var hadComplete = 0;
                    for(var i=0;i<total;i++){
                        var item = statuses[i];
                        if(item.state == 1){
                            hadComplete = hadComplete + 1;
                        }
                    }
                    if (!hadComplete==0) {
                        hadCompleteNum = hadComplete / total;
                        hadCompleteRate = Math.round(((hadCompleteNum*100)/100) * 100) + "%";
                    } else {
                        hadCompleteRate = "0%";
                    }
                    $('.progress-bar').css('width',hadCompleteRate);
                    $('.greenStatus').text(hadCompleteRate);
                }
              }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    $('.person a').click(function(){
        window.location.href = "person_list.php?id=" + courseId;
    });


</script>
</body>

</html>