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
	}
}
