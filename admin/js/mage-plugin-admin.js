jQuery(document).ready(function ($) {

    $(".delivery").on('click', function (e) {

        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: csms.ajaxurl,
            data: {
                'action': 'delivery_based_services',
            },
            dataType: "text",

            success: function (data) {
                $(".all_services_by_default").hide();
                $(".services_by_filter").html(data);
            },

        });

    });

    $(".priority").on('click', function (e) {

        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: csms.ajaxurl,
            data: {
                'action': 'priority_based_services',
            },
            dataType: "text",

            success: function (data) {
                $(".all_services_by_default").hide(1000);
                $(".services_by_filter").html(data);
            },

        });
    });

    //Dateformat
    $(".datepicker").datepicker({
        dateFormat: csms.date_format,
    });

    //===============================================

    // Service add by selected services

    //===============================================
    $(".service_price_add").on("click", function (e) {

        e.preventDefault();

        var $post_id = new Array();

        $post_id = $(".selected_services").val();

        var $last_count = $('.service_price_last_count');
        var $last_count_val = parseInt($last_count.val());

        $last_count_val++;

        $last_count.val($last_count_val);

        $.ajax({
            type: 'POST',
            url: csms.ajaxurl,
            data: {
                'action': 'service_pricing',
                'post_id': $post_id,
                'last_count_val': $last_count_val
            },
            dataType: "text",

            success: function (data) {

                var service_total_price = 0;

                $(".service_pricing_table table").append(data);

                $(".service_pricing_table").find('.service_qty').each(function () {

                    let unit = parseInt($(this).val());
                    let price = $(this).attr("data-price");

                    service_total_price += unit * price;

                    $(".services_total").html(service_total_price);
                    $(".service_total_input").val(service_total_price);

                    var $product_total = parseInt($(".product_total").html());
                    var $services_total = parseInt($(".services_total").html());
                    var $grand_total = $product_total + $services_total;

                    $(".grand_total").html($grand_total);
                    $(".grand_total_input").val($grand_total);


                    /*service_total_price = service_total_price + parseInt($(this).val());

                    $(".services_total").html(service_total_price);
                    $(".service_total_input").val(service_total_price);

                    var $product_total = parseInt($(".product_total").html());
                    var $services_total = parseInt($(".services_total").html());
                    var $grand_total = $product_total + $services_total;

                    $(".grand_total").html($grand_total);
                    $(".grand_total_input").val($grand_total);*/

                });

            },

        });
    });

    //===============================================

    // Service price specific row remove

    //===============================================
    $(".pricing_section").on("click", ".service_price_remove", function (e) {

        e.preventDefault();

        var $service_price = parseInt($(this).closest('tr').find('.service_price').val());
        var $get_service_total = parseInt($(".services_total").text());
        var $service_qty = parseInt($(this).closest('tr').find(".service_qty").val());

        var $set_service_total = $get_service_total - $service_price * $service_qty;

        $(".services_total").html($set_service_total);
        $(".service_total_input").val($set_service_total);


        var $product_total = parseInt($(".product_total").html());
        var $services_total = parseInt($(".services_total").html());
        var $grand_total = $product_total + $services_total;

        $(".grand_total").html($grand_total);
        $(".grand_total_input").val($grand_total);

        $(this).closest('tr').remove();
    });

    //===============================================

    // Multiply all services and product price for grand total.

    //===============================================
    $(".pricing_section").on("change", ".qty", function () {

        var services_total_price = 0;
        var product_total_price = 0;
        var grand_total = 0;

        $(".qty").each(function () {

            var $service_price = $(this).closest("tr").find(".service_price").val();
            var $product_price = $(this).closest("tr").find(".product_price").val();

            services_total_price = parseInt(services_total_price + ($(this).val() * $service_price || 0));
            product_total_price = parseInt(product_total_price + ($(this).val() * $product_price || 0));

            grand_total = services_total_price + product_total_price;

            $(".product_total").html(product_total_price);
            $(".services_total").html(services_total_price);
            $(".grand_total").html(grand_total);

            $(".product_total_input").val(product_total_price);
            $(".service_total_input").val(services_total_price);
            $(".grand_total_input").val(grand_total);

        });
    });


    /**
     *  Product Pricing Section
     */

    //===============================================

    // Product price add

    //===============================================
    $(".product_price_add").on("click", function (e) {

        e.preventDefault();

        var $post_id = new Array();

        $post_id = $(".selected_product").val();

        var $last_count = $('.product_price_last_count');
        var $last_count_val = parseInt($last_count.val());

        $last_count_val++;

        $last_count.val($last_count_val);

        $.ajax({
            type: 'POST',
            url: csms.ajaxurl,
            data: {
                'action': 'product_pricing',
                'post_id': $post_id,
                'last_count_val': $last_count_val
            },
            dataType: "text",

            success: function (data) {


                $(".product_pricing_table table").append(data);

                var product_total_price = 0;

                $(".product_pricing_table").find('.product_qty').each(function () {


                    let unit = parseInt($(this).val());
                    let price = $(this).attr("data-price");

                    product_total_price += unit * price;

                    $(".product_total").html(product_total_price);
                    $(".product_total_input").val(product_total_price);
                    $(".grand_total").html(product_total_price);


                   /* $product_total_price = $product_total_price + parseInt($(this).val());

                    $(".product_total").html($product_total_price);
                    $(".product_total_input").val($product_total_price);

                    var $product_total = parseInt($(".product_total").html());
                    var $services_total = parseInt($(".services_total").html());
                    var $grand_total = $product_total + $services_total;

                    $(".grand_total").html($grand_total);
                    $(".grand_total_input").val($grand_total);*/

                });
            },

        });
    });

    //===============================================

    // product price specific row remove

    //===============================================


    $(".pricing_section").on("click", ".product_price_remove", function (e) {
        e.preventDefault();

        var $product_price = parseInt($(this).closest('tr').find('.product_price').val());
        var $get_product_total = parseInt($(".product_total").text());
        var $product_qty = parseInt($(this).closest('tr').find(".product_qty").val());

        var $set_product_total = $get_product_total - $product_price * $product_qty;

        $(".product_total").html($set_product_total);
        $(".product_total_input").val($set_product_total);

        var $product_total = parseInt($(".product_total").html());
        var $services_total = parseInt($(".services_total").html());
        var $grand_total = $product_total + $services_total;

        $(".grand_total").html($grand_total);
        $(".grand_total_input").val($grand_total);

        $(this).closest('tr').remove();
    });

    $(".selected_product, .selected_services").select2();

    //===============================================

    // Check user availability

    //===============================================
    $("#mobile_number").on("blur", function () {

        var $mobile_number = $(this).val();

        $(".mobile_number").show();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: csms.ajaxurl,
            data: {
                'action': 'check_user_availability',
                'mobile_number': $mobile_number
            },
            success: function (data) {
                $("#customer_name").val(data['user_name']);
                $("#email").val(data['user_email']);
                $("#customer_address").val(data['user_address']);

                $(".mobile_number").hide(500);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".mobile_number").hide(500);
            }
        });

    });


});//end jquery initialization
