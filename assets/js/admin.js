/**
 * Easy Mega Menu — Admin Builder
 */
(function ($) {
	'use strict';

	var S = emmAdmin.strings;
	var state = {
		menuId: '',
		menu: null,
		editingItemIndex: -1,
		editingCatIndex: -1,
		iconTarget: null, // { type: 'cat'|'link', catIndex, linkIndex }
		wpLink: null, // { $url, $text }
	};

	function uid() {
		return 'id_' + Math.random().toString(36).slice(2, 9);
	}

	function wpLinkFieldHtml(urlValue, urlClass, textSelector, disabled) {
		return (
			'<span class="emm-wp-link">' +
			'<input type="text" class="' +
			esc(urlClass) +
			' emm-url-field" value="' +
			esc(urlValue || '') +
			'" placeholder="' +
			esc(S.linkUrl) +
			'"' +
			(disabled ? ' disabled' : '') +
			' />' +
			'<button type="button" class="button emm-pick-url" title="' +
			esc(S.selectLink) +
			'"' +
			(disabled ? ' disabled' : '') +
			(textSelector ? ' data-emm-text-sel="' + esc(textSelector) + '"' : '') +
			'>' +
			'<span class="dashicons dashicons-admin-links" aria-hidden="true"></span>' +
			'</button>' +
			'</span>'
		);
	}

	function initWpLink() {
		$(document).on('click', '.emm-pick-url', function (e) {
			e.preventDefault();
			e.stopPropagation();
			if ($(this).prop('disabled')) return;

			var $btn = $(this);
			var $wrap = $btn.closest('.emm-wp-link');
			var $url = $wrap.find('.emm-url-field').first();
			var $text = null;

			if ($btn.data('emm-text')) {
				$text = $($btn.data('emm-text'));
			} else if ($btn.data('emm-text-sel')) {
				var $row = $btn.closest('.emm-link-row, .emm-nav-item-row');
				$text = $row.find($btn.data('emm-text-sel')).first();
			} else {
				var $rowFallback = $btn.closest('.emm-link-row, .emm-nav-item-row');
				$text = $rowFallback.find('.emm-link-label, .emm-nav-label').first();
			}

			openWpLink($url, $text.length ? $text : null);
		});

		// Capture-phase so we run before core wplink handlers.
		document.addEventListener(
			'click',
			function (e) {
				var submit = e.target && e.target.closest ? e.target.closest('#wp-link-submit') : null;
				if (!submit || !state.wpLink) return;

				e.preventDefault();
				e.stopImmediatePropagation();

				var urlInput = document.getElementById('wp-link-url');
				var textInput = document.getElementById('wp-link-text');
				var url = urlInput ? urlInput.value : '';
				var text = textInput ? textInput.value : '';

				if (state.wpLink.$url && state.wpLink.$url.length) {
					state.wpLink.$url.val(url).trigger('input').trigger('change');
				}
				if (text && state.wpLink.$text && state.wpLink.$text.length) {
					state.wpLink.$text.val(text).trigger('input').trigger('change');
				}

				if (typeof wpLink !== 'undefined' && wpLink.close) {
					wpLink.close();
				}
				state.wpLink = null;
			},
			true
		);

		$(document).on('wplink-close', function () {
			state.wpLink = null;
		});
	}

	function openWpLink($url, $text) {
		if (typeof wpLink === 'undefined' || !wpLink.open) {
			window.alert('WordPress link dialog is not available.');
			return;
		}

		state.wpLink = { $url: $url, $text: $text || null };

		if (!$('#emm-wplink-textarea').length) {
			$('body').append(
				'<textarea id="emm-wplink-textarea" style="display:none;" aria-hidden="true"></textarea>'
			);
		}

		window.wpActiveEditor = 'emm-wplink-textarea';
		wpLink.open(
			'emm-wplink-textarea',
			($url && $url.val()) || '',
			($text && $text.val()) || ''
		);

		setTimeout(function () {
			var urlEl = document.getElementById('wp-link-url');
			var textEl = document.getElementById('wp-link-text');
			if (urlEl && $url) urlEl.value = $url.val() || '';
			if (textEl && $text) textEl.value = $text.val() || '';
			if (urlEl) urlEl.focus();
		}, 50);
	}

	function getMenu() {
		return state.menu;
	}

	function syncSettingsFromForm() {
		var m = getMenu();
		m.title = $('#emm-menu-title').val();
		m.cta = {
			show: $('#emm-cta-show').is(':checked'),
			label: $('#emm-cta-label').val(),
			url: $('#emm-cta-url').val(),
		};
		m.settings = {
			preset: $('#emm-preset').val() || 'custom',
			layout: $('#emm-layout').val() || 'sidebar-left',
			sidebar_bg: $('#emm-sidebar-bg').val(),
			active_bg: $('#emm-active-bg').val(),
			panel_bg: $('#emm-panel-bg').val(),
			header_bg: $('#emm-header-bg').val(),
			accent: $('#emm-accent').val(),
			text_color: $('#emm-text-color').val(),
			nav_link_color: $('#emm-nav-link-color').val(),
			nav_hover_color: $('#emm-nav-hover-color').val(),
			muted_color: $('#emm-muted-color').val(),
			border_color: $('#emm-border-color').val(),
			cta_text: $('#emm-cta-text').val(),
			grid_columns: parseInt($('#emm-grid-cols').val(), 10) || 3,
			panel_width: parseInt($('#emm-panel-width').val(), 10) || 1000,
			full_width: $('#emm-full-width').is(':checked'),
			sidebar_width: parseInt($('#emm-sidebar-width').val(), 10) || 280,
			border_radius: parseInt($('#emm-border-radius').val(), 10) || 0,
			shadow: $('#emm-shadow').val() || 'medium',
			nav_align: $('#emm-nav-align').val() || 'center',
			cta_style: $('#emm-cta-style').val() || 'rounded',
			uppercase_cats: $('#emm-uppercase-cats').is(':checked'),
			show_cat_desc: $('#emm-show-cat-desc').is(':checked'),
			panel_title_align: $('#emm-panel-title-align').val() || 'center',
		};
	}

	function syncFullWidthField() {
		var on = $('#emm-full-width').is(':checked');
		$('#emm-panel-width').prop('disabled', on);
		$('#emm-panel-width-field').toggleClass('is-disabled', on);
	}

	function setColorInput(id, value) {
		var $input = $(id);
		$input.val(value);
		if ($input.hasClass('wp-color-picker') || $input.next('.wp-picker-container').length) {
			$input.wpColorPicker('color', value);
		}
	}

	function applyPreset(key, values) {
		$('#emm-preset').val(key);
		$('.emm-preset-card').removeClass('is-active');
		$('.emm-preset-card[data-preset="' + key + '"]').addClass('is-active');
		if (!values) return;
		setColorInput('#emm-sidebar-bg', values.sidebar_bg);
		setColorInput('#emm-active-bg', values.active_bg);
		setColorInput('#emm-panel-bg', values.panel_bg);
		setColorInput('#emm-header-bg', values.header_bg);
		setColorInput('#emm-accent', values.accent);
		setColorInput('#emm-text-color', values.text_color);
		if (values.nav_link_color) setColorInput('#emm-nav-link-color', values.nav_link_color);
		else if (values.text_color) setColorInput('#emm-nav-link-color', values.text_color);
		if (values.nav_hover_color) setColorInput('#emm-nav-hover-color', values.nav_hover_color);
		else if (values.accent) setColorInput('#emm-nav-hover-color', values.accent);
		setColorInput('#emm-muted-color', values.muted_color);
		setColorInput('#emm-border-color', values.border_color);
		setColorInput('#emm-cta-text', values.cta_text);
		if (values.shadow) $('#emm-shadow').val(values.shadow);
		if (typeof values.border_radius !== 'undefined') {
			$('#emm-border-radius').val(values.border_radius);
		}
		syncSettingsFromForm();
		renderPreview();
	}

	function markCustomPreset() {
		$('#emm-preset').val('custom');
		$('.emm-preset-card').removeClass('is-active');
		$('.emm-preset-card[data-preset="custom"]').addClass('is-active');
	}

	function settingsStyleAttr(s) {
		return (
			'--emm-sidebar-bg:' +
			(s.sidebar_bg || '#f0f0f5') +
			';--emm-active-bg:' +
			(s.active_bg || '#fff') +
			';--emm-panel-bg:' +
			(s.panel_bg || '#fff') +
			';--emm-header-bg:' +
			(s.header_bg || '#fff') +
			';--emm-accent:' +
			(s.accent || '#1a73e8') +
			';--emm-text:' +
			(s.text_color || '#2c2c2c') +
			';--emm-nav-link:' +
			(s.nav_link_color || s.text_color || '#2c2c2c') +
			';--emm-nav-hover:' +
			(s.nav_hover_color || s.accent || '#1a73e8') +
			';--emm-muted:' +
			(s.muted_color || '#6b6b6b') +
			';--emm-border:' +
			(s.border_color || '#e5e5ea') +
			';--emm-cta-text:' +
			(s.cta_text || '#fff') +
			';--emm-grid-cols:' +
			(s.grid_columns || 3) +
			';--emm-panel-width:' +
			(s.panel_width || 1000) +
			'px;--emm-sidebar-width:' +
			(s.sidebar_width || 280) +
			'px;--emm-radius:' +
			(s.border_radius != null ? s.border_radius : 8) +
			'px;'
		);
	}

	function settingsClassList(s) {
		var classes = [
			'emm-header',
			'emm-preview-frame',
			'emm-layout--' + (s.layout || 'sidebar-left'),
			'emm-shadow--' + (s.shadow || 'medium'),
			'emm-nav-align--' + (s.nav_align || 'center'),
			'emm-cta--' + (s.cta_style || 'rounded'),
			'emm-title--' + (s.panel_title_align || 'center'),
		];
		if (s.uppercase_cats) classes.push('emm-cats-upper');
		if (!s.show_cat_desc) classes.push('emm-hide-cat-desc');
		if (s.full_width) classes.push('emm-full-width');
		return classes.join(' ');
	}

	function setStatus(msg, isError) {
		var $el = $('.emm-save-status');
		$el.text(msg).toggleClass('is-error', !!isError).addClass('is-visible');
		if (msg) {
			setTimeout(function () {
				$el.removeClass('is-visible');
			}, 3000);
		}
	}

	/* ---------- List page ---------- */
	function initListPage() {
		$('.emm-create-menu').on('click', function () {
			var title = window.prompt(S.newMenu, S.newMenu);
			if (title === null) return;
			$.post(emmAdmin.ajaxUrl, {
				action: 'emm_create_menu',
				nonce: emmAdmin.nonce,
				title: title || S.newMenu,
			}).done(function (res) {
				if (res.success && res.data.url) {
					window.location = res.data.url;
				}
			});
		});

		$('.emm-delete-menu').on('click', function () {
			if (!window.confirm(S.confirmDelete)) return;
			var id = $(this).data('menu-id');
			$.post(emmAdmin.ajaxUrl, {
				action: 'emm_delete_menu',
				nonce: emmAdmin.nonce,
				menu_id: id,
			}).done(function (res) {
				if (res.success) {
					$('.emm-menu-card[data-menu-id="' + id + '"]').fadeOut(200, function () {
						$(this).remove();
					});
				}
			});
		});

		$('.emm-copy-shortcode').on('click', function () {
			var sc = $(this).data('shortcode');
			if (navigator.clipboard) {
				navigator.clipboard.writeText(sc);
			} else {
				var $tmp = $('<input>').val(sc).appendTo('body').select();
				document.execCommand('copy');
				$tmp.remove();
			}
			var $btn = $(this);
			var old = $btn.text();
			$btn.text(S.copyShortcode);
			setTimeout(function () {
				$btn.text(old);
			}, 1500);
		});
	}

	/* ---------- Builder ---------- */
	function initBuilder() {
		var $builder = $('#emm-builder');
		if (!$builder.length) return;

		state.menuId = $builder.data('menu-id');
		try {
			state.menu = JSON.parse($builder.attr('data-menu'));
		} catch (e) {
			state.menu = { title: '', items: [], cta: {}, settings: {} };
		}
		if (!state.menu.items) state.menu.items = [];
		if (!state.menu.categories) {
			/* ensure structure */
		}

		$('.emm-color').wpColorPicker({
			change: function () {
				markCustomPreset();
				setTimeout(renderPreview, 50);
			},
			clear: function () {
				markCustomPreset();
				setTimeout(renderPreview, 50);
			},
		});

		initWpLink();

		var designSelectors = [
			'#emm-menu-title',
			'#emm-cta-show',
			'#emm-cta-label',
			'#emm-cta-url',
			'#emm-layout',
			'#emm-nav-align',
			'#emm-grid-cols',
			'#emm-full-width',
			'#emm-panel-width',
			'#emm-sidebar-width',
			'#emm-border-radius',
			'#emm-shadow',
			'#emm-cta-style',
			'#emm-panel-title-align',
			'#emm-uppercase-cats',
			'#emm-show-cat-desc',
		].join(', ');

		$(designSelectors).on('change input', function () {
			if (
				$(this).is(
					'#emm-layout, #emm-nav-align, #emm-grid-cols, #emm-full-width, #emm-panel-width, #emm-sidebar-width, #emm-border-radius, #emm-shadow, #emm-cta-style, #emm-panel-title-align, #emm-uppercase-cats, #emm-show-cat-desc'
				)
			) {
				/* keep preset unless colors were edited */
			}
			if ($(this).is('#emm-full-width')) {
				syncFullWidthField();
			}
			syncSettingsFromForm();
			renderPreview();
		});

		$('#emm-preset-grid').on('click', '.emm-preset-card:not([disabled])', function () {
			var key = $(this).data('preset');
			var values = $(this).attr('data-values');
			try {
				values = values ? JSON.parse(values) : null;
			} catch (err) {
				values = null;
			}
			applyPreset(key, values);
		});

		renderNavItems();
		syncFullWidthField();
		renderPreview();
		bindBuilderEvents();
		initIconModal();
	}

	function bindBuilderEvents() {
		$('.emm-add-nav-item').on('click', function () {
			getMenu().items.push({
				id: uid(),
				label: 'New Item',
				url: '#',
				type: 'link',
			});
			renderNavItems();
			renderPreview();
		});

		$('.emm-save-menu').on('click', saveMenu);

		$('#emm-nav-items').on('click', '.emm-remove-nav', function () {
			if (!window.confirm(S.confirmRemove)) return;
			var id = $(this).closest('.emm-nav-item-row').data('id');
			var items = getMenu().items;
			getMenu().items = items.filter(function (it) {
				return it.id !== id;
			});
			if (state.editingItemIndex >= 0) {
				closeMegaEditor();
			}
			renderNavItems();
			renderPreview();
		});

		$('#emm-nav-items').on('change input', '.emm-nav-label, .emm-nav-url, .emm-nav-type', function () {
			var $row = $(this).closest('.emm-nav-item-row');
			var id = $row.data('id');
			var item = getMenu().items.find(function (it) {
				return it.id === id;
			});
			if (!item) return;

			item.label = $row.find('.emm-nav-label').val();
			item.url = $row.find('.emm-nav-url').val();
			var newType = $row.find('.emm-nav-type').val();
			if (newType !== item.type) {
				item.type = newType;
				if (newType === 'mega') {
					if (!item.mega_style) item.mega_style = 'platforms';
					if (!item.columns) item.columns = item.mega_style === 'features' ? 2 : 3;
					if (!item.categories) item.categories = [];
					if (!item.links) item.links = [];
				}
				renderNavItems();
			}
			renderPreview();
		});

		$('#emm-nav-items').on('click', '.emm-edit-mega', function () {
			var id = $(this).closest('.emm-nav-item-row').data('id');
			var idx = getMenu().items.findIndex(function (it) {
				return it.id === id;
			});
			openMegaEditor(idx);
		});

		$('.emm-close-mega-editor').on('click', closeMegaEditor);

		$('.emm-mega-style-card').on('click', function () {
			var item = getMenu().items[state.editingItemIndex];
			if (!item) return;
			item.mega_style = $(this).data('style');
			if (item.mega_style === 'features' && (!item.columns || item.columns === 3)) {
				item.columns = 2;
			}
			if (item.mega_style === 'platforms' && (!item.columns || item.columns === 2)) {
				item.columns = 3;
			}
			syncMegaEditorUI();
			renderPreview();
		});

		$('#emm-mega-item-columns').on('change', function () {
			var item = getMenu().items[state.editingItemIndex];
			if (!item) return;
			item.columns = parseInt($(this).val(), 10) || 2;
			renderPreview();
		});

		$('.emm-add-feature-link').on('click', function () {
			var item = getMenu().items[state.editingItemIndex];
			if (!item) return;
			if (!item.links) item.links = [];
			item.links.push({
				id: uid(),
				label: 'New Feature',
				url: '#',
				icon: 'link',
				icon_url: '',
			});
			renderFeaturesLinkList();
			renderPreview();
		});

		$('#emm-features-link-list').on('click', '.emm-remove-link', function () {
			if (!window.confirm(S.confirmRemove)) return;
			var item = getMenu().items[state.editingItemIndex];
			var id = $(this).closest('.emm-link-row').data('id');
			item.links = (item.links || []).filter(function (l) {
				return l.id !== id;
			});
			renderFeaturesLinkList();
			renderPreview();
		});

		$('#emm-features-link-list').on('input change', '.emm-link-label, .emm-link-url', function () {
			var item = getMenu().items[state.editingItemIndex];
			var $row = $(this).closest('.emm-link-row');
			var id = $row.data('id');
			var link = (item.links || []).find(function (l) {
				return l.id === id;
			});
			if (!link) return;
			link.label = $row.find('.emm-link-label').val();
			link.url = $row.find('.emm-link-url').val();
			renderPreview();
		});

		$('#emm-features-link-list').on('click', '.emm-pick-link-icon', function () {
			var idx = $(this).closest('.emm-link-row').index();
			openIconPicker({ type: 'feature-link', linkIndex: idx });
		});

		$('.emm-add-category').on('click', function () {
			var item = getMenu().items[state.editingItemIndex];
			if (!item) return;
			if (!item.categories) item.categories = [];
			item.categories.push({
				id: uid(),
				title: 'NEW',
				description: '',
				icon: 'grid',
				icon_url: '',
				panel_title: 'Overview',
				panel_url: '',
				groups: [
					{
						id: uid(),
						title: 'Column 1',
						url: '',
						links: [],
					},
				],
				links: [],
			});
			renderCatList();
			selectCategory(item.categories.length - 1);
			renderPreview();
		});

		$('#emm-cat-list').on('click', '.emm-cat-row', function (e) {
			if ($(e.target).closest('.emm-remove-cat, .emm-pick-icon').length) return;
			selectCategory($(this).index());
		});

		$('#emm-cat-list').on('click', '.emm-remove-cat', function (e) {
			e.stopPropagation();
			if (!window.confirm(S.confirmRemove)) return;
			var item = getMenu().items[state.editingItemIndex];
			var idx = $(this).closest('.emm-cat-row').index();
			item.categories.splice(idx, 1);
			state.editingCatIndex = -1;
			renderCatList();
			$('#emm-cat-detail').html(
				'<p class="emm-placeholder">' + S.selectIcon.replace('icon', 'category') + '</p>'
			);
			showCatPlaceholder();
			renderPreview();
		});

		$('#emm-cat-list').on('click', '.emm-pick-icon', function (e) {
			e.stopPropagation();
			var idx = $(this).closest('.emm-cat-row').index();
			openIconPicker({ type: 'cat', catIndex: idx });
		});

		$('#emm-cat-list').on('input change', '.emm-cat-title, .emm-cat-desc', function () {
			var item = getMenu().items[state.editingItemIndex];
			var idx = $(this).closest('.emm-cat-row').index();
			var cat = item.categories[idx];
			if (!cat) return;
			cat.title = $(this).closest('.emm-cat-row').find('.emm-cat-title').val();
			cat.description = $(this).closest('.emm-cat-row').find('.emm-cat-desc').val();
			renderPreview();
		});
	}

	function showCatPlaceholder() {
		$('#emm-cat-detail').html(
			'<p class="emm-placeholder">' +
				'Select a category on the left to edit its links.' +
				'</p>'
		);
	}

	function renderNavItems() {
		var $list = $('#emm-nav-items').empty();
		getMenu().items.forEach(function (item) {
			var isMega = item.type === 'mega';
			var styleLabel =
				item.mega_style === 'features' ? 'Simple columns' : 'With categories';
			var $row = $(
				'<li class="emm-nav-item-row" data-id="' +
					esc(item.id) +
					'">' +
					'<span class="emm-drag-handle" title="' +
					esc(S.dragHint) +
					'">⋮⋮</span>' +
					'<div class="emm-nav-item-row__fields">' +
					'<input type="text" class="emm-nav-label" value="' +
					esc(item.label) +
					'" placeholder="Label" />' +
					wpLinkFieldHtml(item.url || '', 'emm-nav-url', '.emm-nav-label', isMega) +
					'<select class="emm-nav-type">' +
					'<option value="link"' +
					(!isMega ? ' selected' : '') +
					'>' +
					esc(S.simpleLink) +
					'</option>' +
					'<option value="mega"' +
					(isMega ? ' selected' : '') +
					'>' +
					esc(S.megaMenu) +
					'</option>' +
					'</select>' +
					(isMega
						? '<span class="emm-mega-style-badge">' + esc(styleLabel) + '</span>'
						: '') +
					'</div>' +
					(isMega
						? '<button type="button" class="button button-small emm-edit-mega">' +
						  'Edit Mega Content' +
						  '</button>'
						: '') +
					'<button type="button" class="button-link-delete emm-remove-nav" title="Remove">&times;</button>' +
					'</li>'
			);
			$list.append($row);
		});

		$list.sortable({
			handle: '.emm-drag-handle',
			update: function () {
				var order = [];
				$list.children().each(function () {
					var id = $(this).data('id');
					var found = getMenu().items.find(function (it) {
						return it.id === id;
					});
					if (found) order.push(found);
				});
				getMenu().items = order;
				renderPreview();
			},
		});
	}

	function openMegaEditor(index) {
		state.editingItemIndex = index;
		var item = getMenu().items[index];
		if (!item.mega_style) item.mega_style = 'platforms';
		if (!item.columns) item.columns = item.mega_style === 'features' ? 2 : 3;
		if (!item.categories) item.categories = [];
		if (!item.links) item.links = [];
		$('#emm-mega-editor-title').text('Edit Mega Menu: ' + (item.label || ''));
		$('#emm-mega-editor').prop('hidden', false);
		syncMegaEditorUI();
		$('html, body').animate({ scrollTop: $('#emm-mega-editor').offset().top - 40 }, 200);
	}

	function syncMegaEditorUI() {
		var item = getMenu().items[state.editingItemIndex];
		if (!item) return;
		var style = item.mega_style || 'platforms';

		$('.emm-mega-style-card').removeClass('is-active');
		$('.emm-mega-style-card[data-style="' + style + '"]').addClass('is-active');
		$('#emm-mega-item-columns').val(String(item.columns || (style === 'features' ? 2 : 3)));

		if (style === 'features') {
			$('#emm-editor-platforms').prop('hidden', true);
			$('#emm-editor-features').prop('hidden', false);
			renderFeaturesLinkList();
		} else {
			$('#emm-editor-platforms').prop('hidden', false);
			$('#emm-editor-features').prop('hidden', true);
			renderCatList();
			if (item.categories.length) {
				var catIdx = state.editingCatIndex;
				if (catIdx < 0 || catIdx >= item.categories.length) {
					catIdx = 0;
				}
				selectCategory(catIdx);
			} else {
				state.editingCatIndex = -1;
				showCatPlaceholder();
			}
		}
	}

	function renderFeaturesLinkList() {
		var item = getMenu().items[state.editingItemIndex];
		var $list = $('#emm-features-link-list').empty();
		if (!item) return;

		(item.links || []).forEach(function (link) {
			$list.append(linkRowHtml(link));
		});

		$list.sortable({
			handle: '.emm-drag-handle',
			update: function () {
				var order = [];
				$list.children().each(function () {
					var id = $(this).data('id');
					var found = item.links.find(function (l) {
						return l.id === id;
					});
					if (found) order.push(found);
				});
				item.links = order;
				renderPreview();
			},
		});
	}

	function closeMegaEditor() {
		state.editingItemIndex = -1;
		state.editingCatIndex = -1;
		$('#emm-mega-editor').prop('hidden', true);
	}

	function renderCatList() {
		var item = getMenu().items[state.editingItemIndex];
		var $list = $('#emm-cat-list').empty();
		if (!item) return;

		(item.categories || []).forEach(function (cat, i) {
			var iconHtml = iconMarkup(cat.icon, cat.icon_url);
			var $row = $(
				'<li class="emm-cat-row' +
					(i === state.editingCatIndex ? ' is-selected' : '') +
					'" data-id="' +
					esc(cat.id) +
					'">' +
					'<span class="emm-drag-handle">⋮⋮</span>' +
					'<button type="button" class="emm-pick-icon" title="' +
					esc(S.selectIcon) +
					'">' +
					iconHtml +
					'</button>' +
					'<div class="emm-cat-row__fields">' +
					'<input type="text" class="emm-cat-title" value="' +
					esc(cat.title) +
					'" placeholder="' +
					esc(S.categoryTitle) +
					'" />' +
					'<input type="text" class="emm-cat-desc" value="' +
					esc(cat.description) +
					'" placeholder="' +
					esc(S.categoryDesc) +
					'" />' +
					'</div>' +
					'<button type="button" class="button-link-delete emm-remove-cat">&times;</button>' +
					'</li>'
			);
			$list.append($row);
		});

		$list.sortable({
			handle: '.emm-drag-handle',
			update: function () {
				var order = [];
				$list.children().each(function () {
					var id = $(this).data('id');
					var found = item.categories.find(function (c) {
						return c.id === id;
					});
					if (found) order.push(found);
				});
				item.categories = order;
				renderPreview();
			},
		});
	}

	function selectCategory(index) {
		state.editingCatIndex = index;
		$('#emm-cat-list .emm-cat-row').removeClass('is-selected').eq(index).addClass('is-selected');
		renderCatDetail();
	}

	function ensureCategoryGroups(cat) {
		if (!cat) return;
		if (!Array.isArray(cat.groups)) cat.groups = [];
		if (cat.groups.length) return;
		if (Array.isArray(cat.links) && cat.links.length) {
			cat.groups.push({
				id: uid(),
				title: '',
				url: '',
				links: cat.links.slice(),
			});
			cat.links = [];
			return;
		}
		cat.groups.push({
			id: uid(),
			title: 'Column 1',
			url: '',
			links: [],
		});
	}

	function renderCatDetail() {
		var item = getMenu().items[state.editingItemIndex];
		var cat = item && item.categories[state.editingCatIndex];
		var $detail = $('#emm-cat-detail');
		if (!cat) {
			showCatPlaceholder();
			return;
		}

		ensureCategoryGroups(cat);

		var html =
			'<div class="emm-cat-detail-inner">' +
			'<label class="emm-field"><span>' +
			esc(S.panelTitle) +
			'</span>' +
			'<input type="text" class="emm-panel-title" value="' +
			esc(cat.panel_title || '') +
			'" /></label>' +
			'<label class="emm-field"><span>' +
			esc(S.panelUrl) +
			'</span>' +
			wpLinkFieldHtml(cat.panel_url, 'emm-panel-url', '.emm-panel-title', false) +
			'</label>' +
			'<div class="emm-col-editor-head">' +
			'<div>' +
			'<h3>Columns</h3>' +
			'<p class="description">Split this category into columns with headings (e.g. Finance, Spend Management).</p>' +
			'</div>' +
			'<button type="button" class="button emm-add-group">+ Add column</button>' +
			'</div>' +
			'<div class="emm-group-list" id="emm-group-list"></div>' +
			'</div>';

		$detail.html(html);

		var $groupList = $('#emm-group-list');
		(cat.groups || []).forEach(function (group, gi) {
			$groupList.append(groupBlockHtml(group, gi));
		});

		$detail.find('.emm-panel-title').on('input', function () {
			cat.panel_title = $(this).val();
			renderPreview();
		});

		$detail.find('.emm-panel-url').on('input change', function () {
			cat.panel_url = $(this).val();
			renderPreview();
		});

		$detail.find('.emm-add-group').on('click', function () {
			cat.groups.push({
				id: uid(),
				title: 'Column ' + (cat.groups.length + 1),
				url: '',
				links: [],
			});
			renderCatDetail();
			renderPreview();
		});

		$detail.on('click', '.emm-remove-group', function () {
			if (cat.groups.length <= 1) {
				window.alert('Keep at least one column.');
				return;
			}
			if (!window.confirm(S.confirmRemove)) return;
			var gid = $(this).closest('.emm-group-block').data('id');
			cat.groups = cat.groups.filter(function (g) {
				return g.id !== gid;
			});
			renderCatDetail();
			renderPreview();
		});

		$detail.on('input', '.emm-group-title', function () {
			var gid = $(this).closest('.emm-group-block').data('id');
			var group = cat.groups.find(function (g) {
				return g.id === gid;
			});
			if (!group) return;
			group.title = $(this).val();
			renderPreview();
		});

		$detail.on('input change', '.emm-group-url', function () {
			var gid = $(this).closest('.emm-group-block').data('id');
			var group = cat.groups.find(function (g) {
				return g.id === gid;
			});
			if (!group) return;
			group.url = $(this).val();
			renderPreview();
		});

		$detail.on('click', '.emm-add-group-link', function () {
			var gid = $(this).closest('.emm-group-block').data('id');
			var group = cat.groups.find(function (g) {
				return g.id === gid;
			});
			if (!group) return;
			if (!group.links) group.links = [];
			group.links.push({
				id: uid(),
				label: 'New Link',
				url: '#',
				icon: '',
				icon_url: '',
			});
			renderCatDetail();
			renderPreview();
		});

		$detail.on('click', '.emm-remove-link', function () {
			if (!window.confirm(S.confirmRemove)) return;
			var $row = $(this).closest('.emm-link-row');
			var $block = $(this).closest('.emm-group-block');
			var gid = $block.data('id');
			var lid = $row.data('id');
			var group = cat.groups.find(function (g) {
				return g.id === gid;
			});
			if (!group) return;
			group.links = (group.links || []).filter(function (l) {
				return l.id !== lid;
			});
			renderCatDetail();
			renderPreview();
		});

		$detail.on('input change', '.emm-link-label, .emm-link-url', function () {
			var $row = $(this).closest('.emm-link-row');
			var $block = $(this).closest('.emm-group-block');
			var gid = $block.data('id');
			var lid = $row.data('id');
			var group = cat.groups.find(function (g) {
				return g.id === gid;
			});
			if (!group) return;
			var link = (group.links || []).find(function (l) {
				return l.id === lid;
			});
			if (!link) return;
			link.label = $row.find('.emm-link-label').val();
			link.url = $row.find('.emm-link-url').val();
			renderPreview();
		});

		$detail.on('click', '.emm-pick-link-icon', function () {
			var $row = $(this).closest('.emm-link-row');
			var $block = $(this).closest('.emm-group-block');
			openIconPicker({
				type: 'group-link',
				catIndex: state.editingCatIndex,
				groupId: $block.data('id'),
				linkId: $row.data('id'),
			});
		});

		$groupList.find('.emm-group-link-list').each(function () {
			var $list = $(this);
			var gid = $list.closest('.emm-group-block').data('id');
			$list.sortable({
				handle: '.emm-drag-handle',
				update: function () {
					var group = cat.groups.find(function (g) {
						return g.id === gid;
					});
					if (!group) return;
					var order = [];
					$list.children().each(function () {
						var id = $(this).data('id');
						var found = (group.links || []).find(function (l) {
							return l.id === id;
						});
						if (found) order.push(found);
					});
					group.links = order;
					renderPreview();
				},
			});
		});
	}

	function groupBlockHtml(group) {
		var linksHtml = '';
		(group.links || []).forEach(function (link) {
			linksHtml += linkRowHtml(link);
		});

		return (
			'<div class="emm-group-block" data-id="' +
			esc(group.id) +
			'">' +
			'<div class="emm-group-block__head">' +
			'<input type="text" class="emm-group-title" value="' +
			esc(group.title || '') +
			'" placeholder="Column heading (e.g. Finance)" />' +
			'<button type="button" class="button-link-delete emm-remove-group" title="Remove column">&times;</button>' +
			'</div>' +
			'<label class="emm-field emm-field--compact"><span>' +
			esc(S.groupUrl) +
			'</span>' +
			wpLinkFieldHtml(group.url, 'emm-group-url', '.emm-group-title', false) +
			'</label>' +
			'<ul class="emm-sortable emm-link-list emm-group-link-list">' +
			linksHtml +
			'</ul>' +
			'<button type="button" class="button-link emm-add-group-link">+ ' +
			esc(S.addLink) +
			'</button>' +
			'</div>'
		);
	}

	function linkRowHtml(link) {
		return (
			'<li class="emm-link-row" data-id="' +
			esc(link.id) +
			'">' +
			'<span class="emm-drag-handle">⋮⋮</span>' +
			'<button type="button" class="emm-pick-link-icon" title="' +
			esc(S.selectIcon) +
			'">' +
			iconMarkup(link.icon, link.icon_url) +
			'</button>' +
			'<input type="text" class="emm-link-label" value="' +
			esc(link.label) +
			'" placeholder="' +
			esc(S.linkLabel) +
			'" />' +
			wpLinkFieldHtml(link.url || '', 'emm-link-url', '.emm-link-label', false) +
			'<button type="button" class="button-link-delete emm-remove-link">&times;</button>' +
			'</li>'
		);
	}

	/* ---------- Preview ---------- */
	function renderPreview() {
		syncSettingsFromForm();
		var m = getMenu();
		var s = m.settings || {};
		var style = settingsStyleAttr(s);
		var className = settingsClassList(s);

		var itemsHtml = '';
		(m.items || []).forEach(function (item, idx) {
			var isMega = item.type === 'mega';
			itemsHtml +=
				'<li class="emm-nav__item' +
				(isMega ? ' emm-nav__item--mega' : '') +
				'">' +
				'<span class="emm-nav__link">' +
				esc(item.label) +
				(isMega
					? ' <svg class="emm-chevron" width="12" height="12" viewBox="0 0 12 12"><path d="M2.5 4.5L6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>'
					: '') +
				'</span>';

			if (
				isMega &&
				((item.mega_style === 'features' && item.links && item.links.length) ||
					((!item.mega_style || item.mega_style === 'platforms') &&
						item.categories &&
						item.categories.length))
			) {
				itemsHtml += renderMegaPreview(item, idx);
			}
			itemsHtml += '</li>';
		});

		var cta = '';
		if (m.cta && m.cta.show && m.cta.label) {
			cta = '<span class="emm-nav__cta emm-nav__cta--drawer">' + esc(m.cta.label) + '</span>';
		}

		var html =
			'<div class="' +
			className +
			'" style="' +
			style +
			'">' +
			'<nav class="emm-nav"><div class="emm-nav__drawer"><div class="emm-nav__inner">' +
			'<ul class="emm-nav__list">' +
			itemsHtml +
			'</ul>' +
			cta +
			'</div></div></nav></div>';

		$('#emm-preview').html(html);

		// Preview interactivity: open first mega, switch categories on hover
		var $preview = $('#emm-preview');
		$preview.find('.emm-nav__item--mega').first().addClass('is-open').find('.emm-mega').prop('hidden', false);

		$preview.off('mouseenter.emmcat').on('mouseenter.emmcat', '.emm-mega__cat', function () {
			var catId = $(this).data('emm-cat');
			var $mega = $(this).closest('.emm-mega');
			$mega.find('.emm-mega__section').each(function () {
				var on = $(this).data('emm-section') === catId;
				$(this).toggleClass('is-open', on);
				$(this).find('.emm-mega__cat').toggleClass('is-active', on).attr('aria-expanded', on ? 'true' : 'false');
				$(this).find('.emm-mega__panel').toggleClass('is-active', on).prop('hidden', !on);
			});
		});
	}

	function renderMegaPreview(item) {
		var style = item.mega_style || 'platforms';
		var cols = item.columns || (style === 'features' ? 2 : 3);

		if (style === 'features') {
			var featLinks = '';
			(item.links || []).forEach(function (link) {
				featLinks +=
					'<li class="emm-mega__grid-item"><span class="emm-mega__grid-link">' +
					'<span class="emm-mega__grid-icon">' +
					iconMarkup(link.icon, link.icon_url, 'emm-icon emm-icon--sm') +
					'</span>' +
					'<span class="emm-mega__grid-label">' +
					esc(link.label) +
					'</span></span></li>';
			});
			return (
				'<div class="emm-mega emm-mega--features" style="position:relative;display:block;">' +
				'<div class="emm-mega__inner emm-mega__inner--features">' +
				'<ul class="emm-mega__grid emm-mega__grid--features" style="--emm-grid-cols:' +
				cols +
				'">' +
				featLinks +
				'</ul></div></div>'
			);
		}

		var cats = item.categories || [];
		var sections = '';

		cats.forEach(function (cat, i) {
			var active = i === 0;
			ensureCategoryGroups(cat);
			var groups = cat.groups || [];
			var colsHtml = '';
			groups.forEach(function (group) {
				var links = '';
				(group.links || []).forEach(function (link) {
					var hasIcon = !!(link.icon || link.icon_url);
					links +=
						'<li class="emm-mega__col-item"><span class="emm-mega__col-link' +
						(hasIcon ? ' has-icon' : '') +
						'">' +
						(hasIcon
							? '<span class="emm-mega__col-icon">' +
							  iconMarkup(link.icon, link.icon_url, 'emm-icon emm-icon--sm') +
							  '</span>'
							: '') +
						'<span class="emm-mega__col-label">' +
						esc(link.label) +
						'</span></span></li>';
				});
				var colTitle = '';
				if (group.title) {
					colTitle =
						'<h4 class="emm-mega__col-title">' +
						(group.url
							? '<a href="' + esc(group.url) + '">' + esc(group.title) + '</a>'
							: esc(group.title)) +
						'</h4>';
				}
				colsHtml +=
					'<div class="emm-mega__col">' +
					colTitle +
					'<ul class="emm-mega__col-list">' +
					links +
					'</ul></div>';
			});

			sections +=
				'<section class="emm-mega__section' +
				(active ? ' is-open' : '') +
				'" data-emm-section="' +
				esc(cat.id) +
				'">' +
				'<div class="emm-mega__cat' +
				(active ? ' is-active' : '') +
				'" data-emm-cat="' +
				esc(cat.id) +
				'" role="button" tabindex="0">' +
				'<span class="emm-mega__cat-icon">' +
				iconMarkup(cat.icon, cat.icon_url, 'emm-icon emm-icon--lg') +
				'</span>' +
				'<span class="emm-mega__cat-text">' +
				'<span class="emm-mega__cat-title">' +
				esc(cat.title) +
				'</span>' +
				(cat.description
					? '<span class="emm-mega__cat-desc">' + esc(cat.description) + '</span>'
					: '') +
				'</span></div>' +
				'<div class="emm-mega__panel' +
				(active ? ' is-active' : '') +
				'" data-emm-panel="' +
				esc(cat.id) +
				'"' +
				(active ? '' : ' hidden') +
				'>' +
				(cat.panel_title
					? '<h3 class="emm-mega__panel-title">' +
					  (cat.panel_url
							? '<a href="' + esc(cat.panel_url) + '">' + esc(cat.panel_title) + '</a>'
							: esc(cat.panel_title)) +
					  '</h3>'
					: '') +
				'<div class="emm-mega__columns" style="--emm-col-count:' +
				Math.max(1, groups.length) +
				'">' +
				colsHtml +
				'</div></div></section>';
		});

		return (
			'<div class="emm-mega emm-mega--platforms" style="position:relative;display:block;">' +
			'<div class="emm-mega__inner">' +
			'<div class="emm-mega__accordion">' +
			sections +
			'</div></div></div>'
		);
	}

	/* ---------- Icons ---------- */
	function iconMarkup(key, url, cls) {
		cls = cls || 'emm-icon';
		if (url) {
			return '<img src="' + esc(url) + '" class="' + cls + ' emm-icon--custom" alt="" />';
		}
		if (key && emmAdmin.icons[key]) {
			return '<span class="' + cls + '">' + emmAdmin.icons[key] + '</span>';
		}
		return '<span class="' + cls + '">' + (emmAdmin.icons.link || '') + '</span>';
	}

	function initIconModal() {
		var $grid = $('#emm-icon-grid').empty();
		Object.keys(emmAdmin.icons).forEach(function (key) {
			var label = emmAdmin.iconLabels[key] || key;
			$grid.append(
				'<button type="button" class="emm-icon-option" data-icon="' +
					esc(key) +
					'" title="' +
					esc(label) +
					'">' +
					emmAdmin.icons[key] +
					'<span>' +
					esc(label) +
					'</span></button>'
			);
		});

		$('#emm-icon-grid').on('click', '.emm-icon-option', function () {
			applyIcon($(this).data('icon'), '');
			closeIconModal();
		});

		$('.emm-modal__close, .emm-modal__backdrop').on('click', closeIconModal);

		$('.emm-upload-icon').on('click', function () {
			var frame = wp.media({
				title: S.uploadIcon,
				button: { text: 'Use icon' },
				multiple: false,
			});
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				applyIcon('', att.url);
				closeIconModal();
			});
			frame.open();
		});

		$('.emm-clear-icon').on('click', function () {
			applyIcon('link', '');
			closeIconModal();
		});
	}

	function openIconPicker(target) {
		state.iconTarget = target;
		$('#emm-icon-modal').prop('hidden', false);
	}

	function closeIconModal() {
		$('#emm-icon-modal').prop('hidden', true);
		state.iconTarget = null;
	}

	function applyIcon(key, url) {
		var t = state.iconTarget;
		if (!t) return;
		var item = getMenu().items[state.editingItemIndex];
		if (!item) return;

		if (t.type === 'cat') {
			var cat = item.categories[t.catIndex];
			if (cat) {
				cat.icon = key;
				cat.icon_url = url;
			}
			renderCatList();
			if (state.editingCatIndex === t.catIndex) renderCatDetail();
		} else if (t.type === 'link') {
			var c = item.categories[t.catIndex];
			var link = c && c.links && c.links[t.linkIndex];
			if (link) {
				link.icon = key;
				link.icon_url = url;
			}
			renderCatDetail();
		} else if (t.type === 'group-link') {
			var gc = item.categories[t.catIndex];
			var group =
				gc &&
				(gc.groups || []).find(function (g) {
					return g.id === t.groupId;
				});
			var gl =
				group &&
				(group.links || []).find(function (l) {
					return l.id === t.linkId;
				});
			if (gl) {
				gl.icon = key;
				gl.icon_url = url;
			}
			renderCatDetail();
		} else if (t.type === 'feature-link') {
			var fl = item.links && item.links[t.linkIndex];
			if (fl) {
				fl.icon = key;
				fl.icon_url = url;
			}
			renderFeaturesLinkList();
		}
		renderPreview();
	}

	function saveMenu() {
		syncSettingsFromForm();
		var $btn = $('.emm-save-menu').prop('disabled', true);
		$.post(emmAdmin.ajaxUrl, {
			action: 'emm_save_menu',
			nonce: emmAdmin.nonce,
			menu_id: state.menuId,
			menu_data: JSON.stringify(getMenu()),
		})
			.done(function (res) {
				if (res.success) {
					setStatus(S.saved, false);
				} else {
					setStatus(S.saveError, true);
				}
			})
			.fail(function () {
				setStatus(S.saveError, true);
			})
			.always(function () {
				$btn.prop('disabled', false);
			});
	}

	function esc(str) {
		if (str == null) return '';
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	$(function () {
		if ($('#emm-builder').length) {
			initBuilder();
		} else {
			initListPage();
		}
	});
})(jQuery);
