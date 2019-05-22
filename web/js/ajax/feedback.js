function formChecker(page = 1, theNumberOnThePage = 5, userId = null) {
    $('body').on('submit', '.ajaxForm', function (e) {

        e.preventDefault();

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            error: function (response) {
                /**
                 * Because of the returned object with the message
                 * I need for the flash message,
                 * ajax considers this to be an error
                 */
                $(".ajaxForm")[0].reset()
                loadFeedback(page, theNumberOnThePage, userId);

                var content = '';
                if (response.status === 200) {
                    content = ' <div class="alert alert-success flush-message">' + response.responseText + '</div>'
                } else {
                    content = ' <div class="alert alert-danger flush-message">' + response.responseText + '</div>'
                }

                $('#flush-block').html(content);

                setTimeout(function () {
                    $(".flush-message").remove();
                }, 5000)
            }
        })
    });

    $('body').on('submit', '.delete-feedback', function (e) {

        e.preventDefault();

        var feedbackId = $(this).attr('data-feedback')

        $.ajax({
            type: 'GET',
            url: '/ajax/feedback/delete',
            data: 'id=' + feedbackId,
            success: function(response) {
                console.log(response);

                loadFeedback(page, theNumberOnThePage, userId);

                if (response === 'Feedback successfully removed') {
                    content = ' <div class="alert alert-success flush-message">' + response + '</div>'
                } else {
                    content = ' <div class="alert alert-danger flush-message">' + response + '</div>'
                }

                $('#flush-block').html(content);

                setTimeout(function () {
                    $(".flush-message").remove();
                }, 5000)
            },
            error: function(response) {
                console.log(response);
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
            var html = '';

            for (let count = 0; count < feedbackArray.length; ++count) {
                var feedback = feedbackArray[count];

                html += '<div class="feedback">\n' +
                    '       <div class="feedbackHead">\n' +
                    '    <div class="feedbackCreated">' + feedback.created.slice(0, -9) + '</div>\n' +
                    '       <p class="feedbackName">' + feedback.user.first_name + '' + feedback.user.last_name + '</p>\n' +
                    '       <p class="feedbackEmail">' + feedback.user.email + '</p>\n' +
                    '    </div>\n' +
                    '    <div class="feedbackBody">\n' +
                    '        <p class="feedbackMessage">' + feedback.message + '</p>\n';

                var images = feedback.images;
                for (let i = 0; i < images.length; ++i) {
                    var image = images[i];

                    html += '<div class="feedbackFile">\n' +
                        '        <img alt="img" class="feedbackFileImages" src="uploads/images/' + image.image_path + '">\n' +
                        '    </div>\n';
                }

                if (userId != null && feedback.user.id == userId) {
                    html += '<div class="feedbackOptions">\n' +
                        '        <form data-feedback="' + feedback.id + '" class="delete-feedback">\n' +
                        '            <button type="submit" class="feedbackDelete">' +
                        '               <i class="fas fa-trash-alt"></i>' +
                        '            </button>\n' +
                        '        </form>\n' +
                        '    </div>'
                }

                html += '</div>\n' +
                    '     </div>';
            }

            $('#feedback-window').html(html);
        }
    })
}