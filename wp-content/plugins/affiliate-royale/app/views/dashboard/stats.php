<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="wafp-dash-affiliate-panel">
<h3><?php _e('My Stats', 'affiliate-royale', 'easy-affiliate'); ?></h3>
<p><?php _e('Select the period you want to view', 'affiliate-royale', 'easy-affiliate'); ?>:<br/><?php WafpReportsHelper::periods_dropdown('wafp-report-period', $period, 'javascript:wafp_view_dashboard_affiliate_page( \'' . site_url('index.php') . '\', \'dashboard_affiliate_stats\', this.value );'); ?>&nbsp;&nbsp;<img src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('Loading...', 'affiliate-royale', 'easy-affiliate'); ?>" style="display: none;" class="wafp-stats-loader" /></p>
<?php
$series = array('clicks' => array('data' => array(), 'label' => __('Clicks', 'affiliate-royale', 'easy-affiliate')),
                'uniques' => array('data' => array(), 'label' => __('Uniques', 'affiliate-royale', 'easy-affiliate')),
                'transactions' => array('data' => array(), 'label' => __('Sales', 'affiliate-royale', 'easy-affiliate')));
$omax = 0;
$start_date = false;

foreach($stats as $row)
{
  $omax = ($row->clicks > $omax)?$row->clicks:$omax;
  $omax = ($row->uniques > $omax)?$row->uniques:$omax;
  $omax = ($row->transactions > $omax)?$row->transactions:$omax;
  $omax = apply_filters('wafp-dashboard-stats-graph-max', $omax, $row);
  $datestr = date('Y/m/d', $row->tsdate);

  $series['clicks']['data'][] = array($datestr, (int)$row->clicks);
  $series['uniques']['data'][] = array($datestr, (int)$row->uniques);
  $series['transactions']['data'][] = array($datestr, (int)$row->transactions);

  if(!$start_date)
    $start_date = $datestr;
}

$series = apply_filters('wafp-dashboard-stats-graph-series', $series, $start_date, $datestr);

$json_series = json_encode(array_values($series));

global $wafp_options;
?>
<div id="wafp-stats-graph" style="width: 100%; height: 250px;">&nbsp;</div>
<script id="source" language="javascript" type="text/javascript">
jQuery(function () {
    var series = <?php echo $json_series; ?>;
    var tseries = [];

    // Translate dates into timestamps
    for( s in series ) {
      ts = [];
      for( d in series[s]['data'] ) {
        ts.push([(new Date(series[s]['data'][d][0])).getTime(),series[s]['data'][d][1]]);
      }
      tseries.push({"data": ts, "label": series[s]['label']});
    }

    var plot = jQuery.plot(jQuery("#wafp-stats-graph"),
           tseries, {
               series: {
                   lines: { show: true },
                   points: { show: true }
               },
               grid: {   backgroundColor: { colors: ["#fff", "#eee"] },
               hoverable: true, clickable: true },
               yaxis: { min: 0, max: <?php echo $omax; ?> },
               xaxis: {
                 mode: "time",
                 min: (new Date("<?php echo $start_date; ?>")).getTime(),
                 max: (new Date("<?php echo $datestr; ?>")).getTime()
               }
             });

    function showTooltip(x, y, contents) {
        jQuery('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            'text-align': 'center',
            left: x + 5,
            border: '1px solid #fdd',
            padding: '5px',
            border: '3px solid #ababab',
            'background-color': '#fee',
            '-webkit-border-radius': '5px',
            '-moz-border-radius': '5px',
            'border-radius': '5px',
            opacity: 0.95
        }).appendTo("body").fadeIn(100);
    }

    var previousPoint = null;
    jQuery("#wafp-stats-graph").bind("plothover", function (event, pos, item) {
        jQuery("#x").text(pos.x);
        jQuery("#y").text(pos.y);

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;

                jQuery("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1];

                var date = new Date(x);

                showTooltip(item.pageX, item.pageY,
                            date.toDateString() + "<br/><strong>" + y + " " + item.series.label + "</strong>");
            }

        }
    });

    jQuery("#wafp-stats-graph").bind("plotclick", function (event, pos, item) {
        if (item) {
            jQuery("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});
</script>
<table class="wafp-stats-table" cellspacing="0">
<thead>
  <th><?php _e('Date', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php _e('Clicks', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php _e('Uniques', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php _e('Transactions', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php _e('Commissions', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php _e('Corrections', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php _e('Total', 'affiliate-royale', 'easy-affiliate'); ?></th>
  <?php do_action('wafp-dashboard-stats-table-header'); ?>
</thead>
<tbody>
<?php
  $clicks_total = 0;
  $uniques_total = 0;
  $transactions_total = 0;
  $commissions_total = 0;
  $corrections_total = 0;
  $totals_total = 0;
  foreach($stats as $row):
    $clicks_total += $row->clicks;
    $uniques_total += $row->uniques;
    $transactions_total += $row->transactions;
    $commissions_total += $row->commissions;
    $corrections_total += $row->corrections;
    $totals_total += ((float)$row->commissions - (float)$row->corrections);

    if($row->corrections > 0)
      $corrections = "<span class=\"wafp-red-text\">(" . WafpAppHelper::format_currency( $row->corrections ) . ")</span>";
    else
      $corrections = WafpAppHelper::format_currency((float)0.00);
  ?>
<tr>
  <td><?php echo $row->date; ?></td>
  <td><?php echo $row->clicks; ?></td>
  <td><?php echo $row->uniques; ?></td>
  <td><?php echo $row->transactions; ?></td>
  <td><?php echo WafpAppHelper::format_currency( $row->commissions ); ?></td>
  <td><?php echo $corrections; ?></td>
  <td><?php echo WafpAppHelper::format_currency( ((float)$row->commissions - (float)$row->corrections) ); ?></td>
  <?php do_action('wafp-dashboard-stats-table-row',$row); ?>
</tr>
<?php
  endforeach;
?>
</tbody>
<?php
if($corrections_total > 0)
  $corrections_total = "<span class=\"wafp-red-text\">(" . WafpAppHelper::format_currency( $corrections_total ) . ")</span>";
else
  $corrections_total = WafpAppHelper::format_currency((float)0.00);
?>
<tfoot style="text-align: left;">
  <th><?php _e("Totals", 'affiliate-royale', 'easy-affiliate'); ?></th>
  <th><?php echo $clicks_total; ?></th>
  <th><?php echo $uniques_total; ?></th>
  <th><?php echo $transactions_total; ?></th>
  <th><?php echo WafpAppHelper::format_currency( $commissions_total ); ?></th>
  <th><?php echo $corrections_total; ?></th>
  <th><?php echo WafpAppHelper::format_currency( $totals_total ); ?></th>
  <?php do_action('wafp-dashboard-stats-table-footer',$row->date); ?>
</tfoot>
</table>
</div>
