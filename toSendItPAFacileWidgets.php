<?php
class toSendItPAFacileWidgets{
	static function init(){
		wp_register_sidebar_widget('pafacile-bandi','Bandi e Gare', array('toSendItPAFacileWidgets', 'bandi'));
		wp_register_sidebar_widget('pafacile-delibere','Delibere', array('toSendItPAFacileWidgets', 'delibere'));
		wp_register_sidebar_widget('pafacile-determine','Determine', array('toSendItPAFacileWidgets', 'determine'));
		wp_register_sidebar_widget('pafacile-albo-pretorio','Albo Pretorio', array('toSendItPAFacileWidgets', 'alboPretorio'));
		#register_sidebar_widget('Bandi e gare', array('toSendItPAFacileWidgets', 'bandi'));
		#register_sidebar_widget('Delibere', array('toSendItPAFacileWidgets', 'delibere'));
		#register_sidebar_widget('Determine', array('toSendItPAFacileWidgets', 'determine'));
		#register_sidebar_widget( 'Albo Pretorio', array('toSendItPAFacileWidgets', 'alboPretorio'));   

		
		if(is_admin()){
			# -------------------------------------------------------------------------------------------------------   
			# Queste caratteristiche le abilito solo se l'utente è in un contesto di amministrazione.
			# -------------------------------------------------------------------------------------------------------   
			wp_register_widget_control('pafacile-bandi',		'Bandi e Gare', 	array('toSendItPAFacileWidgets', 'bandi_control'));
			wp_register_widget_control('pafacile-delibere',		'Delibere', 		array('toSendItPAFacileWidgets', 'delibere_control'));
			wp_register_widget_control('pafacile-determine',	'Determine', 		array('toSendItPAFacileWidgets', 'determine_control'));
			wp_register_widget_control('pafacile-albo-pretorio','Albo Pretorio', 	array('toSendItPAFacileWidgets', 'alboPretorio_control'));
			
			#register_widget_control( 'Bandi e gare', array('toSendItPAFacileWidgets', 'bandi_control'));   
			#register_widget_control( 'Delibere', array('toSendItPAFacileWidgets', 'delibere_control'));   
			#register_widget_control( 'Determine', array('toSendItPAFacileWidgets', 'determine_control'));   
			#register_widget_control( 'Albo Pretorio', array('toSendItPAFacileWidgets', 'alboPretorio_control'));
		}
		# -------------------------------------------------------------------------------------------------------   
	}

