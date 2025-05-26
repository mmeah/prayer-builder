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
            __( 'Nisab Calculator', 'masjid-nisab-widget' ), // Name
            array( 'description' => __( 'Displays Mahr Fatimi, Minimum Mahr, and Zakaah Nisab based on live silver prices.', 'masjid-nisab-widget' ) ) // Args
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Nisab Calculator', 'masjid-nisab-widget' );
        echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];

        $silver_data = masjid_nisab_fetch_silver_price();

        if ( is_wp_error( $silver_data ) ) {
            printf(
                '<p>%s %s</p>',
                esc_html__( 'Error: Could not retrieve Nisab data.', 'masjid-nisab-widget' ),
                esc_html( $silver_data->get_error_message() )
            );
        } elseif ( empty( $silver_data['price'] ) || empty( $silver_data['date'] ) ) {
            echo '<p>' . esc_html__( 'Error: Insufficient data to calculate Nisab.', 'masjid-nisab-widget' ) . '</p>';
        } else {
            $silver_price = floatval( $silver_data['price'] );
            $update_date  = $silver_data['date']; // Already formatted by the API function potentially

            $mahr_fatimi  = 47.249 * $silver_price;
            $minimum_mahr = 0.984 * $silver_price;
            $zakaah_nisab = 19.688 * $silver_price;
            
            // Format date using WordPress internationalization functions
            $formatted_update_date = date_i18n( get_option( 'date_format' ), strtotime( $update_date ) );

            ?>
            <table id='nisab_content' class='textwidget' border='0' style='width:100%;'>
                <tr>
                    <td><strong><?php esc_html_e( 'Mahr Fatimi:', 'masjid-nisab-widget' ); ?></strong></td>
                    <td style="text-align: right;"><span>$<?php echo number_format( $mahr_fatimi, 2 ); ?></span></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Minimum Mahr:', 'masjid-nisab-widget' ); ?></strong></td>
                    <td style="text-align: right;"><span>$<?php echo number_format( $minimum_mahr, 2 ); ?></span></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Zakaah Nisab:', 'masjid-nisab-widget' ); ?></strong></td>
                    <td style="text-align: right;"><span>$<?php echo number_format( $zakaah_nisab, 2 ); ?></span></td>
                </tr>
                <tr>
                    <td colspan='2'><small><?php esc_html_e( 'Updated', 'masjid-nisab-widget' ); ?> <?php echo esc_html( $formatted_update_date ); ?></small></td>
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Nisab Calculator', 'masjid-nisab-widget' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'masjid-nisab-widget' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p><?php esc_html_e( 'This widget automatically calculates Nisab values based on live silver prices and has no other configurable options.', 'masjid-nisab-widget' ); ?></p>
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
} // End class
?>
