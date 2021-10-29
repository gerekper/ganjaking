<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
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
  $datestr = $row->rdate;

  $series['clicks']['data'][] = array($datestr, (int)$row->clicks);
  $series['uniques']['data'][] = array($datestr, (int)$row->uniques);
  $series['transactions']['data'][] = array($datestr, (int)$row->transactions);

  if(!$start_date)
    $start_date = $datestr;
}

$series = apply_filters('wafp-general-stat-graph-series', $series);

$json_series = json_encode(array_values($series));

$chart_height = (isset($chart_height)?$chart_height:"200px");
$chart_width  = (isset($chart_width)?$chart_width:"100%");
$chart_id     = (isset($chart_id)?$chart_id:"wafp-stats-graph");
?>
<div id="<?php echo $chart_id; ?>" style="width: <?php echo $chart_width; ?>; height: <?php echo $chart_height; ?>;">&nbsp;</div>
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

    var plot = jQuery.plot(jQuery("#<?php echo $chart_id; ?>"),
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
    jQuery("#<?php echo $chart_id; ?>").bind("plothover", function (event, pos, item) {
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

    jQuery("#<?php echo $chart_id; ?>").bind("plotclick", function (event, pos, item) {
        if (item) {
            jQuery("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});
</script>