	static function alboPretorio($settings){

		global $wpdb;
		
		$opt = get_option('PAFacile_alboPretorio');
		
		$plink = get_option('PAFacile_permalinks');
		$permalink = '';
		if(isset($plink['albopretorio_id']) && $plink['albopretorio_id']!=0) $permalink = get_permalink($plink['albopretorio_id']);
		
		$sql = 'select * from ' . $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
		$sql.= ' where (sysdate() between pubblicata_dal and pubblicata_al)'; 
		if($opt['tipo']!=''){
			$sql .= ' and tipo="' .$opt['tipo'] .'"'; 
		}
		
		$sql .=' order by year(pubblicata_dal) desc, numero_registro desc';
		if($opt['righe']) $sql.=' limit ' . $opt['righe'];
		#echo($sql);
		echo($settings['before_widget']);
		echo($settings['before_title']);
		echo('<span>'.$opt['title']);
		if($permalink !='' ) echo(' <a title="consulta l\'archivio dell\'Albo on line" class="link archivio" href="'. $permalink . '">(archivio)</a>');
		echo('</span>');
		echo($settings['after_title']);
		
		$result = $wpdb->get_results($sql);
		if(count($result)>0){
			echo('<ul class="widget-albo-pretorio-results">');
			foreach($result as $row => $data){
				echo('<li>');
				echo('<a href="'.$permalink.'?itemId='. $data->id.'">' . $data->oggetto . '</a>');
				
				echo('<dl>');
				if($opt['display_registro']=='y'){
					echo('<dt>Numero registro</dt>');
					echo('<dd>'. $data->numero_registro.'</dd>');
				}
				echo('<dt>Periodo di affissione</dt>');
				echo('<dd> dal ' . toSendItGenericMethods::formatDateTime($data->pubblicata_dal) . ' al ' .  toSendItGenericMethods::formatDateTime($data->pubblicata_al) . '</dd>');
				
				if($opt['display_repgen']=='y'){
					echo('<dt>Repertorio generale</dt>');
					echo('<dd> numero ' . $data->repertorio_nr . ' del ' . toSendItGenericMethods::formatDateTime($data->repertorio_data) .'</dd>');
				}
				if($opt['display_protocollo']=='y'){
					echo('<dt>Protocollo</dt>');
					echo('<dd> numero ' . $data->protocollo_nr . ' del ' . toSendItGenericMethods::formatDateTime($data->protocollo_data) .'</dd>');
				}
				if($opt['display_fascicolo']=='y'){
					echo('<dt>Fascicolo</dt>');
					echo('<dd> numero ' . $data->fascicolo_nr . ' del ' . toSendItGenericMethods::formatDateTime($data->fascicolo_data) .'</dd>');
				}				
				if($opt['display_atto']=='y'){
					echo('<dt>Atto</dt>');
					echo('<dd>' . PAFacileDecodifiche::tipoAtto($data->tipo) . 'numero' . $data->atto_nr . ' del ' . toSendItGenericMethods::formatDateTime($data->atto_data) .'</dd>');
				}
				if($opt['display_provenienza']=='y'){
					echo('<dt>Provenienza</dt>');
					echo('<dd>' . $data->provenienza .'</dd>');
				}
				if($opt['display_materia']=='y'){
					echo('<dt>Materia</dt>');
					echo('<dd>' . $data->materia .'</dd>');
				}
				if($opt['display_ufficio']=='y'){
					$idArea = PAFacileDecodifiche::areaByOfficeId( $data->id_ufficio );
					if($idArea!=$data->id_ufficio){
						$area = PAFacileDecodifiche::officeNameById( $idArea ) . ' - ';
					}
					echo('<dt>Ufficio/Settore/Area di competenza</dt>');
					echo('<dd>' . $area . PAFacileDecodifiche::officeNameById( $data->id_ufficio ) .'</dd>');
				}
				if($opt['display_dirigente']=='y'){
					echo('<dt>Dirigente</dt>');
					echo('<dd>'. $data->dirigente . '</dd>');
				}
				if($opt['display_responsabile']=='y'){
					echo('<dt>Responsabile</dt>');
					echo('<dd>'. $data->responsabile . '</dd>');
				}
				echo('</dl>');
				echo('</li>');
			}
			echo('</ul>');
		}
		echo($settings['after_widget']);
		 	
	}
	
