<?php

$events = new CustomPostType(
    'events',
    [
        'singular' => __('Event'),
        'plural' => __('Events'),
        'slug' => __('events'),
    ],
    [
        'menu_icon' => 'dashicons-flaticon-stadium',
        'has_archive' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'with_front' => false,
        ],
        'supports' => [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'revisions',
        ],
    ]
);

$events->addTaxonomy(
    'event-type',
    [
        'singular' => __('Event Type'),
        'plural' => __('Event Types'),
        'slug' => __('event-type'),
    ],
    [
        'show_in_rest' => true,
    ]
);

$events->addFieldGroup(
    [
        'title' => __('Events Data'),
        'fields' => [
            acf_group(
                [
                    'name' => 'event-meta',
                    'label' => __('Event Details'),
                    'layout' => 'row',
                    'sub_fields' => [
                        acf_date_picker(
                            [
                                'name' => 'date',
                                'label' => __('Event Date'),
                                'instructions' => __('The day of the event'),
                                'required' => false,
                                'display_format' => 'd.m.Y',
                                'return_format' => 'd.m.Y'
                            ]
                        ),
                        acf_time_picker(
                            [
                                'name' => 'doors_open',
                                'label' => __('Doors open'),
                                'instructions' => __('What time do the doors open'),
                                'required' => false,
                                'display_format' => 'H:i',
                                'return_format' => 'H:i'
                            ]
                        ),
                        acf_time_picker(
                            [
                                'name' => 'show_start',
                                'label' => __('Show Starts'),
                                'instructions' => __('Time the event starts'),
                                'required' => false,
                                'display_format' => 'H:i',
                                'return_format' => 'H:i'
                            ]
                        ),
                        acf_time_picker(
                            [
                                'name' => 'show_end',
                                'label' => __('Show Ends'),
                                'instructions' => __('Time the event ends'),
                                'required' => false,
                                'display_format' => 'H:i',
                                'return_format' => 'H:i'
                            ]
                        ),
                        acf_url(
                            [
                                'name' => 'ticket_link',
                                'label' => __('Ticket Link'),
                                'instructions' => __('Ticket sales link'),
                                'required' => false,
                            ]
                        ),
                        acf_number(
                            [
                                'name' => 'ticket_price',
                                'label' => __('Minimum ticket price'),
                                'prepend' => '€',
                                'min' => 0,
                                'step' => '0.01',
                            ]
                        ),
                        acf_repeater(
                            [
                                'name' => 'lineup',
                                'label' => __('Line-Up'),
                                'button_label' => __('Add Act'),
                                'sub_fields' => [
                                    acf_text(
                                        [
                                            'name' => 'act-name',
                                            'label' => __('Act Name'),
                                        ]
                                    ),
                                    acf_url(
                                        [
                                            'name' => 'act-link',
                                            'label' => __('Link to act')
                                        ]
                                    )
                                ]
                            ]
                        ),
                    ],
                ]
            ),
            acf_group(
                [
                    'name' => 'event-multi-day',
                    'label' => __('Multi Day Event Data'),
                    'layout' => 'block',
                    'sub_fields' => [
                        acf_true_false(
                            [
                                'name' => 'multiday_event',
                                'label' => __('Multi-day event'),
                                'instructions' => 'Add more dates for this event',
                                'default_value' => false,
                                'ui' => true,
                            ]
                        ),
                        acf_repeater(
                            [
                                'name' => 'additional_dates',
                                'label' => __('Additional Dates'),
                                'instructions' => __('Add additional dates for the event'),
                                'button_label' => __('Add Day'),
                                'min' => 1,
                                'layout' => 'row',
                                'sub_fields' => [
                                    acf_date_picker(
                                        [
                                            'name' => 'date',
                                            'label' => __('Event Date'),
                                            'instructions' => __('The day of the event'),
                                            'required' => false,
                                            'display_format' => 'd.m.Y',
                                            'return_format' => 'd.m.Y'
                                        ]
                                    ),
                                    acf_group(
                                        [
                                            'name' => 'doors_open',
                                            'label' => __('Doors open'),
                                            'instructions' => __('What time do the doors open'),
                                            'layout' => 'table',
                                            'sub_fields' => [
                                                acf_true_false(
                                                    [
                                                        'name' => 'inherit_doors_open',
                                                        'label' => __('Inherit'),
                                                        'instructions' => __('Inherit data from original date'),
                                                        'default_value' => true,
                                                        'ui' => true,
                                                    ]
                                                ),
                                                acf_time_picker(
                                                    [
                                                        'name' => 'doors_open',
                                                        'label' => __('Doors open'),
                                                        'required' => false,
                                                        'display_format' => 'H:i',
                                                        'return_format' => 'H:i',
                                                        'conditional_logic' => [
                                                            [
                                                                acf_conditional('inherit_doors_open', false),
                                                            ],
                                                        ],
                                                    ]
                                                ),
                                            ],
                                        ]
                                    ),
                                    acf_group(
                                        [
                                            'name' => 'show_start',
                                            'label' => __('Show Starts'),
                                            'instructions' => __('Time the event starts'),
                                            'layout' => 'table',
                                            'sub_fields' => [
                                                acf_true_false(
                                                    [
                                                        'name' => 'inherit_show_start',
                                                        'label' => __('Inherit'),
                                                        'instructions' => __('Inherit data from original date'),
                                                        'default_value' => true,
                                                        'ui' => true,
                                                    ]
                                                ),
                                                acf_time_picker(
                                                    [
                                                        'name' => 'show_start',
                                                        'label' => __('Show Starts'),
                                                        'required' => false,
                                                        'display_format' => 'H:i',
                                                        'return_format' => 'H:i',
                                                        'conditional_logic' => [
                                                            [
                                                                acf_conditional('inherit_show_start', false),
                                                            ],
                                                        ],
                                                    ]
                                                ),
                                            ],
                                        ]
                                    ),
                                    acf_group(
                                        [
                                            'name' => 'show_end',
                                            'label' => __('Show Ends'),
                                            'instructions' => __('Time the event ends'),
                                            'layout' => 'table',
                                            'sub_fields' => [
                                                acf_true_false(
                                                    [
                                                        'name' => 'inherit_show_end',
                                                        'label' => __('Inherit'),
                                                        'instructions' => __('Inherit data from original date'),
                                                        'default_value' => true,
                                                        'ui' => true,
                                                    ]
                                                ),
                                                acf_time_picker(
                                                    [
                                                        'name' => 'show_end',
                                                        'label' => __('Show Ends'),
                                                        'required' => false,
                                                        'display_format' => 'H:i',
                                                        'return_format' => 'H:i',
                                                        'conditional_logic' => [
                                                            [
                                                                acf_conditional('inherit_show_end', false),
                                                            ],
                                                        ],
                                                    ]
                                                ),
                                            ],
                                        ]
                                    ),
                                    acf_group(
                                        [
                                            'name' => 'ticket_link',
                                            'label' => __('Ticket Link'),
                                            'instructions' => __('Ticket sales link'),
                                            'layout' => 'table',
                                            'sub_fields' => [
                                                acf_true_false(
                                                    [
                                                        'name' => 'inherit_ticket_link',
                                                        'label' => __('Inherit'),
                                                        'instructions' => __('Inherit data from original date'),
                                                        'default_value' => true,
                                                        'ui' => true,
                                                    ]
                                                ),
                                                acf_url(
                                                    [
                                                        'name' => 'ticket_link',
                                                        'label' => __('Ticket Link'),
                                                        'required' => false,
                                                        'conditional_logic' => [
                                                            [
                                                                acf_conditional('inherit_ticket_link', false),
                                                            ],
                                                        ],
                                                    ]
                                                ),
                                            ],
                                        ]
                                    ),
                                    acf_group(
                                        [
                                            'name' => 'ticket_price',
                                            'label' => __('Minimum ticket price'),
                                            'layout' => 'table',
                                            'sub_fields' => [
                                                acf_true_false(
                                                    [
                                                        'name' => 'inherit_ticket_price',
                                                        'label' => __('Inherit'),
                                                        'instructions' => __('Inherit data from original date'),
                                                        'default_value' => true,
                                                        'ui' => true,
                                                    ]
                                                ),
                                                acf_number(
                                                    [
                                                        'name' => 'ticket_price',
                                                        'label' => __('Minimum ticket price'),
                                                        'prepend' => '€',
                                                        'min' => 0,
                                                        'step' => '0.01',
                                                        'conditional_logic' => [
                                                            [
                                                                acf_conditional('inherit_ticket_price', false),
                                                            ],
                                                        ],
                                                    ]
                                                ),
                                            ],
                                        ]
                                    ),
                                    acf_group(
                                        [
                                            'name' => 'lineup',
                                            'label' => __('Line-Up'),
                                            'layout' => 'table',
                                            'sub_fields' => [
                                                acf_true_false(
                                                    [
                                                        'name' => 'inherit_lineup',
                                                        'label' => __('Inherit'),
                                                        'instructions' => __('Inherit data from original date'),
                                                        'default_value' => true,
                                                        'ui' => true,
                                                    ]
                                                ),
                                                acf_repeater(
                                                    [
                                                        'name' => 'lineup',
                                                        'label' => __('Line-Up'),
                                                        'button_label' => __('Add Act'),
                                                        'sub_fields' => [
                                                            acf_text(
                                                                [
                                                                    'name' => 'act-name',
                                                                    'label' => __('Act Name'),
                                                                ]
                                                            ),
                                                            acf_url(
                                                                [
                                                                    'name' => 'act-link',
                                                                    'label' => __('Link to act')
                                                                ]
                                                            )
                                                        ],
                                                        'conditional_logic' => [
                                                            [
                                                                acf_conditional('inherit_lineup', false),
                                                            ],
                                                        ],
                                                    ]
                                                ),
                                            ],
                                        ]
                                    ),
                                ],
                                'conditional_logic' => [
                                    [
                                        acf_conditional('multiday_event', true)
                                    ],
                                ],
                            ]
                        ),
                    ],
                ]
            ),
        ],
        'style' => 'seamless',
        'location' => [
            [
                acf_location('post_type', 'events')
            ]
        ]
    ]
);

$events->addFieldGroup(
    [
        'title' => __('Archived'),
        'style' => 'seamless',
        'position' => 'acf_after_title',
        'location' => [
            [
                acf_location('post_type', 'events'),
            ],
        ],
        'fields' => [
            acf_true_false(
                [
                    'name' => 'archived',
                    'label' => __('Archived'),
                    'instructions' => __('If an event has already passed, this will automatically be set to true.'),
                    'default_value' => false,
                    'ui' => true,
                ]
            ),
        ],
    ]
);

$events->localize_script('sage/archive-events.js', 'events_vars', function () {
    $eventType = is_tax('event-type') ? get_queried_object() : false;

    return [
        'i18n' => [
            'all' => __('Alle'),
            'loading' => __('Laden...'),
        ],
        'eventType' => [
            'id' => $eventType ? $eventType->term_id : 0,
            'name' => $eventType ? $eventType->name : false,
        ],
    ];
});

$events->register();
