<?php
/**
 * Plugin Name:       WooCommerce Event Tickets - Title
 * Description:       Include event title within ticket names at various locations.
 * Version:           1.0
 * Author:            Jory Hogeveen
 * Author URI:        https://www.keraweb.nl
 * Text Domain:       woo-event-tickets-title
 * GitHub Plugin URI: JoryHogeveen/woo-event-tickets-title
 *
 * Event Tickets Plus - WooCommerce Tickets: Filter Visible tickets to have a
 * post title of "Tickets for {Event_Name}" while in Shop or Search.
 *
 * We filter the ticket name in Shop and Search so that an event with 2 tickets
 * (e.g. Lower-Level and Balcony) but only 1 Visible (e.g. Balcony) does not
 * appear simply as "Balcony" in the Shop and then customers are looking around
 * within the Shop for a different type of ticket for the event (e.g. Lower-
 * Level).
 *
 * @link https://gist.github.com/cliffordp/c012f2945a22867a3af2c2878e06fffd
 */

if ( ! defined ( 'ABSPATH' ) ) {
	die;
}

Woo_Event_Tickets_Title::get_instance();

class Woo_Event_Tickets_Title
{
	/**
	 * @var Woo_Event_Tickets_Title
	 */
	private static $_instance = null;

	/**
	 * @return Woo_Event_Tickets_Title
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Woo_Event_Tickets_Title constructor.
	 */
	protected function __construct() {
		add_filter( 'the_title', array( $this, 'filter_wooticket_titles_in_shop_and_search' ), 10, 2 );
		add_filter( 'display_post_states', array( $this, 'filter_display_post_states' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'filter_woocommerce_listing_item_name' ), 10, 2 );
		add_filter( 'woocommerce_order_item_name', array( $this, 'filter_woocommerce_listing_item_name' ), 10, 2 );
	}

	function filter_woocommerce_listing_item_name( $name, $item ) {
		try {
			$event = tribe_events_get_ticket_event( $item['product_id'] );
			if ( $event ) {
				$link = get_permalink( $event->ID );
				$event_date = tribe_get_start_date( $event->ID, $display_time = true, 'M j \a\t g:ia' );
				$name = $name . ' | Ticket for <a href="' . $link . '">' . $event->post_title . '</a>';// - ' . $event_date;
			}
		} catch ( \Throwable $e ) {
			if ( is_super_admin() ) {
				var_dump( $e->getMessage() );
			}
		}
		return $name;
	}

	function filter_display_post_states( $states, $post ) {
		try {
			$screen = get_current_screen();

			if ( 'edit-product' === $screen->id ) {

				$event = tribe_events_get_ticket_event( $post->ID );
				if ( $event ) {
					$link = get_edit_post_link( $event->ID );
					$states['ticket_for'] = 'Ticket for <a href="' . $link . '">' . $event->post_title . '</a>';
				}
			}

		} catch ( \Throwable $e ) {
			if ( is_super_admin() ) {
				var_dump( $e->getMessage() );
			}
		}
		return $states;
	}

	function filter_wooticket_titles_in_shop_and_search( $title, $id ) {

		try {
			if ( is_admin() ) {
				return $title;
			}
			if ( ! function_exists( 'is_shop' ) ) {
				return $title;
			}

			if ( is_shop() || is_search() ) {
				$title = $this->append_event_title( $title, $id );
			}
		} catch ( \Throwable $e ) {
			if ( is_super_admin() ) {
				var_dump( $e->getMessage() );
			}
		}
	
		return $title;
	}

	private function append_event_title( $title, $id ) {
		if ( ! wc_get_product( $id ) ) {
			return $title;
		}

		// Search for the non-deprecated custom field.
		$event_id = (int) get_post_meta( $id, Tribe__Tickets_Plus__Commerce__WooCommerce__Main::ATTENDEE_EVENT_KEY, true );

		if ( ! tribe_is_event( $event_id ) ) {
			// Search for the deprecated custom field, from Tribe__Tickets_Plus__Commerce__WooCommerce__Main::$event_key
			$event_id = (int) get_post_meta( $id, '_tribe_wooticket_for_event', true );
		}

		if ( tribe_is_event( $event_id ) ) {
			$title .= sprintf( ' | Ticket for %s', esc_html( get_the_title( $event_id ) ) );
		}

		return $title;
	}
}
