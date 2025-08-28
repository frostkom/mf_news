<?php

class mf_news
{
	function __construct(){}

	function block_render_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['news_amount'])){		$attributes['news_amount'] = 6;}
		if(!isset($attributes['news_columns'])){	$attributes['news_columns'] = 3;}

		$plugin_include_url = plugin_dir_url(__FILE__);

		mf_enqueue_style('style_news', $plugin_include_url."style.php");

		$out = "<div".parse_block_attributes(array('class' => "widget news", 'attributes' => $attributes)).">
			<ul>";

				$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content, post_date FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s ORDER BY post_date DESC LIMIT 0, ".esc_sql($attributes['news_amount']), 'post', 'publish'));

				foreach($result as $r)
				{
					$post_id = $r->ID;
					$post_title = $r->post_title;
					$post_excerpt = $r->post_excerpt;
					$post_content = $r->post_content;
					$post_date = $r->post_date;

					$arr_categories = get_the_category($post_id);

					$post_url = get_permalink($post_id);
					$post_thumbnail = get_the_post_thumbnail($post_id, 'medium'); // medium / large / full

					if($post_thumbnail == '')
					{
						$post_thumbnail = apply_filters('get_image_fallback', "");
					}

					$out .= "<li>
						<div class='image'>".$post_thumbnail."</div>
						<div class='content'>
							<a href='".$post_url."' class='bold'>".$post_title."</a>
							<div class='meta'>
								<span class='name'>";

									foreach($arr_categories as $category)
									{
										$out .= "<span>".$category->cat_name."</span> ";
									}
								
								$out .= "</span>
								<span class='date'>".format_date($post_date)."</span>
							</div>
							<p>";

								if($post_excerpt != '')
								{
									$out .= $post_excerpt;
								}

								else
								{
									$out .= shorten_text(array('string' => strip_tags($post_content), 'limit' => 120));
								}

							$out .= "</p>
							<div class='wp-block-button'>
								<a href='".$post_url."' class='wp-block-button__link'>".__("Read More", 'lang_news')."</a>
							</div>
						</div>
					</li>";
				}

			$out .= "</ul>
		</div>";

		return $out;
	}

	function enqueue_block_editor_assets()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_news_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		wp_localize_script('script_news_block_wp', 'script_news_block_wp', array(
			'block_title' => __("News", 'lang_news'),
			'block_description' => __("Display News", 'lang_news'),
			'news_amount_label' => __("Amount", 'lang_news'),
			'news_columns_label' => __("Columns", 'lang_news'),
		));
	}

	function init()
	{
		load_plugin_textdomain('lang_news', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		register_block_type('mf/news', array(
			'editor_script' => 'script_news_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
		));
	}
}