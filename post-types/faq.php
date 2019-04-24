<?php

$faq = new CustomPostType(
    'questions-answers',
    [
        'singular' => __('Frage und Antwort'),
        'plural' => __('Fragen und Antworten'),
        'slug' => _x('questions-answers', 'slug'),
    ],
    [
        'menu_icon' => 'dashicons-flaticon-lectern',
        'has_archive' => true,
        'show_in_rest' => true,
        'rewrite' => [
            'slug' => _x('service/fragen-und-antworten', 'slug'),
            'with_front' => false,
        ],
    ]
);

$faq->addTaxonomy(
    'faq-category',
    [
        'singular' => __('Q&A Category'),
        'plural' => __('Q&A Categories'),
        'slug' => 'faq-category',
    ],
    [
        'show_in_rest' => true,
    ]
);

$faq->localize_script('sage/archive-questions-answers.js', 'faq_vars', function () {
    $faq_categories = get_terms('faq-category', ['hide_empty' => false]);

    return [
        'i18n' => [
            'all' => __('Alle'),
            'answer' => __('Antwort'),
        ],
        'categories' => $faq_categories,
        'categories_count' => count($faq_categories),
    ];
});

$faq->register();
