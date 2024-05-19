define([
    'jquery',
    'mage/translate',
    'mage/calendar'
], function ($, $t) {
    'use strict';
    $('.data-picker').calendar({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        currentText: $t('Go Today'),
        closeText: $t('Close'),
        showWeek: true,
        minDate: new Date(),
        defaultDate: new Date()
    });
});
