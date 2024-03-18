<?php
// helper-functions.php

/**
 * Generates a dropdown of users.
 *
 * @param string $name Name attribute of the select element.
 * @param string $id ID attribute of the select element.
 * @param mixed $selected The currently selected user ID.
 */
function luminotes_dropdown_users($name = 'user_id', $id = 'luminotes_user_id', $selected = null) {
    $users = get_users();
    echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($id) . '">';
    echo '<option value="">Select a User</option>';
    foreach ($users as $user) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($user->ID),
            selected($selected, $user->ID, false),
            esc_html($user->display_name)
        );
    }
    echo '</select>';
}
