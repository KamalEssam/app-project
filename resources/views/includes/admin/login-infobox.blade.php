@if(count($errors) > 0) 
     @if($errors->has('email') || $errors->has('password'))
        <span class="error-message">Please check required fields.</span> 
     @endif
     @foreach($errors->all() as $error)
         @if($error == 'These credentials do not match our records.')
           <span class="error-message">{{ $error }}</span> 
         @endif
     @endforeach
 @endif       