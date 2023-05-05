define(['jquery', 'mage/translate', 'mage/calendar'], function ($, $t) {
    $(document).on('change', '.subscription_type_radio', function () {
        var value = $(this).val();
        
        if (value == "subscription") {
            //$(this).parents('.md_subscription_form').find('.md_subscription_content').show();
            $(this).parents('.md_subscription_form').find('.md_subscription_content').removeClass('md_subscription_content_hidden');
        } else {
            //$(this).parents('.md_subscription_form').find('.md_subscription_content').hide();
            $(this).parents('.md_subscription_form').find('.md_subscription_content').addClass('md_subscription_content_hidden');
        }
    });

    $(function () {
        $('.input-date-picker').each(function () {
            var min_date = $(this).attr('data-min-date');
            var selected_date = $(this).attr('data-date');
            $(this).calendar({
                showsTime: false,
                changeMonth: false,
                changeYear: false,
                showOn: 'focus',
                hideIfNoPrevNext: true,
                showAnim: "",
                buttonImageOnly: null,
                buttonImage: null,
                showButtonPanel: false,
                showOtherMonths: false,
                showWeek: false,
                timeFormat: '',
                showTime: false,
                showHour: false,
                showMinute: false,
                buttonText: $t('Select Date'),
                dateFormat: "yy-mm-dd",
                minDate: new Date(min_date)
            });
            
            $(this).calendar().datepicker("setDate", new Date(selected_date));
        });
    });
});