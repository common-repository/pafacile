/**
 * PAFacile jQuery scripts
 */

function rimuoviRiferimento(){
	var $ = jQuery;
	$('#pa_id_padre').val('0');
	$('#bando-selezionato').text( 'Nessun documento' );
}
	
function selezionaBando(id){
	var $ = jQuery;
	if(id==null){
		$('#cerca-bando').show();
	}else{
		$('#cerca-bando').hide();
		$('#pa_id_padre').val( id );
		$('#bando-selezionato').html( $('#bando-' +id).html() );
	}
}


function cercaBando(){
	var $ = jQuery;
	var btype = $('#src-tipo').val();
	$('#bandi-results').text( 'Caricamento in corso... attendere prego.' );
	
	$.post(ajaxurl, {
		action: 'lista_bandi',
		tipo: btype,
		rnd: Math.random()
	}, function(response) {
		$('#bandi-results').html( response );
	});			
}


jQuery(document).ready(function($){
	
	function caricaGiorniTipoAtto(){
		var codice = $('#pa_tipo').val();
		if(codice!=''){
			var gp = $('#giorni_pubblicazione');
			var canChange = false; 
			if(gp.val()=='' || gp.val()=='0'){
				canChange = true;
			}else{
				if(confirm('Vuoi impostare la scadenza predefinita per questo tipo di pubblicazione?')){
					canChange = true;
				}
			}
			if(canChange){
				$.post(ajaxurl, {
					action: 'giorni_atto',
					tipo: codice,
					rnd: Math.random()
				}, function(response) {
					gp.val(response);
				});
			}
		}
		
	}
	
	function statusChanged(){

		if($('#status-1').is(':checked')){
			$('#pubblicata_dal_dd, #pubblicata_dal_mm, #pubblicata_dal_yy').addClass('validator required');
		}else{
			$('#pubblicata_dal_dd, #pubblicata_dal_mm, #pubblicata_dal_yy').removeClass('validator required');
		}
		
		if($('#status-9').is(':checked')){
			$('#data-annullamento, #testo-annulla-atto').show('fast');
		}else{
			$('#data-annullamento, #testo-annulla-atto').hide('fast');
				
		}
		
		if($('#status-2').is(':checked')){
			$('#data-proroga').show('fast');
		}else{
			$('#data-proroga').hide('fast');
				
		}
		
		$('#save-button').text(
				$('#status-9').is(':checked')?'Annulla':
				$('#status-2').is(':checked')?'Proroga':''
		);	
	}

	function mostraMessaggioValidazione(html){
		$('#validator-msg').html(html);
		if(html!=''){
			$('#validator-msg').show('fast');
		}else{
			$('#validator-msg').hide('fast');
		}
	}
	
	function validaSingoloCampo(event){
		var html = '';
		if($(this).val() == ''){
			var fieldId = $(this).attr('id');
			var theLabel = fieldId; 
			if(fieldId != undefined){
				theLabel = $('label[for=' + fieldId + ']');
				if(theLabel.length>0) 
					theLabel = $(theLabel[0]).text();
				else
					theLabel = fieldId;
			}
			theLabel = theLabel.replace(/:$/, '');
			html += '<p>Il valore <strong>' + theLabel + '</strong> non è stato specificato!</p>';
		}
		
		if(event!=null){
			mostraMessaggioValidazione(html);
			event.preventDefault();
		}else{
			return html;
		}
	}
	
	function validazioneGenerica(event){
		var html = '';
		$('.validator.required',this).each(function(){
			html += validaSingoloCampo.apply(this);
		});
		if(html!=''){
			mostraMessaggioValidazione(html);
			event.preventDefault();
		}
	}
	
	$('#status-1, #status-2, #status-9').on('click', statusChanged);
	statusChanged();

	$('span.delete a').on('click', function(ev){
		if(!confirm('sei sicuro di voler eliminare questo documento?')){
			ev.preventDefault();
		}
	});
	
	$('#pa_tipo').on('change',caricaGiorniTipoAtto);
	
	
	$('#modulo-albo-pretorio').on('submit', validazioneGenerica);
	$('#modulo-albo-pretorio .validator').on('blur', validaSingoloCampo);
	
	
	/*
	 * Area di configurazione
	 */
	
	$("#pafacile-page-settings .nav-tab-wrapper a").on('click', function(event){
		$('a', $(this).parent()).not(this).each(function(){
			$($(this).attr('href')).hide();
		});
		$($(this).attr('href')).show();
		$("#pafacile-page-settings .nav-tab-wrapper a.nav-tab-active").removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		event.preventDefault();
	});

	$("#pafacile-page-settings .nav-tab-wrapper a:first").click();

	/*
	 * Area Bandi
	 */
	var cb = $('#cerca-bando');
	if(cb.length>0){
		cb.css({'zIndex': 999, 'position': 'fixed', 'width': '50%', 'backgroundColor': '#fff', 'border-width': '1px', 'border-color': '#ccc', 'border-style': 'solid'});
		cb.css('left', ($(window).width() - cb.outerWidth() )/2 );
		cb.append('<div id="cerca-bando-bc" class="button-container" />');
		$('#cerca-bando-bc').append('<a class="button-primary" href="#">Chiudi</a>');
		$('#cerca-bando-bc a').on('click', function(event){
			cb.hide();
			event.preventDefault();
		});
		cb.hide();
	}
	
	
});