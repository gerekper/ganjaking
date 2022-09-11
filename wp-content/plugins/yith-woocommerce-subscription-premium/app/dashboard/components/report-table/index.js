/** @format */
/**
 * External dependencies
 */
import {__} from '@wordpress/i18n';
import {Component} from '@wordpress/element';

/**
 * WooCommerce dependencies
 */
import {Card} from '@wordpress/components';
import {EmptyTable, TableCard} from '@woocommerce/components';
import React from 'react';
import {onQueryChange, updateQueryString} from '@woocommerce/navigation';

export class ReportTable extends Component {
	getFormattedHeaders() {

		const headers = this.props.headers.map((header, i) => {
			return {
				isLeftAligned: 0 === i,
				hiddenByDefault: false,
				isSortable: header?.isSortable ? header.isSortable : false,
				key: header?.key ? header.key : header.label,
				label: header.label
			};
		});

		return headers;
	}

	getFormattedRows() {
		const rows = this.props.rows.map(row => {
			return row.map(column => {
				return {
					display: <div dangerouslySetInnerHTML={{__html: column.display}}/>,
					value: column.value
				};
			});
		});
		return rows;
	}

	onSort = (key, direction) => {
		const {query} = this.props;
		direction = query.order === 'desc' ? 'asc' : 'desc';
		onQueryChange('sort')(key, direction);
	}

	render() {

		const {isRequesting, totalRows, title, rowsPerPage, query} = this.props;
		const rows = this.getFormattedRows();

		return (
			<TableCard className='woocommerce-report-table woocommerce-analytics__card'
				headers={this.getFormattedHeaders()}
				isLoading={isRequesting}
				rows={rows}
				rowsPerPage={parseInt(rowsPerPage)}
				showMenu={false}
				title={title}
				totalRows={parseInt(totalRows)}
				query={query}
				onPageChange={onQueryChange}
				onQueryChange={onQueryChange}
				onSort={(key, direction) => this.onSort(key, direction)}
			/>
		);
	}
}

ReportTable.defaultProps = {
	rows: [],
	isError: false,
	isRequesting: false
};

export default ReportTable;
