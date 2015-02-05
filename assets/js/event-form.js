jQuery(document).ready(function($) {

  $('div.quantity').hide();

  /**
   * Called on the intiial page load.
   */
  var map;
  var marker;
  var infoWindow;	

  function init() {
    var mapCenter = new google.maps.LatLng(event_form_params.map_marker.latitude, event_form_params.map_marker.longitude);
    map = new google.maps.Map(document.getElementById('single_map_canvas'), {
      zoom: 16,
      center: mapCenter,
      mapTypeControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    marker = new RichMarker({
      position: mapCenter,
      map: map,
      draggable: false,
      content: '<div class="pin"></div><div class="pulse"></div>',
    });

    var contentString = event_form_params.map_marker.html;

    google.maps.event.addListener(marker, 'click', function() {
    	infoWindow.open(map, marker);
    });

    // Define an info window on the map.
		infoWindow = new google.maps.InfoWindow({
			content: contentString,
    		// maxWidth: 100
		});
  }
  
  google.maps.event.addDomListener(window, 'load', init);


	$('#ticket_type').change(function() {
		var ticket_id = $(this).val();

    // Show AJAX loader
    $('.rq_events_tickets_table').block({ message: null, overlayCSS: { background: '#fff url(' + event_form_params.ajax_loader_url + ') no-repeat center', opacity: 0.6 } });

		$.ajax({
			type: "POST",				
			url: event_form_params.ajax_url,
			dataType: "JSON",
			data: {
				'action' : 'calculate_cost', 
				'ticket_id': ticket_id
			},
			success: function(result){
        // Remove AJAX loader
        $('.rq_events_tickets_table').unblock();
				
				$('#results').html(result[0]['input']);
				$('#total_cost').html(result[0]['cost']).show();

        $('div.quantity').show();

				$("input[name='quantity']").bind('click change keyup mousewheel', function() {
          
				  var currency_symbol = $("input[name='currency_symbol']").val();
				  var ticket_count = $(this).val();
				  var base_cost = $("input[name='base_cost']").val();

				  var total_cost = base_cost * ticket_count;

				  $('#total_cost').html(currency_symbol + total_cost).show();
          
				  $("input[name='ticket_total_cost']").val(total_cost);

				});
			}
		});
		
	});
});