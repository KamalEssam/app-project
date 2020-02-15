$(document).ready(function () {
  var navListItems = $('div.setup-panel div a'),
          allWells = $('.setup-content'),
          allNextBtn = $('.nextBtn');

  allWells.hide();

  navListItems.click(function (e) {
      e.preventDefault();
      var $target = $($(this).attr('href')),
              $item = $(this);

      if (!$item.hasClass('disabled')) {
          navListItems.removeClass('btn-primary').addClass('btn-default');
          $item.addClass('btn-primary');
          allWells.hide();
          $target.show();
          $target.find('input:eq(0)').focus();
      }
  });

  allNextBtn.click(function(e){
      var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
          curInputs = curStep.find("textarea, input[type='text'],input[type='date'], input[type='url'], input[type='email'], select, input[type='number'], input[type='file'], .select2-search__field"),
          isValid = true;

      $(".form-group").removeClass("has-error");

      for(var i=0; i<curInputs.length; i++){
          if (!curInputs[i].validity.valid){
              isValid = false;
              e.preventDefault();
              $(curInputs[i]).closest(".form-group").addClass("has-error");
          }
      }

      if (isValid){
          nextStepWizard.removeAttr('disabled').trigger('click');
          if($(this).hasClass('submit')){
              $('#steps-form').submit();
              $('#job-form').submit();
          }
      }
  });

  $('div.setup-panel div a.btn-primary').trigger('click');

  $('#step-1').css('display', 'block');


  $(document).on('click', '.next', function (e) {
      $('.setup-content').fadeOut(10);
      var step = $('a.btn-primary').data('step');

      if(step != 6){

          $('a[href="#step-' + step + '"]').removeClass('btn-primary');
          step ++;
          $('a[href="#step-' + step + '"]').addClass('btn-primary');
          $('#step-' + step).fadeIn(500);
      } else {
          $('.setup-content').fadeOut(10);
          $('a[href="#step-6"]') .addClass('btn-primary');
          $('#step-6').fadeIn(500);
      }



  });
});