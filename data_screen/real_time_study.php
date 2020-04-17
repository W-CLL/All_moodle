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
 * Real time study
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
                        <h1><?PHP echo get_string('real_time_study', 'block_data_screen'); ?></h1>
                    </div>
                </div>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="platform_overview.php"><?PHP echo get_string('total_platform', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="active"><?PHP echo get_string('real_time_study', 'block_data_screen'); ?></span>
                    </li>
                </ul>
                <!-- BEGIN DASHBOARD STATS 1-->
                <div class="clearfix"></div>
                <!-- END DASHBOARD STATS 1-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-social-dribbble font-green hide"></i>
                                    <span class="caption-subject font-dark bold uppercase"><?PHP echo get_string('real_time_study', 'block_data_screen'); ?></span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-scrollable">
                                    <table class="zoomuser-table table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th> id </th>
                                                <th> email </th>
                                                <th> 用户 </th>
                                                <th> 登录时间 </th>
                                                <th> 操作 </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="page"></div>
                        </div>
                    </div>
                </div>
                <!-- END PAGE BASE CONTENT -->
            </div>
        </div>
    </div>
    <?php require_once('public/page_footer.php'); ?>
    <?php require_once('public/platform_publicjs.php'); ?>
    <script src="amd/layouts/layout4/scripts/paging.js"></script>

    <script>
        getTeaherSitu();

        function getTeaherSitu() {
            var role = $("li.active").val();
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_get_semester',
                'args': {
                    'role': role,
                    'user': '<?PHP echo $USER->id; ?>'
                }
            };
            $.ajax({
                type: "POST",
                contentType: "application/json;",
                url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data;
                        var leftHtml = '';
                        var course_list = [];
                        var courseId = '';
                        for (var i = 0; i < result.length; i++) {
                            var item = result[i];
                            course_list = item.course_list;
                            if (course_list.length >= 1) {
                                for (var j = 0; j < course_list.length; j++) {
                                    var itemJ = course_list[j];
                                    if (i == 0) {
                                        if (j === 0) {
                                            courseId = itemJ.id;
                                        }
                                    }
                                }
                            }
                        }
                        $('.course').click(function() {
                            window.location.href = "course_detail.php" + "?id=" + courseId;
                        });
                    } else {}
                },
                error: function(e) {
                    alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
                }
            });
        }

        var curPageId = 1;
        $("title").html("<?PHP echo get_string('real_time_study', 'block_data_screen'); ?>");

        getzoomuserlist(curPageId);

        function getzoomuserlist(curPageId) {
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_online_learning',
                'args': {
                    'page': curPageId,
                    'pagesize': 12
                }
            };
            $.ajax({
                type: "POST",
                contentType: "application/json;",
                url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    //console.log(result);
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data;
                        var list = result.data;
                        var len = list.length;
                        var str = "";
                        for (i = 0; i < len; i++) {
                            str += "<tr>";
                            str += "<td>" + list[i]['id'] + "</td>";
                            str += "<td>" + list[i]['email'] + "</td>";
                            str += "<td>" + list[i]['firstname'] + "</td>";
                            str += "<td>" + list[i]['login'] + "</td>";
                            str += "<td><a href='/user/profile.php?id=" + list[i]['id'] + "'>" + "<?PHP echo get_string('view', 'block_data_screen'); ?>" + "</a></td>";
                            str += "</tr>";
                        }
                        $('.zoomuser-table tbody').html(str);

                        var page = result.page;
                        $("#page").paging({
                            pageNo: page.cur_page,
                            totalPage: page.max_page,
                            callback: function(curPageId) {
                                getzoomuserlist(curPageId);
                            }
                        })
                    } else {}
                },
                error: function(e) {
                    alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
                }
            });
        }
    </script>
</body>

</html>