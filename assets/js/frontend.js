/**
 * Easy Mega Menu — Frontend interactions
 * Desktop: hover mega + category tabs
 * Mobile: drill-down screens (root → categories → links)
 */
(function () {
	'use strict';

	var MOBILE_MQ = '(max-width: 900px)';

	function closest(el, sel) {
		while (el && el.nodeType === 1) {
			if (el.matches(sel)) return el;
			el = el.parentElement;
		}
		return null;
	}

	function isMobile() {
		return window.matchMedia(MOBILE_MQ).matches;
	}

	function closeAllMegas(root) {
		root.querySelectorAll('.emm-nav__item--mega.is-open').forEach(function (item) {
			item.classList.remove('is-open');
			item.removeAttribute('data-drill');
			var btn = item.querySelector('[data-emm-mega-trigger]');
			var panel = item.querySelector('[data-emm-mega-panel]');
			if (btn) btn.setAttribute('aria-expanded', 'false');
			if (panel) {
				panel.hidden = true;
				positionFullWidthPanel(panel, null);
				resetDrillPanels(panel);
			}
		});
		root.classList.remove('emm-is-drilling');
		root.removeAttribute('data-drill-level');
	}

	function resetDrillPanels(mega) {
		mega.querySelectorAll('.emm-mega__section').forEach(function (section) {
			setSectionOpen(section, false);
		});
		var bar = mega.querySelector('[data-emm-mobile-bar]');
		if (bar) bar.hidden = true;
	}

	function setSectionOpen(section, open) {
		var btn = section.querySelector('.emm-mega__cat');
		var panel = section.querySelector('.emm-mega__panel');
		section.classList.toggle('is-open', open);
		if (btn) {
			btn.classList.toggle('is-active', open);
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
		if (panel) {
			panel.classList.toggle('is-active', open);
			panel.hidden = !open;
		}
	}

	function activateCategoryDesktop(catBtn) {
		var mega = closest(catBtn, '.emm-mega');
		if (!mega) return;
		var id = catBtn.getAttribute('data-emm-cat');

		mega.querySelectorAll('.emm-mega__section').forEach(function (section) {
			var on = section.getAttribute('data-emm-section') === id;
			setSectionOpen(section, on);
		});
	}

	function ensureMobileBar(mega) {
		var bar = mega.querySelector('[data-emm-mobile-bar]');
		if (bar) return bar;

		bar = document.createElement('div');
		bar.className = 'emm-mobile-bar';
		bar.setAttribute('data-emm-mobile-bar', '');
		bar.innerHTML =
			'<button type="button" class="emm-mobile-back" data-emm-back aria-label="Back">' +
			'<svg width="18" height="18" viewBox="0 0 12 12" aria-hidden="true"><path d="M7.5 2.5L4 6l3.5 3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
			'<span>Back</span></button>' +
			'<span class="emm-mobile-title" data-emm-mobile-title></span>' +
			'<button type="button" class="emm-nav__drawer-close emm-mobile-close" data-emm-drawer-close aria-label="Close menu">' +
			'<span aria-hidden="true">&times;</span></button>';
		mega.insertBefore(bar, mega.firstChild);
		return bar;
	}

	function setMobileBar(mega, title, visible) {
		var bar = ensureMobileBar(mega);
		bar.hidden = !visible;
		var titleEl = bar.querySelector('[data-emm-mobile-title]');
		if (titleEl) titleEl.textContent = title || '';
	}

	function positionFullWidthPanel(panel, header) {
		if (!panel) return;
		if (!header || !header.classList.contains('emm-full-width') || isMobile()) {
			panel.style.left = '';
			panel.style.right = '';
			panel.style.width = '';
			panel.style.maxWidth = '';
			panel.style.transform = '';
			return;
		}
		var nav = header.querySelector('.emm-nav') || header;
		var rect = nav.getBoundingClientRect();
		panel.style.left = -Math.round(rect.left) + 'px';
		panel.style.right = 'auto';
		panel.style.width = Math.round(window.innerWidth) + 'px';
		panel.style.maxWidth = 'none';
		panel.style.transform = 'none';
	}

	function repositionOpenFullWidth(header) {
		if (!header) return;
		header.querySelectorAll('[data-emm-mega-panel]').forEach(function (panel) {
			var open = closest(panel, '.emm-nav__item--mega.is-open');
			if (!open || isMobile() || !header.classList.contains('emm-full-width')) {
				positionFullWidthPanel(panel, null);
				return;
			}
			positionFullWidthPanel(panel, header);
		});
	}

	function openMegaDesktop(item) {
		var header = closest(item, '.emm-header');
		if (header) closeAllMegas(header);
		item.classList.add('is-open');
		var btn = item.querySelector('[data-emm-mega-trigger]');
		var panel = item.querySelector('[data-emm-mega-panel]');
		if (btn) btn.setAttribute('aria-expanded', 'true');
		if (panel) {
			panel.hidden = false;
			positionFullWidthPanel(panel, header);
			var sections = panel.querySelectorAll('.emm-mega__section');
			if (sections.length) {
				var active = panel.querySelector('.emm-mega__section.is-open') || sections[0];
				sections.forEach(function (s) {
					setSectionOpen(s, s === active);
				});
			}
		}
	}

	/** Mobile: open mega at categories screen (or links for features layout) */
	function openMegaMobile(item) {
		var header = closest(item, '.emm-header');
		if (!header) return;

		closeAllMegas(header);
		item.classList.add('is-open');

		var btn = item.querySelector('[data-emm-mega-trigger]');
		var panel = item.querySelector('[data-emm-mega-panel]');
		var label = (btn && btn.textContent ? btn.textContent : '').replace(/\s+/g, ' ').trim();

		if (btn) btn.setAttribute('aria-expanded', 'true');
		if (!panel) return;

		panel.hidden = false;
		header.classList.add('emm-is-drilling');

		var isFeatures = panel.getAttribute('data-emm-style') === 'features';
		if (isFeatures) {
			item.setAttribute('data-drill', 'links');
			header.setAttribute('data-drill-level', '2');
			setMobileBar(panel, label, true);
		} else {
			item.setAttribute('data-drill', 'cats');
			header.setAttribute('data-drill-level', '1');
			setMobileBar(panel, label, true);
			panel.querySelectorAll('.emm-mega__section').forEach(function (s) {
				setSectionOpen(s, false);
			});
		}

		header.querySelector('.emm-nav__drawer').scrollTop = 0;
	}

	function drillIntoCategory(item, catBtn) {
		var header = closest(item, '.emm-header');
		var mega = closest(catBtn, '.emm-mega');
		var section = closest(catBtn, '.emm-mega__section');
		if (!header || !mega || !section) return;

		mega.querySelectorAll('.emm-mega__section').forEach(function (s) {
			setSectionOpen(s, s === section);
		});

		item.setAttribute('data-drill', 'links');
		header.setAttribute('data-drill-level', '2');

		var title =
			(catBtn.querySelector('.emm-mega__cat-title') || {}).textContent ||
			catBtn.getAttribute('data-emm-cat') ||
			'';
		setMobileBar(mega, title.trim(), true);
		header.querySelector('.emm-nav__drawer').scrollTop = 0;
	}

	function drillBack(item) {
		var header = closest(item, '.emm-header');
		var panel = item.querySelector('[data-emm-mega-panel]');
		if (!header || !panel) return;

		var drill = item.getAttribute('data-drill');
		var isFeatures = panel.getAttribute('data-emm-style') === 'features';
		var trigger = item.querySelector('[data-emm-mega-trigger]');
		var rootLabel = (trigger && trigger.textContent ? trigger.textContent : '').replace(/\s+/g, ' ').trim();

		if (drill === 'links' && !isFeatures) {
			item.setAttribute('data-drill', 'cats');
			header.setAttribute('data-drill-level', '1');
			panel.querySelectorAll('.emm-mega__section').forEach(function (s) {
				setSectionOpen(s, false);
			});
			setMobileBar(panel, rootLabel, true);
			header.querySelector('.emm-nav__drawer').scrollTop = 0;
			return;
		}

		// Back to root menu
		closeAllMegas(header);
		header.querySelector('.emm-nav__drawer').scrollTop = 0;
	}

	function setDrawerOpen(header, open) {
		var toggle = header.querySelector('.emm-nav__toggle');
		var backdrop = header.querySelector('[data-emm-backdrop]');
		header.classList.toggle('is-mobile-open', open);
		if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		if (backdrop) backdrop.hidden = !open;
		document.documentElement.classList.toggle('emm-drawer-open', open);
		if (!open) closeAllMegas(header);
	}

	function syncMode(header) {
		if (isMobile()) return;

		header.classList.remove('emm-is-drilling');
		header.removeAttribute('data-drill-level');

		header.querySelectorAll('.emm-mega--platforms').forEach(function (mega) {
			var bar = mega.querySelector('[data-emm-mobile-bar]');
			if (bar) bar.hidden = true;

			var sections = mega.querySelectorAll('.emm-mega__section');
			if (!sections.length) return;
			var active = mega.querySelector('.emm-mega__section.is-open');
			if (!active) active = sections[0];
			sections.forEach(function (s) {
				setSectionOpen(s, s === active);
			});
		});

		header.querySelectorAll('.emm-nav__item--mega').forEach(function (item) {
			item.removeAttribute('data-drill');
		});
	}

	function initHeader(header) {
		var hoverMq = window.matchMedia('(hover: hover) and (pointer: fine)');
		var closeTimer = null;

		function clearCloseTimer() {
			if (closeTimer) {
				clearTimeout(closeTimer);
				closeTimer = null;
			}
		}

		function scheduleClose() {
			clearCloseTimer();
			closeTimer = setTimeout(function () {
				closeAllMegas(header);
			}, 250);
		}

		header.querySelectorAll('.emm-nav__item').forEach(function (item) {
			item.addEventListener('mouseenter', function () {
				if (isMobile() || !hoverMq.matches) return;
				clearCloseTimer();
				if (item.classList.contains('emm-nav__item--mega')) {
					openMegaDesktop(item);
				} else {
					closeAllMegas(header);
				}
			});
		});

		header.addEventListener('mouseleave', function () {
			if (!isMobile() && hoverMq.matches) scheduleClose();
		});

		header.addEventListener('mouseenter', function () {
			clearCloseTimer();
		});

		header.querySelectorAll('.emm-nav__item--mega').forEach(function (item) {
			var trigger = item.querySelector('[data-emm-mega-trigger]');
			if (!trigger) return;

			trigger.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				clearCloseTimer();

				if (isMobile()) {
					if (item.classList.contains('is-open')) {
						drillBack(item);
					} else {
						openMegaMobile(item);
					}
					return;
				}

				if (item.classList.contains('is-open')) {
					closeAllMegas(header);
				} else {
					openMegaDesktop(item);
				}
			});
		});

		header.querySelectorAll('.emm-mega__cat').forEach(function (btn) {
			btn.addEventListener('mouseenter', function () {
				if (!isMobile() && hoverMq.matches) activateCategoryDesktop(btn);
			});
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var item = closest(btn, '.emm-nav__item--mega');
				if (isMobile()) {
					if (item) drillIntoCategory(item, btn);
				} else {
					activateCategoryDesktop(btn);
				}
			});
			btn.addEventListener('keydown', function (e) {
				if (e.key !== 'Enter' && e.key !== ' ') return;
				e.preventDefault();
				e.stopPropagation();
				var item = closest(btn, '.emm-nav__item--mega');
				if (isMobile()) {
					if (item) drillIntoCategory(item, btn);
				} else {
					activateCategoryDesktop(btn);
				}
			});
		});

		header.addEventListener('click', function (e) {
			var back = e.target.closest ? e.target.closest('[data-emm-back]') : null;
			if (!back || !header.contains(back)) return;
			e.preventDefault();
			e.stopPropagation();
			var item = closest(back, '.emm-nav__item--mega');
			if (item) drillBack(item);
		});

		var toggle = header.querySelector('.emm-nav__toggle');
		if (toggle) {
			toggle.addEventListener('click', function (e) {
				e.stopPropagation();
				var open = toggle.getAttribute('aria-expanded') !== 'true';
				setDrawerOpen(header, open);
			});
		}

		header.addEventListener('click', function (e) {
			var closeBtn = e.target.closest ? e.target.closest('[data-emm-drawer-close]') : null;
			if (!closeBtn || !header.contains(closeBtn)) return;
			e.preventDefault();
			e.stopPropagation();
			setDrawerOpen(header, false);
		});

		var backdrop = header.querySelector('[data-emm-backdrop]');
		if (backdrop) {
			backdrop.addEventListener('click', function () {
				setDrawerOpen(header, false);
			});
		}

		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') return;
			clearCloseTimer();
			if (isMobile()) {
				var openItem = header.querySelector('.emm-nav__item--mega.is-open');
				if (openItem) {
					drillBack(openItem);
					return;
				}
			}
			closeAllMegas(header);
			if (header.classList.contains('is-mobile-open')) {
				setDrawerOpen(header, false);
			}
		});

		document.addEventListener('click', function (e) {
			if (header.contains(e.target)) return;
			clearCloseTimer();
			closeAllMegas(header);
			if (isMobile() && header.classList.contains('is-mobile-open')) {
				setDrawerOpen(header, false);
			}
		});

		window.addEventListener('resize', function () {
			if (!isMobile() && header.classList.contains('is-mobile-open')) {
				setDrawerOpen(header, false);
			}
			syncMode(header);
			repositionOpenFullWidth(header);
		});

		syncMode(header);
	}

	function init() {
		document.querySelectorAll('.emm-header').forEach(initHeader);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
