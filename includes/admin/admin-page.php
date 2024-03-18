<?php
function luminotes_add_admin_menu() {
    add_menu_page('LumiNotes', 'LumiNotes', 'manage_options', 'luminotes', 'luminotes_admin_page', 'dashicons-welcome-write-blog', 6);
}

add_action('admin_menu', 'luminotes_add_admin_menu');

function luminotes_admin_page() {
    ?>
    <div class="wrap">
        <h2>LumiNotes</h2>
        <select id="luminotes_user_select">
            <option value="">Select a User</option>
            <?php
            $users = get_users(['role' => 'subscriber']); // Adjust as necessary
            foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name) . '</option>';
            }
            ?>
        </select>
        <div id="luminotes_notes_container"></div>
        <script>
            jQuery('#luminotes_user_select').change(function() {
                var userId = jQuery(this).val();
                if (userId) {
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_user_notes',
                            user_id: userId,
                        },
                        success: function(response) {
                            jQuery('#luminotes_notes_container').html(response);
                        }
                    });
                } else {
                    jQuery('#luminotes_notes_container').html('');
                }
            });
        </script>
        <form id="luminotes_add_note_form">
    <textarea id="luminotes_note_content" placeholder="Write a new note..."></textarea>
    <input id="luminotes_note_title" type="text" placeholder="Title" required>
<input id="luminotes_note_status" type="text" placeholder="Status" required>
<input id="luminotes_note_amount" type="number" placeholder="Amount" step="0.01" required>
<input id="luminotes_editable_by_user" type="checkbox"> Editable by User

    <button type="submit">Add Note</button>
</form>
<script>
    jQuery(document).ready(function($) {
        $('#luminotes_add_note_form').submit(function(e) {
            e.preventDefault();
            var userId = $('#luminotes_user_select').val();
            var noteTitle = $('#luminotes_note_title').val(); // Title input field
            var noteContent = $('#luminotes_note_content').val();
            var noteStatus = $('#luminotes_note_status').val(); // Status input field
            var noteAmount = $('#luminotes_note_amount').val(); // Amount input field

            if (userId && noteContent && noteTitle && noteStatus && noteAmount) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'add_user_note',
                        user_id: userId,
                        note_title: noteTitle,
                        note_content: noteContent,
                        note_status: noteStatus,
                        note_amount: noteAmount,
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#luminotes_notes_container').prepend('<p>Note added successfully.</p>');
                            // Optionally, refresh the notes list here
                            $('#luminotes_note_title').val(''); // Clear the title input
                            $('#luminotes_note_content').val(''); // Clear the content textarea
                            $('#luminotes_note_status').val(''); // Clear the status input
                            $('#luminotes_note_amount').val(''); // Clear the amount input
                        } else {
                            alert('Error adding note.');
                        }
                    }
                });
            } else {
                alert('All fields are required.');
            }
        });
    });
</script>


    </div>
    <?php
}
