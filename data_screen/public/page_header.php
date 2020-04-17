    <div class="page-header navbar navbar-fixed-top">
        <div class="page-header-inner">
            <div class="page-logo">
                <a href="platform_overview.php" title="<?PHP echo get_string('title', 'block_data_screen'); ?>">
                    <img src="amd/layouts/layout4/img/logo@2x.png" alt="logo" class="logo-default" />
                    <span class="page-title"><?PHP echo get_string('title', 'block_data_screen'); ?></span>
                </a>
                <?php if(in_array($url, $site_page) || in_array($url, $course_page)) {?>
                    <div class="menu-toggler sidebar-toggler">
                        <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                    </div>
                <?php }?>
            </div>
            <div class="top-level-menu">
                <div class="top-level-menu-item <?php if(in_array($url, $site_page)) echo 'active' ?>" id="platform">
                    <a href="platform_overview.php">
                        <i class="customicon customicon-platform"></i>
                        <span class="platformText"><?PHP echo get_string('platform', 'block_data_screen'); ?></span>
                    </a>
                </div>
                <div class="top-level-menu-item <?php if(in_array($url, $course_page)) echo 'active' ?>" id="course">
                    <a href="course_detail.php" onclick="return false;">
                        <i class="customicon customicon-course"></i>
                        <span class="courseText"><?PHP echo get_string('course', 'block_data_screen'); ?></span>
                    </a>
                </div>
                <?php if ($network_teach) { ?>
                    <div class="top-level-menu-item <?php if(in_array($url, $network_page)) echo 'active' ?>" id="network">
                        <a href="one.php">
                            <i class="customicon customicon-network"></i>
                            <span class="courseText"><?PHP echo get_string('network_teach_title', 'block_data_screen'); ?></span>
                        </a>
                    </div>
                <?php } ?>
                <?php if ($zoom) { ?>
                    <div class="top-level-menu-item <?php if(in_array($url, $zoom_page)) echo 'active' ?>" id="zoom">
                        <a href="zoom.php">
                            <i class="customicon customicon-zoom"></i>
                            <span class="courseText"><?PHP echo get_string('live_classroom', 'block_data_screen'); ?></span>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="page-top">
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <li class="dropdown dropdown-user dropdown-dark">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img alt="" class="img-circle" src="<?PHP echo $avatar; ?>" />
                                <span class="username username-hide-on-mobile"><?PHP echo $USER->firstname; ?></span>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="rolelist">
                                <?PHP foreach ($role as $value) {
                                    if ($value['sort'] == $default_role) {
                                        echo '<li role="presentation" class="active" value="' . $value['id'] . '">
                                            <a role="menuitem" data-stopPropagation="true" tabindex="-1" href="#">' . $value['name'] . '</a>
                                        </li>';
                                    } else {
                                        echo '<li role="presentation" value="' . $value['id'] . '">
                                            <a role="menuitem" data-stopPropagation="true" tabindex="-1" href="#">' . $value['name'] . '</a>
                                        </li>';
                                    }
                                } ?>
                            </ul>
                        </li>
                        <li>
                            <a class="backmoodle-btn" href="/"><img class="leave_icon" src="amd/layouts/layout4/img/leave_icon@2x.png"><?PHP echo $site->fullname;?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END HEADER INNER -->
    </div>