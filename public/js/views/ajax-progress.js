// --------------------------------------------------
// Show a loading indicator on all AJAX POST, PUT
// and DELETE.
// --------------------------------------------------
define(function (require) {
	
	// Dependencies
	var $ = require('jquery'),
		_ = require('underscore'),
		Backbone = require('backbone');
		
	// Private static vars
	var app,
		progress = 0, // How many requests have finished
		total = 0; // How many total requests have been made
	
	// Public view module
	var AjaxProgress = Backbone.View.extend({
		
		// Constructor
		initialize: function () {
			_.bindAll(this);
			app = this.options.app;
			
			// Shared vars
			this.$bar = this.$('.bar');
			
			// Listen for start and complete
			this.$el.ajaxSend(this.send);
			this.$el.ajaxComplete(this.complete);
		},
		
		// Add progress of a new ajax request, thus making the
		// progress smaller
		send: function() {
			total++;
			this.render();
		},
		
		// Remove progress of an ajax request cause it finished,
		// thus lengthening the bar
		complete: function() {
			progress++;
			if (progress == total) total = progress = 0; // Totally finished with all requests, so reset
			this.render();
		},
		
		// Update the position of the bar
		render: function() {
			
			// Show and hide the bar
			if (total > 0) this.$bar.stop(true).css('opacity', 1);
			else if (total === 0) this.$bar.stop(true).delay(300).animate({opacity:0}, function() {
				$(this).css('width', 0);
			});
			
			// Animate the bar
			var perc = (progress + 1) / (total + 1);
			this.$bar.css('width', (perc*100)+"%");
		}
		
	});
	
	// Return view
	return AjaxProgress;
	
});