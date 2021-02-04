jQuery.browser = {};
jQuery.browser.mozilla=/mozilla/.test(navigator.userAgent.toLowerCase())&&!/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.webkit=/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.opera=/opera/.test(navigator.userAgent.toLowerCase());
jQuery.browser.msie=/msie/.test(navigator.userAgent.toLowerCase());

(function($){
	if ( ! $) return;

	$.ajax_upload = function(button, options){
		button = $(button);
		if (button.size() != 1 ){
			console.error('You passed ', button.size(),' elements to ajax_upload at once');
			return false;
		}
		return new Ajax_upload(button, options);
	};

	var get_uid = function(){
		var uid = 0;
		return function(){
			return uid++;
		}
	}();

	var Ajax_upload = function(button, options){
		this.button = button;

		this.wrapper = null;
		this.form = null;
		this.input = null;
		this.iframe = null;

		this.disabled = false;
		this.submitting = false;

		this.settings = {
			action: 'upload.php',
			name: 'userfile',
			data: {},
			onSubmit: function(file, extension) {},
			onComplete: function(file, response) {},
			onSuccess: function(file){},
			onError: function(file, response){}
		};

		$.extend(this.settings, options);

		this.create_wrapper();
		this.create_input();

		if (jQuery.browser.msie){
			this.make_parent_opaque();
		}

		this.create_iframe();
	}

	Ajax_upload.prototype = {
		set_data : function(data){
			this.settings.data = data;
		},
		disable : function(){
			this.disabled = true;
			if ( ! this.submitting){
				this.input.attr('disabled', true);
			}
		},
		enable : function(){
			this.disabled = false;
			this.input.attr('disabled', false);
		},

		create_wrapper : function(){
			var button = this.button, wrapper;

			wrapper = this.wrapper = $('<div></div>')
				.insertAfter(button)
				.append(button);

			setTimeout(function(){
				wrapper.css({
					position: 'relative'
					,display: 'block'
					,overflow: 'hidden'

					,height: button.outerHeight(true)
					,width: button.outerWidth(true)
				});
			}, 1);

			var self = this;
			wrapper.mousemove(function(e){
				if (!self.input) {
					return;
				}

				self.input.css({
					top: e.pageY - wrapper.offset().top - 5 + 'px'
					,left: e.pageX - wrapper.offset().left - 170 + 'px'
				});
			});


		},

		create_input : function(){
			var self = this;

			this.input =
				$('<input type="file" />')
				.attr('name', this.settings.name)
				.css({
					'position' : 'absolute'
					,'margin': 0
					,'padding': 0
					,'width': '220px'
					,'heigth': '10px'
					,'opacity': 0
				})
				.change(function(){
					if ($(this).val() == ''){
						return;
					}
					self.submitting = true;
					self.submit();
					self.submitting = false;
				})
				.appendTo(this.wrapper)

				.hover(
					function(){self.button.addClass('hover');}
					,function(){self.button.removeClass('hover');}
				);

			if (this.disabled){
				this.input.attr('disabled', true);
			}

		},

		create_iframe : function(){
			var name = 'iframe_au' + get_uid();

			this.iframe =
				$('<iframe name="' + name + '"></iframe>')
				.css('display', 'none')
				.appendTo('body');
		},

		submit : function(){
			var self = this, settings = this.settings;
			var file = this.file_from_path(this.input.val());

			if (settings.onSubmit.call(this, file, this.get_ext(file)) === false){

				if (self.disabled){
					this.input.attr('disabled', true);
				}
				return;
			}

			this.create_form();
			this.input.appendTo(this.form);
			this.form.submit();

			this.input.remove(); this.input = null;
			this.form.remove();	this.form = null;

			this.submitting = false;

			this.create_input();

			var iframe = this.iframe;
			iframe.load(function(){
				var response = iframe.contents().find('body').html();

				settings.onComplete.call(self, file, response);
				if (response == 'success'){
					settings.onSuccess.call(self, file);
				} else {
					settings.onError.call(self, file, response);
				}

				setTimeout(function(){
					iframe.remove();
				}, 1);
			});

			this.create_iframe();
		},

		create_form : function(){

			this.form =
				$('<form method="post" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="102400" /></form>')
				.appendTo('body')
				.attr({
					"action" : this.settings.action
					,"target" : this.iframe.attr('name')
				});

			for (var i in this.settings.data){
				$('<input type="hidden" />')
					.appendTo(this.form)
					.attr({
						'name': i
						,'value': this.settings.data[i]
					});
			}
		},
		file_from_path : function(file){
			var i = file.lastIndexOf('\\');
			if (i !== -1 ){
				return file.slice(i+1);
			}
			return file;
		},
		get_ext : function(file){
			var i = file.lastIndexOf('.');

			if (i !== -1 ){
				return file.slice(i+1);
			}
			return '';
		},
		make_parent_opaque : function(){
			this.button.add(this.button.parents()).each(function(){
				var color = $(this).css('backgroundColor');
				var image = $(this).css('backgroundImage');

				if ( color != 'transparent' ||  image != 'none'){
					$(this).css('opacity', 1);
					return false;
				}
			});
		}

	};
})(jQuery);