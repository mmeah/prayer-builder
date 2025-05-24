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

/**
 * Fetches the live silver price from Lear Capital.
 *
 * @return array|WP_Error An array with 'price' (float) and 'date' (string) on success, or WP_Error on failure.
 */
function masjid_nisab_fetch_silver_price() {
    $api_url = 'http://www.learcapital.com/charts/generated/silver_daily.json';
    $timeout = 15; // seconds

    $response = wp_remote_get( $api_url, array( 'timeout' => $timeout ) );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        return new WP_Error( 'api_error', sprintf( __( 'Failed to fetch silver price data: HTTP Error %s', 'masjid-prayer-times' ), $response_code ) );
    }

    $body = wp_remote_retrieve_body( $response );
    if ( empty( $body ) ) {
        return new WP_Error( 'empty_response', __( 'API returned an empty response for silver price.', 'masjid-prayer-times' ) );
    }

    // Strip JSONP wrapper
    $start_pos = strpos( $body, '(' );
    $end_pos = strrpos( $body, ')' );

    if ( $start_pos === false || $end_pos === false || $start_pos >= $end_pos ) {
        return new WP_Error( 'jsonp_parse_error', __( 'Invalid JSONP format for silver price data.', 'masjid-prayer-times' ) );
    }

    $json_str = substr( $body, $start_pos + 1, $end_pos - $start_pos - 1 );

    if ( empty( $json_str ) ) {
        return new WP_Error( 'jsonp_parse_error', __( 'Empty JSON content after stripping JSONP wrapper for silver price.', 'masjid-prayer-times' ) );
    }

    $data = json_decode( $json_str ); // Decode as object

    if ( $data === null && strtolower( $json_str ) !== 'null' ) {
        return new WP_Error( 'json_decode_error', __( 'Error decoding silver price JSON: ', 'masjid-prayer-times' ) . json_last_error_msg() );
    }

    // Extract silver price
    if ( ! is_object( $data ) || ! property_exists( $data, 'columnValues' ) || ! is_array( $data->columnValues ) || empty( $data->columnValues ) ) {
        return new WP_Error( 'data_format_error', __( 'Unexpected data structure for silver price: Missing or invalid columnValues.', 'masjid-prayer-times' ) );
    }

    $last_entry_index = count( $data->columnValues ) - 1;
    if ( ! isset( $data->columnValues[ $last_entry_index ] ) || ! is_array( $data->columnValues[ $last_entry_index ] ) || count( $data->columnValues[ $last_entry_index ] ) < 2 ) {
        return new WP_Error( 'data_format_error', __( 'Unexpected data structure for silver price: Invalid last entry in columnValues.', 'masjid-prayer-times' ) );
    }

    $silver_price_raw = $data->columnValues[ $last_entry_index ][1];
    $silver_price = floatval( $silver_price_raw );

    // Extract generatedOn date
    $generated_on_date = null;
    if ( property_exists( $data, 'generatedOn' ) && ! empty( $data->generatedOn ) ) {
        $generated_on_date = sanitize_text_field( $data->generatedOn );
    } else {
        // Fallback to current date if generatedOn is not available
        $generated_on_date = current_time( 'mysql', true ); // GMT
        // Or, if you prefer just date part: current_time( 'Y-m-d', true );
    }
    
    return array(
        'price' => $silver_price,
        'date'  => $generated_on_date,
    );
}
?>
