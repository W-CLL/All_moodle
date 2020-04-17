<?php 

require_once("../../config.php");
global $DB, $USER, $PAGE, $CFG;

if (!isloggedin() || isguestuser()) {
    redirect(get_login_url());
}
$id = required_param('id', PARAM_INT);
$context    = \context_system::instance();
$PAGE->set_context($context);
// get user avatar
$userpicture        = new \user_picture($USER);
$userpicture->size  = 1; // Size f1.
$avatar = $userpicture->get_url($PAGE)->out(false);
$url = new \moodle_url('/');

// Check access permissions.
$systemcontext = context_system::instance();
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Preview page of Metronic Admin Theme #4 for statistics, charts, recent events and reports"
          name="description" />
    <meta content="" name="author" />
    <link href="amd/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="amd/layouts/layout4/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="amd/layouts/layout4/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="amd/layouts/layout4/css/custom.min.css" rel="stylesheet" type="text/css" />
</head>


<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <div class="page-logo">
            
                <img src="amd/layouts/layout4/img/logo@2x.png" alt="logo" class="logo-default" />
                <p class="page-title"><?PHP echo get_string('recommend', 'block_recommend');?></p>
            
        </div>
        <div class="platform topActive">
            <a href="recommend_center.php?id=<?php echo $id;?> ">
                <img class="platformImg" src="amd/layouts/layout4/img/nav_platformicon@2x.png" alt="">
                <p class="platformText"><?PHP echo get_string('recommend', 'block_recommend');?></p>
            </a>
        </div>
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="page-container">

    <div class="page-sidebar-wrapper" >
        <div class="page-sidebar" >
            <ul class="page-sidebar-menu navbar-collapse collapse" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200"  id="example-navbar-collapse">
                <li class="nav-item start active open">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <img class="gaikuang" src="amd/layouts/layout4/img/gaikuang_icon_sel@2x.png">
                        <span class="title"><?PHP echo get_string('recommend_list', 'block_recommend');?></span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item start active ">
                            <a href="recommend_center.php?id=<?php echo $id;?> " class="nav-link ">
                                <span class="title ml-20"><?PHP echo get_string('kn_recommend', 'block_recommend');?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="nav-item start ">
                            <a href="kw_recommend.php" class="nav-link ">
                                <span class="title ml-20"><?PHP echo get_string('kw_recommend', 'block_recommend');?></span>
                            </a>
                        </li>
                        <li class="nav-item start ">
                            <a href="tea_recommend.php" class="nav-link ">
                                <span class="title ml-20"><?PHP echo get_string('tea_recommend', 'block_recommend');?></span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-head">
                <div class="page-title">
                    <h1><?PHP echo get_string('kn_recommend', 'block_recommend');?></h1>
                </div>
            </div>
            <ul class="page-breadcrumb breadcrumb">
                <!-- <li>
                    <a href="recommend_center?id=<?php echo $id;?>"><?PHP echo get_string('kn_recommend', 'block_recommend');?></a>
                    <i class="fa fa-circle"></i>
                </li> 
                <li>
                    <span class="active"><?PHP echo get_string('kn_recommend', 'block_recommend');?></span>
                </li> -->
            </ul>
            <div class="row">
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-green-sharp">
                                    <span class="green"  data-value="0">0</span>
                                </h3>
                                <small><?PHP echo "作业数";//get_string('course_total', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="icon-pie-chart"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                          
                            <div class="status">
                                <div class="status-title"><?PHP echo "已结束的作业";//get_string('increased_last_month', 'block_data_screen');?></div>
                                <div class="status-number greenStatus"> 0 </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-green-sharp">
                                    <span class="green"  data-value="0">0</span>
                                </h3>
                                <small><?PHP echo "测验数";//get_string('course_total', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                          
                            <div class="status">
                                <div class="status-title"><?PHP echo "已结束的测验";//get_string('increased_last_month', 'block_data_screen');?></div>
                                <div class="status-number greenStatus"> 0 </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <!-- <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered ">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-red-haze">
                                    <span  data-value="600" >0</span>
                                </h3>
                                <small><?PHP echo get_string('teacher_total', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                        <span style="width: 0%;" class="progress-bar progress-bar-success red-haze">
                                            <span class="sr-only">  </span>
                                        </span>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen');?></div>
                                <div class="status-number redStatus"> 0% </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-blue-sharp">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><?PHP echo get_string('student_total', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                        <span style="width: 0%;" class="progress-bar progress-bar-success blue-sharp">
                                            <span class="sr-only"></span>
                                        </span>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen');?></div>
                                <div class="status-number blueStatus"> 0% </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-purple-soft">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><?PHP echo get_string('per_capita_elective', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                        <span style="width: 0%;" class="progress-bar progress-bar-success purple-soft">
                                            <span class="sr-only"></span>
                                        </span>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen');?></div>
                                <div class="status-number purpleStatus"> 0% </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-pink-soft">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><?PHP echo get_string('month_access', 'block_data_screen');?></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-map-marker"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                        <span style="width: 0%;" class="progress-bar progress-bar-success pink-soft">
                                            <span class="sr-only"></span>
                                        </span>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('increased_last_month', 'block_data_screen');?></div>
                                <div class="status-number pinkStatus"> 0% </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-grep">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><a href="online_courses.php"><?PHP echo get_string('online_course', 'block_data_screen');?></a></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-book"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                        <span style="width: 0%;" class="progress-bar progress-bar-success pink-soft">
                                            <span class="sr-only"></span>
                                        </span>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('near_ten_min', 'block_data_screen');?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1-5 seven">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <div class="number">
                                <h3 class="font-black">
                                    <span data-value="600">0</span>
                                </h3>
                                <small><a href="real_time_study.php"><?PHP echo get_string('real_time_study', 'block_data_screen');?></a></small>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                        <span style="width: 0%;" class="progress-bar progress-bar-success pink-soft">
                                            <span class="sr-only"></span>
                                        </span>
                            </div>
                            <div class="status">
                                <div class="status-title"><?PHP echo get_string('near_half_an_hour', 'block_data_screen');?></div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="row">
                <div class="col-lg-6 col-xs-12 col-sm-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject bold uppercase font-dark"><?PHP echo get_string('top_active', 'block_recommend');?></span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div id="dashboard_amchart_1" class="CSSAnimationChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xs-12 col-sm-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption ">
                                <span class="caption-subject font-dark bold uppercase"><?PHP echo get_string('top_resources', 'block_recommend');?></span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div id="dashboard_amchart_3" class="CSSAnimationChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-footer">
    <div class="page-footer-inner">
        <!-- <a target="_blank" href=""><?PHP echo $copyright;?></a> -->
    </div>
</div>

<script src="amd/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="amd/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="amd/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
<script src="amd/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
<script src="amd/global/plugins/echarts/echarts.min.js"></script>
<script src="amd/global/scripts/app.min.js" type="text/javascript"></script>
<script src="amd/layouts/layout4/scripts/layout.min.js" type="text/javascript"></script>
<script src="amd/layouts/layout4/scripts/jquery.nicescroll.min.js"></script>
<script src="amd/layouts/layout4/scripts/common.js"></script>
<script type="text/javascript">

$("title").html("<?PHP echo get_string('recommend', 'block_recommend');?>");
    $(document).ready(function(){
        $('.counter').counterUp({
            delay:10,
            time:1000
        });
    });


    getApi();
    function getApi(){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_recommend_Inclass_recommend',
            'args': {}
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;charset=UTF-8",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                console.log(result);
                if (typeof result[0].data != 'undefined') {
                    var result = result[0].data;
                    //console.log(result);
                    var platform = result.platform;
                    $('.font-green-sharp span').attr('data-value',platform.course_num).counterUp();
                    $('.font-red-haze span').attr('data-value',platform.teacher_num).counterUp();
                    $('.font-blue-sharp span').attr('data-value',platform.student_num).counterUp();
                    $('.font-purple-soft span').attr('data-value',platform.percourse_num).counterUp();
                    $('.font-pink-soft span').attr('data-value',platform.pv).counterUp();
                    $('.font-black span').attr('data-value',platform.student).counterUp();
                    $('.font-grep span').attr('data-value',platform.class).counterUp();
                    $('.green-sharp').css('width',platform.course_add);
                    $('.greenStatus').text(platform.course_add);
                    $('.red-haze').css('width',platform.teacher_add);
                    $('.redStatus').text(platform.teacher_add);
                    $('.blue-sharp').css('width',platform.student_add);
                    $('.blueStatus').text(platform.student_add);
                    $('.purple-soft').css('width',platform.percourse_add);
                    $('.purpleStatus').text(platform.percourse_add);
                    $('.pink-soft').css('width',platform.pv_add);
                    $('.pinkStatus').text(platform.pv_add);
                    var effective = result.effective;
                    //showCharts(effective);
                    var Pv = result.pv;
                    //showCharts2(Pv);
                }else{
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

