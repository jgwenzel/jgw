function updateClock ( ) {
  var currentTime = new Date ( );

  var currentHours = currentTime.getHours ( );
  var currentMinutes = currentTime.getMinutes ( );
  var currentSeconds = currentTime.getSeconds ( );

  // Pad the minutes and seconds with leading zeros, if required
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

  // Choose either "AM" or "PM" as appropriate
  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";

  // Convert the hours component to 12-hour format if needed
  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;

  // Convert an hours component of "0" to "12"
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;

  // Compose the string for display
  var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

  // Update the time display
  document.getElementById("clock").firstChild.nodeValue = currentTimeString;
}

function updateTicker(aDataInit,bDataInit,aDataInc,bDataInc,tsInit) {
	var tsMilliseconds = (new Date()).getTime();//milliseconds
	var tsInitMilliseconds = tsInit * 1000;//put into milliseconds
	var secondsElapsed = (tsMilliseconds - tsInitMilliseconds) / 1000;//put back into seconds
	var aBytes = Math.round(aDataInit + (aDataInc * secondsElapsed));
	var bBytes = Math.round(bDataInit + (bDataInc * secondsElapsed));
	var quotient = Math.round(bBytes * 100000 / aBytes) / 1000;
	document.getElementById("a_bytes").value = addCommas(aBytes) + " bytes";
	document.getElementById("b_bytes").value = addCommas(bBytes) + " bytes"; 
	document.getElementById("quotient").value = addCommas(quotient) + "%";
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
