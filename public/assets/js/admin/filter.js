$(document).ready(function(){
	$(document).on('change', '.filter-by-country', function(){
        country_id = $(this).val();
        dataCityString = "";
        dataProductString = "";
        $.ajax({
            type  : 'POST',
            url   : URL + '/admin/filter/bycountry',
            data  : {_token : token,  country_id : country_id }, 
        }).done(function(data){
        	var cities = JSON.parse(data['cities']);
        	var products = JSON.parse(data['products']);
        	for(i=0;i<cities.length;i++){
        		console.log(cities[i].id);
        		dataCityString += '<option value="' + cities[i].id +'">' + cities[i].english_name + '</option>';
        	}

        	for(i=0;i<products.length;i++){
        		console.log(products[i].id);
        		dataProductString += '<option value="' + products[i].id +'">' + products[i].english_product + '</option>';
        	}

        	$('.filtered-city').html('<option slected>City</option>' + dataCityString);
        	$('.filtered-products').html('<option slected>Product</option>' + dataProductString);
        });
	});

    $(document).on('change', '.filtered-products', function(){
        product_id = $(this).val();
        dataString = "";
        $.ajax({
            type  : 'POST',
            url   : URL + '/admin/filter/byproduct',
            data  : {_token : token,  product_id : product_id }, 
        }).done(function(data){
            var services = JSON.parse(data['services']);
            for(i=0;i<services.length;i++){
                console.log(services[i].id);
                dataString += '<option value="' + services[i].id +'">' + services[i].type + '</option>';
            }
            $('.filtered-services').html('<option slected>Service</option>' + dataString);
            console.log(JSON.stringify(data));
        });
    });


     $(document).on('change', '.filter-provider-by-country', function(){
        country_id = $(this).val();
        dataString = "";
        $.ajax({
            type  : 'POST',
            url   : URL + '/admin/filter/providers/bycountry',
            data  : {_token : token,  country_id : country_id }, 
        }).done(function(data){
            console.log(JSON.stringify(data));
            var providers = data;
            for(i=0;i<providers.length;i++){
                console.log(providers[i].id);
                dataString += '<option value="' + providers[i].id +'">' + providers[i].english_name + '</option>';
            }
            $('.providers').html('<option slected>Provider</option>' + dataString);
            console.log(JSON.stringify(data));
        });
    });

});


