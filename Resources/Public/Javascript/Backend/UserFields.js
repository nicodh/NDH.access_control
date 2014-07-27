/*jshint devel: true */
/*global jQuery: false */
(function (globals, $) {
	'use strict';

	var currentUid, existingPrivileges;

	function toggleRelatedPropertyCheckboxes (objectIndex, accessType, visibility) {
		$('tr.property_' + objectIndex + ' td[data-access="' + accessType + '"] input').each(
			function (el) {
				$(el)[visibility]();
			}
		);
	}

	function updateControllerActions() {

		var pluginName, controllerClassName, actionMethodName, overview;
		//overview = parseNodes(existingPrivileges.methods);
		//$('#controllerActionOverview').append(overview);
		if (existingPrivileges && typeof existingPrivileges.methods !== undefined) {
			for(pluginName in existingPrivileges.methods) {
				findByDataAttributes('input',{'pluginname':pluginName, 'privilege': 'grant'}).first().trigger('click');
				for(controllerClassName in existingPrivileges.methods[pluginName]) {
					findByDataAttributes('input',{'pluginname':pluginName, 'controllerClass':controllerClassName, 'privilege': 'grant'}).first().trigger('click');
					for(actionMethodName in existingPrivileges.methods[pluginName][controllerClassName]) {
						findByDataAttributes('input',{'pluginname':pluginName, 'controllerClass':controllerClassName, 'actionname': actionMethodName}).first().trigger('click');
					}
				}
			}
		}
		if (accessControlInheritedPrivileges && typeof accessControlInheritedPrivileges.methods !== undefined) {
			var inheritedControllers = findByDataAttributes('tr',{'inherited':'true'});
			inheritedControllers.each(function(index, inheritedController) {
				var tableRow = $(inheritedController);
				tableRow.prev('tr.pluginHeader').first().find('input').first().click();
				tableRow.prev('tr.pluginHeader').first().find('input').attr('disabled', true);
				tableRow.find('input').first().click();
				tableRow.find('input').attr('disabled', true);
			});
		}
	}

	function findByDataAttributes (selector, attributes) {
		$.each(attributes, function (key, value) {
			selector += "[data-" + key.toLowerCase() + "='" + value.replace(/\\/g,'\\\\') + "']";
		});
		return($(selector));
	}

	function parseNodes(nodes) { // takes a nodes array and turns it into a <ol>
		var ol = document.createElement("ol");
		for(var key in nodes) {
			if(typeof nodes[key] !== 'function') {
				var li = document.createElement("li");
				li.innerHTML = '<li><b>' + key + '</b></li>';
				if(typeof nodes[key] == 'object') {
					li.appendChild(parseNodes(nodes[key]));
				}
				ol.appendChild(li);
			}
		}
		return ol;
	}


	$(document).ready(function() {
		currentUid = globals.accessControlCurrentUid;
		existingPrivileges = globals.accessControlPrivileges;
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
					selector = '',
				index = $(this).data('index');
				if ($(this).attr('disabled')) {
					return;
				}
				selector = 'tr[data-index="' + index + '"]';
				if(index) {
					if ($(this).val() === 'grant') {
						$(selector).show();
					} else {
						$(selector).hide();
						if(type === 'plugin') {
							$('tr[data-index^="' + index + '"]').hide();
						}
					}
				}

			});
		});
		updateControllerActions();

	});


})(window, TYPO3.jQuery);