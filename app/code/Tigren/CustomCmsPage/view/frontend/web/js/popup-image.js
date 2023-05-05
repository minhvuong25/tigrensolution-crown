require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function(
        $,
        modal
    ) {
        var options = {
            type: 'popup',
            responsive: false,
            innerScroll: true,
            modalClass: 'modal-featues',
            title: false,
            buttons: false
        };

        var popup = modal(options, $('#record-feature'));
        $("#view-record").on('click',function(){
            $("#record-feature").modal("openModal");
        });

        var popup1 = modal(options, $('#round-feature'));
        $("#view-round").on('click',function(){
            $("#round-feature").modal("openModal");
        });

        var popup2 = modal(options, $('#job-feature'));
        $("#view-job").on('click',function(){
            $("#job-feature").modal("openModal");
        });

        var popup3 = modal(options, $('#employees-feature'));
        $("#view-employees").on('click',function(){
            $("#employees-feature").modal("openModal");
        });

        var popup4 = modal(options, $('#time-feature'));
        $("#view-time").on('click',function(){
            $("#time-feature").modal("openModal");
        });

        var popup5 = modal(options, $('#track-feature'));
        $("#view-track").on('click',function(){
            $("#track-feature").modal("openModal");
        });

        var popup6 = modal(options, $('#work-feature'));
        $("#view-work").on('click',function(){
            $("#work-feature").modal("openModal");
        });

        var popup7 = modal(options, $('#lunch-feature'));
        $("#view-lunch").on('click',function(){
            $("#lunch-feature").modal("openModal");
        });
    }
);
