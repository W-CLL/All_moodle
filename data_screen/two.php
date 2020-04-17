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
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content" style="margin-left: 0;">
                    <!-- BEGIN PAGE HEAD-->
                    <div class="page-head">
                        <!-- BEGIN PAGE TITLE -->
                        <div class="page-title">
                            <h1>课程建设情况统计
                                <small>3月2日-3日</small>
                            </h1>
                        </div>
                        <!-- END PAGE TITLE -->
                        <!-- BEGIN PAGE TOOLBAR -->
                        <div class="page-toolbar">
                            <select class="bs-select form-control input-small" onchange="window.location=this.value;">
                                <option value="one.php">截止至3月1日</option>
                                <option value="two.php" selected>3月2日-3日</option>
                            </select>
                        </div>
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
                                    <i class="widget-thumb-icon bg-green icon-bulb"></i>
                                    <div class="widget-thumb-body">
                                        <span class="widget-thumb-body-stat">1808</span>
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
                                    <i class="widget-thumb-icon bg-red icon-layers"></i>
                                    <div class="widget-thumb-body">
                                        <span class="widget-thumb-body-stat">1971</span>
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
                                    <i class="widget-thumb-icon bg-purple icon-screen-desktop"></i>
                                    <div class="widget-thumb-body">
                                        <span class="widget-thumb-body-stat">1615</span>
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
                                    <i class="widget-thumb-icon bg-blue icon-bar-chart"></i>
                                    <div class="widget-thumb-body">
                                        <span class="widget-thumb-body-stat" style="font-size: 25px;">32443</span>
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
                                    <span class="name"> 资源数 </span><span class="num"> 31326 </span>
                                </div>
                                <div class="card-desc">
                                    <div class="desc-item"><span class="name"> 资源访问量 </span><span class="num"> 184267 </span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="portlet light">
                                <div class="card-title">
                                    <span class="name"> 讨论区数 </span><span class="num"> 4851 </span>
                                </div>
                                <div class="card-desc">
                                    <div class="desc-item"><span class="name"> 讨论主题数 </span><span class="num"> 2853 </span></div>
                                    <div class="desc-item"><span class="name"> 讨论回帖数 </span><span class="num"> 5470 </span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="portlet light">
                                <div class="card-title">
                                    <span class="name"> 作业数 </span><span class="num"> 2617 </span>
                                </div>
                                <div class="card-desc">
                                    <div class="desc-item"><span class="name"> 作业提交数 </span><span class="num"> 3401 </span></div>
                                    <div class="desc-item"><span class="name"> 作业批改数 </span><span class="num"> 351 </span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="portlet light">
                                <div class="card-title">
                                    <span class="name"> 测验数 </span><span class="num"> 1129 </span>
                                </div>
                                <div class="card-desc">
                                    <div class="desc-item"><span class="name"> 测验完成数 </span><span class="num"> - </span></div>
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
                                        <span class="caption-subject font-green bold uppercase">访问量最多的学院(TOP5)</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="echarts_bar" style="height:450px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-layers font-green"></i>
                                        <span class="caption-subject font-green bold uppercase">访问量最多的课程(TOP5)</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="echarts_bar_2" style="height:450px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-layers font-green"></i>
                                        <span class="caption-subject font-green bold uppercase">综合活跃度最高的学院（Top5）</span>
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
                                        <span class="caption-subject font-green bold uppercase">综合活跃度最高的课程(TOP5)</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="echarts_bar_4" style="height:450px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">

                    </script>
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

            $('.dropdown-menu li').click(function() {
                var tag = $(this);
                var data = Array();
                data[0] = {
                    'index': 0,
                    'methodname': 'block_data_screen_set_role',
                    'args': {
                        'role': tag.val()
                    }
                };
                $.ajax({
                    type: "POST",
                    contentType: "application/json;",
                    url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                    data: JSON.stringify(data),
                    success: function(result) {
                        if (result[0].data.status == 'Success') {
                            tag.addClass('active').siblings().removeClass('active');
                            if (tag.val() == 5) {
                                $("li.student").addClass('student_hidden');
                            } else {
                                $("li.student").removeClass('student_hidden');
                            }
                        } else {}
                    },
                    error: function(e) {
                        alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
                    }
                });
            });

            var first_chart = echarts.init(document.getElementById('echarts_bar'));
            var option1 = {
                color: ['#5CBFDB'],
                grid: {
                    top: '5%',
                    bottom: '10%'
                },
                xAxis: {
                    data: ['马克思主义学院', '化学学院', '物理与电信工程学院', '经济与管理学院', '军事理论教研室'],
                    axisLabel: {
                        textStyle: {
                            color: '#678098',
                        },
                        interval: 0
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#678098'
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    data: [143544, 83491, 80980, 75672, 74972],
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
                    name: "访问量",
                    type: 'bar',
                    label: {
                        show: true,
                        position: 'top'
                    },
                    data: [143544, 83491, 80980, 75672, 74972],

                }],
                tooltip: {
                    trigger: "axis",
                    show: true,
                    axisPointer: {}
                }
            };
            first_chart.setOption(option1);
            var first_chart2 = echarts.init(document.getElementById('echarts_bar_2'));
            var option2 = {
                color: ['#5CBFDB'],
                grid: {
                    top: '5%',
                    bottom: '10%'
                },
                xAxis: {
                    data: ['新冠肺炎疫情防控与当代青年使命担当', '植物学', '基础英语（2）（南海校区）', '世界宗教概论', '思想道德修养与法律基础'],
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
                    data: [13160, 5216, 4728, 4250, 4220],
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
                    name: "访问量",
                    type: 'bar',
                    label: {
                        show: true,
                        position: 'top'
                    },
                    data: [13160, 5216, 4728, 4250, 4220],

                }],
                tooltip: {
                    trigger: "axis",
                    show: true,
                    axisPointer: {}
                }
            };
            first_chart2.setOption(option2);
            var first_chart3 = echarts.init(document.getElementById('echarts_bar_3'));
            var option3 = {
                color: ['#5CBFDB'],
                grid: {
                    top: '5%',
                    bottom: '10%'
                },
                xAxis: {
                    data: ['军事理论教研室', '基础教育培训与研究院', '研究生院', '马克思主义学院', '公体部'],
                    axisLabel: {
                        textStyle: {
                            color: '#678098'
                        },
                        interval: 0
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#678098'
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    data: [2998, 2326, 2311, 2208, 2030],
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
                    name: "综合访问量",
                    type: 'bar',
                    label: {
                        show: true,
                        position: 'top'
                    },
                    data: [2998, 2326, 2311, 2208, 2030],

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
                    data: ['植物学', '食品工艺学', '物理化学', '运动心血管研究进展', '大学物理（III-1）'],
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
                    data: [77, 48, 41, 40, 34],
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
                    name: "综合访问量",
                    type: 'bar',
                    label: {
                        show: true,
                        position: 'top'
                    },
                    data: [77, 48, 41, 40, 34],
                }],
                tooltip: {
                    trigger: "axis",
                    show: true,
                    axisPointer: {}
                }
            };
            first_chart4.setOption(option4);
        </script>
</body>

</html>