jQuery(document).ready(function($) {
    function loadPosts(search = '', sort = 'date', category = '', pricing = '') {
        $.ajax({
            url: frontendajax.ajaxurl,
            data: {
                action: 'load_posts',
                search: search,
                sort: sort,
                category: category,
                pricing: pricing, // sending the pricing value
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
        loadPosts($(this).val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
    });

    $('#ai-tool-filter #sort, #ai-tool-filter #category, #ai-tool-filter #pricing').on('change', function() {
        loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
    });

    $("#ai-tool-filter #category").selectize({
        allowEmptyOption: true,
        placeholder: 'All Categories',
        onChange: function(value) {
            loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), value, $('#ai-tool-filter #pricing').val());
        }
    });
});
