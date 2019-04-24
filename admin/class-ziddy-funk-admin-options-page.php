<?php
namespace ZiddyFunk;

/**
 * Class for helping creating options pages
 */
class OptionsPage extends AbstractOptionsPage
{
    public function __construct($args, $translatable = false)
    {
        parent::__construct($args, $translatable);
        $this->addPage();
    }

    private function addPage()
    {
        if (function_exists('acf_add_options_page')) {
            $args = $this->args;
            $this->prefixOption($args, 'menu_slug', $this->lang);
            $this->prefixOption($args, 'post_id', $this->lang);
            acf_add_options_page($args);
        }
    }

    public function createSubPage($args)
    {
        $this->prefixOption($args, 'menu_slug', $this->lang);

        return new OptionsSubPage($args, $this->slug);
    }
}
