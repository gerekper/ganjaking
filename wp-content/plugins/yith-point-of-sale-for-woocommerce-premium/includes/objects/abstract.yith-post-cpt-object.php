<?php

// Exit if accessed directly
!defined( 'YITH_POS' ) && exit();

if ( !class_exists( 'YITH_POS_CPT_Object' ) ) {
    abstract class YITH_POS_CPT_Object {
        /** @var array */
        protected $data = array();

        /** @var array */
        protected $changes = array();

        /** @var string */
        protected $post_type = '';

        /** @var int */
        protected $id;

        /** @var string */
        protected $object_type = 'cpt_object';

        /** @var bool */
        protected $object_read = false;

        /**
         * YITH_POS_CPT_Object constructor.
         */
        public function __construct( $obj ) {
            if ( is_numeric( $obj ) && $obj > 0 ) {
                $this->set_id( $obj );
            } elseif ( $obj instanceof self ) {
                $this->set_id( absint( $obj->get_id() ) );
            } elseif ( !empty( $obj->ID ) ) {
                $this->set_id( absint( $obj->ID ) );
            }

            if ( $this->get_id() ) {
                if ( !$this->post_type || $this->post_type === get_post_type( $this->get_id() ) ) {
                    $this->populate_props();
                    $this->object_read = true;
                } else {
                    $this->set_id( 0 );
                }
            }
        }

        /**
         * Prefix for action and filter hooks on data.
         *
         * @return string
         */
        protected function get_hook_prefix() {
            return 'yith_pos_' . $this->object_type . '_get_';
        }

        /**
         * Prefix for action and filter hooks on data.
         *
         * @return string
         */
        protected function get_hook() {
            return 'yith_pos_' . $this->object_type . '_get';
        }

        /**
         * Return data changes only
         *
         * @return array
         */
        public function get_changes() {
            return $this->changes;
        }

        /**
         * get object properties
         *
         * @param string $prop
         * @param string $context What the value is for. Valid values are view and edit.
         * @return mixed
         */
        protected function get_prop( $prop, $context = 'view' ) {
            $value = null;

            if ( array_key_exists( $prop, $this->data ) ) {
                $value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

                if ( 'view' === $context ) {
                    $value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this );
                    $value = apply_filters( $this->get_hook(), $value, $prop, $this );
                }
            }

            return $value;
        }

        protected function get_meta_by_prop( $prop ) {
            return '_' . $prop;
        }

        /**
         * Populate all props
         */
        protected function populate_props() {
            foreach ( $this->data as $prop => $default_value ) {
                $meta   = $this->get_meta_by_prop( $prop );
                $value  = metadata_exists( 'post', $this->get_id(), $meta ) ? get_post_meta( $this->get_id(), $meta, true ) : $default_value;
                $setter = "set_{$prop}";
                if ( method_exists( $this, $setter ) ) {
                    $this->$setter( $value );
                } else {
                    $this->set_prop( $prop, $value );
                }
            }
        }

        /**
         * set an object property
         *
         * @param string $prop
         * @param mixed  $value the value
         */
        protected function set_prop( $prop, $value ) {
            if ( array_key_exists( $prop, $this->data ) ) {
                if ( true === $this->object_read ) {
                    if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
                        $this->changes[ $prop ] = $value;
                    }
                } else {
                    $this->data[ $prop ] = $value;
                }
            }
        }

        /**
         * set object properties
         *
         * @param array $props
         */
        public function set_props( $props ) {
            foreach ( $props as $key => $value ) {
                $setter = 'set_' . $key;
                if ( is_callable( array( $this, $setter ) ) ) {
                    $this->$setter( $value );
                }
            }
        }

        /**
         * Merge changes with data and clear.
         */
        public function apply_changes() {
            $this->data    = array_replace_recursive( $this->data, $this->changes );
            $this->changes = array();
        }

        /**
         * Merge changes with data and clear.
         *
         * @param bool $force
         */
        protected function update_post_meta( $force = false ) {
            $props_to_update = !$force ? $this->get_changes() : $this->data;
            foreach ( $props_to_update as $prop => $value ) {
                $meta = $this->get_meta_by_prop( $prop );
                update_post_meta( $this->get_id(), $meta, $value );
            }
        }

        /**
         * Store options in DB
         *
         * @param bool $force
         * @return int
         */
        public function save( $force = false ) {
            if ( $force ) {
                $this->apply_changes();
                $this->update_post_meta( true );
            } else {
                $this->update_post_meta();
                $this->apply_changes();
            }

            return $this->get_id();
        }

        /**
         * get the object ID
         *
         * @return int
         */
        public function get_id() {
            return $this->id;
        }

        /**
         * set the object ID
         *
         * @param $id
         */
        public function set_id( $id ) {
            $this->id = absint( $id );
        }

        /**
         * @return bool
         */
        public function is_valid() {
            return !!$this->get_id() && ( !$this->post_type || $this->post_type === get_post_type( $this->get_id() ) );
        }

        /**
         * trash the related Post
         */
        public function trash() {
            return wp_trash_post( $this->get_id() );
        }

        /**
         * delete the related Post
         */
        public function delete() {
            return wp_delete_post( $this->get_id() );
        }

        /**
         * Return the post_status of the Store
         *
         * @return string
         */
        public function get_post_status() {
            return get_post_status( $this->get_id() );
        }

        /**
         * return the data
         *
         * @return array
         */
        public function get_data() {
            return array_merge( $this->data, array( 'id' => $this->get_id() ) );
        }

        /**
         * return the current data
         *
         * @return array
         */
        public function get_current_data() {
            $current_data = array_replace_recursive( $this->data, $this->changes );
            return array_merge( $current_data, array( 'id' => $this->get_id() ) );
        }
    }
}