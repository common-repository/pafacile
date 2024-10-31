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

class adminFormBuilder{

	private function fields($fields, $isNew){
		
		$fieldBaseTempaltes = array(
			'text'	=> '<label for="%2$s">%1$s</label><br /><input type="text" name="%2$s" id="%2$s" value="%3$s" %4$s />',
			'title'	=> 'BUILT-IN',
			'date'	=> 'BUILT-IN'
		);
		
		foreach($fields as $field){
			$type 		= 	$field['type'];
			$id			=	$field['id'];
			$label		=	$field['label'];
			$value		=	$field['value'];
			$applyTip 	= 	isset($field['suggestion']) && ($isNew && $field['suggestOnNew'] || !$isNew && $field['suggestOnEdit']);
			if(!isset($fieldBaseTempaltes[$type])){
				echo "tipo <strong>$type</strong> non riconosciuto.";
			}else{
				
				if($type  == 'text' ){
					
					$t = $fieldBaseTempaltes[$type];
					$output ='<p>';
					$extra = '';
					if(isset($field['maxlen'])) $extra .= ' maxlength="'.$field['maxlen'].'"';
					 
					$output .= sprintf($t, $label, $id, $value, $extra);
					if($applyTip){
						$suggestion = $field['suggestion'];
						$output 	.= "</p><p>$suggestion";
					}
					$output .='</p>';
				}elseif($type=='title'){
					$value = htmlspecialchars( $value );	
				
					$output = '<div id="titlewrap">';
					$output .= '<div id="titlediv">';
					$output .="<label for=\"title\">$label</label>";
					$output .="<input size=\"30\" type=\"text\" name=\"$id\" id=\"title\" value=\"$value\" />";
					$output .="</div>";
					if($applyTip){
						$suggestion = $field['suggestion'];
						$output .= "<p>$suggestion</p>";
					}
					
					$output .="</div>";
				}elseif($tipe=='date'){
					
				}
				echo $output;
			}
		}
		
	}
	private function submitButton($submitLabel, $deleteLabel, $deleteAction){
		?>
		<div id="major-publishing-actions">
			<div id="delete-action">
				<a class="submitdelete deletion" href="<?php echo $deleteAction ?> "><?php echo $deleteLabel ?></a>
			</div>
			<div id="publishing-action">
				<input class="button-primary"  type="submit" value="<?php echo $submitLabel?>" />
			</div>
			<div class="clear" ></div>
		</div>
		<?php 
	}
	public function build($formSettings){
		$title = $formSettings['title'];
		$uri = isset($formSettings['uri'])?$formSettings['uri']:$_SERVER['REQUEST_URI'];
		$formIconId = isset($formSettings['iconId'])?$formSettings['iconId']:'icon-edit-pages';
		
		if($formSettings['sidebar']){
			$postStuffClass = "has-right-sidebar";
			$sidebar = $formSettings['sidebar'];
		}else{
			$postStuffClass = '';
			$sidebar = null;
		}
		?>
		<div class="wrap">
			<div id="<?php echo $formIconId ?>" class="icon32"><br/></div>
			<h2><?php echo $title ?></h2>
			<form method="post" action="<?php echo $uri ?>">
				<div id="poststuff" class="<?php echo $postStuffClass ?>">
					<input type="hidden" name="id" value="<?php echo($id); ?>" />
					<?php 
					if($sidebar!=null){
						$title = $sidebar['title'];
						?>
						<div class="inner-sidebar">
							<div class="postbox">
								<h3><?php echo $sidebar['title']; ?></h3>
								<div class="inside">
									<?php 
									$this->fields($sidebar['fields'], $formSettings['isNew']);
									?>
								</div>
								<?php 
								$this->submitButton( $formSettings['submit']['label'] ,$formSettings['delete']['label'] ,$formSettings['delete']['action'] );
								?>
							</div>
						</div>
						<?php 
					}
					?>
					<div id="post-body">
						<div id="post-body-content">
							<?php 
							if(isset($formSettings['fields'])){
								$this->fields($formSettings['fields'], $formSettings['isNew']);
							}
							?>
						</div>
					</div>
				</div>
				<?php 
				if(is_null($sidebar)){
					$this->submitButton( $formSettings['submit']['label'] ,$formSettings['delete']['label'] ,$formSettings['delete']['action'] );
				}
				?>
			</form>
		</div>
		<?php
	}	
}

function adminDettaglioTipiOrgani(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
	$id = '0'.$_GET['id'];
	$id = intval($id);
	$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');
	$f = new adminFormBuilder();
	$f->build( array(
		'iconId'	=> 	'icon-edit-pages',
		'title'		=> 	'Gestione tipologia Organo di Governo',
		'action'	=> 	$_SERVER['REQUEST_URI'],
		'isNew'		=> 	$id == 0,
		'delete'	=> 	array(
							'action' 	=>	'?page='. TOSENDIT_PAFACILE_TIPO_ORGANO_EDIT_HANDLER,
							'label'		=>	'Annulla'
						),
		'submit'	=>	array(
							'label'		=>	'Salva'
						),
		'sidebar'	=> array(
							'title'		=> 'Tipologia',
							'fields'	=>	array(
												array(
													'id' =>				'codice',
													'label'			=>	'Codice:',
													'type'			=> 	'text',
													'value'			=> 	$row->codice,
													'maxlen'		=> 	10,
													'suggestion'	=>	"<strong>Attenzione: </strong> modificando questo dato si
																		perderà l'associazione con le tipologie di organo di governo 
																		nell'elenco pubblicato on-line e nell'elenco in area di amministrazione.
																		Questo comporterà che tutti gli organi dientificati dal codice
																		<strong>{$row->codice}</strong> <em>{$row->descrizione}</em>
																		non saranno più identificati correttamente.",
													'suggestOnNew'	=> true,
													'suggestOnEdit'	=> false
												)
											)
						
						),
		'fields'	=>	array(
							array(
								'id' =>				'descrizione',
								'label'			=>	'Descrizione:',
								'type'			=> 	'title',
								'value'			=> 	$row->descrizione
							)
						)
		)
	);
	exit();
}

adminDettaglioTipiOrgani();
?>