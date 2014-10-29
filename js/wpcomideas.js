/*
* WPCOMIdeas JavaScript functions and definitions
*
* Provides the theme with a functional layer to save page reloads, live search, and add general effects.
*/
var WPCOM_Ideas = {

	// Sets a timer so the live search JS works properly.
	livesearch_timer : '',

	init : function() {
		jQuery('.hide-if-no-js').removeClass('hide-if-no-js');

		jQuery('#livesearch').livesearch({
					searchCallback: this.live_search,
					innerText: "",
					queryDelay: 200,
					minimumSearchLength: 1
		});

		jQuery('#livesearch-results').hide();
	},

	/**
	* Pulls results for a set of terms and displays it udner the post tags box.
	*/
	live_search : function(term) {
		this.livesearch_timer = setTimeout(function () {
			jQuery.get('index.php?livesearch='+term+'&u='+encodeURIComponent( query_params()["u"] ) + '&s='+encodeURIComponent( query_params()['s'] ), function (data) {
				if( data.length > 0 ) {
					jQuery('#livesearch-results').show();
					jQuery('#livesearch-results').html(data);
				}
			});
		}, 200);
	},


};

function query_params() {
		var vars = [], hash;
		var hashes = window.location.href.slice( window.location.href.indexOf( '?' ) + 1 ).split( '&' );

		for( var i = 0; i < hashes.length; i++ ) {
			hash = hashes[i].split( '=' );
			vars.push( hash[0] );
			vars[ hash[0] ] = hash[1];
		}
		return vars;
}

/*
* Executes when the page has finished loading. Calls the init function and attaches a few events.
*/
jQuery(document).ready(function() {
	WPCOM_Ideas.init();
});