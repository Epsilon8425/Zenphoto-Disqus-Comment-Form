<!doctype html>

<html lang="en">

    <head>
    
    <title>Login</title>
    
    <?php
	
	// Make zenphoto functions/variables avaliable to login form
	require_once("../../zp-core/template-functions.php");
	
	// Useful paths
	$zpPluginUrl =  FULLWEBPATH . '/' . USER_PLUGIN_FOLDER . '/disqus_comment_form/';
	$zpCoreUrl =  FULLWEBPATH . '/' . ZENFOLDER . '/';
	
	?>
    
    <script src="<?php echo $zpCoreUrl ?>js/jquery.js"></script>
    
    <script src="<?php echo $zpPluginUrl ?>js/disqus_login_form.js"></script>
    
    <link rel="stylesheet" href="<?php echo $zpPluginUrl ?>css/disqus_login_form.css">
    
    <link rel="icon" href="<?php echo $zpPluginUrl ?>img/favicon.ico" type="image/x-icon">
    
    </head>
    
	<body>

	<?php if(zp_loggedin()): ?>
    
    <script>closeWindow();</script>

	<?php else: ?>
        
        <?php if (isset($_POST['user']) && !zp_loggedin()): ?>
        
        <div class="errorbox">
        
            <p>Your email address and/or password was entered incorrectly. Please try again.</p>
            
        </div>
        
        <?php endif; ?>
        
        <form id="login-form" name="login" method="post">
            
            <input type="hidden" name="login" value="1">
            
            <input type="hidden" name="password" value="1">
            
            <fieldset class="form-input-container">
            
                <legend>Email</legend>
                
                <input class="textfield" name="user" id="user" type="text" size="35" value="" autocomplete="off">
                
            </fieldset>
            
            <fieldset class="form-input-container">
            
                <legend>Password</legend>
                
                <input class="textfield" name="pass" id="pass" type="password" size="35" autocomplete="off">
                
            </fieldset>
            
            <fieldset class="show-password-container"> 
                
                <input id="show-password" class="show-password-checkbox" type="checkbox" onclick="togglePassword('');">
                
                <label for="show-password" class="show-password-label">Show password</label>
                
            </fieldset>
            
            <div class="form-input-container"> 
            
                <button type="submit" value="Log in" class="button">Log in</button>
            
                <button type="reset" value="Reset" class="button">Reset</button>
                
            </div>
        
        </form>
            
	<?php endif; ?>
        
    </body>
    
</html>