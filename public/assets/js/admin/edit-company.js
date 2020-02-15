$(document).ready(function(){

	console.log(baseURL);
	$('#edit-english-name').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-english-name').fadeIn(10);
	});

	$('#edit-arabic-name').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-arabic-name').fadeIn(10);
	});


	$('#edit-email').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-email').fadeIn(10);
	});

	$('#edit-mobile').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-mobile').fadeIn(10);
	});

	$('#edit-phone').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-phone').fadeIn(10);
	});

	$('#edit-fax').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-fax').fadeIn(10);
	});

	$('#edit-english-address').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-english-address').fadeIn(10);
	});

	$('#edit-arabic-address').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-arabic-address').fadeIn(10);
	});

	$('#edit-map').on('click', function(event){
		event.preventDefault();
		$(this).hide();
        $('.edit-map').fadeIn(10);
	});


	$('#save-english-name').on('click', function(event){
		event.preventDefault(); 
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, english_name : $('#input-edit-english-name').val()}
		}).done(function(data){
			$('.edit-english-name').fadeOut(10);
			$('#edit-english-name').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});

	$('#save-arabic-name').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, arabic_name : $('#input-edit-arabic-name').val()}
		}).done(function(data){
			$('.edit-arabic-name').fadeOut(10);
			$('#edit-arabic-name').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});


	$('#save-email').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, email : $('#input-edit-email').val()}
		}).done(function(data){
			$('.edit-email').fadeOut(10);
			$('#edit-email').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});

	$('#save-mobile').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, mobile : $('#input-edit-mobile').val()}
		}).done(function(data){
			$('.edit-mobile').fadeOut(10);
			$('#edit-mobile').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});


	$('#save-phone').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, phone : $('#input-edit-phone').val()}
		}).done(function(data){
			$('.edit-phone').fadeOut(10);
			$('#edit-phone').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});

	$('#save-fax').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, fax : $('#input-edit-fax').val()}
		}).done(function(data){
			$('.edit-fax').fadeOut(10);
			$('#edit-fax').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});

	$('#save-english-address').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, english_address : $('#input-edit-english-address').val()}
		}).done(function(data){
			$('.edit-english-address').fadeOut(10);
			$('#edit-english-address').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});

	$('#save-arabic-address').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, arabic_address : $('#input-edit-arabic-address').val()}
		}).done(function(data){
			$('.edit-arabic-address').fadeOut(10);
			$('#edit-arabic-address').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});


	$('#save-map').on('click', function(event){
		event.preventDefault();
		$.ajax({
			type : 'PUT',
			url  : baseURL + '/admin/company/editCompanyInfo',
			data : {_token : token, map : $('#input-edit-map').val()}
		}).done(function(data){
			$('.edit-map').fadeOut(10);
			$('#edit-map').fadeIn(10);
			$('.map').fadeIn(10).html(data['updatedName']);
			/*console.log(JSON.stringify(data))*/
		}); 
	});



	$(document).on('click', '.btn-cancel, .fa-close', function(event){
		event.preventDefault();
		$(event.target).parent('form').fadeOut(10).prev('a').fadeIn(10);
		/*$(event.target).parent().*/
	});
    
    // Check il logo has a filer
	$('#logo').on('change', function(){
		$('.save-logo').css('display', 'inline-block');
	});

});