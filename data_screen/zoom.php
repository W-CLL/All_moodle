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
 * zoom
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
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left: 0px">
                <div class="page-head">
                    <div class="page-title">
                        <h1><?PHP echo get_string('zoom_title', 'block_data_screen'); ?></h1>
                    </div>
                </div>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="active"><?PHP echo get_string('zoom_title', 'block_data_screen'); ?></span>
                    </li>
                </ul>
                <!-- BEGIN DASHBOARD STATS 1-->
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                        <a class="dashboard-stat dashboard-stat-v2 blue zoomdata" data-type="1" href="#">
                            <div class="visual">
                                <i class="fa fa-comments"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" id="zoom_total">0</span>
                                </div>
                                <div class="desc"> <?PHP echo get_string('zoom_total', 'block_data_screen'); ?> </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                        <a class="dashboard-stat dashboard-stat-v2 bg-yellow-casablanca bg-font-yellow-casablanca zoomdata" data-type="2" href="#">
                            <div class="visual">
                                <i class="fa fa-comments"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" id="zoom_using">0</span>
                                </div>
                                <div class="desc"> <?PHP echo get_string('zoom_using', 'block_data_screen'); ?> </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                        <a class="dashboard-stat dashboard-stat-v2 red zoomdata" data-type="3" href="#">
                            <div class="visual">
                                <i class="fa fa-bar-chart-o"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" id="zoom_free"> 0 </span></div>
                                <div class="desc"> <?PHP echo get_string('zoom_free', 'block_data_screen'); ?> </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <a class="dashboard-stat dashboard-stat-v2 green zoommod" data-type="4" href="#">
                            <div class="visual">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" id="zoom_mod_order">0</span>
                                </div>
                                <div class="desc"> <?PHP echo get_string('zoom_mod_order', 'block_data_screen'); ?> </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <a class="dashboard-stat dashboard-stat-v2 purple zoommod" data-type="5" href="#">
                            <div class="visual">
                                <i class="fa fa-globe"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" id="zoom_mod_using">0</span>
                                </div>
                                <div class="desc"> <?PHP echo get_string('zoom_mod_using', 'block_data_screen'); ?> </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!-- END DASHBOARD STATS 1-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-social-dribbble font-green hide"></i>
                                    <span class="caption-subject font-dark bold uppercase"><?PHP echo get_string('zoom_title', 'block_data_screen'); ?></span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-scrollable">
                                    <table class="zoomuser-table table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th> id </th>
                                                <th> 学院 </th>
                                                <th> 课程名称 </th>
                                                <th> 教师姓名 </th>
                                                <th> 学生人数 </th>
                                                <th> 开始时间 </th>
                                                <th> 状态 </th>
                                                <th> 巡课 </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <table class="zoommod-table table table-striped table-hover" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th> id </th>
                                                <th> 活动名称 </th>
                                                <th> 课程名称 </th>
                                                <th> 任教老师 </th>
                                                <th> 预约类型 </th>
                                                <th> 预约时间 </th>
                                                <th> 学生人数 </th>
                                                <th> 状态 </th>
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
    <script src="amd/layouts/layout4/scripts/paging.js"></script>
    <?php require_once('public/platform_publicjs.php'); ?>
    <script>
        var type = 1;
        var curPageId = 1;
        $("title").html("<?PHP echo get_string('zoom_title', 'block_data_screen'); ?>");
        get_count_array();
        getzoomuserlist(curPageId);
        //统计
        function get_count_array() {
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_zoom',
                'args': {
                    'type': type,
                    'page': curPageId,
                    'pagesize': 10
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
                        var countarr = result.countarr;
                        $('#zoom_total').html(countarr.zoom_total);
                        $('#zoom_using').html(countarr.zoom_using);
                        $('#zoom_free').html(countarr.zoom_free);
                        $('#zoom_mod_order').html(countarr.zoom_mod_order);
                        $('#zoom_mod_using').html(countarr.zoom_mod_using);
                    }
                },
                error: function(e) {
                    alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
                }
            });
        }

        //前三者
        $('.zoomdata').click(function() {
            type = $(this).data('type');
            curPageId = 1;
            getzoomuserlist(curPageId);
        })

        function getzoomuserlist(curPageId) {
            var role = $("li.active").val();
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_zoomuser',
                'args': {
                    'type': type,
                    'page': curPageId,
                    'pagesize': 10,
                    'role': role,
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
                        var list = result.list;
                        var len = list.length;
                        var str = "";
                        for (i = 0; i < len; i++) {
                            str += "<tr>";
                            str += "<td>" + list[i]['id'] + "</td>";
                            str += "<td>" + list[i]['category'] + "</td>";
                            if (list[i]['num'] == 0) {
                                str += "<td>-</td>";
                                str += "<td>-</td>";
                                str += "<td>-</td>";
                                str += "<td>-</td>";
                                str += "<td>空闲</td>";
                                str += "<td>-</td>";
                            } else {
                                str += "<td>" + list[i]['coursename'] + "</td>";
                                str += "<td>" + list[i]['uname'] + "</td>";
                                str += "<td>" + list[i]['snum'] + "</td>";
                                str += "<td>" + list[i]['start_time'] + "</td>";
                                str += "<td>使用中</td>";
                                if (list[i]['join_url'] == '-') {
                                    str += "<td>-</td>";
                                } else {
                                    str += "<td><a target='view_window' href='" + list[i]['join_url'] + "'>" + '观看' + "</a></td>";
                                }
                            }
                            str += "</tr>";
                        }
                        $('.zoomuser-table tbody').html(str);

                        $('.zoomuser-table').show();
                        $('.zoommod-table').hide();

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

        //后两者
        $('.zoommod').click(function() {
            type = $(this).data('type');
            curPageId = 1;
            getzoommodlist(curPageId);
        })

        function getzoommodlist(curPageId) {
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_zoommod',
                'args': {
                    'type': type,
                    'page': curPageId,
                    'pagesize': 10
                }
            };
            $.ajax({
                type: "POST",
                contentType: "application/json;",
                url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    console.log(result);
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data;
                        var list = result.list;
                        var len = list.length;
                        var str = "";
                        for (i = 0; i < len; i++) {
                            str += "<tr>";
                            str += "<td>" + list[i]['id'] + "</td>";
                            str += "<td>" + list[i]['zoomname'] + "</td>";
                            str += "<td>" + list[i]['coursename'] + "</td>";
                            str += "<td>" + list[i]['teachers'] + "</td>";
                            str += "<td>" + list[i]['timetype'] + "</td>";
                            str += "<td>" + list[i]['start_time'] + "</td>";
                            str += "<td>" + list[i]['snum'] + "</td>";
                            str += "<td>" + list[i]['status'] + "</td>";
                            str += "</tr>";
                        }
                        $('.zoommod-table tbody').html(str);

                        $('.zoomuser-table').hide();
                        $('.zoommod-table').show();

                        var page = result.page;
                        $("#page").paging({
                            pageNo: page.cur_page,
                            totalPage: page.max_page,
                            callback: function(curPageId) {
                                getzoommodlist(curPageId);
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