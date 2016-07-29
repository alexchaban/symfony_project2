var menu, createPost, trackProgress;

menu = {
	menuColor: function(){
		var activeMenuColor = $('#header-menu .active').data('color') || '#88858d';
		if (activeMenuColor){
			$('#header-menu .active').addClass('active-bdr active-clr');
			$('.active-bdr').css('borderColor', activeMenuColor);
			$('.active-clr, .active-clr *').css('color', activeMenuColor);
		}
	}
}
createPost = {
	showFrom: function(form){
		var form_type;
		form_type = $(form).data('type');
		$('.message-box[data-type="' + form_type + '"]').show(0);
		$('.new-post[data-type="' + form_type + '"]').hide(0);
	},
	hideFrom: function(form){
		var form_type;
		form_type = $(form).data('type');
		$('.message-box[data-type="' + form_type + '"]').hide(0);
		$('.new-post[data-type="' + form_type + '"]').show(0);
	},
	init: function(){
		$('.new-post').on('click', function(){
			var form;
			form = $(this);
			createPost.showFrom(form);
		});
		$('.message-box .cancel-btn').on('click', function(){
			var form;
			form = $(this).closest('.message-box');
			createPost.hideFrom(form);
		});
	}
}

trackProgress = {
	globCurColor: '',
	activeLink: function(){
		var curColor = $('#track-progress ul.nav li.active').data('color');
		var curImage = $('#track-progress ul.nav li.active a').attr('href');
		while(curImage.charAt(0) === '#')
			curImage = curImage.substr(1);
		var curImage = 'url(/img/modal-bgs/'+ curImage +'.jpg)';
		var parent = $('#track-progress .modal-body');
		trackProgress.globCurColor = curColor;
		parent.css('background-image', curImage);
		$('#track-progress ul.nav li.active a').addClass('active-bg');
		$('.active-bg').css('background-color', curColor);
		$('.active-cl').css('color', curColor);
	},
	activeItem: function(){
		var item = $('#track-progress ul.do-now-items li');
		item.on('click', function(){				
			$(this).toggleClass('active');
			if($(this).hasClass('active')){
				$(this).css('color', trackProgress.globCurColor);
			}
			else{
				$(this).css('color', '#fff');
			}
		});
	},
	activeItemLoad: function(){
		var item = $('#track-progress ul.do-now-items li.active');
		item.css('color', trackProgress.globCurColor);
	},
	init: function(){
		if (document.querySelector("#track-progress")) {			
			trackProgress.activeLink();
			trackProgress.activeItem();
			trackProgress.activeItemLoad();

			$('#track-progress a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				e.target // newly activated tab
				e.relatedTarget // previous active tab
				$('#track-progress ul.nav li a.active-bg').css('background-color','').removeClass('active-bg');
				$(this).addClass('active-bg');
				trackProgress.activeLink();
				trackProgress.activeItemLoad();
			});
		}
	}
}

$(document).on('ready', function(){
	menu.menuColor();
	createPost.init();
	trackProgress.init();
	var options = {
		url: function(phrase) {
			return Routing.generate('main_user_friends');
		},
		getValue: function(element) {
			return element.fullname;
		},
		ajaxSettings: {
			dataType: "json",
			method: "POST",
			data: {
				dataType: "json"
			}
		},
		preparePostData: function(data) {
			data.phrase = $("#plate").val();
			return data;
		},
		requestDelay: 400,
		list: {
			onChooseEvent: function() {
				var selected = $("#plate").getSelectedItemData().id;
				$("#plate_id").val(selected).trigger("change");
			},

		}

	};

	$('.selectpicker').selectpicker();

	$("#plate").easyAutocomplete(options);

	$('#datetimepicker2').datetimepicker({
		timepicker: false,
		format: 'd.m.Y'
	});

});

