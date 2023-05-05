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
            responsive: true,
            innerScroll: true,
            title: false,
            buttons: false,
            modalClass: 'video-modal',
            closed: function (e) {
                stopVideo();
            }
        };

        modal(options, $('#popup-modal-banner-video'));
        $(".btn-banner-video").on('click',function(){
            $("#popup-modal-banner-video").modal("openModal");
        });
        function stopVideo() {
            var videos = document.querySelectorAll('iframe, video');
            Array.prototype.forEach.call(videos, function (video) {
                if (video.tagName.toLowerCase() === 'video') {
                    video.pause();
                } else {
                    var src = video.src;
                    video.src = src;
                }
            });
        }
    }
);
