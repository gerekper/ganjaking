<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="datatable">
  <thead>
    <tr>
      <?php foreach($columns as $key => $col) { ?>
        <th width="<?php echo $col['width']; ?>"><?php echo $col['label']; ?></th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="<?php echo count($columns); ?>" class="dataTables_empty"></td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <?php $i = 0; ?>
      <?php foreach($columns as $key => $col) { ?>
        <th><input type="text" name="search_<?php echo $key; ?>" value="Search <?php echo $col['label']; ?>" class="search_init" rel="<?php echo $i++; ?>" /></th>
      <?php } ?>
    </tr>
  </tfoot>
</table>

<script type="text/javascript">
  var asInitVals = new Array();
  jQuery(document).ready(function() {
    var oTable = jQuery('#datatable').dataTable( {
      /* Visible / Hidden columns & Formatting */
      "aoColumnDefs": [
        <?php foreach($columns as $key => $col) { ?>
          <?php $column_keys = array_keys($columns); ?>
          <?php $last_key = ($column_keys[count($columns)-1] == $key); ?>
          <?php $col_index = array_search( $key, array_keys($columns) ); ?>
          <?php if( $col['type'] == 'link' ) { ?>
            /* <?php echo $key; ?> */ {
              "fnRender": function ( oObj ) {
                return '<a href="' + "<?php echo $col['link']; ?>".replace( /:<?php echo $col['replace']; ?>/i, oObj.aData[<?php echo array_search( $col['replace'], array_keys($columns) ); ?>] ) + '">' + oObj.aData[<?php echo $col_index; ?>] + '</a>';
              },
              "aTargets": [ <?php echo $col_index; ?> ]
            }<?php echo ( $last_key ? '' : "," ); ?>
          <?php } elseif( $col['type'] == 'hidden' ) { ?>
            /* <?php echo $key; ?> */ { "bVisible": false, "aTargets": [ <?php echo $col_index; ?> ] }<?php echo ( $last_key ? '' : "," ); ?>
          <?php } else { ?>
            /* <?php echo $key; ?> */ { "aTargets": [ <?php echo $col_index; ?> ] }<?php echo ( $last_key ? '' : "," ); ?>
          <?php } ?>
        <?php } ?>
      ],

      /* Boilerplate Options */
      "bJQueryUI": true,
      "aaSorting": [[ <?php echo (isset($sortcol)?$sortcol:0); ?>, "<?php echo (isset($sortdir)?$sortdir:"asc"); ?>" ]],
      "sPaginationType": "full_numbers",
      "aLengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
      "bProcessing": true,
      "bServerSide": true,
      "sAjaxSource": ajaxurl + "?action=<?php echo $ajax_action; ?>&_wafp_nonce=<?php echo wp_create_nonce($ajax_action); ?>"
  } );

  // Unbind the default search input behavior in favor of one less resource intensive (searching on each keyup event)
  jQuery('#datatable_filter input').unbind();
  jQuery('#datatable_filter input').bind('keyup', function(e) {
    if(e.keyCode == 13) {
      oTable.fnFilter(this.value);
    }
  });

  jQuery("tfoot input").keypress( function (e) {
    // Apparently 13 is the enter key
    if(e.which == 13) {
      e.preventDefault();
      /* Filter on the column (the index) of this element */
      oTable.fnFilter( this.value, jQuery(this).attr('rel') );
      gaiSelected = []; // clear the selection on filter
    }
  } );

  /*
   * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
   * the footer
   */
  jQuery("tfoot input").each( function (i) {
    asInitVals[i] = this.value;
  } );

  jQuery("tfoot input").focus( function () {
    if ( this.className == "search_init" )
    {
      this.className = "";
      this.value = "";
    }
  } );

  jQuery("tfoot input").blur( function (i) {
    if ( this.value == "" )
    {
      this.className = "search_init";
      this.value = asInitVals[jQuery("tfoot input").index(this)];
    }
  } );
} );
</script>
