function formChecker(page = 1, theNumberOnThePage = 5, userId = null) {
    $('body').on('submit', '.ajaxForm', function (e) {

        e.preventDefault();

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (response) {
                loadFeedback(page, theNumberOnThePage, userId)
            }
        })
    });
}

function loadFeedback(page, theNumberOnThePage, userId) {

    $.ajax({
        type: 'GET',
        url: '/api/feedback-list',
        data: 'page=' + page + '&limit=' + theNumberOnThePage,
        success: function (feedbackArray) {
            console.log(feedbackArray);
            var html = '';

            for (let count = 0; count < feedbackArray.length; ++count) {
                var feedback = feedbackArray[count];
                html += '<div class="feedback">\n' +
                    '       <div class="feedbackHead">\n' +
                            '    <div class="feedbackCreated">' + feedback.created + '</div>\n' +
                            '       <p class="feedbackName">' + feedback.user.first_name + '' + feedback.user.last_name + '</p>\n' +
                            '       <p class="feedbackEmail">' + feedback.user.email + '</p>\n' +
                            '    </div>\n' +
                            '    <div class="feedbackBody">\n' +
                            '        <p class="feedbackMessage">' + feedback.message + '</p>\n';

                var images = feedback.images;
                for (let i = 0; i < images.length; ++i) {
                    var image = images[i];

                    console.log(images);
                    html += '<div class="feedbackFile">\n' +
                        '        <img alt="img" class="feedbackFileImages" src="uploads/images/' + image.image_path + '">\n' +
                        '    </div>\n';
                }

                if (userId != null && feedback.user.id == userId) {
                    html += '<div class="feedbackOptions">\n' +
                        '        <a href="/feedback/delete/' + feedback.id + '" class="noneDecoration">\n' +
                        '            <span class="feedbackDelete"><i class="fas fa-trash-alt"></i></span>\n' +
                        '        </a>\n' +
                        '    </div>'
                }

                html +=  '</div>\n' +
                '     </div>';
            }

            $('#feedback-window').html(html);
        }
    })
}