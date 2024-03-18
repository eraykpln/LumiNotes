<?php
/**
 * Plugin Name: LumiNotes
 * Description: Allows admins to add private notes for users, which users can view.
 * Version: 1.1
 * Author: Lumiasoft 
 */

// Admin sayfası ve form işleme
include_once plugin_dir_path(__FILE__) . 'includes/admin/admin-page.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin/handle-form-submission.php';

// Kullanıcıların notlarını görüntülemek için shortcode
include_once plugin_dir_path(__FILE__) . 'includes/shortcode-display.php';
include_once plugin_dir_path(__FILE__) . 'includes/display_notes_shortcode.php';

// Stil dosyasını yükleme
function luminotes_enqueue_styles() {
    wp_enqueue_style('luminotes-admin-style', plugins_url('assets/css/admin-style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'luminotes_enqueue_styles');
add_action('wp_ajax_get_user_notes', 'luminotes_get_user_notes');

function luminotes_get_user_notes() {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    if (!$user_id) {
        wp_send_json_error('User ID is required.');
    }

    // Assume notes are stored as an array of arrays in user meta
    $notes = get_user_meta($user_id, 'luminotes_notes', true);
    if (!$notes) {
        $notes = [];
    }

    // Generate HTML for displaying notes
    $output = '<ul>';
    foreach ($notes as $note) {
        $output .= '<li>' . esc_html($note['content']) . ' - <em>' . esc_html($note['date']) . '</em></li>';
    }
    $output .= '</ul>';
    $output .= '<button>Add New Note</button>'; // Placeholder for adding new note functionality

    echo $output;
    wp_die(); // This is required to terminate immediately and return a proper response
}

add_action('wp_ajax_add_user_note', 'luminotes_add_user_note');

function luminotes_add_user_note() {
    $allowed_html = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array(),
        ),
        // Gerekirse diğer etiketleri de ekleyin.
    );

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $note_title = isset($_POST['note_title']) ? sanitize_text_field($_POST['note_title']) : '';
    // Not içeriğini wp_kses ile filtreleyin
    $note_content = isset($_POST['note_content']) ? wp_kses($_POST['note_content'], $allowed_html) : '';
    $note_status = isset($_POST['note_status']) ? sanitize_text_field($_POST['note_status']) : '';
    $note_amount = isset($_POST['note_amount']) ? floatval($_POST['note_amount']) : 0;
    $editable_by_user = isset($_POST['editable_by_user']) ? filter_var($_POST['editable_by_user'], FILTER_VALIDATE_BOOLEAN) : false;
    $note_date = current_time('mysql');

    if (!$user_id || empty($note_content) || empty($note_title) || empty($note_status)) {
        wp_send_json_error('Required fields are missing.');
    }

    $notes = get_user_meta($user_id, 'luminotes_notes', true) ?: [];
    $notes[] = [
        'title' => $note_title,
        'content' => $note_content,
        'status' => $note_status,
        'amount' => $note_amount,
        'date' => $note_date,
        'editable_by_user' => $editable_by_user
    ];

    update_user_meta($user_id, 'luminotes_notes', $notes);
    wp_send_json_success('Note added successfully.');
}


function luminotes_add_admin_menus() {
    add_menu_page('LumiRecords', 'LumiRecords', 'manage_options', 'lumirecords', 'lumirecords_admin_page', 'dashicons-list-view', 6);
}
add_action('admin_menu', 'luminotes_add_admin_menus');

function lumirecords_admin_page() {
    ?>
    <div class="wrap">
        <h2>LumiRecords</h2>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col">User ID</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">User Name</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = get_users(); // Tüm kullanıcıları al
                foreach ($users as $user) {
                    $notes = get_user_meta($user->ID, 'luminotes_notes', true);
                    $notes_output = is_array($notes) ? implode('<br>', array_map(function($note) { return esc_html($note['content']); }, $notes)) : 'No notes';
                    echo '<tr>';
                    echo '<td>' . esc_html($user->ID) . '</td>';
                    echo '<td>' . esc_html($user->display_name) . '</td>';
                    echo '<td>' . $notes_output . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
