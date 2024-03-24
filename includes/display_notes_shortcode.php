<?php
// Kullanıcı notlarını görüntüleyen kısa kod fonksiyonu
function luminotes_display_user_notes() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $notes = get_user_meta($user_id, 'luminotes_notes', true);

        if (!empty($notes)) {
            
           $output = '<table>';
$output .= '<tr><th style="text-align: start;">Title</th><th style="text-align: start;">Note</th><th style="text-align: start;">Status</th><th style="text-align: start;">Amount</th><th style="text-align: start;">Date</th></tr>'; // Table headers for 'Note' and 'Date'
foreach ($notes as $note) {
    $output .= '<style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
                tr:nth-child(even) {background-color: #f9f9f9;}
            </style>';
'<tr>';
      $output .= '<td>' . esc_html($note['title']) . '</td>'; // Not başlığı
                $output .= '<td>' . $note['content'] . '</td>'; // Not içeriği
                $output .= '<td>' . esc_html($note['status']) . '</td>'; // Not durumu
                $output .= '<td>' . esc_html(number_format($note['amount'], 2)) . '</td>'; // Not miktarı, 2 ondalık basamakla formatlanmış
                $output .= '<td><em>' . esc_html($note['date']) . '</em></td>'; // Not tarihi
                $output .= '</tr>';
    $output .= '</tr>';
}
$output .= '</table>';

        } else {
            $output = '<p>No notes found.</p>';
        }
    } else {
        $output = '<p>Please <a href="https://lumiasoft.com/login/">login </a> to see your notes.</p>';
    }

    return $output;
}

// Kısa kodu ekleyen fonksiyon
add_shortcode('display_user_notes', 'luminotes_display_user_notes');
function luminotes_add_note_form_for_user() {
    // Sadece giriş yapmış kullanıcılar için formu göster
    if (!is_user_logged_in()) {
        return '<p>Please login to add notes.</p>';
    }
/*
    // Form HTML'i
    $form = '<form id="luminotes-add-note-form" method="post">';
    $form .= wp_nonce_field('luminotes_add_note_action', 'luminotes_nonce', true, false); // Güvenlik için nonce ekleyin
    $form .= '<p><label for="note_title">Title:</label><br />';
    $form .= '<input type="text" id="note_title" name="note_title" required></p>';
    $form .= '<p><label for="note_content">Note:</label><br />';
    $form .= '<textarea id="note_content" name="note_content" required></textarea></p>';
    $form .= '<input type="hidden" name="note_status" value="new">'; // Status için varsayılan değer
    $form .= '<input type="hidden" name="editable_by_user" value="no">'; // Editable by user için varsayılan değer
    $form .= '<input type="hidden" name="note_amount" value="0">'; // Amount için varsayılan değer
    $form .= '<input type="submit" value="Add Note">';
    $form .= '</form>';

    // Form gönderimi için JavaScript (AJAX kullanılmıyorsa bu kısmı uyarlayabilirsiniz)
    $form .= '<script>
        jQuery(document).ready(function($) {
            $("#luminotes-add-note-form").submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                formData += "&action=add_user_note"; // WordPress AJAX eylemi
                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert("Note added successfully.");
                            $("#luminotes-add-note-form").find("input[type=text], textarea").val(""); // Form alanlarını temizle
                        } else {
                            alert("Error adding note.");
                        }
                    }
                });
            });
        });
    </script>';

    return $form;*/
    
} 

add_shortcode('luminotes_add_note_user', 'luminotes_add_note_form_for_user');
