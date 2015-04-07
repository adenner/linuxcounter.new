
var $loading = $('#loadingDiv').hide();
$(document)
    .ajaxStart(function () {
        $loading.show();
    })
    .ajaxStop(function () {
        $loading.hide();
    });

$(document).ready(function() {

    if ($('#myCarousel').length >= 1) {
        $('.carousel').carousel({
            interval: 1000 * 10
        });

        $("#myCarousel").swiperight(function () {
            $("#myCarousel").carousel('prev');
        });
        $("#myCarousel").swipeleft(function () {
            $("#myCarousel").carousel('next');
        });
    }

    $( "#question1" ).click(function() {
        $( "#answer1" ).slideToggle( "slow" );
    });
    $( "#question2" ).click(function() {
        $( "#answer2" ).slideToggle( "slow" );
    });
    $( "#question3" ).click(function() {
        $( "#answer3" ).slideToggle( "slow" );
    });
    $( "#question4" ).click(function() {
        $( "#answer4" ).slideToggle( "slow" );
    });
    $( "#question5" ).click(function() {
        $( "#answer5" ).slideToggle( "slow" );
    });
    $( "#question6" ).click(function() {
        $( "#answer6" ).slideToggle( "slow" );
    });

    $(":checkbox").bootstrapSwitch();

    $("#add_city_button").click(function(){
        $("#myModal").modal('show');
    });

    $("#arrt").on("click", "#addcity_save", function(e){
        /* e.preventDefault(); */
        /* $("#myModal").modal('hide'); */
    });





















});

