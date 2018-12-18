<?php

namespace ModularityResourceBooking\Entity;

class Filter
{
    public $taxonomySlug;
    public $postType;

    public function __construct($taxonomySlug, $postType)
    {
        $this->taxonomySlug = $taxonomySlug;
        $this->postType = $postType;

        //Add filters
        add_filter('parse_query', array($this, 'addQueryVar'));
        add_action('restrict_manage_posts', array($this, 'graphicSelect'));
        add_action('restrict_manage_users', array($this, 'graphicSelectUsers'));
        add_action('pre_get_users', array($this, 'filterUsersByTaxonomy'));
    }

    public function graphicSelect()
    {
        global $typenow;

        if ($typenow == $this->postType) {
            wp_dropdown_categories(array(
                'show_option_all' => __("Show All", "modularity-resource-booking") . " " . get_taxonomy($this->taxonomySlug)->label,
                'taxonomy' => $this->taxonomySlug,
                'name' => $this->taxonomySlug,
                'orderby' => 'name',
                'selected' => isset($_GET[$this->taxonomySlug]) ? $_GET[$this->taxonomySlug] : '',
                'show_count' => true,
                'hide_empty' => true,
            ));
        };
    }

    /**
     * Filter posts by taxonomy in admin
     * @author  Mike Hemberger
     * @link    http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
     */
    public function addQueryVar($query)
    {
        //Gather data
        global $pagenow;
        $q_vars = &$query->query_vars;

        //Validate that we are on correct page
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $this->postType && isset($q_vars[$this->taxonomySlug]) && is_numeric($q_vars[$this->taxonomySlug]) && $q_vars[$this->taxonomySlug] != 0) {
            $term = get_term_by('id', $q_vars[$this->taxonomySlug], $this->taxonomySlug);
            $q_vars[$this->taxonomySlug] = $term->slug;
        }

        return $query;
    }

    public function filterUsersByTaxonomy($query)
    {
        global $pagenow;
        $role = $_GET['role'] ?? null;
        $value = $_GET[$this->taxonomySlug] ?? null;

        if (is_admin() && $pagenow === 'users.php' && $role === 'customer' && $value) {
            $result = self::getUserByTaxonomy($this->taxonomySlug, $value);
            $result = !empty($result) ? array_column($result, 'ID') : array(0);

            $query->set('include', $result);
        }
    }

    public static function getUserByTaxonomy($slug, $id)
    {
        global $wpdb;
        $dbQuery = "
                SELECT ID
                FROM {$wpdb->users} AS users
                LEFT JOIN {$wpdb->term_relationships} AS tax_rel ON (users.ID = tax_rel.object_id)
                LEFT JOIN {$wpdb->term_taxonomy} AS term_tax ON (tax_rel.term_taxonomy_id = term_tax.term_taxonomy_id)
                LEFT JOIN {$wpdb->terms} AS terms ON (terms.term_id = term_tax.term_id)
                WHERE terms.term_id = '{$id}'
                AND term_tax.taxonomy = '{$slug}'";
        return $wpdb->get_results($dbQuery, ARRAY_A);
    }

    public function graphicSelectUsers($which)
    {
        $screen = get_current_screen();

        if (isset($screen->base) && $screen->base === $this->postType && $which === 'top' && isset($_GET['role']) && $_GET['role'] === 'customer') {
            echo '<div style="display:block; float:right;">';
            wp_dropdown_categories(array(
                'show_option_all' => __('Show All', 'modularity-resource-booking') . ' ' . get_taxonomy($this->taxonomySlug)->label,
                'taxonomy' => $this->taxonomySlug,
                'name' => $this->taxonomySlug,
                'orderby' => 'name',
                'selected' => isset($_GET[$this->taxonomySlug]) ? $_GET[$this->taxonomySlug] : '',
                'show_count' => true,
                'hide_empty' => true,
            ));

            submit_button(__('Filter', 'modularity-resource-booking'), null, $which, false);
            echo '</div>';
        }
    }
}
