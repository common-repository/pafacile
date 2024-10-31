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

require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';
class Sovvenzioni extends PAFacilePublicBaseClass implements iContents {
	
	public static function dettagli($id){
		
		global $wpdb;
		$itemId = (isset($_GET['itemId']) && is_numeric($_GET['itemId']))?$_GET['itemId']:0;
		
		$tableName = $wpdb->prefix. TOSENDIT_PAFACILE_DB_SOVVENZIONI;
		$sql = "select * from $tableName where id = %d";
		$sql = $wpdb->prepare($sql, $itemId);
		
		$row = $wpdb->get_row($sql);
		
		if(is_null($row)) {
		#	echo self::elenco();
			return false;
		}
		
		$permalinks = get_option('PAFacile_permalinks');
		
		$urlUfficio = get_permalink($permalinks['organigramma_id']);
		$urlUfficio.='?oid=';
		
		
		$pairs = array(
			# Label 				|  Value
			'Data sovvenzione' 		=> toSendItGenericMethods::formatDateTime($row->data_pubblicazione),
			# 'Ragione Sociale'		=> $row->ragione_sociale,
			'Partita IVA'			=> $row->partita_iva,
			'Codice Fiscale'		=> $row->codice_fiscale,
			'Indirizzo'				=> $row->indirizzo,
			'CAP'					=> $row->cap,
			'Città'					=> $row->citta,
			'Provincia'				=> $row->provincia,
			'Importo'				=> $row->importo,
			'Norma di riferimento'	=> $row->norma,
			'Ufficio'				=> '<a href="'.$urlUfficio . $row->id_ufficio.'">' .PAFacileDecodifiche::officeNameById($row->id_ufficio) .'</a>',
			'Dirigente'				=> $row->dirigente
		);
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		$buffer = '';
		$buffer .=	"<h$subLevel>";
		$buffer .=		apply_filters('pafacile_sovvenzioni_detail_title', $row->ragione_sociale, $row);
		$buffer .=	"</h$subLevel>";
		
		$pairs = self::purgeKeyArray($pairs);
		$pairs = apply_filters('pafacile_sovvenzioni_pairs', $pairs, $row);
		$buffer .= self::buildPairValueList($pairs, 'sovvenzioni', false);
		
		$buffer .= wpautop(wptexturize( $row->modo_individuazione) );
		
		echo $buffer;
		/*
		 * Since Ver. 2.5.10
		 * Assenza del box di upload
		 */
		toSendItGenericMethods::displayFileUploadBox($tableName, $itemId);
		
		return true;
	}
	
	public static function elenco($params = null){
		
		
		toSendItPAFacile::displayContentTable('sovvenzioni', '', '', 
				array(				// Columns
					'ragione_sociale' 		=> 'Nome impresa/soggetto beneficiario',
					'partita_iva'			=> 'P.IVA',
					'codice_fiscale'		=> 'Cod.Fisc.',
					'importo'				=> 'Importo',
					'data_pubblicazione'	=> 'Pubblicato il',
				), 
				array(				// Filter
					'anno' 	=> "year(data_pubblicazione) = %d",
					/* 'piva' 	=> "partita_iva like '%%%s%%'",
					'cf'	=> "codice_fiscale like '%%%s%%'",
					'oid'	=> "id_ufficio = %d" */
				), 
				array(), 
				TOSENDIT_PAFACILE_DB_SOVVENZIONI, '', '', '', '');
	}
	
	public static function form($params = null){
		$anno = isset($_GET['anno'])?esc_attr($_GET['anno']):'';
		?>
		<form method="get" action="">
			<p>
				<label for="anno">Anno di riferimento:</label>
				<input type="text" name="anno" id="anno" value="<?php echo $anno ?>" />
				<input type="submit" value="Cerca" />
			</p>
		</form>
		<?php
	} 
	
	public static function mostra($buffer){
		$itemId = isset($_GET['itemId'])?$_GET['itemId']:'';
		if(isset($itemId) && is_numeric($itemId)){
				
			ob_start();
			// Mostro il dettaglio di un atto pubblicato nell'albo
			if(!self::dettagli($itemId)){
				unset($_GET['itemId']);
				echo($buffer);
			}
			$buffer = ob_get_clean();
				
		}
		
		return $buffer;	
	}
	
}