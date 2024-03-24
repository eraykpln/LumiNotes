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
//
//
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
    $output .= '<button>Add New Not3</button>'; // Placeholder for adding new note functionality

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
    //
// Not düzenleme modunu kontrol et
 if (isset($_GET['edit']) && $_GET['edit'] == 'true' && isset($_GET['note_index']) && isset($_GET['user_id'])) {
        $note_index = intval($_GET['note_index']);
        $user_id = intval($_GET['user_id']);
        $notes = get_user_meta($user_id, 'luminotes_notes', true);
        $note_to_edit = $notes[$note_index];

        echo '<h2>Notu Düzenle</h2>';
        echo '<form action="" method="post">';
        wp_nonce_field('luminotes_edit_note_action', 'luminotes_nonce');
        echo '<input type="hidden" name="action" value="edit_note">';
        echo '<input type="hidden" name="note_index" value="' . esc_attr($note_index) . '">';
        echo '<input type="hidden" name="user_id" value="' . esc_attr($user_id) . '">';
        echo '<p><label>Not Başlığı:</label><br /><input type="text" name="note_title" value="' . esc_attr($note_to_edit['title']) . '"></p>';
        echo '<p><label>Not İçeriği:</label><br /><textarea name="note_content">' . $note_to_edit['content'] . '</textarea></p>';
        // Status alanı eklendi
        echo '<p><label>Durum:</label><br /><input type="text" name="note_status" value="' . esc_attr($note_to_edit['status'] ?? 'new') . '"></p>';
        echo '<p><input type="submit" value="Notu Güncelle"></p>';
        echo '</form>';
        return;
}
    
    //
    ?>
    <div class="wrap">
        <h2>LumiRecords</h2>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Notes</th>
                    <th>Actions</th> <!-- Eylemler için yeni sütun -->
                </tr>
            </thead>
            <tbody>
                <?php
                $users = get_users();
                foreach ($users as $user) {
                    $notes = get_user_meta($user->ID, 'luminotes_notes', true);
                    if(is_array($notes)){
                        foreach($notes as $index => $note){
                            echo '<tr>';
                            echo '<td>' . esc_html($user->ID) . '</td>';
                            echo '<td>' . esc_html($user->display_name) . '</td>';
                            echo '<td>' . esc_html($note['content']) . '</td>';
                            // Düzenleme bağlantısını ekleyin
                            echo '<td><a href="'.admin_url('admin.php?page=lumirecords&edit=true&note_index='.$index.'&user_id='.$user->ID).'">Düzenle</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr>';
                        echo '<td>' . esc_html($user->ID) . '</td>';
                        echo '<td>' . esc_html($user->display_name) . '</td>';
                        echo '<td>No notes</td>';
                        echo '<td></td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
function luminotes_handle_note_editing() {
    if (isset($_POST['action'], $_POST['note_index'], $_POST['user_id'], $_POST['note_title'], $_POST['note_content'], $_POST['note_status'], $_POST['luminotes_nonce']) && $_POST['action'] == 'edit_note' && wp_verify_nonce($_POST['luminotes_nonce'], 'luminotes_edit_note_action')) {
        $note_index = intval($_POST['note_index']);
        $user_id = intval($_POST['user_id']);
        $note_title = sanitize_text_field($_POST['note_title']);
        // Not içeriği ve status, kullanıcı girdisi olarak doğrudan kabul ediliyor
        $note_content = $_POST['note_content']; // HTML içeriği kabul ediliyor
        $note_status = $_POST['note_status']; // Status, doğrudan kabul ediliyor

        $notes = get_user_meta($user_id, 'luminotes_notes', true);
        if (isset($notes[$note_index])) {
            $notes[$note_index]['title'] = $note_title;
            $notes[$note_index]['content'] = $note_content;
            $notes[$note_index]['status'] = $note_status;

            update_user_meta($user_id, 'luminotes_notes', $notes);

            // Başarı mesajı
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Not başarıyla güncellendi.</p></div>';
            });
        }
    }
}
add_action('admin_init', 'luminotes_handle_note_editing');

