<?php
/* Simple attempt at classifying audience both by consistent attendence and repeating attendence */

require_once( 'generic-report-parser.php' );

function analyse_sales_report( $filename, $_shows ) {

	$_file = new ArtsPeople_Report( $filename );

	$_audience = array();
	$_shows_to_years = array_flip( $_shows );

	foreach( $_file->parsed_file as $_entry ) {
		$_show = $_entry[ 'Show Name' ];
		if ( ! isset( $_shows_to_years[ $_show ] ) ){
			continue;
		}
		$_perfomance = $_entry[ 'Ticket Date' ];
		$_purchased_on = $_entry[ 'Purchase Date' ];
		$_volume = $_entry[ 'Ticket Count' ];
		$_first_name = $_entry['First Name'];
		$_last_name = $_entry['Last Name'];

		$_whole_name = "$_first_name $_last_name";

		if ( isset( $_audience[$_whole_name][$_shows_to_years[ $_show ]] ) ) {
			$_audience[$_whole_name][$_shows_to_years[ $_show ]] += $_volume;
		} else {
			$_audience[$_whole_name][$_shows_to_years[ $_show ]] = $_volume;
		}
	}

	$_repeat_tenure = array();
	$_alltime_tenure = array();
	foreach ( $_audience as $_name => $_seen_shows ) {
		foreach( $_shows_to_years as $_maybe_seen_show ) {
			$_audience_tenure = calculate_sequential_tenure( $_seen_shows, $_maybe_seen_show, $_shows_to_years );
			if ( 0 !== $_audience_tenure ) {
				if ( ! isset( $_repeat_tenure[$_maybe_seen_show][ $_audience_tenure ] ) ) {
					$_repeat_tenure[$_maybe_seen_show][ $_audience_tenure ] = $_seen_shows[$_maybe_seen_show];
				} else {
					$_repeat_tenure[$_maybe_seen_show][ $_audience_tenure ] += $_seen_shows[$_maybe_seen_show];
				}
			}
			$_audiences = calculate_shows_seen( $_seen_shows, $_maybe_seen_show, $_shows_to_years );
			if ( 0 !== $_audiences ) {
				if ( ! isset( $_alltime_tenure[$_maybe_seen_show][ $_audiences ] ) ) {
					$_alltime_tenure[$_maybe_seen_show][ $_audiences ] = $_seen_shows[$_maybe_seen_show];
				} else {
					$_alltime_tenure[$_maybe_seen_show][ $_audiences ] += $_seen_shows[$_maybe_seen_show];
				}
			}
		}
	}

	echo "Consistent Repeat Customers\n\n";

	ksort( $_repeat_tenure );
	foreach ( $_repeat_tenure as $_show => $_tenure_map ){
		ksort( $_tenure_map );
		echo "{$_shows[$_show]}," . implode( ',', $_tenure_map) . "\n"; 
	}


	echo "All time shows\n\n";

	ksort( $_alltime_tenure );
	foreach ( $_alltime_tenure as $_show => $_tenure_map ){
		ksort( $_tenure_map );
		echo "{$_shows[$_show]}," . implode( ',', $_tenure_map) . "\n"; 
	}

}

function calculate_sequential_tenure( $_data, $_for_show, $_shows ) {
	if ( ! isset( $_data[ $_for_show ] ) ){
		// Not seen this show
		return 0;
	} else {
		// Did they see any prior shows?
		$_tenure = 0;
		foreach( $_shows as $_show_they_might_have_seen ) {
			if ( $_show_they_might_have_seen < $_for_show ) {
				// Earlier than the show we care about
				if ( isset( $_data[$_show_they_might_have_seen] ) ) {
					$_tenure++;
				} else {
					$_tenure = 0;
				}
			} elseif ( $_show_they_might_have_seen === $_for_show ){
				$_tenure++;
			}
		}
		return $_tenure;
	}
}

function calculate_shows_seen( $_data, $_for_show, $_shows ) {
	if ( ! isset( $_data[ $_for_show ] ) ){
		// Not seen this show
		return 0;
	} else {
		// Did they see any prior shows?
		$_tenure = 0;
		foreach( $_shows as $_show_they_might_have_seen ) {
			if ( $_show_they_might_have_seen < $_for_show ) {
				// Earlier than the show we care about
				if ( isset( $_data[$_show_they_might_have_seen] ) ) {
					$_tenure++;
				}
			} elseif ( $_show_they_might_have_seen === $_for_show ){
				$_tenure++;
			}
		}
		return $_tenure;
	}
}


analyse_sales_report(
	realpath( 'Sales_Listing-_Online_-_Box_Office.csv.alltime' ),
	array(
		2011 => 'Death of a Star', // 2011
		2012 => 'A Night at Nicks', // 2012
		2013 => 'Spirit and Soul The Tour', // 2013
		2014 => 'Marry You', // 2014
		2015 => 'A Dark & Stormy Night', // 2015
		2016 => 'Its All Greek To Me', // 2016
		2017 => 'Step Right Up', // 2017
	)
);