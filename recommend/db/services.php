<?php

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'block_recommend_Inclass_recommend' => array(
        'classname'     => 'block_recommend_external',
        'methodname'    => 'Inclass_recommend',
        'classpath'     => 'blocks/recommend/externallib.php',
        'description'   => 'Platform overview',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'block/recommend:statistics',
    ),
    'block_recommend_get_activities_completion_status' => array(
        'classname'     => 'block_recommend_external',
        'methodname'    => 'get_activities_completion_status',
        'classpath'     => 'blocks/recommend/externallib.php',
        'description'   => 'Get activities completion status',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'block/recommend:statistics',
    ),

    
);