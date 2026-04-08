<?php
/**
 * Residence I Mari — functions.php
 *
 * Theme setup, enqueue, CPT, meta boxes, Customizer, AJAX, REST API.
 *
 * @package Residence_I_Mari
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ─────────────────────────────────────────────
 * 1. THEME SETUP
 * ───────────────────────────────────────────── */
add_action( 'after_setup_theme', 'rim_theme_setup' );
function rim_theme_setup() {

    // Titolo gestito da WP
    add_theme_support( 'title-tag' );

    // Immagini in evidenza
    add_theme_support( 'post-thumbnails' );

    // Custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 280,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Navigazione principale
    register_nav_menus( array(
        'primary' => __( 'Menu Principale', 'residence-i-mari' ),
    ) );
}

/* ─────────────────────────────────────────────
 * 2. ENQUEUE STYLES & SCRIPTS
 * ───────────────────────────────────────────── */
add_action( 'wp_enqueue_scripts', 'rim_enqueue_assets' );
function rim_enqueue_assets() {

    $ver = wp_get_theme()->get( 'Version' );

    // Google Fonts — Jost + Cormorant Garamond
    wp_enqueue_style(
        'rim-google-fonts',
        'https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap',
        array(),
        null
    );

    // CSS principali
    wp_enqueue_style(
        'rim-main-style',
        get_theme_file_uri( 'css/style.css' ),
        array( 'rim-google-fonts' ),
        $ver
    );

    wp_enqueue_style(
        'rim-apartment-style',
        get_theme_file_uri( 'css/apartment.css' ),
        array( 'rim-main-style' ),
        $ver
    );

    // JS — main
    wp_enqueue_script(
        'rim-main-js',
        get_theme_file_uri( 'js/main.js' ),
        array(),
        $ver,
        true
    );

    // JS — booking
    wp_enqueue_script(
        'rim-booking-js',
        get_theme_file_uri( 'js/booking.js' ),
        array(),
        $ver,
        true
    );

    // Passa variabili AJAX a booking.js
    wp_localize_script( 'rim-booking-js', 'rimAjax', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'restUrl' => rest_url( 'rim/v1/booking' ),
        'nonce'   => wp_create_nonce( 'rim_booking_nonce' ),
    ) );

    // JS — apartment (solo su single-appartamento)
    if ( is_singular( 'appartamento' ) ) {
        wp_enqueue_script(
            'rim-apartment-js',
            get_theme_file_uri( 'js/apartment.js' ),
            array(),
            $ver,
            true
        );
    }
}

/* ─────────────────────────────────────────────
 * 3. CUSTOM POST TYPE — APPARTAMENTO
 * ───────────────────────────────────────────── */
add_action( 'init', 'rim_register_cpt_appartamento' );
function rim_register_cpt_appartamento() {

    $labels = array(
        'name'               => __( 'Appartamenti', 'residence-i-mari' ),
        'singular_name'      => __( 'Appartamento', 'residence-i-mari' ),
        'add_new'            => __( 'Aggiungi Nuovo', 'residence-i-mari' ),
        'add_new_item'       => __( 'Aggiungi Appartamento', 'residence-i-mari' ),
        'edit_item'          => __( 'Modifica Appartamento', 'residence-i-mari' ),
        'new_item'           => __( 'Nuovo Appartamento', 'residence-i-mari' ),
        'view_item'          => __( 'Visualizza Appartamento', 'residence-i-mari' ),
        'search_items'       => __( 'Cerca Appartamenti', 'residence-i-mari' ),
        'not_found'          => __( 'Nessun appartamento trovato', 'residence-i-mari' ),
        'not_found_in_trash' => __( 'Nessun appartamento nel cestino', 'residence-i-mari' ),
        'menu_name'          => __( 'Appartamenti', 'residence-i-mari' ),
    );

    $args = array(
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'appartamenti' ),
        'menu_icon'    => 'dashicons-admin-home',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest' => true,
    );

    register_post_type( 'appartamento', $args );
}

/* ─────────────────────────────────────────────
 * 4. META BOXES — Campi custom (no ACF)
 * ───────────────────────────────────────────── */
add_action( 'add_meta_boxes', 'rim_add_apartment_meta_boxes' );
function rim_add_apartment_meta_boxes() {

    add_meta_box(
        'rim_apartment_details',
        __( 'Dettagli Appartamento', 'residence-i-mari' ),
        'rim_apartment_details_callback',
        'appartamento',
        'normal',
        'high'
    );

    add_meta_box(
        'rim_apartment_gallery',
        __( 'Galleria Immagini', 'residence-i-mari' ),
        'rim_apartment_gallery_callback',
        'appartamento',
        'normal',
        'default'
    );

    add_meta_box(
        'rim_apartment_floorplan',
        __( 'Planimetria', 'residence-i-mari' ),
        'rim_apartment_floorplan_callback',
        'appartamento',
        'normal',
        'default'
    );
}

/**
 * Render meta box: Dettagli Appartamento.
 */
function rim_apartment_details_callback( $post ) {
    wp_nonce_field( 'rim_apartment_meta', 'rim_apartment_meta_nonce' );

    $fields = array(
        'rim_sqm'               => array(
            'label' => __( 'Superficie (mq)', 'residence-i-mari' ),
            'type'  => 'number',
        ),
        'rim_guests'            => array(
            'label' => __( 'Ospiti max', 'residence-i-mari' ),
            'type'  => 'number',
        ),
        'rim_rooms'             => array(
            'label' => __( 'Camere', 'residence-i-mari' ),
            'type'  => 'number',
        ),
        'rim_short_description' => array(
            'label' => __( 'Descrizione breve', 'residence-i-mari' ),
            'type'  => 'textarea',
        ),
        'rim_amenities'         => array(
            'label' => __( 'Servizi (separati da virgola)', 'residence-i-mari' ),
            'type'  => 'text',
        ),
        'rim_airbnb_url'        => array(
            'label' => __( 'URL Airbnb', 'residence-i-mari' ),
            'type'  => 'url',
        ),
    );

    echo '<table class="form-table"><tbody>';

    foreach ( $fields as $key => $field ) {
        $value = get_post_meta( $post->ID, $key, true );
        echo '<tr>';
        echo '<th><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th>';
        echo '<td>';

        switch ( $field['type'] ) {
            case 'textarea':
                echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" class="large-text">' . esc_textarea( $value ) . '</textarea>';
                break;

            case 'number':
                echo '<input type="number" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="small-text" min="0">';
                break;

            case 'url':
                echo '<input type="url" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_url( $value ) . '" class="regular-text">';
                break;

            default:
                echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text">';
                break;
        }

        echo '</td></tr>';
    }

    echo '</tbody></table>';
}

