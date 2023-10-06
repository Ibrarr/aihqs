jQuery(document).ready(function($) {
    function loadPosts(search = '', sort = 'date', category = '') {
        $.ajax({
            url: frontendajax.ajaxurl,
            data: {
                action: 'load_posts',
                search: search,
                sort: sort,
                category: category,
            },
            success: function(response) {
                $('#posts-container').html(response);
            }
        });
    }

    // Initially load posts
    loadPosts();

    // Add event listeners for filters
    $('#tools-filter #search').on('input', function() {
        loadPosts($(this).val(), $('#tools-filter #sort').val(), $('#tools-filter #category').val());
    });

    $('#sort, #category').on('change', function() {
        loadPosts($('#tools-filter #search').val(), $('#tools-filter #sort').val(), $('#tools-filter #category').val());
    });
});
