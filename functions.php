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
    // display=swap evita il render-blocking su mobile (testo visibile subito con font di sistema)
    wp_enqueue_style(
        'rim-google-fonts',
        'https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap&display=swap',
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

    // CSS — tariffe (solo sulla pagina tariffe)
    if ( is_page( 'tariffe' ) ) {
        wp_enqueue_style(
            'rim-tariffe-style',
            get_theme_file_uri( 'css/tariffe.css' ),
            array( 'rim-main-style' ),
            $ver
        );
    }

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
        'rim_base_price' => array(
            'label' => __( 'Prezzo base / notte (€)', 'residence-i-mari' ),
            'type'  => 'number',
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

    // Amenità a icone — "Cosa troverai"
    $saved_amenities = get_post_meta( $post->ID, 'rim_amenities', true );
    $saved_amenities = is_array( $saved_amenities ) ? $saved_amenities : array();
    $all_amenities   = rim_amenities_master();
    ?>
    <div style="margin-top:20px;">
        <p style="font-weight:600;margin-bottom:10px;">Cosa troverai (dotazioni)</p>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px 16px;">
            <?php foreach ( $all_amenities as $key => $amenity ) :
                $checked = in_array( $key, $saved_amenities, true ) ? ' checked' : ''; ?>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;">
                    <input type="checkbox" name="rim_amenities[]" value="<?php echo esc_attr( $key ); ?>"<?php echo $checked; ?>>
                    <?php echo esc_html( $amenity['label'] ); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
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
        'rim_base_price',
    );

    foreach ( $text_fields as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }

    // Dotazioni (Cosa troverai)
    $valid_keys      = array_keys( rim_amenities_master() );
    $selected        = isset( $_POST['rim_amenities'] ) && is_array( $_POST['rim_amenities'] )
        ? array_values( array_intersect( $_POST['rim_amenities'], $valid_keys ) )
        : array();
    update_post_meta( $post_id, 'rim_amenities', $selected );

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
            'default' => 'piccolo_hotel@virgilio.it',
            'type'    => 'email',
        ),
        'rim_address'              => array(
            'label'   => __( 'Indirizzo', 'residence-i-mari' ),
            'default' => 'Via Ansedonia 10, 58043 Castiglione della Pescaia (GR)',
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
        'default'           => 'Il Residence I Mari si trova in <strong>Via Ansedonia 10</strong>, in una posizione privilegiata a pochi passi dal centro e dalla spiaggia. La località è una delle perle della Maremma Toscana, premiata con la Bandiera Blu per la qualità delle acque.',
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
 * 6b. CUSTOMIZER — Impostazioni SEO / Webmaster
 * ───────────────────────────────────────────── */
add_action( 'customize_register', 'rim_customizer_seo_settings' );
function rim_customizer_seo_settings( $wp_customize ) {

    $wp_customize->add_section( 'rim_seo_webmaster', array(
        'title'       => __( 'SEO / Webmaster Tools', 'residence-i-mari' ),
        'priority'    => 31,
        'description' => __( 'Codici di verifica per Google Search Console e altri strumenti.', 'residence-i-mari' ),
    ) );

    $wp_customize->add_setting( 'rim_google_site_verification', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_google_site_verification', array(
        'label'       => __( 'Google Search Console — Codice verifica', 'residence-i-mari' ),
        'description' => __( 'Incolla solo il valore content del meta tag (es: AbCdEf123456).', 'residence-i-mari' ),
        'section'     => 'rim_seo_webmaster',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'rim_bing_site_verification', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_bing_site_verification', array(
        'label'   => __( 'Bing Webmaster — Codice verifica', 'residence-i-mari' ),
        'section' => 'rim_seo_webmaster',
        'type'    => 'text',
    ) );
}

