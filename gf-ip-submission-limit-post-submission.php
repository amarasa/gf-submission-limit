<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('gform_after_submission', function ($entry, $form) {
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

    // Set the submission limit
    $submission_limit = 3; // Adjust this as needed

    // Debugging: Display submission count directly on the page
    echo '<p>Submission count for IP ' . $ip_address . ': ' . $submission_count . '</p>';

    if ($submission_count >= $submission_limit) {
        // Prevent form submission
        wp_die('You have reached the submission limit. Please try again later.');
    } else {
        // Add the current submission time
        $ip_submissions[] = $current_time;
        update_option('gf_ip_submissions_' . $ip_address, $ip_submissions);
    }
}, 10, 2);
