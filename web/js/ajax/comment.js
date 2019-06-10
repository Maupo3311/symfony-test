function formChecker(userId, prodcutId) {
    $('body').on('submit', '.comment-form', function (e) {

        e.preventDefault();

        var formData = new FormData(this);
        formData.append('productId', prodcutId);

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: formData,
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

                $(".comment-form")[0].reset()
                loadComments(userId, prodcutId)

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

    $('body').on('submit', '.delete-comment', function (e) {

        e.preventDefault();

        var commentId = $(this).attr('data-comment')

        $.ajax({
            type: 'GET',
            url: '/ajax/comment/delete',
            data: 'id=' + commentId,
            success: function(response) {
                console.log(response);

                loadComments(userId, prodcutId);

                if (response === 'Comment successfully removed') {
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

function loadComments(userId, productId) {

    $.ajax({
        type: 'GET',
        url: '/api/product/' + productId + '/comments',
        success: function (comments) {
            var html = '';

            comments = comments.reverse();
            for (let count = 0; count < comments.length; ++count) {

                var comment = comments[count];

                html += '<li class="media">\n' +
                    '       <a href="#" class="pull-left">\n' +
                    '           <img src="https://bootdey.com/img/Content/user_1.jpg" alt="" class="img-circle">\n' +
                    '       </a>\n' +
                    '       <div class="media-body">\n' +
                    '           <span class="text-muted pull-right">\n' +
                    '               <small class="text-muted">' + comment.created_at.slice(0, -9) + '</small>\n' +
                    '           </span>\n' +
                    '           <strong class="text-success">@' + comment.user.first_name + ' ' + comment.user.last_name + '</strong>\n' +
                    '                <p>\n' +
                    '                    ' + comment.message + '\n';

                var images = comment.images;
                if (images != null) {
                    for (let i = 0; i < images.length; ++i) {
                        var image = images[i];

                        html += '<div class="comment-image-window">\n' +
                            '        <img alt="" class="comment-image" src="/uploads/images/' + image.image_path + '">\n' +
                            '     </div>';
                    }
                }

                if (userId === comment.user.id) {
                    html += '<p class="comment_options">\n' +
                        '         <form data-comment="' + comment.id + '" class="delete-comment">\n' +
                        '               <button class="comment_delete">' +
                        '                   <i class="fas fa-trash-alt"></i>' +
                        '               </button>\n' +
                        '         </form>\n' +
                        '     </p>';
                }

                html += ' </p>\n' +
                    '  </div>\n' +
                    '</li>';
            }

            $('.media-list').html(html);
        }
    })
}