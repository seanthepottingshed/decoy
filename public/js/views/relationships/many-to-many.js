// --------------------------------------------------
// Many to Many relationship creator view
// --------------------------------------------------
define(function (require) {
	
	// Dependencies
	var $ = require('jquery'),
		_ = require('underscore'),
		Backbone = require('backbone'),
		Autocomplete = require('decoy/views/autocomplete');
			
	// Public view module
	var ManyToMany = Autocomplete.extend({
		
		// Init
		initialize: function () {

			// There must be a parent_id and parent_controller defined for the saving to work
			this.parent_id = this.$el.data('parent-id');
			this.parent_controller = this.$el.data('parent-controller');

			// Call init after the parent info is read
			Autocomplete.prototype.initialize.apply(this, arguments);
			
			// Cache selectors
			this.$submit = this.$('button[type="submit"]');
			this.$icon = this.$submit.find('i');
			
			// Add extra events
			this.$el.on('submit', this.attach);
		},
		
		// Add the parent stuff to query
		url: function() {
			return Autocomplete.prototype.url.apply(this)+'&'+$.param({
				parent_id: this.parent_id,
				parent_controller: this.parent_controller
			});
		},
		
		// Overide the match function to toggle the state of the add button
		match: function() {
			var changed = Autocomplete.prototype.match.apply(this, arguments);
			if (this.found) this.enable();
			else this.disable();
		},
		
		// Enable the form
		enable: function() {
			if (this.$submit.hasClass('btn-info')) return;
			this.$submit.addClass('btn-info').prop('disabled', false);
			this.$icon.addClass('icon-white');
		},
		
		// Disable the form
		disable: function() {
			if (!this.$submit.hasClass('btn-info')) return;
			this.$submit.removeClass('btn-info').prop('disabled', true);
			this.$icon.removeClass('icon-white');
		},
		
		// Determine if the form should be disabled
		disabled: function() {
			return this.$submit.prop('disabled');
		},
		
		// Tell the server to attach the selected item
		attach: function (e) {
			if (e) e.preventDefault();
			
			// Don't execute it no match is found.  Call the base match
			// because we don't want any UI logic now.
			Autocomplete.prototype.match.apply(this, arguments);
			if (!this.found) return;
				
			// Make the request
			$.ajax(this.route+'/'+this.id+'/attach', {
				data: {
					parent_id: this.parent_id,
					parent_controller: this.parent_controller},
				type:'POST',
				dataType: 'JSON'
			})
			
			// Success
			.done(_.bind(function(data) {
				
				// Tell the editable list to add the new entry
				var payload = { id: this.id, parent_id: this.parent_id, columns: this.selection.columns };
				this.$el.trigger('insert', payload);
				
				// Clear the input to add another.  Must use typeahead to clear or it will reset
				// the value after you de-focus.
				this.$input.typeahead('val', '')
				.focus()
				.prop('placeholder', 'Add another');
				this.match();
				
			}, this));
		}
		
	});
	
	return ManyToMany;
});