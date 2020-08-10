<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Reports', 'energyplus'), 'description' => '', 'buttons'=>'')); ?>

<?php echo EnergyPlus_View::run('reports/nav' ) ?>

<div id="energyplus-reports" class="__A__GP">

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 col-md-offset-2 text-center">
        <div class="__A__Reports_Range btn-group" role="group" aria-label="Button group with nested dropdown">
          <a href="<?php echo EnergyPlus_Helpers::change_url('range', 'daily', 'btn btn-secondary', 'btn-dark', true)?>"><?php esc_html_e('Daily', 'energyplus'); ?></a>
          <a href="<?php echo EnergyPlus_Helpers::change_url('range', 'weekly', 'btn btn-secondary', 'btn-dark')?>"><?php esc_html_e('Weekly', 'energyplus'); ?></a>
          <a href="<?php echo EnergyPlus_Helpers::change_url('range', 'monthly', 'btn btn-secondary', 'btn-dark')?>"><?php esc_html_e('Monthly', 'energyplus'); ?></a>
          <a href="<?php echo EnergyPlus_Helpers::change_url('range', 'yearly', 'btn btn-secondary', 'btn-dark')?>"><?php esc_html_e('Yearly', 'energyplus'); ?></a>
        </div>
      </div>
    </div>

    <div class="row">
      <?php $max_width = 261/30*count($data['results']);
      if (100>$max_width || ( "1" === EnergyPlus::option('reports-graph', "2"))) {
        $max_width = 100;
      }?>

      <div id="__A__eeez" class="__A__eeez_Graph<?php echo esc_attr(EnergyPlus::option('reports-graph', "2"))?>">
        <div id="__A__eee" style="width:<?php echo esc_attr($max_width) ?>%;">
          <canvas id="__A__Chart_1" width="100%" class="__A__Chart_1_<?php echo esc_attr(EnergyPlus::option('reports-graph', "2"))?>"></canvas>
        </div>
        <script>
        jQuery(document).ready(function($) {
          "use strict";

          $(window).on('load', function() {

            var $gal = $("#__A__eeez"),
            mmv = false,
            cnt=1,
            galW = $gal.outerWidth(true),
            galSW = $gal[0].scrollWidth,
            wDiff = (galSW / galW) - 1,
            mPadd = 60,
            damp = 20,
            mX = 0,
            mX2 = 0,
            posX = 0,
            wNew = 0,
            mmAA = galW - (mPadd * 1.2),
            mmAAr = (galW / mmAA);

            var intv =   setInterval(function() {
              ++cnt;

              if(!mmv) {
                posX=  mX = mX2 = $(window).width();
                $("#__A__eeez").scrollLeft($(window).width()*<?php echo esc_attr($max_width/100)?>).css({opacity:1});
              }

              posX += (mX2 - posX) / damp; // catching delay
              if (wNew !== (posX * wDiff)) {
                $gal.scrollLeft(posX * wDiff);
                wNew = posX * wDiff;
              }

            }, 10);

            $gal.mousemove(function(e) {
              mmv = true
              if (cnt<100)
              return;
              mX = e.pageX - $(this).parent().offset().left - this.offsetLeft;
              mX2 = Math.min(Math.max(0, mX - mPadd), mmAA) * mmAAr;
            });

            $("#__A__eeez").scrollLeft($(window).width()*<?php echo esc_attr($max_width/100)?>).css({opacity:1});

          });
        });
        </script>
      </div>
      <div class="__A__Reports_Top_Cards row w-100">

        <?php foreach ($data['quick'] AS $quick) {   ?>
          <div class="col-lg-<?php echo floor(12/count($data['quick'])); ?> col-sm-4">
            <div class="card-body">
              <h5 class="card-title"><?php echo esc_html($quick['title']) ?></h5>
              <p class="card-text text-center"><?php echo wp_kses_data($quick['text']) ?></p>
            </div>
          </div>
        <?php } ?>
      </div>

      <br />&nbsp;
      <br />&nbsp;
      <br />&nbsp;
      <br />


    </div>
  </div>
