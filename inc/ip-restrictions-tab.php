<?php

add_filter('gform_addon_navigation', 'add_ip_restrictions_tab');
function add_ip_restrictions_tab($menus)
{
    $menus[] = array(
        'name'       => 'ip_restrictions',
        'label'      => __('IP Restrictions', 'textdomain'),
        'callback'   => 'display_ip_restrictions_settings',
        'permission' => 'manage_options'
    );
    return $menus;
}

function display_ip_restrictions_settings()
{
    // Fetch the list of blocked IPs within the last 24 hours
    $blocked_ips = get_blocked_ips_last_24_hours();
?>
    <div class="wrap">
        <h2><?php _e('IP Restrictions Settings', 'textdomain'); ?></h2>
        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting "ip_restrictions_options"
            settings_fields('ip_restrictions_options');

            // Output setting sections and their fields
            do_settings_sections('ip_restrictions');

            // Output save settings button
            submit_button(__('Save Settings', 'textdomain'));
            ?>
        </form>

        <h3><?php _e('Blocked IPs (Last 24 Hours)', 'textdomain'); ?></h3>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('IP Address', 'textdomain'); ?></th>
                    <th><?php _e('Submission Count', 'textdomain'); ?></th>
                    <th><?php _e('Last Submission Time', 'textdomain'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($blocked_ips)) : ?>
                    <?php foreach ($blocked_ips as $ip => $timestamps) : ?>
                        <tr>
                            <td><?php echo esc_html($ip); ?></td>
                            <td><?php echo count($timestamps); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', max($timestamps)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3"><?php _e('No IP addresses are blocked within the last 24 hours.', 'textdomain'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}

add_action('admin_init', 'register_ip_restrictions_settings');
function register_ip_restrictions_settings()
{
    // Register a new setting for "ip_restrictions" page
    register_setting('ip_restrictions_options', 'ip_restrictions_options');

    // Register a new section in the "ip_restrictions" page
    add_settings_section(
        'ip_restrictions_section',
        __('IP Restriction Options', 'textdomain'),
        'ip_restrictions_section_callback',
        'ip_restrictions'
    );

    // Register a new field in the "ip_restrictions_section"
    add_settings_field(
        'ip_restrictions_field', // As of WP 4.6 this value is used only internally
        __('Submission Limit', 'textdomain'),
        'ip_restrictions_field_callback',
        'ip_restrictions',
        'ip_restrictions_section',
        [
            'label_for'   => 'submission_limit',
            'class'       => 'ip_restrictions_row',
        ]
    );
}

function ip_restrictions_section_callback()
{
    echo '<p>' . __('Set the maximum number of submissions allowed per IP address within a 24-hour period.', 'textdomain') . '</p>';
}

function ip_restrictions_field_callback($args)
{
    // Get the value of the setting we've registered with register_setting()
    $options = get_option('ip_restrictions_options');
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" name="ip_restrictions_options[submission_limit]" type="number" value="<?php echo esc_attr($options['submission_limit'] ?? 3); ?>" class="small-text">
    <p class="description"><?php _e('This number defines how many times a single IP address can submit a form within 24 hours.', 'textdomain'); ?></p>
<?php
}

function get_blocked_ips_last_24_hours()
{
    $all_options = wp_load_alloptions();
    $current_time = time();
    $blocked_ips = array();

    // Get the submission limit from the settings
    $ip_restrictions_options = get_option('ip_restrictions_options');
    $submission_limit = $ip_restrictions_options['submission_limit'] ?? 3;

    foreach ($all_options as $key => $value) {
        if (strpos($key, 'gf_ip_submissions_') === 0) {
            $ip_address = str_replace('gf_ip_submissions_', '', $key);
            $timestamps = maybe_unserialize($value);

            // Filter out timestamps older than 24 hours
            $timestamps = array_filter($timestamps, function ($timestamp) use ($current_time) {
                return ($current_time - $timestamp) < 86400;
            });

            // Only include IPs that have met or exceeded the submission limit
            if (count($timestamps) >= $submission_limit) {
                $blocked_ips[$ip_address] = $timestamps;
            }
        }
    }

    return $blocked_ips;
}
