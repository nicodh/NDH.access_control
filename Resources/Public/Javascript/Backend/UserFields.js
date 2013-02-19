/* global Prototype false */

Event.observe(window, 'load', function () {

});
/**
function toggleRelatedPropertyCheckboxes (objectIndex, accessType, visibility) {
   $$('tr.property_' + objectIndex + ' td[data-access="' + accessType + '"] input').each(
		   function (el) {
			   el[visibility]();
		   }
   );
}

Event.observe(window, 'load', function () {
   $$('#objectAccessTable input[type="radio"]').each(function (el) {
	   $(el).observe('click', function (event) {
		   var accessType = this.getAttribute('data-access'),
				   objectIndex = this.getAttribute('data-index'),
				   otherAccessType = '',
				   hidePropertyRows = false;
		   if (this.value.indexOf('grant') > -1) {
			   $$('tr.property_' + objectIndex).each(function (el) {
					 el.show();
				 }
			   );
			   toggleRelatedPropertyCheckboxes(objectIndex, accessType, 'show');
		   } else {
			   toggleRelatedPropertyCheckboxes(objectIndex, accessType, 'hide');
			   otherAccessType = (accessType == 'read') ? 'write' : 'read';
			   hidePropertyRows = true;
			   $$('#objectAccessTable input[type="radio"][data-access="' + otherAccessType + '"]][data-index="' + objectIndex + '"]').each(function(el) {
				   if (el.checked && el.value.indexOf('grant') > -1) {
					   hidePropertyRows = false;
				   }
			   });
			   if(hidePropertyRows) {
				   $$('tr.property_' + objectIndex).each(function (el) {
						 el.hide();
					 }
				   );
			   }
		   }
	   });
   });

});

*/