</div>
<div class="__A__Reports_Div">
  <div class="__A__Reports_Div_Inner table-responsive ">
    <?php $data['results_r'] =array_reverse($data['results']) ?>
    <table class="__A__Reports_Table table table-hover text-center">
      <thead>
        <th class="text-left"><?php esc_html_e('Date', 'energyplus'); ?></th>
        <th><?php esc_html_e('Visitors', 'energyplus'); ?></th>
        <th><?php esc_html_e('Orders', 'energyplus'); ?></th>
        <th class="text-right"><?php esc_html_e('Sales', 'energyplus'); ?> (<?php echo get_woocommerce_currency_symbol()?>)</th>
        <th class="text-right"><?php esc_html_e('Net Sal.', 'energyplus'); ?> (<?php echo get_woocommerce_currency_symbol()?>)</th>
        <th class="text-right"><?php esc_html_e('Shipping', 'energyplus'); ?> (<?php echo get_woocommerce_currency_symbol()?>)</th>
        <th class="text-right"><?php esc_html_e('Taxes', 'energyplus'); ?> (<?php echo get_woocommerce_currency_symbol()?>)</th>
        <th class="text-right"><?php esc_html_e('Refunds', 'energyplus'); ?> (<?php echo get_woocommerce_currency_symbol()?>)</th>
        <th class="text-right"><?php esc_html_e('Coupons', 'energyplus'); ?> (<?php echo get_woocommerce_currency_symbol()?>)</th>
        <th class="text-right"><?php esc_html_e('Goals', 'energyplus'); ?> &nbsp;(<?php echo get_woocommerce_currency_symbol()?>)</th>
      </thead>
      <tbody>
        <?php
        foreach ($data['results_r'] AS $date) {
          ?>
          <tr>
            <td class="text-left"><?php echo esc_html($date['label']) ?></td>
            <td><?php echo number_format(absint($date['visitors']))?></td>
            <td><?php echo (0 < $date['orders'])? intval($date['orders']) : '-' ?></td>
            <td class="text-right" ><?php echo floatval(0 < $date['sales'])? wc_price($date['sales']) : '-' ?>

              <?php if ($date['prev'] === 0) {
                echo '&nbsp; <span class="__A__Opacity_0">▲</span>';
              } elseif ($date['sales']>$date['prev']) {
                echo '&nbsp; <span class="text-success">▲</span>';
              } elseif ($date['sales']<$date['prev']) {
                echo '&nbsp; <span class="text-danger">▼</span>';
              } else {
                echo '&nbsp; <span class="text-warning">—</span>';
              }
              ?>
            </td>
            <td class="text-right" ><?php echo (0 < $date['net_sales'])? wc_price($date['net_sales']) : '-' ?></td>
            <td class="text-right" ><?php echo (0 < $date['total_shipping'])? wc_price($date['total_shipping']) : '-' ?></td>
            <td class="text-right" ><?php echo (0 < $date['total_tax'])? wc_price($date['total_tax']) : '-' ?></td>
            <td class="text-right" ><?php echo (0 < $date['total_refunds'])? wc_price($date['total_refunds']) : '-' ?></td>
            <td class="text-right" ><?php echo (0 < $date['total_discount'])? wc_price($date['total_discount']) : '-' ?></td>
            <td class="text-right" ><span class="__A__Goal_Bullet<?php if ($date['sales']<=$date['goal']) { echo ' text-danger'; } else { echo ' text-success'; } ?>"><?php echo wc_price($date['sales']-$date['goal'])?> &nbsp; &#11044;</span></td>

          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ('yearly' !== EnergyPlus_Helpers::get("range")) { ?>
  <div class="__A__Reports_Div">
    <div class="__A__Reports_Div_Inner">
      <h6><?php esc_html_e("Conversions", 'energyplus'); ?></h6>
      <div id="__A__Chart_Conversation">
      </div>
    </div>
  </div>
<?php } ?>

<div class="__A__Reports_Div">
</div>


<?php
if ("2" === EnergyPlus::option('reports-graph', "2")) {   ?>
  <script>
  jQuery(document).ready(function() {
    "use strict";

    var dataset_01 = {
      label: "Visitors",

      borderWidth:1,

      pointRadius: 0,
      /*bezierCurve : false,
      lineTension: 0,*/

      <?php if ('dark' === EnergyPlus::$theme) {?>
        backgroundColor: "#CBE86B",
        backgroundColor: "rgba(0,173,160,0.6)",
        backgroundColor: "rgba(242,201,10,0.5)",
        borderColor: "rgba(255,255,255,0.1)",
        pointBorderColor: "rgba(0,0,0,0)",
        <?php } else {   ?>
          backgroundColor: "#CBE86B",
          backgroundColor: "rgba(0,173,160,0.6)",
          backgroundColor: "rgba(203,232,107,0.5)",
          borderColor: "rgba(255,255,255,0.8)",
          pointBorderColor: "rgba(0,0,0,0)",
          <?php } ?>
          data: [0,<?php echo implode(",", array_column($data['results'], 'visitors')) ?>,0]
        };

        var dataset_02 = {
          label: "Sales",
          borderWidth:2,
          pointRadius: 0,

          <?php if ('dark' === EnergyPlus::$theme){ ?>
            backgroundColor: "rgba(0,173,160,0.6)",
            backgroundColor: "rgba(203,232,107,0.5)",
            backgroundColor: "rgba(0,173,160,0.6)",
            borderColor: "rgba(0,0,0,0)",
            pointBorderColor: "rgba(0,0,0,0)",
            <?php } else {   ?>
              backgroundColor: "rgba(0,173,160,0.6)",
              backgroundColor: "rgba(203,232,107,0.5)",
              backgroundColor: "rgba(0,173,160,0.6)",
              borderColor: "rgba(0,0,0,0)",
              pointBorderColor: "rgba(0,0,0,0)",
              borderColor: "rgba(255,255,255,0.6)",

              <?php } ?>

              data: [0,<?php echo implode(",",array_column($data['results'], 'sales')); ?>,0]

            };

            // Graph data
            var data = {
              labels: ['','<?php echo implode("','", array_column($data['results'], 'label')); ?>',''],
              datasets: [dataset_01]
            };

            // Graph options
            var options = {
              responsive: true,
              maintainAspectRatio: false,
              title: { display: true},
              legend:{ display:false },
              tooltips: {
                position: 'nearest',
                mode: 'index',
                intersect: false,
                bodySpacing: 6,
                fontSize:13,
                callbacks: {
                  label: function (tooltipItems, data) {
                    var label = data.datasets[tooltipItems.datasetIndex].label || '';

                    if (label) {
                      label += ': ';
                    }


                    if (tooltipItems.datasetIndex === 0)
                    label += tooltipItems.yLabel + " <?php echo get_woocommerce_currency()?>";
                    else
                    label += tooltipItems.yLabel;
                    return label;
                  }
                }
              },
              animation: {
                duration : 1400,
                easing : 'easeOutBack'
              },
              scales:{
                xAxes: [{
                  minBarLength: 10,
                  display: true,
                  beginAtZero:true,
                  drawBorder:false,

                  ticks: {
                    fontColor: "#A7A7A2",
                    beginAtZero:true,
                    padding: -35,
                    mirror: true,
                    fontSize: 12,
                    stepSize: 1,
                    max: 81,
                    min:0

                  },
                  gridLines: {
                    drawBorder: false,
                    display:false,
                    zeroLineWidth: 10

                  }}],
                  yAxes: [{ display: false }]
                },
                scaleBeginAtZero : true
              };

              // The container
              var ctx = document.getElementById("__A__Chart_1").getContext("2d");

              // Display the first chart
              var myLineChart = new Chart(ctx, {
                type: 'line',
                data: data,
                options : options
              });

              // Add second chart after a delay
              setTimeout(function(){
                myLineChart.chart.config.data.datasets.unshift(dataset_02);
                myLineChart.update();
              },100);



              <?php if ('yearly' !== EnergyPlus_Helpers::get("range")) { ?>

                /* CONVERSION GRAPH */

                var funnel_data = {
                  labels: ['<?php esc_html_e('Home Page', 'energyplus'); ?>', '<?php esc_html_e('Product Page', 'energyplus'); ?>', '<?php esc_html_e('Add to Cart', 'energyplus'); ?>', '<?php esc_html_e('Checkout', 'energyplus'); ?>', '<?php esc_html_e('Buy', 'energyplus'); ?>'],
                  colors: ['#54c8a7',  '#e4f2af'],
                  values: [<?php echo implode(',', $data['funnel'])?>]
                }

                var graph = new FunnelGraph({
                  container: '#__A__Chart_Conversation',
                  gradientDirection: 'horizontal',
                  data: funnel_data,
                  displayPercent: true,
                  direction: 'horizontal'
                });

                graph.draw();
                <?php } ?>
              })
              </script>
            <?php } else {   ?>
              <script>
              jQuery(document).ready(function() {
                "use strict";

                var dataset_01 = {
                  label: "Sales",
                  <?php if ('dark' === EnergyPlus::$theme) {?>
                    backgroundColor: "#CBE86B",
                    backgroundColor: "rgba(0,173,160,0.6)",
                    borderWidth:0,
                    borderColor: "rgba(0,0,0,0.8)",
                    pointBorderColor: "rgba(0,0,0,0)",
                    <?php } else {   ?>
                      backgroundColor: "#CBE86B",
                      backgroundColor: "rgba(204,204,204,0.8)",
                      borderWidth:1,
                      borderColor: "rgba(255,255,255,0.8)",
                      pointBorderColor: "rgba(0,0,0,0)",
                      <?php }?>
                      pointRadius: 0,
                      data: [<?php echo implode(",",array_column($data['results'], 'sales')); ?>]

                    };

                    // Graph data
                    var data = {
                      labels: ['<?php echo implode("','", array_column($data['results'], 'label')); ?>'],
                      datasets: [dataset_01]
                    };

                    // Graph options
                    var options = {
                      responsive: true,
                      maintainAspectRatio: false,
                      title: { display: true},
                      legend:{ display:false },
                      tooltips: {
                        position: 'nearest',
                        mode: 'index',
                        intersect: false,
                        bodySpacing: 6,
                        fontSize:13,
                        callbacks: {
                          label: function (tooltipItems, data) {
                            var label = data.datasets[tooltipItems.datasetIndex].label || '';

                            if (label) {
                              label += ': ';
                            }


                            if (tooltipItems.datasetIndex === 1)
                            label += tooltipItems.yLabel + " <?php echo get_woocommerce_currency()?>";
                            else
                            label += tooltipItems.yLabel;
                            return label;
                          }
                        }
                      },
                      animation: {
                        duration : 1400,
                        easing : 'easeOutBack'
                      },
                      scales:{
                        xAxes: [{
                          minBarLength: 10,
                          display: true,
                          beginAtZero:true,
                          drawBorder:false,

                          ticks: {
                            fontColor: "#A7A7A2",
                            beginAtZero:true,
                            padding: -35,
                            mirror: true,
                            fontSize: 12,
                            stepSize: 1,
                            max: 81,
                            min:0

                          },
                          gridLines: {
                            drawBorder: false,
                            display:false,
                            zeroLineWidth: 10

                          }}],
                          yAxes: [{ display: true,
                            gridLines: {
                              drawBorder: false,
                              display:true,
                              zeroLineWidth: 0,
                              <?php if ('dark' === EnergyPlus::$theme) { ?>
                                <?php }else {  ?>
                                  color: '#efefef'
                                  <?php }?>

                                }}]
                              },
                              scaleBeginAtZero : true
                            };

                            // The container
                            var ctx = document.getElementById("__A__Chart_1").getContext("2d");

                            // Display the first chart
                            var myLineChart = new Chart(ctx, {
                              type: 'bar',
                              data: data,
                              options : options
                            });


                            <?php if ('yearly' !== EnergyPlus_Helpers::get("range")) { ?>

                              /* CONVERSION GRAPH */

                              var funnel_data = {
                                labels: ['<?php esc_html_e('Home Page', 'energyplus'); ?>', '<?php esc_html_e('Product Page', 'energyplus'); ?>', '<?php esc_html_e('Add to Cart', 'energyplus'); ?>', '<?php esc_html_e('Checkout', 'energyplus'); ?>', '<?php esc_html_e('Buy', 'energyplus'); ?>'],
                                colors: ['#54c8a7',  '#e4f2af'],
                                values: [<?php echo implode(',', $data['funnel'])?>]
                              }

                              var graph = new FunnelGraph({
                                container: '#__A__Chart_Conversation',
                                gradientDirection: 'horizontal',
                                data: funnel_data,
                                displayPercent: true,
                                direction: 'horizontal'
                              });

                              graph.draw();

                              <?php } ?>

                            })
                            </script>
                          <?php } ?>
