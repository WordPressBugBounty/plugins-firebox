<?php

defined('ABSPATH') or die;

return [
	[
		'name' => 'operator',
		'type' => 'Comparator'
	],
	[
		'name' => 'value',
		'type' => 'Repeater',
		'render_group' => true,
		'label' => 'FPF_PRODUCTS_IN_CART',
		'btn_label' => false,
		'class' => ['fpf-repeater-gap-1', 'only-remove-action'],
		'remove_action_class' => ['no-confirm'],
		'default_values' => [
			[
				'value' => ''
			]
		],
		'fields' => [
			[
				'type' => 'CustomDiv',
				'class' => ['fpf-side-by-side-items', 'fpf-gap-6px']
			],
			[
				'type' => 'SearchDropdown',
				'name' => 'value',
				'path' => '\FPFramework\Helpers\EDDHelper',
				'control_inner_class' => ['dropdown-min-width-200', 'truncate-text-200px'],
				'search_query_placeholder' => fpframework()->_('FPF_SEARCH_DOWNLOAD'),
				'placeholder' => 'FPF_PRODUCT',
				'render_group' => false,
				'items' => fpframework()->helper->edd->getItems(),
				'multiple' => false,
				'lazyload' => true
			],
			[
				'type' => 'CustomDiv',
				'class' => ['fpf-side-by-side-items', 'fpf-gap-6px']
			],
			[
				'type' => 'Label',
				'render_group' => false,
				'text' => 'quantity is'
			],
			[
				'name' => 'quantity_operator',
				'type' => 'Dropdown',
				'input_class' => ['width-auto'],
				'render_group' => false,
				'default' => 'any',
				'choices' => [
					'any' => strtolower(fpframework()->_('FPF_ANY')),
					'equals' => strtolower(fpframework()->_('FPF_IS_EQUAL')),
					'less_than' => strtolower(fpframework()->_('FPF_FEWER_THAN')),
					'less_than_or_equal_to' => strtolower(fpframework()->_('FPF_FEWER_THAN_OR_EQUAL_TO')),
					'greater_than' => strtolower(fpframework()->_('FPF_GREATER_THAN')),
					'greater_than_or_equal_to' => strtolower(fpframework()->_('FPF_GREATER_THAN_OR_EQUAL_TO')),
					'range' => strtolower(fpframework()->_('FPF_IS_BETWEEN')),
				]
			],
			// Min Quantity
			[
				'type' => 'CustomDiv',
				'class' => ['fpf-side-by-side-items', 'fpf-gap-6px', 'fpf-range-input-fields'],
				'showon' => '[value][ITEM_ID][quantity_operator]!:any'
			],
			[
				'type' => 'Number',
				'name' => 'quantity_value1',
				'render_group' => false,
				'default' => 1,
				'min' => 1,
				'showon' => '[value][ITEM_ID][quantity_operator]:equals,less_than,less_than_or_equal_to,greater_than,greater_than_or_equal_to,range'
			],
			// Max Quantity
			[
				'type' => 'CustomDiv',
				'class' => ['fpf-side-by-side-items', 'fpf-gap-6px', 'fpf-range-input-fields'],
				'showon' => '[value][ITEM_ID][quantity_operator]:range'
			],
			[
				'type' => 'Label',
				'name' => 'quantity_value_label',
				'render_group' => false,
				'text' => sprintf(' %s ', fpframework()->_('FPF_AND'))
			],
			[
				'type' => 'Number',
				'name' => 'quantity_value2',
				'render_group' => false,
				'min' => 1
			],
			[
				'type' => 'CustomDiv',
				'position' => 'end'
			],
			[
				'type' => 'CustomDiv',
				'position' => 'end'
			],
			[
				'type' => 'CustomDiv',
				'position' => 'end'
			],
			[
				'type' => 'CustomDiv',
				'position' => 'end'
			],
		]
	]
];