	static function alboPretorio_control(){
		if(isset($_POST) && isset($_POST['widget_type']) && $_POST['widget_type'] =='albo_pretorio'){
			$_POST = stripslashes_deep($_POST);
			$titolo = $_POST['title'];
			$tipo	= $_POST['tipo'];
			$righe	= $_POST['righe'];
			
			update_option('PAFacile_alboPretorio',array(
				'title' => $titolo,
				'tipo'	=> $tipo,
				'righe'	=> $righe,
				'display_registro' 		=> $_POST['display_registro'],
				'display_repgen' 		=> $_POST['display_repgen'],
				'display_protocollo' 	=> $_POST['display_protocollo'],
				'display_fascicolo' 	=> $_POST['display_fascicolo'],
				'display_atto' 			=> $_POST['display_atto'],
				'display_provenienza' 	=> $_POST['display_provenienza'],
				'display_materia' 		=> $_POST['display_materia'],
				'display_ufficio' 		=> $_POST['display_ufficio'],
				'display_dirigente' 	=> $_POST['display_dirigente'],
				'display_responsabile' 	=> $_POST['display_responsabile'],
			));
		}
		$settings = get_option('PAFacile_alboPretorio');
		?>
		<p>
			<input type="hidden" name="widget_type" value="albo_pretorio" />
			<label for="pafacile_albo_title">Titolo:</label><br />
			<input type="text" name="title" id="pafacile_albo_title" value="<?php echo $settings['title']?>" />
		</p>
		<p>
			<label for="pafacile_albo_type">Tipo di pubblicazione:</label><br />
			<select name="tipo" id="pafacile_albo_type">
				<option value="">Qualsiasi</option>
				<optgroup label="Bandi, Gare e Concorsi">
					<option value="co" <?php echo($settings['tipo']=='co'?'selected="selected"':'');?> >Bando di Concorso</option>
					<option value="ga" <?php echo($settings['tipo']=='ga'?'selected="selected"':'');?> >Bando di Gara</option>
					<option value="gr" <?php echo($settings['tipo']=='gr'?'selected="selected"':'');?> >Graduatoria</option>
					<option value="es" <?php echo($settings['tipo']=='es'?'selected="selected"':'');?> >Esito</option>
					<option value="ba" <?php echo($settings['tipo']=='ba'?'selected="selected"':'');?> >Altri bandi</option>
				</optgroup>
				<optgroup label="Delibere, Determine e ordinanze">
					<option value="dg" <?php echo($settings['tipo']=='dg'?'selected="selected"':'');?> >Delibere di Giunta</option>
					<option value="dc" <?php echo($settings['tipo']=='dc'?'selected="selected"':'');?> >Delibere di Consiglio</option>
					<option value="dt" <?php echo($settings['tipo']=='dt'?'selected="selected"':'');?> >Determine</option>
					<option value="or" <?php echo($settings['tipo']=='or'?'selected="selected"':'');?> >Ordinanze</option>
				</optgroup>
				<optgroup label="Altro">
					<option value="ma" <?php echo($settings['tipo']=='ma'?'selected="selected"':'');?> >Pubblicazioni di matrimonio</option>
					<option value="pe" <?php echo($settings['tipo']=='pe'?'selected="selected"':'');?> >Permessi di costruire</option>
					<option value="al" <?php echo($settings['tipo']=='al'?'selected="selected"':'');?> >Altro</option>
				</optgroup>
			</select>
		</p>
		<p>
			<label for="pafacile_display_registro"><input type="checkbox" name="display_registro" id="pafacile_display_registro" value="y" <?php echo ($settings['display_registro']=='y')?'checked="checked"':''?> /> Mostra numero di registro</label>
		</p>
		<p>
			<label for="pafacile_display_repgen"><input type="checkbox" name="display_repgen" id="pafacile_display_repgen" value="y" <?php echo ($settings['display_repgen']=='y')?'checked="checked"':''?> /> Mostra dettagli repertorio generale</label>
		</p>
		<p>
			<label for="pafacile_display_protocollo"><input type="checkbox" name="display_protocollo" id="pafacile_display_protocollo" value="y" <?php echo ($settings['display_protocollo']=='y')?'checked="checked"':''?> /> Mostra dettagli protocollo</label>
		</p>
		<p>
			<label for="pafacile_display_fascicolo"><input type="checkbox" name="display_fascicolo" id="pafacile_display_fascicolo" value="y" <?php echo ($settings['display_fascicolo']=='y')?'checked="checked"':''?> /> Mostra dettagli fascicolo</label>
		</p>
		<p>
			<label for="pafacile_display_atto"><input type="checkbox" name="display_atto" id="pafacile_display_atto" value="y" <?php echo ($settings['display_atto']=='y')?'checked="checked"':''?> /> Mostra dettagli atto</label>
		</p>
		<p>
			<label for="pafacile_display_provenienza"><input type="checkbox" name="display_provenienza" id="pafacile_display_provenienza" value="y" <?php echo ($settings['display_provenienza']=='y')?'checked="checked"':''?> /> Mostra la provenienza</label>
		</p>
		<p>
			<label for="pafacile-display-materia"><input type="checkbox" name="display_materia" id="pafacile-display-materia" value="y" <?php echo ($settings['display_materia']=='y')?'checked="checked"':''?> /> Mostra la materia</label>
		</p>
		<p>
			<label for="pafacile-display-ufficio"><input type="checkbox" name="display_ufficio" id="pafacile-display-ufficio" value="y" <?php echo ($settings['display_ufficio']=='y')?'checked="checked"':''?> /> Mostra l'ufficio e l'area</label>
		</p>
		<p>
			<label for="pafacile-display-dirigente"><input type="checkbox" name="display_dirigente" id="pafacile-display-dirigente" value="y" <?php echo ($settings['display_dirigente']=='y')?'checked="checked"':''?> /> Mostra il responsabile dell'ufficio</label>
		</p>
		<p>
			<label for="pafacile-display-responsabile"><input type="checkbox" name="display_responsabile" id="pafacile-display-responsabile" value="y" <?php echo ($settings['display_responsabile']=='y')?'checked="checked"':''?> /> Mostra il dirigente</label>
		</p>
		<p>
			<label for="pafacile_albo_righe">Numero di elementi:</label><br />
			<input type="text" name="righe" id="pafacile_albo_righe" value="<?php echo($settings['righe']); ?>" />
			
		</p>
		<?php
	}
	
