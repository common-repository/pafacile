=== PAFacile ===
Contributors: tosend.it
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FHL54KN64AHHU
Tags: albo pretorio, delibere, determine, ordinanze, organigramma, organi di governo, incarichi professionali, bandi di concorso, bandi di gara, graduatorie, google analytics 
Requires at least: 3.6
Tested up to: 3.9.1
Stable tag: 2.6.1
License: GPLv3

PAFacile è un plugin nato per consentire alle pubbliche amministrazione di gestire la trasparenza amministrativa secondo gli obblighi di legge.

== Description ==

PAFacile è un plugin sviluppato dalla [toSend.it](http://tosend.it) per venire incontro alle esigenze della Pubblica Amministrazione e degli Enti Locali creando uno strumento semplice da usare e facile da manutenere e intuitivo nella sua configurazione.

PAFacile consente una gestione puntuale dell'albo pretorio on-line, dei bandi, gare, concorsi e graduatorie, delle delibere di giunta e consiglio, delle determine d'ufficio, degli incarichi professionali, delle ordinanze, dell'organigramma e degli organi di governo fino alla pubblicazione degli OpenData

PAFacile recepisce le indicazioni del documento fornito dal Governo "linee guida per i requisiti minimi per i siti delle PA".

**Attenzione**: Per ottemperare agli obblighi nei confronti di AVCP (Legge 190/2013) è necessario migrare alla versione [Premium](http://www.pafacile.it)

= Cosa fa? =

PAFacile consente di pubblicare sul proprio sito istituzionale la maggior parte delle informazioni che ogni Pubblica Amministrazione deve presentare on-line.

Tra le funzionalità presenti :

* Gestione dell'albo pretorio on-line
* Gestione delle tipologie di atto disponibili nell'albo pretorio
* Gestione bandi, gare, concorsi e graduatorie
* Gestione delibere di giunta e consiglio
* Gestione delle determinazioni d'ufficio
* Gestione degli incarichi professionali
* Gestione ordinanze
* Gestione organigramma
* Gestione organi di governo
* Gestione delle tipologie di organi di governo disponibili
* Gestione di più incarichi governativi ricoperti dallo stesso soggetto
* Gestione dei livelli di accesso alle relative funzionalità direttamente dalla scheda utente
* Tabelle di monitoraggio delle statistiche di accesso tramite google analytics
* Diversi widget da poter integrare nel tuo template
* Un'alta personalizzazione
* Supporto al Doublin Core
* Gestione concessione delle sovvenzioni, contributi, sussidi ed ausili finanziari (DL 22 giugno 2012, n. 83 art. 18)  
* Gestione degli OpenData

== Installation ==

1. Effettuare il download del plugin
1. Scompattare il plugin nella directory `plugins` del tuo sito
1. Attivare il plugin
1. Accedere come administrator per eseguire le impostazioni di configurazione

== Frequently Asked Questions ==

= Come posso vedere PAFacile in funzione senza doverlo installare sul mio server? =
Accedere al [sito demo del plugin](http://pafacile.tosend.it/) 

Consultare la [Documentazione di PAFacile](http://tosend.it/prodotti/pafacile/documentazione)


== Screenshots ==

1. Una delle pagine di configuraizione di PAFacile
2. Particolare della scheda dell'albo pretorio on-line
3. Particolare della scheda della gestione dei bandi di gara, concorsi e graduatorie
4. Altro particolare della scheda di gestione per i Bandi di gara, concorsi e graduatorie

== Changelog ==

= 2.6.1 (2014-06-16) =
* **New**: Aggiunta la colonna Estremi alla tabella dei bandi in area amministrativa.
* **Update:** Aggiornato link alla [documentazione](http://www.pafacile.it/area-clienti/documentazione/utente/pafacile-free/)
* **Security:** Corretto il codice per evitare un attacco di tipo XSS (grazie a [Gianni Amato](http://www.guelfoweb.com)).
* **Security:** Corretto il codice per evitare una violazione della banca dati in area di amministrazione (grazie a [Gianni Amato](http://www.guelfoweb.com)).

= 2.6.0 (2013-02-09) =
* **New**: Introdotta **gestione degli OpenData**
* **New**: Aggiunti link alternativi nell'intestazione di pagina sugli opendata
* **New**: Esportazione di tutti i dati di PAFacile nel formato CSV (tramite OpenData)
* **New**: Nuova opzione per aggiungere collegamenti agli OpenData nelle opzioni dell'editor visuale.
* **New**: **13 Nuovi filtri** per gestire ed espandere gli opendata di PAFacile (leggi [documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/opendata/))
* **New**: Aggiunti elementi grafici per l'editor visuale relativi ai blocchi di tipo Sovvenzioni e per la modalità link agli OpenData per tutti i tipi
* **New**: Se per la configurazione di una specifica sezione non è stato indicata una pagina sulla quale presentare i dettagli, la sezione non sarà disponibile in area amministrativa  
* **Update**: Se un metadato di intestazione non è definito correttamente nella pagina di configurazione non lo mostro nell'header della relativa pagina
* **Update**: Rimosse tutte le invocazioni del comando PHP **error_log** presenti in PAFacile necessarie al solo fine di debug
* **Bugfix**: Risolto problema sulla cancellazione delle sovvenzioni

= 2.5.10 (2013-02-02) =
* **Update:** Aggiunto box dei file alla sezione pubblica delle sovvenzioni.  
* **Security:** Corretto il codice per evitare un attacco di tipo XSS (thanks to Dejan Lukan).

= 2.5.9 (2013-01-30) =
* **New:** In alcune configurazioni di mySQL il numero di registro dell'albo pretorio veniva duplicato ([leggi articolo](pafacile-versione-2-5-9-bugfix-urgente-per-numerazione-albo-pretorio)).

= 2.5.8 (2013-01-17) =
* Bugfix: Anche il gestore può salvare un nuovo atto da pubblicare nell'albo.

= 2.5.7 (2013-01-16) =
* Bugfix: Invocato erroneamente il metodo strstr al posto di strtr (bug introdotto con la versione 2.5.6). 

= 2.5.6 (2013-01-15) =
* Bugfix: Tutti gli utenti autenticati potevano vedere tutte le voci di menu.
* Bugfix: Corretto il filtro per cui il codice HTML non veniva corretto.

= 2.5.5 (2013-01-14) =
* Update: Aggiunto sul profilo utente opzione per l'abilitazione alla gestione delle sovvenzioni
* Update: Aggiornato il metodo con cui viene calcolato il numero di registro dell'albo pretorio

= 2.5.4 (2013-01-08) =
* La versione sul repository di Wordpress non risultava aggiornata!

= 2.5.3 (2013-01-08) =
* La versione sul repository di Wordpress non risultava aggiornata!

= 2.5.2 (2013-01-08) =
* **New:** Aggiunto link per la [donazione](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FHL54KN64AHHU)
* **New:** Aggiunta nuova modalità di filtro del Registro albo pretorio.
* **Bugfix:** La modalità di inclusione per il registro albo pretorio non manteneva la selezione
* **Bugfix:** Non veniva considerata l'opzione Mostra estremi nel widget causando sempre la visualizzazione degli estremi 

= 2.5.1 (2013-01-04) =
* **Update:** Migliorata la consultazione storica dei cambiamenti.
* **Update:** La sezione "termini" dei bandi di gara viene nascosta se non contiene informazioni.
* **Update:** Aggiunto ordinamento bandi per data pubblicazione e scadenza.
* **Update:** Aggiunta opzione "Esito" nell'elenco delle tipologie di documento in Bandi e Gare.
* **Bugfix:** Un errore di Javascript non consentiva la selezione di un documento collegato.

= 2.5.0 (2012-12-22) =
* **New:** Verificata compatibilità con Wordpress 3.5
* **New:** **Adempimento ai requisiti del DL 22 giugno 2012, n. 83 art. 18**
* **New:** Aggiunto il ruolo "Gestore Sovvenzioni"
* **New:** Aggiunta la tabella in banca dati pa_sovvenzioni
* **New:** Aggiunta la sezione Sovvenzioni, agevolazioni, contributi e sussidi
* **New:** Nuovi filtri e azioni per l'area Albo Pretorio ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/albo-pretorio/))
* **New:** Nuovi filtri e azioni per l'area Bandi e Gare ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/))
* **New:** Aggiunto filtro pafacile_sovvenzioni_menu.
* **New:** Definiti oltre 150 nuovi filtri ed azioni per la sezione Sovvenzioni.
* **New:** Aggiunto audit trail per la sezione Sovvenzioni, agevolazioni, contributi e sussidi 
* **Update:** Aggiornato il codice per compatibilità di PAFacile con TinyMCE.
* **Update:** Aggiornata la versione applicativa alla numero 2.5
* **Update:** Aggiornata la versione della banca dati alla numero 1.6.0 
* **Update:** Migliorata gestione pubblicazione nell'albo pretorio evitando l'annullamento di un atto ancor prima di essere pubblicato. 
* **Update:** Il box di notifica dell'errore sul dettaglio dell'albo viene presentato solo se esistono degli errori.
* **Update:** Spostato il codice Javascript dell'albo pretorio nel file di Javascript jq.pafacile.js
* **Update:** In fase di pubblicazione non è consentito di salvare l'atto se la data di pubblicazione non è specificata.
* **Update:** Il pulsante salva è disponibile solo se l'utente ha le giuste autorizzaizoni e l'atto è nello stato corretto.
* **Update:** Semplificata la pagina di configurazione
* **Update:** Aggiornata la documentazione per sviluppatori
* **Update:** Rimosso tutto i codice javascript non necessario.
* **Update:** Editor Visuale è possibile specificare l'opzione archivio per i bandi
* **Update:** Possibilità di mostrare gli estremi del bando in pubblicazione
* **Bugfox:** Configurazione Widget Bandi riportava erroneamente l'etichetta mostra data di pubblicazione anzichè mostra data esito.
* **Bugfix:** In caso di disattivazione della modalità privacy dell'albo pretorio gli atti scaduti risultavano ancora affissi all'albo.
* **Bugfix:** Passando dall'editor visuale all'editor HTML causava una rottura degli elementi di PAFacile in alcuni contesti.

= 2.4.8 (2012-11-30) =
* **New:** Aggiunta gestione dell'archivio bandi ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/creare-una-pagina-per-la-visualizzazione-dei-bandi-di-gara-concorso-e-graduatorie/) )
* **New:** Aggiunte opzioni al widget Bandi e Gare per personalizzarne l'aspetto ed il comportamento ([leggi la documentazione](http://tosend.it/prodotti/pafacile/documentazione/lavorare-sulla-presentazione-del-widget-bandi-gare/)).
* **New:** Aggiunta conferma prima della cancellazione di un qualsiasi documento.
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_tipo** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_estremi** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_oggetto** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_pubblicato_il** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_scade_il** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_aggiudicato_il** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **New:** Aggiunto il filtro **pafacile_bandi_etichetta_ufficio** ([leggi documentazione](http://tosend.it/prodotti/pafacile/documentazione/filtri/bandi-e-gare/)).
* **Update:** Migliorata l'interfaccia di visualizzazione degli ultimi aggiornamenti.
* **Update:** Aggiornato il CSS di amministrazione per l'aspetto della sezione ultimi cambiamenti.
* **Bugfix:** Corretto un bug introdotto nella versione 2.4.7 che in caso di utilizzo del parametro **itemId** su una qualsiasi pagina del sito non segnata come contenuto di PAFacile, mostrava l'incarico professionale indicato.

= 2.4.7 (2012-11-01) =
* **Bugfix:** Corretto un bug introdotto nella versione 2.4.6 che generava un errore fatale in visualizzazione dell'albo pretorio e degli incarichi professionali.

= 2.4.6 (2012-11-01) =
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Albo Pretorio
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Bandi
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Delibere
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Determine
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Incarichi Professionali
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Ordinanze
* **Update:** Possibilità di impostare parametri aggiuntivi nello shortcode per i Organi di Governo
* **Update:** Aggiornato plugin per l'editor visuale

* **Bugfix:** I bandi scaduti non devono essere visualizzati nell'elenco dei bandi in corso.
* **Bugfix:** Su internet explorer e firefox senza la console attiva il plugin generava degli errori di javascript nell'editor visuale.

= 2.4.5 (2012-10-30) =
* **Update:** Cambiato il tipo di dato da TINYTEXT a LONGTEXT per il campo descrizione nella tabella bandi. 
* **Bugfix:** Modificato *call_user_func_array* in *call_user_func* nella gestione del log attività.
* **Bugfix:** Il link al dettaglio dell'ufficio riportava un carattere errato nella sezione bandi.


= 2.4.4 (2012-10-13) =
* **New:** Aggiunto campo estremi bando
* **New:** Attivato audit trail per sezione Bandi di Gara
* **New:** La sezione allegati sull'organigramma viene visualizzata solo se esistono documenti allegati
* **New:** La sezione allegati sui bandi viene visualizzata solo se esistono documenti allegati
* **Update:** Aggiornata la versione del plugin e della banca dati
* **Update:** Migliorato il sistema di pulitura del markup in presenza di shortcode di PAFacile
* **Bugfix:** Rimosso un testo in eccesso nell'elenco e nel dettaglio dei bandi
* **Bugfix:** Corretto link al dettaglio dell'ufficio che ha pubblicato il documento nella sezione bandi.
* **Bugfix:** Migliorato il codice della sezione bandi per ovviare a diversi avvisi generati dal Webserver in alcuni contesti
* **Bugfix:** In organigramma un'etichetta si riferiva al controllo sbagliato.
* **Bugfix:** Migliorato il codice della sezione organigramma per ovviare a diversi avvisi generati dal Webserver in alcuni contesti
* **Bugfix:** Non venivano salvate due opzioni della scheda organigramma

= 2.4.3 (2012-09-03) =
* La versione sul repository di Wordpress non risultava aggiornata!
* Allineata la versione del plugin alla versione presente sul repository di Wordpress

= 2.4.2 (2012-09-03) =
* La versione sul repository di Wordpress non risultava aggiornata!

= 2.4.1 (2012-07-23) =
* **Bugfix:** Risoluzione problema sull'inserimento e la modifica nuovi bandi

= 2.4 (2012-06-18) =
* **New:** Adeguamento alla **delibera CiViT n. 3 del 2012** in riferimento alla sezione Incarichi Professionali.   
* **New:** Aggiunto log dei cambiamenti (audit trail) nella sezione incarichi professionali. 
* **New:** Inserito filtro *pafacile_bandi_gare_menu*
* **New:** Inserito filtro *pafacile_delibere_menu*
* **New:** Inserito filtro *pafacile_determinazioni_menu*
* **New:** Inserito filtro *pafacile_incarichi_menu*
* **New:** Inserito filtro *pafacile_ordinanze_menu*
* **New:** Inserito filtro *pafacile_organi_governo_menu*
* **New:** Inserito filtro *pafacile_organigramma_menu*
* **Update:** Migliorato il codice della sezione Albo Pretorio per ridurre il numero di variabili non definite che genravano dei **PHP Notice** in determinate condizioni.
* **Update:** Migliorato il codice della sezione Bandi per ridurre il numero di variabili non definite che genravano dei **PHP Notice** in determinate condizioni.
* **Update:** Migliorato il messaggio nel footer per i riconoscimenti.
* **Bugfix:** effettuata la verifica di alcune variabili che causavano dei warning in fase di attivazione in dipendenza del livello di avvisi configurato in PHP. 
* **Bugfix:** definite alcune variabili che causavano waring durante l'esecuzione del codice se il livello di avviso di PHP era impostato a **E_ALL**


= 2.3 (2012-03-31) =
* **New:** Inserito filtro *pafacile_albo_menu*
* **New:** Inserito filtro *pafacile_welcome_menu*
* **New:** Inserito filtro *pafacile_albopretorio_before_print_item*
* **New:** Inserito filtro *pafacile_albopretorio_after_print_item*
* **New:** La ricerca nel registro delle pubblicazioni in area amministrativa consente ora di specificare anche la tipologia di atto.
* **Update:** Migliorata la compatibilità con installazioni locali di XAMPP e WAMP 
* **Update:** La formattazione numerazione contatori nella tabella di monitoraggio adesso presenta il separatore delle migliaia a dei decimali.
* **Bugfix:** L'etichetta di gestione esclusiva PAFacile non puntava correttamente alla relativa casella di input. 
* **Bugfix:** Nei link per l'accesso rapido in modifica alla pagina creata da PAFacile, il link di amministrazione era corrotto.
* **Bugfix:** Corretto markup nella pagina delle informazioni di PAFacile
* **Bugfix:** Se non venvia specificato almeno un filtro il registro delle pubblicazioni albo pretorio risultava vuoto.
* **Bugfix:** La funzione di stampa della relata di pubblicazione riportava un carattere (:) in eccesso causando un non funzionamento della funzione di stampa.

= 2.2.2 (2012-01-10) =
* **Bugfix:** La stampa della certificazione di pubblicazione non funzionava

= 2.2.1 (2012-01-07) =
* **Bugfix:** Dopo il salvataggio di un atto nell'albo pretorio non risulta possibile modificarlo ulteriormente.

= 2.2 (2012-01-05) =
* **New:** **Rimossa la dipendenza da Role Scoper, il plugin non richiede più role scoper per funzionare.**
* **New:** Aggiunta procedura di allineamento ruoli utenti.
* **New:** La gestione delle abilitazioni ai moduli di PAFacile adesso si configura dalla pagina utente.
* **Update:** la pagina "Informazioni" del plugin è stata migliorata. Adesso riporta le informazioni sulle versioni delle librerie installate sul server e la loro compatibilità.
* **Update:** Ottimizzato il codice per non fare più uso di alcune funzioni deprecate del core di Wordpress.
* **Update:** Aggiornata la gestione dell'editor visuale secondo le specifiche di Wordpress 3.3
* **Update:** Migliorata l'interfaccia per la selezione degli shortcode.
* **Update:** Ottimizzazioni varie al codice per rendere più performante e rimuovere alcuni warning su indici e variabili non presenti.
* **Bugfix:** Problema con le tabelle sul monitoraggio delle statistiche di Google Analytics in frontend.
* **Bugfix:** Omessa l'inclusione del file per la gestione delle determinazioni in frontend.
* **Bugfix:** Chiamata errata della funzione hash_file()
* **Bugfix:** Omessa l'inclusione del file per la gestione delle delibere in frontend.
* **Bugfix:** Era impostata una chiave errata per l'acquisizione del permalink dei bandi sul widget. 
* **Bugfix:** Il numero di registro viene elaborato correttamente secondo il filtro *pafacile_albo_pretorio_numero_registro*.

= 2.1.2 (2011-12-04) =
* **Bugfix:** nel repository non era presente la directory google-analytics con la conseguenza che le statistiche non funzionavano.
* **Bugfix:** il numero di versione del plugin risultava ancora erroneamente 2.1 e non veniva notificato l'aggiornamento alla versione 2.1.1

= 2.1.1 (2011-12-03) =
* **Bugfix:** risoluzione problema *Undefined index: allowedRoles*
* **Bugfix:** risoluzione problema *Undefined index: action*
* **Bugfix:** risoluzione problema *Undefined index: page*
* **Bugfix:** rimosse alcune costanti doppiamente ridenifinte
* **Bugfix:** corretti alcuni refusi nei nomi di variabili  

= 2.1 (2011-12-02) =
* Verificata compatibilità con Wordpress 3.3   
* Aggiornata la versione database alla numero 1.4.7
* **New:** Aggiunto shortcode per monitoraggio statistiche sito web tramite Google Analytics
* **New:** Aggiunte opzioni per i box facoltativi sul organigramma sulla scheda di ciascun ufficio
* **New:** Aggiunto hash code del file nell'elenco degli allegati (informazione disponibile in frontend e backend)
* **New:** Aggiunto filtro do_save_incarico
* **New:** Aggiunto filtro do_save_delibera
* **New:** Aggiunto filtro do_save_tipo_atto
* **New:** Aggiunto filtro do_save_tipo_organo
* **New:** Aggiunto filtro do_save_albo_pretorio
* **New:** Aggiunto filtro do_save_ordinanza
* **New:** Aggiunto filtro do_save_organo
* **New:** Aggiunto filtro do_save_organo_rel
* **New:** Aggiunto filtro do_save_organigramma
* **New:** Aggiunto filtro do_save_utenti_organigramma
* **New:** Aggiunto filtro do_save_bando
* **New:** Aggiunto filtro do_save_audit_trail
* **New:** Aggiunto filtro pafacile_albo_pretorio_numero_registro
* **New:** Aggiunta azione pafacile_do_insert
* **New:** Aggiunta azione pafacile_do_update
* **New:** Aggiunta azione pafacile_do_save
* **New:** Aggiunta modalità di ricerca al registro albo pretorio
* **New:** Aggiunta opzione di inclusione di tutti gli atti con o senza certificazione di pubblicazione
* **Update:** Aggiornamento della documentazione tecnica introducendo i [filtri sul salvataggio](http://tosend.it/prodotti/pafacile/documentazione/filtri/salvataggio-dati/)  
* **Update:** Rimosso filtro di ricerca periodo predefinito su elenco pubblico bandi e gare
* **Update:** Rimosso filtro di ricerca periodo predefinito su elenco determine
* **Update:** Rimosso filtro di ricerca periodo predefinito su elenco delibere
* **Update:** Rimosso filtro di ricerca periodo predefinito su elenco ordinanze
* **Update:** Rimosse alcune regole di stile dai CSS di backend in quanto non più necessarie
* **Update:** Gli atti annullati sull'interfaccia pubblica espongono una classe "deleted" con cui è possibile personalizzarne l'aspetto dal CSS custom.
* **Update:** Gli atti annullati sull'interfaccia privata adesso vengono presentati con del testo barrato.
* **Update:** Migliorata la ricerca per data nel registro dell'albo pretorio.
* **Bugfix:* Stampa della relata punta a un link errato
* **Bugfix:* Alla prima attivazione di PAFacile, navigando in area pubblica, non essendo definito alcun permalink viene segnalato un Warning.
* **Bugfix:** Il metodo createMessage non si riferisce correttamente alla classe toSendItGenericMethods
* **Bugfix:** Modificato script di validazione che genera un errore di runtime durante la fase di validazione
* Migliorie al codice e ottimizzazioni varie

= 2.0.1 (2011-11-09) =
* Aggiunta opzione per i riconoscimenti nella pagina di configurazione di PAFacile
* Aggiunti alcuni screenshot di PAFacile

= 2.0 (2011-11-04) =
* Primo rilascio sul repository di Wordpress

== Upgrade Notice ==

= 2.6.0 =
**Attenzione:** Questa versione abilita automaticamente l'accesso agli OpenData, se non si ha intenzione 
di rendere disponibili i dati sul web si suggerisce di non aggiornare a questa versione. 

= 2.5.10 =
**NOTA:** Aggiornare immediatamente PAFacile se si sta utilizzando una versione precedente alla 2.5.10. 
È stata scoperta una vulnerabilità di tipo XSS per la quale un individuo potrebbe iniettare del codice 
Javascript in alcune delle pagine del sito veicolando eventuali codici malevoli verso gli utenti ignari.  

= 2.2.* =
Per gli utenti che provengono dalla versione 2.1 dopo l'aggiornamento devono eseguire la procedura di allineamento degli utenti dal pannello di PAFacile. 
