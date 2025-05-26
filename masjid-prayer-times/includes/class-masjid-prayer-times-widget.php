<?php
/**
 * Adds Masjid_Prayer_Times_Widget widget.
 */
class Masjid_Prayer_Times_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'masjid_prayer_times_widget', // Base ID
            esc_html__( 'Masjid Prayer Times', 'masjid-prayer-times' ), // Name
            array( 'description' => esc_html__( 'Displays prayer times from masjidal.com.', 'masjid-prayer-times' ), ) // Args
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
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $masjid_id = ! empty( $instance['masjid_id'] ) ? $instance['masjid_id'] : '';

        if ( empty( $masjid_id ) ) {
            echo '<p>' . esc_html__( 'Please configure the Masjid ID in the widget settings.', 'masjid-prayer-times' ) . '</p>';
            echo $args['after_widget'];
            return;
        }

        $prayer_data = masjid_prayer_times_fetch_data( $masjid_id );

        if ( is_wp_error( $prayer_data ) ) {
            echo '<p>' . esc_html__( 'Error fetching prayer times: ', 'masjid-prayer-times' ) . esc_html( $prayer_data->get_error_message() ) . '</p>';
        } elseif ( empty( $prayer_data ) || ! isset( $prayer_data['salah'][0] ) || ! isset( $prayer_data['iqamah'][0] ) ) {
            echo '<p>' . esc_html__( 'Could not retrieve prayer times. Please check the Masjid ID or try again later.', 'masjid-prayer-times' ) . '</p>';
        } else {
            $salah_times = $prayer_data['salah'][0];
            $iqamah_times = $prayer_data['iqamah'][0];

            echo '<div class="prayer-times-widget">';

            // Display Dates
            if ( ! empty( $salah_times['date'] ) && ! empty( $salah_times['hijri_date'] ) && ! empty( $salah_times['hijri_month'] ) ) {
                echo '<p class="prayer-date"><strong>' . esc_html__( 'Date:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['date'] ) .
                     ' (' . esc_html( $salah_times['hijri_date'] ) . ' ' . esc_html( $salah_times['hijri_month'] ) . ')</p>';
            }

            // Display Salah/Adhan Times
            echo '<h4>' . esc_html__( 'Salah/Adhan', 'masjid-prayer-times' ) . '</h4>';
            echo '<ul>';
            if ( isset( $salah_times['fajr'] ) ) echo '<li><strong>' . esc_html__( 'Fajr:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['fajr'] ) . '</li>';
            if ( isset( $salah_times['shurooq'] ) ) echo '<li><strong>' . esc_html__( 'Sunrise:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['shurooq'] ) . '</li>';
            if ( isset( $salah_times['zuhr'] ) ) echo '<li><strong>' . esc_html__( 'Zuhr:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['zuhr'] ) . '</li>';
            if ( isset( $salah_times['asr'] ) ) echo '<li><strong>' . esc_html__( 'Asr:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['asr'] ) . '</li>';
            if ( isset( $salah_times['maghrib'] ) ) echo '<li><strong>' . esc_html__( 'Maghrib:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['maghrib'] ) . '</li>';
            if ( isset( $salah_times['isha'] ) ) echo '<li><strong>' . esc_html__( 'Isha:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $salah_times['isha'] ) . '</li>';
            echo '</ul>';

            // Display Iqamah Times
            echo '<h4>' . esc_html__( 'Iqamah', 'masjid-prayer-times' ) . '</h4>';
            echo '<ul>';
            if ( isset( $iqamah_times['fajr'] ) ) echo '<li><strong>' . esc_html__( 'Fajr:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['fajr'] ) . '</li>';
            if ( isset( $iqamah_times['zuhr'] ) ) echo '<li><strong>' . esc_html__( 'Zuhr:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['zuhr'] ) . '</li>';
            if ( isset( $iqamah_times['asr'] ) ) echo '<li><strong>' . esc_html__( 'Asr:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['asr'] ) . '</li>';
            if ( isset( $iqamah_times['maghrib'] ) ) echo '<li><strong>' . esc_html__( 'Maghrib:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['maghrib'] ) . '</li>';
            if ( isset( $iqamah_times['isha'] ) ) echo '<li><strong>' . esc_html__( 'Isha:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['isha'] ) . '</li>';
            echo '</ul>';

            // Display Jummah Times
            $jummah_times_display = array();
            if ( ! empty( $iqamah_times['jummah1'] ) ) {
                $jummah_times_display[] = '<strong>' . esc_html__( 'Jummah 1:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['jummah1'] );
            }
            if ( ! empty( $iqamah_times['jummah2'] ) ) {
                $jummah_times_display[] = '<strong>' . esc_html__( 'Jummah 2:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['jummah2'] );
            }
             if ( ! empty( $iqamah_times['jummah3'] ) ) { // As per API docs, jummah3 can exist
                $jummah_times_display[] = '<strong>' . esc_html__( 'Jummah 3:', 'masjid-prayer-times' ) . '</strong> ' . esc_html( $iqamah_times['jummah3'] );
            }

            if ( ! empty( $jummah_times_display ) ) {
                echo '<h4>' . esc_html__( 'Jummah', 'masjid-prayer-times' ) . '</h4>';
                echo '<ul><li>' . implode( '</li><li>', $jummah_times_display ) . '</li></ul>';
            }
            
            echo '</div>'; // .prayer-times-widget
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
        $masjid_id = ! empty( $instance['masjid_id'] ) ? $instance['masjid_id'] : '';
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'masjid_id' ) ); ?>"><?php esc_attr_e( 'Masjid ID:', 'masjid-prayer-times' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'masjid_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'masjid_id' ) ); ?>" type="text" value="<?php echo esc_attr( $masjid_id ); ?>">
        </p>
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
        $instance['masjid_id'] = ( ! empty( $new_instance['masjid_id'] ) ) ? sanitize_text_field( $new_instance['masjid_id'] ) : '';

        return $instance;
    }

} // class Masjid_Prayer_Times_Widget
?>
