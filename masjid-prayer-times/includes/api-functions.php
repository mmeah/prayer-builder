<?php
/**
 * API functions for Masjid Prayer Times plugin.
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Fetches prayer times data from the Masjidal.com API.
 *
 * @param string $masjid_id The ID of the masjid.
 * @return array|false|WP_Error The decoded prayer times data, false on failure, or a WP_Error object.
 */
function masjid_prayer_times_fetch_data( $masjid_id ) {
    if ( empty( $masjid_id ) ) {
        return new WP_Error( 'missing_masjid_id', __( 'Masjid ID is required.', 'masjid-prayer-times' ) );
    }

    $api_url = 'https://masjidal.com/api/v1/time/range?masjid_id=' . urlencode( $masjid_id );
    $timeout = 15; // seconds

    $response = wp_remote_get( $api_url, array( 'timeout' => $timeout ) );

    if ( is_wp_error( $response ) ) {
        return $response; // Return the WP_Error object from wp_remote_get
    }

    $body = wp_remote_retrieve_body( $response );
    if ( empty( $body ) ) {
        return new WP_Error( 'empty_response', __( 'API returned an empty response.', 'masjid-prayer-times' ) );
    }

    $data = json_decode( $body, true ); // true for associative array

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_Error( 'json_decode_error', __( 'Error decoding JSON response: ', 'masjid-prayer-times' ) . json_last_error_msg() );
    }

    if ( ! isset( $data['status'] ) || $data['status'] !== 'success' ) {
        $error_message = isset( $data['message'] ) ? $data['message'] : __( 'API request was not successful and no message provided.', 'masjid-prayer-times' );
        return new WP_Error( 'api_error', __( 'API Error: ', 'masjid-prayer-times' ) . sanitize_text_field( $error_message ) );
    }

    if ( ! isset( $data['data'] ) ) {
        return new WP_Error( 'missing_data_key', __( 'API response is missing the "data" key.', 'masjid-prayer-times' ) );
    }

    return $data['data'];
}
?>
