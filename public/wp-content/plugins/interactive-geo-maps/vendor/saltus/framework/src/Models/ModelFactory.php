<?php

namespace Saltus\WP\Framework\Models;

use Noodlehaus\AbstractConfig;

class ModelFactory {

	protected $app;
	protected $project;

	public function __construct( $app, $project ) {
		$this->app     = $app;
		$this->project = $project;
	}

	/**
	 * Route to class
	 */
	public function create( AbstractConfig $config ) {

		// soft fail
		if ( ! $config->has( 'type' ) ) {
			return false;
		}

		if ( in_array( $config->get( 'type' ), [ 'post-type', 'cpt', 'posttype', 'post_type' ], true ) ) {
			$cpt = new PostType( $config );
			$cpt->setup();

			$services = [ 'meta', 'settings' ];
			foreach ( $services as $service_name ) {
				if ( $config->has( $service_name ) && $this->app->has( $service_name ) ) {

				$config_value = $config->get( $service_name );
				$service_app = $this->app->get( $service_name );
				$service_app->make( $cpt->name, $this->project, $config_value );
			}
			}

			$service_name = 'features';
			if ( $config->has( $service_name ) ) {
				$features = $config->get( $service_name );

				foreach ( $features as $feature_name => $args ) {

					if ( ! $args ) {
						continue;
					}
					$normalized_feature_name = strtolower( $feature_name );

					// Feature is not available
					if ( ! $this->app->has( $normalized_feature_name ) ) {
						continue;
					}

					// make sure $args is an array
					if( ! is_array( $args ) ){
						$args = [];
					}

					$service = $this->app->get( $normalized_feature_name );
					$service->make( $cpt->name, $this->project, $args );
				}
			}

			// disable block editor only if 'block_editor' is false
			if ( $config->has( 'block_editor' ) && ! $config->get( 'block_editor' ) ) {
				add_filter( 'use_block_editor_for_post_type', array( $cpt, 'disable_block_editor'), 10, 2 );
			}
			return $cpt;

		}
		if ( in_array( $config->get( 'type' ), [ 'taxonomy', 'tax', 'category', 'cat', 'tag' ], true ) ) {
			$taxonomy = new Taxonomy( $config );
			$taxonomy->setup();
			return $taxonomy;
		}

		return false;

	}
}
