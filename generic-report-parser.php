<?php

class ArtsPeople_Report {
	public $parsed_file = array();

	public function __construct( $_filename ) {
		$this->parse( $_filename );		
	}

	private function parse( $_filename ) {
		$_fh = fopen( $_filename, 'r' );
	
		$_headers = array_flip( fgetcsv( $_fh ) );
		
		$_summary = array();
	
		do {
			$_data = fgetcsv( $_fh );
			$_new_line = array();
			foreach( $_headers as $_header_value => $_header_loc ) {
				$_new_line[ $_header_value ] = $_data[ $_header_loc ];
			}
			$this->parsed_file[] = $_new_line;
			
		} while ( !feof( $_fh ) );
	
		fclose( $_fh );
	}
}