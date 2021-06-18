(function() {
	
	var $,
		urls,
		queue,
		abort,
		loaded,
		loading,
		apiBaseUrl = 'https://api.weatherbit.io/v2.0/forecast/daily?';
		
	window.RsWeatherAddOn = function(_$, slider, refresh) {
	
		if(!_$ || !slider || !slider.length || typeof rev_slider_weather_addon === 'undefined' || refresh === 'false') return;
		
		$ = _$;
		var layerz = slider.find('rs-layer[data-weatheraddon], .rs-layer[data-weatheraddon]');
		if(!layerz.length) return;
		
		refresh = parseInt(refresh, 10);
		if(refresh > 0) {
			
			var timer;
			refresh *= 60000;
			$.event.special.rsWeatherDestroyed = {remove: function(evt) {evt.handler();}};
			
			slider.one('revolution.slide.onloaded', function() {
				
				layerz.each(function() {
					$(this).data('weatheraddon', JSON.parse(this.dataset.weatheraddon));
				});
				
				timer = setInterval(function() {
					
					if(loading) return;
					loading = true;

					urls = [];
					queue = [];
					loaded = 0;
					
					layerz.each(updateLayer);
					loadWeather();
					
				}, refresh);
			
			}).one('rsWeatherDestroyed', function() {
				
				abort = true;
				clearInterval(timer);
				
			});
			
		}
		
	};
	
	function updateText(el, itm) {
			
		var clas = el.className;
		if(!clas) return;
		
		clas = clas.replace(/revslider-weather-data|revslider_data_weather_/g, '');
		clas = clas.trim();
		
		var st,
			unit = itm.unit,
			data = itm.data.data,
			day = el.dataset.day;
			
		switch(clas) {
			
			case 'title':
				st = data[day].weather.description;
			break;
			case 'temp':
				st = Math.round(data[day].temp);
			break;
			case 'code':
			case 'todayCode':
				st = data[day].weather.code;
			break;
			case 'currently':
				st = data[day].weather.description;
			break;
			case 'high':
				st = Math.round(data[day].max_temp);
			break;
			case 'low':
				st = Math.round(data[day].min_temp);
			break;
			case 'text':
				st = data[day].weather.description;
			break;
			case 'humidity':
				st = data[day].rh + '%';
			break;
			case 'pressure':
				st = data[day].pres + 'hPa';
			break;
			case 'rising':
				st = data[day].sunrise_ts;
			break;
			case 'visbility':
				st = data[0].vis;
			break;
			case 'sunrise':
				st = data[day].sunrise_ts;
			break;
			case 'sunset':
				st = data[day].sunset_ts;
			break;
			case 'thumbnail':
				st = 'https://www.weatherbit.io/static/img/icons/' + data[day].weather.icon + '.png';
			break;
			case 'image':
				st = 'https://www.weatherbit.io/static/img/icons/' + data[day].weather.icon + '.png';
			break;
			case 'wind_direction':
				st = data[day].wind_cdir;
			break;
			case 'wind_speed':
				st = data[day].wind_spd;
			break;
			case 'alt_temp':
				st = get_alt_temp(unit, Math.round(data[day].temp));
			break;
			case 'alt_high':
				st = get_alt_temp(unit, Math.round(data[day].max_temp));
			break;
			case 'alt_low':
				st = get_alt_temp(unit, Math.round(data[day].min_temp));
			break;
			case 'description':
				st = data[day].weather.description;
			break;
			case 'icon':
				st = '<i class="wi wi-owm-' + data[day].weather.code + '"></i>';
			break;
			
		}
		
		if(st !== el.innerHTML) el.innerHTML = st;
	
	}
	
	function updateItem(itm) {
		
		var items = itm.el.find('.revslider-weather-data').not('.revslider-weather-static').toArray(),
			len = items.length;
			
		for(var i = 0; i < len; i++) {
			updateText(items[i], itm);
		}
		
	}
	
	function updateWeather() {
		
		var len = queue.length;
		for(var i = 0; i < len; i++) updateItem(queue[i]);
	
	}
	
	function checkLoaded() {
		
		if(abort) return;
		if(loaded < queue.length - 1) {	
			loaded++;
			loadWeather();
		}
		else {
			updateWeather();
			loading = false;
		}
	
	}
	
	function onLoad(obj) {
		
		if(abort) return;
		if(obj && obj.data[0].weather) {
			
			queue[loaded].data = obj;
			checkLoaded();
			
		}
		
	}
	
	function loadWeather() {
		
		if(abort) return;
		var url = queue[loaded].url,
			index = urls.indexOf(url);
		
		urls[loaded] = url;
		if(index !== -1) {
			
			queue[loaded].data = queue[index].data;
			checkLoaded();
			return;
		
		}
		
		$.getJSON(url, onLoad);
	}
	
	function updateLayer(i) {
	
		var $this = $(this),
			data = $this.data('weatheraddon'),
			city = isNaN(data.location) ? 'city=' : 'city_id=';
			
		queue[i] = {
			
			el: $this, 
			unit: data.unit, 
			url: apiBaseUrl + city + data.location + '&key=' + rev_slider_weather_addon.api_key + '&units=' + data.unit
			
		};
	
	}
	
	/**
	 * Get alternative temp unit data
	 * @since    1.0.0
	 */
	function get_alt_temp(unit, temp) {
	    if(unit === 'F') {
	      return fahrenheit_to_celsius(temp);
	    } 
	    else {
	      return celsius_to_fahrenheit(temp);
	    }
	}

	/**
	 * Convert Temp Fahrenheit to Celsius
	 * @since    1.0.0
	 */
	function fahrenheit_to_celsius(given_value)
    {
        var celsius=5/9*(given_value-32);
        return (celsius) ;
    }

    /**
	 * Convert Temp Celsius to Fahrenheit
	 * @since    1.0.0
	 */
    function celsius_to_fahrenheit(given_value)
    {
        var fahrenheit= given_value*9/5+32;
        return (fahrenheit);
    }
	
	//Support Defer and Async and Footer Loads
	window.RS_MODULES = window.RS_MODULES || {};
	window.RS_MODULES.weather = {loaded:true, version:'3.0.0'};
	if (window.RS_MODULES.checkMinimal) window.RS_MODULES.checkMinimal();
})();
