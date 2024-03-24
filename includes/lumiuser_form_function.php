function lumiuser_form_sub() {
    if (!is_user_logged_in()) {   
        echo '<div class="wrap">';
        echo '<p>Please <a href="' . wp_login_url( get_permalink() ) . '">login</a> to add tickets.</p>';
        echo '</div>';
    } else {
        echo '<div class="wrap">';
        echo '<h2>LumiNotes</h2>';
        echo '<select id="luminotes_user_select">';
        echo '<option value="">Select a User</option>';
        $users = get_users(['role' => 'subscriber']); // Adjust as necessary
        foreach ($users as $user) {
            echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name) . '</option>';
        }
        echo '</select>';
        echo '<div id="luminotes_notes_container"></div>';
        echo '<script>';
        echo 'jQuery("#luminotes_user_select").change(function() {';
        echo 'var userId = jQuery(this).val();';
        echo 'if (userId) {';
        echo 'jQuery.ajax({';
        echo 'url: ajaxurl,';
        echo 'type: "POST",';
        echo 'data: {';
        echo 'action: "get_user_notes",';
        echo 'user_id: userId,';
        echo '},';
        echo 'success: function(response) {';
        echo 'jQuery("#luminotes_notes_container").html(response);';
        echo '}';
        echo '});';
        echo '} else {';
        echo 'jQuery("#luminotes_notes_container").html("");';
        echo '}';
        echo '});';
        echo '</script>';
        echo '<form id="luminotes_add_note_form" enctype="multipart/form-data">';
        echo '<textarea id="luminotes_note_content" placeholder="Write a new note...."></textarea>';
        echo '<input id="luminotes_note_title" type="text" placeholder="Title" required>';
        echo '<input id="luminotes_note_status" type="hidden" value="new">';
        echo '<input id="luminotes_note_amount" type="hidden" value="0">';
        $isValidFileLumi = get_user_meta(get_current_user_id(), 'IsValidFileLumi', true);
        if ($isValidFileLumi) {
            echo '<input id="luminotes_note_file" type="file" accept=".jpeg, .jpg, .png, .csv, .pdf" max-size="4194304">';
        }
        echo '<input id="luminotes_editable_by_user" type="checkbox"> Editable by User';
        echo '<button type="submit">Add Note</button>';
        echo '</form>';
        echo '<script>';
        echo 'jQuery(document).ready(function($) {';
        echo '$("#luminotes_add_note_form").submit(function(e) {';
        echo 'e.preventDefault();';
        echo 'var userId = $("#luminotes_user_select").val();';
        echo 'var noteTitle = $("#luminotes_note_title").val();';
        echo 'var noteContent = $("#luminotes_note_content").val();';
        echo 'var noteStatus = $("#luminotes_note_status").val();';
        echo 'var noteAmount = $("#luminotes_note_amount").val();';
        echo 'var noteFile = $("#luminotes_note_file").prop("files")[0];';
        echo 'var formData = new FormData();';
        echo 'formData.append("action", "add_user_note");';
        echo 'formData.append("user_id", userId);';
        echo 'formData.append("note_title", noteTitle);';
        echo 'formData.append("note_content", noteContent);';
        echo 'formData.append("note_status", noteStatus);';
        echo 'formData.append("note_amount", noteAmount);';
        echo 'formData.append("note_file", noteFile);';
        echo 'if (userId && noteContent && noteTitle) {';
        echo '$.ajax({';
        echo 'url: ajaxurl,';
        echo 'type: "POST",';
        echo 'data: formData,';
        echo 'contentType: false,';
        echo 'processData: false,';
        echo 'success: function(response) {';
        echo 'if(response.success) {';
        echo '$("#luminotes_notes_container").prepend("<p>Note added successfully.</p>");';
        echo '$("#luminotes_note_title").val("");';
        echo '$("#luminotes_note_content").val("");';
        echo '$("#luminotes_note_status").val("new");';
        echo '$("#luminotes_note_amount").val("0");';
        echo '} else {';
        echo 'alert("Error adding note.");';
        echo '}';
        echo '}';
        echo '});';
        echo '} else {';
        echo 'alert("All fields are required.");';
        echo '}';
        echo '});';
        echo '});';
        echo '</script>';
        echo '</div>';
    }
}
