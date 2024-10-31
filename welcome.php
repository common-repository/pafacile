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

function pageWelcomeVersionOutput($currentVersion, $minimalVersion ){
	$versionOk = (version_compare($minimalVersion, $currentVersion) != 1);
	echo $currentVersion;
	if($versionOk){
		?>
		<span class="ok">(Ok)</span>
		<?php
	} else{
		?>
		<span class="error">Non supportata</span>
		(min. ver. <?php echo $minimalVersion ?>)
		<?php
	}	
}
function pageWelcome(){
	?>
	<div class="wrap pafacile-welcome">
		<div id="icon-users" class="icon32">
		<br/>
		</div>
		
		<h2>PAFacile - Mai è stato così semplice gestire il sito di un EE.LL.</h2>
		<div id="poststuff" class="has-right-sidebar">
			<div class="inner-sidebar">
				<div class="right-column">
					<div>
						<img src="http://www.tosend.it/images/logo.gif"  alt="" />
					</div>
					<div class="postbox">
						<h3>Supporto tecnico</h3>
						<ul>
							<li>
								<strong>Versione applicativa:</strong>
								<?php echo TOSENDIT_PAFACILE_VERSION?>
							</li>
							<li>
								<strong>Versione del database:</strong>
								<?php echo TOSENDIT_PAFACILE_DB_VERSION?>
							</li>
						</ul>
					</div>
					
					<div class="postbox">
						<h3>Versioni librerie</h3>
						<?php 
							$phpver = phpversion();
							if(function_exists('gd_info')){
								$gdver = gd_info();
								$gdver = $gdver['GD Version'];
							}else{
								$gdver = 'Non disponibile';
							}
							if(function_exists('curl_version')){
								$curlver = curl_version();
								$curlver = $curlver['version'];
							}else{
								$curlver = 'Non disponibile';
							}
							
							$domver = (class_exists('DOMDocument')?'Installata':'Non installata');
						?>
						<ul>
							<li>
								<strong>PHP:</strong>
								<?php pageWelcomeVersionOutput($phpver, '5.2.0'); ?>
							</li>
							<li>
								<strong>GD Lib:</strong>
								<?php pageWelcomeVersionOutput($gdver, '2.0'); ?>
								<p class="help">
									Librerie necessarie per le funzioni di presentazione dell'albero 
									dell'organigramma. Se tali funzioni non sono utilizzate si può ignorare 
									l'eventuale mancato supporto
								</p>
							</li>
							<li>
								<strong>CURL Lib:</strong>
								<?php pageWelcomeVersionOutput($curlver, '7.0'); ?><br />
								<strong>DOM Lib:</strong>
								<?php echo $domver ?><br />
								<p class="help">
									Librerie necessarie per le tabelle di monitoraggio statistico. 
									Se tale funzione non è utilizzata si può ignorare l'eventuale mancato supporto.
								</p>
							</li>
						</ul>
					</div>
					<div class="postbox">
						<h3>Link utili</h3>
						<ul>
							<li><a href="http://www.pafacile.it">Acquista la versione premium!</a></li>
						</ul>
						<hr />
						<ul>
							<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FHL54KN64AHHU">Fai una donazione</a></li>
						</ul>
						<hr />
						<p>
							<a href="http://twitter.com/toSendIt/"><strong>Seguici su Twitter!</strong></a><br >
							<a href="http://tosend.it">Sito dell'autore del plugin</a><br >
							<a href="http://tosend.it/prodotti/pafacile/">Scheda informativa PAFacile</a><br >
							<a href="http://www.pafacile.it/area-clienti/documentazione/utente/pafacile-free/">Documentazione per l'utilizzatore finale e per gli sviluppatori</a><br >
							<a href="mailto:pafacile@tosend.it">Invia una richiesta di supporto</a><br />
						</p>
					</div>
					<div class="postbox">
						<h3>Testimonial</h3>
						<p>
							Vuoi diventare un testimonial di PAFacile? Vuoi segnalarci la tua soddisfazione nell'utilizzo
							del plugin? <a href="mailto:pafacile@tosend.it">Scrivici</a> e fallo sapere a tutti!
						</p>
					</div>
				</div>
			</div>
			<div class="left-column" id="post-body">
				<div  id="post-body-content">
				<div class="postbox">
					<h3>Cos'è?</h3>
					<p>
						<em>PAFacile</em> è un plugin sviluppato dalla <a href="http://toSend.it">toSend.it</a> per venire
						incontro alle esigenze della Pubblica Amministrazione e degli Enti Locali.
					</p>
					<p>
						<em>PAFacile</em> è un plugin nato per consentire alle pubbliche amministrazione di gestire la trasparenza amministrativa secondo gli obblighi di legge. Il plugin è l'unico in Italia a consentire l'adeguamento di un sito web di una pubblica amministrazione agli ultimi aggiornamenti normativa in materia di Albo Pretorio on-line, Bandi di Gara, Delbere e determinazioni, Ordinanze, Organigramma, Incarichi professionali, Sovvenzioni.
					</p>
					
					<h4>Vuoi gestire anche AVCP?</h4>
					
					<p>
						Se vuoi ottemperare agli obblighi di <strong>Trasparenza Amministrativa</strong> previsti dalla legge 
						del Decreto Legislativo 33/2013 in materia e agli obblighi di <strong>pubblicazione XML</strong> nei 
						confronti di AVCP previsti della Legge n.190/2012 <a href="http://www.pafacile.it/offerta-e-prezzi/">Acquista PAFacile Premium</a>.  
					</p>
					
				</div>
				<div class="postbox">
					<h3>Contribuisci alla crescita del plugin</h3>
					<p>
						<em>PAFacile</em> è un plugin sviluppato dalla <a href="http://toSend.it">toSend.it</a> investendo
						professionalità, tempo e risorse. Tuttavia è rilasciato con licenza GPLv3.
					</p>
					<p>
						Se ritieni utile questo plugin sarebbe gradita <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FHL54KN64AHHU">una donazione</a>.
						In alternativa ti chiediamo di <a href="http://wordpress.org/support/view/plugin-reviews/pafacile">recensirlo sulla pagina ufficiale di wordpress</a>
						e dare al plugin <a href="http://wordpress.org/support/view/plugin-reviews/pafacile?rate=5#postform">un buon punteggio</a>!
					</p>
					<p>
						Se hai bisogno di supporto all'installazione, alla configurazione o alla personalizzazione del plugin,
						siamo in grado di fornirti il miglior supporto. <a href="mailto:pafacile@tosend.it">Inviaci un'email</a> e ti forniremo un preventivo in tempi  
						brevi per risolvere il tuo problema.
					</p>
					
					<p>
						È <strong>grazie al tuo aiuto</strong> che riusciremo a garantire, anche in futuro, l'aderenza del Plugin agli aggiornamenti normativi!
					</p>
				</div>
				<div class="postbox">
					<h3>Cosa fa?</h3>
					<p>
						<em>PAFacile</em> consente di gestire diverse caratteristiche della PA
					</p>
					<ul>
						<li>Gestione dell'albo pretorio on-line</li>
						<li>Gestione delle tipologie di atto disponibili nell'albo pretorio</li>
						<li>Gestione bandi, gare, concorsi e graduatorie</li>
						<li>Gestione delibere di giunta e consiglio</li>
						<li>Gestione determine d'ufficio</li>
						<li>Gestione degli incarichi professionali</li>
						<li>Gestione ordinanze</li>
						<li>Gestione organigramma</li>
						<li>Gestione organi di governo</li>
						<li>Gestione delle tipologie di organi di governo disponibili</li>
						<li>Gestione di più incarichi governativi ricoperti dallo stesso soggetto</li>
						<li>Tabelle di monitoraggio statistiche</li>
						<li>Gestione concessione delle sovvenzioni, contributi, sussidi ed ausili finanziari (DL 22 giugno 2012, n. 83 art. 18)</li>
					</ul>
					
				</div>
				<div class="postbox" id="pafacile-changelog">
					<?php 
					$ver = isset($_GET['showVer'])?$_GET['showVer']:TOSENDIT_PAFACILE_VERSION;
					?>
					<h3>Le novità introdotte nella versione <?php echo $ver ?></h3>
					<p>
						Rilasci: <?php
						
						$changelog = file_get_contents(dirname(__FILE__). '/readme.txt');
						$allVersions = '#= (\d\.\d(\.\d)?) [^=]+=#i';
						preg_match_all($allVersions, $changelog, $versioni);
						
						$sezioneVersione = '#(^.*)(= '.preg_quote($ver,"#") .' [^=]+=.*$)#ism';
						if(!preg_match($sezioneVersione, $changelog)) $ver = TOSENDIT_PAFACILE_VERSION;
							
						$firstTime = true;
						sort($versioni[1]);
						foreach( $versioni[1] as $index => $verIndex){
							if(!$firstTime) echo", ";
							if($verIndex!=$ver){
								?>
								<a href="?page=<?php echo $_GET['page'] ?>&showVer=<?php echo $verIndex ?>"><?php echo $verIndex ?></a>
								<?php 
							}else{
								?>
								<strong><?php echo $verIndex ?></strong>
								<?php 
							}
							$firstTime = false;
						}
						?>
					</p>
					<?php 
					preg_match($sezioneVersione, $changelog, $sezioni);
					$changelog = $sezioni[2];

					$changelog = preg_replace('#= ' . preg_quote($ver,"#") . '[^=]+=(.*?)= \d+.\d+.*#is', '$1', $changelog);
					$changelog = preg_replace('#\*\*([^\*]+)\*\*#', '<strong>$1</strong>', $changelog);
					$changelog = preg_replace('#\*([^\n]*)#is', '<li> $1 </li>',$changelog);
					$changelog = preg_replace('#\n\n<li>#', '<li>', $changelog);
					$changelog = preg_replace('#\[([^\]]+)\]\(([^\)]+)\)#i', '<a onclick="window.open(this); return false;" href="\\2">\\1</a>', $changelog);
					
					echo("<ul>$changelog</ul>"); 
					?>
				</div>
				<div class="postbox">
				
				
				
					<h3>Le ultime novità su PAFacile</h3>
					
					<?php // Get RSS Feed(s)
					include_once(ABSPATH . WPINC . '/feed.php');
					
					// Get a SimplePie feed object from the specified feed source.
					$rss = fetch_feed('http://tosend.it/category/pafacile/feed/rss/');
					if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
					    // Figure out how many total items there are, but limit it to 5. 
					    $maxitems = $rss->get_item_quantity(5); 
					    // Build an array of all the items, starting with element 0 (first element).
					    $rss_items = $rss->get_items(0, $maxitems); 
					    
						?>
						
						<ul>
						    <?php 
						    // Loop through each feed item and display each item as a hyperlink.
						    // var_dump($rss_items);
						    foreach ( $rss_items as $item ) {
						    	 ?>
							    <li>
							        <a href='<?php echo esc_url( $item->get_permalink() ); ?>'>
							        <?php echo esc_html( $item->get_title() ); ?></a>
							    </li>
						    	<?php 
						    }
						    	
						    ?>
						</ul>
						<?php
					else:
						?>
						<p>
							Verificare la propria connessione ad internet, non sono in grado di comunicare con <a href="http://tosend.it">http://tosend.it</a>
						</p>
						<?php  
				    endif
				    ?>
				</div>
				
			</div>

		</div>
	</div>
	<?php 
}

pageWelcome();
?>