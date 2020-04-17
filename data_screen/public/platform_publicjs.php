    <script type="text/javascript">
        $("body").on('click', '[data-stopPropagation]', function(e) {
            e.stopPropagation();
        });
        $('#rolelist li').click(function() {
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
        function getLinkCourseID() {
            var role = $("#rolelist li.active").val();
            var data = Array({
                'index': 0,
                'methodname': 'block_data_screen_get_semester',
                'args': {
                    'role': role,
                    'user': '<?PHP echo $USER->id; ?>'
                }
            });
            $.ajax({
                type: "POST",
                contentType: "application/json;",
                url: "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey; ?>",
                data: JSON.stringify(data),
                success: function(result) {
                    if (typeof result[0].data != 'undefined') {
                        var result = result[0].data;
                        var leftHtml = '';
                        var course_list = [];
                        var courseId = '';
                        for (var i = 0; i < result.length; i++) {
                            var item = result[i];
                            course_list = item.course_list;
                            if (course_list.length >= 1) {
                                for (var j = 0; j < course_list.length; j++) {
                                    var itemJ = course_list[j];
                                    if (j === 0) {
                                        courseId = itemJ.id;
                                    }
                                }
                            }
                        }
                        $('#course').click(function() {
                            var courseAHref = $(this).children('a').attr('href');
                            window.location.href = courseAHref + "?id=" + courseId;
                        });
                    } else {}
                },
                error: function(e) {
                    alert("<?PHP echo get_string('network_error', 'block_data_screen'); ?>");
                }
            });
        }
        getLinkCourseID();
    </script>