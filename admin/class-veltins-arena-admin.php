<?php
namespace ZiddyFunk;

use GuzzleHttp;
use Curl\Curl;

/**
 * Ziddy Funk admin functionality
 */
class Admin
{
    public function __construct()
    {
        $this->curl = new Curl();

        $this->loadDependencies();

        $this->addOptionsPages();

        $this->addCustomFields();

        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('in_admin_footer', [$this, 'adminFooter']);

        /**
         * Schedule Archive function for events
         *
         * @return void
         */
        add_action('wp', [$this, 'scheduleArchiveEvent']);
        add_action('acf/save_post', [$this, 'updateArchiveEvent']);
        add_action('schedule_archive_event', [__CLASS__, 'archiveEvent']);
    }

    public function loadDependencies()
    {
        require_once ZF_PLUGIN_ROOT . '/includes/class-ziddy-funk-user.php';
        require_once 'class-ziddy-funk-admin-abstract-options-page.php';
        require_once 'class-ziddy-funk-admin-options-page.php';
        require_once 'class-ziddy-funk-admin-options-sub-page.php';
        require_once ZF_PLUGIN_ROOT . '/includes/class-ziddy-funk-cpt.php';
    }

    public function addCustomFields()
    {
        $post_type_locations = [
            [
                acf_location('post_type', 'post')
            ],
            [
                acf_location('post_type', 'page')
            ],
            [
                acf_location('post_type', 'events')
            ],
        ];

        acf_field_group(
            [
                'title' => __('Gallery'),
                'style' => 'default',
                'position' => 'side',
                'location' => $post_type_locations,
                'menu_order' => 1,
                'fields' => [
                    acf_gallery(
                        [
                            'name' => 'post-gallery',
                            'label' => __('Gallery'),
                            'instructions' => __('Add the gallery images.'),
                            'min' => 0,
                            'max' => 4,
                        ]
                    ),
                ],
            ]
        );

        acf_field_group(
            [
                'title' => __('Post Type Featured Image'),
                'style' => 'default',
                'position' => 'side',
                'location' => [
                    [
                        acf_location('options_page', 'rooms-options'),
                    ],
                    [
                        acf_location('options_page', 'events-options'),
                    ],
                    [
                        acf_location('options_page', 'fairs-options'),
                    ],
                ],
                'fields' => [
                    acf_image(
                        [
                            'name' => 'post-type-featured-image',
                            'label' => __('Featured Image'),
                            'instructions' => __('The default image of the post type'),
                            'return_format' => 'id',
                        ]
                    ),
                ],
            ]
        );

        acf_field_group(
            [
                'title' => __('Subtitle'),
                'style' => 'seamless',
                'position' => 'acf_after_title',
                'location' => $post_type_locations,
                'fields' => [
                    acf_text(
                        [
                            'name' => 'subtitle',
                            'label' => __('Subtitle'),
                        ]
                    ),
                ],
            ]
        );

        acf_field_group(
            [
                'title' => __('Page Settings'),
                'style' => 'seamless',
                'position' => 'side',
                'location' => $post_type_locations,
                'layout' => 'table', // block, row or table
                'menu_order' => 0,
                'fields' => [
                    acf_true_false(
                        [
                            'name' => 'show_breadcrumbs',
                            'label' => __('Show Breadcrumbs'),
                            'default_value' => 1,
                        ]
                    ),
                    acf_true_false(
                        [
                            'name' => 'show_title',
                            'label' => __('Show Title'),
                            'default_value' => 1,
                        ]
                    ),
                ],
            ]
        );

        acf_field_group(
            [
                'title' => __('Expanding Content Grid'),
                'location' => [],
                'layout' => 'table', // block, row or table
                'menu_order' => 0,
                'location' => [
                    [
                        acf_location('page_template', 'views/expand-content-grid.blade.php'),
                    ],
                ],
                'fields' => [
                    acf_repeater(
                        [
                            'name' => 'expand_block',
                            'label' => __('Expand Block'),
                            'instructions' => __('Add expand block content'),
                            'min' => 3,
                            'layout' => 'block', // block, row or table
                            'sub_fields' => [
                                acf_text([
                                    'name' => 'title',
                                    'label' => __('Block Title'),
                                    'instructions' => __('Title to appear in the block thumbnail and above the content'),
                                ]),
                                acf_wysiwyg([
                                    'name' => 'content',
                                    'label' => __('Content'),
                                    'media_upload' => false,
                                    'tabs' => 'visual',
                                ]),
                                acf_gallery([
                                    'name' => 'carousel_images',
                                    'label' => __('Images for the carousel'),
                                    'mime_types' => 'jpeg, jpg, png',
                                    'min' => 1,
                                ]),
                            ],
                        ]
                    ),
                ],
            ]
        );

        acf_field_group(
            [
                'title' => __('Term Content'),
                'location' => [
                    [
                        acf_location('taxonomy', 'event-type'),
                    ],
                ],
                'fields' => [
                    acf_wysiwyg(
                        [
                            'name' => 'term_content',
                            'label' => __('Term Content'),
                        ]
                    ),
                ],
            ]
        );
    }

