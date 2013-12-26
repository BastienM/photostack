$( document ).ready(function() {

    var url = null;

   $('.btn-delete').click(function(e){
       e.preventDefault();

       url = $(this).attr('href');
       $('#delete').modal('show');
    });

    $('#confirm').click(function(e){
        $('#delete').modal('hide');
        window.location.replace("http://photostack.dev"+url);
    });

    $('#cancel').click(function(e){
        $('#delete').modal('hide');
    });
});