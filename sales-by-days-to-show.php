<?php

require_once( 'generic-report-parser.php' );

function summarise_sales_report( $filename, $first_show_dates ) {

	$_file = new ArtsPeople_Report( $filename );


	foreach( $_file->parsed_file as $_entry ) {
		$_type = $_entry[ 'Bought What' ];
		$_show = $_entry[ 'Show Name' ];
		$_purchased_on = $_entry[ 'Purchase Date' ];
		$_volume = $_entry[ 'Ticket Count' ];

		if ( isset( $first_show_dates[ $_show ] ) ) {
			$_elapsed_days_seconds = strtotime( $first_show_dates[ $_show ] ) - strtotime( date( 'Y-m-d', strtotime( $_purchased_on ) ) );

			$_days = $_elapsed_days_seconds / 60 / 60 / 24;
	
			if ( isset( $_summary[ $_show ][ $_days ] ) ) {
				$_summary[ $_show ][ $_days ] += $_volume;
			} else {
				$_summary[ $_show ][ $_days ] = $_volume;
			}
		}
	}

	foreach( $_summary as $_show => &$_weeks ) {
		krsort( $_weeks );
	}

	return $_summary;
}

function graph_sales_for_show( $show_sales, $sales_per_char = 25 ) {

	foreach( $show_sales as $_show => $_sales ) {
		echo "\n{$_show}\n";
		echo "\nDays to first show\tSales so far\n";
		$_total = 0;
		foreach( $_sales as $_week => $_sold ) {
			$_total += $_sold;
			echo "{$_week}\t\t\t{$_total}\t" . str_repeat( '#', floor( $_total / $sales_per_char ) ) . "\n";
		}
		echo "Total\t{$_total}\n\n";
	}
}

function csv_sales_for_shows( $show_sales, $shows ) {
	$_max_days = 0;
	foreach( $show_sales as $_show => $_sales ) {
		$_max_days = max( $_max_days, key( $_sales ) );
	}

	$_stdout = fopen("php://output", "w");
	fputcsv( $_stdout, array_merge( array( 'days' ), array_keys( $shows) ) );
	foreach( range( $_max_days, -3 ) as $_day ) {
		$_row = array();
		$_row[] = $_day;
		foreach( $shows as $_show => $_ignore ) {
			if ( isset( $show_sales[ $_show ][ $_day ] ) ) {
				$_row[] = $show_sales[ $_show ][ $_day ];
			} else {
				$_row[] = 0;
			}
		}
		fputcsv( $_stdout, $_row );
	}

}

$__summary = summarise_sales_report(
	realpath( 'Sales_Listing-_Online_-_Box_Office.csv.alltime' ),
	array(
		'A Dark & Stormy Night' => '2015-03-26',
		'Its All Greek To Me' => '2016-03-31',
		'Step Right Up' => '2017-03-23',
	)
);
graph_sales_for_show( $__summary );
csv_sales_for_shows(
	$__summary,
	array(
		'A Dark & Stormy Night' => '2015-03-26',
		'Its All Greek To Me' => '2016-03-31',
		'Step Right Up' => '2017-03-23',
	)
);

$__summary = summarise_sales_report(
	realpath( 'Sales_Listing-_Online_-_Box_Office.csv' ),
	array(
		'Step Right Up' => '2017-03-23',
	)
);
graph_sales_for_show( $__summary );
csv_sales_for_shows(
	$__summary,
	array(
		'A Dark & Stormy Night' => '2015-03-26',
		'Its All Greek To Me' => '2016-03-31',
		'Step Right Up' => '2017-03-23',
	)
);
