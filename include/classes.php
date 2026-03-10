<?php

class mf_news
{
	function __construct(){}

	function block_render_news_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['news_amount'])){			$attributes['news_amount'] = 6;}
		if(!isset($attributes['news_categories'])){		$attributes['news_categories'] = [];}
		if(!isset($attributes['news_images'])){			$attributes['news_images'] = 'yes';}
		if(!isset($attributes['news_datetime'])){		$attributes['news_datetime'] = 'yes';}
		if(!isset($attributes['news_shorten'])){		$attributes['news_shorten'] = 'yes';}

		$arr_out = $arr_categories = [];
		$out = $query_join = $query_where = "";

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

			if($post_excerpt == '' && $attributes['news_shorten'] == 'yes')
			{
				$post_excerpt = shorten_text(array('string' => strip_tags($post_content), 'limit' => 120));
			}

			if(count($attributes['news_categories']) != 1)
			{
				$arr_categories = get_the_category($post_id);
			}

			$post_url = get_permalink($post_id);

			if($attributes['news_images'] == 'yes')
			{
				$post_image = get_the_post_thumbnail_url($post_id, 'large'); // medium / large / full

				if($post_image != '')
				{
					$post_image = "<img src='".$post_image."' alt='".$post_title."'>";
				}

				else
				{
					$post_image = apply_filters('get_image_fallback', "");
				}
			}

			$out_temp = "<li>";

				if($attributes['news_images'] == 'yes')
				{
					$out_temp .= "<div class='image'><a href='".$post_url."'>".$post_image."</a></div>";
				}

				$out_temp .= "<div class='content'>";

					if($post_title != '')
					{
						$out_temp .= "<a href='".$post_url."' class='grid_title'>".$post_title."</a>";
					}

					if(count($arr_categories) > 0 || $attributes['news_datetime'] == 'yes')
					{
						$out_temp .= "<div class='meta'>";

							foreach($arr_categories as $arr_category)
							{
								if($arr_category->cat_name != __("Uncategorized", 'lang_news'))
								{
									$out_temp .= "<span>".$arr_category->cat_name."</span>";
								}
							}

							if($attributes['news_datetime'] == 'yes')
							{
								$out_temp .= "<span class='grey'>".format_date($post_date)."</span>";
							}

						$out_temp .= "</div>";
					}

					if($post_excerpt != '')
					{
						$out_temp .= "<p class='text'>"
							.$post_excerpt
						."</p>";

						if($post_content != $post_excerpt)
						{
							$out_temp .= "<div class='wp-block-button'>
								<a href='".$post_url."' class='wp-block-button__link'>".__("Read More", 'lang_news')."</a>
							</div>";
						}
					}

					else
					{
						$out_temp .= "<div class='text'>"
							.apply_filters('the_content', $post_content)
						."</div>";
					}

				$out_temp .= "</div>
			</li>";

			$arr_out[] = $out_temp;
		}

		if(count($arr_out) > 0)
		{
			$plugin_base_include_url = plugins_url()."/mf_base/include/";
			mf_enqueue_style('style_base_grid_columns', $plugin_base_include_url."style_grid_columns.php");

			$out = "<div".parse_block_attributes(array('class' => "widget news square", 'attributes' => $attributes)).">
				<ul class='grid_columns".(count($arr_out) < 3 ? " grid_grow" : "")."'>"
					.implode("", $arr_out)
				."</ul>
			</div>";
		}

		return $out;
	}

	function block_render_promote_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['promote_include'])){			$attributes['promote_include'] = [];}

		if(count($attributes['promote_include']) > 0)
		{
			$arr_out = [];

			$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s AND ID IN('".implode("','", $attributes['promote_include'])."') ORDER BY menu_order ASC", 'page', 'publish'));

			foreach($result as $r)
			{
				$post_id = $r->ID;
				$post_title = $r->post_title;
				$post_content = $r->post_content;

				$out_temp = "";

				if(strlen($post_content) < 60 && preg_match("/youtube\.com|youtu\.be/i", $post_content))
				{
					$out_temp .= "<li>
						<div class='video'>".apply_filters('the_content', $post_content)."</div>
					</li>";
				}

				else
				{
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

					$out_temp .= "<li>
						<a href='".$post_url."'>
							<div class='image'>"
								.$post_image
								."<div class='content'><span>".$post_title."</span></div>"
							."</div>
						</a>
					</li>";
				}

				$arr_out[] = $out_temp;
			}

			if(count($arr_out) > 0)
			{
				$plugin_base_include_url = plugins_url()."/mf_base/include/";
				mf_enqueue_style('style_base_grid_columns', $plugin_base_include_url."style_grid_columns.php");

				$out = "<div".parse_block_attributes(array('class' => "widget promote square", 'attributes' => $attributes)).">
					<ul class='grid_columns".(count($arr_out) < 3 ? " grid_grow" : "")."'>"
						.implode("", $arr_out)
					."</ul>
				</div>";
			}
		}

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

		$arr_data = [];
		get_post_children(array('post_type' => 'page'), $arr_data); //, 'order_by' => 'post_title'

		wp_localize_script('script_news_block_wp', 'script_news_block_wp', array(
			'block_title' => __("News", 'lang_news'),
			'block_description' => __("Display News", 'lang_news'),
			'news_amount_label' => __("Amount", 'lang_news'),
			'news_categories_label' => __("Categories", 'lang_news'),
			'news_categories' => $this->get_categories_for_select(),
			'news_images_label' => __("Display Images", 'lang_news'),
			'news_datetime_label' => __("Display Date", 'lang_news'),
			'news_shorten_label' => __("Shorten Text", 'lang_news'),
			'yes_no_for_select' => get_yes_no_for_select(),
			'block_title2' => __("Promote", 'lang_news'),
			'block_description2' => __("Display Promotions", 'lang_news'),
			'promote_include_label' => __("Include", 'lang_news'),
			'promote_include' => $arr_data,
		));
	}

	function init()
	{
		load_plugin_textdomain('lang_news', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		register_block_type('mf/news', array(
			'editor_script' => 'script_news_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_news_callback'),
		));

		register_block_type('mf/promote', array(
			'editor_script' => 'script_news_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_promote_callback'),
		));
	}
}