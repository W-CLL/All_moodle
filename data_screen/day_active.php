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
 * Access analysis
 *
 * @package    block_data_screen
 * @copyright  2019 ckf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once('public/header.php');
$url = basename(__FILE__);
require_once('lib.php');
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
                        <h1><?php echo $date = $_GET['date'], get_string('active', 'block_data_screen'); ?></h1>
                    </div>
                </div>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="platform_overview.php"><?php echo get_string('home', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="active_7days.php"><?php echo get_string('active_7days', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="active"><?php echo $date = $_GET['date'], get_string('active', 'block_data_screen'); ?></span>
                    </li>
                </ul>
                <!-- BEGIN DASHBOARD STATS 1-->
                <div class="clearfix"></div>
                <!-- END DASHBOARD STATS 1-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <!--
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-social-dribbble font-green hide"></i>
                                    <span class="caption-subject font-dark bold uppercase"></span>
                                </div>

                            </div> -->
                            <div class="items items_third time">
                                <input class="form-control inputs " id="date_info" type="date" value="">
                            </div>
                            <div class="portlet-body">

                                <div class="table-scrollable">
                                    <table class="zoomuser-table table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th> <?php echo get_string('course_id', 'block_data_screen'); ?></th>
                                                <th> <?php echo get_string('college', 'block_data_screen'); ?> </th>
                                                <th> <?php echo get_string('course', 'block_data_screen'); ?> </th>
                                                <th> <?php echo get_string('teachers', 'block_data_screen'); ?> </th>
                                                <th> <?php echo get_string('student_total', 'block_data_screen'); ?> </th>
                                                <th> <?php echo get_string('real_stu', 'block_data_screen'); ?></th>
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
        $("title").html("<?php echo $date, get_string('active', 'block_data_screen'); ?>");

      
        // getdayactivelist(curPageId,date);
        $(document).ready(function() {
            var curPageId = 1;
            var twoUrl = window.location.search;
            var date4 = twoUrl.slice(1).split('=')[1];

            $('#date_info').val(date4);
            var date3 = $('#date_info').val();
            var date2 = new Date(date3);
            var date = Date.parse(date2) / 1000;

            getdayactivelist(curPageId, date);

            $(function() {
                $('#date_info').on('input', function() {
                    var date3 = $(this).val();
                    window.location.href = 'day_active.php?date=' + date3;
                })
            })
        });

        function getdayactivelist(curPageId, date) {
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_day_active',
                'args': {
                    'page': curPageId,
                    'pagesize':12,
                    'date':date,
                }
            };
            $.ajax({
                type: "POST",
                contentType: "application/json;",
                url: "<?php echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    //console.log(result);
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data;
                        //console.log(result);
                        var list = result.data;
                        console.log(list);
                        var len = list.length;
                        var str = "";
                        for (i = 0; i < len; i++) {
                            str += "<tr>";
                            str += "<td>" + list[i]['id'] + "</td>";
                            str += "<td>" + list[i]['category'] + "</td>";
                            str += "<td>" + list[i]['fullname'] + "</td>";
                            str += "<td>" + list[i]['teachername'] + "</td>";
                            str += "<td>" + list[i]['students'] + "</td>";
                            str += "<td>" + list[i]['real_stu'] + "</td>";
                            str += "</tr>";
                        }
                        $('.zoomuser-table tbody').html(str);

                        var page = result.page;
                        $("#page").paging({
                            pageNo: page.cur_page,
                            totalPage: page.max_page,
                            callback: function(curPageId) {
                                getdayactivelist(curPageId, date);
                            }
                        })
                    } else {}
                },
                error: function(e) {
                    alert("<?php echo get_string('network_error', 'block_data_screen'); ?>");
                }
            });
        }
    </script>
</body>

</html>