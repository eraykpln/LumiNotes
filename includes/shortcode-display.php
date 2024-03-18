<?php
function luminotes_shortcode_display() {
    if (!is_user_logged_in()) {
        return 'Please log in to view your notes.';
    }

    $user_id = get_current_user_id();
    $notes = get_user_meta($user_id, 'luminotes_user_notes', true);

    return !empty($notes) ? sprintf('<div class="luminotes-notes">%s</div>', esc_html($notes)) : 'You have no notes.';
}

add_shortcode('luminotes', 'luminotes_shortcode_display');
