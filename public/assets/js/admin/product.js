$(document).ready(function(){
	$('#choose-country').on('change', function(event){
		if($(this).val() != ""){
		   $('.country-settings .label-info').text('Check/uncheck product to change settings');
	    }
		/*$('.settings-boxes').html($(this).val());*/
		$.ajax({
			type :  'POST',
			url  : baseURL + '/admin/products/settings/countries',
			data : {_token : token, country_id : $(this).val()}
		}).done(function(data){
			var products = data['products'];
			var countryProducts = data['countryProducts'];

			var stringResult = "";
			checked = [];

			for(i=0; i<products.length; i++){
				 stringResult += '<div class="col-md-4">'  +
					   	                      '<label class="ace-check">' + 
							                     '<input name="form-field-checkbox" type="checkbox" class="ace check-box box'+ products[i].id +'" data-id="' + products[i].id + '">' +
							                     '<span class="lbl"> ' +  products[i].english_product + '</span>' +
						                      '</label>' +
					                        '</div>';

                 $('.edit-setting-area').fadeIn(100);
			     $('.settings-boxes').html(stringResult);
                 
                 
			     for(j=0; j<countryProducts.length; j++){
			     	if(countryProducts[j].id == products[i].id ){
			     		checked.push(countryProducts[j].id);
			     	}

			     }
			}

			for(i=0; i<checked.length; i++){
				$('.box' + checked[i]).attr('checked', 'checked');
			}
		});
	});






	$(document).on('click', '#save-country-products', function(){
		var countryID = $('#choose-country').val();
		checked = [];
		$('.settings-boxes').find('.check-box').each(function(event){
			if($(this).is(':checked')){
				checked.push($(this).data('id'));
			}
		});

		var stringData = checked.join(',');
		console.log(stringData);
		$.ajax({
			type : 'POST',
			url  : baseURL + '/admin/products/settings/update',
			data : { _token : token, countryID: countryID, data : stringData}
		}).done(function(data){
			$('.flash-msg').fadeIn(500).text('Products updated successfully.').delay(3000).fadeOut(500);
			/*console.log(JSON.stringify(data));*/
		});
	});
});