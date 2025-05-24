<?php
/**
 * Adds Masjid_Nisab_Widget widget.
 */
class Masjid_Nisab_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'masjid_nisab_widget', // Base ID
            esc_html__( 'Nisab Calculator', 'masjid-prayer-times' ), // Name
            array( 'description' => esc_html__( 'Displays Mahr Fatimi, Minimum Mahr, and Zakaah Nisab based on current silver prices from Lear Capital.', 'masjid-prayer-times' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        // Use the title from the widget instance if set by user, or use the default title set in the constructor.
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Nisab Calculator', 'masjid-prayer-times' );
        echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];

        $silver_data = masjid_nisab_fetch_silver_price();

        if ( is_wp_error( $silver_data ) ) {
            printf(
                '<p>%s %s</p>',
                esc_html__( 'Error: Could not retrieve Nisab data.', 'masjid-prayer-times' ),
                esc_html( $silver_data->get_error_message() )
            );
        } elseif ( ! isset( $silver_data['price'] ) || ! isset( $silver_data['date'] ) ) {
            echo '<p>' . esc_html__( 'Error: Nisab data is incomplete or unavailable.', 'masjid-prayer-times' ) . '</p>';
        } else {
            $silver_price = floatval( $silver_data['price'] );
            $update_date  = $silver_data['date'];

            // Perform calculations
            $mahr_fatimi  = 47.249 * $silver_price;
            $minimum_mahr = 0.984 * $silver_price;
            $zakaah_nisab = 19.688 * $silver_price;
            
            // Format the date for display
            // Assuming $update_date is in 'YYYY-MM-DD HH:MM:SS' or similar format
            $formatted_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $update_date ) );
            if ( strpos( $update_date, ':' ) === false ) { // If it's just a date string without time
                 $formatted_date = date_i18n( get_option( 'date_format' ), strtotime( $update_date ) );
            }


            ?>
            <table id='nisab_content' class='textwidget' border='0' style='width:100%;'>
                <tr>
                    <td><strong><?php esc_html_e( 'Mahr Fatimi:', 'masjid-prayer-times' ); ?></strong></td>
                    <td><span>$<?php echo number_format( $mahr_fatimi, 2 ); ?></span></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Minimum Mahr:', 'masjid-prayer-times' ); ?></strong></td>
                    <td><span>$<?php echo number_format( $minimum_mahr, 2 ); ?></span></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Zakaah Nisab:', 'masjid-prayer-times' ); ?></strong></td>
                    <td><span>$<?php echo number_format( $zakaah_nisab, 2 ); ?></span></td>
                </tr>
                <tr>
                    <td colspan='2'><small><?php esc_html_e( 'Updated', 'masjid-prayer-times' ); ?> <?php echo esc_html( $formatted_date ); ?></small></td>
                </tr>
            </table>
            <?php
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Nisab Calculator', 'masjid-prayer-times' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'masjid-prayer-times' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p><?php esc_html_e( 'This widget automatically calculates Nisab values based on live silver prices and has no other configurable options.', 'masjid-prayer-times' ); ?></p>
        <?php 
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        return $instance;
    }

} // class Masjid_Nisab_Widget
?>
