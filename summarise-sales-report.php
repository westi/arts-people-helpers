<?php

require_once( 'generic-report-parser.php' );

function summarise_sales_report( $filename, $date_format = 'Y-W' ) {

	$_file = new ArtsPeople_Report( $filename );


	foreach( $_file->parsed_file as $_entry ) {
		$_type = $_entry[ 'Bought What' ];
		$_show = $_entry[ 'Show Name' ];
		$_purchased_on = $_entry[ 'Purchase Date' ];
		$_volume = $_entry[ 'Ticket Count' ];

		$_purchase_date_formatted = date( $date_format, strtotime( $_purchased_on ) );

		if ( isset( $_summary[ $_show ][ $_purchase_date_formatted ] ) ) {
			$_summary[ $_show ][ $_purchase_date_formatted ] += $_volume;
		} else {
			$_summary[ $_show ][ $_purchase_date_formatted ] = $_volume;
		}
	}

	foreach( $_summary as $_show => &$_weeks ) {
		ksort( $_weeks );
	}

	return $_summary;
}

function graph_sales_for_show( $show_sales, $sales_per_char = 20 ) {

	foreach( $show_sales as $_show => $_sales ) {
		echo "\n{$_show}\n";
		$_total = 0;
		foreach( $_sales as $_week => $_sold ) {
			echo "{$_week}\t{$_sold}\t" . str_repeat( '#', floor( $_sold / $sales_per_char ) ) . "\n";
			$_total += $_sold;
		}
		echo "Total\t{$_total}\n\n";
	}
}

$__summary = summarise_sales_report( realpath( 'Sales_Listing-_Online_-_Box_Office.csv' ), 'Y-W' );
graph_sales_for_show( $__summary );


$__summary = summarise_sales_report( realpath( 'Sales_Listing-_Online_-_Box_Office.csv' ), 'Y-m-d' );
graph_sales_for_show( $__summary, 5 );
