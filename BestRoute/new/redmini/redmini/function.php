<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
	<script src="js/jquery-1.10.2.min.js"></script>
    <title>Login-- Traffic Master</title>
	  <style type="text/css">
		html{
			height:100%;
			width:100%;
			background-color:#16A085;
		}
		body {
			font-family: "Lato", Helvetica, Arial, sans-serif;
			height:100%;
			width:100%;
			margin: 0;
			color:white;
		}
		#to_search{
			width:100%;
			height:49.5%;
			background-color:#1ABC9C;
			text-align: center;
		}
		#to_routine{
			width:100%;
			height:49.5%;
			background-color:#16A085;
			text-align: center;
		}
		span{
			font-size: -webkit-xxx-large;
			position: relative;
			top: 100px;
		}
	  </style>
</head>
  	<body> 
		<div id="to_search">
			<span>NEVIGATION</span>
		</div>
		<div id="to_routine">
			<span>BUS ROUTE</span>
		</div>
  	</body>
	<script type="text/javascript">
		$("#to_search").click(function(){
			window.location.assign("redIndex.php");
		});
		$("#to_routine").click(function(){
			window.location.assign("index.php");
		});
	</script>
</html>

