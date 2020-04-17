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
                    <h1><?PHP echo get_string('access_analysis', 'block_data_screen');?></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="platform_overview.php"><?PHP echo get_string('home', 'block_data_screen');?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span class="active"><?PHP echo get_string('access_analysis', 'block_data_screen');?></span>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="allCourseDiv">
                                <h4 class="allCourse">
                                    <span><?PHP echo get_string('access_analysis', 'block_data_screen');?></span>
                                    <input type="button" class="form-control pull-right download" value="<?PHP echo get_string('download', 'block_data_screen');?>">
                                </h4>
                            </div>
                            <div class="row m-tb-15">
                                <div class="items items_two startTime">
                                    <label for="" class="labels labels_two"><?PHP echo get_string('course_start', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_two" type="date">
                                </div>

                                <div class="items items_two endTime">
                                    <label for="" class="labels labels_two"><?PHP echo get_string('course_end', 'block_data_screen');?>：</label>
                                    <input class="form-control inputs input_two" type="date">
                                </div>

                                <div class="items search">
                                    <input class="form-control searchInput" type="button" value="<?PHP echo get_string('search', 'block_data_screen');?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 table-responsive-xl table-responsive ">
                                    <table class="table table-bordered anaysisTable">
                                        <tbody>
                                        <tr class="bgTr">
                                            <td></td>
                                            <td><?PHP echo get_string('pv', 'block_data_screen');?></td>
                                            <td><?PHP echo get_string('uv', 'block_data_screen');?></td>
                                            <td>IP</td>
                                            <td><?PHP echo get_string('access_num', 'block_data_screen');?></td>
                                        </tr>
                                        <tr class="todayTr">
                                            <td class="bgTr"><?PHP echo get_string('today', 'block_data_screen');?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="yesterdayTr">
                                            <td class="bgTr"><?PHP echo get_string('yesterday', 'block_data_screen');?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="topTr">
                                            <td class="bgTr"><?PHP echo get_string('history_top', 'block_data_screen');?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="totalTr">
                                            <td class="bgTr"><?PHP echo get_string('history_total', 'block_data_screen');?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                        <option value="" id="0"><?PHP echo get_string('indicators', 'block_data_screen');?></option>
                                        <option class="selectItem" id="1" value="<?PHP echo get_string('pv', 'block_data_screen');?>"><?PHP echo get_string('pv', 'block_data_screen');?></option>
                                        <option class="selectItem" id="2" value=""><?PHP echo get_string('uv', 'block_data_screen');?></option>
                                        <option class="selectItem" id="3" value="">IP</option>
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
    $("title").html('<?PHP echo get_string('access_analysis', 'block_data_screen');?>');
    $(document).ready(function () {
        $('.download').click(function () {
            $('.anaysisTable').table2excel({
                exclude: 'noExl',
                name: 'Excel Document Name.xlsx',
                filename: "<?PHP echo get_string('access_analysis', 'block_data_screen');?>",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true
            });
        });
        var time = new Date();
        var day = ("0" + time.getDate()).slice(-2);
        var month = ("0" + (time.getMonth() + 1)).slice(-2);
        var today = time.getFullYear() + "-" + (month) + "-" + (day);
        $('#date_info').val(today);
        var selectDate = $('#date_info').val();
        getAccessChart(selectDate);
        $(function(){
            $('#date_info').on('input',function(){
                var selectDate = $(this).val();
                getAccessChart(selectDate);
            })
        })
    });

    var arr = [ { name: "<?PHP echo get_string('pv', 'block_data_screen');?>",id: '0', checked: true },{ name: "<?PHP echo get_string('pv', 'block_data_screen');?>",id: '1', checked: true }, { name: "<?PHP echo get_string('uv', 'block_data_screen');?>", id: '2', checked: true},{ name: 'IP', id: '3', checked: true}];
    function selectOption(){
        var selectText = $('option:selected').text();
        var index = $('option:selected').attr('id');
        arr.map((v,ind)=>{
            if(index === '0'){
                v.checked = true;
            }else if(v.id !== index) {
                v.checked = false;
            }else{
                v.checked = true;
            }
        })
        getAccessChart();
    }


    function accessCharts(result){
        var accessNumArr = [];
        var hourArr = [];
        var ipArr = [];
        var uvArr = [];
        var pvArr = [];
        for(var j=0;j<result.length;j++){
            var item = result[j];
            var access_num = item.access_num;
            var hour = item.hour;
            var pv = item.pv;
            var uv = item.uv;
            var ip = item.ip;
            accessNumArr.push(access_num);
            hourArr.push(hour);
            ipArr.push(ip);
            uvArr.push(uv);
            pvArr.push(pv);
        }

        var third_chart = echarts.init(document.getElementById('dashboard_amchart_2'));
        var option3 = {
            legend:{
                data:["<?PHP echo get_string('pv', 'block_data_screen');?>","<?PHP echo get_string('uv', 'block_data_screen');?>",'IP'],
                itemWidth:20,
                itemHeight:10,
                itemGap:10,
                textStyle:{
                    fontStyle:12,
                },
                selected:{
                    "<?PHP echo get_string('pv', 'block_data_screen');?>": arr[1].checked,
                    "<?PHP echo get_string('uv', 'block_data_screen');?>": arr[2].checked,
                    'IP': arr[3].checked,
                }
            },
            tooltip:{
                trigger:'axis'
            },
            grid:{
                top:'20%',
                left:'4%',
                right:'4%',
            },
            xAxis:{
                type:'category',
                name: "<?PHP echo get_string('hour', 'block_data_screen')?>",
                data:hourArr,
                axisLine:{
                    lineStyle:{
                        color: '#678098'
                    }
                }
            },
            yAxis:{
                type:'value',
                axisLine:{
                    show:false,
                    lineStyle:{
                        color:'#678098'
                    }
                }
            },
            series:[
                {
                    name:"<?PHP echo get_string('pv', 'block_data_screen');?>",
                    type:'line',
                    data:pvArr,
                    symbol: 'rect',
                    symbolSize: 8,
                },
                {
                    name:"<?PHP echo get_string('uv', 'block_data_screen');?>",
                    data:uvArr,
                    type:'line',
                    symbol: 'rect',
                    symbolSize: 8,
                    lineStyle:{
                        color:'#AFD8F8'
                    },
                    itemStyle:{
                        color:'#AFD8F8'
                    }
                },
                {
                    name:'IP',
                    type:'line',
                    data:ipArr,
                    symbol: 'rect',
                    symbolSize: 8,
                    lineStyle:{
                        color:'#EDC240',
                    },
                    itemStyle:{
                        color:'#EDC240',
                    }
                }
            ]
        }
        third_chart.setOption(option3);
    }

    getAccessAnalysis();
    function getAccessAnalysis(start_time_text,end_time_text){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_access_table',
            'args': {
                'start_time':start_time_text,
                'end_time':end_time_text,
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
                    var today = result.today;
                    $('.todayTr td:eq(1)').text(today.pv);
                    $('.todayTr td:eq(2)').text(today.uv);
                    $('.todayTr td:eq(3)').text(today.ip);
                    $('.todayTr td:eq(4)').text(today.access_num);
                    var top = result.top;
                    $('.topTr td:eq(1)').text(top.pv);
                    $('.topTr td:eq(2)').text(top.uv);
                    $('.topTr td:eq(3)').text(top.ip);
                    $('.topTr td:eq(4)').text(top.access_num);
                    var total = result.total;
                    $('.totalTr td:eq(1)').text(total.pv);
                    $('.totalTr td:eq(2)').text(total.uv);
                    $('.totalTr td:eq(3)').text(total.ip);
                    $('.totalTr td:eq(4)').text(total.access_num);
                    var yesterday = result.yesterday;
                    $('.yesterdayTr td:eq(1)').text(yesterday.pv);
                    $('.yesterdayTr td:eq(2)').text(yesterday.uv);
                    $('.yesterdayTr td:eq(3)').text(yesterday.ip);
                    $('.yesterdayTr td:eq(4)').text(yesterday.access_num);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    getAccessChart();
    function getAccessChart(selectDate){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_access_chart',
            'args': {
                'date':selectDate,
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
                    accessCharts(result);
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }

    $('.searchInput').click(function () {
        var start_time_text = $('.startTime input').val();
        var end_time_text = $('.endTime input').val();
        if (start_time_text != '' && end_time_text != '') {
            var starttimestamp = new Date(start_time_text);
            var endtimestamp = new Date(end_time_text);
            if (starttimestamp > endtimestamp) {
                var data = start_time_text;
                start_time_text = end_time_text;
                end_time_text = data;
            }
        }
        getAccessAnalysis(start_time_text, end_time_text);
    });
</script>
</body>

</html>


