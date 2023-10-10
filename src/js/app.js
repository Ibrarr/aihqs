jQuery(document).ready(function($) {
    let offset = 0;
    let isLoading = false;

    let params = new URLSearchParams(window.location.search);
    let initialSearchTerm = params.get('tool') || '';
    $('#ai-tool-filter #search').val(initialSearchTerm);

    function scrollHandler() {
        if ($(window).scrollTop() + $(window).height() > $('#posts-container').height() - 100) {
            loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
        }
    }

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
                let data = JSON.parse(response);
                if (data.count > 0) {
                    if (offset === 0) {
                        $('#posts-container').html(data.html);
                    } else {
                        $('#posts-container').append(data.html);
                    }

                    // If less than 12 posts, unbind the scroll event
                    if (data.count < 12) {
                        $(window).off('scroll', scrollHandler); // This line unbinds the specific scroll event handler
                    } else {
                        offset += 12;
                    }
                } else if(data.count === 0 && offset === 0) { // No results found for the first call
                    $('#posts-container').html(data.html); // Update with "No results found" message
                    $(window).off('scroll', scrollHandler); // Unbind the scroll event handler
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
    $(window).scroll(scrollHandler);

    $('#ai-tool-filter #search').on('input', function() {
        offset = 0;
        $(window).off('scroll');
        loadPosts($(this).val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
        $(window).scroll(scrollHandler);
    });

    $('#ai-tool-filter #sort, #ai-tool-filter #category, #ai-tool-filter #pricing').on('change', function() {
        offset = 0;
        $(window).off('scroll');
        loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), $('#ai-tool-filter #category').val(), $('#ai-tool-filter #pricing').val());
        $(window).scroll(scrollHandler);
    });

    $("#ai-tool-filter #category").selectize({
        allowEmptyOption: true,
        placeholder: 'All Categories',
        onChange: function(value) {
            offset = 0;
            $(window).off('scroll');
            loadPosts($('#ai-tool-filter #search').val(), $('#ai-tool-filter #sort').val(), value, $('#ai-tool-filter #pricing').val());
            $(window).scroll(scrollHandler);
        }
    });

    $('#clear-filters').on('click', function() {
        $('#ai-tool-filter #search').val('');

        let selectize = $("#ai-tool-filter #category")[0].selectize;
        if (selectize) {
            selectize.clear();
        }

        $('#ai-tool-filter #sort').val('date');
        $('#ai-tool-filter #pricing').val('');

        $('#ai-tool-filter .sort-filter .select-selected').text('Date Added');
        $('#ai-tool-filter .pricing-filter .select-selected').text('All Pricing');

        offset = 0;
        $(window).off('scroll');
        loadPosts('', 'date', '', '');
        $(window).scroll(scrollHandler);
    });

});