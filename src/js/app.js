jQuery(document).ready(function($) {
    let offset = 0;
    let isLoading = false;

    let params = new URLSearchParams(window.location.search);
    let initialSearchTerm = params.get('tool') || '';
    $('#ai-tool-filter #search').val(initialSearchTerm);


    function loadPosts(search = initialSearchTerm, sort = 'date', category = '', pricing = '') {
        if(isLoading) return;
        isLoading = true;
        $('#loading-indicator').show();

        $.ajax({
            url: frontendajax.ajaxurl,
            data: {
                action: 'load_posts',
                search: search,
                sort: sort,
                category: category,
                pricing: pricing,
                offset: offset
            },
            success: function(response) {
                if(response.trim() !== "") {
                    if(offset === 0) {
                        $('#posts-container').html(response);
                    } else {
                        $('#posts-container').append(response);
                    }
                    offset += 12;
                }
                $('#loading-indicator').hide();
                isLoading = false;
            },
            error: function() {
                $('#loading-indicator').hide();
                isLoading = false;
            }
        });
    }

    loadPosts(initialSearchTerm);

    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() > $('#posts-container').height() - 100) {
            loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
        }
    });

    $('#ai-tool-filter #search').on('input', function() {
        offset = 0;
        loadPosts($(this).val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
    });

    $('#ai-tool-filter #sort, #ai-tool-filter #category, #ai-tool-filter #pricing').on('change', function() {
        offset = 0;
        loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
    });

    $("#ai-tool-filter #category").selectize({
        allowEmptyOption: true,
        placeholder: 'All Categories',
        onChange: function(value) {
            offset = 0;
            loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), value, $('#ai-tool-filter #pricing').val());
        }
    });
});