<?php
/*
 * ------------------------------------------------------------------------------------------------
 * Disqus Comment Form Integration
 * ------------------------------------------------------------------------------------------------
 *
 * Add Disqus Commenting to Zenphoto. Visit https://disqus.com/admin/signup/ to signup.
 *
 * @author Ben Feather (Epsilon)
 * @version 1.0.0
 * @package plugins
 * @subpackage misc
 *
 */
 
$plugin_is_filter = 5 | CLASS_PLUGIN;
$plugin_description = gettext('Adds Disqus to Zenphoto. Can be configured to use SSO (single sign on) and Disqus comment count. Visit https://disqus.com/admin/signup/ to signup.');
$plugin_author = gettext('Ben Feather (Epsilon)');
$plugin_version = '1.0.0';
$plugin_URL = 'https://github.com/Epsilon8425/Zenphoto-Disqus-Comment-Form';
$plugin_disable = (getOption('zp_plugin_comment_form')) ? gettext('Only one commenting plugin can be activated at a time, please disable the core commenting plugin to use the disqus plugin.') : false; 

if ($plugin_disable) 
	setOption('disqus_comment_form',0);
else 
	$option_interface = 'disqus_comment_form';


class disqus_comment_form {
	
	var $name = 'disqus_comment_form';
	
	// Sets default values
	function comment_form() {
		setOptionDefault('disqus_shortname', '');
		setOptionDefault('disqus_use_sso', 'false');
		setOptionDefault('disqus_count_comments', 'false');
		setOptionDefault('disqus_public_key', '');
		setOptionDefault('disqus_private_key', '');
		setOptionDefault('disqus_site_name', '');
		setOptionDefault('disqus_width', 800);
		setOptionDefault('disqus_height', 400);
	}
	
	// Options for the plugin
	function getOptionsSupported() {
		
		$checkboxes = array(gettext('Albums') => 'disqus_albums', gettext('Images') => 'disqus_images');
		if (extensionEnabled('zenpage')) {
			$checkboxes = array_merge($checkboxes, array(gettext('Pages') => 'disqus_pages', gettext('News') => 'disqus_articles'));
		}
		
		$options = array(
			// Input for Disqus Shortname
			gettext('Disqus Shortname:') => array(
				'key'			=> 'disqus_shortname',
				'type'			=> OPTION_TYPE_TEXTBOX,
				'order'			=> 1,
				'desc'			=> gettext('Enter your Disqus site shortname here (excluding \'.disqus.com\'). This is the name you entered during signup that ended in ".disqus.com". If you don\'t remeber your shortname go to the Disqus website, login, Click on "Settings" and then click "Admin". Your custom shortname will be in the url address bar, copy the text that appears AFTER http:// and BEFORE .disqus.com into the box on the left.')
			),
			gettext('Allow comments on') => array(
				'key'			=> 'comment_form_allowed', 
				'type'			=> OPTION_TYPE_CHECKBOX_ARRAY,
				'order'			=> 2,
				'checkboxes' 	=> $checkboxes,
				'desc'			=> gettext('Comment forms will be presented on the checked pages.')
			),
			// Input for Disqus Use SSO
			gettext('Include count comment functionality?:') => array(
				'key'			=> 'disqus_count_comments',
				'type'			=> OPTION_TYPE_CHECKBOX,
				'order'			=> 3,
				'desc'			=> gettext('If enabled you can retrieve the comment count for any page that has Disqus enabled and display the results. For more information on how to display the count see: https://help.disqus.com/customer/portal/articles/565624-adding-comment-count-links-to-your-home-page')
			),
			// Input for Disqus Use SSO
			gettext('Use SSO (Single Sign On)?:') => array(
				'key'			=> 'disqus_use_sso',
				'type'			=> OPTION_TYPE_CHECKBOX,
				'order'			=> 4,
				'desc'			=> gettext('This option will allow users to sign up for your site and automatically be able to use the Disqus commenting form without having to register for a separate Disqus account. For more information on SSO see: https://help.disqus.com/customer/portal/articles/684744-getting-started-with-single-sign-on')
			),
			// Input for Disqus SSO Public Key
			gettext('Disqus SSO Public Key:') => array(
				'key'			=> 'disqus_public_key',
				'type'			=> OPTION_TYPE_TEXTBOX,
				'order'			=> 5,
				'desc'			=> gettext('Your Disqus SSO Public key (Only used if SSO is enabled).')
			),
			// Input for Disqus SSO Private Key
			gettext('Disqus SSO Private Key:') => array(
				'key'			=> 'disqus_private_key',
				'type'			=> OPTION_TYPE_TEXTBOX,
				'order'			=> 6,
				'desc'			=> gettext('Your Disqus SSO Private key (Only used if SSO is enabled).')
			),
			// Input for Disqus SSO Site Name
			gettext('Disqus SSO Site Name:') => array(
				'key'			=> 'disqus_site_name',
				'type'			=> OPTION_TYPE_TEXTBOX,
				'order'			=> 7,
				'desc'			=> gettext('Your site\'s name. It will be displayed in the Post As window. (Only used if SSO is enabled).')
			),
			// Input for Disqus Width
			gettext('Disqus SSO Width:') => array(
				'key'			=> 'disqus_width',
				'type'			=> OPTION_TYPE_TEXTBOX,
				'order'			=> 8,
				'desc'			=> gettext('Width of the login popup window. Default is 800. (Only used if SSO is enabled).')
			),
			// Input for Disqus SSO Height
			gettext('Disqus SSO Height:') => array(
				'key'			=> 'disqus_height',
				'type'			=> OPTION_TYPE_TEXTBOX,
				'order'			=> 9,
				'desc'			=> gettext('Height of the login popup window. Default is 400. (Only used if SSO is enabled).')
			)
		);
		
		return $options;
		
	}
	
} // End Class

