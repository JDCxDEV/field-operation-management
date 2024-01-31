<?php

return array(

    // Main menu
    'main' => array(
        //// Dashboard
        array(
            'title' => 'Dashboard',
            'path'  => '',
            'icon'  => '<i class="ki-duotone ki-abstract-32 fs-3"><i class="path1"></i><i class="path2"></i></i>',
        ),

        //// Modules
        array(
            'classes' => array('content' => 'pt-8 pb-2'),
            'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Management</span>',
        ),

        // Account
        array(
            'title'      => 'Companies',
            'icon'       => '<i class="ki-duotone ki-bank fs-3"><i class="path1"></i><i class="path2"></i></i>',
            'path'   => 'companies',
        ),
        array(
            'title'      => 'Markets',
            'icon'       => '<i class="ki-duotone ki-abstract-28 fs-3"><i class="path1"></i><i class="path2"></i></i>',
            'path'   => 'markets',
        ),
        array(
            'title'      => 'Users',
            'icon'       => '<i class="ki-duotone ki-people fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                <i class="path3"></i>
                <i class="path4"></i>
                <i class="path5"></i>
                </i>',
            'path'   => 'users',
        ),
        array(
            'title'      => 'Lead Form',
            'icon'       => '<i class="ki-duotone ki-clipboard fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                <i class="path3"></i>
                </i>',
            'path'   => 'agent-submission-forms/create',
        ),
        array(
            'title'      => 'Agent Submission Forms',
            'icon'       => '<i class="ki-duotone ki-data fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                <i class="path3"></i>
                <i class="path4"></i>
                <i class="path5"></i>
                </i>',
            'path'   => 'agent-submission-forms',
        ),
        array(
            'classes' => array('content' => 'pt-8 pb-2'),
            'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Admin</span>',
            'admin_only' => true
        ),

        array(
            'title'      => 'Form Manager',
            'icon'       => '<i class="ki-duotone ki-setting-2 fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                </i>',
            'path'       => 'form-templates',
        ),

        array(
            'title'      => 'FAQ Content Management',
            'icon'       => '<i class="ki-duotone ki-pencil fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                </i>',
            'path'       => 'topics',
        ),

        array(
            'title'      => 'Activity Logs',
            'icon'       => '<i class="ki-duotone ki-technology-4 fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                <i class="path3"></i>
                <i class="path4"></i>
                <i class="path5"></i>
                <i class="path6"></i>
                <i class="path7"></i>
                </i>',
            'path'       => 'activity-logs',
        ),

        array(
            'classes' => array('content' => 'pt-8 pb-2'),
            'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Others</span>',
        ),

        array(
            'title'      => 'FAQs',
            'icon'       => '<i class="ki-duotone ki-question-2 fs-3">
                <i class="path1"></i>
                <i class="path2"></i>
                <i class="path3"></i>
                </i>',
            'path'       => 'faqs',
        ),
    ),

    // Horizontal menu
    'horizontal'    => array(
        // Dashboard
        array(
            'title'   => '',
            'path'    => '',
            'classes' => array('item' => 'me-lg-1'),
        ),
    ),
);
