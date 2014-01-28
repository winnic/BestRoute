<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvYq4hsJltYXmv8HOHzjRysyAf6bD9vJU&sensor=false">
    </script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(22.28788, 114.141685),
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);
      }
	  /*function showBusStop(j) {
		var dataUrl = '/bus/xml/route/?NWFB|2|' + j;
		direction = j;
		downloadUrl(dataUrl, function(data) {
			clearOverlays();
			var xml = parseXml(data); 
			var markers = xml.documentElement.getElementsByTagName("marker");  
			var latlngbounds = new google.maps.LatLngBounds();
			for (var i = 0; i < markers.length; i++) { 
				var id = parseFloat(markers[i].getAttribute('id'));
				var name = markers[i].getAttribute('name');
				var address = markers[i].getAttribute('address');
				var distance = parseFloat(markers[i].getAttribute('distance'));
				var point = new google.maps.LatLng(
					parseFloat(markers[i].getAttribute("lat")), 
					parseFloat(markers[i].getAttribute("lng"))); 
				var html = '<b>' + id + ' ' + name + '</b> <br/>' + address + '<br><br>' + (id!=1 ? '<img src="http://cbk0.google.com/cbk?output=thumbnail&w=180&h=136&ll=' + markers[i].getAttribute("lat") +',' + markers[i].getAttribute("lng") + '&thumb=0"><br><a href="#" onclick="javascript:showMarker('+ (id-2) +',' + j +')">上一站</a>' : '') + ' ' + (id!=markers.length ? '<a href="#" onclick="javascript:showMarker('+ id + ',' + j +')">下一站</a>' : '');
				var shadow = new google.maps.MarkerImage('http://www.google.com/mapfiles/shadow50.png', 
					new google.maps.Size(37, 34), 
					new google.maps.Point(0,0), 
					new google.maps.Point(9, 34)); 
				var marker = new google.maps.Marker({ 
					title: name,
					map: map,
					position: point,
					icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + id + '|ff776b',
					shadow: shadow
				});sadas
				bindInfoWindow(marker, map, infoWindow, html); 
				latlngbounds.extend(point);
				markersArray.push(marker);
			}
			map.setCenter(latlngbounds.getCenter());
			map.fitBounds(latlngbounds);
		});
	}*/
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
  <p>Hello World!</p>
    <div id="map-canvas"/>
  </body>
</html>