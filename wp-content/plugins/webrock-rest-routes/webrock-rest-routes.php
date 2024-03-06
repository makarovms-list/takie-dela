<?php
/*
Plugin Name: Webrock REST routes
Plugin URI:
Description:
Version: 1.0.0
Author: Mikhail Makarov
Author URI: https://webrockstudio.ru/
*/

class Webrock_REST_Posts_Controller extends WP_REST_Controller {
    
    function __construct(){
		$this->namespace = 'webrock-namespace/v1';
		$this->rest_base = 'posts';
	}
	
	function register_routes(){

		register_rest_route( $this->namespace, "/$this->rest_base", [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
			],
			'schema' => [ $this, 'get_item_schema' ],
		] );

		register_rest_route( $this->namespace, "/$this->rest_base/(?P<id>[\w]+)", [
			[
				'methods'   => 'GET',
				'callback'  => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
			],
			'schema' => [ $this, 'get_item_schema' ],
		] );
		
	}
	
	function get_items_permissions_check( $request ){
	    /*
		if ( ! current_user_can( 'read' ) )
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), [ 'status' => $this->error_status_code() ] );
        */
		return true;
	}

	function get_items( $request ){
		$data = [];

        $date = (string) $request['date'];
        $category = (string) $request['category'];
        
        if ( !empty($date) || !empty($category) ) {
            
            $params = array(
                'post_type' => 'post',
            );
            
            if ( !empty($date) ) {
                $date_arr = explode( ".", $date );
            	$params['year'] = $date_arr[2];
            	$params['monthnum'] = $date_arr[1];
            	$params['day'] = $date_arr[0];
            }
            
            if ( !empty($category) ) {
                $params['tax_query'] = array(
        			'taxonomy' => 'category',
        			'field' => 'slug',
        			'terms' => $category
                );
            }
            
            $posts_quyery = new WP_Query( $params );    
            $posts = $posts_quyery->posts;
            //return $posts_quyery;
        } else {
    		$posts = get_posts( [
    			'post_per_page' => 3,
    		] );            
        }

		if ( empty( $posts ) )
			return $data;

		foreach( $posts as $post ){
			$response = $this->prepare_item_for_response( $post, $request );
			$data[] = $this->prepare_response_for_collection( $response );
		}

		return $data;
	}
	
    function get_item_permissions_check( $request ){
		return $this->get_items_permissions_check( $request );
	}	
	
	function get_item( $request ){
		$id = (int) $request['id'];
		$post = get_post( $id );

		if( ! $post )
			return array();

		return $this->prepare_item_for_response( $post, $request );
	}
	
	function prepare_item_for_response( $post, $request ){

		$post_data = [];

		$schema = $this->get_item_schema();

		// We are also renaming the fields to more understandable names.
		if ( isset( $schema['properties']['id'] ) )
			$post_data['id'] = (int) $post->ID;

		if ( isset( $schema['properties']['date'] ) )
			$post_data['date'] = (string) get_the_date( 'd.m.Y, h:i', $post );
			
		if ( isset( $schema['properties']['image'] ) && has_post_thumbnail( $post->ID ) ) {
		    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
		    $post_data['image'] = (string) $image[0];
		}

		if ( isset( $schema['properties']['title'] ) )
			$post_data['title'] = (string) $post->post_title;
			
		if ( isset( $schema['properties']['category'] ) ) {
		    
		    $category_detail=get_the_category( $post->ID );
		    $category_str = '';
            foreach($category_detail as $cd){
                if (!empty($category_str)) $category_str .= ', ';
                $category_str .= $cd->cat_name;
            }
		    $post_data['category'] = (string) $category_str;
		}
			
        if ( isset( $schema['properties']['content'] ) )
			$post_data['content'] = apply_filters( 'the_content', $post->post_content, $post );
			
		if ( isset( $schema['properties']['author'] ) )
			$post_data['author'] = (string) get_post_meta( $post->ID, 'author', true );
			
		return $post_data;
	}
	
	function prepare_response_for_collection( $response ){

		if ( ! ( $response instanceof WP_REST_Response ) ){
			return $response;
		}

		$data = (array) $response->get_data();
		$server = rest_get_server();

		if ( method_exists( $server, 'get_compact_response_links' ) ){
			$links = call_user_func( [ $server, 'get_compact_response_links' ], $response );
		}
		else {
			$links = call_user_func( [ $server, 'get_response_links' ], $response );
		}

		if ( ! empty( $links ) ){
			$data['_links'] = $links;
		}

		return $data;
	}
	
	function get_item_schema(){
		$schema = [
			// показывает какую версию схемы мы используем - это draft 4
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			// определяет ресурс который описывает схема
			'title'      => 'vehicle',
			'type'       => 'object',
			// в JSON схеме нужно указывать свойства в атрибуете 'properties'.
			'properties' => [
				'id' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'integer',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'date' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'image' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'title' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'category' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'content' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'author' => [
					'description' => 'Unique identifier for the object.',
					'type'        => 'string',
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true,
				],
				'vin' => [
					'description' => 'VIN code of vehicle.',
					'type'        => 'string',
				],
				// TODO добавить поля
				// []
			],
		];

		return $schema;
	}
	
	function error_status_code(){
		return is_user_logged_in() ? 403 : 401;
	}
	
}

add_action( 'rest_api_init', 'webrock_register_rest_routes' );
function webrock_register_rest_routes() {
	$controller = new Webrock_REST_Posts_Controller();
	$controller->register_routes();
}