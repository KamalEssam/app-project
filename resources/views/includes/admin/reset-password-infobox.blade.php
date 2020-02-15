@if(count($errors) > 0) 
     @foreach($errors->all() as $error)
           @if($errors->has('email') || $erros->has('password')){
              <span class="error-message">Please check required fields</span>
           @else
              <span class="error-message">{{ $error }}</span> 
           @endif
     @endforeach
 @endif 

@if(Session::has('status'))
   <span class="success-message">{{ Session::get('status') }}</span> 
@endif
