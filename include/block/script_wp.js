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
			}
		},
		'supports':
		{
			'html': false,
			'multiple': false,
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
								max: 12,
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
})();