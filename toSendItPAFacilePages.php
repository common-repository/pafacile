<?php
class PAFacilePages{
	function pageWelcome(){ require_once PAFACILE_PLUING_DIRECTORY .'/welcome.php'; }

	public static function pageGenericHandler($tableName, $baseAction, $baseIncludeDirectory){
		$action=toSendItGenericMethods::getActionFromPage($baseAction);
		
		# error_log("$tableName = $action");
		
		switch($action){
			case TOSENDIT_PAFACILE_EDIT:
				if(!isset($_GET['id'])){
					require($baseIncludeDirectory.'/elenco.php');
					break;
				}
			case TOSENDIT_PAFACILE_NEW:
			
				require($baseIncludeDirectory.'/dettaglio.php');
				break;
			case 'delete':
				global $wpdb;
				$tableName = $wpdb->prefix . $tableName;
				$wpdb->query('delete from ' . $tableName . ' where id=' . $_GET['id']);
				/*
				 * Since Ver. 2.6.0
				 * Reset dei valori predefiniti
				 */
				$baseAction = $baseAction . TOSENDIT_PAFACILE_EDIT;
				global $_GET;
				/*
				 * Aggiornamento delle impostazioni in get
				 */
				unset($_GET['id']);
				unset($_GET['action']);
				$_GET['page'] = $baseAction . TOSENDIT_PAFACILE_EDIT;
			default:
				require($baseIncludeDirectory.'/elenco.php');
		}
		
	}
	
