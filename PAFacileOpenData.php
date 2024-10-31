<?php
/*
 * Since ver. 2.6.0
 */

add_action('wp', array('PAFacileOpenData', 'check') );

class PAFacileOpendata{
	
	public static function getAllowedFormats(){
		return apply_filters(
			'pafacile_opendata_allowed_format', 
			array(
				'csv' => array(__CLASS__, 'asCSV')
			)
		);
	}
	public static function getFormatTypes(){
		return apply_filters(
			'pafacile_opendata_mimetype_format', 
			array(
				'csv' => 'text/csv'
			)
		);
	}
	
	public static function makeLinkList($of){
		$plink = get_option('PAFacile_permalinks', array());
		$buffer = '';
		$of = strtolower($of);
		$linkListContainer 	= apply_filters("pafacile_opendata_link_list_container", "ul");
		$linkContainer 		= apply_filters("pafacile_opendata_link_container", 	 "li");
		
		if(isset($plink[$of]) && !empty($plink[$of])){
			
			$thePermalink = get_permalink( $plink[$of] );
			
			$formati = self::getAllowedFormats();
			$tipiFormato = array_keys($formati);
			$buffer = "<$linkListContainer class=\"pafacile-opendata-list-of-links\">";
			$theOpendataPermalink = $thePermalink;
			if(preg_match('#\?#', $theOpendataPermalink)){
				$theOpendataPermalink .='&opendata=';
			}else{
				$theOpendataPermalink .='?opendata=';
			}
			
			foreach ($tipiFormato as $tipoFormato){
				$buffer .= "<$linkContainer class=\"pafacile-opendata-$tipoFormato\">".
							"<a rel=\"opendata\" href=\"$theOpendataPermalink$tipoFormato\">$tipoFormato</a>".
							"</$linkContainer>";
			}
			$buffer .= "</$linkListContainer>";
		}
		return $buffer;
	}
	
	private static function getTableByKey($chiave){
		
		$tabelle = array(
			'bandi'			=> TOSENDIT_PAFACILE_DB_BANDI,
			'albopretorio'	=> TOSENDIT_PAFACILE_DB_ALBO_PRETORIO,
			'delibere'		=> TOSENDIT_PAFACILE_DB_DELIBERE,
			'determine'		=> TOSENDIT_PAFACILE_DB_DETERMINE,
			'incarichi'		=> TOSENDIT_PAFACILE_DB_INCARICHI,
			'ordinanze'		=> TOSENDIT_PAFACILE_DB_ORDINANZE,
			'organi'		=> TOSENDIT_PAFACILE_DB_ORGANI,
			'organigramma'	=> TOSENDIT_PAFACILE_DB_ORGANIGRAMMA,
			'sovvenzioni'	=> TOSENDIT_PAFACILE_DB_SOVVENZIONI
		);
		return (isset($tabelle[$chiave])) ? $tabelle[$chiave] : '';
		
	}
	
	private static function getFieldsByTable($table){
		$fields = array(
				'bandi'			=> '*',
				'albopretorio'	=> '*',
				'delibere'		=> '*',
				'determine'		=> '*',
				'incarichi'		=> '*',
				'ordinanze'		=> '*',
				'organi'		=> '*',
				'organigramma'	=> 'id, id_ufficio_padre, nome, email, pec, telefono, fax, indirizzo, descrizione, dirigente, responsabile, mostra_bandi, mostra_determinazioni',
				'sovvenzioni'	=> '*'
		);
		
		if(!isset($fields[$table])){
			# error_log("campi per $table non definiti");
		}
		
		return (isset($fields[$table])) ? $fields[$table] : '*';
	}
	
