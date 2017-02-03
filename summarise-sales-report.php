<?php

function summarise_sales_report( $filename ) {
	$_fh = fopen( $filename, 'r' );

	$_headers = array_flip( fgetcsv( $_fh ) );
	
	$_summary = array();

	do {
		$_data = fgetcsv( $_fh );
		$_type = $_data[ $_headers[ 'Bought What' ] ];
		$_show = $_data[ $_headers[ 'Show Name' ] ];
		$_purchased_on = $_data[ $_headers[ 'Purchase Date' ] ];
		$_volume = $_data[ $_headers[ 'Ticket Count' ] ];

		$_purchase_week = date( 'Y-W', strtotime( $_purchased_on ) );

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

function graph_sales_for_show( $show_sales ) {
	$_sales_per_char = 5;

	foreach( $show_sales as $_show => $_sales ) {
		echo "\n{$_show}\n";
		foreach( $_sales as $_week => $_sold ) {
			echo "{$_week}\t{$_sold}\t" . str_repeat( '#', floor( $_sold / $_sales_per_char ) ) . "\n";
		}
	}
}

$__summary = summarise_sales_report( realpath( 'Sales_Listing-_Online_-_Box_Office.csv' ) );
graph_sales_for_show( $__summary );
