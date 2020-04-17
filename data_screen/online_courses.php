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
 * Online course
 *
 * @package    block_data_screen
 * @copyright  2020 ckf
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
                    <h1><?PHP echo get_string('active_courses', 'block_data_screen');?></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('total_platform', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active schCollegeName"><?PHP echo get_string('online_course', 'block_data_screen');?></span>
                </li>
            </ul>
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
    $("title").html("<?PHP echo get_string('online_course', 'block_data_screen');?>");
    getSchProfileTwo();
    function getSchProfileTwo(curPageId) {
        var role = $("li.active").val();
        var oneUrl = window.location.search;
        var catId = oneUrl.slice(1).split('=')[1];
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_online_courses',
            'args': {
                'page': curPageId,
                'pagesize': 12,
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
                    showCourse(result.data);
                    var page = result.page;
                    showPage(curPageId, page);
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
            html += "<div class=\"col-md-2 courseItem\" id=\""+ item.id +"\">\n" +
                "<div class=\"classItem\">\n" +
                "<div class=\"coursePic\">\n" +
                "<a href=\" # \"><img class=\"course_img\" src=\"" + item.img + "\" alt=\"\"></a> \n" +
                "</div>\n" +
                "<div class=\"itemText\">\n" +
                "<p class=\"itemName\" id=\"" + item.id + "\"><a href=\"course_detail.php?id=" + item.id +
                "\">" + item.fullname + "</a></p>\n" +
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
    function showPage(curPageId, page){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getSchProfileTwo(curPageId);
          }
      })
    }
</script>
</body>

</html>
