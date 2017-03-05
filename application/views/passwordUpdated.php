<!DOCTYPE html>
<html>
<title>faarbetter films</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<style type="text/css">
	.holder
	{
		width: 300px;
		height: 250px;
		left: 0;
		right: 0;
		top: 0;
		bottom: 0;
		margin: auto;
		position: absolute;
	}

	.logoClass
	{
		width: 100px;
		position: relative;
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
	}
	.buttonClass
	{
		width: 100%;
		background-color: #00645d !important;
		border-radius: 3px;
		border:none;
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
	.inputHolder
	{
		color: #00645d;
		text-align: center;
	}
	
</style>
<body>

<div class="w3-container holder">
	<div class="w3-row logoHolder w3-padding w3-center">
		<img src="<?php echo base_url() ?>assets/images/green-tick.gif" class="logoClass">
			
	</div>
	
	<div class="w3-row w3-padding w3-text-white">
		
	</div>
  	
  	<div class="w3-row inputHolder w3-padding">
  		 <span>Password updated successfully</span>
  	</div>

  	

	
</div>

</body>
</html>