/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{

	if (typeof Cake === "undefined") Cake = {};
	
	Cake.Event = function($element) { this.__construct($element); };
	Cake.Event.prototype =
	{
		__construct: function($div)
		{
			this.$div = $div;
			this.$input = $div.find('.EventAllDay');
			
			this.toggleAllDayFields();
			
			this.$input.on('click', $.context(this, 'toggleAllDayFields'));
		},

		toggleAllDayFields: function()
		{
			if (this.$input.prop('checked')) {
				this.$div.find('.EventTime').addClass('disabled').find('select').prop('disabled', true);
				this.$div.find('.EventTimezone').hide().find('select').prop('disabled', true);
				if (!this.$input.data('allowmultipleday')) {
					this.$div.find('.EventEnd').hide().find('select, input').prop('disabled', true);
				}
			} else {
				this.$div.find('.EventTime').removeClass('disabled').find('select').prop('disabled', false);
				this.$div.find('.EventTimezone').show().find('select').prop('disabled', false);
				if (!this.$input.data('allowmultipleday')) {
					this.$div.find('.EventEnd').show().find('select, input').prop('disabled', false);
				}
			}
		}
	};

	// *********************************************************************

	XenForo.register('.Event', 'Cake.Event');

}
(jQuery, this, document);