jQuery(document).ready(function($) {

    // PRODUCT TYPE SPECIFIC OPTIONS
    $( 'select#product-type' ).change( function () {

        // Get value
        var select_val = $( this ).val();

        if ( 'event' === select_val ) {
            
            $( 'input#_downloadable' ).prop( 'checked', false );
        }

        show_and_hide_panels();

        $( 'body' ).trigger( 'woocommerce-product-type-change', select_val, $( this ) );

    }).change();

    function show_and_hide_panels() {
        var product_type    = $('select#product-type').val();

        if ( 'event' === product_type ) {
            $('.options_group ._manage_stock_field').show();
        }

    }

    $( '#_rq_event_start_date' ).datepicker({
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        showButtonPanel: true,
        showOn: 'button',
        buttonImage: rq_events_writepanel_js_params.calendar_image,
        buttonImageOnly: true
    });
    
    // Add a ticket type
    jQuery('#events_tickets').on('click', 'button.add_ticket', function(){
        jQuery('.rq_events_tickets').block({ message: null, overlayCSS: { background: '#fff url(' + rq_events_writepanel_js_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

        var loop = jQuery('.rq_event_ticket').size();

        var data = {
            action:   'rq_event_add_ticket',
            post_id:  rq_events_writepanel_js_params.post,
            loop:     loop,
            security: rq_events_writepanel_js_params.nonce_add_ticket
        };

        jQuery.post( rq_events_writepanel_js_params.ajax_url, data, function( response ) {
            jQuery('.rq_events_tickets').append( response ).unblock();
            jQuery('.rq_events_tickets #message').hide();
        });

        return false;
    });

    // Remove a ticket type
    jQuery('#events_tickets').on('click', 'button.remove_event_ticket', function(e){
        e.preventDefault();
        var answer = confirm( rq_events_writepanel_js_params.i18n_remove_ticket );
        if ( answer ) {

            var el = jQuery(this).parent().parent();

            var ticket = jQuery(this).attr('rel');

            if ( ticket > 0 ) {

                jQuery(el).block({ message: null, overlayCSS: { background: '#fff url(' + rq_events_writepanel_js_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

                var data = {
                    action:    'rq_event_remove_ticket',
                    ticket_id: ticket,
                    security:  rq_events_writepanel_js_params.nonce_delete_ticket
                };

                jQuery.post( rq_events_writepanel_js_params.ajax_url, data, function( response ) {
                    jQuery(el).fadeOut('300', function(){
                        jQuery(el).remove();
                    });
                });

            } else {
                jQuery(el).fadeOut('300', function(){
                    jQuery(el).remove();
                });
            }

        }
        return false;
    });
});



var $ = jQuery.noConflict();

jQuery(document).ready(function() { if (jQuery("#map_canvas").length) { loader(); } });

function loader() {

    var geocoder;
    var map;
    var marker;
    var zoom;
    var start_lat = jQuery("#_rq_event_lat_name").val();
    var start_lng = jQuery("#_rq_event_lon_name").val();

    if ((start_lat == 0 ) && (start_lng == 0)) {
        zoom = 5;
    } else {
        zoom = 15;
    }
    var latlng = new google.maps.LatLng(start_lat,start_lng);
    var options = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("map_canvas"), options);

    marker = new google.maps.Marker({
        map: map,
        draggable: true
    });
    var location = new google.maps.LatLng(start_lat, start_lng);
    marker.setPosition(location);
    map.setCenter(location);

    geocoder = new google.maps.Geocoder();
    google.maps.event.addListener(marker, 'drag', function() {
        $("#_rq_event_lat_name").val(marker.getPosition().lat());
        $("#_rq_event_lon_name").val(marker.getPosition().lng());
    });
    google.maps.event.addListener(marker, 'mouseup', function() {
        geocoder.geocode({latLng: marker.getPosition(), region: 'en' }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results) {
                    //console.log(results.length);
                    var marker_country = false, marker_region = false, marker_address = false, marker_zip = false;
                    $("#_rq_event_country_name").val(" ");
                    $("#_rq_event_region_name").val(" ");
                    $("#_rq_event_address_name").val(" ");
                    $("#_rq_event_zip_code").val(" ");
                    for (var index_res=0; index_res <= (results.length - 1); index_res++) {
                        //console.log(results[index_res].address_components.length);
                        for (var index_count=0; index_count <= (results[index_res].address_components.length - 1); index_count++) {
                            //console.log(results[index_res].address_components[index_count].types[0]);
                            //console.log(results[index_res].address_components[index_count].long_name);
                            if ((results[index_res].address_components[index_count].types[0] == "country") && (marker_country == false)) {
                                $("#_rq_event_country_name").val(results[index_res].address_components[index_count].long_name);
                                marker_country = true;
                            }
                            if ((results[index_res].address_components[index_count].types[0] == "administrative_area_level_1") && (marker_region == false)) {
                                $("#_rq_event_region_name").val(results[index_res].address_components[index_count].long_name);
                                marker_region = true;
                            }
                            if ((results[index_res].address_components[index_count].types[0] == "locality") && (marker_region == true)) {
                                $("#_rq_event_region_name").val(results[index_res].address_components[index_count].long_name);
                                marker_region = true;
                            }
                            if ((results[index_res].address_components[index_count].types[0] == "postal_code") && (marker_zip == false)) {
                                $("#_rq_event_zip_code").val(results[index_res].address_components[index_count].long_name);
                                marker_zip = true;
                            }
                        }
                    }
                    for (index_count=0; index_count <= (results[0].address_components.length - 1); index_count++) {
                        if ((results[0].address_components[index_count].types[0] != "country") &&
                            (results[0].address_components[index_count].types[0] != "administrative_area_level_1") &&
                            (results[0].address_components[index_count].types[0] != "administrative_area_level_2") &&
                            (results[0].address_components[index_count].types[0] != "administrative_area_level_3") &&
                            (results[0].address_components[index_count].types[0] != "locality")) {
                            if ($("#_rq_event_address_name").val() == " ") { $("#_rq_event_address_name").val(results[0].address_components[index_count].long_name); }
                            else { $("#_rq_event_address_name").val($("#_rq_event_address_name").val() + ", " + results[0].address_components[index_count].long_name); }
                        }
                    }
                    if ((marker_country == true) || (marker_region == true) || (marker_address == true)) {
                        $("#convert_gps_log").html('<span style="color: green;">Geocode was successful. Status: ' + status + '</span>');
                    }
                }
            } else {
                $("#convert_gps_log").html('<span style="color: red;">Error! Geocode was not successful for the following reason: ' + status + '</span>');
            }
        });
    });
}

$('#_redq_event_convert_zip').click(function(){

    var geocoder, geocoder_drag;
    var map;
    var start_lat = jQuery("#_rq_event_lat_name").val();
    var start_lng = jQuery("#_rq_event_lon_name").val();
    var country = $("#_rq_event_country_name").val();
    var region = $("#_rq_event_region_name").val();
    var address = $("#_rq_event_address_name").val();
    var zip = $("#_rq_event_zip_code").val();
    var full_address = zip + ' ' + country + ' ' + region + ' ' + address;

    geocoder = new google.maps.Geocoder();
    geocoder.geocode( { address: full_address, region: 'en'}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {

            var latlng = new google.maps.LatLng(start_lat,start_lng);
            var options = {
                zoom: 15,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map_canvas"), options);
            map.setCenter(results[0].geometry.location);

            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                draggable: true
            });
            $('#_rq_event_lat_name').val(results[0].geometry.location.lat());
            $('#_rq_event_lon_name').val(results[0].geometry.location.lng());
            $("#convert_gps_log").html('<span style="color: green;">Geocode was successful. Status: ' + status + '</span>');

            geocoder_drag = new google.maps.Geocoder();
            google.maps.event.addListener(marker, 'drag', function() {
                $('#_rq_event_lat_name').val(marker.getPosition().lat());
                $('#_rq_event_lon_name').val(marker.getPosition().lng());
            });
            google.maps.event.addListener(marker, 'mouseup', function() {
                geocoder_drag.geocode({latLng: marker.getPosition(), region: 'en'}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results) {
                            //console.log(results.length);
                            var marker_country = false, marker_region = false, marker_address = false, marker_zip = false;
                            $("#_rq_event_country_name").val(" ");
                            $("#_rq_event_region_name").val(" ");
                            $("#_rq_event_address_name").val(" ");
                            $("#_rq_event_zip_code").val(" ");
                            for (var index_res=0; index_res <= (results.length - 1); index_res++) {
                                //console.log(results[index_res].address_components.length);
                                for (var index_count=0; index_count <= (results[index_res].address_components.length - 1); index_count++) {
                                    //console.log(results[index_res].address_components[index_count].types[0]);
                                    //console.log(results[index_res].address_components[index_count].long_name);
                                    if ((results[index_res].address_components[index_count].types[0] == "country") && (marker_country == false)) {
                                        $("#_rq_event_country_name").val(results[index_res].address_components[index_count].long_name);
                                        marker_country = true;
                                    }
                                    if ((results[index_res].address_components[index_count].types[0] == "administrative_area_level_1") && (marker_region == false)) {
                                        $("#_rq_event_region_name").val(results[index_res].address_components[index_count].long_name);
                                        marker_region = true;
                                    }
                                    if ((results[index_res].address_components[index_count].types[0] == "locality") && (marker_region == true)) {
                                        $("#_rq_event_region_name").val(results[index_res].address_components[index_count].long_name);
                                        marker_region = true;
                                    }
                                    if ((results[index_res].address_components[index_count].types[0] == "postal_code") && (marker_zip == false)) {
                                        $("#_rq_event_zip_code").val(results[index_res].address_components[index_count].long_name);
                                        marker_zip = true;
                                    }
                                }
                            }
                            for (index_count=0; index_count <= (results[0].address_components.length - 1); index_count++) {
                                if ((results[0].address_components[index_count].types[0] != "country") &&
                                    (results[0].address_components[index_count].types[0] != "administrative_area_level_1") &&
                                    (results[0].address_components[index_count].types[0] != "administrative_area_level_2") &&
                                    (results[0].address_components[index_count].types[0] != "administrative_area_level_3") &&
                                    (results[0].address_components[index_count].types[0] != "locality")) {
                                    if ($("#_rq_event_address_name").val() == " ") { $("#_rq_event_address_name").val(results[0].address_components[index_count].long_name); }
                                    else { $("#_rq_event_address_name").val($("#_rq_event_address_name").val() + ", " + results[0].address_components[index_count].long_name); }
                                }
                            }
                            if ((marker_country == true) || (marker_region == true) || (marker_address == true)) {
                                $("#convert_gps_log").html('<span style="color: green;">Geocode was successful. Status: ' + status + '</span>');
                            }
                        }
                    } else {
                        $("#convert_gps_log").html('<span style="color: red;">Error! Geocode was not successful for the following reason: ' + status + '</span>');
                    }
                });
            });
        } else {
            $("#convert_gps_log").html('<span style="color: red;">Error! Geocode was not successful for the following reason: ' + status + '</span>');
        }
    });


});