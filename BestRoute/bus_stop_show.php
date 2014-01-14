<?php
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php include "UI_lib.php"; ?>
<script src="script.js"></script>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Traffic Master</title>
<style type="text/css">
	  #search {height:30%}
      #map { width: 800px; height: 450px }
    </style>
<script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>

<script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvYq4hsJltYXmv8HOHzjRysyAf6bD9vJU&sensor=false">
    </script>
    <script type="text/javascript">
	var markersArray = [];
	function clearOverlays() {
  for (var i = 0; i < markersArray.length; i++ ) {
    markersArray[i].setMap(null);
  }
  markersArray = [];
}
	window.directionsService;
	window.directionsDisplay;
	var customIcons = {
      restaurant: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png',
        shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png',
        shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
      }
    };
	
      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(22.28788, 114.141685),
          zoom: 12,
          mapTypeId: 'roadmap'
        };
        window.map = new google.maps.Map(document.getElementById("map"),
            mapOptions);
      }
		function downloadUrl(url, callback) {
			var request = window.ActiveXObject ?
			new ActiveXObject('Microsoft.XMLHTTP') :
			new XMLHttpRequest;

			request.onreadystatechange = function() {
			if (request.readyState == 4) {
				request.onreadystatechange = doNothing;
				callback(request, request.status);
			}	
			};
			request.open('GET', url, true);
			request.send(null);
    }
		function doNothing() {}
		function bindInfoWindow(marker, map, infoWindow, html) {
			google.maps.event.addListener(marker, 'click', function() {
			infoWindow.setContent(html);
			infoWindow.open(map, marker);
			});
		}
		function showBusStop(bus_concerned){
			window.directionsService = new google.maps.DirectionsService();
			window.directionsDisplay;
			var rendererOptions = {
				map: map,
				suppressMarkers : true
			}
			directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
			directionsDisplay.setMap(map);
			var infoWindow = new google.maps.InfoWindow;
			var dataUrl = "UI_lib.php?action=GMap_pos&bus="+bus_concerned;
			downloadUrl(dataUrl, function(data) {
				    var waypoint =[];
			
			var xml = data.responseXML;
			var markers = xml.documentElement.getElementsByTagName("marker");
			for (var i = 0; i < markers.length; i++) {
				var address = markers[i].getAttribute("address");
				//var type = markers[i].getAttribute("type");
				var point = new google.maps.LatLng(
					parseFloat(markers[i].getAttribute("lat")),
					parseFloat(markers[i].getAttribute("lng")));
				var html = "<b>" +address +"</b>";
				var icon = (markers[i].getAttribute("type")=="input")?'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=O|FFFF00|000000':'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=D|FF0000|000000';//control the color
				var marker = new google.maps.Marker({
					map: map,
					position: point,
					icon: null,
					shadow: null
				});
				markersArray.push(marker);
				bindInfoWindow(marker, map, infoWindow, html);
	var numOfPoints=parseInt(markers.length/9+1);
	if(i%numOfPoints==0&i!=0)
		{
		  waypoint.push({
          location:point,
          stopover:true});
		}		
			}
		window.start=new google.maps.LatLng(
					parseFloat(markers[0].getAttribute("lat")),
					parseFloat(markers[0].getAttribute("lng")));
		window.end=new google.maps.LatLng(
					parseFloat(markers[markers.length-1].getAttribute("lat")),
					parseFloat(markers[markers.length-1].getAttribute("lng")));
					
			
			console.log(waypoint);
		calcRoute(waypoint);
			});
			

		}
      google.maps.event.addDomListener(window, 'load', initialize);
	  
	  
function calcRoute(waypoint) {
  var request = {
      origin:start,
      destination:end,
	  waypoints:waypoint,
      travelMode: google.maps.DirectionsTravelMode.DRIVING
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
		console.log(response);
      directionsDisplay.setDirections(response);
    }
  });
}
    </script>

</head>
<body >

<div id="templatemo_wrapper">
    

	
	
    <div id="templatemo_middle">
    
    	<div class="col_w540 float_l v_divider">
        	<h1>Welcome to Traffic Master</h1>
            <p>Hello! We are two students from The Chinese University of Hong Kong and we are trying to implement a traffic searching function in Hong Kong as our project. We hope this can help you to get the best route that suit you</p>
        </div>
        <div class="col_w340 float_r twitter_col">
        	<div class="twitter_box">
                <div class="tb_entry">
                    This product is not yet finished indeed. We are opening this with an aim to let the user test our features and give some feedback to us maybe. If you have any enquires, you can contact jackleung@gmail.com for Leung wai keung or winnic0@hotmail.com for Ng Wai Leung.
                </div>
                <div class="cleaner"></div>
			</div>
            <div class="cleaner"></div>
        </div>
		
		
		<div id="search"><form action="UI.php" method="get"> 
			<div><hr><h2 style="display:inline">Select a bus to show it's route:</h2>
				<select name="StartindDistrict">
					<?php 
						Select_Bus("input");
					?>
				</select>
			</div>
		</form></div>
		<div id="map"></div>
		<div id="bus_detail"></div>
    
    </div>
   
            
        <div class="cleaner"></div>    
    </div>
</div>
<div id="templatemo_footer_wrapper">
     <div id="templatemo_footer">
    
        Copyright © By Ng Wai Leung, Winnic AND Leung Wai Keung<br>  
    </div> <!-- end of templatemo_footer -->
</div>
<div align=center>The Chinese University of Hong Kong</a></div></body>

</body>
    <script type="text/javascript">
	$("select").change(function(e){
			busPin=$(this).val();
			if(typeof(directionsDisplay)!="undefined"){
				clearOverlays();
				directionsDisplay.setMap(null);
			}
			showBusStop(busPin);
			selectbus(busPin);	
			$('html, body').animate({
				scrollTop: $("hr").offset().top
			}, 2000);
		});
    </script>
</html>