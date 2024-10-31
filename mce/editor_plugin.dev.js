/**
 * PAFacile Plugin
 */
function pafacilemceplugin_loader_temp__() {
	tinymce.create('tinymce.plugins.PAFacile', {
    	url: '',
    	visualCode: '<hr ' +
					'style="display: block; height: 32px; border: 1px dotted #ccc; ' +
					'background-image: url(\'%url%/img/%type%_%aspect%.png\');' +
					'background-repeat: no-repeat;" ' +
					'title="%shortcode%" /><br />',
    	visualCodeStatistiche: '<hr ' +
					'style="display: block; height: 32px; border: 1px dotted #ccc; ' +
					'background-image: url(\'%url%/img/%type%.png\');' +
					'background-repeat: no-repeat;" ' +
					'title="%shortcode%" />',
					
    	shortCodeRegExp:/(\[PAFacile ([a-z]+)([^\]]+)?\])/i,
    	
    	is: function(n){
    		var regExp = tinyMCE.activeEditor.plugins.PAFacile.shortCodeRegExp;
    		if(n==null) return false;
    		if(n.nodeName.toLowerCase() != 'hr') return false;
    		return regExp.test(decodeURI(n.title));
    		
    	},
    	getVisual: function(url, shortcode, type, aspect){
    		// le statistiche e gli altri atti hanno un visual shortcode differente
    		
    		var visualCode = (type==='statistiche')?
    				tinyMCE.activeEditor.plugins.PAFacile.visualCodeStatistiche:
					tinyMCE.activeEditor.plugins.PAFacile.visualCode;
    		visualCode = visualCode.replace('%url%', url);
    		if(aspect.indexOf(',')!=-1){
    			aspect = aspect.substring(0, aspect.indexOf(','));
    		}
    		if(aspect.indexOf(' ')!=-1){
    			aspect = aspect.substring(0, aspect.indexOf(' '));
    		}
    		visualCode = visualCode.replace('%shortcode%', encodeURI(shortcode) );
    		visualCode = visualCode.replace('%type%', type);
    		visualCode = visualCode.replace('%aspect%', aspect);
    		return visualCode;
    	},
    	getInfo : function() {
			return {
				longname : 'PAFacile',
				author : 'toSend.it di Luisa Marra',
				authorurl : 'http://toSend.it',
				infourl : 'http://toSend.it/prodotti/pafacile',
				version : "1.1"
			};
		},
		
        init : function(ed, url) {
			ed.plugins.PAFacile.url = url;
			ed.addButton('PAFacile', {
                title : 'Inserisci un elemento di PAFacile',
                image : url+'/img/albo.png',
                onclick : function() {
		    		var template = new Array() , value = "";
					
					var theNode = ed.selection.getNode();
					var 
						width = jQuery(window).width(),
						W = ( 720 < width ) ? 720 : width;
					W = W - 80;
					tb_show( 'Gestione elemento PAFacile', '#TB_inline?width=' + W + '&height=300&inlineId=pafacile-mce-form' );
					
					if (ed.plugins.PAFacile.is(theNode) ) {
						
						
						if (theNode) {
							value = decodeURI(theNode.getAttribute('title') ? theNode.getAttribute('title') : "");
							
							// Rimuovo le parentesi quadre e divido lo shortcode in base agli spazi
							var elements = value.replace(/(\[|\])/g,'').split(' ');
							
							var azione 	= elements[2];
							var tipo 	= elements[1];
							
							jQuery('#pafacile-mce-type').val(tipo);
							if(tipo === 'statistiche'){
								
								jQuery('#pafacile-mce-giorni').val(azione);
								
							}else{
								
								jQuery('#pafacile-mce-action').val(azione);
								
							}
							
						}
						// TODO: Impostare i dati
					} else {			
						var inst = tinyMCE.activeEditor;
						var	st = inst.selection.getSel();
						var html = '', value = '';
					}
						
					
					jQuery('#pafacile-mce-type').change();
					

                }
            });
	    	ed.onNodeChange.add(function(ed, cm, n) {
	    		cm.setActive('PAFacile', ed.plugins.PAFacile.is(n));
            });
	    	
	    	// Viene eseguita quando si passa dall'aspetto visuale al contesto HTML.
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					// Corretto per situazioni anomale che causavano la corruzione del plugin nello switch da editor Visuale ad HTML
					o.content = o.content.replace(/<hr[^>]+title\="(%5BPAFacile.*?%5D)"[^>]+>/g, function(hr) {
						
						hr = hr.replace(/<hr[^>]+title\="(.*)".*>/i,'$1');
						hr = decodeURI(hr);
						return hr;
					});
			});
			ed.onLoadContent.add(function(ed, o) {

			});
	    	// Viene eseguita quando si passa dal contesto HTML all'aspetto visuale.
			ed.onBeforeSetContent.add(function(ed, o) {
				 
				if ( o.content ) {
					o.content = o.content.replace(/(<p>)?\[PAFacile([^\]]+)\](<\/p>)?/g, function(shortcode){
						var firstP = 	/^<p>/g,
							lastP = 	/<\/p>$/g,
							startsWithP = (shortcode != shortcode.replace(firstP, '')),
							endsWithP	= (shortcode != shortcode.replace(lastP, '')),
							postFix 	= '';
							
						
						if( endsWithP && startsWithP ){
							// Se lo shortcode è contornato da paragrafi devo rimuoverlo
							shortcode = shortcode.replace(firstP, '');
							shortcode = shortcode.replace(lastP, '');
						}else if(startsWithP){
							// C'è solo il tag iniziale, ma non c'è il tag finale.
							shortcode = shortcode.replace(firstP, '');
							postFix = '<p>';
						}else if(endsWithP){
							shortcode = shortcode.replace(lastP, '');
						}
						var regExp = tinyMCE.activeEditor.plugins.PAFacile.shortCodeRegExp;
						
						scElements = regExp.exec(shortcode);
						var type= '', aspect = '';
						if(scElements.length>2) type = scElements[2];
						if(scElements.length>3) aspect = scElements[3].trim();
						
						
						var out = tinyMCE.activeEditor.plugins.PAFacile.getVisual(
								tinyMCE.activeEditor.plugins.PAFacile.url,
								shortcode,
								type,
								aspect
							);	
						return out+postFix;
					});
				}
			});
			

        },
        createControl : function(n, cm) {
        	return null;
        },
        execCommand: function(editor_id, element, command, user_interface, value) {
    		// Handle commands
    		switch (command) {
    			case "PAFacile":
    				
    				return true;

    		}
        }
    });
    tinymce.PluginManager.add('PAFacile', tinymce.plugins.PAFacile);

    
    jQuery(function(){
		var theDivContainer = jQuery('<div id="pafacile-mce-form"></div>'); 
		theDivContainer.appendTo('body').hide();
    	
    	var form = jQuery("#pafacile-mce-form").load(
    		ajaxurl, 
    		{
				action: 'shortcode',
				rnd: Math.random()
			},
			function(){
				form.find('#pafacile-mce-type').change(function(){
					switch(this.value){
						case 'statistiche':
							jQuery('#pafacile-mce-aspetto').hide();
							jQuery('#pafacile-mce-bandi').hide();
							jQuery('#pafacile-mce-statistiche').show();
							break;
						
						case 'bandi':
							
							// Since v. 2.5 - Gestione opzione per i bandi
							jQuery('#pafacile-mce-bandi').show();
							jQuery('#pafacile-mce-aspetto').show();
							jQuery('#pafacile-mce-statistiche').hide();
							break;
						default:
							jQuery('#pafacile-mce-bandi').hide();
							jQuery('#pafacile-mce-aspetto').show();
							jQuery('#pafacile-mce-statistiche').hide();
							break;
					
					}
					
					if('statistiche' === this.value){
					}else{
					}
				});
				form.find('#pafacile-mce-submit').click(function(){
					var tipo = jQuery('#pafacile-mce-type').val();
					var azione = jQuery('#pafacile-mce-action').val();
					var giorni = jQuery('#pafacile-mce-giorni').val();

					var shortcode = '[PAFacile ';
					shortcode += tipo;
					
					if(tipo!='statistiche'){ 
						shortcode += ' ' + azione;
						
						if(tipo == 'bandi'){
							
							// Since ver 2.5
							if(jQuery('#bandi-archive').is(':checked')) shortcode += ' archive="y"'; 

						}
						
					}else{
						shortcode += ' ' + giorni;
						azione = giorni;
					}
					shortcode += ']';
					var visualCode = 
						tinyMCE.activeEditor.plugins.PAFacile.getVisual(
							tinyMCE.activeEditor.plugins.PAFacile.url,
							shortcode,
							tipo,
							azione
						);
					
					// inserts the shortcode into the active editor
					tinyMCE.activeEditor.execCommand('mceInsertContent', 0, visualCode);
					
					// closes Thickbox
					tb_remove();
				});
			}
    	);
		
		// handles the click event of the submit button
    });
    
};



pafacilemceplugin_loader_temp__();
