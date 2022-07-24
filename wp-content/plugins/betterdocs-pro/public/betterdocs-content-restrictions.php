<?php

trait BetterDocs_Content_Restrictions
{
    public function content_restriction()
    {
        return BetterDocs_DB::get_settings('enable_content_restriction');
    }

    public function get_404_template()
    {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        get_template_part( 404 );
        exit();
    }

    public function restricted_redirect_url()
    {
        $restricted_redirect_url = BetterDocs_DB::get_settings('restricted_redirect_url');
        if ($restricted_redirect_url) {
            wp_redirect($restricted_redirect_url);
        } else {
            $this->get_404_template();
        }
    }

    public function content_visibility_by_role()
    {
        global $current_user;
        $roles = $current_user->roles;
        $content_visibility = BetterDocs_DB::get_settings('content_visibility');
        $content_visibility = ($content_visibility !== 'off') ? $content_visibility : array('all');
        //If The User Has Multiple Roles Assigned
        $role_exists = is_array($content_visibility) ? ( array_intersect( $roles, $content_visibility ) ) : 'all';
        if (is_user_logged_in() && (($role_exists == true) || in_array('all', $content_visibility))) {
            return true;
        } else {
            return false;
        }
    }

    public function internal_kb_restriction()
    {
        global $wp_query;
        $restrict_template = BetterDocs_DB::get_settings('restrict_template');
        $restrict_template = !empty($restrict_template) ? $restrict_template : array();
        $restrict_category = BetterDocs_DB::get_settings('restrict_category');
        $restrict_category = !empty($restrict_category) ? $restrict_category : array();
        $restrict_kb = BetterDocs_DB::get_settings('restrict_kb');
        $restrict_kb = !empty($restrict_kb) ? $restrict_kb : array();
        $tax = BetterDocs_Helper::get_tax();

        $cat_terms = get_the_terms(get_the_ID(), 'doc_category');
        $kb_terms = get_the_terms(get_the_ID(), 'knowledge_base');

        if ($this->is_betterdocs() && $this->internal_kb == 1 && $this->content_visibility_by_role() == false
            && (is_array($restrict_template) && in_array('all', $restrict_template)
                || (is_array($restrict_template) && in_array('docs', $restrict_template))
                || ($tax === 'knowledge_base'
                    && (is_array($restrict_template) && in_array('knowledge_base', $restrict_template)
                        && (is_array($restrict_kb) && (in_array('all', $restrict_kb) || in_array($wp_query->query['knowledge_base'], $restrict_kb)))))
                || ($tax === 'doc_category'
                    && (is_array($restrict_template) && in_array('doc_category', $restrict_template)
                        && (is_array($restrict_category) && (in_array('all', $restrict_category) || in_array($wp_query->query['doc_category'], $restrict_category)))))
                || (is_singular('docs')
                    && ((is_array($restrict_template) && in_array('doc_category', $restrict_template))
                        && (is_array($restrict_category) && (in_array('all', $restrict_category) || in_array($cat_terms[0]->slug, $restrict_category)))
                        || ((is_array($restrict_template) && in_array('knowledge_base', $restrict_template))
                            && (is_array($restrict_kb) && (in_array('all', $restrict_kb) || (is_array($kb_terms) && in_array($kb_terms[0]->slug, $restrict_kb))))))
                )
            )
        ) {
            $this->restricted_redirect_url();
        }
    }

    public function get_restricted_category()
    {
        $cat_ids = array();
        $restrict_template = ( BetterDocs_DB::get_settings('restrict_template') == 'off' ) ? array( 'off' ) : BetterDocs_DB::get_settings('restrict_template');
        $restrict_category = BetterDocs_DB::get_settings('restrict_category');
        if ((is_array($restrict_template) && in_array('all', $restrict_template) || in_array('doc_category', $restrict_template))
            && is_array($restrict_category) && in_array('all', $restrict_category)) {
            $cat_ids = get_terms([
                'taxonomy' => 'doc_category',
                'fields' => 'ids',
            ]);
        } else if ((is_array($restrict_template) && in_array('all', $restrict_template) || in_array('doc_category', $restrict_template)) && is_array($restrict_category)) {
            foreach ($restrict_category as $category) {
                $term = get_term_by( 'slug', $category, 'doc_category' );
                if( $term != false ) {
                    $cat_ids[] = $term->term_id;
                }
            }
        }
        return $cat_ids;
    }

