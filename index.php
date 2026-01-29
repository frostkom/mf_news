<?php
/*
Plugin Name: MF News
Plugin URI: https://github.com/frostkom/mf_news
Description:
Version: 1.0.14
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_news
Domain Path: /lang
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_news = new mf_news();

	add_action('enqueue_block_editor_assets', array($obj_news, 'enqueue_block_editor_assets'));
	add_action('init', array($obj_news, 'init'));
}