    public function addOptionsPages()
    {

        /**
         * Default variables
         */
        $defaults = [
            'email' => get_field('general-settings-email', 'options'),
        ];

        /**
         * General Theme Settings
         * @var OptionsPage
         */
        $general = new OptionsPage(
            [
                'page_title'    => __('Theme General Settings'),
                'menu_title'    => __('Theme Settings'),
                'menu_slug'     => 'general_settings',
                'capability'    => 'edit_posts',
                'redirect'      => false
            ]
        );

        $general->addFieldGroup(
            __('General Theme Settings'),
            [
                acf_email(
                    [
                        'name' => 'general-settings-email',
                        'label' => __('Site Email Adress'),
                    ]
                ),
                acf_image(
                    [
                        'name' => 'placeholder-image',
                        'label' => __('Placeholder image'),
                        'instructions' => __('Fallback image to display if no image is found'),
                        'return_format' => 'id',
                    ]
                ),
                acf_group(
                    [
                        'name' => 'currency',
                        'label' => __('Currency Settings'),
                        'sub_fields' => [
                            acf_text(
                                [
                                    'name' => 'symbol',
                                    'label' => __('Currency Symbol'),
                                    'default' => '€',
                                ]
                            ),
                            acf_text(
                                [
                                    'name' => 'decimalpoint',
                                    'label' => __('Decimal Point'),
                                    'instructions' => __('The character to use for the decimal point.'),
                                    'default' => ',',
                                ]
                            ),
                            acf_text(
                                [
                                    'name' => 'separator',
                                    'label' => __('Thousands separator'),
                                    'instructions' => __('The character to use for the thousands separator.'),
                                    'default' => '.',
                                ]
                            ),
                            acf_button_group(
                                [
                                    'name' => 'position',
                                    'label' => __('Currency symbol position'),
                                    'instructions' => __('Whether to position the currency symbol before or after the price.'),
                                    'choices' => [
                                        'before' => __('Before'),
                                        'after' => __('After'),
                                    ],
                                    'default_value' => [
                                        'after',
                                    ],
                                ]
                            ),
                        ],
                    ]
                ),
            ]
        );

        $general->register();

        /**
         * Legal Settings
         */
        $legal = $general->createSubPage(
            [
                'page_title' => __('Legal'),
                'menu_title' => __('Legal Settings'),
                'menu_slug' => 'legal_settings',
            ]
        );

        $legal->addFieldGroup(
            __('Data Protection settings'),
            [
                acf_post_object(
                    [
                        'name' => 'data_protection_page',
                        'label' => __('Data protection policy page'),
                        'post_type' => ['page'],
                    ]
                ),
                acf_wysiwyg(
                    [
                        'name' => 'data_agreement_text',
                        'label' => __('Data protection agreement text'),
                        'instructions' => __('Text to appear next to data agreement checkboxes.'),
                        'media_upload' => false,
                        'toolbar' => 'simple',
                    ]
                )
            ]
        );

        $legal->register();

        /**
         * 404 Page settings
         */
        $four_oh_four = $general->createSubPage(
            [
                'page_title'    => __('404 Page Settings'),
                'menu_title'    => __('404 Page Settings'),
                'menu_slug' => 'settings-404',
            ]
        );

        $four_oh_four->addFieldGroup(
            __('404 Page Content'),
            [
                acf_wysiwyg(
                    [
                        'name' => 'content_404',
                        'label' => _x('404 Page Content', '404-page-settings'),
                    ]
                ),
                acf_text(
                    [
                        'name' => 'video_id',
                        'label' => _x('Background video ID', '404-page-settings'),
                        'instructions' => _x('3Q video ID', '404-page-settings'),
                    ]
                ),
            ]
        );

        $four_oh_four->register();

        /**
         * Fairs Options Page
         * @var OptionsPage
         */
        $events_options = new OptionsPage(
            [
                'page_title'  => __('Events Options Page'),
                'menu_title'  => __('Events Options'),
                'menu_slug'   => 'events-options',
                'post_id'     => 'events-options',
                'parent_slug' => 'edit.php?post_type=events',
            ],
            true
        );

        $events_options->addFieldGroup(
            __('Events Page Options'),
            [
                acf_select(
                    [
                        'name' => 'orderby',
                        'label' => __('Order By'),
                        'instructions' => __('Choose what to order the events by on the front end.'),
                        'choices' => [
                            'event_date' => __('Event date'),
                            'date' => __('Date created'),
                            'id' => __('Post ID'),
                            'title' => __('Alphabetically'),
                        ],
                        'default_value' => [
                            'event_date',
                        ],
                    ]
                ),
                acf_select(
                    [
                        'name' => 'order',
                        'label' => __('Order'),
                        'instructions' => __('Ascending or descending'),
                        'choices' => [
                            'desc' => __('Descending'),
                            'asc' => __('Ascending'),
                        ],
                        'default_value' => [
                            'desc',
                        ],
                    ]
                ),
                acf_wysiwyg(
                    [
                        'name' => 'events_description',
                        'label' => __('Events Description'),
                    ]
                ),
            ]
        );

        $events_options->register();
    }