	static function bandi($settings){
		global $wpdb;
		
		$opt = get_option('PAFacile_bandi', array(
				'title'	=> '',
				'tipo'	=> '',
				'righe'	=> 5,
				'display_tipo'		=> 'y',
				'display_estremi'	=> 'y',
				'display_ufficio'	=> 'y',
				'display_data_pubbl'=> 'y',
				'display_data_scad'	=> 'y',
				'display_data_esito'=> 'y',
				'extra_days_scad'	=> '7',
				'extra_days_esito'	=> '31'
		));
		
		if(!isset($opt['display_estremi'])) $opt['display_estremi'] = 'y';
		 
		$ggScad 	= $opt['extra_days_scad'];
		$ggEsito	= $opt['extra_days_esito'];
		$sql = 'select * from ' . $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI . '
		where 
		    data_pubblicazione<=now() and 
		    ( 
		        (
		            (datediff(now(), data_esito)<'. ($ggEsito+1).' and (data_esito is not null and data_esito<>"0000-00-00")) or 
		            (datediff(now(), data_scadenza)<'. ($ggScad+1).') and (data_esito is null or data_esito="0000-00-00"))
		        )';
		
		if($opt['tipo']!='') $sql .=' and tipo = "' . $wpdb->escape($opt['tipo']) . '"';  
		$sql .= ' order by data_esito desc, data_scadenza DESC limit ' . ( is_numeric($opt['righe'])?$opt['righe']:'8');
		#echo($sql);
		$result= $wpdb->get_results($sql);
		$plink = get_option('PAFacile_permalinks');
		$permalink = '';
		if(isset($plink['bandi_id']) && $plink['bandi_id']!=0) $permalink = get_permalink($plink['bandi_id']);
		
		echo($settings['before_widget']);
		echo($settings['before_title']);
		echo('<span>'.$opt['title']);
		if($permalink !='' ) echo(' <a title="consulta l\'archivio dei Bandi, Gare e Concorsi" class="link archivio" href="'. $permalink . '">(archivio)</a>');
		echo('</span>');
		echo($settings['after_title']);
		
		if(count($result)>0) echo('<ul>');
		
		for($i = 0; $i<count($result); $i++){
			$rs = $result[$i];
			echo
				'<li class="'. (($i%2)==0?'odd':'pair') .' ' . $rs->tipo . '" >',
				'<h3><a href="' . $permalink .'?itemId=' . $rs->id .'">' .$rs->oggetto,
				($rs->data_esito!='0000-00-00')?' (aggiudicato)':'',
				'</a></h3>';
			echo '<dl>';
			if($opt['display_tipo']=='y')
				echo 	'<dt class="tipo_bando_label">Tipo:</dt>',
						'<dd class="tipo_bando_value">' . PAFacileDecodifiche::tipoBando($rs->tipo) .'<br /></dd>';
			if($opt['display_estremi']=='y')
				echo 	'<dt class="estremi_bando_label">Estremi:</dt>',
				'<dd class="estremi_bando_value">' . $rs->estremi .'<br /></dd>';
			
			if($opt['display_ufficio']=='y')
				echo	'<dt class="ufficio_label">Ufficio:</dt>' ,
						'<dd class="ufficio_value">' . PAFacileDecodifiche::officeNameById($rs->id_ufficio) .'<br /></dd>';
			
			if($opt['display_data_pubbl']=='y')
				echo 	'<dt class="data_pubblicazione_label">Data Pubblicazione:</dt>',
						'<dd class="data_pubblicazione_value">' . toSendItGenericMethods::formatDateTime( $rs->data_pubblicazione ) .'<br /></dd>';
			if($rs->data_scadenza!=null){
				if($opt['display_data_scad']=='y')
					echo 	'<dt class="data_scadenza_label">Data Scadenza:</dt>',
							'<dd class="data_scadenza_value">' . toSendItGenericMethods::formatDateTime( $rs->data_scadenza) .'<br /></dd>';
			}
			if($rs->data_esito!='0000-00-00'){
				if($opt['display_data_esito']=='y')
					echo	'<dt class="data_scadenza_label">Data Esito:</dt>',
							'<dd class="data_scadenza_value">' . toSendItGenericMethods::formatDateTime( $rs->data_esito) .'<br /></dd>';
			}
			echo('</dl>');
			echo('</li>');
			
		}
		if(count($result)>0) echo('</ul>');
		
		echo($settings['after_widget']);
	}
	
