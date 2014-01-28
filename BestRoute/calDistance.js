	var start = new google.maps.LatLng(
              parseFloat(O.lat),
              parseFloat(O.longitube));
	var end = new google.maps.LatLng(
              parseFloat(D.lat),
              parseFloat(D.longitube));
	

	function calcDistance(){
	alert("hello");
		var service = new google.maps.DistanceMatrixService();
		service.getDistanceMatrix(
	{
      origins: [start],
      destinations: [end],
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.METRIC,
      avoidHighways: false,
      avoidTolls: false
    }, function (response, status) 
	{
		if (status != google.maps.DistanceMatrixStatus.OK) {
		alert('Error was: ' + status);
		} else {
			var origins = response.originAddresses;
			var destinations = response.destinationAddresses;
			var outputDiv = document.getElementById('outputDiv');
			outputDiv.innerHTML = '';
			//deleteOverlays();

		for (var i = 0; i < origins.length; i++) {
			var results = response.rows[i].elements;
			for (var j = 0; j < results.length; j++) {
				outputDiv.innerHTML += origins[i] + ' to ' + destinations[j]
					+ ': ' + results[j].distance.value + 'meter in '
					+ results[j].duration.value + 'second<br>';
			}
		}
		}
	});
};