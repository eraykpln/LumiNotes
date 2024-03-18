<?php 
function luminotes_handle_form_submission() {
 

    // Check if the required fields are set and verify the nonce
    if (isset($_POST['user_id'], $_POST['note_title'], $_POST['note_content'], $_POST['note_status'], $_POST['note_amount'], $_POST['luminotes_nonce']) && wp_verify_nonce($_POST['luminotes_nonce'], 'luminotes_add_note_action')) {
        $user_id = intval($_POST['user_id']);
        $note_title = sanitize_text_field($_POST['note_title']);
        $note_content = sanitize_textarea_field($_POST['note_content']);
        $note_status = sanitize_text_field($_POST['note_status']);
        $note_amount = floatval($_POST['note_amount']);
        $note_date = current_time('mysql'); // Automatically set the current time for the note date
        $editable_by_user = isset($_POST['editable_by_user']) ? (bool)$_POST['editable_by_user'] : false; // Default to false if not set

        // Retrieve existing notes or initialize an empty array if none exist
        $existing_notes = get_user_meta($user_id, 'luminotes_user_notes', true);
        if (!$existing_notes) {
            $existing_notes = [];
        }

        // Append the new note
        $existing_notes[] = [
            'title' => $note_title,
            'content' => $note_content,
            'status' => $note_status,
            'amount' => $note_amount,
            'date' => $note_date,
            'editable_by_user' => $editable_by_user
        ];

        // Update the user meta with the new array of notes
        update_user_meta($user_id, 'luminotes_user_notes', $existing_notes);

        // Display success notice
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Note saved successfully.</p></div>';
        });
    }
}

add_action('admin_init', 'luminotes_handle_form_submission');
