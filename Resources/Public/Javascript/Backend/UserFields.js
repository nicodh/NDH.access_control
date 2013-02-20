/*jshint devel: true */
/*global jQuery: false */
(function (globals, $) {
	'use strict';

	function toggleRelatedPropertyCheckboxes (objectIndex, accessType, visibility) {
		$('tr.property_' + objectIndex + ' td[data-access="' + accessType + '"] input').each(
			function (el) {
				$(el)[visibility]();
			}
		);
	}


	$(document).ready(function() {
		console.log($('#objectAccessTable input[type="radio"]'));
		$('#objectAccessTable input[type="radio"]').each(function (i, el) {
			$(el).on('click', function () {
				var accessType = $(this).data('access'),
				objectIndex = $(this).data('index'),
				otherAccessType = '',
				hidePropertyRows = false;
				if ($(this).val().indexOf('grant') > -1) {
					$('tr.property_' + objectIndex).each(function (el) {
						$(el).show();
					});
					toggleRelatedPropertyCheckboxes(objectIndex, accessType, 'show');
				} else {
					toggleRelatedPropertyCheckboxes(objectIndex, accessType, 'hide');
					otherAccessType = (accessType === 'read') ? 'write' : 'read';
					hidePropertyRows = true;
					$('#objectAccessTable input[type="radio"][data-access="' + otherAccessType + '"]][data-index="' + objectIndex + '"]').each(function(el) {
						if ($(el).attr('checked') && $(el).val().indexOf('grant') > -1) {
							hidePropertyRows = false;
						}
					});
					if(hidePropertyRows) {
						$('tr.property_' + objectIndex).each(function (el) {
							$(el).hide();
						});
					}
				}
			});
		});

		$('#controlAccessControllerActions input[type="radio"]').each(function (i, el) {
			$(el).on('click', function () {
				var type = $(this).data('type'),
				index = $(this).data('index');
				if ($(this).val() === 'grant') {
					$('tr.' + type + index).show();
				} else {
					$('tr.' + type + index).hide();
				}
			});
		});

		$('form[name="editForm"]').on(
			'submit',
			function() {
				alert('Submit');
				var policies = {methods:[]};
				$('input[data-actionname]:checked').each(
					function (i, el) {
						if($(el).val() === '1') {
							policies.methods.push($(el).data());
						}
					}
				);
				console.log(policies);
				return false;
			}
		);
	});

})(window, TYPO3.jQuery);
