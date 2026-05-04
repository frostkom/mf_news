(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl,
		InspectorControls = wp.blockEditor.InspectorControls;

	registerBlockType('mf/news',
	{
		title: script_news_block_wp.block_title,
		description: script_news_block_wp.block_description,
		icon: 'media-document',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'news_amount':
			{
				'type': 'string',
				'default': '6'
			},
			'news_categories':
			{
				'type': 'array',
				'default': ''
			},
			'news_images':
			{
				'type': 'string',
				'default': 'yes'
			},
			'news_datetime':
			{
				'type': 'string',
				'default': 'yes'
			},
			'news_shorten':
			{
				'type': 'string',
				'default': 'yes'
			}
		},
		'supports':
		{
			'html': false,
			'multiple': true,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							TextControl,
							{
								label: script_news_block_wp.news_amount_label,
								type: 'number',
								value: props.attributes.news_amount,
								onChange: function(value)
								{
									props.setAttributes({news_amount: value});
								},
								min: 0,
								max: 60,
								step: 3,
							}
						),
						el(
							SelectControl,
							{
								label: script_news_block_wp.news_categories_label,
								value: props.attributes.news_categories,
								options: convert_php_array_to_block_js(script_news_block_wp.news_categories),
								multiple: true,
								onChange: function(value)
								{
									props.setAttributes({news_categories: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_news_block_wp.news_images_label,
								value: props.attributes.news_images,
								options: convert_php_array_to_block_js(script_news_block_wp.yes_no_for_select),
								onChange: function(value)
								{
									props.setAttributes({news_images: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_news_block_wp.news_datetime_label,
								value: props.attributes.news_datetime,
								options: convert_php_array_to_block_js(script_news_block_wp.yes_no_for_select),
								onChange: function(value)
								{
									props.setAttributes({news_datetime: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_news_block_wp.news_shorten_label,
								value: props.attributes.news_shorten,
								options: convert_php_array_to_block_js(script_news_block_wp.yes_no_for_select),
								onChange: function(value)
								{
									props.setAttributes({news_shorten: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						script_news_block_wp.block_title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});

	registerBlockType('mf/promote',
	{
		title: script_news_block_wp.block_title2,
		description: script_news_block_wp.block_description2,
		icon: 'media-document',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'promote_include':
			{
				'type': 'array',
				'default': ''
			}
		},
		'supports':
		{
			'html': false,
			'multiple': true,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							SelectControl,
							{
								label: script_news_block_wp.promote_include_label,
								value: props.attributes.promote_include,
								options: convert_php_array_to_block_js(script_news_block_wp.promote_include),
								multiple: true,
								onChange: function(value)
								{
									props.setAttributes({promote_include: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						script_news_block_wp.block_title2
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();