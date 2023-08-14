<?php

/**
 * SearchWP Stopwords.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Tokens;

/**
 * Class Stopwords is responsible for removing stopwords from Tokens.
 *
 * @since 4.0
 */
class Stopwords {

	/**
	 * The list of stopwords.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $list = [];

	/**
	 * The language code.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $language_code;

	/**
	 * Stopwords constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		$this->language_code = strtolower( substr( get_locale(), 0, 2 ) );

		// TODO: Build in support for multilanguage setups (WPML, Polylang, soon to be core).

		// Remove stopwords from tokens when they're generated.
		add_filter( 'searchwp\tokens', [ $this, 'remove_from_tokens' ], 9 );
	}

	/**
	 * Updates saved stopwords.
	 *
	 * @since 4.0
	 * @param array $stopwords Stopwords to save.
	 * @return array Saved Stopwords.
	 */
	public function save( array $stopwords = [] ) {
		$stopwords = array_map( 'sanitize_text_field', $stopwords );

		\SearchWP\Settings::update( 'stopwords', $stopwords );

		return $stopwords;
	}

	/**
	 * Removes stopwords from tokens.
	 *
	 * @since 4.0
	 * @param array $tokens The tokens
	 * @return array The tokens without stopwords.
	 */
	public function remove_from_tokens( array $tokens ) {
		$this->set_list();

		return array_diff( $tokens, $this->list );
	}

