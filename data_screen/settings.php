<?php
defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    $options = [
        0 => get_string('false', 'block_data_screen'),
        1 => get_string('true', 'block_data_screen')
    ];

    # logout time
    $setting = new admin_setting_configtext(
        'block_data_screen_logout_time',
        get_string('logout_time', 'block_data_screen'),
        get_string('logout_time_desc', 'block_data_screen'),
        30,
        PARAM_INT
    );
    $settings->add($setting);

    # display attribute course tab
    $setting = new admin_setting_configselect(
        'block_data_screen_display',
        get_string('display_attribute_course', 'block_data_screen'),
        '',
        1,
        $options
    );
    $settings->add($setting);

    # role id
    $setting = new admin_setting_configtext(
        'block_data_screen_edu_role',
        get_string('edu_role', 'block_data_screen'),
        '',
        0,
        PARAM_INT
    );
    $settings->add($setting);

    # statistics zoom
    $options = [
        0 => get_string('false', 'block_data_screen'),
        1 => get_string('true', 'block_data_screen')
    ];
    $setting = new admin_setting_configselect(
        'block_data_screen_netwok_teach',
        get_string('netwok_teach', 'block_data_screen'),
        '',
        0,
        $options
    );
    $settings->add($setting);


    # statistics zoom
    $zoom = $DB->get_field('modules', 'id', ['name'=>'zoom']);
    if ($zoom) {
        $options = [
            0 => get_string('false', 'block_data_screen'),
            1 => get_string('true', 'block_data_screen')
        ];
        $setting = new admin_setting_configselect(
            'block_data_screen_zoom',
            get_string('zoom_statistics', 'block_data_screen'),
            '',
            0,
            $options
        );
        $settings->add($setting);
    }

    # copyright info
    $setting = new admin_setting_configtext(
        'block_data_screen_copyright',
        get_string('copyright', 'block_data_screen'),
        get_string('copyright_desc', 'block_data_screen'),
        'Copyright &#169 2019 South China Normal University.All Rights Reserved',
        PARAM_TEXT
    );
    $settings->add($setting);

    $link = \html_writer::link('/blocks/data_screen/semester.php', get_string('semester_setting', 'block_data_screen'));
    $settings->add(new admin_setting_heading('block_data_screen_addheading', '', $link));
}