	static function bandi_control(){ 
		
		if(isset($_POST) && isset($_POST['widget_type']) && $_POST['widget_type'] =='bandi'){
			$_POST = stripslashes_deep($_POST);
			$titolo = $_POST['title'];
			$tipo	= $_POST['tipo'];
			$righe	= $_POST['righe'];
				
			update_option('PAFacile_bandi',array(
					'title' => $titolo,
					'tipo'	=> $tipo,
					'righe'	=> $righe,
					'display_tipo' 			=> isset($_POST['display_tipo'])?'y':'n',
					'display_estremi'		=> isset($_POST['display_estremi'])?'y':'n',
					'display_ufficio' 		=> isset($_POST['display_ufficio'])?'y':'n',
					'display_data_pubbl' 	=> isset($_POST['display_data_pubbl'])?'y':'n',
					'display_data_scad' 	=> isset($_POST['display_data_scad'])?'y':'n',
					'display_data_esito' 	=> isset($_POST['display_data_esito'])?'y':'n',
					'extra_days_scad'		=> $_POST['gg_scadenza'],
					'extra_days_esito'		=> $_POST['gg_esito'],
						
			));
		}
		$settings = get_option('PAFacile_bandi', array(
			'title'	=> '',
			'tipo'	=> '',
			'righe'	=> 5,
			'display_tipo'		=> 'y',
			'display_estremi'	=> 'y',
			'display_ufficio'	=> 'y',
			'display_data_pubbl'=> 'y',
			'display_data_scad'	=> 'y',
			'display_data_esito'=> 'y',
			'extra_days_scad'	=> '7',
			'extra_days_esito'	=> '31'
		));
		?>
		<p>
			<input type="hidden" name="widget_type" value="bandi" />
			<label for="pafacile_bandi_title">Titolo:</label><br />
			<input type="text" name="title" id="pafacile_bandi_title" value="<?php echo esc_attr($settings['title']) ?>" />
		</p>
		<p>
			<label for="pafacile_bandi_type">Tipo di bando:</label><br />
			<select name="tipo" id="pafacile_bandi_type">
				<option value="">Qualsiasi</option>
				<option value="co" <?php echo($settings['tipo']=='co'?'selected="selected"':'');?> >Bando di Concorso</option>
				<option value="ga" <?php echo($settings['tipo']=='ga'?'selected="selected"':'');?> >Bando di Gara</option>
				<option value="gr" <?php echo($settings['tipo']=='gr'?'selected="selected"':'');?> >Graduatoria</option>
				<option value="es" <?php echo($settings['tipo']=='es'?'selected="selected"':'');?> >Esito</option>
				<option value="ba" <?php echo($settings['tipo']=='ba'?'selected="selected"':'');?> >Altri bandi</option>
			</select>
		</p>
		<p>
			<input type="checkbox" name="display_tipo" id="pafacile_bandi_display_tipo" 
					value="y" <?php echo ($settings['display_tipo']=='y')?'checked="checked"':''?> />
			<label for="pafacile_bandi_display_tipo">Mostra numero di registro</label>
		</p>
		<p>
			<input type="checkbox" name="display_ufficio" id="pafacile_bandi_display_ufficio" value="y" <?php echo ($settings['display_ufficio']=='y')?'checked="checked"':''?> /> 
			<label for="pafacile_bandi_display_ufficio">Mostra Ufficio</label>
		</p>
		<p>
			<input type="checkbox" name="display_estremi" id="pafacile_bandi_display_estremi" value="y" <?php echo ($settings['display_estremi']=='y')?'checked="checked"':''?> /> 
			<label for="pafacile_bandi_display_ufficio">Mostra Estremi</label>
		</p>
		<p>
			<input type="checkbox" name="display_data_pubbl" id="pafacile_bandi_display_data_pubbl" value="y" <?php echo ($settings['display_data_pubbl']=='y')?'checked="checked"':''?> /> 
			<label for="pafacile_bandi_display_data_pubbl">Mostra data pubblicazione</label>
		</p>
		<p>
			<input type="checkbox" name="display_data_scad" id="pafacile_bandi_display_data_scad" value="y" <?php echo ($settings['display_data_scad']=='y')?'checked="checked"':''?> /> 
			<label for="pafacile_bandi_display_data_scad">Mostra data Scadenza</label>
		</p>
		<p>
			<input type="checkbox" name="display_data_esito" id="pafacile_bandi_display_data_esito" value="y" <?php echo ($settings['display_data_esito']=='y')?'checked="checked"':''?> /> 
			<label for="pafacile_bandi_display_data_esito">Mostra data esito</label>
		</p>
		<p>
			<label for="pafacile_bandi_righe">Numero di elementi:</label><br />
			<input type="text" name="righe" id="pafacile_bandi_righe" value="<?php echo($settings['righe']); ?>" />
		</p>
		<div>
			<label for="pafacile_bandi_gg_scad">Giorni dalla scadenza:</label><br />
			<input type="text" name="gg_scadenza" id="pafacile_bandi_gg_scad" value="<?php echo($settings['extra_days_scad']); ?>" />
			<p class="tip">
				Saranno visualizzati anche i bandi scaduti da un numero di giorni massimo indicato in questa casella.
			</p>
		</div>
		<div>
			<label for="pafacile_bandi_gg_esito">Giorni dall'esito:</label><br />
			<input type="text" name="gg_esito" id="pafacile_bandi_gg_esito" value="<?php echo($settings['extra_days_esito']); ?>" />
			<p class="tip">
				Saranno visualizzati i bandi il cui esito è stato definito ed è compreso tra la data corrente e il numero di giorni indicato nella casella.
			</p>
		</div>
		<?php 
	}
	