function disqusJS() {
	if(getOption('disqus_count_comments')):
	?>
    
	<script id="dsq-count-scr" src="//<?php echo getOption('disqus_shortname'); ?>.disqus.com/count.js" async="true"></script>
    
	<?php
	endif;
}

zp_register_filter('theme_body_close', 'disqusJS');

function getDisqusData($object = NULL){
	
	global $_zp_gallery_page, $_zp_current_image, $_zp_current_album, $_zp_current_zenpage_page, $_zp_current_zenpage_news;
	
	// If this function is called without passing in an object, it will get the current active object for the page
	if($object == NULL){
		
		switch ($_zp_gallery_page) {
			
			case 'image.php':
				$object = $_zp_current_image;
				break;
				
			case 'album.php':
				$object = $_zp_current_album;
				break;

			case 'pages.php':
				$object = $_zp_current_zenpage_page;
				break;
				
			case 'news.php':
				if(is_NewsArticle()){
					$object = $_zp_current_zenpage_news;
					break;
				}
				else {
					return;
					break;
				}
				
			default:
				return;
				break;
		}
		
	}
	
	// Get the type of the object
	$objectType = strtolower(get_class($object));
	
	// Generate the unique identifier
	$identifier =  $objectType . '_' . $object->getID();
	
	// Get the title of the current object
	// Note: preg() removes everything that is not an alphanumeric character or a space
	// Note: strtolower() converts string to lowercase (to use with ucwords)
	// Note: ucwords() converts string to titlecase
	$title = ucwords(strtolower(preg_replace('/[^ \w]+/', '',  $object->getTitle())));
	
	// Set comments to be disabled initially
	$commentsEnabled = 0;
	
	// Get request scheme (http/https)
	if(isset($_SERVER['HTTPS']) &&  $_SERVER['HTTPS'] === 'on' )
		$requestScheme = 'https://';
	else
		$requestScheme = 'http://';
	
	// Set url to requestScheme and domain name
	$url = $requestScheme .  $_SERVER['HTTP_HOST'];
	
	// Add the rest of the objects url to the variable
	// Check if comments are enabled for the object
	switch ($objectType) {
			
		case 'image':
			$url .= getImageURL($object);
			if ($object->getCommentsAllowed() && getOption('disqus_images')) $commentsEnabled = 1;
			break;
			
		case 'album': 
		case 'dynamicalbum':
			$url .= getAlbumURL($object);
			if ($object->getCommentsAllowed() && getOption('disqus_albums')) $commentsEnabled = 1;			
			break;
	
		case 'zenpagepage':
			$url .= getPageURL($object->getTitlelink());
			if ($object->getCommentsAllowed() && getOption('disqus_pages')) $commentsEnabled = 1;
			break;
			
		case 'zenpagenews':
			$url .= getNewsURL($object->getTitlelink());
			if ($object->getCommentsAllowed() && getOption('disqus_articles')) $commentsEnabled = 1;
			break;
		
	}
	
	// Return the array containing the objects data
	return array(
		'url' 				=> $url,
		'identifier' 		=> $identifier,
		'title' 			=> $title,
		'commentsEnabled'	=> $commentsEnabled
	);	
}

function printCommentCount($object = NULL){
	if(getOption('disqus_count_comments')){
		$disqusData = getDisqusData($object);
		if($disqusData['commentsEnabled'])
			echo '<span class="disqus-comment-count" data-disqus-identifier="'.$disqusData['identifier'].'"></span>';
		else
			echo '<span>Comments Disabled</span>';
	}
}

function getCommentCountAttributes($object = NULL){
	if(getOption('disqus_count_comments')){
		$disqusData = getDisqusData($object);
		return array(
			'class' => 'disqus-comment-count',
			'url' => $disqusData['url'],
			'dataIdentifier' => 'data-disqus-identifier="' . $disqusData['identifier'] . '"',
			'dataUrl' => 'data-disqus-url="' . $disqusData['url'] . '"',
			'commentsEnabled' => $disqusData['commentsEnabled']
		);
	}
}

