
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

    $( "#apidoc1" ).click(function() {
        $( "#apidoc_desc1" ).slideToggle( "slow" );
    });
    $( "#apidoc2" ).click(function() {
        $( "#apidoc_desc2" ).slideToggle( "slow" );
    });
    $( "#apidoc3" ).click(function() {
        $( "#apidoc_desc3" ).slideToggle( "slow" );
    });
    $( "#apidoc4" ).click(function() {
        $( "#apidoc_desc4" ).slideToggle( "slow" );
    });
    $( "#apidoc5" ).click(function() {
        $( "#apidoc_desc5" ).slideToggle( "slow" );
    });
    $( "#apidoc6" ).click(function() {
        $( "#apidoc_desc6" ).slideToggle( "slow" );
    });
    $( "#apidoc7" ).click(function() {
        $( "#apidoc_desc7" ).slideToggle( "slow" );
    });
    $( "#apidoc8" ).click(function() {
        $( "#apidoc_desc8" ).slideToggle( "slow" );
    });
    $( "#apidoc9" ).click(function() {
        $( "#apidoc_desc9" ).slideToggle( "slow" );
    });
    $( "#apidoc10" ).click(function() {
        $( "#apidoc_desc10" ).slideToggle( "slow" );
    });
    $( "#apidoc11" ).click(function() {
        $( "#apidoc_desc11" ).slideToggle( "slow" );
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

