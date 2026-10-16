/**
 * WPCalibrate Mobile Dock - Admin JavaScript
 */

(function ($) {
	'use strict';

	$(function () {
		// Initialize WordPress Color Pickers if available
		if ($.fn.wpColorPicker) {
			$('.wpcal-color-picker').wpColorPicker();
		}

		// 1. Tablet Breakpoint Toggle
		$('#wpcal-tablet-toggle, #wpcal-global-tablet-toggle').on('change', function () {
			const isChecked = $(this).is(':checked');
			$('#wpcal-tablet-bp-wrap, #wpcal-global-tablet-bp-wrap').toggle(isChecked);
		});

		// 2. Appearance Override Toggle
		$('#wpcal-override-appearance-toggle').on('change', function () {
			$('#wpcal-appearance-fields').toggle($(this).is(':checked'));
		});

		// 3. Preset Palette Switcher
		const presets = {
			light: {
				bg: '#ffffff',
				icon: '#4b5563',
				actIcon: '#2563eb',
				text: '#6b7280',
				actText: '#2563eb'
			},
			dark: {
				bg: '#18181b',
				icon: '#a1a1aa',
				actIcon: '#60a5fa',
				text: '#9ca3af',
				actText: '#60a5fa'
			}
		};

		$('#wpcal-dock-preset-select, #wpcal-global-preset-select').on('change', function () {
			const val = $(this).val();
			if (presets[val]) {
				const form = $(this).closest('form, .wpcal-section-card');
				updateColor(form.find('input[name*="[bg_color]"]'), presets[val].bg);
				updateColor(form.find('input[name*="[icon_color]"]'), presets[val].icon);
				updateColor(form.find('input[name*="[active_icon_color]"]'), presets[val].actIcon);
				updateColor(form.find('input[name*="[text_color]"]'), presets[val].text);
				updateColor(form.find('input[name*="[active_text_color]"]'), presets[val].actText);
			}
		});

		function updateColor($input, color) {
			$input.val(color);
			if ($input.wpColorPicker) {
				$input.wpColorPicker('color', color);
			}
		}

		// 4. Item Details Accordion Toggle
		$(document).on('click', '.wpcal-toggle-details-btn', function (e) {
			e.preventDefault();
			const card = $(this).closest('.wpcal-item-card');
			card.find('.wpcal-item-card__body').slideToggle(150);
		});

		// 5. Dynamic Target Fields per Action Type
		$(document).on('change', '.wpcal-action-select', function () {
			const action = $(this).val();
			const card = $(this).closest('.wpcal-item-card');

			card.find('.wpcal-target-field').hide();
			card.find('.wpcal-target-' + action).show();
			syncItemTarget(card);
		});

		// Sync target input to carrier
		$(document).on('input change', '.wpcal-item-card__body input, .wpcal-item-card__body select', function () {
			const card = $(this).closest('.wpcal-item-card');
			syncItemTarget(card);
		});

		function syncItemTarget(card) {
			const action = card.find('.wpcal-action-select').val();
			let targetVal = '';

			if (action === 'custom_url') {
				targetVal = card.find('input[name*="[target_url]"]').val();
			} else if (action === 'page') {
				targetVal = card.find('select[name*="[target_page]"]').val();
			} else if (action === 'post') {
				targetVal = card.find('input[name*="[target_post]"]').val();
			} else if (action === 'phone') {
				targetVal = card.find('input[name*="[target_phone]"]').val();
			} else if (action === 'whatsapp') {
				targetVal = card.find('input[name*="[target_whatsapp]"]').val();
			} else if (action === 'email') {
				targetVal = card.find('input[name*="[target_email]"]').val();
			} else if (action === 'wc_category') {
				targetVal = card.find('select[name*="[target_wc_category]"]').val();
			}

			card.find('.wpcal-target-carrier').val(targetVal || '');
		}

		// 6. Center Raised Button Uniqueness
		$(document).on('change', '.wpcal-center-raised-checkbox', function () {
			if ($(this).is(':checked')) {
				$('.wpcal-center-raised-checkbox').not(this).prop('checked', false);
			}
		});

		// 7. Reorder Controls (Keyboard accessible Move Up / Move Down)
		$(document).on('click', '.wpcal-move-up-btn', function (e) {
			e.preventDefault();
			const card = $(this).closest('.wpcal-item-card');
			const prev = card.prev('.wpcal-item-card');
			if (prev.length) {
				card.insertBefore(prev);
				reindexItems();
			}
		});

		$(document).on('click', '.wpcal-move-down-btn', function (e) {
			e.preventDefault();
			const card = $(this).closest('.wpcal-item-card');
			const next = card.next('.wpcal-item-card');
			if (next.length) {
				card.insertAfter(next);
				reindexItems();
			}
		});

		// 8. Delete Item
		$(document).on('click', '.wpcal-delete-item-btn', function (e) {
			e.preventDefault();
			const card = $(this).closest('.wpcal-item-card');
			const label = card.find('.wpcal-item-label-input').val();
			if (!label || confirm('Are you sure you want to remove this item?')) {
				card.remove();
				reindexItems();
				updateItemsWarning();
			}
		});

		// 9. Add Item
		let itemIndexCounter = 100;
		$('#wpcal-add-item-btn').on('click', function (e) {
			e.preventDefault();
			itemIndexCounter++;

			const firstCard = $('.wpcal-item-card').first();
			let newCardHtml = '';

			if (firstCard.length) {
				const clone = firstCard.clone();
				clone.attr('data-item-index', itemIndexCounter);
				clone.find('input[name*="[id]"]').val('item_' + Math.random().toString(36).substring(2, 9));
				clone.find('.wpcal-item-label-input').val('');
				clone.find('.wpcal-action-select').val('home');
				clone.find('.wpcal-target-field').hide();
				clone.find('.wpcal-target-carrier').val('');
				clone.find('input[name*="[target_url]"]').val('');
				clone.find('input[name*="[target_phone]"]').val('');
				clone.find('input[name*="[target_whatsapp]"]').val('');
				clone.find('input[name*="[target_email]"]').val('');
				clone.find('select[name*="[target_page]"]').val('');
				clone.find('.wpcal-center-raised-checkbox').prop('checked', false);
				clone.find('input[type="checkbox"][name*="[enabled]"]').prop('checked', true);

				// Update unique input ID for icon picker
				const newIconInputId = 'wpcal-icon-input-' + itemIndexCounter;
				clone.find('.wpcal-icon-select-btn').attr('data-target-input', newIconInputId);
				clone.find('.wpcal-icon-btn-preview').attr('id', 'wpcal-icon-preview-' + itemIndexCounter);
				clone.find('input[name*="[icon]"]').attr('id', newIconInputId).val('home');

				newCardHtml = clone;
			} else {
				// Fallback simple item template
				newCardHtml = '<div class="wpcal-item-card" data-item-index="' + itemIndexCounter + '">' +
					'<div class="wpcal-item-card__header">' +
					'<div class="wpcal-item-card__drag"><span class="dashicons dashicons-menu"></span></div>' +
					'<div class="wpcal-item-card__enable"><input type="checkbox" name="items[' + itemIndexCounter + '][enabled]" value="1" checked /></div>' +
					'<div class="wpcal-item-card__title"><input type="text" name="items[' + itemIndexCounter + '][label]" placeholder="Item Label" class="regular-text wpcal-item-label-input" required /></div>' +
					'<div class="wpcal-item-card__action-type"><select name="items[' + itemIndexCounter + '][action_type]" class="wpcal-action-select"><option value="home">Home</option><option value="custom_url">Custom URL</option><option value="search">Search</option></select></div>' +
					'<div class="wpcal-item-card__controls"><button type="button" class="button button-small button-link-delete wpcal-delete-item-btn">&times;</button></div>' +
					'</div></div>';
			}

			$('.wpcal-no-items-notice').hide();
			$('#wpcal-items-container').append(newCardHtml);
			reindexItems();
			updateItemsWarning();
		});

		function reindexItems() {
			$('.wpcal-item-card').each(function (idx) {
				const card = $(this);
				card.attr('data-item-index', idx);
				card.find('input, select').each(function () {
					const name = $(this).attr('name');
					if (name) {
						$(this).attr('name', name.replace(/items\[\d+\]/, 'items[' + idx + ']'));
					}
				});
			});
			updateItemsWarning();
		}

		function updateItemsWarning() {
			const count = $('.wpcal-item-card').length;
			const $warning = $('#wpcal-items-warning');
			const $text = $('#wpcal-warning-text');

			if (count > 7) {
				$warning.show();
				$text.text('Warning: Having more than 7 items may crowd the mobile screen on narrow devices.');
			} else if (count > 0 && count < 2) {
				$warning.show();
				$text.text('Notice: Navigation docks typically feel best with at least 2 items.');
			} else {
				$warning.hide();
			}
		}

		// 10. Native HTML5 Drag and Drop for Items
		let draggedItem = null;

		$(document).on('dragstart', '.wpcal-item-card', function (e) {
			draggedItem = this;
			$(this).addClass('is-dragging');
			e.originalEvent.dataTransfer.effectAllowed = 'move';
		});

		$(document).on('dragend', '.wpcal-item-card', function () {
			$(this).removeClass('is-dragging');
			draggedItem = null;
			reindexItems();
		});

		$(document).on('dragover', '.wpcal-item-card', function (e) {
			e.preventDefault();
			if (!draggedItem || draggedItem === this) {
				return;
			}
			const bounding = this.getBoundingClientRect();
			const offset = bounding.y + (bounding.height / 2);
			if (e.originalEvent.clientY - offset > 0) {
				$(this).after(draggedItem);
			} else {
				$(this).before(draggedItem);
			}
		});

		$('.wpcal-item-card').attr('draggable', 'true');

		// 11. Icon Picker Modal
		let activeIconInput = null;
		let activeIconPreview = null;

		$(document).on('click', '.wpcal-icon-select-btn', function (e) {
			e.preventDefault();
			const targetInputId = $(this).data('target-input');
			activeIconInput = $('#' + targetInputId);
			activeIconPreview = $(this).find('.wpcal-icon-btn-preview');

			$('#wpcal-icon-search-input').val('');
			$('.wpcal-icon-option').show();
			$('#wpcal-icon-picker-modal').removeAttr('hidden');
			$('#wpcal-icon-search-input').focus();
		});

		$('#wpcal-close-icon-btn, #wpcal-close-icon-modal').on('click', function () {
			$('#wpcal-icon-picker-modal').attr('hidden', '');
		});

		$(document).on('keydown', function (e) {
			if (e.key === 'Escape' && !$('#wpcal-icon-picker-modal').hasAttribute('hidden')) {
				$('#wpcal-icon-picker-modal').attr('hidden', '');
			}
		});

		// Live icon search
		$('#wpcal-icon-search-input').on('input', function () {
			const query = $(this).val().toLowerCase().trim();
			$('.wpcal-icon-option').each(function () {
				const name = $(this).data('icon-label').toString().toLowerCase();
				const id = $(this).data('icon-id').toString().toLowerCase();
				if (name.includes(query) || id.includes(query)) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		// Select icon from grid
		$(document).on('click', '.wpcal-icon-option', function () {
			const iconId = $(this).data('icon-id');
			const iconSvg = $(this).find('.wpcal-icon-preview').html();

			if (activeIconInput && activeIconInput.length) {
				activeIconInput.val(iconId);
			}
			if (activeIconPreview && activeIconPreview.length) {
				activeIconPreview.html(iconSvg);
			}

			$('#wpcal-icon-picker-modal').attr('hidden', '');
		});

		// 12. Visibility Rule Add / Remove
		let ruleIndexCounter = 100;
		$('#wpcal-add-rule-btn').on('click', function (e) {
			e.preventDefault();
			ruleIndexCounter++;

			const firstRule = $('.wpcal-rule-card').first();
			let newRuleHtml = '';

			if (firstRule.length) {
				const clone = firstRule.clone();
				clone.attr('data-rule-index', ruleIndexCounter);
				clone.find('select[name*="[operator]"]').val('include');
				clone.find('select[name*="[condition_type]"]').val('entire_site');
				clone.find('input[name*="[target_ids_input]"]').val('');
				clone.find('select[name*="[user_auth]"]').val('all');
				newRuleHtml = clone;
			} else {
				newRuleHtml = '<div class="wpcal-rule-card" data-rule-index="' + ruleIndexCounter + '">' +
					'<div class="wpcal-rule-card__inner">' +
					'<div class="wpcal-rule-field"><select name="visibility_rules[' + ruleIndexCounter + '][operator]"><option value="include">Show On (Include)</option><option value="exclude">Hide On (Exclude)</option></select></div>' +
					'<div class="wpcal-rule-field"><select name="visibility_rules[' + ruleIndexCounter + '][condition_type]"><option value="entire_site">Entire Site</option><option value="front_page">Front Page</option><option value="blog_page">Blog / Posts Index</option><option value="pages">Selected Pages</option></select></div>' +
					'<div class="wpcal-rule-field"><input type="text" name="visibility_rules[' + ruleIndexCounter + '][target_ids_input]" placeholder="Target IDs (e.g. 12, 45)" class="regular-text" /></div>' +
					'<div class="wpcal-rule-field"><select name="visibility_rules[' + ruleIndexCounter + '][user_auth]"><option value="all">All Users</option><option value="logged_in">Logged-in Only</option><option value="logged_out">Logged-out Only</option></select></div>' +
					'<div class="wpcal-rule-field wpcal-rule-actions"><button type="button" class="button button-small button-link-delete wpcal-delete-rule-btn">&times; Remove</button></div>' +
					'</div></div>';
			}

			$('.wpcal-no-rules-notice').hide();
			$('#wpcal-rules-container').append(newRuleHtml);
			reindexRules();
		});

		$(document).on('click', '.wpcal-delete-rule-btn', function (e) {
			e.preventDefault();
			$(this).closest('.wpcal-rule-card').remove();
			reindexRules();
		});

		function reindexRules() {
			$('.wpcal-rule-card').each(function (idx) {
				const card = $(this);
				card.attr('data-rule-index', idx);
				card.find('input, select').each(function () {
					const name = $(this).attr('name');
					if (name) {
						$(this).attr('name', name.replace(/visibility_rules\[\d+\]/, 'visibility_rules[' + idx + ']'));
					}
				});
			});
		}
	});
})(jQuery);
