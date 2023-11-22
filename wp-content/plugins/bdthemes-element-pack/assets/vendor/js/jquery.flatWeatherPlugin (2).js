
//2017-05-25 v1.3
// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {

		
		var pluginName = "flatWeatherPlugin";

		// Create the defaults once
	    var defaults = {
				location: "Boston, MA", //city, region
				country: "USA", //country
				zmw: "02108.1.99999", //wunderground location identifier, for wunderground only
				displayCityNameOnly: false,
				api : "darksky", //api: yahoo or openweathermap
				forecast: 5, //number of days to forecast, max 5, max 3 wunderground
				apikey : "", //required api key for openweathermap and wunderground
				latitude : "", //required for darksky
				longitude : "", //required for darksky
				view : "full", //options: simple, today, partial, forecast, full
				render : false, //render: false if you to make your own markup, true plugin generates markup
				loadingAnimation: true, //show loading animation
				//units : "metric" or "imperial" default: "auto"
				strings : { //strings for translation
							days: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
							min : "Min",
							max : "Max",
							direction :  ["N","NNE","NE","ENE","E","ESE","SE","SSE","S","SSW","SW","WSW","W","WNW","NW", "NNW"]
					 	  },
				timeformat : "12", //"12" or "24" time format
				lang: "EN" //other languages supported see readme

			};

		var apiurls = {
			"openweathermap" : [
								"https://api.openweathermap.org/data/2.5/weather", 
								"https://api.openweathermap.org/data/2.5/forecast/daily"
							   ],
			"wunderground" : ["https://api.wunderground.com/api/apikey/conditions/forecast/astronomy/"],
			"darksky" : ["https://api.darksky.net/forecast/apikey/"],
		};

		// Plugin Constructor
		function Plugin (element, options ) {

			this.element = element;

			// jQuery has an extend method which merges the contents of two or
			// more objects, storing the result in the first object. The first object
			// is generally empty as we don't want to alter the default options for
			// future instances of the plugin
			this.settings = $.extend( {}, defaults, options );
			
			//set units if otherwise not set
			if (!this.settings.units || this.settings.units == "auto") {
				//basically just support for auto units of USA
				this.settings.units = (["united states", "usa", "united states of america", "us"].indexOf(this.settings.country.toLowerCase()) == -1)?"metric":"imperial";
			}
			
			//bound forecast to max of 5 days, api won't return more then that
			this.settings.forecast = Math.min(this.settings.forecast, 5);

			//bound forecast for wundergroudn to max 3, thats all it returns with standard api
			if (this.settings.api == "wunderground") this.settings.forecast = Math.min(this.settings.forecast, 3);
			
			//store plugin name for local reference
			this._name = pluginName;
			
			this.once = false;
			
			//call initilizaiton
			this.init();
		};

		// Avoid Plugin.prototype conflicts
		$.extend(Plugin.prototype, {
			init: function () {
				//if you want the pluging to render markup init will do all the work
				//otherwise you are on your own
				if (this.settings.render) {

					//if first run show loading icon (if enabled)
					if (this.settings.loadingAnimation && !this.once) {
						//add a loading spinner, animated with css
						this.loading = $("<div/>", {"id" : "flatWeatherLoading", "class" : "wi loading"});
						this.loading.appendTo(this.element);
					}
					
					this.fetchWeather().then(this.render, this.error);
					
				}
				this.once = true; //init has happened, can be used to prevent some init tasks happening again
			},
			fetchWeather: function () {
				//Fetches the weather from the API with an ajax request
				//Returns a promise of (weather object, this)

				//scope of this for nested functions
				var that = this;

				//create promise
				var promise = new $.Deferred();


				//data params to send along with each ajax request
				//array because some apis may require multiple requests
				//params[0] is sent to apiurls[api][0] and so on
				var params = []; 

				//build location query string
				var location = this.settings.location + " " + this.settings.country;

				//build the paramaters required for specified api
				if (this.settings.api == "openweathermap") {
					//openweathermap requires two requests: one for today, another for the forecast.
					
					//see openweathermap api for details on params passed to api

					//the first request grabs the daily forecast
					var parameters = {}; 
					if( this.settings.lat && this.settings.lon ){
					    parameters.lat = this.settings.lat;
					    parameters.lon = this.settings.lon;
					} else {
					    parameters.q = location;
					}
					parameters.units = this.settings.units;
					if(this.settings.apikey) parameters.appid = this.settings.apikey;

					if (this.settings.lang) parameters.lang = this.settings.lang;

					params.push(parameters); //params for first request url
					
					//same as the first with added cnt forecast paramater in days
					//the second request grabs the forecast for the number of days requested
					parameters.cnt = this.settings.forecast + 1; //plus one to include today
					params.push(parameters); //params for second request url

				}
				else if (this.settings.api == "wunderground") {
					//sub in the wunderground api-key
					apiurls[this.settings.api][0] = apiurls[this.settings.api][0].replace(/apikey/, this.settings.apikey);
					//the other paramater is added below, as wunderground format is a little different
				}
				else if (this.settings.api == "darksky") {
				    //Build this out in your backend. Darksy doesn't allow calls from the client side (JS)
				    apiurls[this.settings.api][0] = apiurls[this.settings.api][0].replace(/apikey/, this.settings.apikey);
				}


				//for each request send the associated paramaters, then when all are done render all data
				var requests = []; //requests sent
				//for each url in apiurls for the api set, send the associated params to it in an ajax request
				for (var i = 0; i < apiurls[this.settings.api].length; i++) {
					//jquery ajax request promise
					if (this.settings.api == "wunderground") {
						//build wunderground url differently
						requests.push($.get(apiurls[this.settings.api][i]+ "lang:" + this.settings.lang + "/q/zmw:" + this.settings.zmw + ".json"));					
					} else if (this.settings.api == "darksky") {
						//build wunderground url differently
						requests.push($.get(apiurls[this.settings.api][i]+ this.settings.latitude + "," + this.settings.longitude ));					
					}
					else {
						requests.push($.get(apiurls[this.settings.api][i], params[i]));
					}
				}

				//when all request promises are done
				$.when.apply(this, requests)
		    	.done(function(){
		    		
		    		//grab the result from the promise as passed by arguments 
		    		//and convert it to an actual array with slice
		    		var args = Array.prototype.slice.call(arguments);

					//remove a layer of nesting for easier use
					 //the [0] element is the result, the rest of the array is
					 //info about the ajax request and promise that we can toss
					if (requests.length > 1) {
						//if multiple requests, each promise result of the ajax request is part of an array
						args = args.map(function(val) { return val[0]});
					}
					else {
						args = args[0];
					}
				

					//check for results that returned http 200 but had errors from api
					if (that.settings.api == "openweathermap" && !(args[0].cod == "200" && args[1].cod == "200")) {
						console.log("Error interacting with the openweathermap api see error object below for details:");
						console.log(args);
						promise.reject(args, that);
					}
					else if (that.settings.api == "wunderground" && args.response && args.response.error) {
						console.log("Error interacting with the wunderground api see error object below for details:");
						console.log(args);
						promise.reject(args, that);
					}
					else if (that.settings.api == "darksky" && args.status == 403) {
					    console.log("Error authenticating with the darkmap api see error object below for details:");
					    console.log(args);
					    promise.reject(args, that);
					}
					else {


						//now take that fancy api data and map it to a common format with datamapper function
						var weather = datamapper(args, that.settings);
						
						that._weather = weather; //store it on the instance

						$.data( that.element, "weather", weather); //and store it on the DOM for general use
	
						promise.resolve(weather, that);

					}


		    	})
				.fail(function(error){  	
					//TODO draw fails.
					//console.log("fail");
					promise.reject(error, that);
				 });

				return promise;

			},
			error : function(error, context) {

				if (!context) {
					//if called directly and not via plugin we need to set context to this vs passed when a promise
					context = this;
				}

				if (context.settings.loadingAnimation && context.settings.render) {
					context.loading.remove(); //remove loading spinner
				}

				if (context.settings.api == "openweathermap") {
					if (error.responseJSON.cod != "200") {
						error = error.responseJSON.cod + " " + error.responseJSON.message + " See console log for details.";
					} 
					else {
						error = error.responseJSON.message + " See console log for details.";
					}
				}
				else if (context.settings.api == "wunderground") {
					error = error.response.error.type + " See console log for details.";
				}
				else {
				    error = "Sorry, the weather service is currently down. Please try again later.";
				}


				var div = $("<div/>", {"class": "flatWeatherPlugin " + context.settings.view});
				$("<h2/>").text("Error").appendTo(div);
				$("<p/>").text(error).appendTo(div);
				$(context.element).html(div); //recall that this.element is set in plugin constructor
				return $(context.element);
			},
			//Generates the DOM elements
			render : function (weather, context) {

				if (!context) {
					//if called directly and not via plugin we need to set context to this vs passed when a promise
					context = this;
					weather = this._weather;
				}

				//string showing degree symbol + F or C
				var degrees = context.settings.units == "metric"?"&#176;C":"&#176;F";
				
				if (context.settings.loadingAnimation && context.settings.render) {
					context.loading.remove(); //remove loading spinner
				}

				//Now that we have everything lets make a dom fragment of our data.
				//Then append that fragment once to the dom once its all made.
				//There is a bunch of if switches for various view options but this
				//is mostly self-explainatory dom generating code from the weather object
				var div = $("<div/>", {"class": "flatWeatherPlugin " + context.settings.view});
				
				if (context.settings.displayCityNameOnly) {
					$("<h2/>").text(weather.city).appendTo(div);
				}
				else {
					$("<h2/>").text(weather.location).appendTo(div);
				}
				
				
				if (context.settings.view != "forecast") {
					var today = $("<div/>", {"class": "wiToday"});
					var iconGroup = $("<div/>", {"class": "wiIconGroup"});
					$("<div/>", {"class" : "wi "+ "wi"+weather.today.code}).appendTo(iconGroup);
					$("<p/>", {"class" : "wiText"}).text(weather.today.desc).appendTo(iconGroup);
					iconGroup.appendTo(today);
					$("<p/>", {"class" : "wiTemperature"}).html(weather.today.temp.now + "<sup>" + degrees + "</sup>").appendTo(today);
					today.appendTo(div);
				}

				if (context.settings.view != "simple") {
					var detail = $("<div/>", {"class": "wiDetail"});
					
					if (context.settings.view == "partial") {
						$("<p/>", {"class" : "wiDay"}).text(weather.today.day).appendTo(today);
					}

					if (context.settings.view != "partial") {
						if (context.settings.view != "today") {
							$("<p/>", {"class" : "wiDay"}).text(weather.today.day).appendTo(detail);
						}
						var astro = $("<ul/>", {"class" : "astronomy"}).appendTo(detail);
						$("<li/>", {"class" : "wi sunrise"}).text(weather.today.sunrise).appendTo(astro);
						$("<li/>", {"class" : "wi sunset"}).text(weather.today.sunset).appendTo(astro);
						var temp = $("<ul/>", {"class" : "temp"}).appendTo(detail);
						$("<li/>").html(context.settings.strings.max + ": " + weather.today.temp.max + "<sup>" + degrees + "</sup>").appendTo(temp);
						$("<li/>").html(context.settings.strings.min + ": " + weather.today.temp.min + "<sup>" + degrees + "</sup>").appendTo(temp);
						var atmo = $("<ul/>", {"class" : "atmosphere"}).appendTo(detail);
						$("<li/>", {"class" : "wi humidity"}).text(weather.today.humidity).appendTo(atmo);
						$("<li/>", {"class" : "wi pressure"}).text(weather.today.pressure).appendTo(atmo);
						$("<li/>", {"class" : "wi wind"}).text(formatWind(weather.today.wind.speed, weather.today.wind.deg, context.settings.units, context.settings.strings.direction)).appendTo(atmo);
						detail.appendTo(today);
					}


					if (context.settings.view != "today" || context.settings.view == "forecast") {
						var forecast = $("<ul/>", {"class": "wiForecasts"});
						var startingIndex = (context.settings.view == "forecast")?0:1;
						//index should include today for forecast view exclude for other views
						for (var i = startingIndex; i < weather.forecast.length; i++) {
							var day = $("<li/>", {"class" : "wiDay"}).html("<span>"+weather.forecast[i].day+"</span>").appendTo(forecast);
							var sub = $("<ul/>", {"class" : "wiForecast"}).appendTo(day);
							$("<li/>", {"class" : "wi "+ "wi"+ weather.forecast[i].code}).appendTo(sub);
							$("<li/>", {"class" : "wiMax"}).html(weather.forecast[i].temp.max + "<sup>" + degrees + "</sup>").appendTo(sub);
							$("<li/>", {"class" : "wiMin"}).html(weather.forecast[i].temp.min + "<sup>" + degrees + "</sup>").appendTo(sub);
						}
						forecast.appendTo(div);
					}
				}

 
				//now append our dom fragment to the target element
				$(context.element).html(div); //recall that this.element is set in plugin constructor

				return $(context.element);

			}

		});


		//jQuery Constructor
		// A lightweight plugin wrapper on the jquery fn constructor,
		// preventing against multiple instantiations on the same element
		$.fn[pluginName] = function ( options, args ) {
			if ($.isFunction(Plugin.prototype[options])) {
				//enable function access via .flatWeatherPlugin('function', 'args')
				//grab the plugin instance from the dom reference and call function with any args
				//return the results of the  
				return this.data("plugin_" + pluginName)[options](args);
			}
			//return this for jquery chainability
			return this.each(function() {
				//check if plugin has been attached to the dom
				if (!$.data(this, "plugin_" + pluginName)) {
					var plugin = new Plugin(this, options); //call constructor
					return $.data(this, "plugin_" + pluginName, plugin); //attach plugin instance to the dom data
				}
			});
		};


		/* 
		//datamapper converts raw aka dirty un-standardize data from either api
		//into a unified format for easier use as follows:
			{
				location : String, //as returned back from api
				today : {
					temp : {
						//temperatures are in units requested from api
						now : Number, ex. 18 
						min : Number, ex. 24
						max : Number ex. 12
					},
					desc : String, ex. "Partly Cloudy"
					code : Number, ex. "801" see css or weather codes for meaning
					wind : {
						speed : 4, //either km/h or mph
						deg : Number, //direction in degrees from North
					},
					pressure : Number, //barometric pressure
					humidity : Number, //% humidity
					sunrise : Time,
					sunset : Time,
					day :  String,

				},
				forecast : [{Day: String, code:Number, desc: String, temp : {min:number, max:number}}]
			}
		//note: input data is in an array of the returned api result request(s) in the same order as setup in the apiurls
		//All data manipulation and cleaning up happens below
		//making this was tedious.
		*/
		function datamapper (input, settings) {

			var out = {}; //map input to out

			if (settings.api == "openweathermap") {

				//data[0] is current weather, data[1] is forecast
				if (input[0].name != "") {
					out.location = input[0].name + ", " + input[0].sys.country;
					out.city =  input[0].name;
				}
				else if (input[1].city.name != ""){ //sometimes the api doesn't return a location. weird, try the name from second request
					out.location = input[1].city.name + ", " + input[1].city.country;
					out.city =  input[1].city.name;
				}
				else { //still no location? fall back to settings
					out.location =  settings.location + ", " + settings.country;
					out.city = settings.location;
				} 

				out.today = {};
				out.today.temp = {};
				out.today.temp.now = Math.round(input[0].main.temp);
				out.today.temp.min = Math.round(input[0].main.temp_min);
				out.today.temp.max = Math.round(input[0].main.temp_max);

				out.today.desc = input[0].weather[0].description.capitalize();
				out.today.code = input[0].weather[0].id; 
				//no weather id code remapping needed, we will use this as our default weather code system
				//and convert all other codes to the openweathermap weather code format

				out.today.wind = input[0].wind;
				out.today.humidity = input[0].main.humidity;
				out.today.pressure = input[0].main.pressure;
				out.today.sunrise = epochToHours(input[0].sys.sunrise, settings.timeformat);
				out.today.sunset = epochToHours(input[0].sys.sunset, settings.timeformat);
				
				out.today.day = getDayString(new Date(), settings.strings.days);
				
				out.forecast = [];
				for (var i = 0; i < settings.forecast; i++) {
					var forecast = {};
					forecast.day = getDayString(new Date(input[1].list[i].dt * 1000), settings.strings.days); //api time is in unix epoch
					forecast.code = input[1].list[i].weather[0].id;
					forecast.desc = input[1].list[i].weather[0].description.capitalize();
					forecast.temp = {max: Math.round(input[1].list[i].temp.max), min: Math.round(input[1].list[i].temp.min)}
					out.forecast.push(forecast);
				}

			}
			else if (settings.api == "darksky") {
			    out.location = settings.location; //darksky doesn't return a city/state

			    out.today = {};
			    out.today.temp = {};
			    out.today.temp.now = Math.round(input.currently.temperature);
			    out.today.temp.min = Math.round(input.daily.data[0].temperatureMin);
			    out.today.temp.max = Math.round(input.daily.data[0].temperatureMax);

			    out.today.desc = input.daily.data[0].summary.capitalize();

			    //key = darksky code, value = standard code (based on openweathermap codes)
			        // -hail, thunderstorm, and torndado are not implemented by darksky yet, added anyways
			        // -if partly-cloudy-night is the worst weather condition that was found, that it was clear during the day.
			    var codes = {
			        'clear-day' : '800',
			        'clear-night' : '800',
			        'rain' : '521',
			        'snow' : '601',
			        'sleet' : '611',
			        'wind' : '954',
			        'fog' : '741',
			        'cloudy' : '802',
			        'partly-cloudy-day' : '802',
			        'partly-cloudy-night' : '800',
			        'hail' : '906',
			        'thunderstorm' : '200',
			        'tornado' : '900',
			    }

			    out.today.code = codes[input.currently.icon];

			    out.today.wind = {}
			    out.today.wind.speed = input.currently.windSpeed;
			    out.today.wind.deg = input.currently.windBearing;
			    
			    out.today.humidity = input.currently.humidity;
			    out.today.pressure = input.currently.pressure;
			    out.today.sunrise = epochToHours(input.daily.data[0].sunriseTime);
			    out.today.sunset = epochToHours(input.daily.data[0].sunsetTime);

			    out.today.day = getDayString(new Date(), settings.strings.days);
			    
			    out.forecast = [];
			    for (var i = 0; i < settings.forecast; i++) {
			        var forecast = {};
			        forecast.day = getDayString(new Date(input.daily.data[i].time * 1000), settings.strings.days); //api time is in unix epoch
			        forecast.code = codes[input.daily.data[i].icon];
			        forecast.desc = input.daily.data[i].summary.capitalize();
			        forecast.temp = {max: Math.round(input.daily.data[i].temperatureMax), min: Math.round(input.daily.data[i].temperatureMin)}
			        out.forecast.push(forecast);
				}
			}
			else if (settings.api == "wunderground") {

				//key = wungerground icon code, value = standard code (based on openweathermap codes)
				//lacking a standard coding system, we will use icon name wundergroudn specifies as code
				var codes = {
					chanceflurries : "600",
					chancerain : "500",
					chancesleet	: "611",
					chancesnow	: "600",
					chancetstorms : "200",
					clear : "800",
					cloudy : "802",
					flurries : "600",
					fog : "741",
					hazy : "721",	
					mostlycloudy : "802",
					mostlysunny	 : "802",
					partlycloudy : "802",
					partlysunny	: "802",
					sleet : "611",
					rain : "501",
					snow : "601",
					sunny : "800",
					tstorms	: "211",
					unknown	: "951",
				};

				out.location = input.current_observation.display_location.full;
				out.city = input.current_observation.display_location.city;

				out.today = {};
				out.today.temp = {};

				if  (settings.units == "metric") {
					out.today.temp.now = Math.round(input.current_observation.temp_c);
					out.today.temp.min = Math.round(input.forecast.simpleforecast.forecastday[0].low.celsius);
					out.today.temp.max = Math.round(input.forecast.simpleforecast.forecastday[0].high.celsius);
				}
				else { //imperial
					out.today.temp.now = Math.round(input.current_observation.temp_f);
					out.today.temp.min = Math.round(input.forecast.simpleforecast.forecastday[0].low.fahrenheit);
					out.today.temp.max = Math.round(input.forecast.simpleforecast.forecastday[0].high.fahrenheit);
				}

				out.today.desc = input.current_observation.weather;
				out.today.code = codes[input.current_observation.icon];

				out.today.wind = {};
				if  (settings.units == "metric") {
					out.today.wind.speed = input.current_observation.wind_kph;
				}
				else { //imperial
					out.today.wind.speed = input.current_observation.wind_mph;
				}
				out.today.wind.deg = input.current_observation.wind_degrees;

				out.today.humidity = input.current_observation.relative_humidity;
				if  (settings.units == "metric") {
					out.today.pressure = input.current_observation.pressure_mb;
				}
				else { //imperial
					out.today.pressure = input.current_observation.pressure_in;
				}

				var sunrise_minutes = input.sun_phase.sunrise.minute;
				var sunset_minutes = input.sun_phase.sunset.minute;

				if (settings.timeformat == "12") {
					out.today.sunrise = input.sun_phase.sunrise.hour + ":" + sunrise_minutes + " AM";
					var hours  = input.sun_phase.sunset.hour  % 12;
		 			hours = hours ? hours : 12; // the hour '0' should be '12'
					out.today.sunset = hours + ":" + sunset_minutes + " PM";
				}
				else { //24
					out.today.sunrise = input.sun_phase.sunrise.hour + ":" + sunrise_minutes;
					out.today.sunset = input.sun_phase.sunset.hour + ":" + sunset_minutes;
				}

				
				out.today.day = getDayString(new Date(), settings.strings.days);
				
				out.forecast = [];
				for (var i = 0; i < settings.forecast+1; i++) {
					var forecast = {};
					forecast.day = getDayString(new Date(input.forecast.simpleforecast.forecastday[i].date.epoch * 1000), settings.strings.days); //api time is in unix epoch
					forecast.code = codes[input.forecast.simpleforecast.forecastday[i].icon];
					forecast.desc = input.forecast.simpleforecast.forecastday[i].conditions;
					if (settings.units == "metric") { 
						forecast.temp = {max: Math.round(input.forecast.simpleforecast.forecastday[i].high.celsius), min: Math.round(input.forecast.simpleforecast.forecastday[i].low.celsius)}
					}
					else {
						forecast.temp = {max: Math.round(input.forecast.simpleforecast.forecastday[i].high.fahrenheit), min: Math.round(input.forecast.simpleforecast.forecastday[i].low.fahrenheit)}
					}
					out.forecast.push(forecast);
				}


			}

			return out;

		};

		//Helpers
		String.prototype.capitalize = function() {
		    return this.charAt(0).toUpperCase() + this.slice(1);
		};

		//take a date object and return a day string
		function getDayString(date, days) {
		  return days[date.getDay()];
		};

		//converts and epoch time in seconds to hours in the day
		function epochToHours(date, timeformat) {
		  date = new Date(date * 1000);
		  var hours =  date.getHours();
		  var minutes = date.getMinutes();
		  var ampm = hours >= 12 ? 'PM' : 'AM';
		  if (timeformat == "24") {
		  	ampm = "";
		  	hours = hours < 10 ? '0'+hours : hours;
		  }
		  if (timeformat == "12") hours = hours % 12;
		  hours = hours ? hours : 12; // the hour '0' should be '12'
		  minutes = minutes < 10 ? '0'+minutes : minutes;
		  var strTime = hours + ':' + minutes + ' ' + ampm;
		  return strTime;
		};

		//Takes wind speed, direction in degrees and units 
		//and returns a string ex. (8.5, 270, "metric") returns "W 8.5 km/h"
		function formatWind(speed, degrees, units, direction) {
			var wd = degrees;
			if ((wd >= 0 && wd <= 11.25) || (wd > 348.75 && wd <= 360))  {
				wd = direction[0];
			}
			else if (wd > 11.25 && wd <= 33.75){
				wd = direction[1];
			}
			else if (wd > 33.75 && wd <= 56.25){
				wd = direction[2];
			}
			else if (wd > 56.25 && wd <= 78.75){
				wd = direction[3];
			}
			else if (wd > 78.75 && wd <= 101.25){
				wd = direction[4];
			}
			else if (wd > 101.25 && wd <= 123.75){
				wd = direction[5];
			}
			else if (wd > 123.75 && wd <= 146.25){
				wd = direction[6];
			}
			else if (wd > 146.25 && wd <= 168.75){
				wd = direction[7];
			}
			else if (wd > 168.75 && wd <= 191.25){
				wd = direction[8];
			}
			else if (wd > 191.25 && wd <= 213.75){
				wd = direction[9];
			}
			else if (wd > 213.75 && wd <= 236.25){
				wd = direction[10];
			}
			else if (wd > 236.25 && wd <= 258.75){
				wd = direction[11];
			}
			else if (wd > 258.75 && wd <= 281.25){
				wd = direction[12];
			}
			else if (wd > 281.25 && wd <= 303.75){
				wd = direction[13];
			}
			else if (wd > 303.75 && wd <= 326.25){
				wd = direction[14];
			}
			else if (wd > 326.25 && wd <= 348.75){
				wd = direction[15];
			}
			if (!wd) wd = "";
			var speedUnits = (units == "metric")?"km/h":"mph";
			return wd + " " + speed + " " + speedUnits;
		};


})( jQuery, window, document );
