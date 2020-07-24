/* globals VitalPrivacy */
(function() {

	// Default settings
	var settings = {
		palette: {
			popup: {
				background: '#000',
				text: '#fff',
			},
			button: {
				background: '#3fa2f7',
				text: '#fff'
			}
		},
		position: 'bottom',
		showLink: false,
		static: false,
		content: {
			'message': 'We use cookies to offer you a better browsing experience, personalize content, provide social media features, and to analyze our traffic. To learn more, <a href="#">click here</a>. By continuing to use our site, you accept our use of cookies, <a href="#">Privacy Policy</a>, and <a href="#">Terms of Use</a>.',
			'dismiss': 'Allow Cookies',
			'deny': ''
		},
		location: false
	};

	// Handle opt-outs.
	// Currently not implemented.
	var onStatusChange = function(status, chosenBefore) {
		if (status === 'deny') {
			// disable_cookies
		}
		if (status === 'dismiss') {
			// enable_cookies
		}
	}

	// Customize settings
	if (VitalPrivacy.cookie_notice_pos) {
		settings.position = VitalPrivacy.cookie_notice_pos;

		if (VitalPrivacy.cookie_notice_pos === 'top') {
			settings.static = true;
		}
	}

	if (VitalPrivacy.cookie_notice_msg) {
		settings.content.message = VitalPrivacy.cookie_notice_msg;
	}

	if (VitalPrivacy.cookie_notice_accept_text) {
		settings.content.dismiss = VitalPrivacy.cookie_notice_accept_text;
	}

	if (VitalPrivacy.cookie_notice_accept_text_color) {
		settings.palette.button.text = VitalPrivacy.cookie_notice_accept_text_color;
	}

	if (VitalPrivacy.cookie_notice_accept_bg_color) {
		settings.palette.button.background = VitalPrivacy.cookie_notice_accept_bg_color;
	}

	if (VitalPrivacy.cookie_notice_color) {
		settings.palette.popup.background = VitalPrivacy.cookie_notice_color;
	}

	if (VitalPrivacy.cookie_notice_text_color) {
		settings.palette.popup.text = VitalPrivacy.cookie_notice_text_color;
	}

    function onDocumentReady() {
		window.cookieconsent.initialise(settings);
    }

    window.addEventListener('load', function() {
        onDocumentReady();
    });

})();