	static function delibere($settings){
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix. TOSENDIT_PAFACILE_DB_DELIBERE . ' order by data_albo desc, data_seduta desc limit 3';
		
		$result= $wpdb->get_results($sql);
		echo($settings['before_widget']);
		echo($settings['before_title']);
		echo('<span>'.$settings['widget_name'].'</span>');
		echo($settings['after_title']);
		?>
		<ul>
			<?php 
			for($i = 0; $i<count($result); $i++){
				$rs = $result[$i];
				$tipo = (($rs->tipo=='g')?'Giunta':'Consiglio');
				?>
				<li>
					<h3><a href="#"><?php echo($rs->oggetto)?></a></h3>
					<dl>
						<dt class="tipo_numero_label">Delibera</dt>
						<dd class="tipo_numero_value"><?php echo $tipo?> ( numero <?php echo $rs->numero?>)<br /></dd>
						<dt class="data_seduta_label">Data seduta:</dt>
						<dd class="data_seduta_value"><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_seduta))?><br /></dd>
						<dt class="data_albo_label">Data pubblicazione all'albo</dt>
						<dd class="data_albo_value"><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_albo ))?><br /></dd>
					</dl>
					<span class="cboth">&nbsp;</span>
				</li>
				<?php 
			}
			
			?>
		</ul>
		<?php
		echo($settings['after_widget']); 
	}
	static function delibere_control(){ 
		/*
		 * TODO: Gestire le opzioni per le delibere
		 * - Numero di delibere
		 * - Tipo (Giunta o Consiglio)
		 */
	}
	#static function determine($settings){ 
		/*
		 * TODO: Implementare la rappresentazione del widget delle determine
		 */
	#}
	#static function determine_control(){ 
		/*
		 * TODO: Gestire le opzioni per le determine
		 * - Numero limite di determine
		 * - Opzione sull'ufficio
		 */
	#}
	#static function organi($settings){ 
		/*
		 * TODO: Implementare la rappresentazione del widget Organi Istituzionali
		 */
	#}
	#static function organi_control(){ 
		/*
		 * TODO: Gestire le opzioni per gli organi di controllo
		 * - Tipologia di organo
		 * - Soggetto individuale
		 * - Filtro ad una data precisa
		 */
	#}
	
	#static function organigramma($settings){
		/*
		 * TODO: Gestire la rappresentazione dell'organigramma
		 */
	#}
	
	#static function organigramma_control(){
		/*
		 * TODO: Gestire le opzioni per il widget organigramma
		 */
	#}
}