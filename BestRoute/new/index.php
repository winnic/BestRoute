<!DOCTYPE html>
<?php include "dataLib.php"; ?>
<html>
<script src="js/jquery-1.10.2.min.js"></script>
<style type="text/css">
      #search {height:30%}
      #map { width: 800px; height: 450px }
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
            center: new google.maps.LatLng(22.249912912261856, 114.19781827026371),
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
        var dataUrl = "dataLib.php?action=Get_Latitiude_Longtitude&start="+sp+"&dest="+dp+"";
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
            url="dataLib.php?action=theNearest&lat="+results[0].geometry.location.ob+"&longitube="+results[0].geometry.location.pb;
            AjaxCall("",url,function (response){ 
                window.dynamicSP=new Object();
                dynamicSP=(JSON.parse(response));
                document.getElementById('chinese').innerHTML=dynamicSP["bus_stationCHI"];
                document.getElementById('keyword1').value=document.getElementById('chinese').innerHTML;
            });
            
            google.maps.event.addListener(globalSPmarker, 'dragend', function (){
                console.log(globalSPmarker.getPosition());
                url="dataLib.php?action=theNearest&lat="+globalSPmarker.getPosition().ob+"&longitube="+globalSPmarker.getPosition().pb;
                            console.log("start");
            console.log(url);
            console.log("end");
                AjaxCall("",url,function (response){ 
                    window.dynamicSP=new Object();
                    dynamicSP=(JSON.parse(response));
                    document.getElementById('chinese').innerHTML=dynamicSP["bus_stationCHI"];
                    document.getElementById('keyword1').value=document.getElementById('chinese').innerHTML;
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
            url="dataLib.php?action=theNearest&lat="+results[0].geometry.location.ob+"&longitube="+results[0].geometry.location.pb;
            AjaxCall("",url,function (response){ 
                window.dynamicDP=new Object();
                dynamicDP=(JSON.parse(response));
                document.getElementById('chinese').innerHTML=dynamicDP["bus_stationCHI"];
                document.getElementById('keyword2').value=document.getElementById('chinese').innerHTML;
            });
            
            google.maps.event.addListener(globalDPmarker, 'dragend', function (){
                console.log(globalDPmarker.getPosition());
                url="dataLib.php?action=theNearest&lat="+globalDPmarker.getPosition().ob+"&longitube="+globalDPmarker.getPosition().pb;
                AjaxCall("",url,function (response){ 
                window.dynamicDP=new Object();
                dynamicDP=(JSON.parse(response));
                document.getElementById('chinese').innerHTML=dynamicDP["bus_stationCHI"];
                document.getElementById('keyword2').value=document.getElementById('chinese').innerHTML;
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

    //google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script src="js/index.js"></script>
  <head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>HK public transport</title> 
  </head>
  
  <body onload="init();">
		<div id="search"><form action="search.php" method="get"> 
            <div>Starting Point
                <select name="StartindDistrict" class="SP">
                    <?php 
                        SelectDistrict("SP");
                    ?>
                </select>
            </div>
            <div  id="stations">
                <div  id="StartingPoint">
                </div>
                <div  id="DestinationPoint">
                </div>
                <div>
                    <input id="submittion" type="submit" value="search" style="display:none">
                </div>    
                
            </div>         
            <p></p>
            <div style="top:10px;left:350px;position:absolute;">Destination Point
                <select name="DestinationDistrict" class="DP">            
                    <?php 
                        SelectDistrict("DP");
                    ?>
                </select></div> 
        </form></div>
		<div>
			<form action="interchange.php" method="get"> 
			<input id="keyword1" name="StartingPoint" type="text" >
			<input id="keyword2" name="DestinationPoint" type="text" >
			<button type="submit">search</button>
			</form></div>
		</div>
    <div id="map" style="width: 100%; height: 600px"></div>
			<p  id="chinese" style="display:none"></p>
  </body>
</html>