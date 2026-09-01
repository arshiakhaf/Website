/**
 * آرین‌شاپ — اسکریپت اصلی قالب (بدون وابستگی خارجی)
 */
(function () {
	'use strict';

	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

	/* ---------------- ابزار ---------------- */
	function debounce(fn, wait) {
		var t;
		return function () {
			var args = arguments, ctx = this;
			clearTimeout(t);
			t = setTimeout(function () { fn.apply(ctx, args); }, wait);
		};
	}

	function faDigits(text) {
		var en = '0123456789';
		var fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
		return String(text).replace(/[0-9]/g, function (d) { return fa[en.indexOf(d)]; });
	}

	/* تبدیل اعداد لاتین داخل عناصر قیمت به فارسی */
	function persianize() {
		$$('.price, .amount, .woocommerce-Price-amount, .pi-reviews, .count, .qv-price, .pay-amount, .cd-box strong, .hero-stats strong').forEach(function (el) {
			if (el.dataset.faDone) { return; }
			var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
			var nodes = [];
			while (walker.nextNode()) { nodes.push(walker.currentNode); }
			var changed = false;
			nodes.forEach(function (node) {
				if (node.nodeValue && /[0-9]/.test(node.nodeValue)) {
					node.nodeValue = faDigits(node.nodeValue);
					changed = true;
				}
			});
			if (changed) { el.dataset.faDone = '1'; }
		});
	}

	/* ---------------- منوی موبایل ---------------- */
	var overlay = $('[data-overlay]');
	var mobileMenu = $('[data-mobile-menu]');

	function openMenu() {
		if (mobileMenu) { mobileMenu.hidden = false; requestAnimationFrame(function () { mobileMenu.classList.add('is-open'); }); }
		if (overlay) { overlay.hidden = false; }
		document.body.style.overflow = 'hidden';
	}
	function closeMenu() {
		if (mobileMenu) { mobileMenu.classList.remove('is-open'); setTimeout(function () { mobileMenu.hidden = true; }, 300); }
		if (overlay) { setTimeout(function () { overlay.hidden = true; }, 150); }
		document.body.style.overflow = '';
	}
	if ($('[data-open-menu]')) { $('[data-open-menu]').addEventListener('click', openMenu); }
	if ($('[data-close-menu]')) { $('[data-close-menu]').addEventListener('click', closeMenu); }
	if (overlay) { overlay.addEventListener('click', closeMenu); }

	/* ---------------- فیلتر موبایل ---------------- */
	function openFilters() {
		var sb = $('#shop-sidebar');
		if (!sb) { return; }
		sb.classList.add('is-open');
		if (overlay) { overlay.hidden = false; }
	}
	function closeFilters() {
		var sb = $('#shop-sidebar');
		if (sb) { sb.classList.remove('is-open'); }
		if (overlay && !(mobileMenu && mobileMenu.classList.contains('is-open'))) { overlay.hidden = true; }
	}
	if ($('[data-open-filters]')) { $('[data-open-filters]').addEventListener('click', openFilters); }
	if ($('[data-close-filters]')) { $('[data-close-filters]').addEventListener('click', closeFilters); }

	/* ---------------- نوار بالا: سایه و دکمه بالای صفحه ---------------- */
	var header = $('#site-header');
	var toTop = $('[data-to-top]');
	window.addEventListener('scroll', function () {
		if (header) { header.classList.toggle('is-scrolled', window.scrollY > 8); }
		if (toTop) { toTop.classList.toggle('is-visible', window.scrollY > 500); }
	}, { passive: true });
	if (toTop) {
		toTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
	}

	/* ---------------- دسته‌بندی‌ها (مگامنو) ---------------- */
	var catToggle = $('[data-toggle-cats]');
	var catsPanel = $('[data-cats-panel]');
	if (catToggle && catsPanel) {
		catToggle.addEventListener('click', function (e) {
			e.stopPropagation();
			catsPanel.hidden = !catsPanel.hidden;
		});
		document.addEventListener('click', function (e) {
			if (!catsPanel.hidden && !catsPanel.contains(e.target) && e.target !== catToggle) {
				catsPanel.hidden = true;
			}
		});
	}

	/* ---------------- مینی‌سبد ---------------- */
	var cartBtn = $('[data-toggle-cart]');
	var cartPanel = $('[data-cart-panel]');
	if (cartBtn && cartPanel) {
		cartBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			cartPanel.hidden = !cartPanel.hidden;
		});
		document.addEventListener('click', function (e) {
			if (!cartPanel.hidden && !cartPanel.contains(e.target) && !cartBtn.contains(e.target)) {
				cartPanel.hidden = true;
			}
		});
	}
	if ($('[data-close-cart]')) {
		$('[data-close-cart]').addEventListener('click', function () {
			if (cartPanel) { cartPanel.hidden = true; }
		});
	}

	/* ---------------- جستجوی زنده ---------------- */
	var searchBox = $('[data-searchbox]');
	var searchInput = $('[data-search-input]');
	var searchResults = $('[data-search-results]');
	if (searchBox && searchInput && searchResults && window.arianData) {
		var doSearch = debounce(function () {
			var term = searchInput.value.trim();
			if (term.length < 2) {
				searchResults.classList.remove('is-open');
				searchResults.innerHTML = '';
				return;
			}
			fetch(arianData.ajaxUrl + '?action=arian_search&term=' + encodeURIComponent(term) + '&nonce=' + arianData.nonce)
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!res.success) { return; }
					var items = res.data.items || [];
					searchResults.classList.add('is-open');
					searchResults.innerHTML = items.length
						? items.map(function (it) {
							var img = it.img
								? '<img src="' + it.img + '" alt="">'
								: '<span class="sr-item-icon">📦</span>';
							return '<a class="sr-item" href="' + it.url + '">' + img +
								'<span><strong>' + it.title + '</strong><span class="price">' + it.price + '</span></span></a>';
						}).join('')
						: '<div class="sr-empty">نتیجه‌ای پیدا نشد</div>';
					persianize();
				})
				.catch(function () {});
		}, 280);
		searchInput.addEventListener('input', doSearch);
		document.addEventListener('click', function (e) {
			if (!searchBox.contains(e.target)) { searchResults.classList.remove('is-open'); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { searchResults.classList.remove('is-open'); }
		});
	}

	/* ---------------- پیش‌نمایش سریع ---------------- */
	var qvModal = $('[data-qv-modal]');
	var qvContent = $('[data-qv-content]');
	function openQuickView(id) {
		if (!qvModal || !qvContent || !window.arianData) { return; }
		qvContent.innerHTML = '<div class="qv-loading">در حال بارگذاری...</div>';
		qvModal.hidden = false;
		document.body.style.overflow = 'hidden';
		fetch(arianData.ajaxUrl + '?action=arian_quick_view&product_id=' + id + '&nonce=' + arianData.nonce)
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res.success) {
					qvContent.innerHTML = res.data.html;
					persianize();
				} else {
					qvContent.innerHTML = '<div class="qv-loading">محصول پیدا نشد.</div>';
				}
			})
			.catch(function () { qvContent.innerHTML = '<div class="qv-loading">خطا در بارگذاری.</div>'; });
	}
	function closeQuickView() {
		if (!qvModal) { return; }
		qvModal.hidden = true;
		document.body.style.overflow = '';
	}
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-quickview]');
		if (btn) {
			e.preventDefault();
			openQuickView(btn.getAttribute('data-quickview'));
		}
		if (e.target.closest('[data-qv-close]')) { closeQuickView(); }
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closeQuickView(); }
	});

	/* ---------------- علاقه‌مندی‌ها ---------------- */
	var WISH_KEY = 'arian_wishlist';
	function getWishlist() {
		try { return JSON.parse(localStorage.getItem(WISH_KEY) || '[]'); } catch (e) { return []; }
	}
	function saveWishlist(list) {
		localStorage.setItem(WISH_KEY, JSON.stringify(list));
		var count = $('[data-wl-count]');
		if (count) { count.textContent = faDigits(list.length); }
		$$('[data-wishlist]').forEach(function (btn) {
			btn.classList.toggle('is-active', list.indexOf(String(btn.getAttribute('data-wishlist'))) !== -1);
		});
	}
	saveWishlist(getWishlist());
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-wishlist]');
		if (!btn) { return; }
		e.preventDefault();
		var id = String(btn.getAttribute('data-wishlist'));
		var list = getWishlist();
		var idx = list.indexOf(id);
		if (idx === -1) { list.push(id); } else { list.splice(idx, 1); }
		saveWishlist(list);
	});

	/* ---------------- شمارش معکوس ---------------- */
	$$('[data-countdown]').forEach(function (el) {
		var end = parseInt(el.getAttribute('data-countdown'), 10) * 1000;
		var days = el.querySelector('[data-cd-days]');
		var hours = el.querySelector('[data-cd-hours]');
		var mins = el.querySelector('[data-cd-mins]');
		var secs = el.querySelector('[data-cd-secs]');
		function tick() {
			var diff = Math.max(0, end - Date.now());
			var d = Math.floor(diff / 86400000);
			var h = Math.floor(diff % 86400000 / 3600000);
			var m = Math.floor(diff % 3600000 / 60000);
			var s = Math.floor(diff % 60000 / 1000);
			if (days) { days.textContent = faDigits(d); }
			if (hours) { hours.textContent = faDigits(String(h).padStart(2, '0')); }
			if (mins) { mins.textContent = faDigits(String(m).padStart(2, '0')); }
			if (secs) { secs.textContent = faDigits(String(s).padStart(2, '0')); }
		}
		tick();
		setInterval(tick, 1000);
	});

	/* ---------------- گالری تک‌محصول ---------------- */
	var spMain = $('.sp-mainimg img, .sp-mainimg .sp-img');
	$$('.sp-thumb').forEach(function (thumb) {
		thumb.addEventListener('click', function () {
			var full = thumb.getAttribute('data-full');
			$$('.sp-thumb').forEach(function (t) { t.classList.remove('is-active'); });
			thumb.classList.add('is-active');
			if (spMain) {
				spMain.src = full;
			}
		});
	});

	/* ---------------- خبرنامه (شبیه‌سازی آفلاین) ---------------- */
	var nlForm = $('[data-newsletter]');
	if (nlForm) {
		nlForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var btn = nlForm.querySelector('button');
			var old = btn.textContent;
			btn.textContent = '✔ عضویت شما ثبت شد';
			btn.disabled = true;
			setTimeout(function () {
				btn.textContent = old;
				btn.disabled = false;
				nlForm.querySelector('input').value = '';
			}, 3500);
		});
	}

	/* ---------------- رویدادهای ووکامرس ---------------- */
	document.addEventListener('added_to_cart', function () {
		persianize();
		if (cartPanel) { cartPanel.hidden = false; }
	});
	document.addEventListener('updated_wc_div', persianize);
	document.addEventListener('updated_cart_totals', persianize);
	document.addEventListener('wc_fragments_refreshed', persianize);

	/* مرتب‌سازی خودکار */
	$$('.shop-toolbar select, .woocommerce-ordering select').forEach(function (sel) {
		sel.addEventListener('change', function () { this.form.submit(); });
	});

	/* کلیک‌های امن روی لینک‌های تله */
	$$('a[href="#"]').forEach(function (a) {
		a.addEventListener('click', function (e) { e.preventDefault(); });
	});

	persianize();
	document.addEventListener('DOMContentLoaded', persianize);
})();