	static function pagePAOrgani() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_ORGANI, TOSENDIT_PAFACILE_ORGANI_HANDLER,'organi' );
	}

	static function pagePABandi() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_BANDI, TOSENDIT_PAFACILE_BANDI_HANDLER,'bandi' );
	}
	

	static function pagePAAlboPretorio() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_ALBO_PRETORIO, TOSENDIT_PAFACILE_ALBO_PRETORIO_HANDLER,'alboPretorio' );
	}
	// Since Ver. 1.5.6
	static function pagePATipoAtto() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_TIPO_ATTO, TOSENDIT_PAFACILE_TIPO_ATTO_HANDLER,'tipiAtto' );
	}
	
	static function pagePATipoOrgano() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_TIPO_ORGANO, TOSENDIT_PAFACILE_TIPO_ORGANO_HANDLER,'tipiOrgani' );
	}
		
	// Since Ver. 1.4.2
	static function pagePAAlboRegistro() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_ALBO_PRETORIO, TOSENDIT_PAFACILE_ALBO_PRETORIO_HANDLER,'alboPretorio' );
	}

	static function pagePAIncarichiProfessionali() {
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_INCARICHI, TOSENDIT_PAFACILE_INCARICHI_PROF_HANDLER,'incarichiProfessionali' );
	}
	
	
	static function pagePAOrganigramma(){
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_ORGANIGRAMMA, TOSENDIT_PAFACILE_ORGANIGRAMMA_HANDLER,'organigramma' );
	}
	
	static function pagePADelibere(){
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_DELIBERE, TOSENDIT_PAFACILE_DELIBERE_HANDLER,'delibere' );
	}

	static function pagePADetermine(){
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_DETERMINE, TOSENDIT_PAFACILE_DETERMINE_HANDLER,'determine' );
	}
	
	static function pagePAOrdinanze(){
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_ORDINANZE, TOSENDIT_PAFACILE_ORDINANZE_HANDLER,'ordinanze' );
	}
	
	static function pagePASovvenzioni(){
		# error_log('Sono sulle sovvenzioni!');
		PAFacilePages::pageGenericHandler(TOSENDIT_PAFACILE_DB_SOVVENZIONI, TOSENDIT_PAFACILE_SOVVENZIONI_HANDLER,'sovvenzioni' );
	}
	
	/**
	 * @deprecated
	 */
	private static function settingsSavePageInfo($title, $guid, $postId =0){
		/*
		#print_r(func_get_args());
		$my_post = array();
		
		$my_post['post_status'] = 'publish';
		$my_post['post_date'] = date('Y-m-d H:i:s');  
		$my_post['post_type'] = 'page';
		$my_post['comment_status'] = 'closed';
		$my_post['ping_status'] = 'closed';
		
		$my_post['post_content'] = 'Questa pagina è stata creata in automatico da PAFacile.';
		$status = null;
		!isset($postId) && $postId = 0;
		$id 	= $postId;
		$err 	= null;
		
		if($postId!=0){
			$arr = get_post($postId);
			if($arr==null){
				$postId = 0;
				$id = 0;
				
			}
			
		}
		if($guid!='' && $postId==0){
			$my_post['post_title'] = $title;
			$my_post['post_name'] = $guid;
			$id = wp_insert_post( $my_post, $err);
			
			$status = 1;
		}elseif($postId!=0){
			
			if($guid == ''){
				// Devo cancellare la pagina
				wp_delete_post($postId);
				$id 	= 0;
				$status = 3;
			}else{
				// Devo aggiornarne il contenuto
				$my_post['ID'] = $postId;
				$my_post['post_name'] = $guid;
				unset($my_post['post_status']);
				unset($my_post['post_date']);  
				unset($my_post['post_type']);
				unset($my_post['comment_status']);
				unset($my_post['ping_status']);
				unset($my_post['post_content']);
				
				wp_update_post($my_post);
				$status = 2;
			}
			
		}
		*/
		return array('id'=>$id, 'status'=>$status, 'err'=>$err);
	}
	
	private static function settingsResponse($settings){
		extract($settings);
		
		switch($status){
			case 1:
				if($id==0){
					
					?>
					<p class="error">Errore nel salvataggio dell'informazione</p>
					<?php 
				}else{
					?>
					<p class="updated">Creata pagina corrispondente al permalink</p>
					<?php
				}
				break;
			case 2:
				?>
				<p class="updated">Permalink della pagina aggiornato</p>
				<?php 
				break;
			case 3:
				?>
				<p class="updated">Pagina rimossa regolarmente</p>	
				<?php 
		}
	}
	static private function setPermalinkActionBar($id){
		if($id!=0){
			$permalink = get_permalink($id);
			$admin = get_bloginfo('url') . '/wp-admin/post.php?action=edit&post='. $id;
			$adminNew = get_bloginfo('url') . '/wp-admin/post-new.php?post_type=page';
			?>
			<div class="row-actions">
				<a href="<?php echo $adminNew ?>">Oppure crea una nuova pagina</a> |
				<span class="edit">
					<a href="<?php echo $admin?>">Modifica</a>
				</span> |
				<span class="view">
					<a href="<?php echo $permalink?>" rel="permalink">Visualizza</a>
				</span>
			</div>
			<?php
		}		
	}
	
	private function pageSettingsMetadata($name, $displayName, $valueList, $valueDetail){
		?>
		<tr>
			<th scope="row">
				<strong><label title="Questi metadati verranno riportati solo sull'elenco" for="dc-<?php echo $name?>-list">Metadati sull'elenco:</label></strong>
			</th>
			<td>
				<textarea name="<?php echo $name?>_ldc" id="dc-<?php echo $name?>-list" cols="80" rows="10"><?php echo($valueList)?></textarea>
				<p>
					Inserire un metadato per riga nella forma <code>CHIAVE=VALORE</code>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<strong><label title="Questi metadati verranno riportati solo sulla scheda di dettaglio" for="dc-<?php echo $name?>-detail">Metadati sul dettaglio:</label></strong>
			</th>
			<td>
				<textarea name="<?php echo $name?>_ddc" id="dc-<?php echo $name?>-detail" cols="80" rows="10"><?php echo($valueDetail)?></textarea>
				<p>
					Inserire un metadato per riga nella forma <code>CHIAVE=VALORE</code>.<br />
					Inoltre è possibile inserire nei metadati le informazioni relative ai dettagli del record <strong><?php echo($displayName)?></strong> utilizzando la sintassi <code>@&lt;nome del campo&gt;;</code> (per esempio <code>itemid=<strong>@id;</strong></code>).
				</p>
				<p>&Egrave; possibile, per la scheda di dettaglio, fare riferimento alle informazioni della tabella usando questi codici:</p>
				<ul>
					<?php 
					global $wpdb;
					$sql ='describe '. $wpdb->prefix . 'pa_'. $name;
					$results = $wpdb->get_results($sql);
					$primaVolta = true;
					foreach($results as $row){
						if($primaVolta){
							$primaVolta = false;
						}else{
							echo(", ");
						}
						echo('<code>@' . $row->Field .';</code>' );
						
					}
					
					?>
					
				</ul>
				
			</td>
		</tr>
		<?php
	}
	static private function setPermalinkFormStructure($fieldName, $id , $responses = null){
		$dropdown_args = array(
				'post_type'        => 'page',
				'selected'         => $id,
				'name'             => $fieldName,
				'show_option_none' => "-- Seleziona una pagina --",
				'sort_column'      => 'menu_order, post_title',
				'echo'             => 1,
		);
		
		wp_dropdown_pages( $dropdown_args );
		if(!is_null($responses)) self::settingsResponse(responses);
		self::setPermalinkActionBar($id);
				
	}
		

	
	static function pageSettings(){
		
		$permalinks = get_option('PAFacile_permalinks');
		$generalSettings = get_option('PAFacile_settings'); 
		if(is_array($permalinks)) extract($permalinks);
		if(is_array($generalSettings)) extract($generalSettings);
		if(isset($_POST) && count($_POST)>0){
			if(!is_numeric($_POST['headerLevel']) ) $_POST['headerLevel'] = 3;
			extract($_POST);
			
			do_action("pafacile_save_settings", $_POST);
			
			// Salvo le impostazioni generiche
			$generalSettings = array();
			$generalSettings['AlboPretorioPrivacy'] 			= isset($_POST['albo_pretorio_privato'])?$_POST['albo_pretorio_privato']:'';
			$generalSettings['AlboPretorioEsclusivo'] 			= isset($_POST['albo_esclusivo'])?$_POST['albo_esclusivo']:'';	# Since V.1.4
			$generalSettings['certificazione_pubblicazione_1'] 	= stripslashes( $_POST['certificazione_pubblicazione_1'] );
			$generalSettings['certificazione_pubblicazione_0'] 	= stripslashes( $_POST['certificazione_pubblicazione_0'] );
			
			# Since Ver 2.4
			$generalSettings['dichiarazioneNegativa'] 			= stripslashes( $_POST['dichiarazione_negativa'] );
			
			# Since Ver 1.5.7
			$generalSettings['addDoublinCoreHeaders'] = $_POST['addDoublinCoreHeaders'];
			
			$generalSettings['LivelloHeader']		= $_POST['headerLevel'];
			foreach($_POST as $key => $value){
				if(substr($key,-4)=='_ldc' || substr($key,-4)=='_ddc') $generalSettings[$key] = $_POST[$key];
			}
			
			update_option('PAFacile_settings', $generalSettings);
			
			# -----------------------------------------------------------------------------
			# Settaggi per google analytics (new in ver 2.1)
			# -----------------------------------------------------------------------------
			$googleAnalytics = array(
				'username'		=> $_POST['username_google_analytics'],
				'password'		=> $_POST['password_google_analytics']
			);
			update_option('PAFacile_GoogleAnalytics', $googleAnalytics);
			unset($_POST['username_google_analytics']);
			unset($_POST['password_google_analytics']);
			# -----------------------------------------------------------------------------
			// Opzione per visualizzare la sezione pafacile-credits nel footer della pagina (Ver 2.0.1)
			update_option('PAFacile_Credits',	$_POST['credits']);
			
			unset($_POST['albo_pretorio_privato']);
			unset($_POST['headerLevel']);
			unset($_POST['albo_esclusivo']);	# Since V.1.4
			unset($_POST['credits']);			# Since V.2.0.1
			
			// Since ver 1.4.1 -- Certificazioni di pubblicazione all'albo on-line
			unset($_POST['certificazione_pubblicazione_1']);
			unset($_POST['certificazione_pubblicazione_0']);
			// -------------------------------------------------------------------
			
			unset($_POST['addDoublinCoreHeaders']); # Since Ver 1.5.7
			
			foreach($_POST as $key => $value){
				if(substr($key,-4)=='_ldc' || substr($key,-4)=='_ddc') unset($_POST[$key]);
			}
			
			// Devo creare le pagine corrispondenti se non esistono
			/*
			 * Rimosso dalla versione 2.5
			 * 
			$responses = array();
			$responses['delibere'] 		= self::settingsSavePageInfo('Delibere', 							$delibere, 			$delibere_id);
			$responses['determine'] 	= self::settingsSavePageInfo('Determine', 							$determine, 			$determine_id);
			$responses['bandi'] 		= self::settingsSavePageInfo('Bandi di Gara, Concorsi e Avvisi', 	$bandi, 				$bandi_id);
			$responses['ordinanze'] 	= self::settingsSavePageInfo('Ordinanze', 							$ordinanze, 			$ordinanze_id);
			$responses['organigramma'] 	= self::settingsSavePageInfo('Organigramma', 						$organigramma, 		$organigramma_id);
			$responses['organi'] 		= self::settingsSavePageInfo('Organi Istituzionali', 				$organi, 				$organi_id);
			$responses['albopretorio'] 	= self::settingsSavePageInfo('Albo pretorio', 						$albopretorio, 		$albopretorio_id);
			$responses['incarichi'] 	= self::settingsSavePageInfo('Incarichi professionali', 			$incarichi, 			$incarichi_id);
			*/
			$settings = $_POST;
			$settings['delibere_id'] 		= $delibere; 		# $responses['delibere']['id'];
			$settings['determine_id'] 		= $determine; 		# $responses['determine']['id'];
			$settings['ordinanze_id'] 		= $ordinanze;		# $responses['ordinanze']['id'];
			$settings['bandi_id'] 			= $bandi;			# $responses['bandi']['id'];
			$settings['organigramma_id'] 	= $organigramma;	# $responses['organigramma']['id'];
			$settings['organi_id'] 			= $organi;			# $responses['organi']['id'];
			$settings['albopretorio_id'] 	= $albopretorio;	# $responses['albopretorio']['id'];
			$settings['incarichi_id'] 		= $incarichi;		# $responses['incarichi']['id'];
			
			/*
			 * Since Ver 2.5
			 */
			$settings['sovvenzioni_id'] 	= $sovvenzioni;
					
			unset($settings['Submit']);
/*
			if($responses['delibere']['id']==0) 	$settings['delibere'] ='';
			if($responses['determine']['id']==0) 	$settings['determine'] ='';
			if($responses['ordinanze']['id']==0) 	$settings['ordinanze'] ='';
			if($responses['bandi']['id']==0) 		$settings['bandi'] ='';
			if($responses['organigramma']['id']==0) $settings['organigramma'] ='';
			if($responses['organi']['id']==0) 		$settings['organi'] ='';
			if($responses['albopretorio']['id']==0) $settings['albopretorio'] ='';
			if($responses['incarichi']['id']==0) 	$settings['incarichi'] ='';
			*/
			update_option('PAFacile_permalinks', $settings);
			
			$permalinks = get_option('PAFacile_permalinks');
		}
		
		if(is_array($permalinks)) extract($permalinks);
		if(is_array($generalSettings)) extract($generalSettings);
		
		!isset($AlboPretorioEsclusivo) && $AlboPretorioEsclusivo = 'n';
		!isset($dichiarazioneNegativa) && $dichiarazioneNegativa = '';
		
		?>
		<div class="wrap" id="pafacile-page-settings">
			<div id="icon-options-general" class="icon32"><br/></div>
			<h2>Impostazioni di PAFacile</h2>
			
			<form method="post" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
				
				 <h3 class="nav-tab-wrapper">
					<?php do_action('pafacile_config_tab_before_label_albo'); ?>
					<a class="nav-tab" href="#tab-albo-pretorio">Albo Online</a>
					<?php do_action('pafacile_config_tab_before_label_bandi'); ?>
					<a class="nav-tab" href="#tab-bandi">Bandi e Gare</a></li>
					<?php do_action('pafacile_config_tab_before_label_delibere'); ?>
					<a class="nav-tab" href="#tab-delibere">Delibere</a>
					<?php do_action('pafacile_config_tab_before_label_determine'); ?>
					<a class="nav-tab" href="#tab-determine">Determine</a>
					<?php do_action('pafacile_config_tab_before_label_incarichi'); ?>
					<a class="nav-tab" href="#tab-incarichi">Incarichi</a>
					<?php do_action('pafacile_config_tab_before_label_ordinanze'); ?>
					<a class="nav-tab" href="#tab-ordinanze">Ordinanze</a>
					<?php do_action('pafacile_config_tab_before_label_organi'); ?>
					<a class="nav-tab" href="#tab-organi">Organi</a>
					<?php do_action('pafacile_config_tab_before_label_organigramma'); ?>
					<a class="nav-tab" href="#tab-organigramma">Organigramma</a>
					<?php do_action('pafacile_config_tab_before_label_sovvenzioni'); ?>
					<a class="nav-tab" href="#tab-sovvenzioni">Sovvenzioni</a>
					<?php do_action('pafacile_config_tab_before_label_statistiche'); ?>
					<a class="nav-tab" href="#tab-statistiche">Statistiche</a>
					<?php do_action('pafacile_config_tab_before_label_altro'); ?>
					<a class="nav-tab" href="#tab-other-stuffs">Altro...</a>
				</h3>
				<?php do_action('pafacile_config_tab_before_albo'); ?>
				<div id="tab-albo-pretorio" class="tab-item">
					<h4>Albo Online</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="albopretorio">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaAlbo = ( isset($responses) && isset($responses['albopretorio']) )?$responses['albopretorio']:null;
								self::setPermalinkFormStructure('albopretorio', $albopretorio_id, $rispostaAlbo);
								?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<strong>Privacy:</strong>
							</th>
							<td>
								<input type="checkbox" id="albo_privato" name="albo_pretorio_privato" value="y" <?php echo (($AlboPretorioPrivacy=='y')?'checked="checked"':'') ?> />
								<label for="albo_privato">Rendi private le affissioni all'albo pretorio scadute</label>
								<p>
									Tramite questa opzione tutte le affissioni all'albo pretorio che risultano scadute, risulteranno visibili solo dall'area amministrativa (backend) di Wordpress.
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<strong>Gestione esclusiva:</strong>
							</th>
							<td>
								<input type="checkbox" id="albo_esclusivo" name="albo_esclusivo" value="y" <?php echo (($AlboPretorioEsclusivo=='y')?'checked="checked"':'') ?> />
								<label for="albo_esclusivo">Utilizza solo PAFacile per la gestione del registro per l'Albo on-line</label>
								<p>
									Attivando questa funzione, non sarà possibile gestire manualmente il numero di registro applicabile a ciascun atto inserito nell'albo on-line.
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<strong><label for="certificazione_pubblicazione_1">Certificazione di pubblicazione con osservazioni:</label></strong>
							</th>
							<td>
								<textarea name="certificazione_pubblicazione_1" id="certificazione_pubblicazione_1" cols=80" rows="10"><?php echo $certificazione_pubblicazione_1 ?></textarea>
								<p>
									Utilizzare i seguenti segnaposto per le informazioni acquisite dalla pubblicazione:
								</p>
								<dl>
									<dt>@pubblicazione_dal;</dt>
									<dd>Data di prima pubblicazione</dd>
									<dt>@pubblicazione_al;</dt>
									<dd>Data di ritiro dall'albo on-line</dd>
									<dt>@data_certificazione;</dt>
									<dd>Data di certificazione di pubblicazione</dd>
									<dt>@incaricato;</dt>
									<dd>Nome dell'utente che definisce la data di certificazione</dd>
								</dl>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<strong><label for="certificazione_pubblicazione_0">Certificazione di pubblicazione senza osservazioni:</label></strong>
							</th>
							<td>
								<textarea name="certificazione_pubblicazione_0" id="certificazione_pubblicazione_0" cols="80" rows="10"><?php echo $certificazione_pubblicazione_0 ?></textarea>
								<p>
									Utilizzare i seguenti segnaposto per le informazioni acquisite dalla pubblicazione:
								</p>
								<dl>
									<dt>@pubblicazione_dal;</dt>
									<dd>Data di prima pubblicazione</dd>
									<dt>@pubblicazione_al;</dt>
									<dd>Data di ritiro dall'albo on-line</dd>
									<dt>@data_certificazione;</dt>
									<dd>Data di certificazione di pubblicazione</dd>
									<dt>@incaricato;</dt>
									<dd>Nome dell'utente che definisce la data di certificazione</dd>
								</dl>
							</td>
							
						</tr>
						<?php self::pageSettingsMetadata('albopretorio','Albo Pretorio', $albopretorio_ldc, $albopretorio_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_bandi'); ?>
				<div id="tab-bandi" class="tab-item">
					<h4>Bandi &amp; Gare</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="bandi">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaBandi = ( isset($responses) && isset($responses['bandi']) )?$responses['bandi']:null;
								self::setPermalinkFormStructure('bandi', $bandi_id, $rispostaBandi);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('bandi','Bandi & Gare', $bandi_ldc, $bandi_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_delibere'); ?>
				<div id="tab-delibere">
					<h4>Delibere</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="delibere">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaDelibere = ( isset($responses) && isset($responses['delibere']) )?$responses['delibere']:null;
								self::setPermalinkFormStructure('delibere', $delibere_id, $rispostaDelibere);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('delibere','Delibere', $delibere_ldc, $delibere_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_determine'); ?>
				<div id="tab-determine">
					<h4>Determine</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="determine">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaDetermine = ( isset($responses) && isset($responses['determine']) )?$responses['determine']:null;
								self::setPermalinkFormStructure('determine', $determine_id, $rispostaDetermine);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('determine','Determine', $determine_ldc, $determine_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_incarichi'); ?>
				<div id="tab-incarichi">
					<h4>Incarichi professionali</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="incarichi">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaIncarichi = ( isset($responses) && isset($responses['incarichi']) )?$responses['incarichi']:null;
								self::setPermalinkFormStructure('incarichi', $incarichi_id, $rispostaIncarichi);
								?>
							</td>
						</tr>
						<tr>
							<th scope="row"><strong><label for="nessun-incarico">Dichiarazione negativa:</label></strong>
							<td>
								<textarea name="dichiarazione_negativa" id="nessun-incarico" cols="80" rows="5"><?php echo $dichiarazioneNegativa ?></textarea>
								<p>
									Il suddetto testo verrà presentato sull'elenco delle dichiarazioni dell'anno corrente nel caso in cui l'amministrazione
									non avesse ancora conferito incarichi esterni.
								</p>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('incarichi','Incarichi', $incarichi_ldc, $incarichi_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_ordinanze'); ?>
				<div id="tab-ordinanze">
					<h4>Ordinanze</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="ordinanze">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaOrdinanze = ( isset($responses) && isset($responses['ordinanze']) )?$responses['ordinanze']:null;
								self::setPermalinkFormStructure('ordinanze', $ordinanze_id, $rispostaOrdinanze);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('ordinanze','Ordinanze', $ordinanze_ldc, $ordinanze_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_organi'); ?>
				<div id="tab-organi">
					<h4>Organi</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="organi">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaOrgani = ( isset($responses) && isset($responses['organi']) )?$responses['organi']:null;
								self::setPermalinkFormStructure('organi', $organi_id, $rispostaOrgani);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('organi','Organi', $organi_ldc, $organi_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_organigramma'); ?>
				<div id="tab-organigramma">
					<h4>Organigramma</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="organigramma">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaOrganigramma = ( isset($responses) && isset($responses['organigramma']) )?$responses['organigramma']:null;
								self::setPermalinkFormStructure('organigramma', $organigramma_id, $rispostaOrganigramma);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('organigramma','Organigramma', $organigramma_ldc, $organigramma_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_organigramma'); ?>
				<div id="tab-sovvenzioni">
					<h4>Sovvenzioni</h4>
					<table class="form-table">
						<tr>
							<th scope="row"><strong><label for="sovvenzioni">Permalink:</label></strong></th>
							<td>
								<?php 
								$rispostaOrganigramma = ( isset($responses) && isset($responses['sovvenzioni']) )?$responses['sovvenzioni']:null;
								self::setPermalinkFormStructure('sovvenzioni', $sovvenzioni_id, $rispostaSovvenzioni);
								?>
							</td>
						</tr>
						<?php self::pageSettingsMetadata('sovvenzioni','Sovvenzioni', $sovvenzioni_ldc, $sovvenzioni_ddc); ?>
					</table>
				</div>
				<?php do_action('pafacile_config_tab_before_statistiche'); ?>
				<div id="tab-statistiche">
					<h4>Statistiche</h4>
					<?php 
					$googleAnalytics = get_option('PAFacile_GoogleAnalytics', array('username'=>'', 'password'=>''));
					?>
					<p>
						PAFacile utilizza i dati di Google Analytics per mostrare la pagina delle statistiche di accesso al sito web.
					</p>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="username_google_analytics">Nome utente:</label>
							</th>
							<td>
								<input type="text" id="username_google_analytics" name="username_google_analytics" value="<?php echo $googleAnalytics['username'] ?>" />
								<p class="help">
									Account Google con il quale si effettua l'accesso al pannello di controllo di Google Analytics. 
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="password_google_analytics">Password:</label>
							</th>
							<td>
								<input type="password" id="password_google_analytics" name="password_google_analytics" value="<?php echo $googleAnalytics['password'] ?>" />
							</td>
						</tr>
					</table>
					<?php 
					if($googleAnalytics['username']!='' && $googleAnalytics['password']!=''){
						require_once PAFACILE_PLUING_DIRECTORY .'/google-analytics/index.php';
						?>
						<div id="google-analytics-credential-test">
							<h3>Verifica funzionamento</h3>
							<?php
							$ga = new PAFacileGoogleAnalytics(true);
							echo $ga->getCount(30);
							?>
						</div>
						<?php
					}
					?>
				</div>
				<?php 
				do_action('pafacile_config_tab_before_altro');
				?>
				<div id="tab-other-stuffs">
					<h4>Altro...</h4>
					<table class="form-table">
						<tr>
							<th scope="row">
								<strong>Riconoscimento:</strong>
							</th>
							<td>
								<?php 
								$credits = get_option('PAFacile_Credits');
								?>
								<p>
									<input type="radio" name="credits" value="y" id="pafacile_credits_y" <?php echo ($credits =='y')?'checked="checked"':'' ?> />
									<label for="pafacile_credits_y">Fai sapere a tutti che usi PAFacile</label><br />
								</p>
								<p>
									<input type="radio" name="credits" value="n" id="pafacile_credits_n" <?php echo ($credits =='n')?'checked="checked"':'' ?> />
									<label for="pafacile_credits_n">Non mostrare i riconoscimenti</label>
								</p>
								<p>
									PAFacile è un plugin rilasciato sotto licenza OpenSource GPLv3. Per aiutarci a renderlo un plugin migliore anche per te
									ti chiediamo di attivare il riconoscimento. Se non lo ritieni opportuno, sei libero di disattivare questa opzione in 
									qualsiasi momento. 
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<strong><label for="headerLevel">Indice partenza livello header dei contenuti:</label></strong>
							</th>
							<td>
								<input type="text" name="headerLevel" id="headerLevel" value="<?php echo($LivelloHeader)?>" />
								<p>
									Questa opzione consente di definire da che livello inizieranno le intestazioni prodotte da PAFacile nei contenuti (es. se il valore specificato è 3 l'intestazione di livello maggiore prodotta da PAFacile sarà h3).
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<strong><label for="addDoublinCoreHeaders">Aggiungi le intestazioni di inizializzazione per Doublin Core</label></strong>
							</th>
							<td>
								<select name="addDoublinCoreHeaders" id="addDoublinCoreHeaders">
									<option value="y" <?php if($addDoublinCoreHeaders!='n') echo('selected="selected"') ?>>
										Sì, aggiungi le inizializzazioni per Doublin Core	
									</option>
									<option value="n" <?php if($addDoublinCoreHeaders=='n') echo('selected="selected"') ?>>
										No, non aggiungere i metadati di inizializzazione per Doublin Core.
									</option>
								</select>
								<p>
									<strong>Attenzione!</strong>
									In caso di attivazione dello standard Doublin Core&reg; è necessario che l'elemento <code>head</code>
									della pagina riporti l'attributo profile con i seguenti riferimenti:
									<code>http://gmpg.org/xfn/11</code> e <code>http://dublincore.org/documents/dcq-html/</code>.
								</p>
								<p>
									è importante sapere che <strong>una doppia inizializzazione dei riferimenti a Doublin Core &reg;</strong>
									potrebbero portare a una cattiva interpretazione dei fogli di stile da parte del browser causando conseguenti
									problemi di funzionamento del sito.
								</p>
							</td>
						</tr>
					</table>
				</div>
				<p class="submit"> 
					<input type="submit" name="Submit" class="button-primary" value="Salva le modifiche" /> 
				</p> 
			</form>
		</div>
		<?php
	}
	
}

?>