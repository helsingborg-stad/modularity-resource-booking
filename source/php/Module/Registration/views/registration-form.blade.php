<div class="{{ $classes }}">
    @if (!$hideTitle && !empty($post_title))
        <h4 class="box-title">{!! apply_filters('the_title', $post_title) !!}</h4>
    @endif
    @if (!is_user_logged_in())
        <div class="modularity-registration-form"></div>
    @else
        <div class="gutter">
            <?php _e('You are already logged in.', 'modularity-resource-booking'); ?>
        </div>
    @endif
</div>