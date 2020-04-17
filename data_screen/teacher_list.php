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
<link rel="stylesheet" href="amd/layouts/layout4/css/atributeClass.css">
<link rel="stylesheet" href="amd/layouts/layout4/css/paging.css">
<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<?php require_once('public/page_header.php'); ?>
<!-- END HEADER -->
<div class="page-container">
    <?php require_once('public/platform_leftside.php'); ?>
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <div class="page-head">
                <!-- BEGIN PAGE TITLE -->
                <div class="page-title">
                    <h1><?PHP echo get_string('teacher_profile', 'block_data_screen');?></h1>
                </div>
                <!-- END PAGE TITLE -->
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active"><?PHP echo get_string('teacher_profile', 'block_data_screen');?></span>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered scl">
                        <div class="display totalNum">
                            <div class="row m-tb-15 Option">
                                <div class="items items_two department">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('college', 'block_data_screen');?>：</label>
                                    <select class="form-control inputs input_one teahSelect" name="" id="">
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="items items_two teaName">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('teacher_name', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_one" type="text" placeholder="<?PHP echo get_string('please_input', 'block_data_screen');?>">
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
            </div>
            <div class="row teacherOne">
            </div>
            <div id="page"></div>
        </div>
        <!-- END CONTENT BODY -->
    </div>
</div>
<!-- END CONTAINER -->
<?php require_once('public/page_footer.php'); ?>
<?php require_once('public/platform_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/paging.js"></script>
<script>
    $("title").html('<?PHP echo get_string('teacher_profile', 'block_data_screen');?>');
    getTeahDepart();
    function getTeahDepart(curPageId){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_get_dept',
            'args': {}
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var selectHtml = "";
                    selectHtml = "<option><?PHP echo get_string('choose_college', 'block_data_screen');?></option>"
                    for(var i=0;i<result.length;i++){
                        var item = result[i];
                        selectHtml += "<option class=\"optionItems\" value=\"\" id=\""+ i +"\">"+ item +"</option>"
                    }
                    $('.teahSelect').html(selectHtml);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    getTeaherOne();
    function getTeaherOne(curPageId,department,teaName){
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_teacher_list',
            'args': {
                'dept':department,
                'name':teaName,
                'page':curPageId,
                'pagesize':10,
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
                    var teacher_list = result.teacher_list;
                    showTeahList(teacher_list);
                    var page = result.page;
                    showPage(page,department,teaName);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
    function showTeahList(teacher_list){
        var html = "";
        if(!teacher_list){
            teacher_list=[];
        }
        for(var i=0;i<teacher_list.length;i++){
            var item = teacher_list[i];
            html += "<div class=\"col-xs-1-5\">\n"+
                "<div class=\"content\" id=\"" + item.teacher_id + "\">\n" +
                "<div class=\"headImg\"><a href=\"teacher_detail.php?id="+ item.teacher_id +"\"><img class=\"teacherhead_icon\" src=\""+ item.url +"\" alt=\"\"></a></div>\n"+
                "<p class=\"teacherName\" id=\""+ item.teacher_id +"\"><a class=\"teacherA\" href=\"teacher_detail.php?id="+ item.teacher_id +"\">"+ item.name +"</a> </p>\n"+
                "<p class=\"collegeName\">"+ item.dept +"</p>\n"+
                "<div class=\"bottom\">\n"+
                "<ul class=\"courseData\">\n"+
                "<li class=\"asd\">\n"+
                "<p class=\"num\"><a class=\"teacherA\" href=\"\">"+ item.course_num +"</a></p>\n"+
                "<p class=\"kind\">\n"+
                "<span class=\"oneIcon\"></span>\n"+
                "<span class=\"textTitle\"><?PHP echo get_string('opening_course', 'block_data_screen');?></span>\n"+
                "</p>\n"+
                "</li>\n"+
                "<li class=\"shu\"></li>\n"+
                "<li class=\"\">\n"+
                "<p class=\"num\"><a class=\"teacherA\" href=\"\">"+ item.student_num +"</a></p>\n"+
                "<p class=\"kind\">\n"+
                "<span class=\"twoIcon\"></span>\n"+
                "<span class=\"textTitle\"><?PHP echo get_string('students', 'block_data_screen');?></span>\n"+
                "</p>\n"+
                "</li>\n"+
                "<li class=\"shu\"></li>\n"+
                "<li class=\"\">\n"+
                "<p class=\"num\"><a class=\"teacherA\" href=\"\">"+ item.res_avt_num +"</a></p>\n"+
                "<p class=\"kind\">\n"+
                "<span class=\"threeIcon\"></span>\n"+
                "<span class=\"textTitle\"><?PHP echo get_string('activity_resource', 'block_data_screen');?></span>\n"+
                "</p>\n"+
                "</li>\n"+
                "</ul>\n"+
                "</div>\n"+
                "</div>\n"+
                "</div>"
        }
        $('.teacherOne').html(html);
        $('.content').click(function(){
            var teacherId = $(this).attr('id');
            window.location.href = "teacher_detail.php?id=" + teacherId;
        });
    }
    function showPage(page,department,teaName){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getTeaherOne(curPageId,department,teaName);
          }
      })
    }

    $('.searchInput').click(function(){
        var department = $('.department select option.optionItems:selected').text();;
        var teaName = $('.teaName input').val();
        var curPageId = $('.current').text();
        if(curPageId == ""){
            curPageId = 1;
        }

        if(department == ''){
            department = '0';
        }
        if(teaName == ''){
            teaName = '0';
        }
        getTeaherOne(curPageId,department,teaName);
    });

    $('.schDownload').click(function () {
        getDownload();
    });
    function getDownload() {
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_teacher_list',
            'args': {
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
                    var teacher_list = result.teacher_list;
                    var jsonData = teacher_list;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('teacher', 'block_data_screen');?>",
                        "<?PHP echo get_string('college', 'block_data_screen');?>",
                        "<?PHP echo get_string('opening_course_num', 'block_data_screen');?>",
                        "<?PHP echo get_string('students', 'block_data_screen');?>",
                        "<?PHP echo get_string('activity_resource_num', 'block_data_screen');?>"
                    ]
                    var filter = ['url']
                    JSONToExcelConvertor(jsonData, "<?PHP echo get_string('teacher_profile', 'block_data_screen');?>", title, filter);
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