add_action( 'wp_head', 'rim_webmaster_verification_tags', 0 );
function rim_webmaster_verification_tags() {
    $google = get_theme_mod( 'rim_google_site_verification', '' );
    if ( $google ) {
        echo '<meta name="google-site-verification" content="' . esc_attr( $google ) . '">' . "\n";
    }

    $bing = get_theme_mod( 'rim_bing_site_verification', '' );
    if ( $bing ) {
        echo '<meta name="msvalidate.01" content="' . esc_attr( $bing ) . '">' . "\n";
    }
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
 * 10. SEO — Schema Markup JSON-LD
 * ───────────────────────────────────────────── */
add_action( 'wp_head', 'rim_schema_jsonld' );
function rim_schema_jsonld() {

    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );
    $phone     = get_theme_mod( 'rim_phone', '0564 937081' );
    $email     = get_theme_mod( 'rim_email', 'residenceimari@gmail.com' );
    $address   = get_theme_mod( 'rim_address', 'Via Ansedonia 10, 58043 Castiglione della Pescaia (GR)' );
    $rating    = get_theme_mod( 'rim_google_rating', '4.5' );
    $reviews   = get_theme_mod( 'rim_google_reviews_count', '180' );

    $rating_value = floatval( preg_replace( '/[^0-9.]/', '', $rating ) );
    $reviews_count = absint( preg_replace( '/[^0-9]/', '', $reviews ) );

    if ( is_front_page() ) {
        $lodging = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'LodgingBusiness',
            'name'        => $site_name,
            'description' => 'Residence con 9 appartamenti ristrutturati a 100 metri dal mare a Castiglione della Pescaia, Maremma Toscana. Parcheggio privato, pet friendly.',
            'url'         => $site_url,
            'telephone'   => '+39' . preg_replace( '/[^\d]/', '', $phone ),
            'email'       => $email,
            'address'     => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'Via Ansedonia 10',
                'addressLocality' => 'Castiglione della Pescaia',
                'postalCode'      => '58043',
                'addressRegion'   => 'GR',
                'addressCountry'  => 'IT',
            ),
            'geo' => array(
                '@type'     => 'GeoCoordinates',
                'latitude'  => 42.7644,
                'longitude' => 10.8833,
            ),
            'image'       => get_theme_file_uri( 'img/esterni/Esterni-e-Hall-2.jpg' ),
            'starRating'  => array(
                '@type'       => 'Rating',
                'ratingValue' => '3',
            ),
            'amenityFeature' => array(
                array( '@type' => 'LocationFeatureSpecification', 'name' => 'Parcheggio privato', 'value' => true ),
                array( '@type' => 'LocationFeatureSpecification', 'name' => 'Aria condizionata', 'value' => true ),
                array( '@type' => 'LocationFeatureSpecification', 'name' => 'Pet friendly', 'value' => true ),
                array( '@type' => 'LocationFeatureSpecification', 'name' => 'Wi-Fi', 'value' => true ),
            ),
            'checkinTime'  => '15:00',
            'checkoutTime' => '10:00',
            'petsAllowed'  => true,
            'numberOfRooms' => 9,
        );

        if ( $rating_value > 0 && $reviews_count > 0 ) {
            $lodging['aggregateRating'] = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => $rating_value,
                'bestRating'  => 5,
                'reviewCount' => $reviews_count,
            );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $lodging, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    if ( is_singular( 'appartamento' ) ) {
        $apt_id    = get_the_ID();
        $sqm       = get_post_meta( $apt_id, 'rim_sqm', true );
        $guests    = get_post_meta( $apt_id, 'rim_guests', true );
        $rooms     = get_post_meta( $apt_id, 'rim_rooms', true );
        $desc      = get_post_meta( $apt_id, 'rim_short_description', true );
        $amenities = rim_get_amenities( $apt_id );

        $images = array();
        if ( has_post_thumbnail( $apt_id ) ) {
            $images[] = get_the_post_thumbnail_url( $apt_id, 'full' );
        }
        $gallery = get_post_meta( $apt_id, 'rim_gallery', true );
        if ( is_array( $gallery ) ) {
            foreach ( $gallery as $img_id ) {
                $url = wp_get_attachment_image_url( $img_id, 'large' );
                if ( $url ) {
                    $images[] = $url;
                }
            }
        }

        $apartment_schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Apartment',
            'name'        => get_the_title(),
            'description' => $desc ? $desc : wp_trim_words( get_the_excerpt(), 30 ),
            'url'         => get_permalink(),
            'image'       => $images,
            'containedInPlace' => array(
                '@type' => 'LodgingBusiness',
                'name'  => $site_name,
                'url'   => $site_url,
            ),
        );

        if ( $sqm ) {
            $apartment_schema['floorSize'] = array(
                '@type'    => 'QuantitativeValue',
                'value'    => (int) $sqm,
                'unitCode' => 'MTK',
            );
        }
        if ( $guests ) {
            $apartment_schema['occupancy'] = array(
                '@type'    => 'QuantitativeValue',
                'maxValue' => (int) $guests,
            );
        }
        if ( $rooms ) {
            $apartment_schema['numberOfRooms'] = (int) $rooms;
        }
        if ( ! empty( $amenities ) ) {
            $apartment_schema['amenityFeature'] = array_map( function( $a ) {
                $name = is_array( $a ) ? $a['label'] : $a;
                return array(
                    '@type' => 'LocationFeatureSpecification',
                    'name'  => $name,
                    'value' => true,
                );
            }, $amenities );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $apartment_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    if ( ! is_front_page() ) {
        $breadcrumbs = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array(
                array(
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => $site_url,
                ),
            ),
        );

        if ( is_singular( 'appartamento' ) ) {
            $breadcrumbs['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => 'Appartamenti',
                'item'     => $site_url . '#appartamenti',
            );
            $breadcrumbs['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } elseif ( is_page() ) {
            $breadcrumbs['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumbs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
}

/* ─────────────────────────────────────────────
 * 11. SEO — Meta Tags (Description, Open Graph, Canonical)
 * ───────────────────────────────────────────── */
add_action( 'wp_head', 'rim_seo_meta_tags', 1 );
function rim_seo_meta_tags() {

    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );

    $title       = $site_name . ' — Appartamenti a Castiglione della Pescaia';
    $description = 'Residence con 9 appartamenti ristrutturati a 100 metri dal mare a Castiglione della Pescaia, Maremma Toscana. Parcheggio privato, pet friendly, spiaggia convenzionata.';
    $canonical   = $site_url;
    $og_image    = get_theme_file_uri( 'img/esterni/Esterni-e-Hall-2.jpg' );
    $og_type     = 'website';

    if ( is_singular( 'appartamento' ) ) {
        $apt_id      = get_the_ID();
        $short_desc  = get_post_meta( $apt_id, 'rim_short_description', true );
        $title       = get_the_title() . ' — ' . $site_name;
        $description = $short_desc ? $short_desc : 'Appartamento ' . get_the_title() . ' al Residence I Mari, Castiglione della Pescaia. A 100 metri dal mare.';
        $canonical   = get_permalink();
        $og_type     = 'article';

        if ( has_post_thumbnail( $apt_id ) ) {
            $og_image = get_the_post_thumbnail_url( $apt_id, 'large' );
        }
    } elseif ( is_page() && ! is_front_page() ) {
        $title     = get_the_title() . ' — ' . $site_name;
        $canonical = get_permalink();

        $excerpt = get_the_excerpt();
        if ( $excerpt ) {
            $description = wp_trim_words( $excerpt, 25, '...' );
        }
    } elseif ( is_post_type_archive( 'appartamento' ) ) {
        $title       = 'Appartamenti — ' . $site_name;
        $description = 'Scopri i 9 appartamenti del Residence I Mari a Castiglione della Pescaia: bilocali e trilocali ristrutturati a 100 metri dal mare.';
        $canonical   = get_post_type_archive_link( 'appartamento' );
    }

    if ( mb_strlen( $description ) > 160 ) {
        $description = mb_substr( $description, 0, 157 ) . '...';
    }

    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:locale" content="it_IT">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}

/* ─────────────────────────────────────────────
 * 12. SEO — Disattiva meta duplicati se Yoast/RankMath attivo
 * ───────────────────────────────────────────── */
add_action( 'wp_head', 'rim_maybe_disable_seo_meta', 0 );
function rim_maybe_disable_seo_meta() {
    if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || class_exists( 'FLAVOR_SEO' ) ) {
        remove_action( 'wp_head', 'rim_seo_meta_tags', 1 );
        remove_action( 'wp_head', 'rim_schema_jsonld' );
    }
}

/* ─────────────────────────────────────────────
 * 13. SEO — Sitemap XML nativa WP
 * ───────────────────────────────────────────── */
add_filter( 'wp_sitemaps_post_types', 'rim_sitemap_include_appartamento' );
function rim_sitemap_include_appartamento( $post_types ) {
    if ( ! isset( $post_types['appartamento'] ) ) {
        $post_types['appartamento'] = get_post_type_object( 'appartamento' );
    }
    return $post_types;
}

/* ─────────────────────────────────────────────
 * 14. MOBILE BOTTOM BAR SUPPORT
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
/**
 * Restituisce l'URL di una pagina WordPress dato lo slug.
 *
 * @param  string $slug Slug della pagina.
 * @return string       URL della pagina, o fallback home_url('/$slug/').
 */
function rim_get_page_url( $slug ) {
    $page = get_page_by_path( $slug );
    if ( $page ) {
        return get_permalink( $page );
    }
    return home_url( '/' . $slug . '/' );
}

function rim_phone_link( $phone ) {
    // Rimuove spazi e caratteri non numerici
    $digits = preg_replace( '/[^\d]/', '', $phone );
    // Rimuove prefisso 39 se già presente (es. se il numero inizia con 39...)
    if ( strpos( $digits, '39' ) === 0 && strlen( $digits ) > 10 ) {
        $digits = substr( $digits, 2 );
    }
    return $digits;
}

/**
 * Restituisce l'array di amenities di un appartamento.
 *
 * @param  int   $post_id Post ID.
 * @return array
 */
function rim_amenities_master() {
    $s = 'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
    return array(
        'wifi'          => array( 'label' => 'Wifi',                  'icon' => "<svg $s><path d='M5 12.55a11 11 0 0 1 14.08 0'/><path d='M1.42 9a16 16 0 0 1 21.16 0'/><path d='M8.53 16.11a6 6 0 0 1 6.95 0'/><circle cx='12' cy='20' r='1' fill='currentColor' stroke='none'/></svg>" ),
        'cucina'        => array( 'label' => 'Cucina attrezzata',     'icon' => "<svg $s><path d='M18 8h1a4 4 0 0 1 0 8h-1'/><path d='M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z'/><line x1='6' y1='1' x2='6' y2='4'/><line x1='10' y1='1' x2='10' y2='4'/><line x1='14' y1='1' x2='14' y2='4'/></svg>" ),
        'parcheggio'    => array( 'label' => 'Parcheggio gratuito',   'icon' => "<svg $s><rect x='1' y='3' width='15' height='13'/><polygon points='16 8 20 8 23 11 23 16 16 16 16 8'/><circle cx='5.5' cy='18.5' r='2.5'/><circle cx='18.5' cy='18.5' r='2.5'/></svg>" ),
        'tv'            => array( 'label' => 'TV Satellite',           'icon' => "<svg $s><rect x='2' y='3' width='20' height='14' rx='2' ry='2'/><line x1='8' y1='21' x2='16' y2='21'/><line x1='12' y1='17' x2='12' y2='21'/></svg>" ),
        'aria_cond'     => array( 'label' => 'Aria condizionata',     'icon' => "<svg $s><path d='M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2'/></svg>" ),
        'lavatrice'     => array( 'label' => 'Lavatrice',              'icon' => "<svg $s><polyline points='23 4 23 10 17 10'/><path d='M20.49 15a9 9 0 1 1-.68-8.41L23 10'/></svg>" ),
        'animali'       => array( 'label' => 'Animali ammessi',        'icon' => "<svg $s><path d='M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z'/></svg>" ),
        'culla'         => array( 'label' => 'Culla disponibile',      'icon' => "<svg $s><path d='M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z'/></svg>" ),
        'terrazza'      => array( 'label' => 'Terrazza / Balcone',     'icon' => "<svg $s><circle cx='12' cy='12' r='5'/><line x1='12' y1='1' x2='12' y2='3'/><line x1='12' y1='21' x2='12' y2='23'/><line x1='4.22' y1='4.22' x2='5.64' y2='5.64'/><line x1='18.36' y1='18.36' x2='19.78' y2='19.78'/><line x1='1' y1='12' x2='3' y2='12'/><line x1='21' y1='12' x2='23' y2='12'/><line x1='4.22' y1='19.78' x2='5.64' y2='18.36'/><line x1='18.36' y1='5.64' x2='19.78' y2='4.22'/></svg>" ),
        'cassaforte'    => array( 'label' => 'Cassaforte',             'icon' => "<svg $s><rect x='3' y='11' width='18' height='11' rx='2' ry='2'/><path d='M7 11V7a5 5 0 0 1 10 0v4'/></svg>" ),
        'lavastoviglie' => array( 'label' => 'Lavastoviglie',          'icon' => "<svg $s><path d='M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z'/><path d='M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97'/></svg>" ),
        'lino'          => array( 'label' => 'Biancheria inclusa',     'icon' => "<svg $s><polygon points='12 2 2 7 12 12 22 7 12 2'/><polyline points='2 17 12 22 22 17'/><polyline points='2 12 12 17 22 12'/></svg>" ),
        'ferro_stiro'   => array( 'label' => 'Ferro da stiro',         'icon' => "<svg $s><polyline points='22 12 16 12 14 15 10 15 8 12 2 12'/><path d='M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z'/></svg>" ),
        'asciugacapelli'=> array( 'label' => 'Asciugacapelli',         'icon' => "<svg $s><circle cx='12' cy='12' r='10'/><polyline points='12 8 16 12 12 16'/><line x1='8' y1='12' x2='16' y2='12'/></svg>" ),
        'bbq'           => array( 'label' => 'Area barbecue',          'icon' => "<svg $s><line x1='8.56' y1='2.75' x2='8.56' y2='20'/><line x1='15.44' y1='2.75' x2='15.44' y2='20'/><path d='M20 9H4a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2z'/></svg>" ),
        'microonde'     => array( 'label' => 'Microonde',              'icon' => "<svg $s><rect x='2' y='7' width='20' height='15' rx='2' ry='2'/><polyline points='17 2 17 7'/><polyline points='12 2 12 7'/><polyline points='7 2 7 7'/><line x1='2' y1='12' x2='22' y2='12'/></svg>" ),
    );
}

function rim_get_amenities( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $saved = get_post_meta( $post_id, 'rim_amenities', true );
    // Retrocompatibilità: vecchio formato stringa CSV
    if ( is_string( $saved ) && ! empty( $saved ) ) {
        return array_map( 'trim', explode( ',', $saved ) );
    }
    if ( ! is_array( $saved ) || empty( $saved ) ) {
        return array();
    }
    $master = rim_amenities_master();
    $result = array();
    foreach ( $saved as $key ) {
        if ( isset( $master[ $key ] ) ) {
            $result[ $key ] = $master[ $key ];
        }
    }
    return $result;
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

        // Calculate total price for the stay
        $total_price = 0;
        $price_per_night = array();
        $ci_dt = new DateTime( $checkin );
        $co_dt = new DateTime( $checkout );
        while ( $ci_dt < $co_dt ) {
            $day_str = $ci_dt->format( 'Y-m-d' );
            $p = rim_get_price_for_date( $apt->ID, $day_str );
            $total_price += $p;
            $price_per_night[ $day_str ] = $p;
            $ci_dt->modify( '+1 day' );
        }

        $results[] = array(
            'id'              => $apt->ID,
            'name'            => $apt->post_title,
            'available'       => $available,
            'short_desc'      => get_post_meta( $apt->ID, 'rim_short_description', true ),
            'sqm'             => get_post_meta( $apt->ID, 'rim_sqm', true ),
            'guests'          => get_post_meta( $apt->ID, 'rim_guests', true ),
            'rooms'           => get_post_meta( $apt->ID, 'rim_rooms', true ),
            'thumb'           => $thumb ? $thumb : '',
            'total_price'     => $total_price,
            'price_per_night' => $price_per_night,
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

    // Calculate total price
    $total_price = 0;
    if ( $apt_id ) {
        $ci_dt = new DateTime( $checkin );
        $co_dt = new DateTime( $checkout );
        while ( $ci_dt < $co_dt ) {
            $total_price += rim_get_price_for_date( $apt_id, $ci_dt->format( 'Y-m-d' ) );
            $ci_dt->modify( '+1 day' );
        }
    }
    $price_line = $total_price > 0 ? number_format( $total_price, 0, ',', '.' ) . ' €' : 'Da definire';

    $body  = "NUOVA RICHIESTA DI PRENOTAZIONE\n";
    $body .= "================================\n\n";
    $body .= "Appartamento: $apartment\n";
    $body .= "Check-in:     $checkin\n";
    $body .= "Check-out:    $checkout\n";
    $body .= "Notti:        $nights\n";
    $body .= "Totale:       $price_line\n";
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
        '2.0',
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
            'base'   => floatval( get_post_meta( $apt->ID, 'rim_base_price', true ) ),
            'prices' => rim_get_daily_prices( $apt->ID ),
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

    /* Prices */
    .rc-price{font-size:11px;font-weight:600;color:#333;line-height:44px;display:block}

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

    /* Tabs */
    .rc-tabs{display:flex;gap:0;border-bottom:2px solid #e8e8e8;margin:0 0 16px}
    .rc-tab{background:none;border:none;padding:10px 20px;font-size:14px;font-weight:600;color:#888;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s}
    .rc-tab:hover{color:#333}
    .rc-tab-active{color:#2271b1;border-bottom-color:#2271b1}
    .rc-popup-hint{font-size:12px;color:#888;margin:4px 0 12px}
    .rc-popup-warn{background:#fff3cd;color:#856404;padding:8px 12px;border-radius:4px;font-size:13px;margin-bottom:12px}

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
            'base'   => floatval( get_post_meta( $apt->ID, 'rim_base_price', true ) ),
            'prices' => rim_get_daily_prices( $apt->ID ),
        );
    }

    wp_send_json_success( $data );
}

/* ─────────────────────────────────────────────
 * 14. INIZIALIZZAZIONE PREZZI BASE (one-time)
 * ───────────────────────────────────────────── */

/**
 * Set default base prices for all apartments if not already set.
 * Runs once via admin_init, then sets an option to prevent re-running.
 */
add_action( 'admin_init', 'rim_init_base_prices' );
function rim_init_base_prices() {
    if ( get_option( 'rim_base_prices_initialized' ) ) {
        return;
    }

    // Price map: keyword in title → base price per night
    $price_map = array(
        'Ionio 1'       => 148,
        'Ionio 2'       => 158,
        'Egeo 1'        => 168,
        'Egeo 2'        => 168,
        'Tirreno 1'     => 148,
        'Tirreno 2'     => 148,
        'Adriatico 1'   => 148,
        'Adriatico 2'   => 148,
        'Mediterraneo'  => 168,
    );

    $apartments = get_posts( array(
        'post_type'      => 'appartamento',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ) );

    foreach ( $apartments as $apt ) {
        // Skip if already has a base price
        $existing = get_post_meta( $apt->ID, 'rim_base_price', true );
        if ( $existing ) {
            continue;
        }

        foreach ( $price_map as $keyword => $price ) {
            if ( stripos( $apt->post_title, $keyword ) !== false ) {
                update_post_meta( $apt->ID, 'rim_base_price', $price );
                break;
            }
        }
    }

    update_option( 'rim_base_prices_initialized', true );
}

/* ─────────────────────────────────────────────
 * 15. SISTEMA PREZZI GIORNALIERI
 * ───────────────────────────────────────────── */

/**
 * Get the nightly price for a specific apartment on a specific date.
 * Falls back to rim_base_price if no daily override is set.
 */
function rim_get_price_for_date( $post_id, $date_ymd ) {
    $daily = get_post_meta( $post_id, 'rim_daily_prices', true );
    if ( is_array( $daily ) && isset( $daily[ $date_ymd ] ) ) {
        return floatval( $daily[ $date_ymd ] );
    }
    $base = get_post_meta( $post_id, 'rim_base_price', true );
    return $base ? floatval( $base ) : 0;
}

/**
 * Get all daily price overrides for an apartment.
 */
function rim_get_daily_prices( $post_id ) {
    $prices = get_post_meta( $post_id, 'rim_daily_prices', true );
    return is_array( $prices ) ? $prices : array();
}

/**
 * AJAX: Save prices for a date range (admin bulk pricing).
 */
add_action( 'wp_ajax_rim_save_prices', 'rim_save_prices_handler' );
function rim_save_prices_handler() {
    check_ajax_referer( 'rim_avail_action', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Non autorizzato' );
    }

    $post_id = intval( $_POST['post_id'] );
    $from    = sanitize_text_field( $_POST['from'] );
    $to      = sanitize_text_field( $_POST['to'] );  // inclusive
    $price   = floatval( $_POST['price'] );

    if ( ! $from || ! $to || $from > $to ) {
        wp_send_json_error( 'Date non valide.' );
    }
    if ( $price < 0 ) {
        wp_send_json_error( 'Prezzo non valido.' );
    }

    $daily = rim_get_daily_prices( $post_id );
    $base  = floatval( get_post_meta( $post_id, 'rim_base_price', true ) );

    // Set price for each day in range
    $current = $from;
    while ( $current <= $to ) {
        if ( $price == $base ) {
            // Remove override if it matches base price (keep data clean)
            unset( $daily[ $current ] );
        } else {
            $daily[ $current ] = $price;
        }
        $d = new DateTime( $current );
        $d->modify( '+1 day' );
        $current = $d->format( 'Y-m-d' );
    }

    update_post_meta( $post_id, 'rim_daily_prices', $daily );

    wp_send_json_success( array(
        'prices'  => $daily,
        'base'    => $base,
        'message' => 'Prezzi aggiornati',
    ) );
}

/**
 * AJAX: Save prices for multiple apartments at once (admin bulk).
 */
add_action( 'wp_ajax_rim_save_prices_bulk', 'rim_save_prices_bulk_handler' );
function rim_save_prices_bulk_handler() {
    check_ajax_referer( 'rim_avail_action', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Non autorizzato' );
    }

    $post_ids_raw = sanitize_text_field( $_POST['post_ids'] ?? '' );
    $from         = sanitize_text_field( $_POST['from'] );
    $to           = sanitize_text_field( $_POST['to'] );
    $price        = floatval( $_POST['price'] );
    $post_ids     = array_map( 'intval', explode( ',', $post_ids_raw ) );

    if ( ! $from || ! $to || $from > $to ) {
        wp_send_json_error( 'Date non valide.' );
    }
    if ( $price < 0 ) {
        wp_send_json_error( 'Prezzo non valido.' );
    }

    $results = array();
    foreach ( $post_ids as $pid ) {
        if ( $pid <= 0 ) continue;
        $daily = rim_get_daily_prices( $pid );
        $base  = floatval( get_post_meta( $pid, 'rim_base_price', true ) );

        $current = $from;
        while ( $current <= $to ) {
            if ( $price == $base ) {
                unset( $daily[ $current ] );
            } else {
                $daily[ $current ] = $price;
            }
            $d = new DateTime( $current );
            $d->modify( '+1 day' );
            $current = $d->format( 'Y-m-d' );
        }
        update_post_meta( $pid, 'rim_daily_prices', $daily );
        $results[ $pid ] = array( 'prices' => $daily, 'base' => $base );
    }

    wp_send_json_success( array(
        'updated' => $results,
        'message' => 'Prezzi aggiornati per ' . count( $results ) . ' appartamenti',
    ) );
}

/* ─────────────────────────────────────────────
 * HELPER — Trova il PDF del listino nella Media Library.
 *
 * Cerca un allegato il cui titolo o nome file contiene "listino" o "calendario-listino".
 * In alternativa si può assegnare l'ID manualmente via Customizer (rim_listino_pdf_id).
 * ───────────────────────────────────────────── */
function rim_get_listino_pdf_id() {

    // 1. Controlla se è stato impostato manualmente nel Customizer
    $manual_id = get_theme_mod( 'rim_listino_pdf_id', 0 );
    if ( $manual_id && get_post( $manual_id ) ) {
        return (int) $manual_id;
    }

    // 2. Cerca in Media Library per nome file
    $attachments = new WP_Query( array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => 1,
        'post_status'    => 'inherit',
        'no_found_rows'  => true,
        's'              => 'listino',
    ) );

    if ( $attachments->have_posts() ) {
        return $attachments->posts[0]->ID;
    }

    return 0;
}

/* ─────────────────────────────────────────────
 * ANALYTICS — Google Analytics 4 (GA4)
 *
 * Sostituisci G-XXXXXXXXXX con il tuo Measurement ID reale.
 * Lo trovi su analytics.google.com → Admin → Data Streams.
 * ───────────────────────────────────────────── */
add_action( 'wp_head', 'rim_google_analytics', 1 );
function rim_google_analytics() {
    $ga4_id = get_theme_mod( 'rim_ga4_id', 'G-ZBDBYH9YHH' );
    if ( empty( $ga4_id ) ) {
        return;
    }
    $ga4_id = sanitize_text_field( $ga4_id );
    ?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php echo esc_js( $ga4_id ); ?>', {
    'anonymize_ip': true
  });
</script>
    <?php
}

/* ─────────────────────────────────────────────
 * ANALYTICS — Customizer: campo GA4 Measurement ID
 * ───────────────────────────────────────────── */
add_action( 'customize_register', 'rim_customizer_ga4' );
function rim_customizer_ga4( $wp_customize ) {
    $wp_customize->add_section( 'rim_analytics', array(
        'title'    => __( 'Analytics & Tracking', 'residence-i-mari' ),
        'priority' => 200,
    ) );
    $wp_customize->add_setting( 'rim_ga4_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'rim_ga4_id', array(
        'label'       => __( 'Google Analytics 4 — Measurement ID', 'residence-i-mari' ),
        'description' => __( 'Formato: G-XXXXXXXXXX. Trovalo su analytics.google.com → Admin → Data Streams.', 'residence-i-mari' ),
        'section'     => 'rim_analytics',
        'type'        => 'text',
    ) );
}

/* ─────────────────────────────────────────────
 * SEO — Schema LodgingBusiness + AggregateRating (JSON-LD)
 *
 * Inietta schema strutturato nel <head> per migliorare
 * la visibilità nei risultati Google (rich snippets).
 * ───────────────────────────────────────────── */
add_action( 'wp_head', 'rim_seo_lodging_schema', 99 );
function rim_seo_lodging_schema() {
    // Mostra schema su tutte le pagine del sito
    if ( is_admin() ) {
        return;
    }

    $address  = get_theme_mod( 'rim_address', 'Via Ansedonia 10, 58043 Castiglione della Pescaia (GR)' );
    $phone    = get_theme_mod( 'rim_phone', '0564 937081' );
    $email    = get_theme_mod( 'rim_email', 'piccolo_hotel@virgilio.it' );
    $logo_id  = get_theme_mod( 'custom_logo' );
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

    $schema = array(
        '@context'       => 'https://schema.org',
        '@type'          => 'LodgingBusiness',
        'name'           => 'Residence I Mari',
        'description'    => 'Appartamenti vacanze a Castiglione della Pescaia, Maremma Toscana. 9 appartamenti a 100 metri dal mare.',
        'url'            => home_url( '/' ),
        'telephone'      => '+39 ' . $phone,
        'email'          => $email,
        'starRating'     => array(
            '@type'       => 'Rating',
            'ratingValue' => '3',
        ),
        'address'        => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Via Ansedonia, 10',
            'addressLocality' => 'Castiglione della Pescaia',
            'postalCode'      => '58043',
            'addressRegion'   => 'GR',
            'addressCountry'  => 'IT',
        ),
        'geo'            => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => 42.7637,
            'longitude' => 10.8837,
        ),
        'image'          => $logo_url,
        'priceRange'     => '€€',
        'checkinTime'    => '14:00',
        'checkoutTime'   => '10:00',
        'amenityFeature' => array(
            array( '@type' => 'LocationFeatureSpecification', 'name' => 'WiFi gratuito', 'value' => true ),
            array( '@type' => 'LocationFeatureSpecification', 'name' => 'Parcheggio privato', 'value' => true ),
            array( '@type' => 'LocationFeatureSpecification', 'name' => 'Aria condizionata', 'value' => true ),
            array( '@type' => 'LocationFeatureSpecification', 'name' => 'Biciclette gratuite', 'value' => true ),
        ),
        'aggregateRating' => array(
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.5',
            'reviewCount' => '180',
            'bestRating'  => '5',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

/* ─────────────────────────────────────────────
 * SEO — Disabilita sitemap Yoast per author e category
 * ───────────────────────────────────────────── */
add_filter( 'wpseo_sitemap_exclude_author', '__return_true' );

add_filter( 'wpseo_sitemap_exclude_taxonomy', function( $excluded, $taxonomy ) {
    if ( 'category' === $taxonomy ) {
        return true;
    }
    return $excluded;
}, 10, 2 );

/* ─────────────────────────────────────────────
 * SEO — Noindex sample-page e author/category pages
 * ───────────────────────────────────────────── */
add_filter( 'wpseo_robots', function( $robots ) {
    if ( is_page( 'sample-page' ) || is_author() || is_category() ) {
        return 'noindex, nofollow';
    }
    return $robots;
} );

/* ─────────────────────────────────────────────
 * SEO — Title tag ottimizzati (filtro Yoast)
 * ───────────────────────────────────────────── */
add_filter( 'wpseo_title', 'rim_seo_custom_titles' );
function rim_seo_custom_titles( $title ) {
    $sep = ' | Residence I Mari';

    if ( is_front_page() ) {
        return 'Appartamenti Vacanza Castiglione della Pescaia' . $sep;
    }
    if ( is_page( 'appartamenti' ) || is_post_type_archive( 'appartamento' ) ) {
        return '9 Appartamenti a 100m dalla Spiaggia · Castiglione della Pescaia' . $sep;
    }
    if ( is_page( 'il-residence' ) ) {
        return 'Residence a 100m dal Mare, Castiglione della Pescaia' . $sep;
    }
    if ( is_page( 'tariffe' ) ) {
        return 'Tariffe 2026 Appartamenti Castiglione della Pescaia' . $sep;
    }
    if ( is_page( 'castiglione-della-pescaia' ) ) {
        return 'Castiglione della Pescaia: Spiagge, Borghi e Natura' . $sep;
    }
    if ( is_page( 'servizi' ) ) {
        return 'Servizi Inclusi e Comfort' . $sep;
    }
    if ( is_page( 'gallery' ) ) {
        return 'Foto Appartamenti e Residence' . $sep;
    }
    if ( is_page( 'posizione' ) ) {
        return 'Come Arrivare a Castiglione della Pescaia' . $sep;
    }
    if ( is_page( 'contatti' ) ) {
        return 'Contatti e Prenotazioni' . $sep;
    }
    if ( is_singular( 'appartamento' ) ) {
        return get_the_title() . ' a Castiglione della Pescaia' . $sep;
    }

    return $title;
}

/* ─────────────────────────────────────────────
 * SEO — Meta description di fallback per pagine senza Yoast desc
 * ───────────────────────────────────────────── */
add_filter( 'wpseo_metadesc', 'rim_seo_custom_metadesc' );
function rim_seo_custom_metadesc( $desc ) {
    // Non sovrascrivere se già compilata in Yoast
    if ( ! empty( $desc ) ) {
        return $desc;
    }

    if ( is_front_page() ) {
        return 'Residence I Mari: 9 appartamenti a 100 metri dal mare a Castiglione della Pescaia, Maremma Toscana. Recentemente ristrutturati, parcheggio, WiFi, pet friendly. Prenota direttamente.';
    }
    if ( is_page( 'tariffe' ) ) {
        return 'Tariffe 2025/2026 degli appartamenti Residence I Mari a Castiglione della Pescaia. Bilocali e trilocali con prezzi a partire da €80/notte. Preventivo personalizzato.';
    }
    if ( is_page( 'appartamenti' ) || is_post_type_archive( 'appartamento' ) ) {
        return 'Scopri i 9 appartamenti del Residence I Mari a Castiglione della Pescaia: bilocali e trilocali a 100m dal mare con giardino e parcheggio.';
    }
    if ( is_page( 'il-residence' ) ) {
        return 'Residence I Mari: appartamenti vacanza nel cuore di Castiglione della Pescaia, Maremma Toscana. A 100 metri dalla spiaggia, comfort e tranquillità.';
    }
    if ( is_page( 'castiglione-della-pescaia' ) ) {
        return 'Castiglione della Pescaia: spiagge premiate Bandiera Blu, borgo medievale e riserva naturale della Diaccia Botrona. Scopri cosa vedere e fare.';
    }
    if ( is_page( 'servizi' ) ) {
        return 'WiFi, parcheggio, biciclette, biancheria e pulizie incluse. Scopri tutti i servizi del Residence I Mari a Castiglione della Pescaia.';
    }
    if ( is_page( 'gallery' ) ) {
        return 'Foto degli appartamenti, del giardino e degli esterni del Residence I Mari a Castiglione della Pescaia.';
    }
    if ( is_page( 'posizione' ) ) {
        return 'Come arrivare al Residence I Mari: a 100 metri dal mare nel centro di Castiglione della Pescaia. Mappa, indicazioni e parcheggio.';
    }
    if ( is_page( 'contatti' ) ) {
        return 'Contatta il Residence I Mari per informazioni e prenotazioni. Telefono, email e modulo di richiesta disponibilità.';
    }
    if ( is_singular( 'appartamento' ) ) {
        $data = rim_get_apt_seo_content( get_the_ID() );
        if ( $data ) {
            // Rimuove i tag HTML dall'intro per la meta description
            return wp_strip_all_tags( $data['intro'] );
        }
    }

    return $desc;
}

/* ─────────────────────────────────────────────
 * SEO — Fix breadcrumb Yoast per single-appartamento
 *
 * Aggiunge il livello "Appartamenti" tra Home e il singolo appartamento.
 * ───────────────────────────────────────────── */
add_filter( 'wpseo_breadcrumb_links', 'rim_seo_breadcrumb_appartamento' );
function rim_seo_breadcrumb_appartamento( $links ) {
    if ( ! is_singular( 'appartamento' ) ) {
        return $links;
    }

    $apt_page = get_page_by_path( 'appartamenti' );
    if ( ! $apt_page ) {
        return $links;
    }

    $new_crumb = array(
        'url'  => get_permalink( $apt_page ),
        'text' => __( 'Appartamenti', 'residence-i-mari' ),
    );

    // Inserisci dopo "Home" (posizione 1)
    array_splice( $links, 1, 0, array( $new_crumb ) );

    return $links;
}

/* ─────────────────────────────────────────────
 * SEO — Testi descrittivi per ogni appartamento
 *
 * Restituisce contenuto HTML keyword-rich usato come
 * fallback quando il post non ha contenuto nell'editor WP.
 * Include keyword primarie per ogni appartamento:
 * nome + "Castiglione della Pescaia" + caratteristiche.
 * ───────────────────────────────────────────── */
function rim_get_apt_seo_content( $post_id ) {
    $title = strtolower( get_the_title( $post_id ) );

    // Mappa: keyword nel titolo → contenuto SEO
    $map = array(

        'adriatico 1' => array(
            'intro'      => 'L\'<strong>Appartamento Adriatico 1</strong> è un luminoso bilocale al piano terra del Residence I Mari, a soli 100 metri dalla spiaggia di Castiglione della Pescaia. Ideale per coppie e famiglie fino a 4 persone, unisce comfort moderno e atmosfera mediterranea.',
            'body'       => '<p>L\'appartamento si sviluppa su circa 45 mq con soggiorno, angolo cottura completamente attrezzato, camera matrimoniale, bagno con doccia e terrazzo privato affacciato sul giardino. I pavimenti in ceramica di Vietri e i dettagli in legno creano un\'atmosfera calda e accogliente.</p>
<p>Tutti gli ambienti sono dotati di <strong>climatizzatore</strong>, TV satellite, cassaforte e connessione WiFi gratuita. La cucina è completamente equipaggiata con frigorifero, piano cottura, microonde e tutto il necessario per soggiorni di qualsiasi durata.</p>
<p>La posizione privilegiata — a 100 metri dalla <strong>Spiaggia di Levante</strong> di Castiglione della Pescaia — permette di raggiungere il mare in meno di 2 minuti a piedi. Il parcheggio privato è incluso nel soggiorno.</p>',
            'closing'    => 'Prenota l\'Appartamento Adriatico 1 per le tue <strong>vacanze a Castiglione della Pescaia</strong> e goditi la Maremma Toscana a pochi passi dal mare.',
        ),

        'adriatico 2' => array(
            'intro'      => 'L\'<strong>Appartamento Adriatico 2</strong> è un accogliente bilocale al primo piano del Residence I Mari, con vista sul giardino interno. Perfetto per coppie o famiglie fino a 4 persone in vacanza a <strong>Castiglione della Pescaia</strong>.',
            'body'       => '<p>Con una superficie di circa 45 mq, l\'appartamento offre soggiorno con angolo cottura, camera matrimoniale, bagno con doccia e balcone. I materiali di pregio — ceramica di Vietri, infissi in legno, arredi su misura — garantiscono un soggiorno di qualità superiore.</p>
<p>Dotazioni complete: <strong>climatizzatore</strong>, TV satellite, WiFi gratuito, cassaforte e angolo cottura con tutti gli elettrodomestici. Biancheria da letto e da bagno incluse nel prezzo.</p>
<p>A 100 metri dalla spiaggia di Levante, nel cuore del lungomare di <strong>Castiglione della Pescaia</strong>, è la base ideale per esplorare la Maremma: dal Parco dell\'Uccellina alle Terme di Saturnia, dalla Cala Violina al borgo medievale.</p>',
            'closing'    => 'Scegli l\'Appartamento Adriatico 2 per una vacanza rilassante sul <strong>mare della Toscana</strong>, a due passi da tutto.',
        ),

        'egeo 1' => array(
            'intro'      => 'L\'<strong>Appartamento Egeo 1</strong> è un luminoso bilocale del Residence I Mari che combina design contemporaneo e comfort a 100 metri dalla spiaggia di <strong>Castiglione della Pescaia</strong>. Capacità fino a 4 ospiti.',
            'body'       => '<p>Circa 40 mq distribuiti tra soggiorno con angolo cottura, camera matrimoniale, bagno con doccia e terrazzo. Le ampie finestre garantiscono luminosità naturale durante tutta la giornata. I pavimenti in parquet e la ceramica di Vietri in bagno e cucina conferiscono carattere all\'ambiente.</p>
<p>Ogni comfort è incluso: <strong>aria condizionata</strong>, TV satellite, connessione WiFi ad alta velocità, cassaforte, lavatrice condivisa. L\'angolo cottura dispone di tutto il necessario per chi preferisce cucinare in autonomia.</p>
<p>La <strong>spiaggia di Levante</strong> dista meno di due minuti a piedi. Il residence offre parcheggio privato gratuito — un vantaggio non scontato in alta stagione a Castiglione della Pescaia.</p>',
            'closing'    => 'L\'Appartamento Egeo 1 è la scelta ideale per chi cerca un <strong>appartamento vacanze a Castiglione della Pescaia</strong> con tutti i comfort e la vicinanza al mare.',
        ),

        'egeo 2' => array(
            'intro'      => 'L\'<strong>Appartamento Egeo 2</strong> è un bilocale rinnovato al Residence I Mari, perfetto per coppie e famiglie in vacanza a <strong>Castiglione della Pescaia</strong>. A 100 metri dalla spiaggia, con parcheggio incluso.',
            'body'       => '<p>Spazio ottimizzato su circa 40 mq: soggiorno con angolo cottura, camera matrimoniale, bagno con doccia, balcone. Arredi moderni e funzionali, pavimenti in parquet, bagno rivestito in ceramica pregiata.</p>
<p>Dotazioni: <strong>climatizzatore</strong>, TV satellite, WiFi gratuito, cassaforte, angolo cottura completo. L\'appartamento è stato ristrutturato di recente con materiali di qualità e cura nei dettagli.</p>
<p>La posizione al centro del <strong>lungomare di Castiglione della Pescaia</strong> consente di raggiungere a piedi spiaggia, ristoranti, negozi e il porto canale. In auto, in meno di 30 minuti si raggiungono le Terme di Saturnia e il Parco della Maremma.</p>',
            'closing'    => 'Prenota l\'Appartamento Egeo 2 per le tue prossime <strong>vacanze in Maremma Toscana</strong> a pochi passi dal mare.',
        ),

        'ionio 1' => array(
            'intro'      => 'L\'<strong>Appartamento Ionio 1</strong> è un ampio bilocale del Residence I Mari, progettato per offrire il massimo comfort a coppie e famiglie fino a 4 persone in vacanza a <strong>Castiglione della Pescaia</strong>.',
            'body'       => '<p>Circa 45 mq di spazio ben organizzato: soggiorno con divano letto, angolo cottura, camera matrimoniale, bagno con doccia e balcone privato. La disposizione degli ambienti garantisce privacy e funzionalità, con ampie aree di storage e armadi a muro.</p>
<p>Tutto l\'essenziale è incluso: <strong>aria condizionata</strong>, riscaldamento autonomo, TV satellite, WiFi gratuito, cassaforte. La cucina è attrezzata con frigorifero, piano cottura, microonde, bollitore, tostapane e set completo di stoviglie.</p>
<p>A 100 metri dalla <strong>spiaggia di Castiglione della Pescaia</strong>, con accesso diretto agli stabilimenti balneari convenzionati. Il parcheggio privato coperto è incluso — ideale per chi arriva in auto dalla Toscana o da altre regioni.</p>',
            'closing'    => 'Scegli l\'Appartamento Ionio 1 per una <strong>vacanza al mare in Toscana</strong> senza rinunciare a nessun comfort.',
        ),

        'ionio 2' => array(
            'intro'      => 'L\'<strong>Appartamento Ionio 2</strong> è un accogliente bilocale del Residence I Mari, ideale per soggiorni di coppia o in famiglia a <strong>Castiglione della Pescaia</strong>. Ristrutturato, a 100 metri dalla spiaggia.',
            'body'       => '<p>Circa 45 mq con soggiorno, angolo cottura attrezzato, camera matrimoniale, bagno con doccia e terrazzo. Gli arredi moderni e le finiture di qualità creano un ambiente elegante e funzionale, perfetto per soggiorni di una settimana o più.</p>
<p>Dotazioni complete: <strong>climatizzatore</strong>, TV satellite, connessione WiFi, cassaforte, lavatrice (uso condiviso). La biancheria da letto e da bagno è inclusa e cambiata a metà soggiorno per permanenze superiori ai 7 giorni.</p>
<p>La <strong>Spiaggia di Levante di Castiglione della Pescaia</strong> è raggiungibile in 2 minuti a piedi. Il residence è pet friendly: i tuoi animali domestici sono i benvenuti.</p>',
            'closing'    => 'Prenota Ionio 2 per le tue <strong>vacanze a Castiglione della Pescaia</strong> con tutta la famiglia, animali inclusi.',
        ),

        'tirreno 1' => array(
            'intro'      => 'L\'<strong>Appartamento Tirreno 1</strong> è un bilocale raffinato al Residence I Mari di <strong>Castiglione della Pescaia</strong>, con finiture di pregio e posizione a 100 metri dalla spiaggia. Capacità fino a 4 persone.',
            'body'       => '<p>Circa 45 mq distribuiti tra soggiorno con angolo cottura, camera matrimoniale, bagno con doccia e balcone. Parquet in legno chiaro, ceramiche di Vietri e infissi in alluminio a taglio termico rendono l\'appartamento silenzioso, fresco d\'estate e caldo d\'inverno.</p>
<p>Tutte le dotazioni: <strong>climatizzatore</strong>, TV satellite HD, WiFi ad alta velocità, cassaforte, angolo cottura con elettrodomestici moderni. Il parcheggio privato riservato agli ospiti è uno dei punti di forza del residence.</p>
<p>Dal Residence I Mari si raggiunge a piedi la spiaggia, i ristoranti del lungomare, il porto canale e il mercato di <strong>Castiglione della Pescaia</strong>. In auto, in 20 minuti si arriva a Cala Violina, una delle spiagge più belle della Toscana.</p>',
            'closing'    => 'Tirreno 1 è la scelta perfetta per una <strong>vacanza sul mare in Maremma</strong> con comfort da residence di qualità.',
        ),

        'tirreno 2' => array(
            'intro'      => 'L\'<strong>Appartamento Tirreno 2</strong> è un bilocale luminoso e recentemente rinnovato al Residence I Mari, situato a soli 100 metri dalla spiaggia di <strong>Castiglione della Pescaia</strong>.',
            'body'       => '<p>Con circa 45 mq di superficie abitabile, l\'appartamento offre soggiorno con angolo cottura, camera matrimoniale, bagno con doccia e balcone privato. Le finiture sono state curate nei minimi dettagli: ceramica di Vietri, parquet, porte interne in legno massello.</p>
<p>Dotazioni: <strong>aria condizionata</strong>, riscaldamento autonomo, TV satellite, WiFi gratuito, cassaforte, cucina completa. Biancheria da letto e da bagno incluse. Parcheggio privato coperto incluso nel prezzo.</p>
<p>La posizione centrale nel <strong>lungomare di Castiglione della Pescaia</strong> permette di raggiungere tutto a piedi: spiaggia, supermercato, farmacia, bar e ristoranti sono tutti entro 200 metri dall\'appartamento.</p>',
            'closing'    => 'Scegli Tirreno 2 per un <strong>soggiorno vacanza a Castiglione della Pescaia</strong> senza rinunciare alla comodità.',
        ),

        'mediterraneo' => array(
            'intro'      => 'L\'<strong>Appartamento Mediterraneo</strong> è il più spazioso del Residence I Mari: un trilocale da 50 mq ideale per famiglie numerose o gruppi fino a 5 persone in vacanza a <strong>Castiglione della Pescaia</strong>.',
            'body'       => '<p>Il layout include soggiorno con angolo cottura, due camere (una matrimoniale e una con letti singoli), bagno con doccia e ampio terrazzo. Lo spazio superiore rispetto ai bilocali lo rende particolarmente adatto a famiglie con bambini o a soggiorni prolungati di 2 settimane o più.</p>
<p>Dotazioni premium: <strong>climatizzatore</strong> in ogni ambiente, TV satellite, WiFi ad alta velocità, cassaforte, cucina completamente attrezzata con lavastoviglie. Parcheggio privato incluso, biciclette disponibili per gli ospiti.</p>
<p>A 100 metri dalla <strong>spiaggia di Levante di Castiglione della Pescaia</strong>, è la soluzione ideale per famiglie che vogliono la comodità di un appartamento con i servizi di un residence: pulizia inclusa, cambio biancheria, reception.</p>
<p>Il Mediterraneo è perfetto anche come base per esplorare la <strong>Maremma Toscana</strong>: il Parco dell\'Uccellina, Vetulonia etrusca, le Terme di Saturnia e l\'isola d\'Elba sono tutte destinazioni raggiungibili in giornata.</p>',
            'closing'    => 'Prenota l\'Appartamento Mediterraneo per la tua <strong>vacanza in famiglia a Castiglione della Pescaia</strong>: il più grande, il più comodo, a 100 metri dal mare.',
        ),

    );

    // Cerca corrispondenza per keyword nel titolo
    foreach ( $map as $keyword => $content ) {
        if ( strpos( $title, $keyword ) !== false ) {
            return $content;
        }
    }

    return null;
}

/**
 * Genera HTML completo del testo SEO per un appartamento.
 *
 * @param  int $post_id Post ID.
 * @return string       HTML pronto da stampare.
 */
function rim_render_apt_seo_content( $post_id ) {
    $data = rim_get_apt_seo_content( $post_id );
    if ( ! $data ) {
        return '';
    }

    $html  = '<p class="apt-intro"><strong>' . wp_kses_post( $data['intro'] ) . '</strong></p>';
    $html .= wp_kses_post( $data['body'] );
    $html .= '<p class="apt-closing">' . wp_kses_post( $data['closing'] ) . '</p>';

    return $html;
}
