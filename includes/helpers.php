<?php
namespace ZiddyFunk;

if (!function_exists('acf_helper_loaded')) {
    function acf_helper_loaded()
    {
        return function_exists('acf_field_group');
    }
}

if (!function_exists('acf_transit_icons')) {
    function acf_transit_icons($icons)
    {
        $sub_fields = [];

        foreach ($icons as $icon_key => $icon_name) {
            $sub_fields[] = acf_accordion(
                [
                    'label' => sprintf(__('Icon for %s'), $icon_name),
                ]
            );

            $sub_fields[] = acf_image(
                [
                    'name' => $icon_key,
                    'label' => $icon_name,
                ]
            );
        }

        return $sub_fields;
    }
}
