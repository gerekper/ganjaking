
  jQuery(document).ready(function() {
	 jQuery("#ali-draft-publish-btn").click(function () {

					  var data = {
						  'action': 'pub_ali_draft_prod'
					  };

					  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					  jQuery.post(ajaxurl, data, function(response) {
						  alert('Products Published Successfuly');
						  window.location.reload();
					  });
			  });
  });



function monthToName( monthInNumber ) {
	monthInNumber = monthInNumber - 1;
	var month = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]
	  return month[ monthInNumber ];
  }

  function last7Days() {
	  var result = [];
	  var last_day_orders= [];
	  var currentDate = '';
	  var currentMonth = '';

	  alert(last_day_orders);
	  for( var i = 0; i < 7; i++ ) {
		  var d = new Date();
		  if(last_day_orders.length	> 0){
				  currentMonth = last_day_orders['active_months'][i];
				  currentDate = last_day_orders['last7days'][i];
				  result.push( currentDate + ' ' + monthToName(currentMonth) );
		  }
	  }
		  return result.reverse();
  }

  function last7DaysOrders( last_orders ) {
	var result = [];
	var today = new Date();
	  currentDate = today.getDate();
	var yesterday = new Date( today );
	yesterday.setDate ( yesterday.getDate() - 1 );
	yesterday = yesterday.getDate();
	for( var i = 7; i > 0; i-- ) {
			  var totalOrders = 0;
			  for ( var key in last_orders ) {
				  if ( key == currentDate && last_orders[ key ] > 0 ){
					  totalOrders = last_orders[ key ];
				  }
			  }
			  result.push( totalOrders );
		if ( 1 !== currentDate ) {
				currentDate = currentDate - 1;
		} else {
		  var date = new Date();
		  date.setDate(0);
		  currentDate = date.getDate();
		}
	}
	  return result.reverse();
  }

  Chart.defaults.global.defaultFontColor = 'white';
  Chart.defaults.global.defaultFontSize = 16;
  var last_day_orders= [];
  if(last_day_orders.length	> 0){
  new Chart(document.getElementById("bar-chart-grouped"), {
	   type: 'bar',
	   data: {
		   labels: last7Days(),
		   datasets: [
			   {
						label: "Orders Total in " + last_day_orders['currency'],
						backgroundColor: "#ff7789",
						data: last7DaysOrders( last_day_orders_data )

				},
			   {
					   label: "Profit",
				   backgroundColor: "#21e2ae",
				   data: last7DaysOrders( profit )
			   }
		   ]
	   },
	   options: {
		   responsive: true,
		   maintainAspectRatio: false
	   }
  });
}

  var canvas = document.getElementById("barChart");

  // Global Options:
  Chart.defaults.global.defaultFontColor = 'white';
  Chart.defaults.global.defaultFontSize = 16;

  // Notice the rotation from the documentation.
  var options = {
	  responsive: true,
	  maintainAspectRatio: false,
	  title: {
		  display: true,
	  },
	  rotation: -0.7 * Math.PI
  };

