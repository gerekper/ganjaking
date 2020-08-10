/*
* jQuery.fontselect - A font selector for system fonts, local fonts and Google Web Fonts
*
* Made by Arjan Haverkamp, https://www.webgear.nl
* Based on original by Tom Moor, http://tommoor.com
* Copyright (c) 2011 Tom Moor, 2019-2020 Arjan Haverkamp
* MIT Licensed
* @version 1.0 - 2020-02-26
* @url https://github.com/av01d/fontselect-jquery-plugin
*/

(function($){
  "use strict";

  var fontsLoaded = {};

  $.fn.fontselect = function(options) {
    var __bind = function(fn, me) { return function(){ return fn.apply(me, arguments); }; };

    var settings = {
      style: 'font-select',
      placeholder: 'Select a font',
      placeholderSearch: 'Search...',
      searchable: true,
      lookahead: 2,
      googleApi: 'https://fonts.googleapis.com/css?family=',
      localFontsUrl: '/fonts/',
      systemFonts: 'Arial|Helvetica+Neue|Courier+New|Times+New+Roman|Comic+Sans+MS|Verdana|Impact'.split('|'),

      googleFonts: [
        "ABeeZee:400",
        "Abel:400",
        "Abhaya+Libre:400",
        "Abril+Fatface:400",
        "Aclonica:400",
        "Acme:400",
        "Actor:400",
        "Adamina:400",
        "Advent+Pro:400",
        "Aguafina+Script:400",
        "Akronim:400",
        "Aladin:400",
        "Alata:400",
        "Alatsi:400",
        "Aldrich:400",
        "Alef:400",
        "Alegreya:400",
        "Alegreya+SC:400",
        "Alegreya+Sans:400",
        "Alegreya+Sans+SC:400",
        "Aleo:400",
        "Alex+Brush:400",
        "Alfa+Slab+One:400",
        "Alice:400",
        "Alike:400",
        "Alike+Angular:400",
        "Allan:400",
        "Allerta:400",
        "Allerta+Stencil:400",
        "Allura:400",
        "Almarai:400",
        "Almendra:400",
        "Almendra+Display:400",
        "Almendra+SC:400",
        "Amarante:400",
        "Amaranth:400",
        "Amatic+SC:400",
        "Amethysta:400",
        "Amiko:400",
        "Amiri:400",
        "Amita:400",
        "Anaheim:400",
        "Andada:400",
        "Andika:400",
        "Angkor:400",
        "Annie+Use+Your+Telescope:400",
        "Anonymous+Pro:400",
        "Antic:400",
        "Antic+Didone:400",
        "Antic+Slab:400",
        "Anton:400",
        "Arapey:400",
        "Arbutus:400",
        "Arbutus+Slab:400",
        "Architects+Daughter:400",
        "Archivo:400",
        "Archivo+Black:400",
        "Archivo+Narrow:400",
        "Aref+Ruqaa:400",
        "Arima+Madurai:400",
        "Arimo:400",
        "Arizonia:400",
        "Armata:400",
        "Arsenal:400",
        "Artifika:400",
        "Arvo:400",
        "Arya:400",
        "Asap:400",
        "Asap+Condensed:400",
        "Asar:400",
        "Asset:400",
        "Assistant:400",
        "Astloch:400",
        "Asul:400",
        "Athiti:400",
        "Atma:400",
        "Atomic+Age:400",
        "Aubrey:400",
        "Audiowide:400",
        "Autour+One:400",
        "Average:400",
        "Average+Sans:400",
        "Averia+Gruesa+Libre:400",
        "Averia+Libre:400",
        "Averia+Sans+Libre:400",
        "Averia+Serif+Libre:400",
        "B612:400",
        "B612+Mono:400",
        "Bad+Script:400",
        "Bahiana:400",
        "Bahianita:400",
        "Bai+Jamjuree:400",
        "Baloo:400",
        "Baloo+Bhai:400",
        "Baloo+Bhaijaan:400",
        "Baloo+Bhaina:400",
        "Baloo+Chettan:400",
        "Baloo+Da:400",
        "Baloo+Paaji:400",
        "Baloo+Tamma:400",
        "Baloo+Tammudu:400",
        "Baloo+Thambi:400",
        "Balthazar:400",
        "Bangers:400",
        "Barlow:400",
        "Barlow+Condensed:400",
        "Barlow+Semi+Condensed:400",
        "Barriecito:400",
        "Barrio:400",
        "Basic:400",
        "Baskervville:400",
        "Battambang:400",
        "Baumans:400",
        "Bayon:400",
        "Be+Vietnam:400",
        "Bebas+Neue:400",
        "Belgrano:400",
        "Bellefair:400",
        "Belleza:400",
        "BenchNine:400",
        "Bentham:400",
        "Berkshire+Swash:400",
        "Beth+Ellen:400",
        "Bevan:400",
        "Big+Shoulders+Display:400",
        "Big+Shoulders+Text:400",
        "Bigelow+Rules:400",
        "Bigshot+One:400",
        "Bilbo:400",
        "Bilbo+Swash+Caps:400",
        "BioRhyme:400",
        "BioRhyme+Expanded:400",
        "Biryani:400",
        "Bitter:400",
        "Black+And+White+Picture:400",
        "Black+Han+Sans:400",
        "Black+Ops+One:400",
        "Blinker:400",
        "Bokor:400",
        "Bonbon:400",
        "Boogaloo:400",
        "Bowlby+One:400",
        "Bowlby+One+SC:400",
        "Brawler:400",
        "Bree+Serif:400",
        "Bubblegum+Sans:400",
        "Bubbler+One:400",
        "Buda:",
        "Buenard:400",
        "Bungee:400",
        "Bungee+Hairline:400",
        "Bungee+Inline:400",
        "Bungee+Outline:400",
        "Bungee+Shade:400",
        "Butcherman:400",
        "Butterfly+Kids:400",
        "Cabin:400",
        "Cabin+Condensed:400",
        "Cabin+Sketch:400",
        "Caesar+Dressing:400",
        "Cagliostro:400",
        "Cairo:400",
        "Calistoga:400",
        "Calligraffitti:400",
        "Cambay:400",
        "Cambo:400",
        "Candal:400",
        "Cantarell:400",
        "Cantata+One:400",
        "Cantora+One:400",
        "Capriola:400",
        "Cardo:400",
        "Carme:400",
        "Carrois+Gothic:400",
        "Carrois+Gothic+SC:400",
        "Carter+One:400",
        "Catamaran:400",
        "Caudex:400",
        "Caveat:400",
        "Caveat+Brush:400",
        "Cedarville+Cursive:400",
        "Ceviche+One:400",
        "Chakra+Petch:400",
        "Changa:400",
        "Changa+One:400",
        "Chango:400",
        "Charm:400",
        "Charmonman:400",
        "Chathura:400",
        "Chau+Philomene+One:400",
        "Chela+One:400",
        "Chelsea+Market:400",
        "Chenla:400",
        "Cherry+Cream+Soda:400",
        "Cherry+Swash:400",
        "Chewy:400",
        "Chicle:400",
        "Chilanka:400",
        "Chivo:400",
        "Chonburi:400",
        "Cinzel:400",
        "Cinzel+Decorative:400",
        "Clicker+Script:400",
        "Coda:400",
        "Coda+Caption:",
        "Codystar:400",
        "Coiny:400",
        "Combo:400",
        "Comfortaa:400",
        "Coming+Soon:400",
        "Concert+One:400",
        "Condiment:400",
        "Content:400",
        "Contrail+One:400",
        "Convergence:400",
        "Cookie:400",
        "Copse:400",
        "Corben:400",
        "Cormorant:400",
        "Cormorant+Garamond:400",
        "Cormorant+Infant:400",
        "Cormorant+SC:400",
        "Cormorant+Unicase:400",
        "Cormorant+Upright:400",
        "Courgette:400",
        "Courier+Prime:400",
        "Cousine:400",
        "Coustard:400",
        "Covered+By+Your+Grace:400",
        "Crafty+Girls:400",
        "Creepster:400",
        "Crete+Round:400",
        "Crimson+Pro:400",
        "Crimson+Text:400",
        "Croissant+One:400",
        "Crushed:400",
        "Cuprum:400",
        "Cute+Font:400",
        "Cutive:400",
        "Cutive+Mono:400",
        "DM+Sans:400",
        "DM+Serif+Display:400",
        "DM+Serif+Text:400",
        "Damion:400",
        "Dancing+Script:400",
        "Dangrek:400",
        "Darker+Grotesque:400",
        "David+Libre:400",
        "Dawning+of+a+New+Day:400",
        "Days+One:400",
        "Dekko:400",
        "Delius:400",
        "Delius+Swash+Caps:400",
        "Delius+Unicase:400",
        "Della+Respira:400",
        "Denk+One:400",
        "Devonshire:400",
        "Dhurjati:400",
        "Didact+Gothic:400",
        "Diplomata:400",
        "Diplomata+SC:400",
        "Do+Hyeon:400",
        "Dokdo:400",
        "Domine:400",
        "Donegal+One:400",
        "Doppio+One:400",
        "Dorsa:400",
        "Dosis:400",
        "Dr+Sugiyama:400",
        "Duru+Sans:400",
        "Dynalight:400",
        "EB+Garamond:400",
        "Eagle+Lake:400",
        "East+Sea+Dokdo:400",
        "Eater:400",
        "Economica:400",
        "Eczar:400",
        "El+Messiri:400",
        "Electrolize:400",
        "Elsie:400",
        "Elsie+Swash+Caps:400",
        "Emblema+One:400",
        "Emilys+Candy:400",
        "Encode+Sans:400",
        "Encode+Sans+Condensed:400",
        "Encode+Sans+Expanded:400",
        "Encode+Sans+Semi+Condensed:400",
        "Encode+Sans+Semi+Expanded:400",
        "Engagement:400",
        "Englebert:400",
        "Enriqueta:400",
        "Erica+One:400",
        "Esteban:400",
        "Euphoria+Script:400",
        "Ewert:400",
        "Exo:400",
        "Exo+2:400",
        "Expletus+Sans:400",
        "Fahkwang:400",
        "Fanwood+Text:400",
        "Farro:400",
        "Farsan:400",
        "Fascinate:400",
        "Fascinate+Inline:400",
        "Faster+One:400",
        "Fasthand:400",
        "Fauna+One:400",
        "Faustina:400",
        "Federant:400",
        "Federo:400",
        "Felipa:400",
        "Fenix:400",
        "Finger+Paint:400",
        "Fira+Code:400",
        "Fira+Mono:400",
        "Fira+Sans:400",
        "Fira+Sans+Condensed:400",
        "Fira+Sans+Extra+Condensed:400",
        "Fjalla+One:400",
        "Fjord+One:400",
        "Flamenco:400",
        "Flavors:400",
        "Fondamento:400",
        "Fontdiner+Swanky:400",
        "Forum:400",
        "Francois+One:400",
        "Frank+Ruhl+Libre:400",
        "Freckle+Face:400",
        "Fredericka+the+Great:400",
        "Fredoka+One:400",
        "Freehand:400",
        "Fresca:400",
        "Frijole:400",
        "Fruktur:400",
        "Fugaz+One:400",
        "GFS+Didot:400",
        "GFS+Neohellenic:400",
        "Gabriela:400",
        "Gaegu:400",
        "Gafata:400",
        "Galada:400",
        "Galdeano:400",
        "Galindo:400",
        "Gamja+Flower:400",
        "Gayathri:400",
        "Gelasio:400",
        "Gentium+Basic:400",
        "Gentium+Book+Basic:400",
        "Geo:400",
        "Geostar:400",
        "Geostar+Fill:400",
        "Germania+One:400",
        "Gidugu:400",
        "Gilda+Display:400",
        "Girassol:400",
        "Give+You+Glory:400",
        "Glass+Antiqua:400",
        "Glegoo:400",
        "Gloria+Hallelujah:400",
        "Goblin+One:400",
        "Gochi+Hand:400",
        "Gorditas:400",
        "Gothic+A1:400",
        "Goudy+Bookletter+1911:400",
        "Graduate:400",
        "Grand+Hotel:400",
        "Gravitas+One:400",
        "Great+Vibes:400",
        "Grenze:400",
        "Griffy:400",
        "Gruppo:400",
        "Gudea:400",
        "Gugi:400",
        "Gupter:400",
        "Gurajada:400",
        "Habibi:400",
        "Halant:400",
        "Hammersmith+One:400",
        "Hanalei:400",
        "Hanalei+Fill:400",
        "Handlee:400",
        "Hanuman:400",
        "Happy+Monkey:400",
        "Harmattan:400",
        "Headland+One:400",
        "Heebo:400",
        "Henny+Penny:400",
        "Hepta+Slab:400",
        "Herr+Von+Muellerhoff:400",
        "Hi+Melody:400",
        "Hind:400",
        "Hind+Guntur:400",
        "Hind+Madurai:400",
        "Hind+Siliguri:400",
        "Hind+Vadodara:400",
        "Holtwood+One+SC:400",
        "Homemade+Apple:400",
        "Homenaje:400",
        "IBM+Plex+Mono:400",
        "IBM+Plex+Sans:400",
        "IBM+Plex+Sans+Condensed:400",
        "IBM+Plex+Serif:400",
        "IM+Fell+DW+Pica:400",
        "IM+Fell+DW+Pica+SC:400",
        "IM+Fell+Double+Pica:400",
        "IM+Fell+Double+Pica+SC:400",
        "IM+Fell+English:400",
        "IM+Fell+English+SC:400",
        "IM+Fell+French+Canon:400",
        "IM+Fell+French+Canon+SC:400",
        "IM+Fell+Great+Primer:400",
        "IM+Fell+Great+Primer+SC:400",
        "Ibarra+Real+Nova:400",
        "Iceberg:400",
        "Iceland:400",
        "Imprima:400",
        "Inconsolata:400",
        "Inder:400",
        "Indie+Flower:400",
        "Inika:400",
        "Inknut+Antiqua:400",
        "Inria+Serif:400",
        "Inter:400",
        "Irish+Grover:400",
        "Istok+Web:400",
        "Italiana:400",
        "Italianno:400",
        "Itim:400",
        "Jacques+Francois:400",
        "Jacques+Francois+Shadow:400",
        "Jaldi:400",
        "Jim+Nightshade:400",
        "Jockey+One:400",
        "Jolly+Lodger:400",
        "Jomhuria:400",
        "Jomolhari:400",
        "Josefin+Sans:400",
        "Josefin+Slab:400",
        "Joti+One:400",
        "Jua:400",
        "Judson:400",
        "Julee:400",
        "Julius+Sans+One:400",
        "Junge:400",
        "Jura:400",
        "Just+Another+Hand:400",
        "Just+Me+Again+Down+Here:400",
        "K2D:400",
        "Kadwa:400",
        "Kalam:400",
        "Kameron:400",
        "Kanit:400",
        "Kantumruy:400",
        "Karla:400",
        "Karma:400",
        "Katibeh:400",
        "Kaushan+Script:400",
        "Kavivanar:400",
        "Kavoon:400",
        "Kdam+Thmor:400",
        "Keania+One:400",
        "Kelly+Slab:400",
        "Kenia:400",
        "Khand:400",
        "Khmer:400",
        "Khula:400",
        "Kirang+Haerang:400",
        "Kite+One:400",
        "Knewave:400",
        "KoHo:400",
        "Kodchasan:400",
        "Kosugi:400",
        "Kosugi+Maru:400",
        "Kotta+One:400",
        "Koulen:400",
        "Kranky:400",
        "Kreon:400",
        "Kristi:400",
        "Krona+One:400",
        "Krub:400",
        "Kulim+Park:400",
        "Kumar+One:400",
        "Kumar+One+Outline:400",
        "Kurale:400",
        "La+Belle+Aurore:400",
        "Lacquer:400",
        "Laila:400",
        "Lakki+Reddy:400",
        "Lalezar:400",
        "Lancelot:400",
        "Lateef:400",
        "Lato:400",
        "League+Script:400",
        "Leckerli+One:400",
        "Ledger:400",
        "Lekton:400",
        "Lemon:400",
        "Lemonada:400",
        "Lexend+Deca:400",
        "Lexend+Exa:400",
        "Lexend+Giga:400",
        "Lexend+Mega:400",
        "Lexend+Peta:400",
        "Lexend+Tera:400",
        "Lexend+Zetta:400",
        "Libre+Barcode+128:400",
        "Libre+Barcode+128+Text:400",
        "Libre+Barcode+39:400",
        "Libre+Barcode+39+Extended:400",
        "Libre+Barcode+39+Extended+Text:400",
        "Libre+Barcode+39+Text:400",
        "Libre+Baskerville:400",
        "Libre+Caslon+Display:400",
        "Libre+Caslon+Text:400",
        "Libre+Franklin:400",
        "Life+Savers:400",
        "Lilita+One:400",
        "Lily+Script+One:400",
        "Limelight:400",
        "Linden+Hill:400",
        "Literata:400",
        "Liu+Jian+Mao+Cao:400",
        "Livvic:400",
        "Lobster:400",
        "Lobster+Two:400",
        "Londrina+Outline:400",
        "Londrina+Shadow:400",
        "Londrina+Sketch:400",
        "Londrina+Solid:400",
        "Long+Cang:400",
        "Lora:400",
        "Love+Ya+Like+A+Sister:400",
        "Loved+by+the+King:400",
        "Lovers+Quarrel:400",
        "Luckiest+Guy:400",
        "Lusitana:400",
        "Lustria:400",
        "M+PLUS+1p:400",
        "M+PLUS+Rounded+1c:400",
        "Ma+Shan+Zheng:400",
        "Macondo:400",
        "Macondo+Swash+Caps:400",
        "Mada:400",
        "Magra:400",
        "Maiden+Orange:400",
        "Maitree:400",
        "Major+Mono+Display:400",
        "Mako:400",
        "Mali:400",
        "Mallanna:400",
        "Mandali:400",
        "Manjari:400",
        "Mansalva:400",
        "Manuale:400",
        "Marcellus:400",
        "Marcellus+SC:400",
        "Marck+Script:400",
        "Margarine:400",
        "Markazi+Text:400",
        "Marko+One:400",
        "Marmelad:400",
        "Martel:400",
        "Martel+Sans:400",
        "Marvel:400",
        "Mate:400",
        "Mate+SC:400",
        "Maven+Pro:400",
        "McLaren:400",
        "Meddon:400",
        "MedievalSharp:400",
        "Medula+One:400",
        "Meera+Inimai:400",
        "Megrim:400",
        "Meie+Script:400",
        "Merienda:400",
        "Merienda+One:400",
        "Merriweather:400",
        "Merriweather+Sans:400",
        "Metal:400",
        "Metal+Mania:400",
        "Metamorphous:400",
        "Metrophobic:400",
        "Michroma:400",
        "Milonga:400",
        "Miltonian:400",
        "Miltonian+Tattoo:400",
        "Mina:400",
        "Miniver:400",
        "Miriam+Libre:400",
        "Mirza:400",
        "Miss+Fajardose:400",
        "Mitr:400",
        "Modak:400",
        "Modern+Antiqua:400",
        "Mogra:400",
        "Molengo:400",
        "Molle:",
        "Monda:400",
        "Monofett:400",
        "Monoton:400",
        "Monsieur+La+Doulaise:400",
        "Montaga:400",
        "Montez:400",
        "Montserrat:400",
        "Montserrat+Alternates:400",
        "Montserrat+Subrayada:400",
        "Moul:400",
        "Moulpali:400",
        "Mountains+of+Christmas:400",
        "Mouse+Memoirs:400",
        "Mr+Bedfort:400",
        "Mr+Dafoe:400",
        "Mr+De+Haviland:400",
        "Mrs+Saint+Delafield:400",
        "Mrs+Sheppards:400",
        "Mukta:400",
        "Mukta+Mahee:400",
        "Mukta+Malar:400",
        "Mukta+Vaani:400",
        "Muli:400",
        "Mystery+Quest:400",
        "NTR:400",
        "Nanum+Brush+Script:400",
        "Nanum+Gothic:400",
        "Nanum+Gothic+Coding:400",
        "Nanum+Myeongjo:400",
        "Nanum+Pen+Script:400",
        "Neucha:400",
        "Neuton:400",
        "New+Rocker:400",
        "News+Cycle:400",
        "Niconne:400",
        "Niramit:400",
        "Nixie+One:400",
        "Nobile:400",
        "Nokora:400",
        "Norican:400",
        "Nosifer:400",
        "Notable:400",
        "Nothing+You+Could+Do:400",
        "Noticia+Text:400",
        "Noto+Sans:400",
        "Noto+Sans+HK:400",
        "Noto+Sans+JP:400",
        "Noto+Sans+KR:400",
        "Noto+Sans+SC:400",
        "Noto+Sans+TC:400",
        "Noto+Serif:400",
        "Noto+Serif+JP:400",
        "Noto+Serif+KR:400",
        "Noto+Serif+SC:400",
        "Noto+Serif+TC:400",
        "Nova+Cut:400",
        "Nova+Flat:400",
        "Nova+Mono:400",
        "Nova+Oval:400",
        "Nova+Round:400",
        "Nova+Script:400",
        "Nova+Slim:400",
        "Nova+Square:400",
        "Numans:400",
        "Nunito:400",
        "Nunito+Sans:400",
        "Odibee+Sans:400",
        "Odor+Mean+Chey:400",
        "Offside:400",
        "Old+Standard+TT:400",
        "Oldenburg:400",
        "Oleo+Script:400",
        "Oleo+Script+Swash+Caps:400",
        "Open+Sans:400",
        "Open+Sans+Condensed:400",
        "Oranienbaum:400",
        "Orbitron:400",
        "Oregano:400",
        "Orienta:400",
        "Original+Surfer:400",
        "Oswald:400",
        "Over+the+Rainbow:400",
        "Overlock:400",
        "Overlock+SC:400",
        "Overpass:400",
        "Overpass+Mono:400",
        "Ovo:400",
        "Oxygen:400",
        "Oxygen+Mono:400",
        "PT+Mono:400",
        "PT+Sans:400",
        "PT+Sans+Caption:400",
        "PT+Sans+Narrow:400",
        "PT+Serif:400",
        "PT+Serif+Caption:400",
        "Pacifico:400",
        "Padauk:400",
        "Palanquin:400",
        "Palanquin+Dark:400",
        "Pangolin:400",
        "Paprika:400",
        "Parisienne:400",
        "Passero+One:400",
        "Passion+One:400",
        "Pathway+Gothic+One:400",
        "Patrick+Hand:400",
        "Patrick+Hand+SC:400",
        "Pattaya:400",
        "Patua+One:400",
        "Pavanam:400",
        "Paytone+One:400",
        "Peddana:400",
        "Peralta:400",
        "Permanent+Marker:400",
        "Petit+Formal+Script:400",
        "Petrona:400",
        "Philosopher:400",
        "Piedra:400",
        "Pinyon+Script:400",
        "Pirata+One:400",
        "Plaster:400",
        "Play:400",
        "Playball:400",
        "Playfair+Display:400",
        "Playfair+Display+SC:400",
        "Podkova:400",
        "Poiret+One:400",
        "Poller+One:400",
        "Poly:400",
        "Pompiere:400",
        "Pontano+Sans:400",
        "Poor+Story:400",
        "Poppins:400",
        "Port+Lligat+Sans:400",
        "Port+Lligat+Slab:400",
        "Pragati+Narrow:400",
        "Prata:400",
        "Preahvihear:400",
        "Press+Start+2P:400",
        "Pridi:400",
        "Princess+Sofia:400",
        "Prociono:400",
        "Prompt:400",
        "Prosto+One:400",
        "Proza+Libre:400",
        "Public+Sans:400",
        "Puritan:400",
        "Purple+Purse:400",
        "Quando:400",
        "Quantico:400",
        "Quattrocento:400",
        "Quattrocento+Sans:400",
        "Questrial:400",
        "Quicksand:400",
        "Quintessential:400",
        "Qwigley:400",
        "Racing+Sans+One:400",
        "Radley:400",
        "Rajdhani:400",
        "Rakkas:400",
        "Raleway:400",
        "Raleway+Dots:400",
        "Ramabhadra:400",
        "Ramaraja:400",
        "Rambla:400",
        "Rammetto+One:400",
        "Ranchers:400",
        "Rancho:400",
        "Ranga:400",
        "Rasa:400",
        "Rationale:400",
        "Ravi+Prakash:400",
        "Red+Hat+Display:400",
        "Red+Hat+Text:400",
        "Redressed:400",
        "Reem+Kufi:400",
        "Reenie+Beanie:400",
        "Revalia:400",
        "Rhodium+Libre:400",
        "Ribeye:400",
        "Ribeye+Marrow:400",
        "Righteous:400",
        "Risque:400",
        "Roboto:400",
        "Roboto+Condensed:400",
        "Roboto+Mono:400",
        "Roboto+Slab:400",
        "Rochester:400",
        "Rock+Salt:400",
        "Rokkitt:400",
        "Romanesco:400",
        "Ropa+Sans:400",
        "Rosario:400",
        "Rosarivo:400",
        "Rouge+Script:400",
        "Rozha+One:400",
        "Rubik:400",
        "Rubik+Mono+One:400",
        "Ruda:400",
        "Rufina:400",
        "Ruge+Boogie:400",
        "Ruluko:400",
        "Rum+Raisin:400",
        "Ruslan+Display:400",
        "Russo+One:400",
        "Ruthie:400",
        "Rye:400",
        "Sacramento:400",
        "Sahitya:400",
        "Sail:400",
        "Saira:400",
        "Saira+Condensed:400",
        "Saira+Extra+Condensed:400",
        "Saira+Semi+Condensed:400",
        "Saira+Stencil+One:400",
        "Salsa:400",
        "Sanchez:400",
        "Sancreek:400",
        "Sansita:400",
        "Sarabun:400",
        "Sarala:400",
        "Sarina:400",
        "Sarpanch:400",
        "Satisfy:400",
        "Sawarabi+Gothic:400",
        "Sawarabi+Mincho:400",
        "Scada:400",
        "Scheherazade:400",
        "Schoolbell:400",
        "Scope+One:400",
        "Seaweed+Script:400",
        "Secular+One:400",
        "Sedgwick+Ave:400",
        "Sedgwick+Ave+Display:400",
        "Sevillana:400",
        "Seymour+One:400",
        "Shadows+Into+Light:400",
        "Shadows+Into+Light+Two:400",
        "Shanti:400",
        "Share:400",
        "Share+Tech:400",
        "Share+Tech+Mono:400",
        "Shojumaru:400",
        "Short+Stack:400",
        "Shrikhand:400",
        "Siemreap:400",
        "Sigmar+One:400",
        "Signika:400",
        "Signika+Negative:400",
        "Simonetta:400",
        "Single+Day:400",
        "Sintony:400",
        "Sirin+Stencil:400",
        "Six+Caps:400",
        "Skranji:400",
        "Slabo+13px:400",
        "Slabo+27px:400",
        "Slackey:400",
        "Smokum:400",
        "Smythe:400",
        "Sniglet:400",
        "Snippet:400",
        "Snowburst+One:400",
        "Sofadi+One:400",
        "Sofia:400",
        "Solway:400",
        "Song+Myung:400",
        "Sonsie+One:400",
        "Sorts+Mill+Goudy:400",
        "Source+Code+Pro:400",
        "Source+Sans+Pro:400",
        "Source+Serif+Pro:400",
        "Space+Mono:400",
        "Special+Elite:400",
        "Spectral:400",
        "Spectral+SC:400",
        "Spicy+Rice:400",
        "Spinnaker:400",
        "Spirax:400",
        "Squada+One:400",
        "Sree+Krushnadevaraya:400",
        "Sriracha:400",
        "Srisakdi:400",
        "Staatliches:400",
        "Stalemate:400",
        "Stalinist+One:400",
        "Stardos+Stencil:400",
        "Stint+Ultra+Condensed:400",
        "Stint+Ultra+Expanded:400",
        "Stoke:400",
        "Strait:400",
        "Stylish:400",
        "Sue+Ellen+Francisco:400",
        "Suez+One:400",
        "Sulphur+Point:400",
        "Sumana:400",
        "Sunflower:",
        "Sunshiney:400",
        "Supermercado+One:400",
        "Sura:400",
        "Suranna:400",
        "Suravaram:400",
        "Suwannaphum:400",
        "Swanky+and+Moo+Moo:400",
        "Syncopate:400",
        "Tajawal:400",
        "Tangerine:400",
        "Taprom:400",
        "Tauri:400",
        "Taviraj:400",
        "Teko:400",
        "Telex:400",
        "Tenali+Ramakrishna:400",
        "Tenor+Sans:400",
        "Text+Me+One:400",
        "Thasadith:400",
        "The+Girl+Next+Door:400",
        "Tienne:400",
        "Tillana:400",
        "Timmana:400",
        "Tinos:400",
        "Titan+One:400",
        "Titillium+Web:400",
        "Tomorrow:400",
        "Trade+Winds:400",
        "Trirong:400",
        "Trocchi:400",
        "Trochut:400",
        "Trykker:400",
        "Tulpen+One:400",
        "Turret+Road:400",
        "Ubuntu:400",
        "Ubuntu+Condensed:400",
        "Ubuntu+Mono:400",
        "Ultra:400",
        "Uncial+Antiqua:400",
        "Underdog:400",
        "Unica+One:400",
        "UnifrakturCook:",
        "UnifrakturMaguntia:400",
        "Unkempt:400",
        "Unlock:400",
        "Unna:400",
        "VT323:400",
        "Vampiro+One:400",
        "Varela:400",
        "Varela+Round:400",
        "Vast+Shadow:400",
        "Vesper+Libre:400",
        "Vibes:400",
        "Vibur:400",
        "Vidaloka:400",
        "Viga:400",
        "Voces:400",
        "Volkhov:400",
        "Vollkorn:400",
        "Vollkorn+SC:400",
        "Voltaire:400",
        "Waiting+for+the+Sunrise:400",
        "Wallpoet:400",
        "Walter+Turncoat:400",
        "Warnes:400",
        "Wellfleet:400",
        "Wendy+One:400",
        "Wire+One:400",
        "Work+Sans:400",
        "Yanone+Kaffeesatz:400",
        "Yantramanav:400",
        "Yatra+One:400",
        "Yellowtail:400",
        "Yeon+Sung:400",
        "Yeseva+One:400",
        "Yesteryear:400",
        "Yrsa:400",
        "ZCOOL+KuaiLe:400",
        "ZCOOL+QingKe+HuangYou:400",
        "ZCOOL+XiaoWei:400",
        "Zeyada:400",
        "Zhi+Mang+Xing:400",
        "Zilla+Slab:400",
        "Zilla+Slab+Highlight:400"
      ]
    };

    var Fontselect = (function(){

      function Fontselect(original, o) {
        if (!o.systemFonts) { o.systemFonts = []; }
        if (!o.localFonts) { o.localFonts = []; }
        if (!o.googleFonts) { o.googleFonts = []; }

        var googleFonts = [];
        for (var i = 0; i < o.googleFonts.length; i++) {
          var item = o.googleFonts[i].split(':'); // Unna:regular,italic,700,700italic
          var fontName = item[0], fontVariants = item[1] ? item[1].split(',') : [];
          for (var v = 0; v < fontVariants.length; v++) {
            googleFonts.push(fontName + ':' + fontVariants[v]);
          }
        }
        o.googleFonts = googleFonts;

        this.options = o;
        this.$original = $(original);
        this.setupHtml();
        this.getVisibleFonts();
        this.bindEvents();
        this.query = '';
        this.keyActive = false;
        this.searchBoxHeight = 0;

        var font = this.$original.val();
        if (font) {
          this.updateSelected();
          this.addFontLink(font);
        }
      }

      Fontselect.prototype = {
        keyDown: function(e) {

          function stop(e) {
            e.preventDefault();
            e.stopPropagation();
          }

          this.keyActive = true;
          if (e.keyCode == 27) {// Escape
            stop(e);
            this.toggleDropdown('hide');
            return;
          }
          if (e.keyCode == 38) {// Cursor up
            stop(e);
            var $li = $('li.active', this.$results), $pli = $li.prev('li');
            if ($pli.length > 0) {
              $li.removeClass('active');
              this.$results.scrollTop($pli.addClass('active')[0].offsetTop - this.searchBoxHeight);
            }
            return;
          }
          if (e.keyCode == 40) {// Cursor down
            stop(e);
            var $li = $('li.active', this.$results), $nli = $li.next('li');
            if ($nli.length > 0) {
              $li.removeClass('active');
              this.$results.scrollTop($nli.addClass('active')[0].offsetTop - this.searchBoxHeight);
            }
            return;
          }
          if (e.keyCode == 13) {// Enter
            stop(e);
            $('li.active', this.$results).trigger('click');
            return;
          }
          this.query += String.fromCharCode(e.keyCode).toLowerCase();
          var $found = $("li[data-query^='"+ this.query +"']").first();
          if ($found.length > 0) {
            $('li.active', this.$results).removeClass('active');
            this.$results.scrollTop($found.addClass('active')[0].offsetTop);
          }
        },

        keyUp: function(e) {
          this.keyActive = false;
        },

        bindEvents: function() {
          var self = this;

          $('li', this.$results)
          .click(__bind(this.selectFont, this))
          .mouseover(__bind(this.activateFont, this));

          this.$select.click(__bind(function() { self.toggleDropdown('show') }, this));

          // Call like so: $("input[name='ffSelect']").trigger('setFont', [fontFamily, fontWeight]);
          this.$original.on('setFont', function(evt, fontFamily, fontWeight) {
            fontWeight = fontWeight || 400;

            var fontSpec = fontFamily.replace(/ /g, '+') + ':' + fontWeight;

            var $li = $("li[data-value='"+ fontSpec +"']", self.$results);
            if ($li.length == 0) {
              fontSpec = fontFamily.replace(/ /g, '+');
            }
            console.log(fontSpec);
            $li = $("li[data-value='"+ fontSpec +"']", self.$results);
            $('li.active', self.$results).removeClass('active');
            $li.addClass('active');

            self.$original.val(fontSpec);
            self.updateSelected();
            self.addFontLink($li.data('value'));
            //$li.trigger('click'); // Removed 2019-10-16
          });
          this.$original.on('change', function() {
            self.updateSelected();
            self.addFontLink($('li.active', self.$results).data('value'));
          });

          if (this.options.searchable) {
            this.$input.on('keyup', function() {
              var q = this.value.toLowerCase();
              // Hide options that don't match query:
              $('li', self.$results).each(function() {
                if ($(this).text().toLowerCase().indexOf(q) == -1) {
                  $(this).hide();
                }
                else {
                  $(this).show();
                }
              })
            })
          }

          $(document).on('click', function(e) {
            if ($(e.target).closest('.'+self.options.style).length === 0) {
              self.toggleDropdown('hide');
            }
          });
        },

        toggleDropdown: function(hideShow) {
          if (hideShow === 'hide') {
            // Make inactive
            this.$element.off('keydown keyup');
            this.query = '';
            this.keyActive = false;
            this.$element.removeClass('font-select-active');
            this.$drop.hide();
            clearInterval(this.visibleInterval);
          } else {
            // Make active
            this.$element.on('keydown', __bind(this.keyDown, this));
            this.$element.on('keyup', __bind(this.keyUp, this));
            this.$element.addClass('font-select-active');
            this.$drop.show();

            this.visibleInterval = setInterval(__bind(this.getVisibleFonts, this), 500);
            this.searchBoxHeight = this.$search.outerHeight();
            this.moveToSelected();

            /*
            if (this.options.searchable) {
            // Focus search box
            $this.$input.focus();
          }
          */
        }
      },

      selectFont: function() {
        var font = $('li.active', this.$results).data('value');
        this.$original.val(font).change();
        this.updateSelected();
        this.toggleDropdown('hide'); // Hide dropdown
      },

      moveToSelected: function() {
        var font = this.$original.val().replace(/ /g, '+');
        var $li = font ? $("li[data-value='"+ font +"']", this.$results) : $li = $('li', this.$results).first();
        this.$results.scrollTop($li.addClass('active')[0].offsetTop - this.searchBoxHeight);
      },

      activateFont: function(e) {
        if (this.keyActive) { return; }
        $('li.active', this.$results).removeClass('active');
        $(e.target).addClass('active');
      },

      updateSelected: function() {
        var font = this.$original.val();
        $('span', this.$element).text(this.toReadable(font)).css(this.toStyle(font));
      },

      setupHtml: function() {
        this.$original.hide();
        this.$element = $('<div>', {'class': this.options.style});
        this.$select = $('<span tabindex="0">' + this.options.placeholder + '</span>');
        this.$search = $('<div>', {'class': 'fs-search'});
        this.$input = $('<input>', {type:'text'});
        if (this.options.placeholderSearch) {
          this.$input.attr('placeholder', this.options.placeholderSearch);
        }
        this.$search.append(this.$input);
        this.$drop = $('<div>', {'class': 'fs-drop'});
        this.$results = $('<ul>', {'class': 'fs-results'});
        this.$original.after(this.$element.append(this.$select, this.$drop));
        this.options.searchable && this.$drop.append(this.$search);
        this.$drop.append(this.$results.append(this.fontsAsHtml())).hide();
      },

      fontsAsHtml: function() {
        var i, r, s, style, h = '';
        var systemFonts = this.options.systemFonts;
        var localFonts = this.options.localFonts;
        var googleFonts = this.options.googleFonts;

        for (i = 0; i < systemFonts.length; i++){
          r = this.toReadable(systemFonts[i]);
          s = this.toStyle(systemFonts[i]);
          style = 'font-family:' + s['font-family'];
          if ((localFonts.length > 0 || googleFonts.length > 0) && i == systemFonts.length-1) {
            style += ';border-bottom:1px solid #444'; // Separator after last system font
          }
          h += '<li data-value="'+ systemFonts[i] +'" data-query="' + systemFonts[i].toLowerCase() + '" style="' + style + '">' + r + '</li>';
        }

        for (i = 0; i < localFonts.length; i++){
          r = this.toReadable(localFonts[i]);
          s = this.toStyle(localFonts[i]);
          style = 'font-family:' + s['font-family'];
          if (googleFonts.length > 0 && i == localFonts.length-1) {
            style += ';border-bottom:1px solid #444'; // Separator after last local font
          }
          h += '<li data-value="'+ localFonts[i] +'" data-query="' + localFonts[i].toLowerCase() + '" style="' + style + '">' + r + '</li>';
        }

        for (i = 0; i < googleFonts.length; i++){
          r = this.toReadable(googleFonts[i]);
          s = this.toStyle(googleFonts[i]);
          style = 'font-family:' + s['font-family'] + ';font-weight:' + s['font-weight'] + ';font-style:' + s['font-style'];
          h += '<li data-value="'+ googleFonts[i] +'" data-query="' + googleFonts[i].toLowerCase() + '" style="' + style + '">' + s['font-family'].replace(/'/g,'') + '</li>';
        }

        return h;
      },

      toReadable: function(font) {
        return font.replace(/[\+|:]/g, ' ').replace(/(\d+)italic/, '$1 italic');
      },

      toStyle: function(font) {
        var t = font.split(':'), italic = false;
        if (t[1] && /italic/.test(t[1])) {
          italic = true;
          t[1] = t[1].replace('italic','');
        }

        return {'font-family':"'"+this.toReadable(t[0])+"'", 'font-weight': (t[1] || 400), 'font-style': italic?'italic':'normal'};
      },

      getVisibleFonts: function() {
        if(this.$results.is(':hidden')) { return; }

        var fs = this;
        var top = this.$results.scrollTop();
        var bottom = top + this.$results.height();

        if (this.options.lookahead){
          var li = $('li', this.$results).first().height();
          bottom += li * this.options.lookahead;
        }

        $('li:visible', this.$results).each(function(){
          var ft = $(this).position().top+top;
          var fb = ft + $(this).height();

          if ((fb >= top) && (ft <= bottom)){
            fs.addFontLink($(this).data('value'));
          }
        });
      },

      addFontLink: function(font) {
        if (fontsLoaded[font]) { return; }
        fontsLoaded[font] = true;

        if (this.options.googleFonts.indexOf(font) > -1) {
          $('link:last').after('<link href="' + this.options.googleApi + font + '" rel="stylesheet" type="text/css">');
        }
        else if (this.options.localFonts.indexOf(font) > -1) {
          font = this.toReadable(font);
          $('head').append("<style> @font-face { font-family:'" + font + "'; font-style:normal; font-weight:400; src:local('" + font + "'), url('" + this.options.localFontsUrl + font + ".woff') format('woff'); } </style>");
        }
        // System fonts need not be loaded!
      }
    }; // End prototype

    return Fontselect;
  })();

  return this.each(function() {
    // If options exist, merge them
    if (options) { $.extend(settings, options); }

    return new Fontselect(this, settings);
  });
};
})(jQuery);
