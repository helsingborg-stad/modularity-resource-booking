<div class="{{ isset($classes) ? is_array($classes) ? implode(' ', $classes) : $classes : null  }}">
    @if (!$hideTitle && !empty($post_title))
        <h4 class="box-title">{!! apply_filters('the_title', $post_title) !!}</h4>
    @endif
    @if (is_user_logged_in())
        <div class="modularity-resource-booking-form">
            <div class="gutter gutter-xl">
                <div class="loading">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    @else
        <div class="gutter">
            <div class="notice info">
                <?php _e('You are not logged in.', 'modularity-resource-booking'); ?>
            </div>
        </div>
    @endif
</div>
