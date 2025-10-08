; (function ($) {
	$(function () {
		// Init all main weather shortcode blocks
		$('.splw-main-wrapper').each(function () {
			const $mainWrapper = $(this);
			const wrapperId = $mainWrapper.attr('id');
			const shortcodeId = $mainWrapper.data('shortcode-id');

			initPreloader($mainWrapper);
			initAjaxReload($mainWrapper, shortcodeId);
			initForecastSelect($mainWrapper);
			initForecastTabs($mainWrapper);
		});

		// ======================
		// Preloader Removal
		// ======================
		function initPreloader($wrapper) {
			const $preloaderWrapper = $wrapper.find('.splw-lite-wrapper.lw-preloader-wrapper');
			const $preloader = $wrapper.find('.lw-preloader');

			if ($preloaderWrapper.length && $preloader.length) {
				$preloader.animate({ opacity: 0 }, 600, function () {
					$(this).remove();
				});
			}
		}

		// ======================
		// AJAX Reload if Skip Cache Enabled
		// ======================
		function initAjaxReload($wrapper, shortcodeId) {
			if (
				!$wrapper.hasClass('splw-ajax-loaded') &&
				typeof splw_ajax_object !== 'undefined' &&
				splw_ajax_object.splw_skip_cache === '1' &&
				shortcodeId
			) {
				const data = {
					splw_nonce: splw_ajax_object.splw_nonce,
					action: 'splw_ajax_location_weather',
					id: shortcodeId,
				};

				$.post(splw_ajax_object.ajax_url, data, function (response) {
					if (response) {
						$wrapper.replaceWith(response);
						// Rebind events on new content
						const $newWrapper = $('#' + $wrapper.attr('id'));
						initForecastSelect($newWrapper);
						initForecastTabs($newWrapper);
						$newWrapper.addClass('splw-ajax-loaded');
					}
				});
			}
		}

		// ======================
		// Forecast Select Dropdown
		// ======================
		function initForecastSelect($wrapper) {
			const $select = $wrapper.find('#forecast-select');

			if (!$select.length) return;

			const map = {
				temp: '.temp-min-mex',
				wind: '.temp-wind',
				humidity: '.temp-humidity',
				pressure: '.temp-pressure',
				precipitation: '.temp-precipitation',
				rainchance: '.temp-rainchance',
				snow: '.temp-snow',
			};

			const $forecast = $wrapper.find('.splw-adv-forecast-days .splw-forecast');

			$select.on('change', function () {
				const value = $(this).val();
				if (map[value]) {
					$forecast.find(map[value])
						.addClass('active')
						.siblings()
						.removeClass('active');
				}
			});
		}

		// ======================
		// Forecast Tab Click Handling
		// ======================
		function initForecastTabs($wrapper) {
			$wrapper.find('.splw-adv-forecast-days').each(function () {
				const $container = $(this);
				const $tabs = $container.find('[data-tab-target]');
				const $tabContents = $container.find('[data-tab-content]');

				$tabs.off('click').on('click', function () {
					const $tab = $(this);
					const targetSelector = $tab.data('tabTarget');

					if (!targetSelector) return;

					$tab.addClass('active').siblings().removeClass('active');
					$tabContents.removeClass('active');
					$tabContents.filter(targetSelector).addClass('active');
				});
			});
		}
	});
})(jQuery);
