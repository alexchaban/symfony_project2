var aim;

aim = {
	config: {
		vars: {
			toastrStatusList: [ 'warning', 'success', 'error' ],
			selectedPostType: 'note',
			dashboardPostCommentsLoaded: [],
			dashboardNotificationsStatus: true,
		}
	},

	getAjaxPostResponse: function(data) {
		if (jQuery.isPlainObject(data) && jQuery.inArray(data.status, this.config.vars.toastrStatusList) != -1) {
			toastr[data.status]((typeof data.msg !== void 0) ? data.msg : 'Undefined message.');
			return true;
		} else {
			toastr.warning('Oops. Something went wrong.');
			return false;
		}
	},

	refreshPostForm: function(form) {
		// wtf???
		// form = (void 0 !== form) ? form : void 0;
		if (void 0 !== form) {
			$(form).trigger('reset');
		} else {
			var forms, _i, _v;
			forms = $('.dashboardPostSubmit');
			$.each(forms, function(_i, _v) {
				$(_v).trigger('reset');
			})
		}
	},

	getAjaxUserPosts: function(el) {
		var last_post_id, aim, type;
		aim = (void 0 !== $(el).data('post-aim')) ? $(el).data('post-aim') : 'home';
		type = (void 0 !== $(el).data('post-type')) ? $(el).data('post-type') : 'all';
		filter = (void 0 !== $(el).data('filter')) ? true : false;

		if (aim != 'home') {
			last_post_id = $('#'+type+'s').find('.media-list .article').last().data('id');
		} else {
			last_post_id = $('.media-list .article').last().data('id');
		}
		
		var post_data = { lastId : last_post_id };
		if (filter)
		{
			jQuery.each($("#form_filters").serializeArray(), function(i, field){
				post_data[field.name] = field.value;
			})
		}
		
		$.ajax({
			type: 'POST',
			url: Routing.generate('main_dashboard_more_posts', {
				aim: aim,
				type: type
			}),
			data: post_data
		}).done(function(data) {
			var _i, _v;
			$.each(data, function(_i, _v) {
				var html, last_article;
				html = new EJS({url: article_template}).render({data: _v, uid: uid});
				if (aim != 'home') {
					if (filter)
						last_article = $('#filters').find('.media.article').last();
					else
						last_article = $('#'+type+'s').find('.media.article').last();
				} else {
					last_article = $('.media.article').last();
				}

				last_article.after(html);
			});
		});
	},
	
	getAjaxFilteredPosts: function(el) {
		var aim = $("#form_filters").data('post-aim');
		var post_data = {};
		jQuery.each($("#form_filters").serializeArray(), function(i, field){
			post_data[field.name] = field.value;
		})
	
		
		$.ajax({
			type: 'POST',
			url: Routing.generate('main_dashboard_more_posts', {
				aim: aim,
				type: "all"
			}),
			data: post_data
		}).done(function(data) {
			$('#filters').find('.media-list').html("");
			$(".tab-pane").removeClass("in").removeClass("active")
			$("#filters").addClass("in").addClass("active")
			var _i, _v;
			$.each(data, function(_i, _v) {
				var html, last_article;
				html = new EJS({url: article_template}).render({data: _v, uid: uid});
				$('#filters').find('.media-list').append(html);
			});
			if ($('#filters').find('.media-list .media').length >= 5)
			{
				$('#filters .view-all').removeClass("hidden");
			}
			else
				$('#filters .view-all').addClass("hidden");
				
		});
	},

	getAjaxUserNotifications: function(el, limit, checkDataLength) {
		limit = (void 0 !== limit) ? limit : 5;
		checkDataLength = (void 0 !== checkDataLength) ? checkDataLength : true;

		var last_notification_id;
		last_notification_id = $('.widget.notifications').find('.media').last().data('id');

		$.ajax({
			type: 'POST',
			url: Routing.generate('main_get_notifications', {
				lastId: last_notification_id,
				limit: limit
			}),
		}).done(function(data) {
			var _i, _v;
			$.each(data, function(_i, _v) {
				var html;
				html = new EJS({url: notification_template}).render({data: _v});
				$('.widget.notifications').find('.media').last().after(html);
			});

			if (checkDataLength && data.length < 5 || data.length == 0) {
				aim.config.vars.dashboardNotificationsStatus = false;
				$('.view-all.notifications').remove();
			}
		});
	},

	ajaxPostFavorite: function(el) {
		var id;
		id = $(el).data('id');
		$.ajax({
			type: 'POST',
			url: Routing.generate('main_favorite_post', {
				postId: id
			}),
		}).done(function(data) {
			var response;
			response = aim.getAjaxPostResponse(data);

			console.log(data);

			if (response) {
				var fav_class;
				fav_class = 'favorite-ok';
				if (!data.removed) {
					$(el).addClass(fav_class);
					//$(el).find('span').removeClass('fa-star-o').addClass('fa-star');
				} else {
					$(el).removeClass(fav_class);
					//$(el).find('span').removeClass('fa-star').addClass('fa-star-o');
				}
			}
		});
	},

	removeUserNotification: function(el) {
		var notification_id;
		notification_id = $(el).parents('.media').data('id');

		$.ajax({
			type: 'POST',
			url: Routing.generate('main_delete_notification', {
				notificationId: notification_id
			}),
		}).done(function(data) {
			var response;
			response = aim.getAjaxPostResponse(data);
			if (response) {
				var notification;
				notification = $('.widget.notifications').find('.media[data-id="' + notification_id + '"]');
				if (notification.length > 0) {
					$(notification).remove();
				}
				if (true === aim.config.vars.dashboardNotificationsStatus) {
					aim.getAjaxUserNotifications(void 0, 1, false);
				}
			}
		});
	},


	dashboardPostSubmit: function(form, event) {
		event.preventDefault();
		aim.validatePostFrom(form);
	},

	dashboardPostViewComments: function(el, e) {
		e.preventDefault();
		var postId, commentSection;
		postId = $(el).data('post');
		commentSection = $('.comments[data-post="' + postId + '"]');
		commentSection.toggleClass('active');
		$(el).toggleClass('active');

		aim.dashboardPostGetComments(postId, commentSection);

		return false;
	},

	dashboardPostSaveComment: function(el, e) {
		e.preventDefault();
		var postId, form;
		form = $(el).closest('.media.article');
		postId = form.data('id');

		if (postId) {
			var comment_input;
			comment_input = form.find('textarea');

			$.ajax({
				type: 'POST',
				url: Routing.generate('main_user_save_post_comment', { postId: postId }),
				data: { content: comment_input.val() }
			}).done(function(data) {
				var response;
				response = aim.getAjaxPostResponse(data);
				if (response) {
					var commentSectionPost;
					commentSectionPost = form.find('.leave-comment')
					comment_input.val('');
					html = new EJS({url: comment_template}).render({data: data.comment});
					$(commentSectionPost).before(html);
				}
			});
		} else {
			console.error('Missing id param');
		}
	},

	dashboardPostGetComments: function(postId, commentSection) {
		var comments;
		if (void 0 != postId && jQuery.inArray(postId, aim.config.vars.dashboardPostCommentsLoaded) == -1) {
			$.ajax({
				type: 'POST',
				url: Routing.generate('main_user_get_post_comments', { 'postId': postId }),
			}).done(function(data) {
				var commentSectionPost;
				aim.config.vars.dashboardPostCommentsLoaded.push(postId);

				commentSectionPost = $(commentSection).find('.leave-comment');

				if (data.comments_count > 0) {
					var html, _i, _v;
					$.each(data.comments, function(_i, _v) {
						html = new EJS({url: comment_template}).render({data: _v});
						$(commentSectionPost).before(html);
					});
				}
			});
		}

		return comments;
	},

	validatePostFrom: function(form) {
		var data;
		//data = $(form).serialize();

		data = new FormData($(form)[0]);

		//console.log(data);
		//return false;

		$.ajax({
			type: $(form).attr('method'),
			url: $(form).attr('action'),
			data: data,
			processData: false,
			contentType: false,
		}).done(function(data) {
			if (data) {
				var response;
				response = aim.getAjaxPostResponse(data);
				if (response) {
					aim.refreshPostForm(form);

					if (($('#dashboardPostReply').data('bs.modal') || {}).isShown) {
						$('#dashboardPostReply').modal('hide');
					} else {
						createPost.hideFrom(form.closest('.message-box'));
					}

					if (data.post) {
						var html, post_type, has_posts;
						html = new EJS({url: article_template}).render({data: data.post, uid: uid});
						post_type = data.post.type + 's';
						has_posts = $('main.notifications div#' + post_type).find('.no-posts');
						$('main.notifications div#' + post_type).find('ul.media-list').prepend(html);

						if (has_posts.length > 0) {
							$(has_posts).remove();
						}
					}
				}

				console.log(data);
			}
		});
	},

	checkFileInputs: function() {
		var file_inputs, _i, _v;
		file_inputs = $('.media-post-file');
		$.each(file_inputs, function(_i, _v) {
			if ($(_v).get(0).files.length !== 0) {
				$(_v).parent().addClass('active');
			} else {
				if ($(_v).parent().hasClass('active')) {
					$(_v).parent().removeClass('active');
				}
			}
		});
	},

	emptyFormFields: function(el) {
		var form;
		form = $(el).find('form');
		form[0].reset();
		$(form).find('.media-post-file').val("").trigger('change');
	},

	loadFormInfo: function(e, el) {
		if ('show' === e.type) {
			console.log('dsa');
		}
	},

	showSearchBox: function() {
		$(document).on('click', '.search_input', function(event) {
			$(".search").addClass("open");
		});
		
		 $(document).bind('click', function(e) {
			var $clicked = $(e.target);
			if (! $clicked.parents().hasClass("search"))
				$(".search").removeClass("open");
		});
	
	}
}

