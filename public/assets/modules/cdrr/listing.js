$(function(){
    var base_url = $("#sources").data("baseurl");
    var cdrr_url = base_url + "/public/cdrr_core/listing";

    //Order Listing Datatable
    createTable("#cdrr_listing",cdrr_url,1,'desc');
});

