<?php
/**
 * Electronic invoice options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$current_date = getdate();

$countries = WC()->countries->get_countries();

$general_options = array(

	'electronic-invoice' => array(

		array(
			'name' => 'Impostazioni generali',
			'type' => 'title',
			'id'   => 'ywpi_impostazioni_generali',
			'desc' => __(
				'<span style="font-weight: 400">Il nome di ogni file XML che viene generato deve essere obbigatoriamente così composto:</span> 
					   Codice Paese | Identificativo univoco del Trasmittente _ Progressivo univoco del file.<span style="font-weight: 400"> Il progressivo univoco del file è rappresentato da una stringa alfanumerica di lunghezza massima di 5 caratteri e con valori ammessi da "A" a "Z" e da "0" a "9". 
					   Nel nostro caso ad ogni file verrà assegnato un ID Progressivo univoco, composto da due lettere e tre numeri (Es. AA000). Le opzioni verranno aggiornate automaticamente dal sistema, non vi è necessità di modificarle.</span>
                       ',
				'yith-woocommerce-pdf-invoice'
			),
		),

		'progressive_file_id_number'         => array(
			'name'    => 'Progressivo numerico usato per comporre il nome del file',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_progressive_file_id_number',
			'desc'    => 'Numero che verrà incluso nel nome del prossimo file XML. ',
			'default' => '0',
			'std'     => '0',
			'class'   => 'yith-disabled',
		),
		'progressive_file_id_letter'         => array(
			'name'    => 'Progressivo in lettere usato per comporre il nome del file',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_progressive_file_id_letter',
			'desc'    => 'Lettere che verranno incluse nel nome del prossimo file XML. ',
			'default' => 'AA',
			'std'     => 'AA',
			'class'   => 'yith-disabled',
		),
		array(
			'type' => 'sectionend',
		),


		array(
			'name' => 'Impostazioni dettagli del soggetto o dell\'azienda trasmittente',
			'type' => 'title',
		),
		/*
		'country_id' => array(
			'name' => 'ID del paese',
			'type' => 'text',
			'id' => 'ywpi_electronic_invoice_country_id',
			'desc' => 'Inserisce la sigla del paese',
			'default' => 'IT',
		),*/
		'transmitter_id'                     => array(
			'name'    => 'Codice fiscale azienda',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_transmitter_id',
			'desc'    => 'Inserisci il codice fiscale associato del soggetto o dell\'azienda che emette fattura. Il valore verrà usato come "Transmitter ID" nel file XML',
			'default' => '',
		),
		'company_vat'                        => array(
			'name'    => 'Partita IVA azienda',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_vat',
			'desc'    => 'Inserisci la partita IVA del soggetto o dell\'azienda che emette fattura',
			'default' => '',
		),
		/*
		'transmission_format' => array(
			'name' => 'Formato trasmissione',
			'type' => 'text',
			'id' => 'ywpi_electronic_invoice_transmission_format',
			'desc' => 'Imposta il formato di trasmissione del documento XML',
			'default' => 'FPR12',
		),*/
		'fiscal_regime'                      => array(
			'name'    => 'Regime fiscale',
			'type'    => 'select',
			'id'      => 'ywpi_electronic_invoice_fiscal_regime',
			'desc'    => 'Imposta il regime fiscale legato al soggetto o all\'azienda che emette fattura',
			'options' => array(
				'RF01' => 'Ordinario',
				'RF02' => 'Contribuenti minimi (art.1, c.96-117, L. 244/07)',
				'RF04' => 'Agricoltura e attività connesse e pesca (artt.34 e 34-bis, DPR 633/72)',
				'RF05' => 'Vendita sali e tabacchi (art.74, c.1, DPR. 633/72)',
				'RF06' => 'Commercio fiammiferi (art.74, c.1, DPR  633/72)',
				'RF07' => 'Editoria (art.74, c.1, DPR  633/72)',
				'RF08' => 'Gestione servizi telefonia pubblica (art.74, c.1, DPR 633/72)',
				'RF09' => 'Rivendita documenti di trasporto pubblico e di sosta (art.74, c.1, DPR  633/72) ',
				'RF10' => 'Intrattenimenti, giochi e altre attività di cui alla tariffa allegata al DPR 640/72 (art.74, c.6, DPR 633/72)',
				'RF11' => 'Agenzie viaggi e turismo (art.74-ter, DPR 633/72)',
				'RF12' => 'Agriturismo (art.5, c.2, L. 413/91)',
				'RF13' => 'Vendite a domicilio (art.25-bis, c.6, DPR  600/73)',
				'RF14' => 'Rivendita beni usati, oggetti d’arte, d’antiquariato o da collezione (art.36, DL 41/95) ',
				'RF15' => 'Agenzie di vendite all’asta di oggetti d’arte, antiquariato o da collezione (art.40-bis, DL 41/95)',
				'RF16' => 'IVA per cassa P.A. (art.6, c.5, DPR 633/72)',
				'RF17' => 'IVA per cassa (art. 32-bis, DL 83/2012)',
				'RF18' => 'Altro',
				'RF19' => 'Regime forfettario (art.1, c.54-89, L. 190/2014)',
			),
			'default' => 'RF01',
		),
		'chargeability_vat'                  => array(
			'name'    => 'Esigibilità IVA',
			'type'    => 'select',
			'id'      => 'ywpi_electronic_invoice_chargeability_vat',
			'options' => array(
				'I' => 'IVA ad esigibilità immediata',
				'D' => 'IVA ad esigibilità differita',
				'S' => 'Scissione dei pagamenti',
			),
			'default' => 'I',
		),
		'natura'                             => array(
			'name'    => 'Natura',
			'type'    => 'select',
			'id'      => 'ywpi_electronic_invoice_natura',
			'desc'    => "L'elemento serve per indicare il motivo (Natura dell'operazione) per il quale l'emittente della fattura non indica aliquota IVA",
			'options' => array(
				'N1'   => 'escluse ex art. 15',
				'N2.1' => 'Non soggette ad IVA ai sensi degli artt. da 7 a 7-septies del DPR 633/72',
				'N2.2' => 'Non soggette - altri casi',
				'N3.1' => 'Non imponibili - esportazioni',
				'N3.2' => 'Non imponibili - cessioni intracomunitarie',
				'N3.3' => 'Non imponibili - cessioni verso San Marino',
				'N3.4' => "Non imponibili - operazioni assimilate alle cessioni all'esportazione",
				'N3.5' => "Non imponibili - a seguito di dichiarazioni d'intento",
				'N3.6' => 'Non imponibili - altre operazioni che non concorrono alla formazione del plafond',
				'N4'   => 'Esenti',
				'N5'   => 'Regime del margine / IVA non esposta in fattura',
			),
			'default' => 'N4',
		),
		'company_registered_name'            => array(
			'name'    => 'Nome registrato azienda',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_registered_name',
			'desc'    => 'Inserisci il nome con cui l\'azienda è stata registrata',
			'default' => '',
		),
		'transmitter_name'                   => array(
			'name'    => 'Nome persona fisica',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_transmitter_name',
			'desc'    => 'Inserisci il nome della persona fisica solo nel caso in cui non esista un nome registrato per l\'azienda',
			'default' => '',
		),
		'transmitter_lastname'               => array(
			'name'    => 'Cognome persona fisica',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_transmitter_lastname',
			'desc'    => 'Inserisci il cognome della persona fisica solo nel caso in cui non esista un nome registrato per l\'azienda',
			'default' => '',
		),
		'company_phone'                      => array(
			'name'    => 'Contatto telefonico',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_phone',
			'desc'    => 'Inserisci il numero di telefono associato all\'azienda',
			'default' => '',
		),
		'company_email'                      => array(
			'name'    => 'Indirizzo email',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_email',
			'desc'    => 'Inserisci l\'indirizzo di posta elettronica associata all\'azienda',
			'default' => '',
		),
		'company_address'                    => array(
			'name'    => 'Indirizzo',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_address',
			'desc'    => 'Imposta l\'indirizzo della tua azienda',
			'default' => '',
		),
		'company_cap'                        => array(
			'name'    => 'CAP',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_cap',
			'desc'    => 'Imposta il CAP della tua azienda',
			'default' => '',
		),
		'company_city'                       => array(
			'name'    => 'Città dell\'azienda',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_company_city',
			'desc'    => 'Imposta la città della tua azienda',
			'default' => '',
		),
		'company_province'                   => array(
			'name'    => 'Provincia azienda',
			'type'    => 'select',
			'id'      => 'ywpi_electronic_invoice_company_province',
			'desc'    => 'Inserisci la provincia della tua azienda',
			'options' => wc()->countries->get_states( 'IT' ),
			'default' => '',
		),

		// 'company_state' => array(
		// 'name' => 'Company state',
		// 'type' => 'text',
		// 'id' => 'ywpi_electronic_invoice_company_state',
		// 'desc' => 'Set the state of your company',
		// 'default' => '',
		// ),
		// 'include-tracking-info' => array(
		// 'name' => 'Include tracking info',
		// 'type' => 'checkbox',
		// 'id' => 'ywpi_electronic_invoice_include_tracking_info',
		// 'desc' => 'Enable this field to include tracking fields inside the XML document. To fill these fields you\'ll have to custom code an integration of these data.',
		// 'default' => 'no',
		// ),
			array(
				'type' => 'sectionend',
			),

		array(
			'name' => 'Terzo intermediario',
			'type' => 'title',
		),

		'third_intermediary'                 => array(
			'name'      => 'Terzo intermediario',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_electronic_invoice_third_intermediary',
			'desc'      => 'Abilita questo campo solo nel caso in cui vi sia un soggetto che emette fattura per conto del cedente/prestatore',
			'default'   => 'no',
		),
		'third_intermediary_vat'             => array(
			'name'      => 'Numero Partita Iva',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_electronic_invoice_third_intermediary_vat',
			'desc'      => 'Codice identificativo fiscale del soggetto terzo intermediario',
			'default'   => '',
		),
		'third_intermediary_country'         => array(
			'name'      => 'Nazione',
			'id'        => 'ywpi_electronic_invoice_third_intermediary_country',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => $countries,
			'default'   => 'IT',
			'desc'      => 'Codice identificativo fiscale soggetto terzo intermediario',
		),
		'third_intermediary_ssn'             => array(
			'name'    => 'Numero Codice Fiscale',
			'id'      => 'ywpi_electronic_invoice_third_intermediary_ssn',
			'type'    => 'text',
			'default' => '',
			'desc'    => 'Numero di Codice Fiscale del soggetto terzo intermediario',
		),
		'third_intermediary_registred_name'  => array(
			'name'    => 'Denominazione',
			'id'      => 'ywpi_electronic_invoice_third_intermediary_registred_name',
			'type'    => 'text',
			'default' => '',
			'desc'    => 'Ditta, denominazione o ragione sociale (ditta, impresa, società, ente), da valorizzare in alternativa ai campi nome e cognome per il soggetto terzo intemediario',
		),
		'third_intermediary_name'            => array(
			'name'    => 'Nome',
			'id'      => 'ywpi_electronic_invoice_third_intermediary_name',
			'type'    => 'text',
			'default' => '',
			'desc'    => 'Nome della persona fisica che funge da terzo intemediario. Da valorizzare in assenza di valore per il campo "Denominazione"',
		),
		'third_intermediary_lastname'        => array(
			'name'    => 'Cognome',
			'id'      => 'ywpi_electronic_invoice_third_intermediary_lastname',
			'type'    => 'text',
			'default' => '',
			'desc'    => 'Cognome della persona fisica che funge da terzo intemediario. Da valorizzare in assenza di valore per il campo "Denominazione"',
		),
		'third_intermediary_qualification'   => array(
			'name'    => 'Titolo',
			'id'      => 'ywpi_electronic_invoice_third_intermediary_qualification',
			'type'    => 'text',
			'default' => '',
			'desc'    => 'Titolo onorifico del soggetto terzo intermediario',
		),
		'third_intermediary_codeori'         => array(
			'name'    => 'Numero del Codice EORI ',
			'id'      => 'ywpi_electronic_invoice_third_intermediary_codeori',
			'type'    => 'text',
			'default' => '',
			'desc'    => 'Numero del Codice EORI (Economic Operator Registration and Identification) in base al Regolamento (CE) n. 312 del 16 Aprile 2009. In vigore dal 1 luglio 2009',
		),

		array(
			'type' => 'sectionend',
		),



		array(
			'name' => 'Impostazioni Checkout',
			'type' => 'title',
		),
		'receiver-type-label'                => array(
			'name'    => 'Etichetta per Tipologia Utente',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_type',
			'desc'    => 'Seleziona l\'etichetta da mostrare per il campo Tipologia Utente',
			'default' => 'Tipologia utente',
		),
		'receiver-id-label'                  => array(
			'name'    => 'Etichetta Codice Destinatario',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_id_label',
			'desc'    => 'Seleziona l\'etichetta da mostrare per il campo Codice Destinatario',
			'default' => 'Receiver ID',
		),
		'receiver-pec-label'                 => array(
			'name'    => 'Etichetta PEC Destinatario',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_pec_label',
			'desc'    => 'Seleziona l\'etichetta da mostrare per il campo PEC Destinatario',
			'default' => 'PEC Destinatario',
		),
		'receiver-mandatory-id-pec-message'  => array(
			'name'    => 'Messaggio Codice Destianatario/PEC obbligatori',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_mandatory_id_pec_message',
			'desc'    => 'Inserisci il messaggio di errore da mostrare quando Codice Destinatario e/o PEC dell\'utente non sono stati inseriti e diventano obbligatori',
			'default' => 'Inserisci almeno uno tra i campi Codice Destinatario e PEC',
		),
		'receiver-mandatory-company-message' => array(
			'name'    => 'Messaggio Intestazione Azienda obbligatoria',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_mandatory_company_message',
			'desc'    => 'Inserisci il messaggio di errore da mostrare quando l\'intestazione azienda dell\'utente non è stata inserita e diventa obbligatoria',
			'default' => 'Inserisci l\'intesazione della tua azienda',
		),
		'receiver-mandatory-vat-message'     => array(
			'name'    => 'Messaggio Partita IVA obbligatoria',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_mandatory_vat_message',
			'desc'    => 'Inserisci il messaggio di errore da mostrare quando il campo Partita IVA diventa obbligatorio',
			'default' => 'Partita IVA è un campo obbligatorio',
		),
		'receiver-mandatory-ssn-message'     => array(
			'name'    => 'Messaggio Codice Fiscale obbligatorio',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_mandatory_ssn_message',
			'desc'    => 'Inserisci il messaggio di errore da mostrare quando il campo Codice Fiscale diventa obbligatorio',
			'default' => 'Codice fiscale è un campo obbligatorio',
		),
		'receiver-wrong-id-message'          => array(
			'name'    => 'Messaggio Codice Destinatario errato',
			'type'    => 'textarea',
			'id'      => 'ywpi_electronic_invoice_receiver_wrong_id_message',
			'desc'    => 'Inserisci il messaggio di errore da mostrare quando il campo Codice Destinatario è errato',
			'default' => 'Il codice destinatario inserito non è corretto. Verifica di non aver non aver inserito al suo posto il codice fiscale o il numero di partita IVA della tua azienda',
		),
		'receiver-wrong-ssn-message'         => array(
			'name'    => 'Messaggio Codice Fiscale errato',
			'type'    => 'text',
			'id'      => 'ywpi_electronic_invoice_receiver_wrong_ssn_message',
			'desc'    => 'Inserisci il messaggio di errore da mostrare quando il campo Codice Fiscale è errato',
			'default' => 'Il codice fiscale inserito non è corretto',
		),
		'general-description'                => array(
			'type'      => 'yith-field',
			'yith-type' => 'html',
			'html'      => __( 'We recommend to carefully verify the correct data provided, to generate the invoice. The plugin\'s authors refuse any responsibility about possible mistakes or shortcomings when generating invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		array(
			'type' => 'sectionend',
		),
	),
);


return apply_filters( 'ywpi_electronic_options', $general_options );
