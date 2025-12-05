$(document).ready(function () {
    const dropdown = $('#mainDropdown');

    $('.dropdown_btn').on('click', function (e) {
        e.preventDefault();
        const target = $(this).data('target');
        const $targetContent = $(target);

        if ($targetContent.hasClass('show')) {
            dropdown.removeClass('show');
            $targetContent.removeClass('show');
        } else {
            // Hiện khung ngoài
            dropdown.addClass('show');

            // Ẩn tất cả nội dung con
            dropdown.find('.dropdown-content').removeClass('show');

            // Hiện đúng nội dung được chọn
            $targetContent.addClass('show');
        }
    });

    // Click ra ngoài thì đóng
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.menu-item, #mainDropdown').length) {
            dropdown.removeClass('show');
            dropdown.find('.dropdown-content').removeClass('show');
        }
    });
});
