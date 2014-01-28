<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
	<script src="js/jquery-1.10.2.min.js"></script>
    <title>Login-- Traffic Master</title>
	  <style type="text/css">
		  body {
		  font-family: "Lato", Helvetica, Arial, sans-serif;
		  font-size: 18px;
		  line-height: 1.72222;
		  color: #34495e;
		  background-color: #1abc9c;
		  min-height: 473px;
		  padding: 10px 10px 10px 10px;
		  color : white;
		  }
		  .login-form {
		  background-color: #edeff1;
		  padding: 24px 23px 20px;
		  position: relative;
		  border-radius: 6px;
		  }
		  .login-form:before {
		  content: '';
		  border-style: solid;
		  border-width: 12px 12px 12px 0;
		  border-color: transparent #edeff1 transparent transparent;
		  height: 0;
		  position: absolute;
		  left: -12px;
		  top: 35px;
		  width: 0;
		  -webkit-transform: rotate(360deg);
		  }
		  .form-group {
		  position: relative;
		  }
		  .form-control {
		  border: 2px solid #bdc3c7;
		  color: #34495e;
		  font-family: "Lato", Helvetica, Arial, sans-serif;
		  font-size: 15px;
		  padding: 8px 12px;
		  height: 42px;
		  -webkit-appearance: none;
		  border-radius: 6px;
		  -webkit-box-shadow: none;
		  box-shadow: none;
		  -webkit-transition: border .25s linear, color .25s linear, background-color .25s linear;
		  transition: border .25s linear, color .25s linear, background-color .25s linear;
		  }
		  .login-form .login-field {
		  border-color: transparent;
		  font-size: 17px;
		  text-indent: 3px;
		  }
		  .login-form .login-field-icon {
		  color: #bfc9ca;
		  font-size: 16px;
		  position: absolute;
		  right: 13px;
		  top: 9px;
		  -webkit-transition: 0.25s;
		  transition: 0.25s;
		  }
		  .login-link {
		  color: #bfc9ca;
		  display: block;
		  font-size: 13px;
		  margin-top: 15px;
		  text-align: center;
		  }
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
		  div>img{
			display: block;
			margin-left: auto;
			margin-right: auto;
		  }
	  </style>
</head>
  	<body> 
		<div class="login">
			<div class="login-screen">
				<div class="login-icon">
					<img src="img/TMlogo.PNG" width="300px">
					<h4>Welcome to <small>Traffic Master</small></h4>
				</div>
				
				<div class="login-form">
					<div class="form-group">
						<input type="text" class="form-control login-field" value="" placeholder="Enter your name" id="login-name">
						<label class="login-field-icon fui-user" for="login-name"></label>
					</div>
					
					<div class="form-group">
						<input type="password" class="form-control login-field" value="" placeholder="Password" id="login-pass">
						<label class="login-field-icon fui-lock" for="login-pass"></label>
					</div>
					
					<button class="btn" id="login">Login</button>
					<a class="login-link" href="#">Lost your password?</a>
				</div>
			</div>
		</div>
  	</body>
	<script type="text/javascript">
		$("#login").click(function(){
			authInfo="../interchange.php?user="+$("#login-name").val()+"&pw="+$("#login-pass").val();
			if($("#login-name").val()=="engg"&&$("#login-pass").val()=="123456"){
				window.location.assign("function.php");
			}else{
				alert("Sorry! Wrong user name or password.");
			}
		});
	</script>
</html>

