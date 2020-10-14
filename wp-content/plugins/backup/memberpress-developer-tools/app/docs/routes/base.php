<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

return array(
  "get_{$this->class_info->plural}" => (object)array(
    'name'   => sprintf(__('Get %s', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->plural)),
    'desc'   => sprintf(__('Get all %s based on the search arguments provided.', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->plural)),
    'method' => 'GET',
    'url'    => rest_url($this->namespace.'/'.$this->base),
    'auth'   => true,
    'search_args'  => $this->search_fields,
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => false,
      'count' => 10
    )
  ),
  "get_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Get %s', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'desc'   => sprintf(__('Get one %s with a given id.', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'method' => 'GET',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  ),
  "create_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Create %s', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'desc'   => sprintf(__('Create a %s with the given field values.', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base),
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => $this->accept_fields,
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  ),
  "update_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Update %s', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'desc'   => sprintf(__('Update a %s with the given id and field values.', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'method' => 'POST',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => $this->accept_fields,
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  ),
  "delete_{$this->class_info->singular}" => (object)array(
    'name'   => sprintf(__('Delete %s', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'desc'   => sprintf(__('Delete a %s with the given id.', 'memberpress-developer-tools'), MpdtInflector::humanize($this->class_info->singular)),
    'method' => 'DELETE',
    'url'    => rest_url($this->namespace.'/'.$this->base) . '/:id',
    'auth'   => true,
    'search_args'  => __('None', 'memberpress-developer-tools'),
    'update_args'  => __('None', 'memberpress-developer-tools'),
    'output' => __('JSON', 'memberpress-developer-tools'),
    'resp'   => (object)array(
      'utils_class' => $this->class_info->singular,
      'single_result' => true,
      'count' => 1
    )
  )
);

