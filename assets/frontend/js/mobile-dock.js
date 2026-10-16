/**
 * WPCalibrate Mobile Dock - Frontend Script
 * Fast, accessible, dependency-free vanilla JavaScript.
 */

(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		const dock = document.getElementById('wpcal-mobile-dock');
		if (!dock) {
			return;
		}

		// Read configuration passed via localized script or data attributes.
		const config = window.wpcalMobileDockConfig || {
			breakpoint: 768,
			showOnTablet: false,
			tabletBreakpoint: 1024,
			isWooActive: false
		};

		const effectiveBreakpoint = (config.showOnTablet && config.tabletBreakpoint > config.breakpoint)
			? config.tabletBreakpoint
			: config.breakpoint;

		// 1. Safe Body Padding & Spacing Management
		function updateBodySpacing() {
			const isVisible = window.innerWidth <= effectiveBreakpoint;
			if (isVisible) {
				document.body.classList.add('wpcal-has-mobile-dock');
			} else {
				document.body.classList.remove('wpcal-has-mobile-dock');
			}
		}

		updateBodySpacing();

		let resizeTimeout;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(updateBodySpacing, 100);
		}, { passive: true });

		// 2. Action Handlers (Back, Scroll-to-Top, Search)
		const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		dock.addEventListener('click', function (event) {
			const target = event.target.closest('[data-wpcal-action]');
			if (!target) {
				return;
			}

			const action = target.getAttribute('data-wpcal-action');

			if (action === 'back') {
				event.preventDefault();
				if (window.history.length > 1) {
					window.history.back();
				} else {
					window.location.href = '/';
				}
			} else if (action === 'scroll-top') {
				event.preventDefault();
				window.scrollTo({
					top: 0,
					behavior: prefersReducedMotion ? 'auto' : 'smooth'
				});
			} else if (action === 'search') {
				event.preventDefault();
				openSearchModal(target);
			}
		});

		// 3. Search Modal Dialog & Focus Management
		const searchModal = document.getElementById('wpcal-search-modal');
		let lastActiveTrigger = null;

		function openSearchModal(triggerEl) {
			if (!searchModal) {
				return;
			}
			lastActiveTrigger = triggerEl;
			searchModal.removeAttribute('hidden');
			if (triggerEl) {
				triggerEl.setAttribute('aria-expanded', 'true');
			}
			const searchInput = searchModal.querySelector('.wpcal-search-input');
			if (searchInput) {
				setTimeout(function () {
					searchInput.focus();
				}, 50);
			}
		}

		function closeSearchModal() {
			if (!searchModal) {
				return;
			}
			searchModal.setAttribute('hidden', '');
			if (lastActiveTrigger) {
				lastActiveTrigger.setAttribute('aria-expanded', 'false');
				lastActiveTrigger.focus();
				lastActiveTrigger = null;
			}
		}

		if (searchModal) {
			// Close on backdrop or close button click
			searchModal.addEventListener('click', function (event) {
				if (event.target.closest('[data-wpcal-close-search]')) {
					closeSearchModal();
				}
			});

			// Escape key dismisses modal
			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && !searchModal.hasAttribute('hidden')) {
					closeSearchModal();
				}
			});
		}

		// 4. WooCommerce Dynamic Cart Badge Bridge
		function updateCartBadge(count) {
			const badgeWrappers = document.querySelectorAll('.wpcal-dock-badge-wrapper');
			badgeWrappers.forEach(function (wrapper) {
				const badgeSpan = wrapper.querySelector('.wpcal-dock-badge');
				const srLabel = wrapper.querySelector('.wpcal-cart-sr-label');

				const numericCount = parseInt(count, 10) || 0;
				wrapper.setAttribute('data-cart-count', numericCount);

				if (numericCount <= 0) {
					wrapper.style.display = 'none';
				} else {
					wrapper.style.display = '';
					if (badgeSpan) {
						badgeSpan.textContent = numericCount > 99 ? '99+' : numericCount;
					}
					if (srLabel) {
						srLabel.textContent = 'Cart, ' + numericCount + ' items';
					}
				}
			});
		}

		// Listen to standard WooCommerce jQuery events if jQuery is present
		if (window.jQuery) {
			window.jQuery(document.body).on('added_to_cart removed_from_cart wc_cart_button_updated', function (e, fragments, cartHash, button) {
				if (fragments && fragments['span.wpcal-dock-badge-wrapper']) {
					// Replaced by fragment automatically
				} else if (fragments && fragments['div.widget_shopping_cart_content']) {
					// Fallback to fragment update if needed
				}
			});

			window.jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {
				// Fragments refreshed
			});
		}

		// Native CustomEvent listener for modern headless or custom cart implementations
		document.addEventListener('wc_fragments_refreshed', function () {
			// Triggered when fragments refreshed
		});
	});
})();
