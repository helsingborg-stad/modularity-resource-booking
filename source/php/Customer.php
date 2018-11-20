<?php

namespace ModularityResourceBooking;

class Customer
{

    public $roles = array();

    public function __construct()
    {
        add_action('init', array($this, 'registerOptionsPage'));
    }

    /**
     * Registers an options page
     *
     * @return void
     */
    public function registerOptionsPage()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(
                array(
                    'icon_url' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMi4wMDEgNTEyLjAwMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyLjAwMSA1MTIuMDAxOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij48Zz48Zz48cGF0aCBkPSJNMjcxLjAyOSwwYy0zMy4wOTEsMC02MSwyNy45MDktNjEsNjFzMjcuOTA5LDYxLDYxLDYxczYwLTI3LjkwOSw2MC02MVMzMDQuMTIsMCwyNzEuMDI5LDB6IiBmaWxsPSIjRkZGRkZGIi8+PC9nPjwvZz48Zz48Zz48cGF0aCBkPSJNMzM3LjYyMSwxMjJjLTE2LjQ4NSwxOC4yNzktNDAuMDk2LDMwLTY2LjU5MiwzMGMtMjYuNDk2LDAtNTEuMTA3LTExLjcyMS02Ny41OTItMzAgICAgYy0xNC4zOTIsMTUuOTU5LTIzLjQwOCwzNi44NjYtMjMuNDA4LDYwdjE1YzAsOC4yOTEsNi43MDksMTUsMTUsMTVoMTUxYzguMjkxLDAsMTUtNi43MDksMTUtMTV2LTE1ICAgIEMzNjEuMDI5LDE1OC44NjYsMzUyLjAxMywxMzcuOTU5LDMzNy42MjEsMTIyeiIgZmlsbD0iI0ZGRkZGRiIvPjwvZz48L2c+PGc+PGc+PHBhdGggZD0iTTE0NC45NDYsNDYwLjQwNEw2OC41MDUsMzA3LjE0OWMtNy4zODEtMTQuNzk5LTI1LjM0NS0yMC44MzQtNDAuMTYyLTEzLjQ5M2wtMTkuOTc5LDkuODk3ICAgIGMtNy40MzksMy42ODktMTAuNDY2LDEyLjczLTYuNzUzLDIwLjE1Nmw5MCwxODBjMy43MDEsNy40MjMsMTIuNzA0LDEwLjM3NywyMC4wODMsNi43MzhsMTkuNzIyLTkuNzcxICAgIEMxNDYuMjkxLDQ5My4zMDgsMTUyLjM1NCw0NzUuMjU5LDE0NC45NDYsNDYwLjQwNHoiIGZpbGw9IiNGRkZGRkYiLz48L2c+PC9nPjxnPjxnPjxwYXRoIGQ9Ik00OTkuNzMsMjQ3LjdjLTEyLjMwMS05LTI5LjQwMS03LjItMzkuNiwzLjlsLTgyLDEwMC44Yy01LjcsNi0xNi41LDkuNi0yMi4yLDkuNmgtNjkuOTAxYy04LjQwMSwwLTE1LTYuNTk5LTE1LTE1ICAgIHM2LjU5OS0xNSwxNS0xNWMyMC4wOSwwLDQyLjMzMiwwLDYwLDBjMTYuNSwwLDMwLTEzLjUsMzAtMzBzLTEzLjUtMzAtMzAtMzBjLTcwLjQ0NiwwLTMuMjUsMC03OC42LDAgICAgYy03LjQ3NiwwLTExLjIwNC00Ljc0MS0xNy4xLTkuOTAxYy0yMy4yMDktMjAuODg1LTU3Ljk0OS0zMC45NDctOTMuMTE5LTIyLjc5NWMtMTkuNTI4LDQuNTI2LTMyLjY5NywxMi40MTUtNDYuMDUzLDIyLjk5MyAgICBsLTAuNDQ1LTAuMzYxTDg5LjAxNiwyODEuMDNMMTc0LjI4LDQ1MmgyNS4yNDhoMTQ2LjUwMWMyOC4yLDAsNTUuMjAxLTEzLjUsNzIuMDAxLTM2bDg3Ljk5OS0xMjYgICAgQzUxNS45MjksMjc2Ljc5OSw1MTMuMjI5LDI1Ny42MDEsNDk5LjczLDI0Ny43eiIgZmlsbD0iI0ZGRkZGRiIvPjwvZz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PC9zdmc+',
                    'page_title' => __('Customer Types', 'modularity-resource-booking')
                )
            );
        }
    }

    /**
     * Create custom user roles
     * @return void
     */
    public static function createUserRoles()
    {
        add_role('customer', __('Customer', 'modularity-resource-booking'), array(
            'read' => true,
            'level_0' => true,
            'upload_files' => true
        ));
    }

    /**
     * Remove custom user roles
     * @return void
     */
    public static function removeUserRoles()
    {
        if (get_role('customer')) {
            remove_role('customer');
        }
    }

}
