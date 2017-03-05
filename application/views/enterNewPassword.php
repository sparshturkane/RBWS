<!DOCTYPE html>
<html>
<title>faarbetter films</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<style type="text/css">
	@font-face {
		font-family:Gotham-Book;

		src: url("<?php echo base_url() ?>assets/font/Gotham-Book.otf")

	}
	@font-face {
		font-family:Gotham-Medium;

		src: url("<?php echo base_url() ?>assets/font/Gotham-Medium.otf")

	}

	body
	{
		background-image: url("<?php echo base_url() ?>assets/images/background.jpg");
		background-repeat: no-repeat;
    	background-size: cover;
    	font-family: Gotham-Book !important;
	}
	.holder
	{
		width: 300px;
		height: 350px;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		position: absolute;
		margin: auto;
		
	}
	.logoClass
	{
		width: 100px;
		position: relative;
		padding-bottom: 50px;
	}
	.inputClass
	{
		width: 100%;
		border-radius: 3px;
		border:1px solid #00645d;
		outline: none;
		padding: 5px;
		background: transparent;
    	color: white;
		font-family: Gotham-Book;
		font-size: 12px;
	}
		::placeholder
		{
		  font-family: Gotham-Book;
		}
	.buttonClass
	{
		width: 100%;
		background-color: #00645d !important;
		border-radius: 3px;
		border:none;
		font-size: 12px;
		font-family:Gotham-Medium;
	}
	.textOnImg
	{
		position: relative;
		color: white;
		top: 0px;
    	left: -3px;
    	font-size: 15px;
    	letter-spacing: 2px;
	}

	.js #loader { display: block; position: absolute; left: 100px; top: 0; }
	.se-pre-con {
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url("<?php echo base_url() ?>assets/images/loader.gif") center no-repeat rgba(0,0,0,0.9);
		opacity: 0.5;
	}
	
</style>
<body>

<div class="w3-container holder">
	<div class="se-pre-con" id="displayLoader" style="display:none;"></div>
	<div class="w3-row logoHolder w3-padding w3-center">
		<img src="<?php echo base_url() ?>assets/images/rockabyteLogo.png" class="logoClass">
		<!-- <div class="textOnImg">
			<span> <strong>ROCKA</strong><br> BYTE</span>
		</div> -->
		
	</div>
	
	<div class="w3-row w3-padding w3-text-white">
		
	</div>
	<form class="form-horizontal" id="enterNewPasswordForm" method="post" >
	  	<input type="hidden" name="userID" value="<?= $userData[0]['userID']; ?>">
	  	<div class="w3-row inputHolder w3-padding">
	  		<input type="password" placeholder="Password" name="password" id="password" class="inputClass" onkeypress="resetError()" required>
	  	</div>

	  	<div class="w3-row inputHolder w3-padding">
	  		<input type="password" placeholder="ConfirmPassword" name="confirmPassword" id="confirmPassword" class="inputClass" required>
	  		<span id="passwordErrorMsg" style="display:none;color:#dc1010;">* Passwords Don't Match</span>
	  	</div>

		<div class="w3-row buttonHolder w3-padding">
			 <button class="w3-btn-block buttonClass" type="button" onclick="enterPassword();">Submit</button>
		</div>
	</form>
</div>
	<script type="text/javascript">
		// function resetError(){

		// 	//console.log('hit');
		// 	var password = $('#password').val();
  //           var confirmPassword = $('#confirmPassword').val();

  //           if(confirmPassword==password){
  //           	//console.log('truehit');
		// 		$('#password').css('border','1px solid #00645d');
		// 		//$("#confirmPassword").val('');
		// 		$('#confirmPassword').css('border','1px solid #00645d');
		// 		$('#passwordErrorMsg').css('display','none');
		// 	}
		// }

		function resetError(){
            $('#password').css('border','1px solid #00645d');
            $('#confirmPassword').css('border','1px solid #00645d');
            $('#passwordErrorMsg').css('display','none');
        }
	</script>

<!-- 	<script type="text/javascript">
		function loader(){
			$("#displayLoader").css('display','block');
			// $('#confirmPassword').css('border','1px solid #00645d');
		}
	</script> -->

	<script type="text/javascript">
		function enterPassword(){

            var password = $('#password').val();
            var confirmPassword = $('#confirmPassword').val();
            //passwordDontMatchDiv

            if(confirmPassword!=password){
            	    //border: 1px solid #dc1010;
            	$('#password').css('border','1px solid #dc1010');
            	$('#confirmPassword').css('border','1px solid #dc1010');
            	$('#passwordErrorMsg').css('display','block');
            }else{
	            var form = $('#enterNewPasswordForm')[0]; 
	            var formData = new FormData(form);
	            // for (var [key, value] of formData.entries()) { 
	            //     console.log(key, value);
	            // }
		        $.ajax({
	                url:  "<?php echo site_url(''); ?>/api/doEnterNewPassword",
	                type: "POST",
	                data:  formData,
	                contentType: false,
	                cache: false,
	                processData:false,

	                beforeSend: function() {
	                	$("#displayLoader").css('display','block');
	                },

	                success: function(data){
	                    var objJson = JSON.parse(data);
	                    if (objJson.txtsuccess == false) {
	                        console.log(objJson.txtsuccess);
	                    } else if (objJson.txtsuccess == true) {
	                        console.log(objJson.txtsuccess);
	                        window.location = "<?php echo site_url(''); ?>/api/passwordUpdatedSuccessFully";
	                        // window.location="http://www.tutorialspoint.com";
	                    }
	                },

	                error: function(){
	                    alert('please try again');
	                }
	            });
	        }

        }
	</script>
</body>
</html>