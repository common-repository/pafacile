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

require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/Determine.php';
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/Delibere.php';
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/AlboPretorio.php';
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/BandiGare.php';
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/Organi.php';
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/Ordinanze.php';
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/Incarichi.php';
# Since Ver 2.5
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/Sovvenzioni.php';

class toSendItPAFacileContents{
	
	static public function mostraDelibere($buffer){ 				return Delibere::mostra($buffer); 		}
	
	static public function mostraDetermine($buffer){ 				return Determine::mostra($buffer);		}
	static public function mostraDetermineForm($params = null){		return Determine::form($params);		}
	static public function mostraDetermineElenco($params =null){ 	return Determine::elenco($params);		}
	static public function mostraDetermineDettagli($id){			return Determine::dettagli($id);		}

	static public function mostraIncarichi($buffer){				return Incarichi::mostra($buffer);		}
	static public function mostraIncarichiDettagli($id){			return Incarichi::dettagli($id);		}
	static public function mostraIncarichiForm($params = null){		return Incarichi::form($params);		}
	static public function mostraIncarichiElenco($params = null){	return Incarichi::elenco($params);		}
	
	static public function mostraOrgani($buffer){					return Organi::mostra($buffer);			}
	static public function mostraOrganiDettaglio($itemId){			return Organi::dettagli($itemId);		}
	static public function mostraOrganiForm($params = null){		return Organi::form($params);			}
	static public function mostraOrganiElenco($params = null){		return Organi::elenco($params);			}
	
	static public function mostraOrdinanze($buffer){				return Ordinanze::mostra($buffer);		}
	static public function mostraOrdinanzeDettagli($id){			return Ordinanze::dettagli($id);		}
	static public function mostraOrdinanzeForm($params = null){		return Ordinanze::form($params);		}
	static public function mostraOrdinanzeElenco($params = null){	return Ordinanze::elenco($params);		}
	
	static function mostraOrganigramma(){
		require_once 'organigramma/elenco.php';
		ob_start();
		displayOrganigrammaPublic(0);
		$buffer = ob_get_clean();
		return $buffer;
	}
	
	/* -------- */ 
	
	
	public static function PAFacileConfigurationError(){
		?>
		<h3>Impossibile caricare il contenuto della pagina</h3>
		<p>
			Verificare di aver correttamente configurato i permalinks.
		</p>
		<?php 
	}
}
?>