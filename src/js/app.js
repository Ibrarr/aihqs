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
    $('#ai-tool-filter #search').on('input', function() {
        loadPosts($(this).val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val());
    });

    $('#sort, #category').on('change', function() {
        loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val());
    });
});
