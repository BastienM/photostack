$( document ).ready(function() {

   $('.btn-delete').click(function(e){
       e.preventDefault();
       $('#delete').modal('show');
    });
});