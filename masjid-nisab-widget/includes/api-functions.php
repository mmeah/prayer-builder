<?php
/**
 * API functions for Masjid Nisab Widget plugin.
 */

if ( ! defined( 'WPINC' ) ) {
    die;
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
        return new WP_Error( 'api_error', sprintf( __( 'Failed to fetch silver price data: HTTP Error %s', 'masjid-nisab-widget' ), $response_code ) );
    }

    $body = wp_remote_retrieve_body( $response );
    if ( empty( $body ) ) {
        return new WP_Error( 'empty_response', __( 'API returned an empty response for silver price.', 'masjid-nisab-widget' ) );
    }

    // Strip JSONP wrapper: jsonpChartCallback(JSON_DATA);
    $prefix = 'jsonpChartCallback(';
    $suffix = ');';

    if ( strpos( $body, $prefix ) !== 0 ) {
        return new WP_Error( 'jsonp_parse_error', __( 'Invalid JSONP format: Missing prefix.', 'masjid-nisab-widget' ) );
    }

    // Remove prefix
    $json_str = substr( $body, strlen( $prefix ) );

    // Remove suffix (and potentially trailing whitespace like newlines)
    // Look for the suffix starting from the end of the string, after removing prefix
    if ( substr( rtrim( $json_str ), -strlen( $suffix ) ) === $suffix ) {
        $json_str = substr( rtrim( $json_str ), 0, -strlen( $suffix ) );
    } else {
        return new WP_Error( 'jsonp_parse_error', __( 'Invalid JSONP format: Missing suffix.', 'masjid-nisab-widget' ) );
    }
    
    if ( empty( $json_str ) ) {
        return new WP_Error( 'jsonp_parse_error', __( 'Empty JSON content after stripping JSONP wrapper for silver price.', 'masjid-nisab-widget' ) );
    }
    
    $data = json_decode( $json_str ); // Decode as object

    if ( $data === null && strtolower( $json_str ) !== 'null' ) {
        return new WP_Error( 'json_decode_error', __( 'Error decoding silver price JSON: ', 'masjid-nisab-widget' ) . json_last_error_msg() );
    }

    // Extract silver price
    if ( ! is_object( $data ) || ! property_exists( $data, 'columnValues' ) || ! is_array( $data->columnValues ) || empty( $data->columnValues ) ) {
        return new WP_Error( 'data_format_error', __( 'Unexpected data structure for silver price: Missing or invalid columnValues.', 'masjid-nisab-widget' ) );
    }

    $last_entry_index = count( $data->columnValues ) - 1;
    if ( ! isset( $data->columnValues[ $last_entry_index ] ) || ! is_array( $data->columnValues[ $last_entry_index ] ) || count( $data->columnValues[ $last_entry_index ] ) < 2 ) {
        return new WP_Error( 'data_format_error', __( 'Unexpected data structure for silver price: Invalid last entry in columnValues.', 'masjid-nisab-widget' ) );
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
    }
    
    return array(
        'price' => $silver_price,
        'date'  => $generated_on_date,
    );
}
?>
