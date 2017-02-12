<?php

function summarise_sales_report( $filename, $date_format = 'Y-W' ) {
	$_fh = fopen( $filename, 'r' );

	$_headers = array_flip( fgetcsv( $_fh ) );
	
	$_summary = array();

	do {
		$_data = fgetcsv( $_fh );
		$_type = $_data[ $_headers[ 'Bought What' ] ];
		$_show = $_data[ $_headers[ 'Show Name' ] ];
		$_purchased_on = $_data[ $_headers[ 'Purchase Date' ] ];
		$_volume = $_data[ $_headers[ 'Ticket Count' ] ];

		$_purchase_week = date( $date_format, strtotime( $_purchased_on ) );

		if ( isset( $_summary[ $_show ][ $_purchase_week ] ) ) {
			$_summary[ $_show ][ $_purchase_week ] += $_volume;
		} else {
			$_summary[ $_show ][ $_purchase_week ] = $_volume;
		}
	} while ( !feof( $_fh ) );

	fclose( $_fh );

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
