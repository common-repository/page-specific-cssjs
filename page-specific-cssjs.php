<?php
/*
Plugin Name: Page Specific CSS/JS
Plugin URI: http://wordpress.org/extend/plugins/page-specific-cssjs/
Description: A very simple plugin that checks for css and javascript files matching the name of the current page and automatically includes the necessary markup in the <head> to include them in the page.
Author: Brad Touesnard
Version: 0.1
Author URI: http://bradt.ca/
*/

// Copyright (c) 2008 Brad Touesnard. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
//
// Borrowed a bunch of code from the WP-DB-Backup plugin
// which in turn borrowed from the phpMyAdmin project.
// Thanks to both for GPL.


function pscj_add_head() {
	$config = array(
		'.js' => array(
			'paths' => array('', 'js', '_js', '.js', 'javascript', 'res/js'),
			'markup' => '<script src="%s" type="text/javascript"></script>'
		),
		'.css' => array(
			'paths' => array('', 'css', '_css', '.css', 'styles', 'res/css'),
			'markup' => '<link type="text/css" rel="stylesheet" href="%s" media="screen" />'
		),
		'-ie6.css' => array(
			'paths' => array('', 'css', '_css', '.css', 'styles', 'res/css'),
			'markup' => '<!--[if lt IE 7]><link type="text/css" rel="stylesheet" href="%s" media="screen" /><![endif]-->'
		)
	);
	
	do_action('pscj_config');
	
	$file_name = pscj_get_page_name();

	if ($file_name != '') {
		foreach ($config as $ext => $conf) {
			$file_url = '';
			foreach ($conf['paths'] as $path) {
				if ($path != '') {
					$path .= '/';
				}
				
				$path = '/' . $path . $file_name . $ext;
			
				if (file_exists(TEMPLATEPATH . $path)) {
					$file_url = get_bloginfo('template_url') . $path;
					break;
				}
			}
			
			if ($file_url != '') {
				printf($conf['markup'], $file_url);
			}
		}
	}
}

function pscj_get_page_name() {
	global $wp_query, $post;

	$id = $post->ID;
	$show_on_front = get_option('show_on_front');
	$page_on_front = get_option('page_on_front');

	if (($show_on_front == 'page' && $page_on_front == $id)) {
		return 'home';
	}
	elseif (is_page()) {
		$page_obj = $wp_query->get_queried_object();
		return $page_obj->post_name;
	}
	else {
		return '';
	}
}

add_action('wp_head', 'pscj_add_head');
?>
