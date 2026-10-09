<?php

class mf_news
{
	function __construct(){}

	function block_render_news_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['news_amount'])){			$attributes['news_amount'] = 6;}
		if(!isset($attributes['news_categories'])){		$attributes['news_categories'] = [];}
		if(!isset($attributes['news_style'])){			$attributes['news_style'] = '';}
		if(!isset($attributes['news_images'])){			$attributes['news_images'] = 'yes';}
		if(!isset($attributes['news_datetime'])){		$attributes['news_datetime'] = 'yes';}

		if($attributes['news_images'] == 'no')
		{
			do_log(__FUNCTION__." - Images are deactivted on this site");
		}

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

			$post_url = "#";

			if($post_content != '')
			{
				$post_url = get_permalink($post_id);
			}

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
					if($post_content != '')
					{
						$out_temp .= "<a href='".$post_url."'>";
					}
					
						$out_temp .= "<div class='grid_image'>"
							.$post_image;

							if($post_excerpt == '')
							{
								$out_temp .= "<div class='grid_content'>
									<div>
										<span class='grid_title'>".$post_title."</span>
									</div>
								</div>";
							}

							$out_meta_temp = "";

							if(count($attributes['news_categories']) != 1)
							{
								$arr_categories = get_the_category($post_id);
							}

							foreach($arr_categories as $arr_category)
							{
								if($arr_category->cat_name != __("Uncategorized", 'lang_news'))
								{
									$out_meta_temp .= "<span>".$arr_category->cat_name."</span>";
								}
							}

							$arr_tags = get_the_tags($post_id);

							if(!empty($arr_tags))
							{
								foreach($arr_tags as $post_tag)
								{
									$out_meta_temp .= "<span class='tag'>".$post_tag->name."</span>";
								}
							}

							if($attributes['news_datetime'] == 'yes')
							{
								$out_meta_temp .= "<span>".format_date($post_date)."</span>";
							}

							if($out_meta_temp != "")
							{
								$out_temp .= "<div class='grid_meta'>".$out_meta_temp."</div>";
							}
							
						$out_temp .= "</div>";

					if($post_content != '')
					{
						$out_temp .= "</a>";
					}
				}

				if($post_excerpt != '')
				{
					$out_temp .= "<div class='grid_content'>";

						if($post_title != '')
						{
							if($post_content != '')
							{
								$out_temp .= "<a href='".$post_url."' class='grid_title'>".$post_title."</a>";
							}

							else
							{
								$out_temp .= "<span class='grid_title'>".$post_title."</span>";
							}
						}

						if($post_excerpt != '')
						{
							$out_temp .= "<p class='grid_text'>"
								.$post_excerpt
							."</p>";
						}

						/*else
						{
							$out_temp .= "<div class='grid_text'>"
								.apply_filters('the_content', $post_content)
							."</div>";
						}*/

					$out_temp .= "</div>";

					if($post_content != $post_excerpt)
					{
						$out_temp .= "<div class='grid_buttons'>
							<div class='wp-block-button'>
								<a href='".$post_url."' class='wp-block-button__link'>".__("Read More", 'lang_news')."</a>
							</div>
						</div>";
					}
				}

			$out_temp .= "</li>";

			$arr_out[] = $out_temp;
		}

		if(count($arr_out) > 0)
		{
			switch($attributes['news_style'])
			{
				case 'scroll':
					do_action('load_grid_columns_scrollable');

					$block_class = " is_scrollable";
				break;

				default:
					do_action('load_grid_columns');

					$block_class = "";
				break;
			}

			$out = "<div".parse_block_attributes(array('class' => "widget news square".$block_class, 'attributes' => $attributes)).">
				<ul class='grid_columns'>" //".(count($arr_out) < 3 ? " grid_grow" : "")."
					.implode("", $arr_out)
				."</ul>
			</div>";
		}

		return $out;
	}

	function block_render_news_promo_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['news_day_limit'])){		$attributes['news_day_limit'] = 0;}
		if(!isset($attributes['news_text'])){			$attributes['news_text'] = "";}
		if(!isset($attributes['news_categories'])){		$attributes['news_categories'] = [];}

		$out = $query_join = $query_where = "";

		if(count($attributes['news_categories']) > 0)
		{
			$query_join .= " INNER JOIN ".$wpdb->term_relationships." ON ".$wpdb->posts.".ID = ".$wpdb->term_relationships.".object_id INNER JOIN ".$wpdb->term_taxonomy." USING (term_taxonomy_id)";
			$query_where .= " AND term_id IN('".implode("','", $attributes['news_categories'])."')";
		}

		if($attributes['news_day_limit'] > 0)
		{
			$query_where .= " AND post_date > DATE_SUB(NOW(), INTERVAL ".esc_sql($attributes['news_day_limit'])." DAY)";
		}

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM ".$wpdb->posts.$query_join." WHERE post_type = %s AND post_status = %s".$query_where." ORDER BY post_date DESC LIMIT 0, 1", 'post', 'publish'));

		foreach($result as $r)
		{
			$post_id = $r->ID;
			$post_title = $r->post_title;

			$post_url = get_permalink($post_id);

			$out .= "<div".parse_block_attributes(array('class' => "widget news_promo", 'attributes' => $attributes)).">";

				if($attributes['news_text'])
				{
					$out .= "<span>".$attributes['news_text']."</span>&nbsp;";
				}

				$out .= "<a href='".$post_url."'>".$post_title."</a>
			</div>";
		}

		return $out;
	}

	function block_render_pages_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['promote_include'])){			$attributes['promote_include'] = [];}
		if(!isset($attributes['promote_display_title'])){	$attributes['promote_display_title'] = 'yes';}

		$out = "";

		if(count($attributes['promote_include']) > 0)
		{
			$arr_out = [];

			$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s AND ID IN('".implode("','", $attributes['promote_include'])."') ORDER BY menu_order ASC", 'page', 'publish'));

			foreach($result as $r)
			{
				$post_id = $r->ID;
				$post_title = $r->post_title;
				$post_excerpt = $r->post_excerpt;
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
							<div class='grid_image'>"
								.$post_image;

								if($attributes['promote_display_title'] == 'yes')
								{
									$out_temp .= "<div class='grid_content'>
										<div>
											<span class='grid_title'>".$post_title."</span>";

											if($post_excerpt != '')
											{
												$out_temp .= "<p class='grid_text'>".$post_excerpt."</p>";
											}

										$out_temp .= "</div>
									</div>";
								}

							$out_temp .= "</div>
						</a>
					</li>";
				}

				$arr_out[] = $out_temp;
			}

			if(count($arr_out) > 0)
			{
				do_action('load_grid_columns');

				$out .= "<div".parse_block_attributes(array('class' => "widget promote square", 'attributes' => $attributes)).">
					<ul class='grid_columns".(count($arr_out) < 3 ? " grid_grow" : "")."'>"
						.implode("", $arr_out)
					."</ul>
				</div>";
			}
		}

		return $out;
	}

	function block_render_post_type_callback($attributes)
	{
		global $wpdb, $post;

		if(!isset($attributes['post_type_include'])){			$attributes['post_type_include'] = [];}

		$out = "";

		if(count($attributes['post_type_include']) > 0)
		{
			$arr_out = [];

			$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM ".$wpdb->posts." WHERE post_type IN('".implode("','", $attributes['post_type_include'])."') AND post_status = %s AND ID != '%d' ORDER BY menu_order ASC LIMIT 0, 6", 'publish', $post->ID));

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
						<div class='grid_image'>
							<a href='".$post_url."'>"
								.$post_image
							."</a>
						</div>
						<div class='grid_content'>
							<a href='".$post_url."' class='grid_title'>".$post_title."</a>
						</div>
					</li>";
				}

				$arr_out[] = $out_temp;
			}

			if(count($arr_out) > 0)
			{
				do_action('load_grid_columns');

				$out .= "<div".parse_block_attributes(array('class' => "widget news_post_type square", 'attributes' => $attributes)).">
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

	function get_style_for_select()
	{
		$arr_data = [];
		$arr_data[''] = "-- ".__("Choose Here", 'lang_news')." --";
		$arr_data['default'] = __("Default", 'lang_news');
		$arr_data['scroll'] = __("Horizontal Scroll", 'lang_news');

		return $arr_data;
	}

	function enqueue_block_editor_assets()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_news_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		$arr_data_pages = [];
		get_post_children(array('post_type' => 'page'), $arr_data_pages);

		$arr_data_post_types = [];

		foreach(get_post_types(array('public' => true, 'exclude_from_search' => false), 'objects') as $arr_post_type)
		{
			$arr_data_post_types[$arr_post_type->name] = $arr_post_type->label;
		}

		wp_localize_script('script_news_block_wp', 'script_news_block_wp', array(
			'block_title' => __("Posts", 'lang_news'),
			'block_description' => __("Display Posts", 'lang_news'),
			'news_amount_label' => __("Amount", 'lang_news'),
			'news_categories_label' => __("Categories", 'lang_news'),
			'news_categories' => $this->get_categories_for_select(),
			'news_style_label' => __("Style", 'lang_news'),
			'arr_news_style' => $this->get_style_for_select(),
			'news_images_label' => __("Display Images", 'lang_news'),
			'news_datetime_label' => __("Display Date", 'lang_news'),
			'yes_no_for_select' => get_yes_no_for_select(),
			'block_title_news_promo' => __("Promote Post", 'lang_news'),
			'block_description_news_promo' => __("Display Post Promotion", 'lang_news'),
			'news_day_limit_label' => __("Day Limit", 'lang_news'),
			'news_text_label' => __("Text", 'lang_news'),
			'block_title_pages' => __("Other Pages", 'lang_news'),
			'block_description_pages' => __("Display other pages", 'lang_news'),
			'promote_include_label' => __("Include", 'lang_news'),
			'promote_include' => $arr_data_pages,
			'promote_display_title_label' => __("Display Title", 'lang_news'),
			'block_title_posttype' => __("Other Posts from Type", 'lang_news'),
			'block_description_posttype' => __("Display other posts from type", 'lang_news'),
			'post_type_include_label' => __("Include", 'lang_news'),
			'post_type_include' => $arr_data_post_types,
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

		register_block_type('mf/newspromo', array(
			'editor_script' => 'script_news_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_news_promo_callback'),
		));

		register_block_type('mf/promote', array(
			'editor_script' => 'script_news_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_pages_callback'),
		));

		register_block_type('mf/posttype', array(
			'editor_script' => 'script_news_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_post_type_callback'),
		));
	}
}