function printCommentForm(){ 

	$disqusData = getDisqusData();
	
	if($disqusData['commentsEnabled']) {
		
		// Paths
		$zpPluginUrl =  FULLWEBPATH . '/' . USER_PLUGIN_FOLDER  . '/disqus_comment_form/';
		$url = $disqusData['url'];
		
		// Logout link will log user out and refresh the current page
		if (MOD_REWRITE) {
			 $logoutUrl = 'javascript:launchScript("' . $url . '",["userlog=0"]);';
		}
		else {
			if(strpos($url, '?'))
				$logoutUrl = $url . '&logout=0' ;
			else
				$logoutUrl = $url . '?logout=0' ;
		}
		
		// Variables
		$shortname = getOption('disqus_shortname');
		$privateKey = getOption('disqus_private_key');
		$publicKey = getOption('disqus_public_key');
		$useSso = getOption('disqus_use_sso');
		$ssoName = Addslashes(getOption('disqus_site_name'));
		$ssoWidth = getOption('disqus_width');
		$ssoHeight = getOption('disqus_height');
		$isLoggedIn =  zp_loggedin();
		
		// User details and disqus auth message generation
		if($isLoggedIn && $useSso){
			// Get user details from zenphoto
			$userId = $GLOBALS['_zp_current_admin_obj']->getID();
			$userName =  $GLOBALS['_zp_current_admin_obj']->getName();
			$userEmail = $GLOBALS['_zp_current_admin_obj']->getEmail();
			
			// Create array of details
			$data = array(
				'id' => $userId,
				'username' => $userName,
				'email' => $userEmail
			);
			// Get the message
			$message = base64_encode(json_encode($data));
			// Get the timestamp
			$timestamp = time();
			// Get the HMAC
			$hmac = dsq_hmacsha1($message . ' ' . $timestamp, $privateKey);
		}
		?>
	
		<div id="disqus_thread"></div>
		
		<script>
		
			var disqus_config = function () {
				
				<?php if($useSso && $isLoggedIn): ?>
				
				// The generated payload which authenticates users with Disqus (used if SSO is enabled and a user is logged into Zenphoto)
				this.page.remote_auth_s3 = "<?php echo $message . ' ' . $hmac . ' ' . $timestamp; ?>";
				this.page.api_key = "<?php echo $publicKey; ?>";
				
				<?php endif; ?>
				
				<?php if($useSso): ?>
						
				// The custom login/logout configuration (used if SSO is enabled)
				this.sso = {
					  name:		'<?php echo $ssoName; ?>',
					  button:	'<?php echo $zpPluginUrl . 'img/button.jpg'; ?>',
					  icon:		'<?php echo $zpPluginUrl . 'img/favicon.ico'; ?>',
					  url:		'<?php echo $zpPluginUrl . 'disqus_login_form.php'; ?>',
					  logout:	'<?php echo $logoutUrl; ?>',
					  width:	'<?php echo $ssoWidth; ?>',
					  height:	'<?php echo $ssoHeight; ?>'
				};
					
				<?php endif; ?>
				
				// identifies the page (always used)
				//this.page.url = '<?php echo $disqusData['url']; ?>';
				this.page.identifier = '<?php echo $disqusData['identifier']; ?>';
				this.page.title = '<?php echo $disqusData['title']; ?>';
	
			};
			
			<?php if($shortname != NULL): ?>
			
			// Initialises Disqus
			(function() {
			
				var d = document, s = d.createElement('script');
				
				s.src = '//<?php echo $shortname; ?>.disqus.com/embed.js';
				
				s.setAttribute('data-timestamp', +new Date());
				
				(d.head || d.body).appendChild(s);
				
			})(); 
			
			<?php endif; ?>
			
		</script>
		
		<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
	
<?php 
	}
	else {
		echo '<div class="Messagebox fade-message"><p>Comments have been disabled for this page.</p></div>';
	}

} // End printCommentForm 

//Disqus function DO NOT edit
function dsq_hmacsha1($data, $key) {
	$blocksize=64;
	$hashfunc='sha1';
	if (strlen($key)>$blocksize)
		$key=pack('H*', $hashfunc($key));
	$key=str_pad($key,$blocksize,chr(0x00));
	$ipad=str_repeat(chr(0x36),$blocksize);
	$opad=str_repeat(chr(0x5c),$blocksize);
	$hmac = pack(
				'H*',$hashfunc(
					($key^$opad).pack(
						'H*',$hashfunc(
							($key^$ipad).$data
						)
					)
				)
			);
	return bin2hex($hmac);
}
// End dsq_hmacsha1
?>