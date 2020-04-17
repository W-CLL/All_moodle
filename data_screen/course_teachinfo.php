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
            <div class="note note-info my-10">
                <h3><?PHP echo get_string('course_completion', 'block_data_screen');?></h3>
            </div>
            <div class="row sectionDiv"></div>
        </div>
    </div>
</div>
<?php require_once("public/page_footer.php"); ?>
<?php require_once("public/course_publicjs.php"); ?>
<script>
    var courseID = window.location.search.slice(1).split('=')[1];
    if(courseID != 0){
        getTeaherSituDetail(courseID);
    }else{
        $('.page-title h1').text("<?PHP echo get_string('no_course', 'block_data_screen');?>");
    }
    function getTeaherSituDetail(courseID){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_course_teachinfo',
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
                    var section = result.section;
                    // console.log(course);
                    $('.page-title h1, .courseName, #course-name').text(course.fullname);
                    $("title").html(course.fullname);
                    showSection(section);
                }
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
            var sectionClass = 'col-md-6 col-sm-12 col-xs-12 section-item';
            var itemI = section[i];
            var currItem = itemI.item;
            html += "<div class=\"" + sectionClass + "\">\n" +
                "<div class=\"portlet light bordered my-10\">\n" +
                "<div class=\"portlet-title\"><div class=\"caption\">\n" +
                "<span class=\"caption-subject font-dark bold uppercase\">" + itemI.name + "</span>\n" +
                "</div></div>\n" +
                "<div class=\"portlet-body util-btn-margin-bottom-5\"><table class=\"table table-hover common\">\n" +
                "<thead class=\"thead-item\">\n" +
                "<tr>\n" +
                "<td class=\"fontleft1 fontColor thead-td-1\"><?PHP echo get_string('course_activity', 'block_data_screen');?></td>\n" +
                "<td class=\"fontColor textCenter thead-td-2\"><?PHP echo get_string('student_completion', 'block_data_screen');?></td>\n" +
                "<td class=\"fontColor textCenter thead-td-3\"><?PHP echo get_string('pv', 'block_data_screen');?></td>\n" +
                "</tr>\n" +
                "</thead>\n" +
                "<tbody class=\"sectionItem\">\n";
            if(currItem.length >= 1){
                for(var n=0;n<currItem.length;n++){
                    var itemN = currItem[n];
                    html +=   "<tr>\n"+
                        "<td class=\"fontleft \"><p title=\""+ itemN.name +"\"><i class=\"customicon customicon-20 customicon-activityitem\"></i>"+ itemN.name +"</p></td>\n"+
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
                "</table></div>\n"+
                "</div>\n"+
                "</div>"
            // }

            $('.sectionDiv').html(html);
            //scroll
            $(".sectionItem").niceScroll({cursorborder:"#e7ecf1",cursorcolor:"#e7ecf1",boxzoom:true}); // First scrollable DIV
        }
    }
</script>
</body>
</html>