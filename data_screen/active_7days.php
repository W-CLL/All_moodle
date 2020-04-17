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
?>
<link rel="stylesheet" href="amd/layouts/layout4/css/atributeClass.css">

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
                        <h1><?PHP echo get_string('active_7days', 'block_data_screen'); ?></h1>
                    </div>
                </div>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen'); ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="active"><?PHP echo get_string('active_7days', 'block_data_screen'); ?></span>
                    </li>
                </ul>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="dashboard-stat2 bordered">
                            <div class="display">
                                <div class="dateTime">
                                    <div class="items items_third time">
                                        <input class="form-control inputs " id="date_info" type="date" value="">
                                    </div>
                                    <div class="items items_third">
                                        <select class="form-control inputs " name="" id="indicators" onchange="selectOption()">
                                            <option value="" id="0"><?PHP echo get_string('indicators', 'block_data_screen'); ?></option>
                                            <option class="selectItem" id="1" value="<?PHP echo get_string('active_7days_course', 'block_data_screen'); ?>"><?PHP echo get_string('active_7days_course', 'block_data_screen'); ?></option>
                                            <option class="selectItem" id="2" value="<?PHP echo get_string('active_7days_student', 'block_data_screen'); ?>"><?PHP echo get_string('active_7days_student', 'block_data_screen'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div id="dashboard_amchart_2" class="accessAnalyChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once('public/page_footer.php'); ?>
    <?php require_once('public/platform_publicjs.php'); ?>
    <script>
        $("title").html('<?PHP echo get_string('active_7days', 'block_data_screen'); ?>');
        $(document).ready(function() {
            var time = new Date();
            var day = ("0" + time.getDate()).slice(-2);
            var month = ("0" + (time.getMonth() + 1)).slice(-2);
            var today = time.getFullYear() + "-" + (month) + "-" + (day);
            $('#date_info').val(today);
            var selectDate = $('#date_info').val();
            getAccessChart(selectDate);
            $(function() {
                $('#date_info').on('input', function() {
                    var selectDate = $(this).val();
                    getAccessChart(selectDate);
                })
            })
        });
        var arr = [{
            name: "<?PHP echo get_string('indicators', 'block_data_screen'); ?>",
            id: '0',
            checked: true
        }, {
            name: "<?PHP echo get_string('active_7days_course', 'block_data_screen'); ?>",
            id: '1',
            checked: true
        }, {
            name: "<?PHP echo get_string('active_7days_student', 'block_data_screen'); ?>",
            id: '2',
            checked: true
        }];

        function selectOption() {
            var selectText = $('option:selected').text();
            var index = $('option:selected').attr('id');
            arr.map((v, ind) => {
                if (index === '0') {
                    v.checked = true;
                } else if (v.id !== index) {
                    v.checked = false;
                } else {
                    v.checked = true;
                }
            })
            var selectDate = $('#date_info').val();
            getAccessChart(selectDate);
        }


        function accessCharts(result) {
            //console.log(result);
            var courseArr = [];
            var studentArr = [];
            var dayArr = [];
            for (var j = 0; j < result.length; j++) {
                var item = result[j];
                var course = item.courses;
                var student = item.students;
                var day = item.date;
                dayArr.push(day);
                courseArr.push(course);
                studentArr.push(student);
            }

            var third_chart = echarts.init(document.getElementById('dashboard_amchart_2'));
            var option3 = {
                legend: {
                    data: ["<?PHP echo get_string('active_7days_course', 'block_data_screen'); ?>", "<?PHP echo get_string('active_7days_student', 'block_data_screen'); ?>"],
                    itemWidth: 20,
                    itemHeight: 10,
                    itemGap: 10,
                    textStyle: {
                        fontStyle: 12,
                    },
                    selected: {
                        "<?PHP echo get_string('active_7days_course', 'block_data_screen'); ?>": arr[1].checked,
                        "<?PHP echo get_string('active_7days_student', 'block_data_screen'); ?>": arr[2].checked
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                grid: {
                    top: '20%',
                    left: '4%',
                    right: '4%',
                },
                xAxis: {
                    type: 'category',
                    name: "日期",
                    data: dayArr,
                    axisLine: {
                        lineStyle: {
                            color: '#678098'
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    axisLine: {
                        show: false,
                        lineStyle: {
                            color: '#678098'
                        }
                    }
                },
                series: [{
                        name: "<?PHP echo get_string('active_7days_course', 'block_data_screen'); ?>",
                        type: 'line',
                        data: courseArr,
                        symbol: 'rect',
                        symbolSize: 8,
                    },
                    {
                        name: "<?PHP echo get_string('active_7days_student', 'block_data_screen'); ?>",
                        data: studentArr,
                        type: 'line',
                        symbol: 'rect',
                        symbolSize: 8,
                        lineStyle: {
                            color: '#AFD8F8'
                        },
                        itemStyle: {
                            color: '#AFD8F8'
                        }
                    }
                ]
            }
            third_chart.setOption(option3);
            third_chart.on('click', 'series.line', function(params) {
                var name = params.seriesName;
                var data = params.name;
                if (name =="<?php echo get_string('active_7days_course', 'block_data_screen');?>") {
                    window.location.href = "day_active.php?date=" + data;
                }
            })
        }

        // getAccessChart();
        function getAccessChart(selectDate) {
            var data = Array();
            data[0] = {
                'index': 0,
                'methodname': 'block_data_screen_active_7days',
                'args': {
                    'date': selectDate,
                }
            };
            $.ajax({
                type: "POST",
                contentType: "application/json;",
                url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data.list;
                        accessCharts(result);
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