<?php
class PAFacileBackend{
	
	public static $userAllowedRoles = array(
		
		TOSENDIT_PAFACILE_ROLE_EDITORE_ALBO_PRETORIO 	=> "Può inserire e richiedere la pubblicazione degli atti nell'albo pretorio on-line",
		TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO 			=> "Può inserire, pubblicare e approvare le richieste di pubblicazione nell'albo pretorio on-line",
		TOSENDIT_PAFACILE_ROLE_BANDI_GARE				=> "Può pubblicare informazioni relative ai Bandi di Gara, Concorsi, Graduatorie",
		TOSENDIT_PAFACILE_ROLE_DELIBERE					=> "Può gestire completamente le informazioni della sezione delibere",
		TOSENDIT_PAFACILE_ROLE_DETERMINE				=> 'Può gestire la sezione Determine',
		TOSENDIT_PAFACILE_ROLE_ORDINANZE				=> "Può gestire la sezione Ordinanze",
		TOSENDIT_PAFACILE_ROLE_INCARICHI_PROFESSIONALI	=> "Può gestire la sezione Incarichi professionali",
		TOSENDIT_PAFACILE_ROLE_DETERMINE				=> "Può gestire la sezione Determine",
		TOSENDIT_PAFACILE_ROLE_ORGANIGRAMMA				=> "Può gestire l'organigramma",
		TOSENDIT_PAFACILE_ROLE_ORGANI					=> "Può gestire gli Organi di Governo",
		/*
		 * Since ver. 2.5.5
		 */
		TOSENDIT_PAFACILE_ROLE_SOVVENZIONI				=> "Può gestire le sovvenzioni e i contributi",
		
	);
	static function loadScriptsAndStylesheets(){
		if(self::isPAFacilePage()){
			/*
			 * Since WP 3.5
			 * Se non presente questa chiamata non è possibile aprire il popup nell'editor visuale
			 */ 
			add_thickbox();
			
			$dir = basename(dirname(__FILE__));
			$PAFacilePluginDir = WP_PLUGIN_URL . '/' .$dir;
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script('pafacile-jast-core', 		"$PAFacilePluginDir/scripts/JAST.src.js"  );
			wp_enqueue_script('pafacile-jast-popup', 		"$PAFacilePluginDir/scripts/JAST-popupbox.src.js", array('pafacile-jast-core')  );
			wp_enqueue_script('pafacile-jast-validator', 	"$PAFacilePluginDir/scripts/JAST-validator.src.js", array('pafacile-jast-core')  );
			wp_enqueue_script('pafacile-jq-methods', 		"$PAFacilePluginDir/scripts/jq.pafacile.js" , array('jquery') );
			
			wp_enqueue_style('pafacile-admin', 				"$PAFacilePluginDir/admin-pafacile.css", array(), TOSENDIT_PAFACILE_VERSION);
			wp_enqueue_style('pafacile-admin-print', 		"$PAFacilePluginDir/print-pafacile.css", array(), TOSENDIT_PAFACILE_VERSION, 'print');
		}
	}
	
	static function isPAFacileEditPage(){
		$p = isset($_GET['page'])?$_GET['page']:'';
		return (self::isPAFacilePage() && ( (preg_match('/\-edit$/i', $p) && isset($_GET['id'])) || preg_match('/\-new$/i', $p)));
		
	}
	static function isPAFacilePage(){
		# Eliminazione notice
		$p = isset($_GET['page'])?$_GET['page']:'';
		if(preg_match('/toSendIt/i', $p)){
			
			if(preg_match('/tosendit\-pa/i', $p)) return true;
			if(preg_match('/\-pa\?/i', $p)) return true;
			
			if(preg_match('/PAFacile/i', $p)) return true;
			
		} 
		
		return false;
	}
	
	
	static function setAdminHeader(){
		
		if(self::isPAFacilePage()){
			
			if(isset($_GET['printout'])){
				?>
				<!--[if lt IE 9]>
				<script type="text/javascript" src="<?php echo toSendItGenericMethods::pluginDirectory(); ?>/scripts/IE9.js"></script>
				<![endif]-->
				<?php
			}
		}
		if(self::isPAFacileEditPage()){
			toSendItGenericMethods::showTinyMCE();
			if(function_exists('insert_cforms_script')) insert_cforms_script();
		}
		
	}

