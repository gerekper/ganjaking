<?php

class UEExchangeRateAPIRate extends UEExchangeRateAPIModel{

	/**
	 * Get the identifier.
	 *
	 * @return string
	 */
	public function getId(){

		$id = $this->getCode();
		$id = strtolower($id);

		return $id;
	}

	/**
	 * Get the code.
	 *
	 * @return string
	 */
	public function getCode(){

		$code = $this->getAttribute("code");

		return $code;
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function getName(){

		$name = $this->getInfo("name", $this->getCode());

		return $name;
	}

	/**
	 * Get the symbol.
	 *
	 * @return string
	 */
	public function getSymbol(){

		$symbol = $this->getInfo("symbol", $this->getCode());

		return $symbol;
	}

	/**
	 * Get the flag URL.
	 *
	 * @return string
	 */
	public function getFlagUrl(){

		$country = $this->getCountry();
		$country = strtolower($country);

		$url = "https://flagcdn.com/" . $country . ".svg";

		return $url;
	}

	/**
	 * Get the rate.
	 *
	 * @param int $precision
	 *
	 * @return float
	 */
	public function getRate($precision = 2){

		$rate = $this->getAttribute("rate");
		$rate = number_format($rate, $precision, ".", "");

		return $rate;
	}

	/**
	 * Get the info.
	 *
	 * @param string $field
	 * @param string $code
	 *
	 * @return string
	 */
	private function getInfo($field, $code){

		$info = array(
			"USD" => array(
				"name" => "United States Dollar",
				"symbol" => "$",
				"country" => "US",
			),
			"AED" => array(
				"name" => "United Arab Emirates Dirham",
				"symbol" => "د.إ",
				"country" => "AE",
			),
			"AFN" => array(
				"name" => "Afghan Afghani",
				"symbol" => "؋",
				"country" => "AF",
			),
			"ALL" => array(
				"name" => "Albanian Lek",
				"symbol" => "L",
				"country" => "AL",
			),
			"AMD" => array(
				"name" => "Armenian Dram",
				"symbol" => "֏",
				"country" => "AM",
			),
			"ANG" => array(
				"name" => "Netherlands Antillean Guilder",
				"symbol" => "ƒ",
				"country" => "NL",
			),
			"AOA" => array(
				"name" => "Angolan Kwanza",
				"symbol" => "Kz",
				"country" => "AO",
			),
			"ARS" => array(
				"name" => "Argentine Peso",
				"symbol" => "$",
				"country" => "AR",
			),
			"AUD" => array(
				"name" => "Australian Dollar",
				"symbol" => "$",
				"country" => "AU",
			),
			"AWG" => array(
				"name" => "Aruban Florin",
				"symbol" => "ƒ",
				"country" => "AW",
			),
			"AZN" => array(
				"name" => "Azerbaijani Manat",
				"symbol" => "₼",
				"country" => "AZ",
			),
			"BAM" => array(
				"name" => "Bosnia-Herzegovina Convertible Mark",
				"symbol" => "KM",
				"country" => "BA",
			),
			"BBD" => array(
				"name" => "Barbadian Dollar",
				"symbol" => "$",
				"country" => "BB",
			),
			"BDT" => array(
				"name" => "Bangladeshi Taka",
				"symbol" => "৳",
				"country" => "BD",
			),
			"BGN" => array(
				"name" => "Bulgarian Lev",
				"symbol" => "лв",
				"country" => "BG",
			),
			"BHD" => array(
				"name" => "Bahraini Dinar",
				"symbol" => "ب.د",
				"country" => "BH",
			),
			"BIF" => array(
				"name" => "Burundian Franc",
				"symbol" => "Fr",
				"country" => "BI",
			),
			"BMD" => array(
				"name" => "Bermudian Dollar",
				"symbol" => "$",
				"country" => "BM",
			),
			"BND" => array(
				"name" => "Brunei Dollar",
				"symbol" => "$",
				"country" => "BN",
			),
			"BOB" => array(
				"name" => "Bolivian Boliviano",
				"symbol" => "Bs.",
				"country" => "BO",
			),
			"BRL" => array(
				"name" => "Brazilian Real",
				"symbol" => "R$",
				"country" => "BR",
			),
			"BSD" => array(
				"name" => "Bahamian Dollar",
				"symbol" => "$",
				"country" => "BS",
			),
			"BTN" => array(
				"name" => "Bhutanese Ngultrum",
				"symbol" => "Nu.",
				"country" => "BT",
			),
			"BWP" => array(
				"name" => "Botswanan Pula",
				"symbol" => "P",
				"country" => "BW",
			),
			"BYN" => array(
				"name" => "Belarusian Ruble",
				"symbol" => "Br",
				"country" => "BY",
			),
			"BZD" => array(
				"name" => "Belize Dollar",
				"symbol" => "BZ$",
				"country" => "BZ",
			),
			"CAD" => array(
				"name" => "Canadian Dollar",
				"symbol" => "$",
				"country" => "CA",
			),
			"CDF" => array(
				"name" => "Congolese Franc",
				"symbol" => "Fr",
				"country" => "CD",
			),
			"CHF" => array(
				"name" => "Swiss Franc",
				"symbol" => "Fr",
				"country" => "CH",
			),
			"CLP" => array(
				"name" => "Chilean Peso",
				"symbol" => "$",
				"country" => "CL",
			),
			"CNY" => array(
				"name" => "Chinese Yuan",
				"symbol" => "¥",
				"country" => "CN",
			),
			"COP" => array(
				"name" => "Colombian Peso",
				"symbol" => "$",
				"country" => "CO",
			),
			"CRC" => array(
				"name" => "Costa Rican Colón",
				"symbol" => "₡",
				"country" => "CR",
			),
			"CUP" => array(
				"name" => "Cuban Peso",
				"symbol" => "$",
				"country" => "CU",
			),
			"CVE" => array(
				"name" => "Cape Verdean Escudo",
				"symbol" => "$",
				"country" => "CV",
			),
			"CZK" => array(
				"name" => "Czech Republic Koruna",
				"symbol" => "Kč",
				"country" => "CZ",
			),
			"DJF" => array(
				"name" => "Djiboutian Franc",
				"symbol" => "Fdj",
				"country" => "DJ",
			),
			"DKK" => array(
				"name" => "Danish Krone",
				"symbol" => "kr",
				"country" => "DK",
			),
			"DOP" => array(
				"name" => "Dominican Peso",
				"symbol" => "RD$",
				"country" => "DO",
			),
			"DZD" => array(
				"name" => "Algerian Dinar",
				"symbol" => "دج",
				"country" => "DZ",
			),
			"EGP" => array(
				"name" => "Egyptian Pound",
				"symbol" => "E£",
				"country" => "EG",
			),
			"ERN" => array(
				"name" => "Eritrean Nakfa",
				"symbol" => "Nfk",
				"country" => "ER",
			),
			"ETB" => array(
				"name" => "Ethiopian Birr",
				"symbol" => "Br",
				"country" => "ET",
			),
			"EUR" => array(
				"name" => "Euro",
				"symbol" => "€",
				"country" => "EU",
			),
			"FJD" => array(
				"name" => "Fijian Dollar",
				"symbol" => "$",
				"country" => "FJ",
			),
			"FKP" => array(
				"name" => "Falkland Islands Pound",
				"symbol" => "£",
				"country" => "FK",
			),
			"FOK" => array(
				"name" => "Faroese Króna",
				"symbol" => "kr",
				"country" => "FO",
			),
			"GBP" => array(
				"name" => "British Pound Sterling",
				"symbol" => "£",
				"country" => "GB",
			),
			"GEL" => array(
				"name" => "Georgian Lari",
				"symbol" => "₾",
				"country" => "GE",
			),
			"GGP" => array(
				"name" => "Guernsey Pound",
				"symbol" => "£",
				"country" => "GG",
			),
			"GHS" => array(
				"name" => "Ghanaian Cedi",
				"symbol" => "₵",
				"country" => "GH",
			),
			"GIP" => array(
				"name" => "Gibraltar Pound",
				"symbol" => "£",
				"country" => "GI",
			),
			"GMD" => array(
				"name" => "Gambian Dalasi",
				"symbol" => "D",
				"country" => "GM",
			),
			"GNF" => array(
				"name" => "Guinean Franc",
				"symbol" => "Fr",
				"country" => "GN",
			),
			"GTQ" => array(
				"name" => "Guatemalan Quetzal",
				"symbol" => "Q",
				"country" => "GT",
			),
			"GYD" => array(
				"name" => "Guyanese Dollar",
				"symbol" => "$",
				"country" => "GY",
			),
			"HKD" => array(
				"name" => "Hong Kong Dollar",
				"symbol" => "$",
				"country" => "HK",
			),
			"HNL" => array(
				"name" => "Honduran Lempira",
				"symbol" => "L",
				"country" => "HN",
			),
			"HRK" => array(
				"name" => "Croatian Kuna",
				"symbol" => "kn",
				"country" => "HR",
			),
			"HTG" => array(
				"name" => "Haitian Gourde",
				"symbol" => "G",
				"country" => "HT",
			),
			"HUF" => array(
				"name" => "Hungarian Forint",
				"symbol" => "Ft",
				"country" => "HU",
			),
			"IDR" => array(
				"name" => "Indonesian Rupiah",
				"symbol" => "Rp",
				"country" => "ID",
			),
			"ILS" => array(
				"name" => "Israeli New Shekel",
				"symbol" => "₪",
				"country" => "IL",
			),
			"IMP" => array(
				"name" => "Isle of Man Pound",
				"symbol" => "£",
				"country" => "IM",
			),
			"INR" => array(
				"name" => "Indian Rupee",
				"symbol" => "₹",
				"country" => "IN",
			),
			"IQD" => array(
				"name" => "Iraqi Dinar",
				"symbol" => "ع.د",
				"country" => "IQ",
			),
			"IRR" => array(
				"name" => "Iranian Rial",
				"symbol" => "﷼",
				"country" => "IR",
			),
			"ISK" => array(
				"name" => "Icelandic Króna",
				"symbol" => "kr",
				"country" => "IS",
			),
			"JEP" => array(
				"name" => "Jersey Pound",
				"symbol" => "£",
				"country" => "JE",
			),
			"JMD" => array(
				"name" => "Jamaican Dollar",
				"symbol" => "J$",
				"country" => "JM",
			),
			"JOD" => array(
				"name" => "Jordanian Dinar",
				"symbol" => "د.ا",
				"country" => "JO",
			),
			"JPY" => array(
				"name" => "Japanese Yen",
				"symbol" => "¥",
				"country" => "JP",
			),
			"KES" => array(
				"name" => "Kenyan Shilling",
				"symbol" => "KSh",
				"country" => "KE",
			),
			"KGS" => array(
				"name" => "Kyrgystani Som",
				"symbol" => "с",
				"country" => "KG",
			),
			"KHR" => array(
				"name" => "Cambodian Riel",
				"symbol" => "៛",
				"country" => "KH",
			),
			"KID" => array(
				"name" => "Kiribati Dollar",
				"symbol" => "$",
				"country" => "KI",
			),
			"KMF" => array(
				"name" => "Comorian Franc",
				"symbol" => "Fr",
				"country" => "KM",
			),
			"KRW" => array(
				"name" => "South Korean Won",
				"symbol" => "₩",
				"country" => "KR",
			),
			"KWD" => array(
				"name" => "Kuwaiti Dinar",
				"symbol" => "د.ك",
				"country" => "KW",
			),
			"KYD" => array(
				"name" => "Cayman Islands Dollar",
				"symbol" => "$",
				"country" => "KY",
			),
			"KZT" => array(
				"name" => "Kazakhstani Tenge",
				"symbol" => "₸",
				"country" => "KZ",
			),
			"LAK" => array(
				"name" => "Laotian Kip",
				"symbol" => "₭",
				"country" => "LA",
			),
			"LBP" => array(
				"name" => "Lebanese Pound",
				"symbol" => "ل.ل",
				"country" => "LB",
			),
			"LKR" => array(
				"name" => "Sri Lankan Rupee",
				"symbol" => "₨",
				"country" => "LK",
			),
			"LRD" => array(
				"name" => "Liberian Dollar",
				"symbol" => "$",
				"country" => "LR",
			),
			"LSL" => array(
				"name" => "Lesotho Loti",
				"symbol" => "L",
				"country" => "LS",
			),
			"LYD" => array(
				"name" => "Libyan Dinar",
				"symbol" => "ل.د",
				"country" => "LY",
			),
			"MAD" => array(
				"name" => "Moroccan Dirham",
				"symbol" => "د.م.",
				"country" => "MA",
			),
			"MDL" => array(
				"name" => "Moldovan Leu",
				"symbol" => "L",
				"country" => "MD",
			),
			"MGA" => array(
				"name" => "Malagasy Ariary",
				"symbol" => "Ar",
				"country" => "MG",
			),
			"MKD" => array(
				"name" => "Macedonian Denar",
				"symbol" => "ден",
				"country" => "MK",
			),
			"MMK" => array(
				"name" => "Myanmar Kyat",
				"symbol" => "K",
				"country" => "MM",
			),
			"MNT" => array(
				"name" => "Mongolian Tugrik",
				"symbol" => "₮",
				"country" => "MN",
			),
			"MOP" => array(
				"name" => "Macanese Pataca",
				"symbol" => "MOP$",
				"country" => "MO",
			),
			"MRU" => array(
				"name" => "Mauritanian Ouguiya",
				"symbol" => "UM",
				"country" => "MR",
			),
			"MUR" => array(
				"name" => "Mauritian Rupee",
				"symbol" => "₨",
				"country" => "MU",
			),
			"MVR" => array(
				"name" => "Maldivian Rufiyaa",
				"symbol" => "Rf",
				"country" => "MV",
			),
			"MWK" => array(
				"name" => "Malawian Kwacha",
				"symbol" => "MK",
				"country" => "MW",
			),
			"MXN" => array(
				"name" => "Mexican Peso",
				"symbol" => "$",
				"country" => "MX",
			),
			"MYR" => array(
				"name" => "Malaysian Ringgit",
				"symbol" => "RM",
				"country" => "MY",
			),
			"MZN" => array(
				"name" => "Mozambican Metical",
				"symbol" => "MT",
				"country" => "MZ",
			),
			"NAD" => array(
				"name" => "Namibian Dollar",
				"symbol" => "$",
				"country" => "NA",
			),
			"NGN" => array(
				"name" => "Nigerian Naira",
				"symbol" => "₦",
				"country" => "NG",
			),
			"NIO" => array(
				"name" => "Nicaraguan Córdoba",
				"symbol" => "C$",
				"country" => "NI",
			),
			"NOK" => array(
				"name" => "Norwegian Krone",
				"symbol" => "kr",
				"country" => "NO",
			),
			"NPR" => array(
				"name" => "Nepalese Rupee",
				"symbol" => "₨",
				"country" => "NP",
			),
			"NZD" => array(
				"name" => "New Zealand Dollar",
				"symbol" => "$",
				"country" => "NZ",
			),
			"OMR" => array(
				"name" => "Omani Rial",
				"symbol" => "ر.ع.",
				"country" => "OM",
			),
			"PAB" => array(
				"name" => "Panamanian Balboa",
				"symbol" => "B/.",
				"country" => "PA",
			),
			"PEN" => array(
				"name" => "Peruvian Nuevo Sol",
				"symbol" => "S/.",
				"country" => "PE",
			),
			"PGK" => array(
				"name" => "Papua New Guinean Kina",
				"symbol" => "K",
				"country" => "PG",
			),
			"PHP" => array(
				"name" => "Philippine Peso",
				"symbol" => "₱",
				"country" => "PH",
			),
			"PKR" => array(
				"name" => "Pakistani Rupee",
				"symbol" => "₨",
				"country" => "PK",
			),
			"PLN" => array(
				"name" => "Polish Złoty",
				"symbol" => "zł",
				"country" => "PL",
			),
			"PYG" => array(
				"name" => "Paraguayan Guarani",
				"symbol" => "₲",
				"country" => "PY",
			),
			"QAR" => array(
				"name" => "Qatari Riyal",
				"symbol" => "ر.ق",
				"country" => "QA",
			),
			"RON" => array(
				"name" => "Romanian Leu",
				"symbol" => "lei",
				"country" => "RO",
			),
			"RSD" => array(
				"name" => "Serbian Dinar",
				"symbol" => "дин.",
				"country" => "RS",
			),
			"RUB" => array(
				"name" => "Russian Ruble",
				"symbol" => "₽",
				"country" => "RU",
			),
			"RWF" => array(
				"name" => "Rwandan Franc",
				"symbol" => "Fr",
				"country" => "RW",
			),
			"SAR" => array(
				"name" => "Saudi Riyal",
				"symbol" => "ر.س",
				"country" => "SA",
			),
			"SBD" => array(
				"name" => "Solomon Islands Dollar",
				"symbol" => "$",
				"country" => "SB",
			),
			"SCR" => array(
				"name" => "Seychellois Rupee",
				"symbol" => "₨",
				"country" => "SC",
			),
			"SDG" => array(
				"name" => "Sudanese Pound",
				"symbol" => "ج.س.",
				"country" => "SD",
			),
			"SEK" => array(
				"name" => "Swedish Krona",
				"symbol" => "kr",
				"country" => "SE",
			),
			"SGD" => array(
				"name" => "Singapore Dollar",
				"symbol" => "$",
				"country" => "SG",
			),
			"SHP" => array(
				"name" => "Saint Helena Pound",
				"symbol" => "£",
				"country" => "SH",
			),
			"SLE" => array(
				"name" => "Sierra Leonean Leone",
				"symbol" => "Le",
				"country" => "SL",
			),
			"SLL" => array(
				"name" => "Sierra Leonean Leone",
				"symbol" => "Le",
				"country" => "SL",
			),
			"SOS" => array(
				"name" => "Somali Shilling",
				"symbol" => "Sh.So.",
				"country" => "SO",
			),
			"SRD" => array(
				"name" => "Surinamese Dollar",
				"symbol" => "$",
				"country" => "SR",
			),
			"SSP" => array(
				"name" => "South Sudanese Pound",
				"symbol" => "£",
				"country" => "SS",
			),
			"STN" => array(
				"name" => "São Tomé and Príncipe Dobra",
				"symbol" => "Db",
				"country" => "ST",
			),
			"SYP" => array(
				"name" => "Syrian Pound",
				"symbol" => "£",
				"country" => "SY",
			),
			"SZL" => array(
				"name" => "Swazi Lilangeni",
				"symbol" => "L",
				"country" => "SZ",
			),
			"THB" => array(
				"name" => "Thai Baht",
				"symbol" => "฿",
				"country" => "TH",
			),
			"TJS" => array(
				"name" => "Tajikistani Somoni",
				"symbol" => "ЅМ",
				"country" => "TJ",
			),
			"TMT" => array(
				"name" => "Turkmenistani Manat",
				"symbol" => "T",
				"country" => "TM",
			),
			"TND" => array(
				"name" => "Tunisian Dinar",
				"symbol" => "د.ت",
				"country" => "TN",
			),
			"TOP" => array(
				"name" => "Tongan Pa'anga",
				"symbol" => "T$",
				"country" => "TO",
			),
			"TRY" => array(
				"name" => "Turkish Lira",
				"symbol" => "₺",
				"country" => "TR",
			),
			"TTD" => array(
				"name" => "Trinidad and Tobago Dollar",
				"symbol" => "TT$",
				"country" => "TT",
			),
			"TVD" => array(
				"name" => "Tuvaluan Dollar",
				"symbol" => "$",
				"country" => "TV",
			),
			"TWD" => array(
				"name" => "New Taiwan Dollar",
				"symbol" => "NT$",
				"country" => "TW",
			),
			"TZS" => array(
				"name" => "Tanzanian Shilling",
				"symbol" => "TSh",
				"country" => "TZ",
			),
			"UAH" => array(
				"name" => "Ukrainian Hryvnia",
				"symbol" => "₴",
				"country" => "UA",
			),
			"UGX" => array(
				"name" => "Ugandan Shilling",
				"symbol" => "USh",
				"country" => "UG",
			),
			"UYU" => array(
				"name" => "Uruguayan Peso",
				"symbol" => "$",
				"country" => "UY",
			),
			"UZS" => array(
				"name" => "Uzbekistan Som",
				"symbol" => "лв",
				"country" => "UZ",
			),
			"VES" => array(
				"name" => "Venezuelan Bolívar",
				"symbol" => "Bs.S",
				"country" => "VE",
			),
			"VND" => array(
				"name" => "Vietnamese Dong",
				"symbol" => "₫",
				"country" => "VN",
			),
			"VUV" => array(
				"name" => "Vanuatu Vatu",
				"symbol" => "VT",
				"country" => "VU",
			),
			"WST" => array(
				"name" => "Samoan Tala",
				"symbol" => "WS$",
				"country" => "WS",
			),
			"XAF" => array(
				"name" => "Central African CFA Franc",
				"symbol" => "FCFA",
				"country" => "CM",
			),
			"XCD" => array(
				"name" => "East Caribbean Dollar",
				"symbol" => "$",
				"country" => "AG",
			),
			"XDR" => array(
				"name" => "International Monetary Fund (IMF) Special Drawing Rights",
				"symbol" => "XDR",
				"country" => "",
			),
			"XOF" => array(
				"name" => "West African CFA Franc",
				"symbol" => "CFA",
				"country" => "BJ",
			),
			"XPF" => array(
				"name" => "CFP Franc",
				"symbol" => "Fr",
				"country" => "PF",
			),
			"YER" => array(
				"name" => "Yemeni Rial",
				"symbol" => "﷼",
				"country" => "YE",
			),
			"ZAR" => array(
				"name" => "South African Rand",
				"symbol" => "R",
				"country" => "ZA",
			),
			"ZMW" => array(
				"name" => "Zambian Kwacha",
				"symbol" => "ZK",
				"country" => "ZM",
			),
			"ZWL" => array(
				"name" => "Zimbabwean Dollar",
				"symbol" => "Z$",
				"country" => "ZW",
			),
		);

		$fields = UniteFunctionsUC::getVal($info, $code, array());
		$value = UniteFunctionsUC::getVal($fields, $field, "");

		return $value;
	}

	/**
	 * Get the country.
	 *
	 * @return string
	 */
	private function getCountry(){

		$country = $this->getInfo("country", $this->getCode());

		return $country;
	}

}
