<div class="{{ isset($classes) ? is_array($classes) ? implode(' ', $classes) : $classes : null  }}">
    @if (!$hideTitle && !empty($post_title))
        <h4 class="box-title">{!! apply_filters('the_title', $post_title) !!}</h4>
    @endif
    @if (is_user_logged_in())
        <div class="modularity-resource-booking-form"></div>
    @else
        <p>You are not logged in.</p>
    @endif
</div>
