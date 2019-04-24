<?php
namespace ZiddyFunk;

/**
 * Ziddy Funk Functionality plugin controller
 */
class ZiddyFunk
{
    public function __construct()
    {
        $this->post_types = [
            'events',
            'rooms',
            'fairs',
            'eventmodules',
            'catering',
            'faq',
            'kiosks',
        ];

        $this->loadDependencies();

        $this->admin = new Admin();

        $this->registerPostTypes();
    }

    private function require($path)
    {
        require_once ZF_PLUGIN_ROOT . "$path";
    }

    private function loadDependencies()
    {
        $this->require('/admin/class-ziddy-funk-admin.php');
        $this->require('/includes/class-ziddy-funk-cpt.php');
    }

    public function registerPostTypes()
    {
        foreach ($this->post_types as $post_type) {
            $this->require("/post-types/$post_type.php");
        }
    }
}
