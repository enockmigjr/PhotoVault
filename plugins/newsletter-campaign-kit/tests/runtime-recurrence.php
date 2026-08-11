<?php
/**
 * WordPress runtime verification for recurring campaign occurrences.
 *
 * Run with: wp eval-file tests/runtime-recurrence.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function newsletter_recurrence_runtime_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

global $wpdb;

$suffix          = strtolower( wp_generate_password( 8, false, false ) );
$campaigns_table = newsletter_campaign_kit_get_campaigns_table();
$queue_table     = newsletter_campaign_kit_get_queue_table();
$snapshots_table = newsletter_campaign_kit_get_audience_snapshots_table();
$members_table   = newsletter_campaign_kit_get_audience_snapshot_members_table();
$master_id       = 0;
$scheduled_id    = 0;
$launch_master_id = 0;
$occurrence_ids  = array();
$subscriber_id   = 0;

try {
	$email = 'recurrence-' . $suffix . '@photovault.test';
	$wpdb->insert(
		newsletter_campaign_kit_get_subscribers_table(),
		array(
			'email'             => $email,
			'email_hash'        => newsletter_campaign_kit_hash_email( $email ),
			'unsubscribe_token' => newsletter_campaign_kit_create_unsubscribe_token( newsletter_campaign_kit_hash_email( $email ) ),
			'status'            => 'subscribed',
			'source'            => 'runtime_recurrence',
			'consent_text'      => 'Runtime recurrence consent',
			'created_at'        => current_time( 'mysql', true ),
			'updated_at'        => current_time( 'mysql', true ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);
	$subscriber_id = (int) $wpdb->insert_id;

	$scheduled_id = newsletter_campaign_kit_create_campaign(
		array(
			'title'                      => 'Runtime scheduled creation ' . $suffix,
			'subject'                    => 'Runtime scheduled creation',
			'html_body'                  => '<p>Runtime scheduled creation</p>',
			'text_body'                  => 'Runtime scheduled creation',
			'target_audience'            => 'all',
			'scheduled_at'               => wp_date( 'Y-m-d\\TH:i', time() + HOUR_IN_SECONDS ),
			'campaign_recurrence_enabled' => true,
			'recurrence_interval_days'   => 2,
			'recurrence_until'           => gmdate( 'Y-m-d', time() + ( 3 * DAY_IN_SECONDS ) ),
		),
		1
	);
	newsletter_recurrence_runtime_assert( is_int( $scheduled_id ), 'Campaign created with a schedule failed.' );
	$scheduled_created = newsletter_campaign_kit_get_campaign( $scheduled_id );
	newsletter_recurrence_runtime_assert( 'ready' === $scheduled_created['status'] && ! empty( $scheduled_created['scheduled_at'] ), 'Scheduled campaign was not created ready.' );
	newsletter_recurrence_runtime_assert( 2 === absint( $scheduled_created['recurrence_interval_days'] ) && ! empty( $scheduled_created['recurrence_until'] ), 'Recurring options were not persisted at creation.' );

	$launch_master_id = newsletter_campaign_kit_create_campaign(
		array(
			'title'           => 'Runtime launch now ' . $suffix,
			'subject'         => 'Runtime launch now',
			'html_body'       => '<p>Runtime launch now</p>',
			'text_body'       => 'Runtime launch now',
			'target_audience' => 'all',
		),
		1
	);
	newsletter_recurrence_runtime_assert( is_int( $launch_master_id ), 'Launch-now master could not be created.' );
	$wpdb->update( $campaigns_table, array( 'status' => 'ready' ), array( 'id' => $launch_master_id ), array( '%s' ), array( '%d' ) );
	$launch_master = newsletter_campaign_kit_get_campaign( $launch_master_id );
	$launch_review = newsletter_campaign_kit_prepare_campaign_delivery_review( $launch_master );
	$until         = gmdate( 'Y-m-d', time() + ( 3 * DAY_IN_SECONDS ) );
	$launch        = newsletter_campaign_kit_launch_confirmed_recurrence_now( $launch_master_id, 2, $until, $launch_master['title'], $launch_review['fingerprint'], 1 );
	newsletter_recurrence_runtime_assert( ! is_wp_error( $launch ) && ! empty( $launch['occurrence_id'] ), 'Recurring launch now failed: ' . ( is_wp_error( $launch ) ? $launch->get_error_code() . ':' . $launch->get_error_message() : 'empty result' ) );
	$occurrence_ids[] = absint( $launch['occurrence_id'] );
	$occurrence       = newsletter_campaign_kit_get_campaign( $launch['occurrence_id'] );
	$launch_master    = newsletter_campaign_kit_get_campaign( $launch_master_id );
	newsletter_recurrence_runtime_assert( $launch_master_id === absint( $occurrence['parent_campaign_id'] ?? 0 ) && 1 === absint( $occurrence['occurrence_number'] ?? 0 ), 'Launch-now occurrence sequence is invalid.' );
	newsletter_recurrence_runtime_assert( 'recurring' === $launch_master['status'] && ! empty( $launch_master['next_occurrence_at'] ), 'Launch now did not advance the recurring calendar.' );
	newsletter_recurrence_runtime_assert( (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$queue_table} WHERE campaign_id = %d", $launch['occurrence_id'] ) ) > 0, 'Launch-now occurrence was not enqueued.' );

	$forced_next = gmdate( 'Y-m-d H:i:s', time() + ( 5 * DAY_IN_SECONDS ) );
	$wpdb->update(
		$campaigns_table,
		array( 'next_occurrence_at' => $forced_next ),
		array( 'id' => $launch_master_id ),
		array( '%s' ),
		array( '%d' )
	);
	$next_launch = newsletter_campaign_kit_launch_recurring_occurrence_now( $launch_master_id, 1 );
	newsletter_recurrence_runtime_assert( ! is_wp_error( $next_launch ) && absint( $launch['occurrence_id'] ) !== absint( $next_launch['occurrence_id'] ), 'Recurring master could not launch its next occurrence.' );
	$occurrence_ids[] = absint( $next_launch['occurrence_id'] );
	$next_occurrence  = newsletter_campaign_kit_get_campaign( $next_launch['occurrence_id'] );
	$advanced_master  = newsletter_campaign_kit_get_campaign( $launch_master_id );
	newsletter_recurrence_runtime_assert( 2 === absint( $next_occurrence['occurrence_number'] ?? 0 ), 'Recurring next-occurrence sequence is invalid.' );
	newsletter_recurrence_runtime_assert( $forced_next !== $advanced_master['next_occurrence_at'], 'Recurring master did not recalculate its next date.' );

	$master_id = newsletter_campaign_kit_create_campaign(
		array(
			'title'           => 'Runtime recurrence ' . $suffix,
			'subject'         => 'Runtime recurrence',
			'html_body'       => '<p>Runtime recurrence</p>',
			'text_body'       => 'Runtime recurrence',
			'target_audience' => 'all',
		),
		1
	);
	newsletter_recurrence_runtime_assert( is_int( $master_id ), 'Recurring master could not be created.' );
	$wpdb->update( $campaigns_table, array( 'status' => 'ready' ), array( 'id' => $master_id ), array( '%s' ), array( '%d' ) );

	$master = newsletter_campaign_kit_get_campaign( $master_id );
	$review = newsletter_campaign_kit_prepare_campaign_delivery_review( $master );
	$first  = gmdate( 'Y-m-d H:i:s', time() + HOUR_IN_SECONDS );
	$until  = gmdate( 'Y-m-d', time() + ( 3 * DAY_IN_SECONDS ) );
	$result = newsletter_campaign_kit_schedule_confirmed_recurrence( $master_id, $first, 2, $until, $master['title'], $review['fingerprint'], 1 );
	newsletter_recurrence_runtime_assert( true === $result, 'Recurring master could not be scheduled.' );

	$wpdb->update(
		$campaigns_table,
		array( 'next_occurrence_at' => gmdate( 'Y-m-d H:i:s', time() - MINUTE_IN_SECONDS ) ),
		array( 'id' => $master_id ),
		array( '%s' ),
		array( '%d' )
	);
	$claimed       = newsletter_campaign_kit_claim_due_recurrences( 1 );
	$occurrence_ids = array_merge( $occurrence_ids, $claimed );
	newsletter_recurrence_runtime_assert( 1 === count( $claimed ), 'Due recurrence did not create exactly one occurrence.' );

	$occurrence = newsletter_campaign_kit_get_campaign( $claimed[0] );
	$master     = newsletter_campaign_kit_get_campaign( $master_id );
	newsletter_recurrence_runtime_assert( $master_id === absint( $occurrence['parent_campaign_id'] ?? 0 ), 'Occurrence lost its master relationship.' );
	newsletter_recurrence_runtime_assert( 1 === absint( $occurrence['occurrence_number'] ?? 0 ), 'Occurrence sequence is invalid.' );
	newsletter_recurrence_runtime_assert( 'recurring' === $master['status'] && ! empty( $master['next_occurrence_at'] ), 'Recurring master did not advance to its next run.' );

	echo wp_json_encode(
		array(
			'master'               => 'advanced',
			'occurrence'           => 'created',
			'sequence'             => 1,
			'interval_days'        => 2,
			'schedule_at_creation' => true,
			'launch_now'           => true,
			'next_launch_now'      => true,
		)
	);
} finally {
	foreach ( $occurrence_ids as $occurrence_id ) {
		$snapshot_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$snapshots_table} WHERE campaign_id = %d", $occurrence_id ) );
		foreach ( $snapshot_ids as $snapshot_id ) {
			$wpdb->delete( $members_table, array( 'snapshot_id' => $snapshot_id ), array( '%d' ) );
		}
		$wpdb->delete( $snapshots_table, array( 'campaign_id' => $occurrence_id ), array( '%d' ) );
		$wpdb->delete( $queue_table, array( 'campaign_id' => $occurrence_id ), array( '%d' ) );
		$wpdb->delete( $campaigns_table, array( 'id' => $occurrence_id ), array( '%d' ) );
	}
	if ( $master_id ) {
		$wpdb->delete( $campaigns_table, array( 'id' => $master_id ), array( '%d' ) );
	}
	if ( $launch_master_id ) {
		$wpdb->delete( $campaigns_table, array( 'id' => $launch_master_id ), array( '%d' ) );
	}
	if ( $scheduled_id ) {
		$wpdb->delete( $campaigns_table, array( 'id' => $scheduled_id ), array( '%d' ) );
	}
	if ( $subscriber_id ) {
		$wpdb->delete( newsletter_campaign_kit_get_subscriber_topics_table(), array( 'subscriber_id' => $subscriber_id ), array( '%d' ) );
		$wpdb->delete( newsletter_campaign_kit_get_subscriber_lists_table(), array( 'subscriber_id' => $subscriber_id ), array( '%d' ) );
		$wpdb->delete( newsletter_campaign_kit_get_subscribers_table(), array( 'id' => $subscriber_id ), array( '%d' ) );
	}
}
