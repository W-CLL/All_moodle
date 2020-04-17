        <div class="page-sidebar-wrapper">
            <div class="page-sidebar">
                <ul class="page-sidebar-menu navbar-collapse collapse" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" id="example-navbar-collapse">
                    <li class="nav-item <?php if(!in_array($url,['college_list.php','college_detail.php','teacher_list.php','teacher_detail.php'])) echo 'active' ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="customicon customicon-20 customicon-ptgk"></i>
                            <span class="title"><?PHP echo get_string('platform_overview', 'block_data_screen'); ?></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item <?php if(in_array($url,['platform_overview.php','online_courses.php','real_time_study.php'])) echo 'active' ?>">
                                <a href="platform_overview.php" class="nav-link ">
                                    <span class="title ml-20"><?PHP echo get_string('total_platform', 'block_data_screen'); ?></span>
                                </a>
                            </li>
                            <?php if ($display) { ?>
                                <li class="nav-item <?php if($url=='attribute_course.php') echo 'active' ?>">
                                    <a href="attribute_course.php" class="nav-link ">
                                        <span class="title ml-20"><?PHP echo get_string('attribute_course', 'block_data_screen'); ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                            <li class="nav-item <?php if($url=='active_7days.php') echo 'active' ?>">
                                <a href="active_7days.php" class="nav-link ">
                                    <span class="title ml-20"><?PHP echo get_string('active_7days', 'block_data_screen');?></span>
                                </a>
                            </li>
                            <li class="nav-item student <?PHP if ($default_role == 5) echo 'student_hidden'; ?> <?php if($url=='invalid_course.php') echo 'active' ?>">
                                <a href="invalid_course.php" class="nav-link ">
                                    <span class="title ml-20"><?PHP echo get_string('invalid_course', 'block_data_screen'); ?></span>
                                </a>
                            </li>
                            <li class="nav-item <?php if($url=='access_analysis.php') echo 'active' ?>">
                                <a href="access_analysis.php" class="nav-link ">
                                    <span class="title ml-20"><?PHP echo get_string('access_analysis', 'block_data_screen'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item <?php if(in_array($url,['college_list.php','college_detail.php'])) echo 'active' ?>">
                        <a href="college_list.php" class="nav-link nav-toggle">
                            <i class="customicon customicon-20 customicon-kcfl"></i>
                            <span class="title"><?PHP echo get_string('college_profile', 'block_data_screen'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item student <?PHP if ($default_role == 5) echo 'student_hidden'; ?> <?php if(in_array($url,['teacher_list.php','teacher_detail.php'])) echo 'active' ?>">
                        <a href="teacher_list.php" class="nav-link nav-toggle">
                            <i class="customicon customicon-20 customicon-jsgk"></i>
                            <span class="title"><?PHP echo get_string('teacher_profile', 'block_data_screen'); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>