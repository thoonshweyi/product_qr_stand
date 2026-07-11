$(document).on('click', '[data-modal-toggle]', function () {
    const targetId = $(this).attr('data-modal-toggle');
    const $modal = $('#' + targetId);
    
    if ($modal.length) {
        if ($modal.hasClass('hidden')) {
            $modal.removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden');      // background scroll ဖြစ်တာ ပိတ်မယ်
        } else {
            $modal.removeClass('flex').addClass('hidden');
            $('body').removeClass('overflow-hidden');   // background scroll ပြန်ဖွင့်မယ်
        }
    }
});

$(document).on('click', '.modals', function (e) {
    if ($(e.target).is(this)) {
        $(this).removeClass('flex').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }
});