    public function restrict_doc_category($terms_object)
    {
        if ($this->content_visibility_by_role() == false && !empty($this->get_restricted_category())) {
            $terms_object['exclude'] = $this->get_restricted_category();
        }
        return $terms_object;
    }

    public function get_restricted_kb()
    {
        $kb_ids = array();
        $restrict_template = ( BetterDocs_DB::get_settings('restrict_template') == 'off' ) ? array('off') : BetterDocs_DB::get_settings('restrict_template');
        $restrict_kb = BetterDocs_DB::get_settings('restrict_kb');
        $multiple_kb = BetterDocs_DB::get_settings('multiple_kb');

        if ((is_array($restrict_template) && in_array('all', $restrict_template) && $multiple_kb == 1 || in_array('knowledge_base', $restrict_template) && $multiple_kb == 1 )
            && is_array($restrict_kb) && in_array('all', $restrict_kb) && $multiple_kb == 1 ) {
            $kb_ids = get_terms([
                'taxonomy' => 'knowledge_base',
                'fields' => 'ids',
            ]);
        } else if ((is_array($restrict_template) && in_array('all', $restrict_template) && $multiple_kb == 1 || in_array('knowledge_base', $restrict_template) && $multiple_kb == 1 ) && is_array($restrict_kb) && $multiple_kb == 1) {
            foreach ( $restrict_kb as $kb ) {
                $term = get_term_by( 'slug', $kb, 'knowledge_base' );
                if( $term != false ) {
                    $kb_ids[] = $term->term_id;
                }
            }
        }

        return $kb_ids;
    }

    public function restrict_kb($terms_object)
    {
        if ($this->content_visibility_by_role() == false && !empty($this->get_restricted_kb())) {
            $terms_object['exclude'] = $this->get_restricted_kb();
        }
        return $terms_object;
    }

    public function search_articles_args($terms_object)
    {
        if ($this->content_visibility_by_role() == false) {
            $terms_object = array(
                'relation' => 'AND'
            );

            if (!empty($this->get_restricted_category())){
                $terms_object[] = array(
                    'taxonomy' => 'doc_category',
                    'field' => 'term_id',
                    'operator' => 'NOT IN',
                    'terms' => $this->get_restricted_category(),
                    'include_children'  => true,
                );
            }

            if (BetterDocs_Multiple_Kb::$enable == 1 && (!empty($this->get_restricted_kb()))){
                $terms_object[] = array(
                    'taxonomy' => 'knowledge_base',
                    'field' => 'term_id',
                    'terms' => $this->get_restricted_kb(),
                    'operator' => 'NOT IN',
                    'include_children'  => true,
                );
            }
        }
        return $terms_object;
    }

    public function restrict_tax_query($tax_query, $object)
    {
        if ($this->content_visibility_by_role() == false) {
            $tax_query = array(
                array(
                    'taxonomy' => 'doc_tag',
                    'field'    => 'slug',
                    'terms'    => $object->slug
                ),
                'relation' => 'AND',
                array(
                    'relation' => 'AND',
                )
            );

            if (!empty($this->get_restricted_category())){
                $tax_query[1][] = array(
                    'taxonomy' => 'doc_category',
                    'field' => 'term_id',
                    'operator' => 'NOT IN',
                    'terms' => $this->get_restricted_category(),
                    'include_children'  => true,
                );
            }

            if (BetterDocs_Multiple_Kb::$enable == 1 && (!empty($this->get_restricted_kb()))){
                $tax_query[1][] = array(
                    'taxonomy' => 'knowledge_base',
                    'field' => 'term_id',
                    'terms' => $this->get_restricted_kb(),
                    'operator' => 'NOT IN',
                    'include_children'  => true,
                );
            }
        }
        return $tax_query;
    }

    public function uncategorized_docs_query($args) {
        $args['tax_query'] = array( 
            array( 
                'taxonomy' => 'doc_category',
                'field'    => 'term_id',
                'terms'    => $this->get_restricted_category(),
                'operator' => 'NOT IN',
            ) 
        );
        return $args;
    }
}