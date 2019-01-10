<div class="{{ $classes }}">
    @if (!$hideTitle && !empty($post_title))
        <h4 class="box-title">{!! apply_filters('the_title', $post_title) !!}</h4>
    @endif
    @if (is_user_logged_in())
        <div class="modularity-order-history"></div>
    @else
        <div class="gutter"><p><?php echo __('You are not logged in.', 'modularity-resource-booking') ?></p></div>
    @endif
</div>