/**
 * Render meta box: Galleria Immagini.
 *
 * Salva un array di attachment ID in rim_gallery.
 */
function rim_apartment_gallery_callback( $post ) {
    $gallery_ids = get_post_meta( $post->ID, 'rim_gallery', true );
    $gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : array();
    ?>
    <div id="rim-gallery-container">
        <ul id="rim-gallery-list" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;">
            <?php foreach ( $gallery_ids as $id ) :
                $thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
                if ( $thumb ) : ?>
                    <li data-id="<?php echo esc_attr( $id ); ?>" style="position:relative;">
                        <img src="<?php echo esc_url( $thumb ); ?>" width="100" height="100" style="object-fit:cover;border-radius:4px;">
                        <button type="button" class="rim-gallery-remove" style="position:absolute;top:-6px;right:-6px;background:#d63638;color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:14px;line-height:1;">&times;</button>
                        <input type="hidden" name="rim_gallery[]" value="<?php echo esc_attr( $id ); ?>">
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <button type="button" id="rim-gallery-add" class="button"><?php esc_html_e( 'Aggiungi Immagini', 'residence-i-mari' ); ?></button>
    </div>

    <script>
    (function($){
        var frame;
        $('#rim-gallery-add').on('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: '<?php echo esc_js( __( 'Seleziona Immagini', 'residence-i-mari' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Aggiungi alla galleria', 'residence-i-mari' ) ); ?>' },
                multiple: true
            });
            frame.on('select', function(){
                var attachments = frame.state().get('selection').toJSON();
                $.each(attachments, function(i, att){
                    var li = '<li data-id="'+att.id+'" style="position:relative;">' +
                        '<img src="'+(att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url)+'" width="100" height="100" style="object-fit:cover;border-radius:4px;">' +
                        '<button type="button" class="rim-gallery-remove" style="position:absolute;top:-6px;right:-6px;background:#d63638;color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:14px;line-height:1;">&times;</button>' +
                        '<input type="hidden" name="rim_gallery[]" value="'+att.id+'">' +
                        '</li>';
                    $('#rim-gallery-list').append(li);
                });
            });
            frame.open();
        });
        $(document).on('click', '.rim-gallery-remove', function(){
            $(this).closest('li').remove();
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * Render meta box: Planimetria (singola immagine).
 */
function rim_apartment_floorplan_callback( $post ) {
    $fp_id  = get_post_meta( $post->ID, 'rim_floor_plan', true );
    $fp_url = $fp_id ? wp_get_attachment_image_url( $fp_id, 'medium' ) : '';
    ?>
    <div id="rim-floorplan-container">
        <div id="rim-floorplan-preview" style="margin-bottom:10px;<?php echo $fp_url ? '' : 'display:none;'; ?>">
            <img src="<?php echo esc_url( $fp_url ); ?>" style="max-width:300px;border-radius:4px;">
            <br>
            <button type="button" id="rim-floorplan-remove" class="button" style="margin-top:6px;color:#d63638;">Rimuovi planimetria</button>
        </div>
        <input type="hidden" name="rim_floor_plan" id="rim-floorplan-id" value="<?php echo esc_attr( $fp_id ); ?>">
        <button type="button" id="rim-floorplan-add" class="button"<?php echo $fp_url ? ' style="display:none;"' : ''; ?>>Seleziona Planimetria</button>
        <p class="description">Immagine della planimetria dell'appartamento (viene mostrata nella pagina dell'appartamento).</p>
    </div>
    <script>
    (function($){
        var fpFrame;
        $('#rim-floorplan-add').on('click', function(e){
            e.preventDefault();
            if (fpFrame) { fpFrame.open(); return; }
            fpFrame = wp.media({ title: 'Seleziona Planimetria', button: { text: 'Usa come planimetria' }, multiple: false });
            fpFrame.on('select', function(){
                var att = fpFrame.state().get('selection').first().toJSON();
                var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
                $('#rim-floorplan-id').val(att.id);
                $('#rim-floorplan-preview img').attr('src', url);
                $('#rim-floorplan-preview').show();
                $('#rim-floorplan-add').hide();
            });
            fpFrame.open();
        });
        $('#rim-floorplan-remove').on('click', function(){
            $('#rim-floorplan-id').val('');
            $('#rim-floorplan-preview').hide();
            $('#rim-floorplan-add').show();
        });
    })(jQuery);
    </script>
    <?php
}

/* ─────────────────────────────────────────────
 * 5. SAVE META BOX DATA
 * ───────────────────────────────────────────── */
add_action( 'save_post_appartamento', 'rim_save_apartment_meta' );
function rim_save_apartment_meta( $post_id ) {

    // Verifiche di sicurezza
    if ( ! isset( $_POST['rim_apartment_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['rim_apartment_meta_nonce'], 'rim_apartment_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Campi testuali / numerici
    $text_fields = array(
        'rim_sqm',
        'rim_guests',
        'rim_rooms',
        'rim_short_description',
        'rim_amenities',
    );

    foreach ( $text_fields as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }

    // URL Airbnb
    if ( isset( $_POST['rim_airbnb_url'] ) ) {
        update_post_meta( $post_id, 'rim_airbnb_url', esc_url_raw( wp_unslash( $_POST['rim_airbnb_url'] ) ) );
    }

    // Galleria
    if ( isset( $_POST['rim_gallery'] ) && is_array( $_POST['rim_gallery'] ) ) {
        $ids = array_map( 'absint', $_POST['rim_gallery'] );
        update_post_meta( $post_id, 'rim_gallery', $ids );
    } else {
        delete_post_meta( $post_id, 'rim_gallery' );
    }

    // Planimetria
    if ( isset( $_POST['rim_floor_plan'] ) ) {
        $fp_id = absint( $_POST['rim_floor_plan'] );
        if ( $fp_id ) {
            update_post_meta( $post_id, 'rim_floor_plan', $fp_id );
        } else {
            delete_post_meta( $post_id, 'rim_floor_plan' );
        }
    }
}

/* ─────────────────────────────────────────────
 * 6. CUSTOMIZER — Impostazioni contatto
 * ───────────────────────────────────────────── */
add_action( 'customize_register', 'rim_customizer_settings' );
function rim_customizer_settings( $wp_customize ) {

    // Sezione: Contatti Residence
    $wp_customize->add_section( 'rim_contact_info', array(
        'title'    => __( 'Contatti Residence', 'residence-i-mari' ),
        'priority' => 30,
    ) );

    $fields = array(
        'rim_phone'                => array(
            'label'   => __( 'Telefono fisso', 'residence-i-mari' ),
            'default' => '0564 937081',
        ),
        'rim_cellphone'            => array(
            'label'   => __( 'Cellulare', 'residence-i-mari' ),
            'default' => '338 8625775',
        ),
        'rim_winter_phone'         => array(
            'label'   => __( 'Telefono invernale', 'residence-i-mari' ),
            'default' => '0564 932566',
        ),
        'rim_email'                => array(
            'label'   => __( 'Email', 'residence-i-mari' ),
            'default' => 'residenceimari@gmail.com',
            'type'    => 'email',
        ),
        'rim_address'              => array(
            'label'   => __( 'Indirizzo', 'residence-i-mari' ),
            'default' => 'Via Montecristo 7, 58043 Castiglione della Pescaia (GR)',
        ),
        'rim_google_maps_embed_url' => array(
            'label'   => __( 'URL embed Google Maps', 'residence-i-mari' ),
            'default' => '',
        ),
        'rim_google_rating'        => array(
            'label'   => __( 'Valutazione Google', 'residence-i-mari' ),
            'default' => '',
        ),
        'rim_google_reviews_count' => array(
            'label'   => __( 'Numero recensioni Google', 'residence-i-mari' ),
            'default' => '',
        ),
    );

    foreach ( $fields as $key => $field ) {
        $wp_customize->add_setting( $key, array(
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $control_args = array(
            'label'   => $field['label'],
            'section' => 'rim_contact_info',
        );

        if ( isset( $field['type'] ) && 'email' === $field['type'] ) {
            $control_args['type'] = 'email';
        }

        $wp_customize->add_control( $key, $control_args );
    }

    // ── Sezione: Homepage — Il Residence ──
    $wp_customize->add_section( 'rim_homepage_intro', array(
        'title'    => __( 'Homepage — Il Residence', 'residence-i-mari' ),
        'priority' => 31,
    ) );

    $intro_fields = array(
        'rim_intro_title' => array(
            'label'   => __( 'Titolo sezione', 'residence-i-mari' ),
            'default' => 'Un angolo di Maremma<br>a due passi dal mare',
            'type'    => 'text',
        ),
        'rim_intro_p1' => array(
            'label'   => __( 'Paragrafo 1', 'residence-i-mari' ),
            'default' => 'Il Residence I Mari è un complesso di <strong>9 appartamenti di recentissima ristrutturazione</strong>, situato a soli 100 metri dal mare nel cuore di Castiglione della Pescaia, una delle località più belle della costa toscana.',
            'type'    => 'textarea',
        ),
        'rim_intro_p2' => array(
            'label'   => __( 'Paragrafo 2', 'residence-i-mari' ),
            'default' => 'Ogni appartamento è stato progettato con cura: ampie finestre luminose, climatizzatori, pavimenti in parquet o ceramica di Vietri, TV satellite e cassaforte. L\'arredamento è studiato per coniugare praticità ed eleganza.',
            'type'    => 'textarea',
        ),
        'rim_intro_p3' => array(
            'label'   => __( 'Paragrafo 3', 'residence-i-mari' ),
            'default' => 'A soli 50 metri troverete alimentari, bar, farmacia, ristoranti e tutti i servizi. La spiaggia con stabilimenti balneari convenzionati è raggiungibile comodamente a piedi.',
            'type'    => 'textarea',
        ),
    );

    foreach ( $intro_fields as $key => $field ) {
        $wp_customize->add_setting( $key, array(
            'default'           => $field['default'],
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'refresh',
        ) );

        $control_args = array(
            'label'   => $field['label'],
            'section' => 'rim_homepage_intro',
        );

        if ( 'textarea' === $field['type'] ) {
            $control_args['type'] = 'textarea';
        }

        $wp_customize->add_control( $key, $control_args );
    }

    // ── Sezione: Homepage — Posizione ──
    $wp_customize->add_section( 'rim_homepage_location', array(
        'title'    => __( 'Homepage — Posizione', 'residence-i-mari' ),
        'priority' => 32,
    ) );

    $wp_customize->add_setting( 'rim_location_desc', array(
        'default'           => 'Il Residence I Mari si trova in <strong>Via Montecristo 7</strong>, in una posizione privilegiata a pochi passi dal centro e dalla spiaggia. La località è una delle perle della Maremma Toscana, premiata con la Bandiera Blu per la qualità delle acque.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_location_desc', array(
        'label'   => __( 'Descrizione posizione', 'residence-i-mari' ),
        'section' => 'rim_homepage_location',
        'type'    => 'textarea',
    ) );

    // ── Sezione: Homepage — Hero ──
    $wp_customize->add_section( 'rim_homepage_hero', array(
        'title'    => __( 'Homepage — Hero', 'residence-i-mari' ),
        'priority' => 29,
    ) );

    $wp_customize->add_setting( 'rim_hero_pretitle', array(
        'default'           => 'Benvenuti al',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_hero_pretitle', array(
        'label'   => __( 'Pre-titolo hero', 'residence-i-mari' ),
        'section' => 'rim_homepage_hero',
    ) );

    $wp_customize->add_setting( 'rim_hero_title', array(
        'default'           => 'Residence I Mari',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_hero_title', array(
        'label'   => __( 'Titolo hero', 'residence-i-mari' ),
        'section' => 'rim_homepage_hero',
    ) );

    $wp_customize->add_setting( 'rim_hero_subtitle', array(
        'default'           => 'A 100 metri dal mare — Castiglione della Pescaia',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_hero_subtitle', array(
        'label'   => __( 'Sottotitolo hero', 'residence-i-mari' ),
        'section' => 'rim_homepage_hero',
    ) );
}

/* ─────────────────────────────────────────────
 * 7. BOOKING FORM — AJAX HANDLER
 * ───────────────────────────────────────────── */
add_action( 'wp_ajax_rim_booking_submit', 'rim_handle_booking_ajax' );
add_action( 'wp_ajax_nopriv_rim_booking_submit', 'rim_handle_booking_ajax' );

function rim_handle_booking_ajax() {

    // Verifica nonce
    if ( ! check_ajax_referer( 'rim_booking_nonce', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => __( 'Errore di sicurezza. Ricarica la pagina e riprova.', 'residence-i-mari' ) ) );
    }

    $result = rim_process_booking( $_POST );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ) );
    }

    wp_send_json_success( array( 'message' => __( 'Richiesta inviata con successo! Ti risponderemo entro 24 ore.', 'residence-i-mari' ) ) );
}

/**
 * Processa i dati del form di prenotazione e invia email.
 *
 * @param  array $data Dati del form.
 * @return true|WP_Error
 */
function rim_process_booking( $data ) {

    // Validazione campi obbligatori
    $name  = isset( $data['name'] )  ? sanitize_text_field( wp_unslash( $data['name'] ) )  : '';
    $email = isset( $data['email'] ) ? sanitize_email( wp_unslash( $data['email'] ) )      : '';

    if ( empty( $name ) || empty( $email ) ) {
        return new WP_Error( 'missing_fields', __( 'Nome e email sono obbligatori.', 'residence-i-mari' ) );
    }

    // Raccolta dati facoltativi
    $phone     = isset( $data['phone'] )     ? sanitize_text_field( wp_unslash( $data['phone'] ) )     : '';
    $checkin   = isset( $data['checkin'] )    ? sanitize_text_field( wp_unslash( $data['checkin'] ) )   : '';
    $checkout  = isset( $data['checkout'] )   ? sanitize_text_field( wp_unslash( $data['checkout'] ) )  : '';
    $apartment = isset( $data['apartment'] )  ? sanitize_text_field( wp_unslash( $data['apartment'] ) ) : '';
    $adults    = isset( $data['adults'] )     ? absint( $data['adults'] )   : 0;
    $children  = isset( $data['children'] )   ? absint( $data['children'] ) : 0;
    $message   = isset( $data['message'] )    ? sanitize_textarea_field( wp_unslash( $data['message'] ) ) : '';

    // Email al gestore
    $to      = get_theme_mod( 'rim_email', get_option( 'admin_email' ) );
    $subject = sprintf(
        /* translators: %s: nome del richiedente */
        __( '[Residence I Mari] Richiesta prenotazione da %s', 'residence-i-mari' ),
        $name
    );

    $body  = __( 'Nuova richiesta di prenotazione', 'residence-i-mari' ) . "\n\n";
    $body .= __( 'Nome:', 'residence-i-mari' )          . ' ' . $name . "\n";
    $body .= __( 'Email:', 'residence-i-mari' )         . ' ' . $email . "\n";
    $body .= __( 'Telefono:', 'residence-i-mari' )      . ' ' . $phone . "\n";
    $body .= __( 'Check-in:', 'residence-i-mari' )      . ' ' . $checkin . "\n";
    $body .= __( 'Check-out:', 'residence-i-mari' )     . ' ' . $checkout . "\n";
    $body .= __( 'Appartamento:', 'residence-i-mari' )  . ' ' . $apartment . "\n";
    $body .= __( 'Adulti:', 'residence-i-mari' )        . ' ' . $adults . "\n";
    $body .= __( 'Bambini:', 'residence-i-mari' )       . ' ' . $children . "\n";

    if ( ! empty( $message ) ) {
        $body .= "\n" . __( 'Messaggio:', 'residence-i-mari' ) . "\n" . $message . "\n";
    }

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( ! $sent ) {
        return new WP_Error( 'mail_failed', __( 'Errore nell\'invio dell\'email. Riprova o contattaci telefonicamente.', 'residence-i-mari' ) );
    }

    return true;
}

/* ─────────────────────────────────────────────
 * 8. REST API — Booking endpoint
 * ───────────────────────────────────────────── */
add_action( 'rest_api_init', 'rim_register_rest_routes' );
function rim_register_rest_routes() {

    register_rest_route( 'rim/v1', '/booking', array(
        'methods'             => 'POST',
        'callback'            => 'rim_rest_booking_handler',
        'permission_callback' => '__return_true',
    ) );
}

/**
 * Handler REST per il form di prenotazione.
 */
function rim_rest_booking_handler( WP_REST_Request $request ) {

    $nonce = $request->get_header( 'X-WP-Nonce' );

    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        // Fallback: verifica nonce custom nel body
        $body_nonce = $request->get_param( 'nonce' );
        if ( ! $body_nonce || ! wp_verify_nonce( $body_nonce, 'rim_booking_nonce' ) ) {
            return new WP_REST_Response(
                array( 'success' => false, 'message' => __( 'Errore di sicurezza.', 'residence-i-mari' ) ),
                403
            );
        }
    }

    $result = rim_process_booking( $request->get_params() );

    if ( is_wp_error( $result ) ) {
        return new WP_REST_Response(
            array( 'success' => false, 'message' => $result->get_error_message() ),
            400
        );
    }

    return new WP_REST_Response(
        array( 'success' => true, 'message' => __( 'Richiesta inviata con successo!', 'residence-i-mari' ) ),
        200
    );
}

/* ─────────────────────────────────────────────
 * 9. WIDGET AREAS
 * ───────────────────────────────────────────── */
add_action( 'widgets_init', 'rim_register_widget_areas' );
function rim_register_widget_areas() {

    register_sidebar( array(
        'name'          => __( 'Footer - Colonna 1', 'residence-i-mari' ),
        'id'            => 'footer-1',
        'description'   => __( 'Widget area per la prima colonna del footer.', 'residence-i-mari' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer - Colonna 2', 'residence-i-mari' ),
        'id'            => 'footer-2',
        'description'   => __( 'Widget area per la seconda colonna del footer.', 'residence-i-mari' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer - Colonna 3', 'residence-i-mari' ),
        'id'            => 'footer-3',
        'description'   => __( 'Widget area per la terza colonna del footer.', 'residence-i-mari' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
}

/* ─────────────────────────────────────────────
 * 10. MOBILE BOTTOM BAR SUPPORT
 * ───────────────────────────────────────────── */

/**
 * Aggiunge body class per la bottom bar mobile.
 */
add_filter( 'body_class', 'rim_body_classes' );
function rim_body_classes( $classes ) {
    if ( wp_is_mobile() ) {
        $classes[] = 'has-mobile-bottom-bar';
    }
    return $classes;
}

/* ─────────────────────────────────────────────
 * 11. HELPERS
 * ───────────────────────────────────────────── */

/**
 * Restituisce il numero di telefono formattato per link tel:.
 *
 * @param  string $phone Numero con spazi/trattini.
 * @return string        Numero pulito (solo cifre e +).
 */
function rim_phone_link( $phone ) {
    return preg_replace( '/[^\d+]/', '', $phone );
}

/**
 * Restituisce l'array di amenities di un appartamento.
 *
 * @param  int   $post_id Post ID.
 * @return array
 */
function rim_get_amenities( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $raw = get_post_meta( $post_id, 'rim_amenities', true );
    if ( empty( $raw ) ) {
        return array();
    }
    return array_map( 'trim', explode( ',', $raw ) );
}

/* ─────────────────────────────────────────────
 * 12. AVAILABILITY MANAGEMENT SYSTEM
 * ───────────────────────────────────────────── */

/**
 * Meta box: Gestione Disponibilità (admin).
 */
add_action( 'add_meta_boxes', 'rim_add_availability_meta_box' );
function rim_add_availability_meta_box() {
    add_meta_box(
        'rim_availability',
        __( 'Gestione Disponibilità', 'residence-i-mari' ),
        'rim_availability_meta_box_html',
        'appartamento',
        'normal',
        'high'
    );
}

function rim_availability_meta_box_html( $post ) {
    wp_nonce_field( 'rim_avail_action', 'rim_avail_nonce' );
    $booked = get_post_meta( $post->ID, 'rim_booked_dates', true );
    if ( ! is_array( $booked ) ) {
        $booked = array();
    }
    usort( $booked, function( $a, $b ) {
        return strcmp( $a['from'], $b['from'] );
    } );
    ?>
    <div id="rim-avail-manager" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
        <div class="rim-avail-add-form">
            <h4 style="margin-top:0;">Aggiungi periodo occupato</h4>
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:4px;">Dal</label>
                    <input type="date" id="rim-avail-from" style="padding:6px;">
                </div>
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:4px;">Al</label>
                    <input type="date" id="rim-avail-to" style="padding:6px;">
                </div>
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:4px;">Ospite</label>
                    <input type="text" id="rim-avail-guest" placeholder="Nome (opzionale)" style="padding:6px;width:160px;">
                </div>
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:4px;">Note</label>
                    <input type="text" id="rim-avail-note" placeholder="Note (opzionale)" style="padding:6px;width:160px;">
                </div>
                <button type="button" id="rim-avail-add-btn" class="button button-primary" style="height:36px;">Aggiungi Periodo</button>
            </div>
            <div id="rim-avail-feedback" style="margin-top:8px;display:none;padding:8px 12px;border-radius:4px;"></div>
        </div>
        <hr style="margin:20px 0;">
        <h4>Periodi occupati</h4>
        <table class="wp-list-table widefat striped" id="rim-avail-table">
            <thead>
                <tr><th>Dal</th><th>Al</th><th>Notti</th><th>Ospite</th><th>Note</th><th style="width:80px;">Azioni</th></tr>
            </thead>
            <tbody>
                <?php if ( empty( $booked ) ) : ?>
                    <tr class="rim-avail-empty"><td colspan="6" style="text-align:center;padding:20px;color:#888;">Nessun periodo occupato &mdash; l&rsquo;appartamento &egrave; completamente disponibile.</td></tr>
                <?php else : ?>
                    <?php
                    foreach ( $booked as $i => $period ) :
                        $from_dt   = new DateTime( $period['from'] );
                        $to_dt     = new DateTime( $period['to'] );
                        $nights    = $from_dt->diff( $to_dt )->days;
                        $past_attr = ( $to_dt < new DateTime( 'today' ) ) ? ' style="opacity:0.5;"' : '';
                    ?>
                        <tr data-index="<?php echo $i; ?>"<?php echo $past_attr; ?>>
                            <td><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $period['from'] ) ) ); ?></td>
                            <td><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $period['to'] ) ) ); ?></td>
                            <td><?php echo esc_html( $nights ); ?></td>
                            <td><?php echo esc_html( $period['guest'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $period['note'] ?? '' ); ?></td>
                            <td><button type="button" class="button rim-avail-remove" data-index="<?php echo $i; ?>">Rimuovi</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p style="margin-top:12px;color:#666;font-style:italic;">I periodi passati sono mostrati in trasparenza. Per liberare un periodo, clicca &ldquo;Rimuovi&rdquo;.</p>
    </div>
    <?php
}

/**
 * Enqueue admin scripts for availability management.
 */
add_action( 'admin_enqueue_scripts', 'rim_admin_availability_scripts' );
function rim_admin_availability_scripts( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    global $post;
    if ( ! $post || 'appartamento' !== $post->post_type ) {
        return;
    }
    wp_enqueue_script(
        'rim-admin-avail',
        get_theme_file_uri( 'js/admin-availability.js' ),
        array( 'jquery' ),
        '1.1',
        true
    );
    wp_localize_script( 'rim-admin-avail', 'rimAvail', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'rim_avail_action' ),
        'postId'  => $post->ID,
    ) );
}

/**
 * AJAX: Add blocked dates (admin).
 */
add_action( 'wp_ajax_rim_add_blocked_dates', 'rim_add_blocked_dates_handler' );
function rim_add_blocked_dates_handler() {
    check_ajax_referer( 'rim_avail_action', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Non autorizzato' );
    }

    $post_id = intval( $_POST['post_id'] );
    $from    = sanitize_text_field( $_POST['from'] );
    $to      = sanitize_text_field( $_POST['to'] );
    $guest   = sanitize_text_field( $_POST['guest'] ?? '' );
    $note    = sanitize_text_field( $_POST['note'] ?? '' );

    if ( ! $from || ! $to || $from >= $to ) {
        wp_send_json_error( 'Date non valide: la data di fine deve essere successiva.' );
    }

    $booked = get_post_meta( $post_id, 'rim_booked_dates', true );
    if ( ! is_array( $booked ) ) {
        $booked = array();
    }

    foreach ( $booked as $period ) {
        if ( $from < $period['to'] && $to > $period['from'] ) {
            wp_send_json_error(
                'Sovrapposizione con: ' .
                date_i18n( 'd/m/Y', strtotime( $period['from'] ) ) . ' - ' .
                date_i18n( 'd/m/Y', strtotime( $period['to'] ) )
            );
        }
    }

    $booked[] = array(
        'from'  => $from,
        'to'    => $to,
        'guest' => $guest,
        'note'  => $note,
    );
    usort( $booked, function( $a, $b ) {
        return strcmp( $a['from'], $b['from'] );
    } );

    update_post_meta( $post_id, 'rim_booked_dates', $booked );
    wp_send_json_success( array(
        'booked'  => $booked,
        'message' => 'Periodo aggiunto con successo',
    ) );
}

/**
 * AJAX: Remove blocked dates (admin).
 */
add_action( 'wp_ajax_rim_remove_blocked_dates', 'rim_remove_blocked_dates_handler' );
function rim_remove_blocked_dates_handler() {
    check_ajax_referer( 'rim_avail_action', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Non autorizzato' );
    }

    $post_id = intval( $_POST['post_id'] );
    $index   = intval( $_POST['index'] );

    $booked = get_post_meta( $post_id, 'rim_booked_dates', true );
    if ( ! is_array( $booked ) ) {
        wp_send_json_error( 'Nessun dato' );
    }

    if ( isset( $booked[ $index ] ) ) {
        array_splice( $booked, $index, 1 );
        update_post_meta( $post_id, 'rim_booked_dates', $booked );
    }

    wp_send_json_success( array( 'booked' => $booked ) );
}

/**
 * AJAX: Check availability for all apartments (frontend).
 */
add_action( 'wp_ajax_nopriv_rim_check_availability', 'rim_check_availability_handler' );
add_action( 'wp_ajax_rim_check_availability', 'rim_check_availability_handler' );
function rim_check_availability_handler() {
    $checkin  = sanitize_text_field( $_GET['checkin'] ?? '' );
    $checkout = sanitize_text_field( $_GET['checkout'] ?? '' );

    if ( ! $checkin || ! $checkout || $checkin >= $checkout ) {
        wp_send_json_error( 'Date non valide' );
    }

    $apartments = get_posts( array(
        'post_type'      => 'appartamento',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    $results = array();
    foreach ( $apartments as $apt ) {
        $booked = get_post_meta( $apt->ID, 'rim_booked_dates', true );
        if ( ! is_array( $booked ) ) {
            $booked = array();
        }

        $available = true;
        foreach ( $booked as $period ) {
            if ( $checkin < $period['to'] && $checkout > $period['from'] ) {
                $available = false;
                break;
            }
        }

        $thumb = get_the_post_thumbnail_url( $apt->ID, 'medium' );

        $results[] = array(
            'id'         => $apt->ID,
            'name'       => $apt->post_title,
            'available'  => $available,
            'short_desc' => get_post_meta( $apt->ID, 'rim_short_description', true ),
            'sqm'        => get_post_meta( $apt->ID, 'rim_sqm', true ),
            'guests'     => get_post_meta( $apt->ID, 'rim_guests', true ),
            'rooms'      => get_post_meta( $apt->ID, 'rim_rooms', true ),
            'thumb'      => $thumb ? $thumb : '',
        );
    }

    wp_send_json_success( array( 'apartments' => $results ) );
}

/**
 * AJAX: Send booking request email (frontend).
 */
add_action( 'wp_ajax_nopriv_rim_send_booking', 'rim_send_booking_handler' );
add_action( 'wp_ajax_rim_send_booking', 'rim_send_booking_handler' );
function rim_send_booking_handler() {
    check_ajax_referer( 'rim_booking_nonce', 'nonce' );

    $name      = sanitize_text_field( $_POST['name'] ?? '' );
    $email     = sanitize_email( $_POST['email'] ?? '' );
    $phone     = sanitize_text_field( $_POST['phone'] ?? '' );
    $apartment = sanitize_text_field( $_POST['apartment'] ?? '' );
    $apt_id    = intval( $_POST['apartment_id'] ?? 0 );
    $checkin   = sanitize_text_field( $_POST['checkin'] ?? '' );
    $checkout  = sanitize_text_field( $_POST['checkout'] ?? '' );
    $adults    = intval( $_POST['adults'] ?? 2 );
    $children  = intval( $_POST['children'] ?? 0 );
    $notes     = sanitize_textarea_field( $_POST['notes'] ?? '' );

    if ( ! $name || ! $email || ! $checkin || ! $checkout || ! $apartment ) {
        wp_send_json_error( 'Compila tutti i campi obbligatori.' );
    }

    // Double-check availability
    if ( $apt_id ) {
        $booked = get_post_meta( $apt_id, 'rim_booked_dates', true );
        if ( is_array( $booked ) ) {
            foreach ( $booked as $period ) {
                if ( $checkin < $period['to'] && $checkout > $period['from'] ) {
                    wp_send_json_error( 'L\'appartamento non e\' piu\' disponibile per le date selezionate.' );
                }
            }
        }
    }

    $d1     = new DateTime( $checkin );
    $d2     = new DateTime( $checkout );
    $nights = $d1->diff( $d2 )->days;

    $to_email = get_theme_mod( 'rim_email', get_option( 'admin_email' ) );
    $subject  = "[Residence I Mari] Richiesta: $apartment | $checkin - $checkout ($nights notti)";

    $body  = "NUOVA RICHIESTA DI PRENOTAZIONE\n";
    $body .= "================================\n\n";
    $body .= "Appartamento: $apartment\n";
    $body .= "Check-in:     $checkin\n";
    $body .= "Check-out:    $checkout\n";
    $body .= "Notti:        $nights\n";
    $body .= "Adulti:       $adults\n";
    $body .= "Bambini:      $children\n\n";
    $body .= "DATI OSPITE\n";
    $body .= "----------------------------\n";
    $body .= "Nome:     $name\n";
    $body .= "Email:    $email\n";
    $body .= "Telefono: " . ( $phone ? $phone : 'Non specificato' ) . "\n";
    if ( $notes ) {
        $body .= "\nNote: $notes\n";
    }
    $body .= "\n--- Inviato dal sito Residence I Mari ---\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: $name <$email>",
    );

    $sent = wp_mail( $to_email, $subject, $body, $headers );

    if ( $sent ) {
        wp_send_json_success( array( 'message' => 'Richiesta inviata! Ti risponderemo entro 24 ore.' ) );
    } else {
        wp_send_json_error( 'Errore nell\'invio. Contattaci direttamente al telefono.' );
    }
}

/* ─────────────────────────────────────────────
 * 13. CALENDARIO DISPONIBILITÀ (ADMIN MULTI-CALENDAR)
 * ───────────────────────────────────────────── */

/**
 * Submenu page "Calendario" under Appartamenti CPT.
 */
add_action( 'admin_menu', 'rim_add_calendar_page' );
function rim_add_calendar_page() {
    add_submenu_page(
        'edit.php?post_type=appartamento',
        'Calendario Disponibilità',
        'Calendario',
        'edit_posts',
        'rim-calendario',
        'rim_render_calendar_page'
    );
}

/**
 * Enqueue calendar JS only on the calendar admin page.
 */
add_action( 'admin_enqueue_scripts', 'rim_calendar_page_scripts' );
function rim_calendar_page_scripts( $hook ) {
    if ( 'appartamento_page_rim-calendario' !== $hook ) {
        return;
    }
    wp_enqueue_script(
        'rim-admin-calendar',
        get_theme_file_uri( 'js/admin-calendar.js' ),
        array(),
        '1.2',
        true
    );
    wp_localize_script( 'rim-admin-calendar', 'rimCal', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'rim_avail_action' ),
    ) );
}

