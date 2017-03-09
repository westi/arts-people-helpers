<?php

require_once( 'generic-report-parser.php' );

function summarise_donation_report( $filename, $date_format = 'Y-W' ) {

	$_file = new ArtsPeople_Report( $filename );

	foreach( $_file->parsed_file as $_entry ) {
		$_type = $_entry[ 'Fund Name' ];
		$_purchased_on = $_entry[ 'Date Time' ];
		$_value = $_entry[ 'Amount' ];

		$_purchase_date_formatted = date( $date_format, strtotime( $_purchased_on ) );
		$_purchase_year = date( 'Y', strtotime( $_purchased_on ) );

		if ( isset( $_summary[ "$_type - $_purchase_year" ][ $_purchase_date_formatted ] ) ) {
			$_summary[ "$_type - $_purchase_year" ][ $_purchase_date_formatted ] += $_value;
		} else {
			$_summary[ "$_type - $_purchase_year" ][ $_purchase_date_formatted ] = $_value;
		}
	}

	foreach( $_summary as $_show => &$_weeks ) {
		ksort( $_weeks );
	}

	ksort( $_summary );
	return $_summary;
}

function graph_donations_for_funds( $_data, $dollars_per_char = 20 ) {

	foreach( $_data as $_fund => $_sales ) {
		echo "\n{$_fund}\n";
		$_total = 0;
		foreach( $_sales as $_week => $_sold ) {
			echo "{$_week}\t{$_sold}\t" . str_repeat( '#', floor( $_sold / $dollars_per_char ) ) . "\n";
			$_total += $_sold;
		}
		echo "Total\t{$_total}\n\n";
	}
}


$__summary = summarise_donation_report( realpath( 'Donation_Listing.csv' ), 'Y-m-d' );
graph_donations_for_funds( $__summary );
