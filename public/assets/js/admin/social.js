$(document).ready(function(){
	// dlete social link /***************************/
	valid  = false;
	ID = null;
	$(document).on('click', '.delete_soc', function(event){
		event.preventDefault();
		ID = $(event.target).data('id');
		$('#modal-confirm').modal();
		$('.modal-title').text('Are you you want to delete this social ?');
	});

	$('.confirm-delete').click(function(event){
		event.preventDefault();
		$.ajax({
			type : 'DELETE',
			url  : baseURL + '/admin/social/'+ ID,
			data : {_token : token , id : ID},
			success : function(data){
				$('#modal-confirm').modal('hide');
				$("#social"+ID).fadeOut(100, function(){
					$(this).remove();
				});
				if(data['count'] == 0){
				  $('.socials').html('<p class="empty">There are no socials to show</p>');
			    }
			}
		});
	});
	/************************************************/

    // Add social link /****************************/
    $(document).on('click', '#btn-add', function(){
		$('#modal-add-social').modal();
	});

	$('.pick-icon').on('click', function(event){
		$(this).addClass('active').siblings().removeClass('active');
		$('#icon_id').val($(event.target).data('id'));
	});

	$(document).on('click', '#btn-add-social', function(){
		valid = true;
		// start validation
		$('.required').css('border-color' , '#ccc');
		if($('#name').val() == "" || $('#icon_id').val() == "" || $('#link').val() == ""){
			$('.required').filter(function(index) {
		      return !this.value;
			}).css("border-color" , "red");	 
			valid = false;
		}

		if(valid){
			$.ajax({
				type : 'POST',
				URL  : baseURL + '/admin/social',
				data : {_token : token, name : $('#name').val(), icon : $('#icon_id').val(), link : $('#link').val()}
			}).done(function(data){
				$('.records').append('<tr id="social' + data['social'].id + '">' +
										'<td>' + data['social'].name + '</td>' +
										'<td><i class="fa fa-' + data['class'] + ' fa-lg" data-id="' + data['social'].id + '"></i></td>' +
										'<td><a href="' + data['social'].url + '" target="_blank" >' + data['social'].url + '</a></td>' +
										'<td>' +
											'<div class="btn-group control-social">' +
												'<i class="ace-icon fa fa-pencil bigger-120 edit-soc" style="margin-right:3px"></i>' +
												'<i class="ace-icon fa fa-trash-o bigger-120 delete_soc" data-id="' + data['social'].id + '"></i>' +
								
											'</div>' +
										'</td>' +
									 '</tr>');
				$('#modal-add-social').modal('hide');
			});
		}
	});
	/***********************************************/

	// Edit social link /****************************/

	$(document).on('click', '.edit-soc', function(){
		$('#modal-edit-social').modal();
		$.ajax({
			type : 'POST',
			url  : baseURL + '/admin/social/' + $(this).data('id') + '/edit',
			data : {_token : token, id : $(this).data('id')},
			cache: false,
		}).done(function(data){
			$('#edit-name').val(data['social'].name);
			$('#edit-link').val(data['social'].url);
			$('.icon-box').find('i').each(function(){
			    if($(this).data('id') == data['social'].icon_id){
			    	$(this).addClass('active').siblings().removeClass('active');
			    	$('#edit-icon_id').val(data['social'].icon_id);
			    }
			});
		});
	});

	$('.edit-icon').on('click', function(event){
		$(this).addClass('active').siblings().removeClass('active');
		$('#edit-icon_id').val($(event.target).data('id'));
	});

	$(document).on('click', '#btn-edit-social', function(){
		valid = true;
		// start validation
		$('.required').css('border-color' , '#ccc');
		if($('#edit-name').val() == "" || $('#edit-icon_id').val() == "" || $('#edit-link').val() == ""){
			$('.required').filter(function(index) {
		      return !this.value;
			}).css("border-color" , "red");	 
			valid = false;
		}
	});
});