<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_news/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

// Same as in Navigation
##########################
$setting_breakpoint_tablet = apply_filters('get_styles_content', '', 'max_width');

if($setting_breakpoint_tablet != '')
{
	preg_match('/^([0-9]*\.?[0-9]+)([a-zA-Z%]+)$/', $setting_breakpoint_tablet, $matches);

	$setting_breakpoint_tablet = $matches[1];
	$setting_breakpoint_suffix = $matches[2];

	$setting_breakpoint_mobile = ($setting_breakpoint_tablet * .775);
}

else
{
	$setting_breakpoint_tablet = get_option_or_default('setting_navigation_breakpoint_tablet', 1200);
	$setting_breakpoint_mobile = get_option_or_default('setting_navigation_breakpoint_mobile', 930);

	$setting_breakpoint_suffix = "px";
}
##########################

$setting_social_desktop_columns = 3;
$setting_social_tablet_columns = 2;
$setting_social_mobile_columns = 1;

if(!function_exists('calc_width'))
{
	function calc_width($columns)
	{
		return (100 / $columns) - ($columns > 1 ? 1 : 0);
	}
}

$column_width_desktop = calc_width($setting_social_desktop_columns);
$column_width_tablet = calc_width($setting_social_tablet_columns);
$column_width_mobile = calc_width($setting_social_mobile_columns);

echo "@media all
{
	.widget.news ul
	{
		display: flex;
	    flex-wrap: wrap;
		gap: 1%;
		list-style: none;
		padding-left: 0;
	}

		.widget.news li
		{
			background: #fff;
			box-shadow: 0 .5rem .75rem rgba(0, 0, 0, .15);
			flex: 0 1 auto;
			margin: 0 0 .6em;
			overflow: hidden;
			position: relative;
		    width: ".$column_width_desktop."%;
		}

			.widget.news li .image
			{
				background: rgba(0, 0, 0, .03);
			}

				.widget.news li .image img
				{
					display: block;
					object-fit: cover;
					transition: all 1s ease;
					width: 100%;
				}

					.widget.news li:hover .image img
					{
						transform: scale(1.1);
					}

			.widget.news .content
			{
				padding: 1em;
			}

				.widget.news .meta
				{
					font-size: .7em;
					margin: .5em 0;
				}

					.widget.news .date
					{
						color: #ccc;
						font-size: .9em;
					}

				.widget.news .content a
				{
					text-decoration: none;
				}

				.widget.news .content p
				{
					margin: 0;
				}

				.widget.news .content .wp-block-button
				{
					margin-top: .5em;
					text-align: right;
				}

					.widget.news .content .wp-block-button__link
					{
						font-size: .9em;
						padding: .5em 1em;
					}
}";

if($setting_breakpoint_mobile > 0 && $setting_breakpoint_tablet > $setting_breakpoint_mobile)
{
	echo "@media screen and (min-width: ".$setting_breakpoint_mobile.$setting_breakpoint_suffix.") and (max-width: ".($setting_breakpoint_tablet - 1).$setting_breakpoint_suffix.")
	{
		.widget.news li
		{
			width: ".$column_width_tablet."%;
		}
	}";
}

if($setting_breakpoint_mobile > 0)
{
	echo "@media screen and (max-width: ".($setting_breakpoint_mobile - 1).$setting_breakpoint_suffix.")
	{
		.widget.news li
		{
			width: ".$column_width_mobile."%;
		}
	}";
}