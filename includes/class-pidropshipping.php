<?php

class PIDropshipping {

    public function __construct() {
        // Hook into WooCommerce to add custom fields
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_dropshipping_fields']);
        add_action('woocommerce_process_product_meta', [$this, 'save_dropshipping_fields']);
        add_action('woocommerce_checkout_order_processed', [$this, 'handle_order']);

        // Add hooks for the settings page
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('woocommerce_product_import_inserted_product_object', [$this, 'set_dropshipping_meta'], 10, 2);

        // Set custom email sender
        add_filter('wp_mail_from', [$this, 'custom_mail_from']);
        add_filter('wp_mail_from_name', [$this, 'custom_mail_from_name']);
    }

    public function set_dropshipping_meta($product, $data) {
        // Automatically set _is_dropshipping to 'yes' during import
        $product->update_meta_data('_is_dropshipping', 'yes');
        $product->save();
    }

    // Add custom fields to product settings
    public function add_dropshipping_fields() {
        echo '<div class="options_group">';

        // Checkbox for dropshipping product
        woocommerce_wp_checkbox([
            'id' => '_is_dropshipping',
            'label' => __('Dropshipping Product', 'woocommerce'),
            'description' => __('Check this if the product is a dropshipping product from Prestige Import.', 'woocommerce')
        ]);

        echo '</div>';
    }

    // Save the custom field
    public function save_dropshipping_fields($post_id) {
        $is_dropshipping = isset($_POST['_is_dropshipping']) ? 'yes' : 'no';
        update_post_meta($post_id, '_is_dropshipping', $is_dropshipping);
    }

    // Handle orders for dropshipping products
    public function handle_order($order_id) {
        $order = wc_get_order($order_id);
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $is_dropshipping = get_post_meta($product->get_id(), '_is_dropshipping', true);

            if ($is_dropshipping === 'yes') {
                $this->notify_prestige_import($order, $item);
            }
        }
    }

    // Notify Prestige Import
    private function notify_prestige_import($order, $item) {
        $prestige_email = get_option('prestige_import_email', 'default@prestigeimportgroup.com');
        $logo_url = get_option('pidropshipping_logo_url', '');
        $email_subject = get_option('pidropshipping_email_subject', 'New Dropshipping Order');
        $email_template = get_option('pidropshipping_email_template', '');

        // Get product SKU
        $sku = $item->get_product()->get_sku();

        // Prepare dynamic content
        $message = $email_template;
        $message = str_replace('{order_id}', $order->get_id(), $message);
        $message = str_replace('{product_name}', $item->get_name(), $message);
        $message = str_replace('{sku}', $sku ? $sku : 'N/A', $message);
        $message = str_replace('{quantity}', $item->get_quantity(), $message);
        $message = str_replace('{shipping_address}', $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2() . ', ' . $order->get_shipping_city() . ', ' . $order->get_shipping_postcode(), $message);
        $message = str_replace('{billing_email}', $order->get_billing_email(), $message);
        $message = str_replace('{billing_phone}', $order->get_billing_phone(), $message);
        $message = str_replace('{order_total}', $order->get_formatted_order_total(), $message);
        $message = str_replace('{logo}', $logo_url ? '<img src="' . esc_url($logo_url) . '" alt="Logo" style="max-width:150px;"/>' : '', $message);

        // Set email headers for HTML content
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        wp_mail($prestige_email, $email_subject, $message, $headers);
    }

    // Set custom email sender
    public function custom_mail_from($original_email_address) {
        return 'example@example.com'; // Set the custom email address
    }

    public function custom_mail_from_name($original_email_from) {
        return 'Email From Name'; // Set the custom sender name
    }

    // Add settings page
    public function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            'Wp Dropshipping Settings',
            'Dropshipping Settings',
            'manage_options',
            'pidropshipping-settings',
            [$this, 'settings_page_html']
        );
    }

    // Settings page HTML
    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1>WP Dropshipping Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('pidropshipping_options_group');
                do_settings_sections('pidropshipping-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Register settings
    public function register_settings() {
        register_setting('pidropshipping_options_group', 'prestige_import_email');
        register_setting('pidropshipping_options_group', 'pidropshipping_logo_url');
        register_setting('pidropshipping_options_group', 'pidropshipping_email_subject');
        register_setting('pidropshipping_options_group', 'pidropshipping_email_template');

        add_settings_section('pidropshipping_main_section', 'Main Settings', null, 'pidropshipping-settings');

        add_settings_field(
            'prestige_import_email',
            'Receiver  Email',
            [$this, 'prestige_import_email_callback'],
            'pidropshipping-settings',
            'pidropshipping_main_section'
        );

        add_settings_field(
            'pidropshipping_logo_url',
            'Email Logo URL',
            [$this, 'pidropshipping_logo_url_callback'],
            'pidropshipping-settings',
            'pidropshipping_main_section'
        );

        add_settings_field(
            'pidropshipping_email_subject',
            'Email Subject',
            [$this, 'pidropshipping_email_subject_callback'],
            'pidropshipping-settings',
            'pidropshipping_main_section'
        );

        add_settings_field(
            'pidropshipping_email_template',
            'Email Template',
            [$this, 'pidropshipping_email_template_callback'],
            'pidropshipping-settings',
            'pidropshipping_main_section'
        );
    }

    // Callback for  Email field
    public function prestige_import_email_callback() {
        $email = get_option('prestige_import_email', 'default@prestigeimportgroup.com');
        echo '<input type="email" name="prestige_import_email" value="' . esc_attr($email) . '" style="width:100%;" />';
    }

    // Callback for Logo URL field
    public function pidropshipping_logo_url_callback() {
        $logo_url = get_option('pidropshipping_logo_url', '');
        echo '<input type="text" name="pidropshipping_logo_url" value="' . esc_attr($logo_url) . '" style="width:100%;" />';
        echo '<p class="description">Enter the URL of your logo image. Example: https://yourwebsite.com/path-to-logo.png</p>';
    }

    // Callback for Email Subject field
    public function pidropshipping_email_subject_callback() {
        $subject = get_option('pidropshipping_email_subject', 'New Dropshipping Order');
        echo '<input type="text" name="pidropshipping_email_subject" value="' . esc_attr($subject) . '" style="width:100%;" />';
    }

    // Callback for Email Template field
    public function pidropshipping_email_template_callback() {
        $template = get_option('pidropshipping_email_template', 'Order ID: {order_id}<br>Product: {product_name}<br>SKU: {sku}<br>Quantity: {quantity}<br>Shipping Address: {shipping_address}<br>Billing Email: {billing_email}<br>Billing Phone: {billing_phone}<br>Order Total: {order_total}<br>{logo}<br>Thank you for your order!');
        wp_editor($template, 'pidropshipping_email_template', [
            'textarea_name' => 'pidropshipping_email_template',
            'textarea_rows' => 15,
            'media_buttons' => false,
            'tinymce' => true
        ]);
        echo '<p>You can use the following placeholders in your template:</p>';
        echo '<ul>';
        echo '<li>{order_id} - Order ID</li>';
        echo '<li>{product_name} - Product Name</li>';
        echo '<li>{sku} - Product SKU</li>';
        echo '<li>{quantity} - Quantity</li>';
        echo '<li>{shipping_address} - Shipping Address</li>';
        echo '<li>{billing_email} - Billing Email</li>';
        echo '<li>{billing_phone} - Billing Phone</li>';
        echo '<li>{order_total} - Order Total</li>';
        echo '<li>{logo} - Logo Image</li>';
        echo '</ul>';
    }
}