	static function createMenu(){
		global $submenu, $wpdb, $current_user;
		$creatorFile = 'tosendit-pa';
		$permalinkSections = get_option('PAFacile_permalinks', array());
		$gruppi = toSendItGenericMethods::getUserGroups("pafacile");
		$minLevel = TOSENDIT_PAFACILE_DEFAULT_MIN_LEVEL;
		
		toSendItGenericMethods::createMenuStructure(
		
		
	  		array(
	  			'pageTitle' 	=> 'PAFacile',
	  			'menuTitle' 	=> 'PAFacile',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> $creatorFile,
	  			'imageUrl'		=> TOSENDIT_PAFACILE_PLUGIN_URL .'/images/fiocco20.png',
	  			'defaultAction'	=> array('PAFacilePages','pageWelcome'),
	  			'allowedRoles'	=>  array()
	  		), 
  			apply_filters('pafacile_welcome_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PA-Facile: informazioni sul plugin',
		  				'menuTitle'	=>	'Informazioni',
		  			),
		  			array(
		  				'pageTitle'	=> 	'PAFacile: configurazione del plugin', 
		  				'menuTitle'	=>	'Configurazione',
		  				'handler'	=>	$creatorFile.'?settings',
		  				'action'	=>	array('PAFacilePages','pageSettings'),
		  				'minLevel'	=>	"manage_options"	# Administrator
		  			)
	  			)
  			),$gruppi
  		);
		
	 	/*
	 	 * Since ver 2.6.0
	 	 */
	 	if(!empty($permalinkSections['albopretorio']))
		 toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Albo on-line',
	  			'menuTitle' 	=> 'Albo on-line',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/albo.png',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePAAlboPretorio'),
	  			'allowedRoles'	=>  array(TOSENDIT_PAFACILE_ROLE_EDITORE_ALBO_PRETORIO, TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO)
	  		),
	  		apply_filters('pafacile_albo_menu', 
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Modifica avviso all\'albo',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuovo avviso all\'albo',
		  				'menuTitle'	=>	'Nuovo',
		  				'handler'	=>	TOSENDIT_PAFACILE_ALBO_PRETORIO_NEW_HANDLER,
		  			),
		  			array(
		  				'pageTitle'	=> 	'PAFacile - Registro delle pubblicazioni', 
		  				'menuTitle'	=>	'Registro',
		  				'handler'	=>	TOSENDIT_PAFACILE_ALBO_PRETORIO_REGISTRO_HANDLER,
		  				'action'	=>	array('PAFacilePages','pagePAAlboRegistro'),
		  			),
		  			array(
		  				'pageTitle'	=> 	'PAFacile - Tipologie di atti pubblicabili', 
		  				'menuTitle'	=>	'Tipi atto',
		  				'handler'	=>	TOSENDIT_PAFACILE_TIPO_ATTO_EDIT_HANDLER,
		  				'action'	=>	array('PAFacilePages','pagePATipoAtto'),
		  				'allowedRoles'=>array(TOSENDIT_PAFACILE_ROLE_EDITORE_ALBO_PRETORIO) 
		  			),
		  			array(
		  				'pageTitle'	=> 	'PAFacile - Nuovo atto da pubblicare', 
		  				'menuTitle'	=>	'Nuovo tipo',
		  				'handler'	=>	TOSENDIT_PAFACILE_TIPO_ATTO_NEW_HANDLER,
		  				'action'	=>	array('PAFacilePages','pagePATipoAtto'),
		  				'allowedRoles'=>array(TOSENDIT_PAFACILE_ROLE_EDITORE_ALBO_PRETORIO)
		  			)
	  			)
	  		),$gruppi
	  	);
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['bandi']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Bandi & Gare',
	  			'menuTitle' 	=> 'Bandi & Gare',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/calendar.png',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePABandi'),
	  			'allowedRoles'	=>  array(TOSENDIT_PAFACILE_ROLE_BANDI_GARE)
	  		), 
			apply_filters('pafacile_bandi_gare_menu',
			  	array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Gestione Bando, Gara o Graduatoria',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuovo Bando, Gara o Graduatoria',
		  				'menuTitle'	=>	'Nuovo',
		  				'handler'	=>	TOSENDIT_PAFACILE_BANDI_NEW_HANDLER
		  			)
		  		)
			),$gruppi
	  	);
	 	
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['delibere']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Delibere',
	  			'menuTitle' 	=> 'Delibere',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_DELIBERE_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/delibere.png',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePADelibere'),
	  			'allowedRoles'	=>  array(TOSENDIT_PAFACILE_ROLE_DELIBERE)
	  		), 
			apply_filters('pafacile_delibere_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Gestione Delibere',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuova delibera',
		  				'menuTitle'	=>	'Nuova delibera',
		  				'handler'	=>	TOSENDIT_PAFACILE_DELIBERE_NEW_HANDLER
		  			)
		  		)
			),$gruppi
	  	);
	  	
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['determine']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Determinazioni',
	  			'menuTitle' 	=> 'Determinazioni',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_DETERMINE_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/determine.png',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePADetermine'),
	  			'allowedRoles'	=>  array(TOSENDIT_PAFACILE_ROLE_DETERMINE)
	  		), 
			apply_filters('pafacile_determinazioni_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Gestione determinazioni',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuova determinazione',
		  				'menuTitle'	=>	'Nuova determinazione',
		  				'handler'	=>	TOSENDIT_PAFACILE_DETERMINE_NEW_HANDLER
		  			)
		  		)
			),$gruppi
	  	);
	  	
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['incarichi']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Incarichi professionali',
	  			'menuTitle' 	=> 'Incarichi prof.',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> TOSENDIT_PAFACILE_INCARICHI_PROF_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/incarichi.png',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePAIncarichiProfessionali'),
	  			'allowedRoles'	=>  array(TOSENDIT_PAFACILE_ROLE_INCARICHI_PROFESSIONALI)
	  		), 
			apply_filters('pafacile_incarichi_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Gestione incarichi',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuovo incarico',
		  				'menuTitle'	=>	'Nuovo incarico',
		  				'handler'	=>	TOSENDIT_PAFACILE_INCARICHI_PROF_NEW_HANDLER
		  			)
		  		)
			),$gruppi
	  	);
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['ordinanze']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Ordinanze',
	  			'menuTitle' 	=> 'Ordinanze.',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_ORDINANZE_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/ordinanze.gif',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePAOrdinanze'),
	  			'allowedRoles'	=>	array(TOSENDIT_PAFACILE_ROLE_ORDINANZE)
	  		),
			apply_filters('pafacile_ordinanze_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Modifica Ordinanza',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuova ordinanza',
		  				'menuTitle'	=>	'Nuova',
		  				'handler'	=>	TOSENDIT_PAFACILE_ORDINANZE_NEW_HANDLER
		  			)
	  			)
			),$gruppi
  		);
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['organi']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Organi di Governo',
	  			'menuTitle' 	=> 'Organi gov.',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_ORGANI_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/organi.gif',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePAOrgani'),
	  			'allowedRoles'	=>	array(TOSENDIT_PAFACILE_ROLE_ORGANI)
	  		), 
			apply_filters('pafacile_organi_governo_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - Modifica nominativo',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuovo nominativo',
		  				'menuTitle'	=>	'Nuovo',
		  				'handler'	=>	TOSENDIT_PAFACILE_ORGANI_NEW_HANDLER,
		  			),
		  			array(
		  				'pageTitle'	=> 	'PAFacile - Tipologie degli organi di governo', 
		  				'menuTitle'	=>	'Tipologie',
		  				'handler'	=>	TOSENDIT_PAFACILE_TIPO_ORGANO_EDIT_HANDLER,
		  				'action'	=>	array('PAFacilePages','pagePATipoOrgano'),
		  			),
					array(
		  				'pageTitle'	=> 	'PAFacile - Nuova tipologia di organo', 
		  				'menuTitle'	=>	'Nuovo tipo',
		  				'handler'	=>	TOSENDIT_PAFACILE_TIPO_ORGANO_NEW_HANDLER,
		  				'action'	=>	array('PAFacilePages','pagePATipoOrgano'),
		  			)
		  		)
			),$gruppi
	  	);
	 	
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['organigramma']))
		toSendItGenericMethods::createMenuStructure(
	  		array(
	  			'pageTitle' 	=> 'Organigramma',
	  			'menuTitle' 	=> 'Organigramma',
	  			'minLevel'  	=> $minLevel,
	  			'menuSlug'		=> 	TOSENDIT_PAFACILE_ORGANIGRAMMA_EDIT_HANDLER,
	  			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/chart_organisation.png',
	  			'defaultAction'	=> 	array('PAFacilePages','pagePAOrganigramma'),
	  			'allowedRoles'	=>	array(TOSENDIT_PAFACILE_ROLE_ORGANIGRAMMA)
	  		), 
			apply_filters('pafacile_organigramma_menu',
		  		array(	
		  			array(
		  				'pageTitle' => 	'PAFacile - nodo dell\'Organigramma',
		  				'menuTitle'	=>	'Modifica',
		  			),
		  			array(
		  				'pageTitle' => 	'PAFacile - Nuovo nodo dell\'Organigramma',
		  				'menuTitle'	=>	'Nuovo',
		  				'handler'	=>	TOSENDIT_PAFACILE_ORGANIGRAMMA_NEW_HANDLER,
		  			)
	  			)
			), $gruppi
  		);
	 	
	 	/*
	 	 * Since ver 2.6.0
	 	*/
	 	if(!empty($permalinkSections['sovvenzioni']))
		toSendItGenericMethods::createMenuStructure(
			array(
				'pageTitle' 	=> 'Sovvenzioni, contributi e sussidi',
				'menuTitle' 	=> 'Sovvenzioni',
				'minLevel'  	=> $minLevel,
				'menuSlug'		=> 	TOSENDIT_PAFACILE_SOVVENZIONI_EDIT_HANDLER,
				'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/chart_organisation.png',
				'defaultAction'	=> 	array('PAFacilePages','pagePASovvenzioni'),
				'allowedRoles'	=>	array(TOSENDIT_PAFACILE_ROLE_SOVVENZIONI)
			),
			apply_filters('pafacile_sovvenzioni_menu',
				array(
					array(
		  				'pageTitle' => 	'PAFacile - Sovvenzioni, contributi e sussidi',
		  				'menuTitle'	=>	'Modifica',
					),
					array(
		  				'pageTitle' => 	'PAFacile - Nuova sovvenzione',
		  				'menuTitle'	=>	'Nuovo',
		  				'handler'	=>	TOSENDIT_PAFACILE_SOVVENZIONI_NEW_HANDLER,
					)
				)
			), $gruppi
		);
		
	}
	static function userProfilePage($user){
		global $current_user;
		if( $current_user->has_cap("create_users") ){
			?>
			<h4>Abilitazioni alle funzioni di PAFacile</h4>
			<?php
			$i = 0;
			$abilitazioni = toSendItGenericMethods::getUserGroups('pafacile',$user);
			
			foreach(self::$userAllowedRoles as $key => $value){
				$i +=1;
				?>
				<p>
					<input type="checkbox" value="<?php echo $key ?>" name="pafacile_auth[]" <?php echo (array_search($key, $abilitazioni, true)!==false)?'checked="checked"':'' ?> id="pafacile_auth_<?php echo $i?>" />
					<label for="pafacile_auth_<?php echo $i?>">
						<strong><?php echo $key ?></strong>
						<?php echo $value ?>
					</label>
				</p>
				<?php 
			}
		}else{
		}
	}
	
	static function userProfileSave($userId){
		global $current_user;
		if( $current_user->has_cap("create_users") ){
			update_user_option($userId, 'pafacile', $_POST['pafacile_auth'] );
		}
	}
}