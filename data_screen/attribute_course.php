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
 * Attribute course
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
                    <h1><?PHP echo get_string('course_attribute', 'block_data_screen');?></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active"><?PHP echo get_string('attribute_course', 'block_data_screen');?></span>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="allCourseDiv">
                                <h4 class="allCourse">
                                    <span><?PHP echo get_string('all_course', 'block_data_screen');?></span>
                                    <small><?PHP echo get_string('explain_invalid_course', 'block_data_screen');?></small>
                                    <input type="button" class="form-control pull-right download" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                                </h4>
                            </div>
                            <div class="row m-tb-15">
                                <div class="items items_one courseProject">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('course_pro', 'block_data_screen');?>：</label>
                                    <select class="form-control inputs input_one ">
                                    </select>
                                </div>
                                <div class="items items_one courseAttr">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('course_attribute', 'block_data_screen');?>：</label>
                                    <select class="form-control inputs input_one">
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
                                <div class="items items_one numStart">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('start_num', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_one" type="text" placeholder="<?PHP echo get_string('please_input', 'block_data_screen');?>">
                                </div>
                                <div class="items search">
                                    <input class="form-control searchInput" type="button" value="<?PHP echo get_string('search', 'block_data_screen');?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 table-responsive-xl">
                                    <table class="table table-bordered table-striped attrTable">
                                        <thead>
                                        <tr>
                                            <td><?PHP echo get_string('course_id', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('full_name', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('short_name', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('course_start', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('course_end', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('teachers', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('course_attribute', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('start_num', 'block_data_screen');?></td>
                                        </tr>
                                        </thead>
                                        <tbody class="content">
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
<!-- END THEME LAYOUT SCRIPTS -->
<script>
    $("title").html("<?PHP echo get_string('attribute_course', 'block_data_screen');?>");
    getApi();
    function getApi(){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_get_tags',
            'args': {}
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                var courseProjectHtml ='';
                var courseAttrHtml = '';
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var course_pro = result.course_pro;
                    var course_att = result.course_att;
                    if(!course_att){
                        course_att=[];
                    }
                    if(!course_pro){
                        course_pro=[];
                    }
                    courseProjectHtml = "<option style=\"\" value=\"\"><?PHP echo get_string('all', 'block_data_screen');?></option>\n";
                    for(var i=0;i<course_pro.length;i++){
                        var item = course_pro[i];
                        courseProjectHtml += "<option class=\"courseproject\" value=\"\" id=\""+ item.id +"\">"+ item.name +"</option>"
                        $('.courseProject select').html(courseProjectHtml);
                    }
                    courseAttrHtml = "<option style=\"\" value=\"\"><?PHP echo get_string('all', 'block_data_screen');?></option>\n";
                    for(var j=0;j<course_att.length;j++){
                        var item1= course_att[j];
                        courseAttrHtml += "<option class=\"courseArr\" value=\"\" id=\""+ item1.id +"\">"+ item1.name +"</option>"
                        $('.courseAttr select').html(courseAttrHtml);
                    }
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
    getAttrCourse();
    function getAttrCourse(course_pro_id,course_att_id,start_time_text,end_time_text,openTimes,curPageId){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_attribute_course',
            'args': {
                'course_pro': course_pro_id,
                'course_att': course_att_id,
                'start_time': start_time_text,
                'end_time': end_time_text,
                'open_times': openTimes,
                'page': curPageId,
                'pagesize':'10'
            }
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                var contentHtml = '';
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    var data = result.data;
                    if(!data){
                        data = [];
                    }
                    if(data.length>=1){
                      for(var n=0;n<data.length;n++){
                        var item2 = data[n];
                        if(item2.start_time=='1970-01-01')item2.start_time='无'
                        if(item2.end_time=='1970-01-01')item2.end_time='无'
                        contentHtml += "<tr>\n"+
                            "<td>"+ item2.course_id +"</td>\n"+
                            "<td class=\"all-name\"><a href=\"/blocks/data_screen/course_detail.php?id="+item2.course_id+"\">"+ item2.fullname +"</a></td>\n"+
                            "<td>"+ item2.shortname +"</td>\n"+
                            "<td>"+ item2.start_time +"</td>\n"+
                            "<td>"+ item2.end_time +"</td>\n"+
                            "<td>"+ item2.teachers +"</td>\n"+
                            "<td>"+ item2.tags +"</td>\n"+
                            "<td>"+ item2.open_times +"</td>\n"+
                            "</tr>"
                     }
                    }else{
                      contentHtml += "<tr>\n"+
                            "<td colspan=\"8\" style=\"text-align:center;\">暂无数据</td>\n"+
                            "</tr>"
                    }
                    
                    $('tbody.content').html(contentHtml);
                    var page = result.page;
                    showPage(course_pro_id,course_att_id,start_time_text,end_time_text,openTimes,page);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showPage(course_pro_id,course_att_id,start_time_text,end_time_text,openTimes,page){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getAttrCourse(course_pro_id,course_att_id,start_time_text,end_time_text,openTimes,curPageId);
          }
      })
    }

    $('.searchInput').click(function () {
        var course_pro_id = $('.courseProject select option.courseproject:selected').attr('id');
        var course_att_id = $('.courseAttr select option.courseArr:selected').attr('id');
        var start_time_text = $('.startTime input').val();
        var end_time_text = $('.endTime input').val();
        var openTimes = $('.numStart input').val();
        var curPageId = $('.current').text();
        if(curPageId == ""){
            curPageId = 1;
        }
        if (course_pro_id == null) {
            course_pro_id = 0;
        }
        if (course_att_id == null) {
            course_att_id = 0;
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
        if (openTimes == '') {
            openTimes = -1;
        }
        getAttrCourse(course_pro_id, course_att_id, start_time_text, end_time_text, openTimes, curPageId);
    });

    $('.download').click(function () {
        getDownload();
    });
    function getDownload() {
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_attribute_course',
            'args': {
                'pagesize':'0'
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
                    var data = result.data;
                    var jsonData = data;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('full_name', 'block_data_screen');?>",
                        "<?PHP echo get_string('short_name', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_start', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_end', 'block_data_screen');?>",
                        "<?PHP echo get_string('teachers', 'block_data_screen');?>",
                        "<?PHP echo get_string('start_num', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_attribute', 'block_data_screen');?>"
                    ];
                    JSONToExcelConvertor(jsonData, "<?PHP echo get_string('attribute_course', 'block_data_screen');?>", title);
                } else {}
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
</script>
</body>
</html>