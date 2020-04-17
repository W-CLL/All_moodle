            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 sol-xs-12">
                    <ul class="nav nav-tabs">
                        <li <?php if($url=='course_detail.php') echo 'class="active"' ?>>
                            <a href="course_detail.php?id=<?PHP echo $_GET['id']?>"><?PHP echo get_string('basic_situation', 'block_data_screen');?></a>
                        </li>
                        <li <?php if($url=='course_teachinfo.php') echo 'class="active"' ?>>
                            <a href="course_teachinfo.php?id=<?PHP echo $_GET['id']?>"><?PHP echo get_string('teaching_situation', 'block_data_screen');?></a>
                        </li>
                        <li <?php if($url=='activity_analysis.php') echo 'class="active"' ?>>
                            <a href="activity_analysis.php?id=<?PHP echo $_GET['id']?>"><?PHP echo get_string('activity_analysis', 'block_data_screen');?></a>
                        </li>
                        <li <?php if(in_array($url,['person_list.php','person_detail.php'])) echo 'class="active"' ?>>
                            <a href="person_list.php?id=<?PHP echo $_GET['id']?>"><?PHP echo get_string('person_analysis', 'block_data_screen');?></a>
                        </li>
                    </ul>
                </div>
            </div>