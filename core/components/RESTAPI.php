<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RESTAPI extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'rest-api';
	protected $object_type = 'route';
	protected $icon = 'ui-target';

	private array $actions = array(
		'HEAD'    => 'read',
		'OPTIONS' => 'read',
		'GET'     => 'read',
		'POST'    => 'edit',
		'PATCH'   => 'edit',
		'PUT'     => 'edit',
		'DELETE'  => 'delete',
	);

	public function tracking() {
		if ( $this->is_any_event_active() ) {
			add_filter( 'rest_pre_serve_request', array( $this, 'pre_serve_request' ), 10, 4 );
		}
	}

	public function label() : string {
		return __( 'REST API', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'rest-route-unknown'        => array(
				'label' => __( 'Unknown REST Route', 'coreactivity' ),
			),
			'rest-request-unauthorized' => array(
				'label' => __( 'Unauthorized REST Request', 'coreactivity' ),
			),
			'rest-request-invalid'      => array(
				'label' => __( 'Invalid REST Request', 'coreactivity' ),
			),
			'rest-server-error'         => array(
				'label' => __( 'REST Server Error', 'coreactivity' ),
			),
			'rest-delete-item'          => array(
				'label' => __( 'REST Item Delete', 'coreactivity' ),
			),
			'rest-edit-item'            => array(
				'label' => __( 'REST Item Edited', 'coreactivity' ),
			),
			'rest-create-item'          => array(
				'label' => __( 'REST Item Created', 'coreactivity' ),
			),
			'rest-read-item'            => array(
				'label'  => __( 'REST Item Read', 'coreactivity' ),
				'status' => 'inactive',
			),
		);
	}

	/**
	 * @param bool             $served   Whether the request has already been served.
	 * @param WP_HTTP_Response $response Result to send to the client. Usually a WP_REST_Response.
	 * @param WP_REST_Request  $request  Request used to generate the response.
	 * @param WP_REST_Server   $server   Server instance.
	 */
	public function pre_serve_request( $served, $response, $request, $server ) : bool {
		$data = $response->get_data();
		$rest = array(
			'method' => $request->get_method(),
			'action' => $this->actions[ $request->get_method() ] ?? 'unknown',
			'query'  => $request->get_query_params(),
			'body'   => $request->get_body_params(),
			'status' => $response->get_status(),
			'code'   => $data['code'] ?? '',
		);

		if ( $rest['status'] == 404 ) {
			$this->_log( 'rest-route-unknown', $request->get_route(), $rest );
		} else if ( $rest['status'] == 401 || $rest['status'] == 403 ) {
			$this->_log( 'rest-request-unauthorized', $request->get_route(), $rest );
		} else if ( $rest['status'] >= 400 && $rest['status'] < 500 ) {
			$this->_log( 'rest-request-invalid', $request->get_route(), $rest );
		} else if ( $rest['status'] >= 500 ) {
			$this->_log( 'rest-server-error', $request->get_route(), $rest );
		} else if ( $rest['status'] == 201 ) {
			if ( $rest['action'] == 'edit' ) {
				$this->_log( 'rest-create-item', $request->get_route(), $rest );
			}
		} else {
			if ( $rest['action'] == 'delete' ) {
				$this->_log( 'rest-delete-item', $request->get_route(), $rest );
			} else if ( $rest['action'] == 'edit' ) {
				$this->_log( 'rest-edit-item', $request->get_route(), $rest );
			} else {
				$this->_log( 'rest-read-item', $request->get_route(), $rest );
			}
		}

		return $served;
	}

	private function _log( $event, $route, $rest ) {
		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_type' => $this->object_type,
				'object_name' => $route,
			), $rest );
		}
	}
}
