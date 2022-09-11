import { Component, Fragment } from '@wordpress/element';
import {isEqual} from "lodash";
import { __, _x } from '@wordpress/i18n';
import {getProductReportData} from "../../packages/data/products";
import {getEditorlLink} from "../../lib/general";
import {formatCurrency} from "../../lib/numbers";
import {ReportTable} from "../report-table";
import React from "react";


class ProductsTable extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			productIsRequesting: false,
			productReport: [],
			totalResults: 0,
			totalPages: 0
		};

	}

	componentDidMount() {
		const { query } = this.props;
		this.update( query );
	}

	componentDidUpdate( prevProps, prevState ) {

		if ( !isEqual( prevProps.query, this.props.query ) ) {
			const prevQuery    = this.getQueryWithoutChart( prevProps.query );
			const currentQuery = this.getQueryWithoutChart( this.props.query );

			if ( !isEqual( prevQuery, currentQuery ) ) {
				this.update( currentQuery );
			}
		}
	}

	getQueryWithoutChart = ( query ) => {
		const { chart, chartType, ...newQuery } = query;
		return { ...newQuery };
	};

	update = ( query) => {

		this.setState( { productIsRequesting: true } );

		getProductReportData(query).then(report => {
			const {data, totalResults, totalPages} = report;
			this.setState({
				productReport: data,
				productIsRequesting: false,
				totalResults,
				totalPages
			});
		});

	};

	getHeadersContent() {

		let headerContent = [
			{
				label: __('Product name', 'yith-woocommerce-subscription'),
				key: 'product_name',
				isSortable: true,
			},
			{
				label: __('Subscribers', 'yith-woocommerce-subscription'),
				key: 'subscribers',
				isSortable: true
			},
			{
				label: __('MRR', 'yith-woocommerce-subscription'),
				key: 'mrr',
				isSortable: true
			},

		];

		return headerContent;
	}

	getProductsRows() {

		const {productReport} = this.state;
		return productReport.map(product => {

			return [
				{
					display: getEditorlLink(product.product_name, product.product_id),
					value: product.product_name
				},
				{display: product.subscribers, value: product.subscribers},
				{display: formatCurrency(product.mrr), value: product.mrr},
			]
		});

	}

	onSort = (key, direction) => {
		const {query} = this.props;
		direction = query.order === 'desc' ? 'asc' : 'desc';
		onQueryChange('sort')(key, direction);
	}

	render(){
		const {query} = this.props;
		const productRows = this.getProductsRows();
		const {totalResults, productIsRequesting} = this.state;
		const per_page = query?.per_page ? query.per_page : 25;
		return(

			<ReportTable
				headers={this.getHeadersContent()}
				rows={productRows}
				query={ query }
				totalRows = {totalResults}
				rowsPerPage={parseInt(per_page)}
				title={ __( 'Subscription Products', 'yith-woocommerce-subscription' ) }
				isRequesting={productIsRequesting}
			/>

		);
	}
}

export default ProductsTable;