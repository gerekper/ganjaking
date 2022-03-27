// import { addFilter } from '../../../node_modules/@wordpress/hooks';
function addCostsToOrdersReport( reportTableData ) {
  const { endpoint, items } = reportTableData;
  console.log(endpoint);
  if ( 'orders' !== endpoint ) {
    return reportTableData;
  }

  reportTableData.headers = [
    ...reportTableData.headers,
    {
      label: 'Total cost',
      key: 'cog_total_cost',
    },
  ];
  reportTableData.rows = reportTableData.rows.map( ( row, index ) => {
    const item = items.data[ index ];
    console.log(item);
    const newRow = [
      ...row,
      {
        display: item.cog_total_cost,
        value: item.cog_total_cost,
      },
    ];
    return newRow;
  } );

  return reportTableData;
}
wp.hooks.addFilter( 'woocommerce_admin_report_table', 'dev-blog-example', addCostsToOrdersReport );
