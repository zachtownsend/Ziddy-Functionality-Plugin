<?php
namespace ZiddyFunk;

/**
 * Class for helping creating options pages
 */
class OptionsSubPage extends AbstractOptionsPage
{
    public function __construct($args, $parent_slug)
    {
        parent::__construct($args);
        $this->parent_slug = $parent_slug;
        $this->addPage();
    }

    private function addPage()
    {
        if (function_exists('acf_add_options_page')) {
            $args = array_merge($this->args, ['parent_slug' => $this->parent_slug]);
            acf_add_options_sub_page($args);
        }
    }
}
