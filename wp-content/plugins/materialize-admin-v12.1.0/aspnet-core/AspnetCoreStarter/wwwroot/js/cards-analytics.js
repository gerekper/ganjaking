/**
 * Analytics Cards
 */

'use strict';

(function () {
  let cardColor, labelColor, headingColor, borderColor, grayColor;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
    grayColor = '#3b3e59';
  } else {
    cardColor = config.colors.cardColor;
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
    grayColor = '#f4f4f6';
  }

  const chartColors = {
    donut: {
      series1: config.colors.warning,
      series2: '#fdb528cc',
      series3: '#fdb52899',
      series4: '#fdb52866',
      series5: config.colors_label.warning
    }
  };

  // Total Transactions Bar Chart
  // --------------------------------------------------------------------
  const totalTransactionChartEl = document.querySelector('#totalTransactionChart'),
    totalTransactionChartConfig = {
      chart: {
        height: 218,
        stacked: true,
        type: 'bar',
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return Math.abs(val);
          }
        }
      },
      legend: { show: false },
      dataLabels: { enabled: false },
      colors: [config.colors.primary, config.colors.success],
      grid: {
        borderColor,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: false } },
        padding: {
          top: -5,
          bottom: -25
        }
      },
      states: {
        hover: { filter: { type: 'none' } },
        active: { filter: { type: 'none' } }
      },
      plotOptions: {
        bar: {
          borderRadius: 5,
          barHeight: '30%',
          horizontal: true,
          endingShape: 'flat',
          startingShape: 'rounded'
        }
      },
      xaxis: {
        position: 'top',
        axisTicks: { show: false },
        axisBorder: { show: false },
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        labels: {
          formatter: function (val) {
            return Math.abs(Math.round(val));
          },
          style: {
            colors: labelColor,
            fontFamily: 'Inter'
          }
        }
      },
      yaxis: { labels: { show: false } },
      series: [
        {
          name: 'Last Week',
          data: [83, 153, 213, 279, 213, 153, 83]
        },
        {
          name: 'This Week',
          data: [-84, -156, -216, -282, -216, -156, -84]
        }
      ]
    };
  if (typeof totalTransactionChartEl !== undefined && totalTransactionChartEl !== null) {
    const totalTransactionChart = new ApexCharts(totalTransactionChartEl, totalTransactionChartConfig);
    totalTransactionChart.render();
  }

  // Performance Overview Line Chart
  // --------------------------------------------------------------------
  const performanceOverviewChartEl = document.querySelector('#performanceOverviewChart'),
    performanceOverviewChartConfig = {
      chart: {
        height: 218,
        type: 'line',
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      series: [
        {
          data: [7, 65, 40, 7, 40, 80, 45, 65, 65]
        }
      ],
      stroke: {
        curve: 'stepline'
      },
      tooltip: {
        enabled: false
      },
      colors: [config.colors.warning],
      grid: {
        yaxis: {
          lines: {
            show: false
          }
        }
      },
      xaxis: {
        labels: {
          show: false
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      },
      responsive: [
        {
          breakpoint: 1200,
          options: {
            chart: {
              height: 268
            }
          }
        }
      ]
    };
  if (typeof performanceOverviewChartEl !== undefined && performanceOverviewChartEl !== null) {
    const performanceOverviewChart = new ApexCharts(performanceOverviewChartEl, performanceOverviewChartConfig);
    performanceOverviewChart.render();
  }

  // Visits By Day Bar Chart
  // --------------------------------------------------------------------
  const visitsByDayChartEl = document.querySelector('#visitsByDayChart'),
    visitsByDayChartConfig = {
      chart: {
        height: 240,
        type: 'bar',
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 8,
          distributed: true,
          columnWidth: '55%',
          endingShape: 'rounded',
          startingShape: 'rounded'
        }
      },
      series: [
        {
          data: [38, 55, 48, 65, 80, 38, 48]
        }
      ],
      tooltip: {
        enabled: false
      },
      legend: {
        show: false
      },
      dataLabels: {
        enabled: false
      },
      colors: [
        config.colors_label.primary,
        config.colors.primary,
        config.colors_label.primary,
        config.colors.primary,
        config.colors.primary,
        config.colors_label.primary,
        config.colors_label.primary
      ],
      grid: {
        show: false,
        padding: {
          top: -15,
          left: -7,
          right: -4
        }
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      },
      xaxis: {
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        },
        categories: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        labels: {
          style: {
            colors: labelColor
          }
        }
      },
      yaxis: { show: false },
      responsive: [
        {
          breakpoint: 1025,
          options: {
            chart: {
              height: 210
            }
          }
        }
      ]
    };
  if (typeof visitsByDayChartEl !== undefined && visitsByDayChartEl !== null) {
    const visitsByDayChart = new ApexCharts(visitsByDayChartEl, visitsByDayChartConfig);
    visitsByDayChart.render();
  }

  // Organic Sessions Donut Chart
  // --------------------------------------------------------------------
  const organicSessionsEl = document.querySelector('#organicSessionsChart'),
    organicSessionsConfig = {
      chart: {
        height: 355,
        type: 'donut',
        parentHeightOffset: 0
      },
      labels: ['USA', 'India', 'Canada', 'Japan', 'France'],
      tooltip: { enabled: false },
      dataLabels: { enabled: false },
      stroke: {
        width: 3,
        lineCap: 'round',
        colors: [cardColor]
      },
      states: {
        hover: {
          filter: { type: 'none' }
        },
        active: {
          filter: { type: 'none' }
        }
      },
      plotOptions: {
        pie: {
          endAngle: 130,
          startAngle: -130,
          customScale: 0.9,
          donut: {
            size: '83%',
            labels: {
              show: true,
              name: {
                offsetY: 25,
                fontSize: '50rem',
                fontFamily: 'Inter',
                color: labelColor
              },
              value: {
                offsetY: -15,
                fontWeight: 500,
                fontSize: '2.125rem',
                fontFamily: 'Inter',
                color: headingColor,
                formatter: function (val) {
                  return parseInt(val) + 'K';
                }
              },
              total: {
                show: true,
                label: '2022',
                fontSize: '1rem',
                fontFamily: 'Inter',
                color: labelColor,
                formatter: function (w) {
                  return '89K';
                }
              }
            }
          }
        }
      },
      series: [13, 18, 18, 24, 16],
      tooltip: {
        enabled: false
      },
      legend: {
        position: 'bottom',
        fontFamily: 'Inter',
        fontSize: '15px',
        markers: { offsetX: -5 },
        itemMargin: { horizontal: 10 },
        labels: {
          colors: headingColor
        }
      },
      colors: [
        chartColors.donut.series1,
        chartColors.donut.series2,
        chartColors.donut.series3,
        chartColors.donut.series4,
        chartColors.donut.series5
      ]
    };
  if (typeof organicSessionsEl !== undefined && organicSessionsEl !== null) {
    const organicSessions = new ApexCharts(organicSessionsEl, organicSessionsConfig);
    organicSessions.render();
  }

  // Weekly Sales Line Chart
  // --------------------------------------------------------------------
  const weeklySalesEl = document.querySelector('#weeklySalesChart'),
    weeklySalesConfig = {
      chart: {
        stacked: true,
        type: 'line',
        height: 235,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      tooltip: { enabled: false },
      series: [
        {
          type: 'column',
          name: 'Earning',
          data: [90, 52, 67, 45, 75, 55, 48]
        },
        {
          type: 'column',
          name: 'Expense',
          data: [-53, -29, -67, -84, -60, -40, -77]
        },
        {
          type: 'line',
          name: 'Expense',
          data: [73, 20, 50, -20, 58, 15, 31]
        }
      ],
      plotOptions: {
        bar: {
          borderRadius: 8,
          columnWidth: '57%',
          endingShape: 'flat',
          startingShape: 'rounded'
        }
      },
      markers: {
        size: 4,
        strokeWidth: 3,
        fillOpacity: 1,
        strokeOpacity: 1,
        colors: [cardColor],
        strokeColors: config.colors.warning
      },
      stroke: {
        curve: 'smooth',
        width: [0, 0, 3],
        colors: [config.colors.warning]
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        show: false
      },
      colors: [config.colors.primary, config.colors_label.primary],
      grid: {
        yaxis: { lines: { show: false } },
        padding: {
          top: -28,
          left: -6,
          right: -8,
          bottom: -5
        }
      },
      xaxis: {
        axisTicks: { show: false },
        axisBorder: { show: false },
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        labels: {
          style: {
            colors: labelColor
          }
        }
      },
      yaxis: {
        max: 100,
        min: -100,
        show: false
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof weeklySalesEl !== undefined && weeklySalesEl !== null) {
    const weeklySales = new ApexCharts(weeklySalesEl, weeklySalesConfig);
    weeklySales.render();
  }

  // Project Timeline Range Bar Chart
  // --------------------------------------------------------------------
  const projectTimelineEl = document.querySelector('#projectTimelineChart'),
    labels = ['Development Apps', 'UI Design', 'IOS Application', 'Web App Wireframing', 'Prototyping'],
    labelsResponsive = ['Development', 'UI Design', 'Application', 'App Wireframing', 'Prototyping'],
    projectTimelineConfig = {
      chart: {
        height: 230,
        type: 'rangeBar',
        parentHeightOffset: 0,
        toolbar: { show: false }
      },
      series: [
        {
          data: [
            {
              x: 'Catherine',
              y: [
                new Date(`${new Date().getFullYear()}-01-01`).getTime(),
                new Date(`${new Date().getFullYear()}-05-02`).getTime()
              ],
              fillColor: config.colors.primary
            },
            {
              x: 'Janelle',
              y: [
                new Date(`${new Date().getFullYear()}-02-18`).getTime(),
                new Date(`${new Date().getFullYear()}-05-30`).getTime()
              ],
              fillColor: config.colors.success
            },
            {
              x: 'Wellington',
              y: [
                new Date(`${new Date().getFullYear()}-02-07`).getTime(),
                new Date(`${new Date().getFullYear()}-05-31`).getTime()
              ],
              fillColor: config.colors.secondary
            },
            {
              x: 'Blake',
              y: [
                new Date(`${new Date().getFullYear()}-01-14`).getTime(),
                new Date(`${new Date().getFullYear()}-06-30`).getTime()
              ],
              fillColor: config.colors.info
            },
            {
              x: 'Quinn',
              y: [
                new Date(`${new Date().getFullYear()}-04-01`).getTime(),
                new Date(`${new Date().getFullYear()}-07-31`).getTime()
              ],
              fillColor: config.colors.warning
            }
          ]
        }
      ],

      tooltip: { enabled: false },
      plotOptions: {
        bar: {
          horizontal: true,
          borderRadius: 15,
          distributed: true,
          endingShape: 'rounded',
          startingShape: 'rounded',
          dataLabels: {
            hideOverflowingLabels: false
          }
        }
      },
      stroke: {
        width: 2,
        colors: [cardColor]
      },
      dataLabels: {
        enabled: true,
        style: { fontWeight: 400 },
        formatter: function (val, opts) {
          return labels[opts.dataPointIndex];
        }
      },
      states: {
        hover: { filter: { type: 'none' } },
        active: { filter: { type: 'none' } }
      },
      legend: { show: false },
      grid: {
        strokeDashArray: 6,
        borderColor,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: false } },
        padding: {
          top: -32,
          left: 15,
          right: 18,
          bottom: 4
        }
      },
      xaxis: {
        type: 'datetime',
        axisTicks: { show: false },
        axisBorder: { show: false },
        labels: {
          style: { colors: labelColor },
          datetimeFormatter: {
            year: 'MMM',
            month: 'MMM'
          }
        }
      },
      yaxis: {
        labels: {
          show: true,
          align: 'left',
          style: {
            fontSize: '0.875rem',
            colors: headingColor
          }
        }
      },
      responsive: [
        {
          breakpoint: 446,
          options: {
            dataLabels: {
              formatter: function (val, opts) {
                return labelsResponsive[opts.dataPointIndex];
              }
            }
          }
        }
      ]
    };
  if (typeof projectTimelineEl !== undefined && projectTimelineEl !== null) {
    const projectTimeline = new ApexCharts(projectTimelineEl, projectTimelineConfig);
    projectTimeline.render();
  }

  // Monthly Budget Area Chart
  // --------------------------------------------------------------------
  const monthlyBudgetEl = document.querySelector('#monthlyBudgetChart'),
    monthlyBudgetConfig = {
      chart: {
        height: 179,
        type: 'area',
        parentHeightOffset: 0,
        offsetY: -8,
        toolbar: { show: false }
      },
      tooltip: { enabled: false },
      dataLabels: { enabled: false },
      stroke: {
        width: 5,
        curve: 'smooth'
      },
      series: [
        {
          data: [0, 85, 25, 125, 90, 250, 200, 350]
        }
      ],
      grid: {
        show: false,
        padding: {
          left: 10,
          top: 0,
          right: 12
        }
      },
      fill: {
        type: 'gradient',
        gradient: {
          opacityTo: 0.7,
          opacityFrom: 0.5,
          shadeIntensity: 1,
          stops: [0, 90, 100],
          colorStops: [
            [
              {
                offset: 0,
                opacity: 0.6,
                color: config.colors.success
              },
              {
                offset: 100,
                opacity: 0.1,
                color: cardColor
              }
            ]
          ]
        }
      },
      theme: {
        monochrome: {
          enabled: true,
          shadeTo: 'light',
          shadeIntensity: 1,
          color: config.colors.success
        }
      },
      xaxis: {
        type: 'numeric',
        labels: { show: false },
        axisTicks: { show: false },
        axisBorder: { show: false }
      },
      yaxis: { show: false },
      markers: {
        size: 1,
        offsetY: 1,
        offsetX: -5,
        strokeWidth: 4,
        strokeOpacity: 1,
        colors: ['transparent'],
        strokeColors: 'transparent',
        discrete: [
          {
            size: 7,
            seriesIndex: 0,
            dataPointIndex: 7,
            strokeColor: config.colors.success,
            fillColor: cardColor
          }
        ]
      },
      responsive: [
        {
          breakpoint: 1200,
          options: {
            chart: {
              height: 255
            }
          }
        }
      ]
    };
  if (typeof monthlyBudgetEl !== undefined && monthlyBudgetEl !== null) {
    const monthlyBudget = new ApexCharts(monthlyBudgetEl, monthlyBudgetConfig);
    monthlyBudget.render();
  }

  // Performance Radar Chart
  // --------------------------------------------------------------------
  const performanceChartEl = document.querySelector('#performanceChart'),
    performanceChartConfig = {
      chart: {
        height: 300,
        type: 'radar',
        toolbar: {
          show: false
        }
      },
      legend: {
        show: true,
        markers: { offsetX: -2 },
        itemMargin: { horizontal: 10 },
        fontFamily: 'Inter',
        fontSize: '15px',
        labels: {
          colors: labelColor,
          useSeriesColors: false
        }
      },
      plotOptions: {
        radar: {
          polygons: {
            strokeColors: borderColor,
            connectorColors: borderColor
          }
        }
      },
      yaxis: {
        show: false
      },
      series: [
        {
          name: 'Income',
          data: [70, 90, 80, 95, 75, 90]
        },
        {
          name: 'Net Worth',
          data: [110, 72, 62, 65, 100, 75]
        }
      ],
      colors: [config.colors.warning, config.colors.primary],
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        labels: {
          show: true,
          style: {
            colors: [labelColor, labelColor, labelColor, labelColor, labelColor, labelColor],
            fontSize: '15px',
            fontFamily: 'Inter'
          }
        }
      },
      fill: {
        opacity: [1, 0.9]
      },
      stroke: {
        show: false,
        width: 0
      },
      markers: {
        size: 0
      },
      grid: {
        show: false,
        padding: {
          top: 0,
          bottom: -10
        }
      }
    };
  if (typeof performanceChartEl !== undefined && performanceChartEl !== null) {
    const performanceChart = new ApexCharts(performanceChartEl, performanceChartConfig);
    performanceChart.render();
  }

  // External Links Stacked Bar Chart
  // --------------------------------------------------------------------
  const externalLinksChartEl = document.querySelector('#externalLinksChart'),
    externalLinksChartConfig = {
      chart: {
        type: 'bar',
        height: 232,
        parentHeightOffset: 0,
        stacked: true,
        toolbar: {
          show: false
        }
      },
      series: [
        {
          name: 'Google Analytics',
          data: [155, 135, 320, 100, 150, 335, 160]
        },
        {
          name: 'Facebook Ads',
          data: [110, 235, 125, 230, 215, 115, 200]
        }
      ],
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '40%',
          borderRadius: 10,
          startingShape: 'rounded',
          endingShape: 'rounded'
        }
      },
      dataLabels: {
        enabled: false
      },
      tooltip: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 6,
        lineCap: 'round',
        colors: [cardColor]
      },
      legend: {
        show: false
      },
      colors: [config.colors.primary, config.colors.secondary],
      grid: {
        strokeDashArray: 10,
        borderColor,
        padding: {
          top: -12,
          left: -4,
          right: -5,
          bottom: 5
        }
      },
      xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        labels: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        show: false
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      },
      responsive: [
        {
          breakpoint: 1441,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '50%'
              }
            }
          }
        },
        {
          breakpoint: 1025,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '45%'
              }
            }
          }
        },
        {
          breakpoint: 577,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '35%'
              }
            }
          }
        },
        {
          breakpoint: 426,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '50%'
              }
            }
          }
        }
      ]
    };
  if (typeof externalLinksChartEl !== undefined && externalLinksChartEl !== null) {
    const externalLinksChart = new ApexCharts(externalLinksChartEl, externalLinksChartConfig);
    externalLinksChart.render();
  }

  // Sales Country Bar Chart
  // --------------------------------------------------------------------
  const salesCountryChartEl = document.querySelector('#salesCountryChart'),
    salesCountryChartConfig = {
      chart: {
        type: 'bar',
        height: 295,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      series: [
        {
          name: 'Sales',
          data: [17165, 13850, 12375, 9567, 7880]
        }
      ],
      plotOptions: {
        bar: {
          borderRadius: 8,
          barHeight: '60%',
          horizontal: true,
          distributed: true,
          startingShape: 'rounded',
          dataLabels: {
            position: 'bottom'
          }
        }
      },
      dataLabels: {
        enabled: true,
        textAnchor: 'start',
        offsetY: 8,
        offsetX: 11,
        style: {
          fontWeight: 600,
          fontSize: '0.9375rem',
          fontFamily: 'Inter'
        }
      },
      tooltip: {
        enabled: false
      },
      legend: {
        show: false
      },
      colors: [
        config.colors.primary,
        config.colors.success,
        config.colors.warning,
        config.colors.info,
        config.colors.danger
      ],
      grid: {
        strokeDashArray: 8,
        borderColor,
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: false } },
        padding: {
          top: -18,
          left: 21,
          right: 33,
          bottom: 10
        }
      },
      xaxis: {
        categories: ['US', 'IN', 'JA', 'CA', 'AU'],
        labels: {
          formatter: function (val) {
            return Number(val / 1000) + 'K';
          },
          style: {
            fontSize: '0.9375rem',
            colors: labelColor,
            fontFamily: 'Inter'
          }
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          style: {
            fontWeight: 600,
            fontSize: '0.9375rem',
            colors: headingColor,
            fontFamily: 'Inter'
          }
        }
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof salesCountryChartEl !== undefined && salesCountryChartEl !== null) {
    const salesCountryChart = new ApexCharts(salesCountryChartEl, salesCountryChartConfig);
    salesCountryChart.render();
  }

  // Weekly Overview Line Chart
  // --------------------------------------------------------------------
  const weeklyOverviewChartEl = document.querySelector('#weeklyOverviewChart'),
    weeklyOverviewChartConfig = {
      chart: {
        type: 'line',
        height: 178,
        offsetY: -9,
        offsetX: -16,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      series: [
        {
          name: 'Sales',
          type: 'column',
          data: [83, 68, 56, 65, 65, 50, 39]
        },
        {
          name: 'Sales',
          type: 'line',
          data: [63, 38, 31, 45, 46, 27, 18]
        }
      ],
      plotOptions: {
        bar: {
          borderRadius: 9,
          columnWidth: '50%',
          endingShape: 'rounded',
          startingShape: 'rounded',
          colors: {
            ranges: [
              {
                to: 50,
                from: 40,
                color: config.colors.primary
              }
            ]
          }
        }
      },
      markers: {
        size: 3.5,
        strokeWidth: 2,
        fillOpacity: 1,
        strokeOpacity: 1,
        colors: [cardColor],
        strokeColors: config.colors.primary
      },
      stroke: {
        width: [0, 2],
        colors: [config.colors.primary]
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        show: false
      },
      colors: [grayColor],
      grid: {
        strokeDashArray: 10,
        borderColor,
        padding: {
          bottom: -10
        }
      },
      xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        tickPlacement: 'on',
        labels: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        min: 0,
        max: 90,
        show: true,
        tickAmount: 3,
        labels: {
          formatter: function (val) {
            return parseInt(val) + 'K';
          },
          style: {
            fontSize: '0.75rem',
            fontFamily: 'Inter',
            colors: labelColor
          }
        }
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (typeof weeklyOverviewChartEl !== undefined && weeklyOverviewChartEl !== null) {
    const weeklyOverviewChart = new ApexCharts(weeklyOverviewChartEl, weeklyOverviewChartConfig);
    weeklyOverviewChart.render();
  }
})();
