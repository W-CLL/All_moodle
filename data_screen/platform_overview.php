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
    <div class="page-container">
        <?php require_once('public/platform_leftside.php'); ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-head">
                    <div class="page-title">
                        <h1><?PHP echo get_string('platform_overview', 'block_data_screen'); ?></h1>
                    </div>
                </div>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="active"><?PHP echo get_string('total_platform', 'block_data_screen'); ?></span>
                    </li>
                </ul>
                <div class="row mr-0">
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-green-sharp">
                                        <span class="green" data-value="0" id="course_num">0</span>
                                    </h3>
                                    <small><?PHP echo get_string('course_total', 'block_data_screen'); ?></small>
                                </div>
                                <div class="icon">
                                    <i class="icon-pie-chart"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="course_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success green-sharp">
                                        <span class="sr-only"></span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen'); ?></div>
                                    <div class="status-number greenStatus"> 0% </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered ">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-red-haze">
                                        <span data-value="600" id="teacher_num">0</span>
                                    </h3>
                                    <small><?PHP echo get_string('teacher_total', 'block_data_screen'); ?></small>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="teacher_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success red-haze">
                                        <span class="sr-only"> </span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen'); ?></div>
                                    <div class="status-number redStatus"> 0% </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-blue-sharp">
                                        <span data-value="600" id="student_num">0</span>
                                    </h3>
                                    <small><?PHP echo get_string('student_total', 'block_data_screen'); ?></small>
                                </div>
                                <div class="icon">
                                    <i class="icon-user"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="student_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success blue-sharp">
                                        <span class="sr-only"></span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen'); ?></div>
                                    <div class="status-number blueStatus"> 0% </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-purple-soft">
                                        <span data-value="600" id="percourse_num">0</span>
                                    </h3>
                                    <small><?PHP echo get_string('per_capita_elective', 'block_data_screen'); ?></small>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-book"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="percourse_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success purple-soft">
                                        <span class="sr-only"></span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen'); ?></div>
                                    <div class="status-number purpleStatus"> 0% </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-pink-soft">
                                        <span data-value="600" id="pv_num">0</span>
                                    </h3>
                                    <small><?PHP echo get_string('month_access', 'block_data_screen'); ?></small>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-map-marker"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="pv_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success pink-soft">
                                        <span class="sr-only"></span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen'); ?></div>
                                    <div class="status-number pinkStatus"> 0% </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-grep">
                                        <span data-value="600" id="realclass_num">0</span>
                                    </h3>
                                    <small><a href="online_courses.php"><?PHP echo get_string('online_course', 'block_data_screen'); ?></a></small>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-book"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="realclass_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success pink-soft">
                                        <span class="sr-only"></span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('near_ten_min', 'block_data_screen'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="platform-totalcount">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="number">
                                    <h3 class="font-black">
                                        <span data-value="600" id="realstudent_num">0</span>
                                    </h3>
                                    <small><a href="real_time_study.php"><?PHP echo get_string('real_time_study', 'block_data_screen'); ?></a></small>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                            <div class="progress-info" id="realstudent_add_box">
                                <div class="progress">
                                    <span style="width: 0%;" class="progress-bar progress-bar-success pink-soft">
                                        <span class="sr-only"></span>
                                    </span>
                                </div>
                                <div class="status">
                                    <div class="status-title"><?PHP echo get_string('near_half_an_hour', 'block_data_screen'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <span class="caption-subject bold uppercase font-dark"><?PHP echo get_string('opening_course', 'block_data_screen'); ?></span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="echart_kskc" class="CSSAnimationChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xs-12 col-sm-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption ">
                                    <span class="caption-subject font-dark bold uppercase"><?PHP echo get_string('access_num', 'block_data_screen'); ?></span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="echart_fwcs" class="CSSAnimationChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('public/page_footer.php'); ?>
    <?php require_once('public/platform_publicjs.php'); ?>
    <script type="text/javascript">
        $("title").html("<?PHP echo get_string('platform_overview_title', 'block_data_screen'); ?>");
        function barchart(data,datetype,objid,legenddata) {
            let xText = [], yText = [];
            if (data) {
                for (var i = 0; i < data.length; i++) {
                    let item = data[i];
                    xText.push(item.title);
                    yText.push(item[datetype]);
                }
                var echart_obj = echarts.init(document.getElementById(objid));
                var echart_option = {
                    color: ['#5CBFDB'],
                    grid: {
                        top: '5%',
                        bottom: '10%'
                    },
                    xAxis: {
                        data: xText,
                        axisLabel: {
                            textStyle: {
                                color: '#678098'
                            },
                            interval: 0,
                            formatter: function(value) {
                                var ret = ""; //拼接加\n返回的类目项
                                var maxLength = 7; //每项显示文字个数
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
                        data: yText,
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
                        name: legenddata,
                        type: 'bar',
                        data: yText,
                        label: {
                            show: true,
                            position: 'top'
                        },
                    }],
                    tooltip: {
                        trigger: "axis",
                        show: true,
                        axisPointer: {}
                    }
                };
                echart_obj.setOption(echart_option);
            }
        }
        function getPageData() {
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_platform_overview',
                'args': {}
            };
            $.ajax({
                type: "POST",
                contentType: "application/json;charset=UTF-8",
                url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data;
                        for(let item in result.platform){
                            if(item.indexOf('add') > 0){
                                $('#' + item + '_box .progress-bar').css('width', result.platform[item]);
                                $('#' + item + '_box .status-number').text(result.platform[item]);
                            }else{
                                $('#' + item).attr('data-value', result.platform[item]).counterUp();
                            }
                        }
                        barchart(result.effective,'counts','echart_kskc',"<?PHP echo get_string('course_num', 'block_data_screen'); ?>")
                        barchart(result.pv,'pv','echart_fwcs',"<?PHP echo get_string('access_num', 'block_data_screen'); ?>")
                    } else {}
                },
                error: function(e) {
                    alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
                }
            });
        }
        getPageData();
    </script>
</body>

</html>