$(function() {
	aim.checkFileInputs();

	$(document).on('hide.bs.modal, show.bs.modal', '#dashboardPostReply', function(event) {
		aim.emptyFormFields(this);
		aim.loadFormInfo(event, this);
	});

	$(document).on('change', '.media-post-file', function() {
		aim.checkFileInputs();
	});

	$(document).on('submit', '.dashboardPostSubmit', function(event) {
		aim.dashboardPostSubmit(this, event);
	});

	$('#dashboardPostReply').on('show.bs.modal', function(event) {
		var aim = $(event.relatedTarget).data('aim');
		var user_id = $(event.relatedTarget).data('userId');
		$(event.currentTarget).find('input[name="form[aim]"]').val(aim);
		$(event.currentTarget).find('input[name="form[userId]"]').val(user_id);
	});

	$(document).on('click', '.view-comments', function(event) {
		aim.dashboardPostViewComments(this, event);
	});

	$(document).on('click', '.view-all.posts', function() {
		aim.getAjaxUserPosts(this);
	});

	$(document).on('change', '#form_filters input,#form_filters select', function() {
		aim.getAjaxFilteredPosts(this);
	});

	$(document).on('click', '.favorite-btn', function() {
		aim.ajaxPostFavorite(this);
	});

	$(document).on('click', '.view-all.notifications', function() {
		aim.getAjaxUserNotifications(this);
	});

	$(document).on('click', '.remove-notification', function() {
		aim.removeUserNotification(this);
	});

	$(document).on('click', '.save-comment', function(event) {
		aim.dashboardPostSaveComment(this, event);
	});

	$(document).ajaxStart(function() {
		$('.view-all').addClass('activated');
	}).ajaxStop(function() {
		$('.view-all').removeClass('activated');
	});

	$('main .nav-tabs a').on('show.bs.tab', function(){
		aim.config.vars.selectedPostType = $(this).data('type');
	});
	
	aim.showSearchBox();
	
	
});
