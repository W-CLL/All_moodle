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
 * College list
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
                    <h1><?PHP echo get_string('college_profile', 'block_data_screen');?></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active"><?PHP echo get_string('college_profile', 'block_data_screen');?></span>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered scl">
                        <div class="display totalNum">
                            <div class="row m-tb-15 Option">
                                <div class="items items_two startTime">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('start_time', 'block_data_screen');?>：</label>
                                    <input type="date" class="form-control inputs input_one">
                                </div>
                                <div class="items items_two endTime">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('end_time', 'block_data_screen');?>：</label>
                                    <input type="date" class="form-control inputs input_one">
                                </div>

                                <div class="items items_two schoolName">
                                    <label for="" class="labels labels_one"><?PHP echo get_string('college_name', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_one" type="text" placeholder="<?PHP echo get_string('please_input', 'block_data_screen');?>">
                                </div>

                                <div class="items search search_scl">
                                    <input class="form-control searchInput schlSearch" type="button" value="<?PHP echo get_string('search', 'block_data_screen');?>">
                                </div>

                                <div class="items download  mt-0">
                                    <input type="button" class="form-control pull-right schDownload" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row xueYuan">
            </div>
            <div id="page"></div>
        </div>
    </div>
</div>
<?php require_once('public/page_footer.php'); ?>
<?php require_once('public/platform_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/paging.js"></script>
<script>
    $("title").html('<?PHP echo get_string('college_profile', 'block_data_screen');?>');
    getSchProfileOne();
    function getSchProfileOne(start_time_text,end_time_text,shcoolName,curPageId){
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_college_list',
            'args': {
                'page':curPageId,
                'pagesize':15,
                'start_time': start_time_text,
                'end_time': end_time_text,
                'name':shcoolName,
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
                    var collegeList = result.college_list;
                    showXueYuan(collegeList);
                    if(collegeList == ''){
                      $('#page').css('display','none');
                    }else{
                      $('#page').css('display','block');
                    }
                    var page = result.page;
                    showPage(start_time_text,end_time_text,shcoolName,page);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
    function showPage(start_time_text,end_time_text,shcoolName,page){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getSchProfileOne(start_time_text,end_time_text,shcoolName,curPageId);
          }
      })
    }
    function showXueYuan(collegeList){
        var html = "";
        if(!collegeList){
            collegeList=[];
        }
        for(var i=0;i<collegeList.length;i++){
            var item = collegeList[i];
            if(item.idnumber == null || item.idnumber == ''){
              var idumHtml = ''
            }else{
              var idumHtml = "<span class=\"idnum\">ID:<span class=\"idText\">"+ item.idnumber +"</span></span>"
            }
            html += "<div class=\"col-xs-1-5\">\n"+
                "<div class=\"collageContent\"  id=\"" + item.id + "\">\n" +
                "<div class=\"topcontent\">\n"+
                "<div class=\"left\">\n"+
                "<h4><a class=\"teacherA fontWhite\" id=\""+ item.id +"\" href=\"college_detail.php?id="+ item.id +"\">"+ item.name +"</a></h4>\n"+ idumHtml +
                // "<span class=\"idnum\">ID:<span class=\"idText\">"+ item.idnumber +"</span></span>\n"+
                "</div>\n"+
                "<img class=\"schoolcard_img\" src=\"amd/layouts/layout4/img/schoolcard_img@2x.png\" alt=\"\">\n"+
                "</div>\n"+
                "<div class=\"bottom\">\n"+
                "<ul class=\"courseData\">\n"+
                "<li class=\"asd\">\n"+
                "<p class=\"num\"><a class=\"teacherA\" href=\"\">"+ item.course_num +"</a></p>\n"+
                "<p class=\"kind\">\n"+
                "<span class=\"oneIcon\"></span>\n"+
                "<span class=\"textTitle\"><?PHP echo get_string('course_num', 'block_data_screen');?></span>\n"+
                "</p>\n"+
                "</li>\n"+
                "<li class=\"shu\"></li>\n"+
                "<li class=\"\">\n"+
                "<p class=\"num\"><a class=\"teacherA\" href=\"\">"+ item.teacher_num +"</a></p>\n"+
                "<p class=\"kind\">\n"+
                "<span class=\"twoIcon\"></span>\n"+
                "<span class=\"textTitle\"><?PHP echo get_string('teacher_num', 'block_data_screen');?></span>\n"+
                "</p>\n"+
                "</li>\n"+
                "<li class=\"shu\"></li>\n"+
                "<li class=\"\">\n"+
                "<p class=\"num\"><a class=\"teacherA\" href=\"\">"+ item.student_num +"</a></p>\n"+
                "<p class=\"kind\">\n"+
                "<span class=\"threeIcon\"></span>\n"+
                "<span class=\"textTitle\"><?PHP echo get_string('student_num', 'block_data_screen');?></span>\n"+
                "</p>\n"+
                "</li>\n"+
                "</ul>\n"+
                "</div>\n"+
                "</div>\n"+
                "</div>"
        }
        $('.xueYuan').html(html);
        $('.collageContent').click(function(){
            var courseId = $(this).attr('id');
            window.location.href="college_detail.php?id=" + courseId;
        });
    }

    $('.schlSearch').click(function () {
        var start_time_text = $('.startTime input').val();
        var end_time_text = $('.endTime input').val();
        var shcoolName = $('.schoolName input').val();
        var curPageId = $('.current').text();
        if(curPageId == ""){
            curPageId = 1;
        }
        if (start_time_text == '') {
            start_time_text = '0';
        }
        if (end_time_text == '') {
            end_time_text = '0';
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
        if (shcoolName == '') {
            shcoolName = '0';
        }
        getSchProfileOne(start_time_text, end_time_text, shcoolName, curPageId);
    });

    $('.schDownload').click(function () {
        getDownload();
    });

    function getDownload() {
        var role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_college_list',
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
                    var collegeList = result.college_list;
                    var jsonData = collegeList;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('college', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_num', 'block_data_screen');?>",
                        "<?PHP echo get_string('teacher_num', 'block_data_screen');?>",
                        "<?PHP echo get_string('student_num', 'block_data_screen');?>"
                    ]
                    var filter = ['id']
                    JSONToExcelConvertor(jsonData, "<?PHP echo get_string('college_profile', 'block_data_screen');?>", title, filter);
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