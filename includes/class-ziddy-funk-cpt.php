<?php

/**
 * Custom Post Type
 */
class CustomPostType
{
    public function __construct($slug, $labels, $args = [])
    {
        $this->slug = $slug;
        $this->labels = $labels;
        $this->args = $args;
        $this->field_groups = [];
        $this->taxonomies = [];
        $this->localize = [];
    }

    public function addFieldGroup($args)
    {
        $this->field_groups[] = $args;
    }

    public function addTaxonomy($slug, $labels, $args = [])
    {
        $taxonomy = new stdClass();
        $taxonomy->slug = $slug;
        $taxonomy->labels = $labels;
        $taxonomy->args = $args;

        $this->taxonomies[] = $taxonomy;
    }

    public function localize_script($handle = false, $object_name = false, $l10n = [])
    {
        if (is_string($handle) && is_string($object_name) && ((is_array($l10n) && !empty($l10n)) || is_callable($l10n))) {
            $this->localize[] = [
                'handle' => $handle,
                'object_name' => $object_name,
                'l10n' => $l10n,
            ];
        }
    }

    public function registerPostTypes()
    {
        register_extended_post_type($this->slug, $this->args, $this->labels);

        if (count($this->taxonomies)) {
            foreach ($this->taxonomies as $taxonomy) {
                register_extended_taxonomy(
                    $taxonomy->slug,
                    $this->slug,
                    $taxonomy->args,
                    $taxonomy->labels
                );
            }
        }
    }

    public function registerFieldGroups()
    {
        if (count($this->field_groups)) {
            foreach ($this->field_groups as $field_group) {
                acf_field_group($field_group);
            }
        }
    }

    public function run()
    {
        add_action('init', [$this, 'registerPostTypes'], 9);
        $this->registerFieldGroups();
    }

    public function register()
    {
        $this->run();
        add_action('wp_head', function () {
            foreach ($this->localize as $value) {
                extract($value);
                wp_localize_script($handle, $object_name, is_callable($l10n) ? $l10n() : $l10n);
            }
        });
    }
}
