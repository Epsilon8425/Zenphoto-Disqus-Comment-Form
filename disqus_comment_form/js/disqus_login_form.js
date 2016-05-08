/*
 *
 * This file contains the Javascript/JQuery for the disqus comment form login window.
 *
 */

// Toggles the visibility of the users password
function togglePassword(id) {
	if ($('#pass' + id).attr('type') == 'password') {
		var oldp = $('#pass' + id);
		var newp = oldp.clone();
		newp.attr('type', 'text');
		newp.insertAfter(oldp);
		oldp.remove();
		$('.password_field_' + id).hide();
	} else {
		var oldp = $('#pass' + id);
		var newp = oldp.clone();
		newp.attr('type', 'password');
		newp.insertAfter(oldp);
		oldp.remove();
		$('.password_field_' + id).show();
	}
}

//Closes the popup window if the user logs in successfully
function closeWindow(){
	close(); 
}