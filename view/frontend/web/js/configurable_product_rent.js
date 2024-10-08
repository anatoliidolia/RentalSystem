define([
    'jquery',
    'mage/url',
], function ($, urlBuilder) {
    'use strict';

    return function (config) {
        $('.rentbutton').on('click', function (e) {
            saveButtonClickHandler(e);
        });

        function saveButtonClickHandler(e) {
            e.preventDefault();

            var product_id = config.product_id;
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                alert('Please fill in both Start Date and End Date.');
                return;
            }

            $.ajax({
                type: 'POST',
                url: urlBuilder.build('rent/cart/add'),
                data: {
                    rent_product_id: product_id,
                    start_date: startDate,
                    end_date: endDate
                }
            }).done(function () {
                var successMessage = $('<div class="success-message">All is fine</div>');
                $('body').append(successMessage);
                $('.rentbutton').text('Added').attr('disabled', true);

            }).fail(function () {
                var errorMessage = $('<div class="error-message">Something went wrong</div>');
                $('body').append(errorMessage);
                console.log(errorMessage);
            });
        }
    }
});
