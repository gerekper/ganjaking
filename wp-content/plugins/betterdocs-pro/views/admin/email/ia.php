<table width="640" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width:640px; width:100%;" bgcolor="#FFFFFF">
    <tr>
        <td align="center" valign="top" style="padding:10px;">
            <table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width:600px; width:100%;">
                <?php
                    foreach ( $all_data as $key => $data ) {
                        $output = '';
                        if ( is_array( $data ) && $key == 'files' ) {
                            $output .= '<tr bgcolor="#ddd"><td align="left" valign="top" style="padding:10px;">' . $key . '</td></tr>';
                            $output .= '<tr><td align="left" valign="top" style="padding:10px;">';
                            foreach ( $data as $file_key => $file ) {
                                $output .= '<a href="' . $file['url'] . '" style="width:300px; display: inline-block">';
                                $output .= '<img width="100%" src="' . $file['url'] . '"/>';
                                $output .= '</a>';
                            }
                            $output .= '</td></tr>';
                        } else {
                            $output .= '<tr bgcolor="#ddd"><td align="left" valign="top" style="padding:10px;">' . $key . '</td></tr>';
                            $output .= '<tr><td align="left" valign="top" style="padding:10px;">' . $data . '</td></tr>';
                        }

                        echo wp_kses_post( $output );
                    }
                ?>
            </table>
        </td>
    </tr>
</table>
