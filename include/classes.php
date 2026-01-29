<?php

class mf_news
{
	function __construct(){}

	function block_render_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['news_amount'])){			$attributes['news_amount'] = 6;}
		if(!isset($attributes['news_categories'])){		$attributes['news_categories'] = [];}

		$plugin_base_include_url = plugins_url()."/mf_base/include/";
		mf_enqueue_style('style_base_grid_columns', $plugin_base_include_url."style_grid_columns.php");

		$out = "<div".parse_block_attributes(array('class' => "widget news square", 'attributes' => $attributes)).">
			<ul class='grid_columns'>";

				$arr_categories = [];

				$query_join = $query_where = "";

				if(count($attributes['news_categories']) > 0)
				{
					$query_join .= " INNER JOIN ".$wpdb->term_relationships." ON ".$wpdb->posts.".ID = ".$wpdb->term_relationships.".object_id INNER JOIN ".$wpdb->term_taxonomy." USING (term_taxonomy_id)";
					$query_where .= " AND term_id IN('".implode("','", $attributes['news_categories'])."')";
				}

				$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content, post_date FROM ".$wpdb->posts.$query_join." WHERE post_type = %s AND post_status = %s".$query_where." ORDER BY post_date DESC LIMIT 0, ".esc_sql($attributes['news_amount']), 'post', 'publish'));

				foreach($result as $r)
				{
					$post_id = $r->ID;
					$post_title = $r->post_title;
					$post_excerpt = $r->post_excerpt;
					$post_content = $r->post_content;
					$post_date = $r->post_date;

					if(count($attributes['news_categories']) != 1)
					{
						$arr_categories = get_the_category($post_id);
					}

					$post_url = get_permalink($post_id);
					$post_image = get_the_post_thumbnail_url($post_id, 'large'); // medium / large / full

					if($post_image != '')
					{
						$post_image = "<img src='".$post_image."' alt='".$post_title."'>";
					}

					else
					{
						$post_image = apply_filters('get_image_fallback', "");
					}

					$out .= "<li>
						<div class='image'><a href='".$post_url."'>".$post_image."</a></div>
						<div class='content'>
							<a href='".$post_url."'>".$post_title." (".var_export($attributes['news_categories'], true).")</a>
							<div class='meta'>";

								foreach($arr_categories as $arr_category)
								{
									$out .= "<span>".$arr_category->cat_name."</span>";
								}

								$out .= "<span class='grey'>".format_date($post_date)."</span>
							</div>
							<p class='text'>";

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

	function get_categories_for_select()
	{
		$arr_data = [];

		$arr_categories = get_categories(array(
			'taxonomy' => 'category',
			'parent' => 0,
			'hierarchical' => false,
			'hide_empty' => false,
		));

		foreach($arr_categories as $arr_category)
		{
			$arr_data[$arr_category->term_id] = $arr_category->name;
		}

		return $arr_data;
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
			'news_categories_label' => __("Categories", 'lang_news'),
			'news_categories' => $this->get_categories_for_select(),
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