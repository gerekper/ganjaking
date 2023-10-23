<?php
/**
 * Vendor electronic invoice options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Show and save option for the specific vendor
 */
$current_date = getdate();
$vendor       = yith_get_vendor( get_current_user_id(), 'user' );

$vendor_options = array(
	'vendor-electronic-invoice' => array(
		array(
			'name' => 'Impostazioni generali',
			'type' => 'title',
			'desc' => 'Il nome di ogni file XML che viene generato deve essere obbigatoriamente così composto<br>
                        <strong>Codice Paese | Identificativo univoco del Trasmittente _ Progressivo univoco del file</strong><br>
                       Il progressivo univoco del file è rappresentato da una stringa alfanumerica di lunghezza massima di 5 caratteri e con valori ammessi da "A" a "Z" e da "0" a "9".<br><br>
                       Nel nostro caso ad ogni file verrà assegnato un ID Progressivo univoco, composto da due lettere e tre numeri (Es. AA000)<br><br>
                       Le opzioni verranno aggiornate automaticamente dal sistema, non vi è necessità di modificarle
                       ',
		),
		'progressive_file_id_number' => array(
			'name'      => 'Progressivo numerico usato per comporre il nome del file',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_progressive_file_id_number' ),
			'desc'      => 'Numero che verrà incluso nel nome del prossimo file XML. ',
			'default'   => '0',
			'std'       => '0',
			'class'     => 'yith-disabled',
		),
		'progressive_file_id_letter' => array(
			'name'      => 'Progressivo in lettere usato per comporre il nome del file',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_progressive_file_id_letter' ),
			'desc'      => 'Lettere che verranno incluse nel nome del prossimo file XML. ',
			'default'   => 'AA',
			'std'       => 'AA',
			'class'     => 'yith-disabled',
		),
		array(
			'type' => 'sectionend',
		),
		array(
			'name' => 'Impostazioni dettagli del soggetto o dell\'azienda trasmittente',
			'type' => 'title',
		),
		'transmitter_id'             => array(
			'name'      => 'Codice fiscale azienda',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_transmitter_id' ),
			'desc'      => 'Inserisci il codice fiscale associato del soggetto o dell\'azienda che emette fattura. Il valore verrà usato come "Transmitter ID" nel file XML',
			'default'   => '',
		),
		'company_vat'                => array(
			'name'      => 'Partita IVA azienda',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_company_vat' ),
			'desc'      => 'Inserisci la partita IVA del soggetto o dell\'azienda che emette fattura',
			'default'   => '',
		),
		'fiscal_regime'              => array(
			'name'      => 'Regime fiscale',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_fiscal_regime' ),
			'desc'      => 'Imposta il regime fiscale legato al soggetto o all\'azienda che emette fattura',
			'options'   => array(
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
			'default'   => 'RF01',
		),
		'chargeability_vat'          => array(
			'name'      => 'Esigibilità IVA',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_chargeability_vat' ),
			'options'   => array(
				'I' => 'IVA ad esigibilità immediata',
				'D' => 'IVA ad esigibilità differita',
				'S' => 'Scissione dei pagamenti',
			),
			'default'   => 'I',
		),
		'natura'                     => array(
			'name'      => 'Natura',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_natura' ),
			'desc'      => "L'elemento serve per indicare il motivo (Natura dell'operazione) per il quale l'emittente della fattura non indica aliquota IVA",
			'options'   => array(
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
			'default'   => 'N4',
		),
		'company_registered_name'    => array(
			'name'      => 'Nome registrato azienda',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_company_registered_name' ),
			'desc'      => 'Inserisci il nome con cui l\'azienda è stata registrata',
			'default'   => '',
		),
		'company_address'            => array(
			'name'      => 'Indirizzo',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_company_address' ),
			'desc'      => 'Imposta l\'indirizzo della tua azienda',
			'default'   => '',
		),
		'company_cap'                => array(
			'name'      => 'CAP',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_company_cap' ),
			'desc'      => 'Imposta il CAP della tua azienda',
			'default'   => '',
		),
		'company_city'               => array(
			'name'      => 'Città dell\'azienda',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_company_city' ),
			'desc'      => 'Imposta la città della tua azienda',
			'default'   => '',
		),
		'company_province'           => array(
			'name'      => 'Provincia azienda',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_electronic_invoice_company_province' ),
			'desc'      => 'Inserisci la provincia della tua azienda',
			'options'   => wc()->countries->get_states( 'IT' ),
			'default'   => '',
		),
		array(
			'type' => 'sectionend',
		),
	),
);

return apply_filters( 'ywpi_vendor_electronic_options', $vendor_options );
