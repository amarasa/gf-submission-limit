<?php
/*
Plugin Name: Gravity Forms IP Submission Limit
Description: Limits the number of form submissions from a single IP address within a 24-hour period.
Version: 1.1.1
Author: Angelo Marasa
*/


/* -------------------------------------------------------------------------------------- */
// Updater
require 'puc/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/amarasa/gf-submission-limit',
    __FILE__,
    'gf-submission-limit'
);

//Set the branch that contains the stable release.
//$myUpdateChecker->setBranch('stable-branch-name');

//Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');

/* -------------------------------------------------------------------------------------- */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the IP Restrictions settings tab
require_once plugin_dir_path(__FILE__) . 'inc/ip-restrictions-tab.php';

// Enqueue the CSS file for the plugin
function my_plugin_enqueue_styles()
{
    wp_enqueue_style('my-plugin-style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles');

// Record the submission timestamp after each form submission
function record_submission_timestamp($entry, $form)
{
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $current_time = time();

    // Fetch existing submissions for this IP address
    $ip_submissions = get_option('gf_ip_submissions_' . $ip_address, array());

    // Add the current submission time
    $ip_submissions[] = $current_time;

    // Update the option with the new submission timestamps
    update_option('gf_ip_submissions_' . $ip_address, $ip_submissions);

    // Debugging: Log the submission timestamps to ensure they are being recorded
    error_log('Recording submission for IP: ' . $ip_address . ' | Submissions: ' . print_r($ip_submissions, true));
}
add_action('gform_after_submission', 'record_submission_timestamp', 10, 2);

// Check the submission limit before rendering the form
add_filter('gform_pre_render', function ($form) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $current_time = time();

    // Fetch existing submissions for this IP address
    $ip_submissions = get_option('gf_ip_submissions_' . $ip_address, array());

    // Remove old submissions (older than 24 hours)
    $ip_submissions = array_filter($ip_submissions, function ($timestamp) use ($current_time) {
        return ($current_time - $timestamp) < 86400;
    });

    // Count the number of valid submissions in the last 24 hours
    $submission_count = count($ip_submissions);

    // Get the stored submission limit from the settings
    $ip_restrictions_options = get_option('ip_restrictions_options');
    $submission_limit = $ip_restrictions_options['submission_limit'] ?? 5;

    // Debugging: Log the submission count and limit
    error_log('Submission count: ' . $submission_count . ' | Submission limit: ' . $submission_limit);

    // If the submission count is already at the limit, replace the form with a message
    if ($submission_count >= $submission_limit) {
        // Check if we are on a form page or a confirmation page
        if (!rgpost('gform_submit')) {
            // If we're on the form page, display the limit reached message
            echo '<p>You have reached the submission limit. Please try again later.</p>';
            return array(); // Prevent the form from rendering
        }
    }

    // Return the form unchanged if the limit is not reached
    return $form;
});

// Handle what happens after form submission (confirmation page logic)
add_filter('gform_confirmation', function ($confirmation, $form, $entry) {
    // The confirmation page logic stays unchanged
    return $confirmation;
}, 10, 3);
