<!DOCTYPE html>
<?php include "red_dataLib.php"; ?>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>HK public transport</title> 
<script src="js/jquery-1.10.2.min.js"></script>
<style type="text/css">
	body{font-family: "Lato", Helvetica, Arial, sans-serif;}
	#search {height:30%}
	#map { height: 450px }
	.btn{
		border: none;
		font-size: 13.5px;
		font-weight: normal;
		line-height: 1.4;
		border-radius: 4px;
		padding: 10px 15px;
		-webkit-font-smoothing: subpixel-antialiased;
		-webkit-transition: 0.25s linear;
		transition: 0.25s linear;
		color: #ffffff;
		background-color: #1abc9c;
		width:100%;
	}
	#div_searchImg{
		display:inline-block;
		border-radius: 4px;
		position : relative;
		background-color: #34495e;
		height:62px;
		width:18%;
		list-style-type: disc;
		-webkit-margin-before: 1em;
		-webkit-margin-after: 1em;
		-webkit-margin-start: 0px;
		-webkit-margin-end: 0px;
		right:0px;
	}
	#div_searchImg img{
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
	.dropdown-menu {
		border-radius: 4px;
		background-color: #f3f4f5;
		border: none;
		display: block;
		margin-top: 8px;
		padding: 0;
		visibility: visible;
		width: 100%;
		-webkit-box-shadow: none;
		box-shadow: none;
		-webkit-transition: 0.25s;
		transition: 0.25s;
	}
	.dropdown-inverse {
		background-color: #34495e;
		color: #cccccc;
		padding: 4px 0 6px;
	}
	.dropdown-inverse li {
		line-height: 1.72222em;
		margin: 0 4px -2px;
	}
	.dropdown-inverse li:first-child > a, .dropdown-inverse li:last-child > a {
		border-radius: 2px;
		padding-bottom: 7px;
		padding-top: 5px;
	}
	.dropdown-inverse li > span {
		color: #ffffff;
		font-size: 14px;
		list-style: none;
		left: 10px;
		position: relative;
		padding: 5px 11px 7px;
		text-decoration:none;
	}
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvYq4hsJltYXmv8HOHzjRysyAf6bD9vJU&sensor=false"> </script>
<script type="text/javascript">
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
            center: new google.maps.LatLng(22.279912912261856, 114.15781827026371),
            zoom: 13,
            mapTypeId: 'roadmap'
        };
        window.map = new google.maps.Map(document.getElementById("map"),
            mapOptions);
        codeAddress();
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
    function bindInfoWindow(marker, map, infoWindow, html) {
        google.maps.event.addListener(marker, 'click', function() {
            infoWindow.setContent(html);
            infoWindow.open(map, marker);
        });
    }
    function showBusStop(station,dp,sp){
        var infoWindow = new google.maps.InfoWindow;
        var dataUrl = "red_dataLib.php?action=Get_Latitiude_Longtitude&start="+sp+"&dest="+dp+"";
        downloadUrl(dataUrl, function(data,status) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        for (var i = 0; i < markers.length; i++) {
            var name = markers[i].getAttribute("name");
            var address = markers[i].getAttribute("address");
            //var type = markers[i].getAttribute("type");
            var point = new google.maps.LatLng(
                parseFloat(markers[i].getAttribute("lat")),
                parseFloat(markers[i].getAttribute("lng")));
            var html = "<b>" + name + "</b> <br/>" + address;
            var icon = (markers[i].getAttribute("type")=="SP")?'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=O|FFFF00|000000':'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=D|FF0000|000000';
            var marker = new google.maps.Marker({
                map: map,
                position: point,
                icon: icon,
                shadow: icon.shadow
            });
            //bindInfoWindow(marker, map, infoWindow, html);
        }
        });
    }    
    function codeAddress(){//getElementById("keyword").value
        var SPaddress ="&#20013&#29872&#65288&#20132&#26131&#24291&#22580&#65289&#32317&#31449";
        var DPaddress ="&#39321&#28207&#20013&#29872&#24178&#35582&#36947&#20013";
        window.geocoder = new google.maps.Geocoder();
        geocoder.geocode( {'address': SPaddress}, function(results, status) {
            window.globalSPmarker = new google.maps.Marker({
                    map: map,
                    icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=O|FFFF00|000000',
                                shadow: "",
                    draggable:true,
                });
            globalSPmarker.setPosition(results[0].geometry.location);
            console.log(results);
            url="red_dataLib.php?action=theNearest&lat="+results[0].geometry.location.pb+"&longitube="+results[0].geometry.location.qb;
			console.log(url);
            AjaxCall("",url,function (response){ 
				console.log(response);
                window.dynamicSP=new Object();
                dynamicSP=(JSON.parse(response));
                document.getElementById('chinese').innerHTML=dynamicSP["bus_stationCHI"];
				$("li span").first().text(document.getElementById('chinese').innerHTML);
            });
            
            google.maps.event.addListener(globalSPmarker, 'dragend', function (){
                console.log(globalSPmarker.getPosition());
                url="red_dataLib.php?action=theNearest&lat="+globalSPmarker.getPosition().pb+"&longitube="+globalSPmarker.getPosition().qb;
                            console.log("start");
            console.log(url);
            console.log("end");
                AjaxCall("",url,function (response){ 
                    window.dynamicSP=new Object();
                    dynamicSP=(JSON.parse(response));
                    document.getElementById('chinese').innerHTML=dynamicSP["bus_stationCHI"];
					$("li span").first().text(document.getElementById('chinese').innerHTML);
                });
            });
        });
        geocoder.geocode( {'address': DPaddress}, function(results, status) {
            window.globalDPmarker = new google.maps.Marker({
                    map: map,
                    icon:'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=D|FF0000|000000',
                    draggable:true,
                });
            globalDPmarker.setPosition(results[0].geometry.location);  
            url="red_dataLib.php?action=theNearest&lat="+results[0].geometry.location.pb+"&longitube="+results[0].geometry.location.qb;
            AjaxCall("",url,function (response){ 
                window.dynamicDP=new Object();
                dynamicDP=(JSON.parse(response));
                document.getElementById('chinese').innerHTML=dynamicDP["bus_stationCHI"];
				$("li span").last().text(document.getElementById('chinese').innerHTML);
            });
            
            google.maps.event.addListener(globalDPmarker, 'dragend', function (){
                console.log(globalDPmarker.getPosition());
                url="red_dataLib.php?action=theNearest&lat="+globalDPmarker.getPosition().pb+"&longitube="+globalDPmarker.getPosition().qb;
                AjaxCall("",url,function (response){ 
                window.dynamicDP=new Object();
                dynamicDP=(JSON.parse(response));
                document.getElementById('chinese').innerHTML=dynamicDP["bus_stationCHI"];
				$("li span").last().text(document.getElementById('chinese').innerHTML);
            });});
                
        });
    }

    function AjaxCall(data, url, callback) {
        var xhr =window.ActiveXObject ?
              new ActiveXObject('Microsoft.XMLHTTP') :
              new XMLHttpRequest;
        xhr.open('GET', url);
        //xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
                    if(xhr.readyState == 4)
                    {
                        if(xhr.status != 200)
                            alert("Error code = " + new String(xhr.status));
                         else
                                {
                                    callback(xhr.responseText);
                                }
                    }
                };
        xhr.send(data);
    }

    google.maps.event.addDomListener(window, 'load', initialize);
	

</script>
<script src="js/redIndex.js"></script>
  </head>
  
  <body >
	  <div class="selection">
		<button class="btn dropdown-toggle clearfix btn-primary" data-toggle="dropdown">Choose Starting Position&nbsp;&&nbsp;Ending Position &nbsp;</button>
		<div style="display:inline-block;width:80%">
		<ul class="dropdown-menu dropdown-inverse" style="overflow-y: auto; max-height: 58px;max-height:150px">
			<li rel="0">From : <span></span></li>
			<li rel="1">To : &nbsp;&nbsp;&nbsp;&nbsp;<span></span></li>
		</ul>
		</div>
		<div id="div_searchImg"><img src="img/search.png" width="65px" /></div>
	  </div  >

    <div id="map" style="width: 100%; height: 450px"></div>
			<p  id="chinese" style="display:none"></p>
  </body>
  
  <script type="text/javascript">
  	$("#div_searchImg").click(function(){
		searchUrl="../interchange.php?StartingPoint="+$("li span").first().text()+"&DestinationPoint="+$("li span").last().text();
		window.location.assign(searchUrl);
		});
  </script>
</html>