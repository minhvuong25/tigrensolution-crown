define([
    'jquery'
], function ($) {
    return function (config, element) {
        $(element).on('click', function (e) {

            $.ajax({
                url: config.url,
                type: 'POST',
                data: {
                    data: JSON.stringify(config.data),
                    namespace: config.namespace,
                    form_key: config.form_key
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    alert('Oops, something wrong happned');
                }
            }).done(function (result) {
                window.location.href = config.redirect;
            });
        });
    };
});