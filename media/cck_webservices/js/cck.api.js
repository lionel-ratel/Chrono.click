/* Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved. */
(function ($){
	JCck.WebService = {
		api_url: "",
		call: function(resource) {
			var parts = resource.split('-');

			var api_data = [];
			var api_url = this.api_url+parts[2];
			var filters = [];

			$('['+resource+']').each(function(i) {
				var k = $(this).attr('name');
				var t = $(this).attr(resource);
				var v = $(this).val();

				if (v != '') {
					if (t == "filter") {
						filters.push(k+'='+v);
					} else {
						api_data.push(k+"="+v);
					}
				}
			});

			if (filters.length) {
				api_data.unshift('filter=('+filters.join('|')+')');
			}
			if (api_data.length) {
				api_data = api_data.join('&');
			} else {
				api_data = '';
			}

			JCck.WebService.request(resource, api_url, api_data);
		},
		request: function(resource, api_url, api_data) {
			var api_data = api_data || '';
			$.ajax({
				cache: false,
				data: api_data,
				type: 'GET',
				url: api_url,
				beforeSend:function(jqXHR){
					var auth = Joomla.getOptions('cck_ws_auth');

					if (auth) {
						jqXHR.setRequestHeader('Authorization',auth);
					}
				},
				success: function(response,textStatus,jqXHR){
					$('.'+resource+' .request pre').html(api_url+(api_data ? '?' : '')+api_data);
					$('.'+resource+' .response pre').html(JSON.stringify(response, null, 2));
					$('.'+resource+' .call').slideDown();
					$('.'+resource+' .api-reset').show();

					console.log(response);

					if (response.links) {
						$.each(response.links, function(i,elem) {
							var $el = $('.'+resource+' .api-go[data-page="'+elem.rel+'"]');

							$el.attr('data-url', elem.href);

							if (elem.href) {
								$el.prop('disabled',false);
							} else {
								$el.prop('disabled',true);
							}
						});
					}
				}
			});
		},
		reset: function(resource) {
			$('.'+resource+' .call').slideUp();
			$('.'+resource+' .api-go').prop('disabled',true);
			$('['+resource+']').val('');
		},
		setInstance: function(data) {
			this.api_url = JCck.Core.sourceURI+data.api_url;
		}
	};

	$(document).ready(function() {
		$("#api-resources").on("click", ".api-call", function() {
			JCck.WebService.call($(this).attr('data-resource'));
		});
		$("#api-resources").on("click", ".api-go", function() {
			var api_url = $(this).attr('data-url');

			if (api_url) {
				JCck.WebService.request($(this).attr('data-resource'),api_url);
			}
		});
		$("#api-resources").on("click", ".api-reset", function() {
			JCck.WebService.reset($(this).attr('data-resource'));
			$(this).hide();
		});
	});
})(jQuery);