	private static function getFilterByTable($table){
		$filtro = array(
			'bandi'			=> '(data_pubblicazione<=now() and data_scadenza >= now()) or (data_pubblicazione<=now() and date_add( data_pubblicazione, interval 15 day) >= now())',
			'albopretorio'	=> 'pubblicata_dal <= now() and ( pubblicata_al>= now() or data_proroga >= now() ) and status in(' . TOSENDIT_PAFACILE_ATTO_PUBBLICATO .', '. TOSENDIT_PAFACILE_ATTO_PROROGATO .')',
			'delibere'		=> '(data_seduta<=now() and date_add( data_seduta, interval 15 day) >= now())',
			'determine'		=> '(data_adozione<=now() and date_add( data_adozione, interval 15 day) >= now())',
			'incarichi'		=> '(data_pubblicazione<=now() and date_add( data_pubblicazione, interval 15 day) >= now())',
			'ordinanze'		=> '(data_adozione<=now() and date_add( data_adozione, interval 15 day) >= now())',
			'organi'		=> "in_carica_dal<=now() and (in_carica_al is null or in_carica_al = '0000-00-00' or in_carica_al >=now() )",
			'organigramma'	=> "mostra_su_organigramma = 'y'",
			'sovvenzioni'	=> 'data_pubblicazione<=now() and date_add(data_pubblicazione, interval 15 day) >= now()'
		);
		return (isset($filtro[$table])) ? $filtro[$table] : '';
	}
	private static function getOrderByTable($table){
		$orderBy = array(
				'bandi'			=> '',
				'albopretorio'	=> '',
				'delibere'		=> '',
				'determine'		=> '',
				'incarichi'		=> '',
				'ordinanze'		=> '',
				'organi'		=> '',
				'organigramma'	=> 'ordine asc',
				'sovvenzioni'	=> ''
		);
		return (isset($orderBy[$table])) ? $orderBy[$table] : '';
	}
	public static function asCSV($tableName, $sql){
		global $wpdb;
		
		$results 		= $wpdb->get_results($sql, ARRAY_A);
		
		$colSeparator 	= apply_filters("pafacile_opendata_csv_column_separator", 		";");
		$rowSeparator 	= apply_filters("pafacile_opendata_csv_row_separator", 			"\r\n");
		$string 		= apply_filters("pafacile_opendata_csv_string_delimiter", 		"\"");
		$delimitAll		= apply_filters("pafacile_opendata_csv_use_delimiter_for_all", 	true);
		
		$buffer = "";
		
		if(count($results)>0){
			
			$columnKeys = array_keys($results[0]);
			$buffer = $string . implode("$string$colSeparator$string", $columnKeys) . $string;
			
		}
		foreach($results as $row){
			
			$buffer .= $rowSeparator;
			
			$rowBuffer = '';
			foreach($row as $colName => $col){
				
				$col = apply_filters('pafacile_opendata_csv_data', $col, $colName, $tableName);
				
				if($rowBuffer != ''){
					$rowBuffer .= $colSeparator; 
				}
				if($delimitAll || !is_numeric($col) || preg_match('#"#', $col)){
					$col = str_replace('"', '""', $col);
					$rowBuffer .= "$string$col$string";
				}else{
					$rowBuffer .=  $col;
				}
			}
			$buffer .= $rowBuffer;
		}
		
		$fileName = apply_filters('pafacile_opendata_csv_filename', "pafacile-opendata-$tableName.csv", $tableName);
		
		header("Pragma: public"); // required
	    header("Expires: 0");		// Non prendere il contenuto dalla cache
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");	// Non prendere il contenuto dalla cache
	    header("Cache-Control: private",false); 								// Richiesto per alcuni browser
	    header("Content-Type:  text/csv" );										// Il file è un csv
	    header("Content-Disposition: attachment; filename=\"$fileName\";" );
		
	    echo $buffer;
		
		exit();
		
	}
	
	public static function check(){
		#$permalink = get_permalink();
		
		/*
		 * Solo sulle pagine
		 */
		if(is_page() && isset($_GET['opendata'])){
			$opendataFormat = $_GET['opendata'];
			/*
			 * Attualmente prevedo come formato solo il CSV 
			 * 
			 */
			$allowedFormats = self::getAllowedFormats();
			/*
			 * Se non è un formato consentito non faccio nulla ed esco subito.
			 */
			if(array_search($opendataFormat, array_keys($allowedFormats) ) ===false ){
				
				return ;
			}
		
			$postID = get_the_ID();
			$plink = get_option('PAFacile_permalinks', array());
			$chiavi = array_keys($plink);
			$valori = array_values($plink);
			$indicePlink = array_search($postID, $valori);
			
			if($indicePlink!==false){
				
				global $wpdb;
				
				$chiave = $chiavi[$indicePlink];
				
				$tableName 			= self::getTableByKey($chiave);
				$originalTableName 	= $tableName;
				$tableName 			= $wpdb->prefix.$tableName;
				
				$campiDefault 		= self::getFieldsByTable($chiave);
				$filtroDefault		= self::getFilterByTable($chiave);
				$ordinamentoDefault = self::getOrderByTable($chiave);
				
				$campiDefault 		= apply_filters("pafacile_opendata_filtering_fields", $campiDefault, $tableName);
				$filtroDefault 		= apply_filters("pafacile_opendata_filtering_filter", $filtroDefault, $tableName);
				$ordinamentoDefault = apply_filters("pafacile_opendata_filtering_sort", $ordinamentoDefault, $tableName);
				
				$tableName 			= apply_filters("pafacile_opendata_filter_table", $tableName);
				
				if($filtroDefault != '') 		$filtroDefault = "where $filtroDefault";
				if($ordinamentoDefault != '') 	$ordinamentoDefault = "order by $ordinamentoDefault";
				
				$sql ="select $campiDefault from $tableName $filtroDefault $ordinamentoDefault";
				
				$function = $allowedFormats[$opendataFormat];
				call_user_func($function, $originalTableName, $sql);
			}
		}
	} 
	
}


?>