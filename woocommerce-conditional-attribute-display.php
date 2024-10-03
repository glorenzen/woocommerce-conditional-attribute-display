<?php
/*
Plugin Name: WooCommerce Conditional Attribute Display
Description: Conditionally display Location product attribute.
Version: 1.0
Author: Greg Lorenzen
Requires Plugins: woocommerce
*/

// Add settings menu
function cad_add_settings_menu() {
    add_options_page(
        'Conditional Attribute Display Settings',
        'Conditional Attribute Display',
        'manage_options',
        'cad-settings',
        'cad_render_settings_page'
    );
}
add_action('admin_menu', 'cad_add_settings_menu');

// Render settings page
function cad_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Conditional Attribute Display Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cad_settings_group');
            do_settings_sections('cad-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function cad_register_settings() {
    register_setting('cad_settings_group', 'cad_local_logo_slug');
    register_setting('cad_settings_group', 'cad_local_logo_value');
    register_setting('cad_settings_group', 'cad_location_slug');

    add_settings_section('cad_settings_section', 'Settings', null, 'cad-settings');

    add_settings_field('cad_local_logo_slug', 'Local Logo Attribute Slug', 'cad_local_logo_slug_callback', 'cad-settings', 'cad_settings_section');
    add_settings_field('cad_local_logo_value', 'Local Logo Attribute Value', 'cad_local_logo_value_callback', 'cad-settings', 'cad_settings_section');
    add_settings_field('cad_location_slug', 'Location Attribute Slug', 'cad_location_slug_callback', 'cad-settings', 'cad_settings_section');
}
add_action('admin_init', 'cad_register_settings');

// Callbacks for settings fields
function cad_local_logo_slug_callback() {
    $value = get_option('cad_local_logo_slug', '');
    echo '<input type="text" name="cad_local_logo_slug" value="' . esc_attr($value) . '" />';
}

function cad_local_logo_value_callback() {
    $value = get_option('cad_local_logo_value', '');
    echo '<input type="text" name="cad_local_logo_value" value="' . esc_attr($value) . '" />';
}

function cad_location_slug_callback() {
    $value = get_option('cad_location_slug', '');
    echo '<input type="text" name="cad_location_slug" value="' . esc_attr($value) . '" />';
}

// Enqueue custom script
function cad_enqueue_custom_script() {
    if (is_product()) {
        wp_enqueue_script('cad-public-js', plugin_dir_url(__FILE__) . 'public/js/conditional-attribute-display-public.js', array('jquery'), date("h:i:s"), true);

        // Localize script with settings
        $local_logo_slug = get_option('cad_local_logo_slug', 'attribute_pa_local-logo');
        $local_logo_value = get_option('cad_local_logo_value', 'piledrivers');
        $location_slug = get_option('cad_location_slug', 'attribute_pa_city-name');

        wp_localize_script('cad-public-js', 'cad_settings', array(
            'local_logo_slug' => $local_logo_slug,
            'local_logo_value' => $local_logo_value,
            'location_slug' => $location_slug,
        ));
    }
}
add_action('wp_enqueue_scripts', 'cad_enqueue_custom_script');

add_filter('woocommerce_add_cart_item', 'get_custom_field_from_session', 10, 2);
function get_custom_field_from_session($cart_item, $cart_item_key) {
    $local_logo_slug = get_option('cad_local_logo_slug', 'attribute_pa_local-logo');
    $local_logo_value = get_option('cad_local_logo_value', 'piledrivers');
    $location_slug = get_option('cad_location_slug', 'attribute_pa_city-name');

    if (isset($cart_item['variation']['attribute_pa_' . $local_logo_slug]) && $cart_item['variation']['attribute_pa_' . $local_logo_slug] !== $local_logo_value) {
        unset($cart_item['variation']['attribute_pa_' . $location_slug]);
    }
    return $cart_item;
}