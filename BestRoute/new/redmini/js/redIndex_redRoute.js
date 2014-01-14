// wont run if there is a bug after pre-run the script
var sp = window.sp = (window.sp || {});
var dp = window.dp = (window.dp || {});
        function new_request()
        {
            var _factories = [
                function () { return new XMLHttpRequest(); },
                function () { return new ActiveXObject("Msxml2.XMLHTTP"); },
                function () { return new ActiveXObject("Microsoft.XMLHTTP"); }
            ];
            
            for(var i = 0; i < _factories.length; i++)
            {
                try {
                    var factory = _factories[i];
                    var r = factory();
                    if(r != null)
                    {
                        return r;
                    }
                }
                catch(e) {
                    continue;
                }
            }
            
            return null;
        }
        
        function SelectBus_station(district,StartOrDestination){
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'red_dataLib_redRoute.php?action=SelectBusStation&district='+district+'&StartOrDestination='+StartOrDestination);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		
            xhr.onreadystatechange = function () {
                if(xhr.readyState == 4)
                {
                    if(xhr.status != 200)
                        alert("Error code = " + new String(xhr.status));
                     else
                            {
                                document.getElementById(StartOrDestination).innerHTML="Please select a bus station of "+StartOrDestination+" in "+district+": <select id='"+StartOrDestination+"' name='"+StartOrDestination+"' class='"+StartOrDestination+"'>"+xhr.responseText+"</select>";
                            }
                }
            };
            xhr.send();
        }
        

        function init()
        {     
            document.body.addEventListener('click', function(e){
                var t = e.target;
                if(t.className == "SP"){
                    SelectBus_station(t.value,"StartingPoint");
                }
                if(t.className == "DP"){    
                    SelectBus_station(t.value,"DestinationPoint");
                }
                if(t.className == "StartingPoint"||t.className == "DestinationPoint"){
					if(t.className == "StartingPoint"){
						sp=t.value;
					}
					if(t.className == "DestinationPoint"){
						dp=t.value;
					}
					showBusStop(t.value,dp,sp);
                    if(t.className == "DestinationPoint"){
                         document.getElementById("submittion").style.display="block";
                    }
                }
            },false);
        }     
		function init2(){
			document.body.addEventListener('click', function(e){
					var t = e.target;
					if(t.className == "input")
						clearOverlays();
						showBusStop(t.value);
						selectbus(t.value);	
				},false);
		}
		 function selectbus(bus_num){
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'UI_lib.php?action=fetch_bus&bus='+bus_num);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if(xhr.readyState == 4)
                {
                    if(xhr.status != 200)
                        alert("Error code = " + new String(xhr.status));
                     else
                            {
                                //document.getElementById(StartOrDestination).innerHTML="Please select a bus station of "+StartOrDestination+" in "+district+": <select id='"+StartOrDestination+"' name='"+StartOrDestination+"'>"+xhr.responseText+"</select>";
								document.getElementById("bus_detail").innerHTML=xhr.responseText;
							}
                }
            };
            xhr.send();
        }
    