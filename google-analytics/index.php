<?php
/** 
 * 		Adattamento dello script: GAcounter 0.8b - 03 maggio 2009
 * 		based on Electrictoolbox.com php class ( http://www.electrictoolbox.com/php-class-google-analytics-api/ )
 * 		developed by Marco Cilia ( http://www.goanalytics.info )
 * 		modificato da Mario Varini 07/11/2011
 * 		adattato per wordpress e semplificato da toSend.it (http://tosend.it) 29 Novembre 2011
 */
require_once(PAFACILE_PLUING_DIRECTORY .'/google-analytics/api.php');

class PAFacileGoogleAnalytics{
	private $adminPanel = false;
	public function __construct($admin = false){
		$this->adminPanel = $admin; 
	}
	public function getCount($days){

		if(!is_numeric($days) ) $days = 0;
		$oggi = toSendItGenericMethods::formatDateTime( date("Y-m-d") );
		$startDate = toSendItGenericMethods::formatDateTime( date("Y-m-d", time()-(86400*$days)) );
		
		$ga = get_option('PAFacile_GoogleAnalytics', array('username' => '', 'password' => ''));
		
		$login = $ga['username'];
		$password = $ga['password'];
		$site = $_SERVER['HTTP_HOST'];
		
		$api = new analytics_api();
		if($api->login($login, $password)){
			
		    $api->load_accounts();
		    if(!isset($api->accounts[$site])){
		    	if($this->adminPanel){
			    	?>
					<p class="error">Non è disponibile un profilo per il sito <strong><?php echo $site ?></strong></p>
					<?php
		    	}
		    }else{
		    	try{
				    $id = $api->accounts[$site]['tableId'];
				    $data = $api->data($id, '', 'ga:visits,ga:visitors,ga:pageviews,ga:newVisits,ga:timeOnSite,ga:avgTimeOnSite,ga:pageviewsPerVisit,ga:uniquePageviews',false, $startdate, date("Y-m-d"), 1000);

				    # 24 ore
				    $secondiTotaliMedi=$data["ga:avgTimeOnSite"]/60/60/24;
				    $giorni=(int)$secondiTotaliMedi;
				    
				    $avanzoGiorno=$secondiTotaliMedi-$giorni;
				    $oreMedie=$avanzoGiorno*24;
				    $ore=(int)$oreMedie;
				    
				    $avanzoOre=$oreMedie-$ore;
				    
				    $minutiMedi=$avanzoOre*60;
				    $minuti=(int)$minutiMedi;
				    $avanzoMinuti=$minutiMedi-$minuti;
				    
				    $secondi=$avanzoMinuti*60;
				    $secondi=(int)$secondi;
				    
			    	$visiteTotali 			= number_format($data["ga:visits"],2,",",".");
			    	$visitatoriTotali 		= number_format($data["ga:visitors"],2,",",".");
			    	$nuoviVisitatori		= number_format($data["ga:newVisits"],2,",",".");
			    	$pagineViste 			= number_format($data["ga:pageviews"],2,",",".");
			    	$pagineVistePerVisita	= number_format(round($data["ga:pageviewsPerVisit"],2),2,",",".");
			    	$pagineUniche			= number_format($data["ga:uniquePageviews"],2,",",".");
			    	
			    	$tempoMedio = '';
			    	if($giorni>0) 	$tempoMedio .= "$giorni <abbr title=\"giorni\">g</abbr> ";
			    	if($ore>0) 		$tempoMedio .= "$ore <abbr title=\"ore\">h</abbr> ";
			    	if($minuti>0) 	$tempoMedio .= "$minuti <abbr title=\"minuti\">min</abbr> ";
			    	if($secondi>0) 	$tempoMedio .= "$secondi <abbr title=\"secondi\">sec</abbr>";
			    	
			        $tabella = <<<EOT
			        <table class="pafacile-ga">
			            <caption>Dati rilevati da Google Analitycs (dal $startDate al $oggi)</caption>        
			            <tbody>
			                <tr><th scope="row">Visite totali</th><td>$visiteTotali</td></tr>
			                <tr><th scope="row">Visitatori totali</th><td>$visitatoriTotali</td></tr>
			                <tr><th scope="row">Nuovi visitatori</th><td>$nuoviVisitatori</td></tr>
			                <tr><th scope="row">Pagine viste</th><td>$pagineViste</td></tr>
			                <tr><th scope="row">Pagine viste per visita</th><td>$pagineVistePerVisita</td></tr>
			                <tr><th scope="row">Pagine uniche </th><td>$pagineUniche</td></tr>
			                <tr><th scope="row">Tempo medio di permanenza sul sito </th><td>$tempoMedio</td></tr>
			            </tbody>
			        </table>
EOT;
					return $tabella;
				}catch(Exception $e){
					if($this->adminPanel){
						?>
						<p class="error">Si è verificato un errore imprevisto! Il messaggio di errore è il seguente: "<?php echo $e->getMessage() ?>"</p>
						<?php 
					}
					return "";
				}
		    }
		}else{
			if($this->adminPanel){
				?>
				<p class="error">Dati di autenticazione non validi</p>
				<?php	
			}else{
				return '<!-- dati di autenticazione GA non corretti -->';
			}
		}
		
	}
	
	
}