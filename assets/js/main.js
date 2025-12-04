$(document).ready(function(){
    const modal = $('.modal');
    const overlay = $('#logoutModal');

    $('#loginButton').on('click', function(e){
        e.preventDefault();
        modal.addClass('show');
    });
            
    overlay.on('click', function(){
        modal.removeClass('show');
    });
});
