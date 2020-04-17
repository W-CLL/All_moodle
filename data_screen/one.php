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

<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
    <!-- BEGIN HEADER -->
    <?php require_once('public/page_header.php'); ?>
    <!-- END HEADER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content" style="margin-left: 0;">
                <!-- BEGIN PAGE HEAD-->
                <div class="page-head">
                    <!-- BEGIN PAGE TITLE -->
                    <div class="page-title">
                        <h1>课程建设情况统计</h1>
                    </div>
                    <!-- END PAGE TITLE -->
                    <!-- BEGIN PAGE TOOLBAR -->
                    <!-- <div class="page-toolbar">
                        <select class="bs-select form-control input-small" onchange="window.location=this.value;">
                            <option value="one.php" selected>截止至3月1日</option>
                            <option value="two.php">3月2日-3日</option>
                        </select>
                    </div> -->
                    <!-- END PAGE TOOLBAR -->
                </div>
                <!-- END PAGE HEAD-->
                <!-- BEGIN PAGE BREADCRUMB -->
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="index.html">首页</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="active">课程建设情况统计</span>
                    </li>
                </ul>
                <!-- END PAGE BREADCRUMB -->
                <style type="text/css">
                    .widget-thumb .widget-thumb-wrap .widget-thumb-icon {
                        width: 50px;
                        height: 50px;
                        padding: 6px;
                    }

                    .portlet .card-title,
                    .portlet .card-desc {
                        overflow: auto;
                    }

                    .portlet .card-title {
                        font-size: 20px;
                        font-weight: bold;
                        padding: 10px 0;
                        border-bottom: 1px solid #DDD;
                    }

                    .portlet .name {
                        float: left;
                        width: 40%;
                    }

                    .portlet .num {
                        float: right;
                        width: 60%;
                        text-align: right;
                    }

                    .portlet .desc-item {
                        overflow: auto;
                        padding: 5px 0;
                    }
                </style>
                <!-- BEGIN PAGE BASE CONTENT -->
                <div class="row widget-row">
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">在线课程数</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-green icon-book-open"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-body-stat course_num">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">教师协作课程数</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-red icon-bubbles"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-body-stat multi_teacher_courses_num">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">教师数</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-purple icon-user"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-body-stat teacher_num">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                    <div class="col-md-3">
                        <!-- BEGIN WIDGET THUMB -->
                        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                            <h4 class="widget-thumb-heading">学生数</h4>
                            <div class="widget-thumb-wrap">
                                <i class="widget-thumb-icon bg-blue icon-users"></i>
                                <div class="widget-thumb-body">
                                    <span class="widget-thumb-body-stat student_num">0</span>
                                </div>
                            </div>
                        </div>
                        <!-- END WIDGET THUMB -->
                    </div>
                </div>
                <!-- END PAGE BASE CONTENT -->
                <!-- BEGIN CARDS -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="portlet light">
                            <div class="card-title">
                                <span class="name"> 资源数 </span><span class="num resource_num"> 0 </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="portlet light">
                            <div class="card-title">
                                <span class="name"> 讨论区数 </span><span class="num forums_num"> 0 </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="portlet light">
                            <div class="card-title">
                                <span class="name"> 作业数 </span><span class="num assigns_num"> 0 </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="portlet light">
                            <div class="card-title">
                                <span class="name"> 测验数 </span><span class="num quiz_num"> 0 </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END CARDS -->
                <!-- BEGIN CHARTS -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" icon-layers font-green"></i>
                                    <span class="caption-subject font-green bold uppercase">学生数最多的课程(TOP5)</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="echarts_bar_3" style="height:450px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" icon-layers font-green"></i>
                                    <span class="caption-subject font-green bold uppercase">活动资源最丰富的课程(TOP5)</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="echarts_bar_4" style="height:450px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END CHARTS -->
            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->
    <?php require_once('public/page_footer.php'); ?>
    <?php require_once('public/platform_publicjs.php'); ?>
    <script type="text/javascript">
        $("title").html("<?PHP echo get_string('platform_overview_title', 'block_data_screen'); ?>");

        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_network_teach',
            'args': {}
        };
        $.ajax({
            type: "POST",
            contentType: "application/json;charset=UTF-8",
            url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
            data: JSON.stringify(data),
            success: function(result) {
                if (typeof result[0].data != 'undefined') {
                    total_statistics = result[0].data.total_statistics;
                    top_student_courses = result[0].data.top_student_courses;
                    top_activity_courses = result[0].data.top_activity_courses;

                    for(var attr in total_statistics){
                        if(typeof(total_statistics[attr])!="function"){
                            $('.' + attr).html(total_statistics[attr]);
                        }
                    }

                    first_chart3_x_title = [];
                    first_chart3_x_value = [];
                    for (let item in top_student_courses) {
                        first_chart3_x_title.push(top_student_courses[item]['short_name'] + '-' + top_student_courses[item]['teachers']);
                        first_chart3_x_value.push(top_student_courses[item]['student_num']);
                    }

                    first_chart4_x_title = [];
                    first_chart4_x_value = [];
                    for (let item in top_activity_courses) {
                        first_chart4_x_title.push(top_activity_courses[item]['short_name'] + '-' + top_activity_courses[item]['teachers']);
                        first_chart4_x_value.push(top_activity_courses[item]['activity_resource_num']);
                    }

                    var first_chart3 = echarts.init(document.getElementById('echarts_bar_3'));
                    var option3 = {
                        color: ['#5CBFDB'],
                        grid: {
                            top: '5%',
                            bottom: '10%'
                        },
                        xAxis: {
                            data: first_chart3_x_title,
                            axisLabel: {
                                textStyle: {
                                    color: '#678098'
                                },
                                interval: 0,
                                formatter: function(value) {
                                    var ret = ""; //拼接加\n返回的类目项
                                    var maxLength = 9; //每项显示文字个数
                                    var valLength = value.length; //X轴类目项的文字个数
                                    var rowN = Math.ceil(valLength / maxLength); //类目项需要换行的行数
                                    if (rowN > 1) //如果类目项的文字大于3,
                                    {
                                        for (var i = 0; i < rowN; i++) {
                                            var temp = ""; //每次截取的字符串
                                            var start = i * maxLength; //开始截取的位置
                                            var end = start + maxLength; //结束截取的位置
                                            //这里也可以加一个是否是最后一行的判断，但是不加也没有影响，那就不加吧
                                            temp = value.substring(start, end) + "\n";
                                            ret += temp; //凭借最终的字符串
                                        }
                                        return ret;
                                    } else {
                                        return value;
                                    }
                                }
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#678098'
                                }
                            }
                        },
                        yAxis: {
                            type: 'value',
                            data: first_chart3_x_value,
                            nameLocation: 'center',
                            nameGap: 35,
                            nameTextStyle: {
                                color: '#678098'
                            },
                            axisLine: {
                                show: false,
                                lineStyle: {
                                    color: '#678098'
                                }
                            },
                        },
                        series: [{
                            name: "学生数",
                            type: 'bar',
                            label: {
                                show: true,
                                position: 'top'
                            },
                            data: first_chart3_x_value,
                        }],
                        tooltip: {
                            trigger: "axis",
                            show: true,
                            axisPointer: {}
                        }
                    };
                    first_chart3.setOption(option3);

                    var first_chart4 = echarts.init(document.getElementById('echarts_bar_4'));
                    var option4 = {
                        color: ['#5CBFDB'],
                        grid: {
                            top: '5%',
                            bottom: '10%'
                        },
                        xAxis: {
                            data: first_chart4_x_title,
                            axisLabel: {
                                textStyle: {
                                    color: '#678098'
                                },
                                interval: 0,
                                formatter: function(value) {
                                    var ret = ""; //拼接加\n返回的类目项
                                    var maxLength = 8; //每项显示文字个数
                                    var valLength = value.length; //X轴类目项的文字个数
                                    var rowN = Math.ceil(valLength / maxLength); //类目项需要换行的行数
                                    if (rowN > 1) //如果类目项的文字大于3,
                                    {
                                        for (var i = 0; i < rowN; i++) {
                                            var temp = ""; //每次截取的字符串
                                            var start = i * maxLength; //开始截取的位置
                                            var end = start + maxLength; //结束截取的位置
                                            //这里也可以加一个是否是最后一行的判断，但是不加也没有影响，那就不加吧
                                            temp = value.substring(start, end) + "\n";
                                            ret += temp; //凭借最终的字符串
                                        }
                                        return ret;
                                    } else {
                                        return value;
                                    }
                                }
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#678098'
                                }
                            }
                        },
                        yAxis: {
                            type: 'value',
                            data: first_chart4_x_value,
                            nameLocation: 'center',
                            nameGap: 35,
                            nameTextStyle: {
                                color: '#678098'
                            },
                            axisLine: {
                                show: false,
                                lineStyle: {
                                    color: '#678098'
                                }
                            },
                        },
                        series: [{
                            name: "课程数",
                            type: 'bar',
                            label: {
                                show: true,
                                position: 'top'
                            },
                            data: first_chart4_x_value,
                        }],
                        tooltip: {
                            trigger: "axis",
                            show: true,
                            axisPointer: {}
                        }
                    };
                    first_chart4.setOption(option4);
                }
            },
            error: function(e) {
                alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
            }
        });
    </script>
</body>
</html>