	/**
	 * Sets the list of Stopwords.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_list() {
		// Give priority to saved Stopwords.
		$saved      = \SearchWP\Settings::get( 'stopwords' );
		$this->list = false === $saved ? $this->get_default() : $saved;

		$this->list = apply_filters( 'searchwp\stopwords', array_values( $this->list ) );
	}

	/**
	 * Getter for stopwords.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public function get() {
		if ( empty( $this->list ) ) {
			$this->set_list();
		}

		return $this->list;
	}

	/**
	 * Getter for suggested stopwords. Determined by calculating an overall prevalence in the index
	 * as opposed to evaluating the total number of occurrences. We care more about how common stopwords
	 * are throughout the entire index. A single entry that has the same word many times doesn't satisfy.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public function get_suggestions( $args ) {
		global $wpdb;

		if ( isset( $args['exclude'] ) && ! empty( $args['exclude'] ) ) {
			$tokens = new Tokens( $args['exclude'] );
			$args['exclude'] = \SearchWP\Utils::map_token_ids( $tokens->get() );
		}

		$args = wp_parse_args( $args, [
			'limit'     => 20,
			'threshold' => 0.3,
			'exclude'   => [''],
		] );

		$args = [
			'limit'     => absint( $args['limit'] ),
			'threshold' => floatval( $args['threshold'] ),
			'exclude'   => array_map( 'sanitize_text_field', (array) $args['exclude'] ),
		];

		$threshold = absint( $args['threshold'] * 100 );
		$index     = \SearchWP::$index;
		$values    = [ $index->get_count_entries() ];
		$where     = [ '1=1' ];

		if ( ! empty( $args['exclude'][0] ) ) {
			$where[] = 'i.token NOT IN (' . implode( ',', array_fill( 0, count( $args['exclude'] ), '%d' ) ) . ')';
			$values  = array_merge( $values, array_keys( $args['exclude'] ) );
		}

		$values = array_merge( $values, [ $threshold, $args['limit'] ] );

		$suggested_stopwords = $wpdb->get_results( $wpdb->prepare( "
			SELECT t.id AS id,
				t.token AS token,
				( ( SELECT COUNT(DISTINCT i.id, i.source) AS occurrences
					FROM {$index->get_tables()['index']->table_name} i
					WHERE i.token = t.id
					GROUP BY i.token ) / ( %d ) * 100
				) AS prevalence
			FROM {$index->get_tables()['tokens']->table_name} t
			LEFT JOIN {$index->get_tables()['index']->table_name} i ON i.token = t.id
			WHERE " . implode( ' AND ', $where ) . "
			GROUP BY t.token
			HAVING prevalence >= %d
			ORDER BY prevalence DESC LIMIT %d",
			$values ),
			ARRAY_A
		);

		foreach ( $suggested_stopwords as $i => $suggested_stopword ) {
			$suggested_stopwords[ $i ] = [
				'id'         => absint( $suggested_stopword['id'] ),
				'token'      => $suggested_stopword['token'],
				'prevalence' => number_format( floatval( $suggested_stopword['prevalence'] ), 2 ),
			];
		}

		return $suggested_stopwords;
	}

	/**
	 * Retrieve all default stopwords.
	 *
	 * @since 4.0
	 * @return array The stopwords for this language code.
	 */
	function get_default() {
		$defaults = array(
			'cs' => array(
				"aby", "aj", "ale", "ani", "asi", "az", "bez", "bude", "budem",
				"budes", "by", "byl", "byla", "byli", "bylo", "byt", "ci",
				"clanek", "clanku", "clanky", "co", "coz", "cz", "dalsi", "design",
				"dnes", "do", "email", "ho", "jak", "jako", "je", "jeho", "jej",
				"jeji", "jejich", "jen", "jeste", "ji", "jine", "jiz", "jsem",
				"jses", "jsme", "jsou", "jste", "kam", "kde", "kdo", "kdyz", "ke",
				"ktera", "ktere", "kteri", "kterou", "ktery", "ma", "mate", "mezi",
				"mi", "mit", "muj", "muze", "na", "nad", "nam", "napiste", "nas",
				"nasi", "ne", "nebo", "nejsou", "neni", "nez", "nic", "nove",
				"novy", "od", "pak", "po", "pod", "podle", "pokud", "pouze",
				"prave", "pred", "pres", "pri", "pro", "proc", "proto", "protoze",
				"prvni", "pta", "re", "si", "strana", "sve", "svych", "svym",
				"svymi", "ta", "tak", "take", "takze", "tato", "tedy", "tema",
				"ten", "tento", "teto", "tim", "timto", "tipy", "to", "tohle",
				"toho", "tohoto", "tom", "tomto", "tomuto", "tu", "tuto", "ty",
				"tyto", "uz", "vam", "vas", "vase", "ve", "vice", "vsak", "za",
				"zda", "zde", "ze", "zpet", "zpravy",
			),
			'da' => array(
				"af", "alle", "andet", "andre", "at", "begge", "da", "de", "den",
				"denne", "der", "deres", "det", "dette", "dig", "din", "dog",
				"du", "ej", "eller", "en", "end", "ene", "eneste", "enhver", "et",
				"fem", "fire", "flere", "fleste", "for", "fordi", "forrige", "fra",
				"få", "før", "god", "han", "hans", "har", "hendes", "her", "hun",
				"hvad", "hvem", "hver", "hvilken", "hvis", "hvor", "hvordan",
				"hvorfor", "hvornår", "i", "ikke", "ind", "ingen", "intet", "jeg",
				"jeres", "kan", "kom", "kommer", "lav", "lidt", "lille", "man",
				"mand", "mange", "med", "meget", "men", "mens", "mere", "mig",
				"ned", "ni", "nogen", "noget", "ny", "nyt", "nær", "næste",
				"næsten", "og", "op", "otte", "over", "på", "se", "seks", "ses",
				"som", "stor", "store", "syv", "ti", "til", "to", "tre", "ud",
				"var",
			),
			'de' => array(
				"aber", "als", "am", "an", "auch", "auf", "aus", "bei", "bin",
				"bis", "bist", "da", "dadurch", "daher", "darum", "das", "daß",
				"dass", "dein", "deine", "dem", "den", "der", "des", "dessen",
				"deshalb", "die", "dies", "dieser", "dieses", "doch", "dort", "du",
				"durch", "ein", "eine", "einem", "einen", "einer", "eines", "er",
				"es", "euer", "eure", "für", "hatte", "hatten", "hattest", "hattet",
				"hier", "hinter", "ich", "ihr", "ihre", "im", "in", "ist", "ja",
				"jede", "jedem", "jeden", "jeder", "jedes", "jener", "jenes",
				"jetzt", "kann", "kannst", "können", "könnt", "machen", "mein",
				"meine", "mit", "muß", "mußt", "musst", "müssen", "müßt", "nach",
				"nachdem", "nein", "nicht", "nun", "oder", "seid", "sein", "seine",
				"sich", "sie", "sind", "soll", "sollen", "sollst", "sollt", "sonst",
				"soweit", "sowie", "und", "unser", "unsere", "unter", "vom", "von",
				"vor", "wann", "warum", "was", "weiter", "weitere", "wenn", "wer",
				"werde", "werden", "werdet", "weshalb", "wie", "wieder", "wieso",
				"wir", "wird", "wirst", "wo", "woher", "wohin", "zu", "zum", "zur",
				"über",
			),
			'en' => array(
				"a", "about", "above", "after", "again", "against", "all", "am",
				"an", "and", "any", "are", "aren't", "as", "at", "be", "because",
				"been", "before", "being", "below", "between", "both", "but", "by",
				"can't", "cannot", "could", "couldn't", "did", "didn't", "do",
				"does", "doesn't", "doing", "don't", "down", "during", "each",
				"few", "for", "from", "further", "had", "hadn't", "has", "hasn't",
				"have", "haven't", "having", "he", "he'd", "he'll", "he's", "her",
				"here", "here's", "hers", "herself", "him", "himself", "his", "how",
				"how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into",
				"is", "isn't", "it", "it's", "its", "itself", "let's", "me", "more",
				"most", "mustn't", "my", "myself", "no", "nor", "not", "of", "off",
				"on", "once", "only", "or", "other", "ought", "our", "ours",
				"ourselves", "out", "over", "own", "same", "shan't", "she", "she'd",
				"she'll", "she's", "should", "shouldn't", "so", "some", "such",
				"than", "that", "that's", "the", "their", "theirs", "them",
				"themselves", "then", "there", "there's", "these", "they", "they'd",
				"they'll", "they're", "they've", "this", "those", "through", "to",
				"too", "under", "until", "up", "very", "was", "wasn't", "we",
				"we'd", "we'll", "we're", "we've", "were", "weren't", "what",
				"what's", "when", "when's", "where", "where's", "which", "while",
				"who", "who's", "whom", "why", "why's", "with", "won't", "would",
				"wouldn't", "you", "you'd", "you'll", "you're", "you've", "your",
				"yours", "yourself", "yourselves",
			),
			'es' => array(
				"alguna", "algunas", "alguno", "algunos", "algún", "ambos",
				"ampleamos", "ante", "antes", "aquel", "aquellas", "aquellos",
				"aqui", "arriba", "atras", "bajo", "bastante", "bien", "cada",
				"cierta", "ciertas", "cierto", "ciertos", "como", "con",
				"conseguimos", "conseguir", "consigo", "consigue", "consiguen",
				"consigues", "cual", "cuando", "dentro", "desde", "donde", "dos",
				"el", "ellas", "ellos", "empleais", "emplean", "emplear", "empleas",
				"empleo", "en", "encima", "entonces", "entre", "era", "eramos",
				"eran", "eras", "eres", "es", "esta", "estaba", "estado", "estais",
				"estamos", "estan", "estoy", "fin", "fue", "fueron", "fui",
				"fuimos", "gueno", "ha", "hace", "haceis", "hacemos", "hacen",
				"hacer", "haces", "hago", "incluso", "intenta", "intentais",
				"intentamos", "intentan", "intentar", "intentas", "intento", "ir",
				"la", "largo", "las", "lo", "los", "mientras", "mio", "modo",
				"muchos", "muy", "nos", "nosotros", "otro", "para", "pero",
				"podeis", "podemos", "poder", "podria", "podriais", "podriamos",
				"podrian", "podrias", "por", "por qué", "porque", "primero",
				"puede", "pueden", "puedo", "quien", "sabe", "sabeis", "sabemos",
				"saben", "saber", "sabes", "ser", "si", "siendo", "sin", "sobre",
				"sois", "solamente", "solo", "somos", "soy", "su", "sus", "también",
				"teneis", "tenemos", "tener", "tengo", "tiempo", "tiene", "tienen",
				"todo", "trabaja", "trabajais", "trabajamos", "trabajan",
				"trabajar", "trabajas", "trabajo", "tras", "tuyo", "ultimo", "un",
				"una", "unas", "uno", "unos", "usa", "usais", "usamos", "usan",
				"usar", "usas", "uso", "va", "vais", "valor", "vamos", "van",
				"vaya", "verdad", "verdadera", "verdadero", "vosotras", "vosotros",
				"voy", "yo",
			),
			'fi' => array(
				"aiemmin", "aika", "aikaa", "aikaan", "aikaisemmin", "aikaisin",
				"aikajen", "aikana", "aikoina", "aikoo", "aikovat", "aina",
				"ainakaan", "ainakin", "ainoa", "ainoat", "aiomme", "aion",
				"aiotte", "aist", "aivan", "ajan", "älä", "alas", "alemmas",
				"älköön", "alkuisin", "alkuun", "alla", "alle", "aloitamme",
				"aloitan", "aloitat", "aloitatte", "aloitattivat", "aloitettava",
				"aloitettevaksi", "aloitettu", "aloitimme", "aloitin", "aloitit",
				"aloititte", "aloittaa", "aloittamatta", "aloitti", "aloittivat",
				"alta", "aluksi", "alussa", "alusta", "annettavaksi", "annetteva",
				"annettu", "antaa", "antamatta", "antoi", "aoua", "apu", "asia",
				"asiaa", "asian", "asiasta", "asiat", "asioiden", "asioihin",
				"asioita", "asti", "avuksi", "avulla", "avun", "avutta", "edellä",
				"edelle", "edelleen", "edeltä", "edemmäs", "edes", "edessä",
				"edestä", "ehkä", "ei", "eikä", "eilen", "eivät", "eli", "ellei",
				"elleivät", "ellemme", "ellen", "ellet", "ellette", "emme", "en",
				"enää", "enemmän", "eniten", "ennen", "ensi", "ensimmäinen",
				"ensimmäiseksi", "ensimmäisen", "ensimmäisenä", "ensimmäiset",
				"ensimmäisiä", "ensimmäisiksi", "ensimmäisinä", "ensimmäistä",
				"ensin", "entinen", "entisen", "entisiä", "entistä", "entisten",
				"eräät", "eräiden", "eräs", "eri", "erittäin", "erityisesti", "esi",
				"esiin", "esillä", "esimerkiksi", "et", "eteen", "etenkin", "että",
				"ette", "ettei", "halua", "haluaa", "haluamatta", "haluamme",
				"haluan", "haluat", "haluatte", "haluavat", "halunnut", "halusi",
				"halusimme", "halusin", "halusit", "halusitte", "halusivat",
				"halutessa", "haluton", "hän", "häneen", "hänellä", "hänelle",
				"häneltä", "hänen", "hänessä", "hänestä", "hänet", "he", "hei",
				"heidän", "heihin", "heille", "heiltä", "heissä", "heistä", "heitä",
				"helposti", "heti", "hetkellä", "hieman", "huolimatta", "huomenna",
				"hyvä", "hyvää", "hyvät", "hyviä", "hyvien", "hyviin", "hyviksi",
				"hyville", "hyviltä", "hyvin", "hyvinä", "hyvissä", "hyvistä",
				"ihan", "ilman", "ilmeisesti", "itse", "itseään", "itsensä", "ja",
				"jää", "jälkeen", "jälleen", "jo", "johon", "joiden", "joihin",
				"joiksi", "joilla", "joille", "joilta", "joissa", "joista", "joita",
				"joka", "jokainen", "jokin", "joko", "joku", "jolla", "jolle",
				"jolloin", "jolta", "jompikumpi", "jonka", "jonkin", "jonne", "joo",
				"jopa", "jos", "joskus", "jossa", "josta", "jota", "jotain",
				"joten", "jotenkin", "jotenkuten", "jotka", "jotta", "jouduimme",
				"jouduin", "jouduit", "jouduitte", "joudumme", "joudun", "joudutte",
				"joukkoon", "joukossa", "joukosta", "joutua", "joutui", "joutuivat",
				"joutumaan", "joutuu", "joutuvat", "juuri", "kahdeksan",
				"kahdeksannen", "kahdella", "kahdelle", "kahdelta", "kahden",
				"kahdessa", "kahdesta", "kahta", "kahteen", "kai", "kaiken",
				"kaikille", "kaikilta", "kaikkea", "kaikki", "kaikkia", "kaikkiaan",
				"kaikkialla", "kaikkialle", "kaikkialta", "kaikkien", "kaikkin",
				"kaksi", "kannalta", "kannattaa", "kanssa", "kanssaan", "kanssamme",
				"kanssani", "kanssanne", "kanssasi", "kauan", "kauemmas", "kautta",
				"kehen", "keiden", "keihin", "keiksi", "keillä", "keille", "keiltä",
				"keinä", "keissä", "keistä", "keitä", "keittä", "keitten", "keneen",
				"keneksi", "kenellä", "kenelle", "keneltä", "kenen", "kenenä",
				"kenessä", "kenestä", "kenet", "kenettä", "kennessästä", "kerran",
				"kerta", "kertaa", "kesken", "keskimäärin", "ketä", "ketkä",
				"kiitos", "kohti", "koko", "kokonaan", "kolmas", "kolme", "kolmen",
				"kolmesti", "koska", "koskaan", "kovin", "kuin", "kuinka",
				"kuitenkaan", "kuitenkin", "kuka", "kukaan", "kukin", "kumpainen",
				"kumpainenkaan", "kumpi", "kumpikaan", "kumpikin", "kun", "kuten",
				"kuuden", "kuusi", "kuutta", "kyllä", "kymmenen", "kyse",
				"lähekkäin", "lähellä", "lähelle", "läheltä", "lähemmäs", "lähes",
				"lähinnä", "lähtien", "läpi", "liian", "liki", "lisää", "lisäksi",
				"luo", "mahdollisimman", "mahdollista", "me", "meidän", "meillä",
				"meille", "melkein", "melko", "menee", "meneet", "menemme", "menen",
				"menet", "menette", "menevät", "meni", "menimme", "menin", "menit",
				"menivät", "mennessä", "mennyt", "menossa", "mihin", "mikä",
				"mikään", "mikäli", "mikin", "miksi", "milloin", "minä", "minne",
				"minun", "minut", "missä", "mistä", "mitä", "mitään", "miten",
				"moi", "molemmat", "mones", "monesti", "monet", "moni", "moniaalla",
				"moniaalle", "moniaalta", "monta", "muassa", "muiden", "muita",
				"muka", "mukaan", "mukaansa", "mukana", "mutta", "muu", "muualla",
				"muualle", "muualta", "muuanne", "muulloin", "muun", "muut",
				"muuta", "muutama", "muutaman", "muuten", "myöhemmin", "myös",
				"myöskään", "myöskin", "myötä", "näiden", "näin", "näissä",
				"näissähin", "näissälle", "näissältä", "näissästä", "näitä", "nämä",
				"ne", "neljä", "neljää", "neljän", "niiden", "niin", "niistä",
				"niitä", "noin", "nopeammin", "nopeasti", "nopeiten", "nro", "nuo",
				"nyt", "ohi", "oikein", "ole", "olemme", "olen", "olet", "olette",
				"oleva", "olevan", "olevat", "oli", "olimme", "olin", "olisi",
				"olisimme", "olisin", "olisit", "olisitte", "olisivat", "olit",
				"olitte", "olivat", "olla", "olleet", "olli", "ollut", "oma",
				"omaa", "omaan", "omaksi", "omalle", "omalta", "oman", "omassa",
				"omat", "omia", "omien", "omiin", "omiksi", "omille", "omilta",
				"omissa", "omista", "on", "onkin", "onko", "ovat", "päälle",
				"paikoittain", "paitsi", "pakosti", "paljon", "paremmin", "parempi",
				"parhaillaan", "parhaiten", "peräti", "perusteella", "pian",
				"pieneen", "pieneksi", "pienellä", "pienelle", "pieneltä",
				"pienempi", "pienestä", "pieni", "pienin", "puolesta", "puolestaan",
				"runsaasti", "saakka", "sadam", "sama", "samaa", "samaan",
				"samalla", "samallalta", "samallassa", "samallasta", "saman",
				"samat", "samoin", "sata", "sataa", "satojen", "se", "seitsemän",
				"sekä", "sen", "seuraavat", "siellä", "sieltä", "siihen", "siinä",
				"siis", "siitä", "sijaan", "siksi", "sillä", "silloin", "silti",
				"sinä", "sinne", "sinua", "sinulle", "sinulta", "sinun", "sinussa",
				"sinusta", "sinut", "sisäkkäin", "sisällä", "sitä", "siten",
				"sitten", "suoraan", "suuntaan", "suuren", "suuret", "suuri",
				"suuria", "suurin", "suurten", "taa", "täällä", "täältä", "taas",
				"taemmas", "tähän", "tahansa", "tai", "takaa", "takaisin", "takana",
				"takia", "tällä", "tällöin", "tämä", "tämän", "tänä", "tänään",
				"tänne", "tapauksessa", "tässä", "tästä", "tätä", "täten",
				"tavalla", "tavoitteena", "täysin", "täytyvät", "täytyy", "te",
				"tietysti", "todella", "toinen", "toisaalla", "toisaalle",
				"toisaalta", "toiseen", "toiseksi", "toisella", "toiselle",
				"toiselta", "toisemme", "toisen", "toisensa", "toisessa",
				"toisesta", "toista", "toistaiseksi", "toki", "tosin", "tuhannen",
				"tuhat", "tule", "tulee", "tulemme", "tulen", "tulet", "tulette",
				"tulevat", "tulimme", "tulin", "tulisi", "tulisimme", "tulisin",
				"tulisit", "tulisitte", "tulisivat", "tulit", "tulitte", "tulivat",
				"tulla", "tulleet", "tullut", "tuntuu", "tuo", "tuolla", "tuolloin",
				"tuolta", "tuonne", "tuskin", "tykö", "usea", "useasti",
				"useimmiten", "usein", "useita", "uudeksi", "uudelleen", "uuden",
				"uudet", "uusi", "uusia", "uusien", "uusinta", "uuteen", "uutta",
				"vaan", "vähän", "vähemmän", "vähintään", "vähiten", "vai",
				"vaiheessa", "vaikea", "vaikean", "vaikeat", "vaikeilla",
				"vaikeille", "vaikeilta", "vaikeissa", "vaikeista", "vaikka",
				"vain", "välillä", "varmasti", "varsin", "varsinkin", "varten",
				"vasta", "vastaan", "vastakkain", "verran", "vielä", "vierekkäin",
				"vieri", "viiden", "viime", "viimeinen", "viimeisen", "viimeksi",
				"viisi", "voi", "voidaan", "voimme", "voin", "voisi", "voit",
				"voitte", "voivat", "vuoden", "vuoksi", "vuosi", "vuosien",
				"vuosina", "vuotta", "yhä", "yhdeksän", "yhden", "yhdessä", "yhtä",
				"yhtäällä", "yhtäälle", "yhtäältä", "yhtään", "yhteen", "yhteensä",
				"yhteydessä", "yhteyteen", "yksi", "yksin", "yksittäin", "yleensä",
				"ylemmäs", "yli", "ylös", "ympäri",
			),
			'fr' => array(
				"alors", "au", "aucuns", "aussi", "autre", "avant", "avec", "avoir",
				"bon", "car", "ce", "cela", "ces", "ceux", "chaque", "ci", "comme",
				"comment", "dans", "des", "du", "dedans", "dehors", "depuis",
				"devrait", "doit", "donc", "dos", "début", "elle", "elles", "en",
				"encore", "essai", "est", "et", "eu", "fait", "faites", "fois",
				"font", "hors", "ici", "il", "ils", "je", "juste", "la", "le",
				"les", "leur", "là", "ma", "maintenant", "mais", "mes", "mine",
				"moins", "mon", "mot", "même", "ni", "nommés", "notre", "nous",
				"ou", "où", "par", "parce", "pas", "peut", "peu", "plupart", "pour",
				"pourquoi", "quand", "que", "quel", "quelle", "quelles", "quels",
				"qui", "sa", "sans", "ses", "seulement", "si", "sien", "son",
				"sont", "sous", "soyez", "sujet", "sur", "ta", "tandis",
				"tellement", "tels", "tes", "ton", "tous", "tout", "trop", "très",
				"tu", "voient", "vont", "votre", "vous", "vu", "ça", "étaient",
				"état", "étions", "été", "être",
			),
			'ga' => array(
				"a", "ach", "ag", "agus", "an", "aon", "ar", "arna", "as", "b'",
				"ba", "beirt", "bhúr", "caoga", "ceathair", "ceathrar", "chomh",
				"chtó", "chuig", "chun", "cois", "céad", "cúig", "cúigear", "d'",
				"daichead", "dar", "de", "deich", "deichniúr", "den", "dhá", "do",
				"don", "dtí", "dá", "dár", "dó", "faoi", "faoin", "faoina",
				"faoinár", "fara", "fiche", "gach", "gan", "go", "gur", "haon",
				"hocht", "i", "iad", "idir", "in", "ina", "ins", "inár", "is", "le",
				"leis", "lena", "lenár", "m'", "mar", "mo", "mé", "na", "nach",
				"naoi", "naonúr", "ná", "ní", "níor", "nó", "nócha", "ocht",
				"ochtar", "os", "roimh", "sa", "seacht", "seachtar", "seachtó",
				"seasca", "seisear", "siad", "sibh", "sinn", "sna", "sé", "sí",
				"tar", "thar", "thú", "triúr", "trí", "trína", "trínár", "tríocha",
				"tú", "um", "ár", "é", "éis", "í", "ó", "ón", "óna", "ónár",
			),
			'it' => array(
				"a", "adesso", "ai", "al", "alla", "allo", "allora", "altre",
				"altri", "altro", "anche", "ancora", "avere", "aveva", "avevano",
				"ben", "buono", "che", "chi", "cinque", "comprare", "con",
				"consecutivi", "consecutivo", "cosa", "cui", "da", "del", "della",
				"dello", "dentro", "deve", "devo", "di", "doppio", "due", "e",
				"ecco", "fare", "fine", "fino", "fra", "gente", "giu", "giù", "ha",
				"hai", "hanno", "ho", "il", "indietro", "invece", "io", "la",
				"lavoro", "le", "lei", "lo", "loro", "lui", "lungo", "ma", "me",
				"meglio", "molta", "molti", "molto", "nei", "nella", "no", "noi",
				"nome", "nostro", "nove", "nuovi", "nuovo", "o", "oltre", "ora",
				"otto", "peggio", "pero", "però", "persone", "piu", "poco", "primo",
				"promesso", "qua", "quarto", "quasi", "quattro", "quello", "questo",
				"qui", "quindi", "quinto", "rispetto", "sara", "secondo", "sei",
				"sembra", "sembrava", "senza", "sette", "sia", "siamo", "siete",
				"solo", "sono", "sopra", "soprattutto", "sotto", "stati", "stato",
				"stesso", "su", "subito", "sul", "sulla", "tanto", "te", "tempo",
				"terzo", "tra", "tre", "triplo", "ultimo", "un", "una", "uno", "va",
				"vai", "voi", "volte", "vostro",
			),
			'nl' => array(
				"aan", "af", "al", "als", "bij", "dan", "dat", "die", "dit", "een",
				"en", "er", "had", "heb", "hem", "het", "hij", "hoe", "hun", "ik",
				"in", "is", "je", "kan", "me", "men", "met", "mij", "nog", "nu",
				"of", "ons", "ook", "te", "tot", "uit", "van", "was", "wat", "we",
				"wel", "wij", "zal", "ze", "zei", "zij", "zo", "zou",
			),
			'pl' => array(
				"ach", "aj", "albo", "bardzo", "bez", "bo", "być", "ci", "cię",
				"ciebie", "co", "czy", "daleko", "dla", "dlaczego", "dlatego", "do",
				"dobrze", "dokąd", "dość", "dużo", "dwa", "dwaj", "dwie", "dwoje",
				"dziś", "dzisiaj", "gdyby", "gdzie", "go", "ich", "ile", "im",
				"inny", "ja", "ją", "jak", "jakby", "jaki", "je", "jeden", "jedna",
				"jedno", "jego", "jej", "jemu", "jeśli", "jest", "jestem", "jeżeli",
				"już", "każdy", "kiedy", "kierunku", "kto", "ku", "lub", "ma",
				"mają", "mam", "mi", "mną", "mnie", "moi", "mój", "moja", "moje",
				"może", "mu", "my", "na", "nam", "nami", "nas", "nasi", "nasz",
				"nasza", "nasze", "natychmiast", "nią", "nic", "nich", "nie",
				"niego", "niej", "niemu", "nigdy", "nim", "nimi", "niż", "obok",
				"od", "około", "on", "ona", "one", "oni", "ono", "owszem", "po",
				"pod", "ponieważ", "przed", "przedtem", "są", "sam", "sama", "się",
				"skąd", "tak", "taki", "tam", "ten", "to", "tobą", "tobie", "tu",
				"tutaj", "twoi", "twój", "twoja", "twoje", "ty", "wam", "wami",
				"was", "wasi", "wasz", "wasza", "wasze", "we", "więc", "wszystko",
				"wtedy", "wy", "żaden", "zawsze", "że",
			),
			'pt' => array(
				"último", "é", "acerca", "agora", "algmas", "alguns", "ali",
				"ambos", "antes", "apontar", "aquela", "aquelas", "aquele",
				"aqueles", "aqui", "atrás", "bem", "bom", "cada", "caminho", "cima",
				"com", "como", "comprido", "conhecido", "corrente", "das",
				"debaixo", "dentro", "desde", "desligado", "deve", "devem",
				"deverá", "direita", "diz", "dizer", "dois", "dos", "e", "ela",
				"ele", "eles", "em", "enquanto", "então", "está", "estão", "estado",
				"estar", "estará", "este", "estes", "esteve", "estive", "estivemos",
				"estiveram", "eu", "fará", "faz", "fazer", "fazia", "fez", "fim",
				"foi", "fora", "horas", "iniciar", "inicio", "ir", "irá", "ista",
				"iste", "isto", "ligado", "maioria", "maiorias", "mais", "mas",
				"mesmo", "meu", "muito", "muitos", "nós", "não", "nome", "nosso",
				"novo", "o", "onde", "os", "ou", "outro", "para", "parte", "pegar",
				"pelo", "pessoas", "pode", "poderá", "podia", "por", "porque",
				"povo", "promeiro", "quê", "qual", "qualquer", "quando", "quem",
				"quieto", "são", "saber", "sem", "ser", "seu", "somente", "têm",
				"tal", "também", "tem", "tempo", "tenho", "tentar", "tentaram",
				"tente", "tentei", "teu", "teve", "tipo", "tive", "todos",
				"trabalhar", "trabalho", "tu", "um", "uma", "umas", "uns", "usa",
				"usar", "valor", "veja", "ver", "verdade", "verdadeiro", "você",
			),
			'ro' => array(
				"acea", "aceasta", "această", "aceea", "acei", "aceia", "acel",
				"acela", "acele", "acelea", "acest", "acesta", "aceste", "acestea",
				"aceşti", "aceştia", "acolo", "acord", "acum", "ai", "aia", "aibă",
				"aici", "al", "ăla", "ale", "alea", "ălea", "altceva", "altcineva",
				"am", "ar", "are", "aş", "aşadar", "asemenea", "asta", "ăsta",
				"astăzi", "astea", "ăstea", "ăştia", "asupra", "aţi", "au", "avea",
				"avem", "aveţi", "azi", "bine", "bucur", "bună", "ca", "că", "căci",
				"când", "care", "cărei", "căror", "cărui", "cât", "câte", "câţi",
				"către", "câtva", "caut", "ce", "cel", "ceva", "chiar", "cinci",
				"cînd", "cine", "cineva", "cît", "cîte", "cîţi", "cîtva", "contra",
				"cu", "cum", "cumva", "curând", "curînd", "da", "dă", "dacă", "dar",
				"dată", "datorită", "dau", "de", "deci", "deja", "deoarece",
				"departe", "deşi", "din", "dinaintea", "dintr", "dintre", "doi",
				"doilea", "două", "drept", "după", "ea", "ei", "el", "ele", "eram",
				"este", "eşti", "eu", "face", "fără", "fata", "fi", "fie",
				"fiecare", "fii", "fim", "fiţi", "fiu", "frumos", "graţie", "halbă",
				"iar", "ieri", "îi", "îl", "îmi", "împotriva", "în", "înainte",
				"înaintea", "încât", "încît", "încotro", "între", "întrucât",
				"întrucît", "îţi", "la", "lângă", "le", "li", "lîngă", "lor", "lui",
				"mă", "mai", "mâine", "mea", "mei", "mele", "mereu", "meu", "mi",
				"mie", "mîine", "mine", "mult", "multă", "mulţi", "mulţumesc", "ne",
				"nevoie", "nicăieri", "nici", "nimeni", "nimeri", "nimic", "nişte",
				"noastră", "noastre", "noi", "noroc", "noştri", "nostru", "nouă",
				"nu", "opt", "ori", "oricând", "oricare", "oricât", "orice",
				"oricînd", "oricine", "oricît", "oricum", "oriunde", "până",
				"patra", "patru", "patrulea", "pe", "pentru", "peste", "pic",
				"pînă", "poate", "pot", "prea", "prima", "primul", "prin", "puţin",
				"puţina", "puţină", "rog", "sa", "să", "săi", "sale", "şapte",
				"şase", "sau", "său", "se", "şi", "sînt", "sîntem", "sînteţi",
				"spate", "spre", "ştiu", "sub", "sunt", "suntem", "sunteţi", "sută",
				"ta", "tăi", "tale", "tău", "te", "ţi", "ţie", "timp", "tine",
				"toată", "toate", "tot", "toţi", "totuşi", "trei", "treia",
				"treilea", "tu", "un", "una", "unde", "undeva", "unei", "uneia",
				"unele", "uneori", "unii", "unor", "unora", "unu", "unui", "unuia",
				"unul", "vă", "vi", "voastră", "voastre", "voi", "voştri", "vostru",
				"vouă", "vreme", "vreo", "vreun", "zece", "zero", "zi", "zice",
			),
			'ru' => array(
				"а", "е", "и", "ж", "м", "о", "на", "не", "ни", "об", "но", "он",
				"мне", "мои", "мож", "она", "они", "оно", "мной", "много",
				"многочисленное", "многочисленная", "многочисленные",
				"многочисленный", "мною", "мой", "мог", "могут", "можно",
				"может", "можхо", "мор", "моя", "моё", "мочь", "над", "нее",
				"оба", "нам", "нем", "нами", "ними", "мимо", "немного", "одной",
				"одного", "менее", "однажды", "однако", "меня", "нему", "меньше",
				"ней", "наверху", "него", "ниже", "мало", "надо", "один",
				"одиннадцать", "одиннадцатый", "назад", "наиболее", "недавно",
				"миллионов", "недалеко", "между", "низко", "меля", "нельзя",
				"нибудь", "непрерывно", "наконец", "никогда", "никуда", "нас",
				"наш", "нет", "нею", "неё", "них", "мира", "наша", "наше",
				"наши", "ничего", "начала", "нередко", "несколько", "обычно",
				"опять", "около", "мы", "ну", "нх", "от", "отовсюду", "особенно",
				"нужно", "очень", "отсюда", "в", "во", "вон", "вниз", "внизу",
				"вокруг", "вот", "восемнадцать", "восемнадцатый", "восемь",
				"восьмой", "вверх", "вам", "вами", "важное", "важная", "важные",
				"важный", "вдали", "везде", "ведь", "вас", "ваш", "ваша", "ваше",
				"ваши", "впрочем", "весь", "вдруг", "вы", "все", "второй",
				"всем", "всеми", "времени", "время", "всему", "всего", "всегда",
				"всех", "всею", "всю", "вся", "всё", "всюду", "г", "год",
				"говорил", "говорит", "года", "году", "где", "да", "ее", "за",
				"из", "ли", "же", "им", "до", "по", "ими", "под", "иногда",
				"довольно", "именно", "долго", "позже", "более", "должно",
				"пожалуйста", "значит", "иметь", "больше", "пока", "ему", "имя",
				"пор", "пора", "потом", "потому", "после", "почему", "почти",
				"посреди", "ей", "два", "две", "двенадцать", "двенадцатый",
				"двадцать", "двадцатый", "двух", "его", "дел", "или", "без",
				"день", "занят", "занята", "занято", "заняты", "действительно",
				"давно", "девятнадцать", "девятнадцатый", "девять", "девятый",
				"даже", "алло", "жизнь", "далеко", "близко", "здесь", "дальше",
				"для", "лет", "зато", "даром", "первый", "перед", "затем",
				"зачем", "лишь", "десять", "десятый", "ею", "её", "их", "бы",
				"еще", "при", "был", "про", "процентов", "против", "просто",
				"бывает", "бывь", "если", "люди", "была", "были", "было",
				"будем", "будет", "будете", "будешь", "прекрасно", "буду",
				"будь", "будто", "будут", "ещё", "пятнадцать", "пятнадцатый",
				"друго", "другое", "другой", "другие", "другая", "других",
				"есть", "пять", "быть", "лучше", "пятый", "к", "ком", "конечно",
				"кому", "кого", "когда", "которой", "которого", "которая",
				"которые", "который", "которых", "кем", "каждое", "каждая",
				"каждые", "каждый", "кажется", "как", "какой", "какая", "кто",
				"кроме", "куда", "кругом", "с", "т", "у", "я", "та", "те", "уж",
				"со", "то", "том", "снова", "тому", "совсем", "того", "тогда",
				"тоже", "собой", "тобой", "собою", "тобою", "сначала", "только",
				"уметь", "тот", "тою", "хорошо", "хотеть", "хочешь", "хоть",
				"хотя", "свое", "свои", "твой", "своей", "своего", "своих",
				"свою", "твоя", "твоё", "раз", "уже", "сам", "там", "тем", "чем",
				"сама", "сами", "теми", "само", "рано", "самом", "самому",
				"самой", "самого", "семнадцать", "семнадцатый", "самим",
				"самими", "самих", "саму", "семь", "чему", "раньше", "сейчас",
				"чего", "сегодня", "себе", "тебе", "сеаой", "человек", "разве",
				"теперь", "себя", "тебя", "седьмой", "спасибо", "слишком",
				"так", "такое", "такой", "такие", "также", "такая", "сих", "тех",
				"чаще", "четвертый", "через", "часто", "шестой", "шестнадцать",
				"шестнадцатый", "шесть", "четыре", "четырнадцать",
				"четырнадцатый", "сколько", "сказал", "сказала", "сказать",
				"ту", "ты", "три", "эта", "эти", "что", "это", "чтоб", "этом",
				"этому", "этой", "этого", "чтобы", "этот", "стал", "туда",
				"этим", "этими", "рядом", "тринадцать", "тринадцатый", "этих",
				"третий", "тут", "эту", "суть", "чуть", "тысяч",
			),
			'sv' => array(
				"aderton", "adertonde", "adjö", "aldrig", "alla", "allas", "allt",
				"alltid", "alltså", "än", "andra", "andras", "annan", "annat",
				"ännu", "artonde", "artonn", "åtminstone", "att", "åtta", "åttio",
				"åttionde", "åttonde", "av", "även", "båda", "bådas", "bakom",
				"bara", "bäst", "bättre", "behöva", "behövas", "behövde", "behövt",
				"beslut", "beslutat", "beslutit", "bland", "blev", "bli", "blir",
				"blivit", "bort", "borta", "bra", "då", "dag", "dagar", "dagarna",
				"dagen", "där", "därför", "de", "del", "delen", "dem", "den",
				"deras", "dess", "det", "detta", "dig", "din", "dina", "dit",
				"ditt", "dock", "du", "efter", "eftersom", "elfte", "eller", "elva",
				"en", "enkel", "enkelt", "enkla", "enligt", "er", "era", "ert",
				"ett", "ettusen", "få", "fanns", "får", "fått", "fem", "femte",
				"femtio", "femtionde", "femton", "femtonde", "fick", "fin",
				"finnas", "finns", "fjärde", "fjorton", "fjortonde", "fler",
				"flera", "flesta", "följande", "för", "före", "förlåt", "förra",
				"första", "fram", "framför", "från", "fyra", "fyrtio", "fyrtionde",
				"gå", "gälla", "gäller", "gällt", "går", "gärna", "gått", "genast",
				"genom", "gick", "gjorde", "gjort", "god", "goda", "godare",
				"godast", "gör", "göra", "gott", "ha", "hade", "haft", "han",
				"hans", "har", "här", "heller", "hellre", "helst", "helt", "henne",
				"hennes", "hit", "hög", "höger", "högre", "högst", "hon", "honom",
				"hundra", "hundraen", "hundraett", "hur", "i", "ibland", "idag",
				"igår", "igen", "imorgon", "in", "inför", "inga", "ingen",
				"ingenting", "inget", "innan", "inne", "inom", "inte", "inuti",
				"ja", "jag", "jämfört", "kan", "kanske", "knappast", "kom", "komma",
				"kommer", "kommit", "kr", "kunde", "kunna", "kunnat", "kvar",
				"länge", "längre", "långsam", "långsammare", "långsammast",
				"långsamt", "längst", "långt", "lätt", "lättare", "lättast",
				"legat", "ligga", "ligger", "lika", "likställd", "likställda",
				"lilla", "lite", "liten", "litet", "man", "många", "måste", "med",
				"mellan", "men", "mer", "mera", "mest", "mig", "min", "mina",
				"mindre", "minst", "mitt", "mittemot", "möjlig", "möjligen",
				"möjligt", "möjligtvis", "mot", "mycket", "någon", "någonting",
				"något", "några", "när", "nästa", "ned", "nederst", "nedersta",
				"nedre", "nej", "ner", "ni", "nio", "nionde", "nittio", "nittionde",
				"nitton", "nittonde", "nödvändig", "nödvändiga", "nödvändigt",
				"nödvändigtvis", "nog", "noll", "nr", "nu", "nummer", "och", "också",
				"ofta", "oftast", "olika", "olikt", "om", "oss", "över",
				"övermorgon", "överst", "övre", "på", "rakt", "rätt", "redan", "så",
				"sade", "säga", "säger", "sagt", "samma", "sämre", "sämst", "sedan",
				"senare", "senast", "sent", "sex", "sextio", "sextionde", "sexton",
				"sextonde", "sig", "sin", "sina", "sist", "sista", "siste", "sitt",
				"sjätte", "sju", "sjunde", "sjuttio", "sjuttionde", "sjutton",
				"sjuttonde", "ska", "skall", "skulle", "slutligen", "små", "smått",
				"snart", "som", "stor", "stora", "större", "störst", "stort",
				"tack", "tidig", "tidigare", "tidigast", "tidigt", "till", "tills",
				"tillsammans", "tio", "tionde", "tjugo", "tjugoen", "tjugoett",
				"tjugonde", "tjugotre", "tjugotvå", "tjungo", "tolfte", "tolv",
				"tre", "tredje", "trettio", "trettionde", "tretton", "trettonde",
				"två", "tvåhundra", "under", "upp", "ur", "ursäkt", "ut", "utan",
				"utanför", "ute", "vad", "vänster", "vänstra", "var", "vår", "vara",
				"våra", "varför", "varifrån", "varit", "varken", "värre", "varsågod",
				"vart", "vårt", "vem", "vems", "verkligen", "vi", "vid", "vidare",
				"viktig", "viktigare", "viktigast", "viktigt", "vilka", "vilken",
				"vilket", "vill",
			),
			'tr' => array(
				"acaba", "altmýþ", "altý", "ama", "bana", "bazý", "belki", "ben",
				"benden", "beni", "benim", "beþ", "bin", "bir", "biri", "birkaç",
				"birkez", "birþey", "birþeyi", "biz", "bizden", "bizi", "bizim",
				"bu", "buna", "bunda", "bundan", "bunu", "bunun", "da", "daha",
				"dahi", "de", "defa", "diye", "doksan", "dokuz", "dört", "elli",
				"en", "gibi", "hem", "hep", "hepsi", "her", "hiç", "iki", "ile",
				"INSERmi", "ise", "için", "katrilyon", "kez", "ki", "kim", "kimden",
				"kime", "kimi", "kýrk", "milyar", "milyon", "mu", "mü", "mý",
				"nasýl", "ne", "neden", "nerde", "nerede", "nereye", "niye",
				"niçin", "on", "ona", "ondan", "onlar", "onlardan", "onlari",
				"onlarýn", "onu", "otuz", "sanki", "sekiz", "seksen", "sen",
				"senden", "seni", "senin", "siz", "sizden", "sizi", "sizin",
				"trilyon", "tüm", "ve", "veya", "ya", "yani", "yedi", "yetmiþ",
				"yirmi", "yüz", "çok", "çünkü", "üç", "þey", "þeyden", "þeyi",
				"þeyler", "þu", "þuna", "þunda", "þundan", "þunu",
			),
		);

		return array_key_exists( $this->language_code, $defaults )
				? $defaults[ $this->language_code ]
				: $defaults['en'];
	}
}