/**
 * Render the calendar admin page with inline CSS.
 */
function rim_render_calendar_page() {
    $apartments = get_posts( array(
        'post_type'      => 'appartamento',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    $apt_data = array();
    foreach ( $apartments as $apt ) {
        $booked = get_post_meta( $apt->ID, 'rim_booked_dates', true );
        if ( ! is_array( $booked ) ) {
            $booked = array();
        }
        $apt_data[] = array(
            'id'     => $apt->ID,
            'title'  => $apt->post_title,
            'booked' => $booked,
        );
    }
    ?>
    <div class="wrap" id="rim-cal-wrap"></div>
    <script>var rimCalData = <?php echo wp_json_encode( $apt_data ); ?>;</script>
    <style>
    /* ── Calendar Layout ── */
    #rim-cal-wrap{padding:0}
    #rim-cal-wrap *{box-sizing:border-box}
    .rc-header{display:flex;align-items:center;gap:16px;padding:20px 0}
    .rc-month-title{font-size:22px;font-weight:600;min-width:220px;text-align:center;margin:0;padding:0}
    .rc-nav{background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;padding:8px 14px;font-size:16px;cursor:pointer;transition:all .15s}
    .rc-nav:hover{background:#dcdcde}
    .rc-btn-today{margin-left:auto;background:#2271b1;color:#fff;border:none;border-radius:4px;padding:8px 16px;cursor:pointer;font-size:13px;font-weight:600}
    .rc-btn-today:hover{background:#135e96}

    /* ── Legend + Status ── */
    .rc-legend{display:flex;gap:20px;align-items:center;padding:4px 0 8px;flex-wrap:wrap}
    .rc-leg{display:flex;align-items:center;gap:6px;font-size:13px}
    .rc-dot{width:16px;height:16px;border-radius:3px;display:inline-block}
    .rc-dot-free{background:#e8f5e9;border:1px solid #a5d6a7}
    .rc-dot-booked{background:#e74c3c}
    .rc-dot-sel{background:#3498db}
    .rc-leg-hint{font-size:12px;color:#888;margin-left:auto}
    .rc-status{padding:6px 14px;border-radius:4px;font-size:13px;margin-bottom:12px;min-height:32px;background:#f0f6fc;color:#2271b1;border:1px solid #c5d9ed}
    .rc-status-sel{background:#ebf5fb;color:#2271b1;font-weight:600}

    /* ── Grid ── */
    .rc-grid-wrap{overflow-x:auto;border:1px solid #c3c4c7;border-radius:6px;background:#fff}
    .rc-grid{border-collapse:collapse;width:max-content;min-width:100%}
    .rc-grid th,.rc-grid td{border:1px solid #e8e8e8;text-align:center;padding:0;vertical-align:middle}

    /* Day headers */
    .rc-day-h{width:44px;min-width:44px;padding:8px 2px;font-size:11px;font-weight:500;line-height:1.3;background:#f8f9fa;color:#555}
    .rc-day-h.rc-we{background:#f0f0f1;color:#999}
    .rc-day-h.rc-today-h{background:#2271b1;color:#fff;font-weight:700}

    /* Apartment names */
    .rc-apt-header{min-width:150px;padding:8px 12px;background:#f8f9fa;font-weight:600;text-align:left!important;position:sticky;left:0;z-index:20}
    .rc-apt-name{padding:8px 12px;font-weight:600;font-size:13px;white-space:nowrap;text-align:left!important;background:#fff;position:sticky;left:0;z-index:10;border-right:2px solid #c3c4c7!important}

    /* ── Cells ── */
    .rc-cell{height:44px;width:44px;min-width:44px;cursor:pointer;transition:background .12s;position:relative}
    .rc-cell:not(.rc-bk):not(.rc-sel):hover{background:#d4edda}
    .rc-cell.rc-we-c:not(.rc-bk):not(.rc-sel){background:#fafafa}
    .rc-today-c{box-shadow:inset 0 0 0 2px #2271b1;z-index:5}

    /* Booked */
    .rc-bk{background:#e74c3c;cursor:pointer}
    .rc-bk:hover{background:#c0392b!important}
    .rc-bk-start{border-top-left-radius:6px;border-bottom-left-radius:6px;border-right-color:transparent}
    .rc-bk-mid{border-left-color:transparent;border-right-color:transparent}
    .rc-bk-end{border-top-right-radius:6px;border-bottom-right-radius:6px;border-left-color:transparent}
    .rc-bk-single{border-radius:6px}
    .rc-bk-label{position:absolute;left:4px;top:50%;transform:translateY(-50%);font-size:10px;color:#fff;font-weight:600;white-space:nowrap;overflow:visible;z-index:5;pointer-events:none;text-shadow:0 1px 2px rgba(0,0,0,.3)}
    .rc-bk-start,.rc-bk-single{overflow:visible}

    /* Selection */
    .rc-sel{background:#3498db!important}
    .rc-sel-start{border-top-left-radius:6px;border-bottom-left-radius:6px}
    .rc-sel-end{border-top-right-radius:6px;border-bottom-right-radius:6px}
    .rc-sel-single{border-radius:6px}
    .rc-sel-hover{background:#85c1e9!important}

    /* ── Popup ── */
    .rc-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:100000;display:flex;align-items:center;justify-content:center}
    .rc-popup{background:#fff;border-radius:12px;padding:28px;min-width:380px;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.3);position:relative}
    .rc-popup-close{position:absolute;top:12px;right:16px;background:none;border:none;font-size:24px;color:#999;cursor:pointer;padding:4px 8px}
    .rc-popup-close:hover{color:#333}
    .rc-popup h3{margin:0 0 4px;font-size:18px}
    .rc-popup-apt{font-weight:600;color:#2271b1;margin:0 0 4px;font-size:15px}
    .rc-popup-dates{color:#555;margin:0 0 16px;font-size:14px}
    .rc-popup-field{margin-bottom:12px}
    .rc-popup-field label{display:block;font-weight:600;font-size:13px;margin-bottom:4px;color:#333}
    .rc-popup-field input{width:100%;padding:8px 12px;border:1px solid #c3c4c7;border-radius:4px;font-size:14px}
    .rc-popup-field input:focus{border-color:#2271b1;box-shadow:0 0 0 2px rgba(34,113,177,.2);outline:none}
    .rc-popup-btns{display:flex;gap:10px;margin-top:16px}
    .rc-pbtn{padding:10px 20px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .15s}
    .rc-pbtn-save{background:#2271b1;color:#fff;flex:1}
    .rc-pbtn-save:hover{background:#135e96}
    .rc-pbtn-save:disabled{background:#a0c4e8;cursor:wait}
    .rc-pbtn-del{background:#e74c3c;color:#fff;flex:1}
    .rc-pbtn-del:hover{background:#c0392b}
    .rc-pbtn-del:disabled{background:#f5a0a0;cursor:wait}
    .rc-pbtn-cancel{background:#f0f0f1;color:#555}
    .rc-pbtn-cancel:hover{background:#dcdcde}
    .rc-popup-msg{margin-top:10px;font-size:13px}
    .rc-msg-err{color:#e74c3c}
    .rc-msg-ok{color:#2e7d32}

    /* Info table */
    .rc-info-table{width:100%;border-collapse:collapse;margin:12px 0}
    .rc-info-table td{padding:6px 10px;border-bottom:1px solid #eee;font-size:14px;text-align:left}
    .rc-info-table tr:last-child td{border-bottom:none}

    /* ── Responsive ── */
    @media(max-width:782px){
        .rc-popup{min-width:90vw;max-width:95vw;padding:20px}
        .rc-leg-hint{display:none}
        .rc-header{flex-wrap:wrap}
        .rc-btn-today{margin-left:0}
    }
    </style>
    <?php
}

/**
 * AJAX: Get all apartments' booked data (calendar refresh).
 */
add_action( 'wp_ajax_rim_calendar_get_data', 'rim_calendar_get_data_handler' );
function rim_calendar_get_data_handler() {
    check_ajax_referer( 'rim_avail_action', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Non autorizzato' );
    }

    $apartments = get_posts( array(
        'post_type'      => 'appartamento',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    $data = array();
    foreach ( $apartments as $apt ) {
        $booked = get_post_meta( $apt->ID, 'rim_booked_dates', true );
        if ( ! is_array( $booked ) ) {
            $booked = array();
        }
        $data[] = array(
            'id'     => $apt->ID,
            'title'  => $apt->post_title,
            'booked' => $booked,
        );
    }

    wp_send_json_success( $data );
}