    // public function add_custom_fields()
    // {
    //  $this->general->settings_fields->apply('General Theme Settings');
    //  $this->quicklinks->settings_fields->apply('Quick Link Fields');
    // }

    public function enqueueScripts()
    {
        wp_enqueue_style('custom-dashicons', plugin_dir_url(__FILE__) . '/assets/dashicons/font/flaticon.css');
    }

    public function adminFooter()
    {
        echo '<p>Events, Fairs and Rooms dashicons made by <a href="https://www.flaticon.com/authors/freepik" target="_blank">Freepik</a> from <a href="https://www.flaticon.com" target="_blank">www.flaticon.com</a></p>';
    }

    public static function archiveEvent($post_id)
    {
        if (get_field('archived', $post_id) === false) {
            update_field('archived', true, $post_id);
        }
    }

    public function updateArchiveEvent()
    {
        $post = $_POST;
        if (get_post_type() === 'events' && boolval($post['_acf_changed'])) {
            $post_id = get_the_ID();
            $hook = 'schedule_archive_event';

            if (wp_next_scheduled($hook, [$post_id])) {
                wp_clear_scheduled_hook($hook, [$post_id]);
            }

            $this->scheduleArchiveEvent();
        }
    }

    public function scheduleArchiveEvent()
    {
        if (get_post_type() === 'events') {
            $post_id = get_the_ID();
            $hook = 'schedule_archive_event';
            if (!wp_next_scheduled($hook, [$post_id])) {
                date_default_timezone_set('Europe/Berlin');

                if (get_field('event-multi-day_multiday_event')) {
                    $additionalDates = get_field('event-multi-day_additional_dates');
                    usort($additionalDates, function ($a, $b) {
                        $atime = strtotime($a['date']);
                        $btime = strtotime($b['date']);
                        if ($atime < $btime) {
                            return 1;
                        }

                        if ($atime > $btime) {
                            return -1;
                        }

                        return 0;
                    });

                    $latestDate = $additionalDates[0];
                    $showStart = $latestDate['show_start']['inherit_show_start'] ?
                        get_field('event-meta_show_start') :
                        $latestDate['show_start']['show_start'];
                    $showEnd = $latestDate['show_end']['inherit_show_end'] ?
                        get_field('event-meta_show_end') :
                        $latestDate['show_end']['show_end'];

                    if (empty($showEnd)) {
                        $showEnd = date('H:i', strtotime('+3 Hours', strtotime($showStart)));
                    }

                    $scheduleTime = strtotime("{$latestDate['date']} $showEnd");

                    wp_schedule_single_event($scheduleTime, $hook, [get_the_ID()]);
                } else {
                    $showDate = get_field('event-meta_date');
                    $showStart = get_field('event-meta_show_start');
                    $showEnd = get_field('event-meta_show_end');


                    if (empty($showEnd)) {
                        $showEnd = date('H:i', strtotime('+3 Hours', strtotime($showStart)));
                    }

                    $scheduleTime = strtotime("$showDate $showEnd");

                    wp_schedule_single_event($scheduleTime, $hook, [get_the_ID()]);
                }

                return true;
            }
        }

        return false;
    }
}
