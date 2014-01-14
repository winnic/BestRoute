<html>
<script>

var alertButton = null;
var resetButton = null;
var phaseButton = null;
var propagateButton = null;

var alertValue = true;
var phaseValue = true;
var propagateValue = true;

var divArray = null;
var borderArray = [0,0,0,0];

function setBorder(index)
{
	if(borderArray[index] == 0)
		divArray[index].style.border = "3px solid white";
	else
		divArray[index].style.border = "3px solid black";
}

function setPropagateMessage()
{
	var string = "";
	if(phaseValue == false)
		string = "big/small-&gt;bigger-&gt;biggest";
	else
		string = "biggest-&gt;bigger-&gt;big/small";

	document.getElementById("message").innerHTML = string;
}

function phaseButton_handler(e)
{
	for(var i = 0; i < divArray.length; i++)
		divArray[i].removeEventListener("click", mouseclick_handler, phaseValue);

	phaseValue = (phaseValue ? false : true);

	if(phaseValue)
		e.currentTarget.value = "Phase: capture";
	else
		e.currentTarget.value = "Phase: bubble";

	for(var i = 0; i < divArray.length; i++)
		divArray[i].addEventListener("click", mouseclick_handler, phaseValue);

	setPropagateMessage();
	e.stopPropagation();
}

function propagateButton_handler(e)
{
	propagateValue = (propagateValue ? false : true);

	if(propagateValue)
		e.currentTarget.value = "Propagation: yes";
	else
		e.currentTarget.value = "Propagation: no";

	e.stopPropagation();
}

function alertButton_handler(e)
{
	alertValue = (alertValue ? false : true);

	if(alertValue)
		e.currentTarget.value = "Alert: on";
	else
		e.currentTarget.value = "Alert: off";

	e.stopPropagation();
}

function mouseclick_handler(e)
{
	for(var i = 0; i < divArray.length;i++)
		if(e.currentTarget == divArray[i])
		{
			borderArray[i] = ((borderArray[i] == 0) ? 1 : 0);
			setBorder(i);
			break;
		}

	if(!propagateValue)
		e.stopPropagation();

	if(alertValue)
		alert("currentTarget = " + e.currentTarget.id + "; " + "target = " + e.target.id + "; ");
}

function init()
{
	alertValue = true;
	phaseValue = true;
	propagateValue = true;
	borderArray = [0,0,0,0];

	if(phaseButton == null)
	{
		phaseButton = document.getElementById("phaseButton");
		phaseButton.addEventListener("click", phaseButton_handler, false);
	}

	if(propagateButton == null)
	{
		propagateButton = document.getElementById("propagateButton");
		propagateButton.addEventListener("click", propagateButton_handler, false);
	}

	if(alertButton == null)
	{
		alertButton = document.getElementById("alertButton");
		alertButton.addEventListener("click", alertButton_handler, false);
	}

	if(resetButton == null)
	{
		resetButton = document.getElementById("resetButton");
		resetButton.addEventListener("click", init, false);
	}

	if(divArray == null)
	{
		divArray = new Array();
		divArray.push(document.getElementById("biggest"));
		divArray.push(document.getElementById("bigger"));
		divArray.push(document.getElementById("big"));
		divArray.push(document.getElementById("small"));
	}

	for(var i = 0; i < divArray.length; i++)
	{
		setBorder(i);
		divArray[i].addEventListener("click", mouseclick_handler, phaseValue);
	}
	setPropagateMessage();
}


</script>

<body onload="init();">

<div id=biggest style="
	width: 200px;
	height: 200px;
	background: red;
	float:left;
">

	<div id=bigger style="
		width: 100px;
		height: 100px;
		background: green;
	">

		<div id=big style="
			width: 70px;
			height: 70px;
			background: blue;
			float:left;
		">
		</div>

		<div id=small style="
			width: 30px;
			height: 30px;
			background: gray;
			float:left;
		">
		</div>

	</div>


</div>

<div id=big style="
	width: 200px;
	height: 200px;
	padding: 5px;
	border: 1px solid gray;
	float:right;
">
<input type=button value="Reset all" id="resetButton">
<br>
<input type=button value="Alert: on" id="alertButton">
<br><br>
<input type=button value="Propagation: yes" id="propagateButton">
<br>
<input type=button value="Phase: capture" id="phaseButton">
<p id=message>
</p>
</div>
</body>

</html>
