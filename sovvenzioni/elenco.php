<?php
/*
 * Since Version 2.5.10
* Avoid XSS vulnerability discovered by Dejan Lukan many thanks!
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) &&
		basename(__FILE__)             == basename($_SERVER['SCRIPT_FILENAME']) &&            // Same script file
		basename(dirname(__FILE__)) == basename(dirname($_SERVER['SCRIPT_FILENAME']))    // Same directory
)
	die ('Please do not load this page directly. Thanks to Dejan Lukan for the notification!');

function displaySovvenzioniPublic($params, $extraParams = array()){
	global $wpdb;
	$params = toSendItGenericMethods::identifyParameters($params,
			array(
					'kind'			=> 'table', 		// Può essere table (elenco dei bandi) oppure box (il form di ricerca)
					'anno'			=> '',				// Può essere uno dei tipi disponibili
			)
	);
	$params = array_merge ($params, $extraParams);

	switch($params['kind']){
		case 'box':
			Sovvenzioni::form($params);
			break;
		case 'table':
		default:
			Sovvenzioni::elenco($params);
			break;
	}
}

function setSearchFormSovvenzioni(){
	
	?>
	<p>
		<label for="rag-soc">Nome impresa/soggetto beneficiario:</label>
		<input type="text" name="rs" id="rag-soc" 
			value="<?php echo esc_attr(isset($_GET['rs'])?$_GET['rs']:''); ?>" />
		
		<label for="rag-soc">Codice fiscale:</label>
		<input type="text" name="cf" id="cod-fisc" 
			value="<?php echo esc_attr(isset($_GET['cf'])?$_GET['cf']:''); ?>" />
			
		<label for="rag-soc">Partita IVA:</label>
		<input type="text" name="piva" id="piva" 
			value="<?php echo esc_attr(isset($_GET['piva'])?$_GET['piva']:''); ?>" />
	</p>
	<p>
		<?php
		toSendItPAFacile::buildOfficeSelector('oid','oid','',isset($_GET['oid'])?$_GET['oid']:'',false);
		?>
	</p>
	<span class="cboth" >&nbsp;</span>
	<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
	<span class="cboth" >&nbsp;</span>
	
	<?php
}

function displaySovvenzioni(){
	
	add_action('pafacile_sovvenzioni_admin_filter_form', 'setSearchFormSovvenzioni');
	
	toSendItPAFacile::displayContentTable(
		'sovvenzioni', 		// type
		
		'Sovvenzioni, Agevolazioni, contributi e sussidi', // Title
			
		'ragione_sociale',  // description Column Key
		
		array(				// Columns
			'ragione_sociale' 		=> 'Nome impresa/soggetto beneficiario',
			'partita_iva'			=> 'P.IVA',
			'codice_fiscale'		=> 'Cod.Fisc.',
			'importo'				=> 'Importo',
			'data_pubblicazione'	=> 'Assegnato il',
		), 
			
		array(				// Filter
			'rs' 	=> "ragione_sociale like '%%%s%%'",
			'piva' 	=> "partita_iva like '%%%s%%'",
			'cf'	=> "codice_fiscale like '%%%s%%'",
			'oid'	=> "id_ufficio = %d"
		), 
			
		array(				// Classes
		
			'ragione_sociale' 		=> 'wide-text',
			'partita_iva'			=> 'wide-10-text',
			'codice_fiscale'		=> 'wide-10-text',
			'importo'				=> 'wide-10-text',
			'data_pubblicazione'	=> 'wide-20-text',
		),
		
		TOSENDIT_PAFACILE_DB_SOVVENZIONI,		// Table Name 
		TOSENDIT_PAFACILE_ROLE_SOVVENZIONI, 	// Edit Role
		TOSENDIT_PAFACILE_ROLE_SOVVENZIONI, 	// Delete Role
		TOSENDIT_PAFACILE_SOVVENZIONI_EDIT_HANDLER, 	// Edit handler
		TOSENDIT_PAFACILE_SOVVENZIONI_DELETE_HANDLER	// Delete handler
	);	
}



if(is_admin()) {
	$action=toSendItGenericMethods::getActionFromPage($baseAction);
	if($action == TOSENDIT_PAFACILE_EDIT){
		isset($_GET['id']) && displayDettaglioSovvenzioni();
		!isset($_GET['id']) && displaySovvenzioni();
	}
}
?>