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
 * Person list
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
<link rel="stylesheet" href="amd/layouts/layout4/css/paging.css">
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
                    <div class="dashboard-stat2 bordered anaysisTable1 mb-0">
                        <div class="row m-tb-15">
                            <div class="items items_two startTime">
                                <label for="" class="labels labels_one"><?PHP echo get_string('role', 'block_data_screen');?>：</label>
                                <select class="form-control inputs input_one roleSelect" name="" id="">
                                    <option value=""></option>
                                </select>
                            </div>

                            <div class="items items_two endTime">
                                <label for="" class="labels labels_one"><?PHP echo get_string('name1', 'block_data_screen');?>：</label>
                                <input class="form-control inputs input_one inputName" type="text" placeholder="<?PHP echo get_string('please_input', 'block_data_screen');?>">
                            </div>

                            <div class="items search search_scl">
                                <input class="form-control searchInput" type="button" value="<?PHP echo get_string('search', 'block_data_screen');?>">
                            </div>

                            <div class="items download1">
                                <input type="button" class="form-control pull-right download1 schDownload" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row person-list">
            </div>
            <div id="page"></div>
        </div>
    </div>
</div>
<?php require_once('public/page_footer.php'); ?>
<script src="amd/layouts/layout4/scripts/Json2Excel.js"></script>
<?php require_once('public/course_publicjs.php'); ?>
<script src="amd/layouts/layout4/scripts/paging.js"></script>
<script>
    getRole();
    function getRole(){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_get_role',
            'args': {}
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                if (typeof result[0].data != 'undefined') {
                    var role = result[0].data;
                    var roleHtml = '';
                    roleHtml = "<option><?PHP echo get_string('choose_role', 'block_data_screen');?></option>"
                    for (var i = 0; i < role.length; i++) {
                        var itemR = role[i];
                        roleHtml += "<option class=\"roleItem\" id=\"" + itemR.id + "\" value=\"\">" + itemR.name +
                            "</option>"
                    }
                    $('.roleSelect').html(roleHtml);
                } else {
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    var teahUrl = window.location.search;
    var courseId = teahUrl.slice(1).split('=')[1];
    if (courseId != 0) {
        getPersonAnalysis(courseId);
    } else {
        $('.page-title h1').text("<?PHP echo get_string('no_course', 'block_data_screen');?>");
    }
    getPersonAnalysis(courseId);
    function getPersonAnalysis(courseId,optionId,inputName,curPageId){
        var cur_role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_personal_analysis',
            'args': {
                'course':courseId,
                'role':optionId,
                'name': inputName,
                'cur_role': cur_role,
                'user': '<?PHP echo $USER->id;?>',
                'page':curPageId,
                'pagesize':12
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
                    $('.page-title h1, span.active, title').text(result.course);
                    var courseLi = result.course_list;
                    var html = '';
                    if (!courseLi) {
                        courseLi = [];
                    }
                    for (var i = 0; i < courseLi.length; i++) {
                        var personClass = 'col-xs-1-5';
                        var item = courseLi[i];
                        if ((i % 5) == 0) {
                            personClass += " pr-7 pl-15";
                        } else if ((i + 1) % 5 == 0) {
                            personClass += " pl-7 pr-15"
                        } else {
                            personClass += " pl-7 pr-7"
                        }
                        html += "<div class=\"" + personClass + "\">\n" +
                            "<a class=\"bg-white d-block personList my-10\" href=\"person_detail.php?id="+ courseId +"&userId="+ item.id +"&role="+ item.role +"\">\n" +
                            "<div class=\"media personMedia\">\n" +
                            "<div class=\"media-body\">\n" +
                            "<h4 class=\"media-heading\">" + item.name + "</h4>\n" +
                            "<p class=\"college\" title=\""+ item.dept +"\">" + item.dept + "</p>\n" +
                            "<p class=\"colorGray\"><?PHP echo get_string('id_number', 'block_data_screen')?>:&nbsp;&nbsp;<span>" + item.idnumber + "</span></p>\n" +
                            "</div>\n" +
                            "<div class=\"media-right\">\n" +
                            "<img class=\"headImg\" src=\"" + item.avatar + "\" alt=\"\">\n" +
                            "</div>\n" +
                            "</div>\n" +
                            "</a>\n" +
                            "</div>"
                    }
                    $('.person-list').html(html)
                }
                var page = result.page;
                showPage(courseId,optionId,inputName,page);
              }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    function showPage(courseId,optionId,inputName,page){
      $("#page").paging({
          pageNo:page.cur_page,
          totalPage: page.max_page,
          callback: function(curPageId) {
            getPersonAnalysis(courseId,optionId,inputName,curPageId)
          }
      })
    }

    $('.searchInput').click(function(){
        var optionId = $('.roleSelect option.roleItem:selected').attr('id');;
        var inputName = $('.inputName').val();
        if(optionId == null){
            optionId = 0;
        }
        if(inputName == ''){
            inputName = '0';
        }
        getPersonAnalysis(courseId,optionId,inputName);
    });

    $('.schDownload').click(function () {
        getDownload(courseId);
    });

    function getDownload(courseId) {
        var cur_role = $("li.active").val();
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_personal_analysis',
            'args': {
                'course':courseId,
                'cur_role': cur_role,
                'user': '<?PHP echo $USER->id;?>',
                'pagesize': 0
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
                    var courseLi = result.course_list;
                    var jsonData = courseLi;
                    var title = [
                        'ID',
                        "<?PHP echo get_string('name1', 'block_data_screen');?>",
                        "<?PHP echo get_string('college', 'block_data_screen');?>",
                        "<?PHP echo get_string('id_number', 'block_data_screen');?>",
                        "<?PHP echo get_string('role', 'block_data_screen');?> ID",
                        "<?PHP echo get_string('spend_time', 'block_data_screen');?>",
                        "<?PHP echo get_string('login', 'block_data_screen');?>",
                        "<?PHP echo get_string('grade', 'block_data_screen');?>",
                        "<?PHP echo get_string('course_completion_progress', 'block_data_screen');?>"
                    ];
                    var filter = ['avatar']
                    JSONToExcelConvertor(jsonData, "<?PHP echo get_string('person_analysis', 'block_data_screen');?>", title, filter);
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
