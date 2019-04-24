<?php
namespace ZiddyFunk;

/**
 * Class for helping creating options pages
 */
abstract class AbstractOptionsPage
{
    public function __construct($args, $translatable = false)
    {
        $this->translatable = $translatable;
        $this->args = $args;
        $this->slug = $args['menu_slug'];
        $this->field_groups = [];
        $this->lang = $this->languagePrefix();

        if ($this->translatable) {
            add_filter('wpml_admin_language_switcher_items', [$this, 'correctAdminBarLinks']);
            add_filter('acf/validate_post_id', [$this, 'getFieldInCorrectLanguage']);
        }
    }

    private function addPage()
    {
    }

    public function run()
    {
        if (count($this->field_groups)) {
            foreach ($this->field_groups as $field_group) {
                acf_field_group($field_group);
            }
        }
    }

    public function addFieldGroup($title, $fields, $style = 'seamless')
    {
        $this->field_groups[] = [
            'title' => $title,
            'fields' => $fields,
            'style' => $style,
            'location' => [
                [
                    acf_location('options_page', $this->lang . $this->slug),
                ]
            ],
        ];
    }

    public function register()
    {
        add_action('init', [$this, 'run']);
    }

    protected function languagePrefix()
    {
        if ($this->translatable && defined('ICL_LANGUAGE_CODE')) {
            global $sitepress;

            $sitepress->get_default_language();

            if ($sitepress->get_default_language() == ICL_LANGUAGE_CODE) {
                $lang = '';
            } else {
                $lang = ICL_LANGUAGE_CODE.'_';
            }
        } else {
            $lang = '';
        }

        return $lang;
    }

    public function prefixOption(&$options, $key, $prefix = '')
    {
        if (isset($options[$key])) {
            $options[$key] = $prefix . $options[$key];
        }
    }

    public function correctAdminBarLinks($items)
    {
        if (defined('ICL_LANGUAGE_CODE')) {
            global $sitepress;
            $default_language = $sitepress->get_default_language();

            $current_page = get_current_screen();

            if (strpos($current_page->id, $this->lang . $this->args['menu_slug'])) {
                if (isset($items['all'])) {
                    unset($items['all']);
                }

                foreach ($items as $lang => $item) {
                    if ($default_language === $lang || $lang === 'all') {
                        $items[$lang]['url'] = preg_replace("/(&|\?)page={$this->lang}([^&]+)/", "$1page=$2", $item['url']);
                    } else {
                        $items[$lang]['url'] = preg_replace("/(&|\?)page={$this->lang}([^&]+)/", "$1page={$lang}_$2", $item['url']);
                    }
                }
            }
        }

        return $items;
    }

    public function getFieldInCorrectLanguage($post_id)
    {
        if ($post_id === $this->slug) {
            $post_id = $this->lang . $post_id;
        }

        return $post_id;
    }
}
