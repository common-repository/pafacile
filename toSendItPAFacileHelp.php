<?php
class toSendItPAFacileHelp{
	static function contextualHelp($buffer){
		
		if(PAFacileBackend::isPAFacilePage()){
			$buffer = 'Consulta la <a href="http://tosend.it/prodotti/pafacile/documentazione/">documentazione on-line di PAFacile</a>.'; 
		}
		
		return $buffer;
	} 
}

# add_action('contextual_help', array('toSendItPAFacileHelp','contextualHelp'));
?>