<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class HMWP_MS_Text_Diff_Renderer_Table extends WP_Text_Diff_Renderer_Table {

	/**
	 * Number of context lines before
	 *
	 * @var int
	 */
	public $_leading_context_lines  = 3;

	/**
	 * Number of context lines after
	 *
	 * @var int
	 */
	public $_trailing_context_lines = 3;

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	function addedLine( $line ) {
		return "<td class='diff-addedline first'>+</td><td class='diff-addedline'>{$line}</td>";
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	function deletedLine( $line ) {
		return "<td class='diff-deletedline first'>-</td><td class='diff-deletedline'>{$line}</td>";
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 * @return string
	 */
	function contextLine( $line ) {
		return "<td class='diff-context first'> </td><td class='diff-context'>{$line}</td>";
	}

	/**
	 * @ignore
	 *
	 * @param string $header
	 * @return string
	 */
	function _startBlock( $header ) {
		return '<tr><td colspan="2" class="start-block">&nbsp;' . $header . "</td></tr>\n";
	}

    function _blockHeader( $xbeg, $xlen, $ybeg, $ylen ) {
        if ( $xlen > 1 ) {
            $xbeg .= ',' . ($xbeg + $xlen - 1);
        }
        if ( $ylen > 1 ) {
            $ybeg .= ',' . ($ybeg + $ylen - 1);
        }

        // this matches the GNU Diff behaviour
        if ( $xlen && ! $ylen ) {
            $ybeg--;
        } elseif ( ! $xlen ) {
            $xbeg--;
        }

        return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
    }

	/**
	 * Render block
	 *
	 * @return string
	 */
    function _block( $xbeg, $xlen, $ybeg, $ylen, &$edits ) {
        $output = $this->_startBlock( $this->_blockHeader( $xbeg, $xlen, $ybeg, $ylen ) );

        foreach ( $edits as $edit ) {
            switch ( strtolower( get_class( $edit ) ) ) {
            case 'text_diff_op_copy':
                $output .= $this->_context( $edit->orig );
                break;

            case 'text_diff_op_add':
                $output .= $this->_added( $edit->final );
                break;

            case 'text_diff_op_delete':
                $output .= $this->_deleted( $edit->orig );
                break;

            case 'text_diff_op_change':
                $output .= $this->_changed( $edit->orig, $edit->final );
                break;
            }
        }

        return $output . $this->_endBlock();
    }

	/**
	 * @ignore
	 * @access private
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	function _added( $lines, $encode = true ) {
		$r = '';
		foreach ( $lines as $line ) {
			if ( $encode )
				$line = htmlspecialchars( $line );

			$r .= '<tr>' . $this->addedLine( $line ) . "</tr>\n";
		}
		return $r;
	}

	/**
	 * @ignore
	 * @access private
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	function _deleted( $lines, $encode = true ) {
		$r = '';
		foreach ( $lines as $line ) {
			if ( $encode )
				$line = htmlspecialchars( $line );

			$r .= '<tr>' . $this->deletedLine( $line ) . "</tr>\n";
		}
		return $r;
	}

	/**
	 * @ignore
	 * @access private
	 *
	 * @param array $lines
	 * @param bool $encode
	 * @return string
	 */
	function _context( $lines, $encode = true ) {
		$r = '';
		foreach ( $lines as $line ) {
			if ( $encode )
				$line = htmlspecialchars( $line );

			$r .= '<tr>' . $this->contextLine( $line ) . "</tr>\n";
		}
		return $r;
	}

	/**
	 * Process changed lines to do word-by-word diffs for extra highlighting.
	 *
	 * (TRAC style) sometimes these lines can actually be deleted or added rows.
	 * We do additional processing to figure that out
	 *
	 * @access private
	 * @since 2.6.0
	 *
	 * @param array $orig
	 * @param array $final
	 * @return string
	 */
	function _changed( $orig, $final ) {
		$r = '';

		// Does the aforementioned additional processing
		// *_matches tell what rows are "the same" in orig and final.  Those pairs will be diffed to get word changes
		//	match is numeric: an index in other column
		//	match is 'X': no match.  It is a new row
		// *_rows are column vectors for the orig column and the final column.
		//	row >= 0: an indix of the $orig or $final array
		//	row  < 0: a blank row for that column
		list($orig_matches, $final_matches, $orig_rows, $final_rows) = $this->interleave_changed_lines( $orig, $final );

		// These will hold the word changes as determined by an inline diff
		$orig_diffs  = array();
		$final_diffs = array();

		// Compute word diffs for each matched pair using the inline diff
		foreach ( $orig_matches as $o => $f ) {
			if ( is_numeric( $o ) && is_numeric( $f ) ) {
				$text_diff = new Text_Diff( 'auto', array( array( $orig[$o] ), array( $final[$f] ) ) );
				$renderer  = new $this->inline_diff_renderer;
				$diff = $renderer->render( $text_diff );

				// If they're too different, don't include any <ins> or <dels>
				if ( $diff_count = preg_match_all( '!(<ins>.*?</ins>|<del>.*?</del>)!', $diff, $diff_matches ) ) {
					// length of all text between <ins> or <del>
					$stripped_matches = strlen( strip_tags( join( ' ', $diff_matches[0] ) ) );
					// since we count lengith of text between <ins> or <del> (instead of picking just one),
					//	we double the length of chars not in those tags.
					$stripped_diff = strlen( strip_tags( $diff ) ) * 2 - $stripped_matches;
					$diff_ratio    = $stripped_matches / $stripped_diff;
					if ( $diff_ratio > $this->_diff_threshold )
						continue; // Too different.  Don't save diffs.
				}

				// Un-inline the diffs by removing del or ins
				$orig_diffs[$o]  = preg_replace( '|<ins>.*?</ins>|', '', $diff );
				$final_diffs[$f] = preg_replace( '|<del>.*?</del>|', '', $diff );
			}
		}

		foreach ( array_keys( $orig_rows ) as $row ) {
			// Both columns have blanks.  Ignore them.
			if ( $orig_rows[$row] < 0 && $final_rows[$row] < 0 )
				continue;

			// If we have a word based diff, use it.  Otherwise, use the normal line.
			if ( isset( $orig_diffs[$orig_rows[$row]] ) ) {
				$orig_line = $orig_diffs[$orig_rows[$row]];
			}
			elseif ( isset( $orig[$orig_rows[$row]] ) ) {
				$orig_line = htmlspecialchars( $orig[$orig_rows[$row]] );
			}
			else
				$orig_line = '';

			if ( isset( $final_diffs[$final_rows[$row]] ) ) {
				$final_line = $final_diffs[$final_rows[$row]];
			}
			elseif ( isset( $final[$final_rows[$row]] ) ) {
				$final_line = htmlspecialchars( $final[$final_rows[$row]] );
			}
			else
				$final_line = '';

			if ( $orig_rows[$row] < 0 ) { // Orig is blank.  This is really an added row.
				$r .= $this->_added( array( $final_line ), false );
			} elseif ( $final_rows[$row] < 0 ) { // Final is blank.  This is really a deleted row.
				$r .= $this->_deleted( array( $orig_line ), false );
			} else { // A true changed row.
				$r .= '<tr>' . $this->deletedLine( $orig_line ) . "</tr>\n";
				$r .= '<tr>' . $this->addedLine( $final_line ) . "</tr>\n";
			}
		}
		return $r;
	}
}