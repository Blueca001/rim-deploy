<?php
/**
 * Homepage template.
 *
 * @package Residence_I_Mari
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Dati dal Customizer
$google_rating   = get_theme_mod( 'rim_google_rating', '4.5/5' );
$google_reviews  = get_theme_mod( 'rim_google_reviews_count', '141' );
$maps_embed_url  = get_theme_mod( 'rim_google_maps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2919.5!2d10.8833!3d42.7644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sVia+Montecristo+7%2C+Castiglione+della+Pescaia!5e0!3m2!1sit!2sit!4v1' );
$phone           = get_theme_mod( 'rim_phone', '0564 937081' );
$cellphone       = get_theme_mod( 'rim_cellphone', '338 8625775' );
$winter_phone    = get_theme_mod( 'rim_winter_phone', '0564 932566' );
$email           = get_theme_mod( 'rim_email', 'piccolo_hotel@virgilio.it' );
$address         = get_theme_mod( 'rim_address', 'Via Ansedonia 10, 58043 Castiglione della Pescaia (GR)' );

// Hero
$hero_pre   = get_theme_mod( 'rim_hero_pretitle', 'Benvenuti al' );
$hero_title = get_theme_mod( 'rim_hero_title', 'Residence I Mari' );
$hero_sub   = get_theme_mod( 'rim_hero_subtitle', 'A 100 metri dal mare — Castiglione della Pescaia' );

// Intro
$intro_title = get_theme_mod( 'rim_intro_title', 'Un angolo di Maremma<br>a due passi dal mare' );
$intro_p1    = get_theme_mod( 'rim_intro_p1', 'Il Residence I Mari &egrave; un complesso di <strong>9 appartamenti di recentissima ristrutturazione</strong>, situato a soli 100 metri dal mare nel cuore di Castiglione della Pescaia, una delle localit&agrave; pi&ugrave; belle della costa toscana.' );
$intro_p2    = get_theme_mod( 'rim_intro_p2', 'Ogni appartamento &egrave; stato progettato con cura: ampie finestre luminose, climatizzatori, pavimenti in parquet o ceramica di Vietri, TV satellite e cassaforte. L\'arredamento &egrave; studiato per coniugare praticit&agrave; ed eleganza.' );
$intro_p3    = get_theme_mod( 'rim_intro_p3', 'A soli 50 metri troverete alimentari, bar, farmacia, ristoranti e tutti i servizi. La spiaggia con stabilimenti balneari convenzionati &egrave; raggiungibile comodamente a piedi.' );

// Location
$location_desc = get_theme_mod( 'rim_location_desc', 'Il Residence I Mari si trova in <strong>Via Ansedonia 10</strong>, in una posizione privilegiata a pochi passi dal centro e dalla spiaggia. La localit&agrave; &egrave; una delle perle della Maremma Toscana, premiata con la Bandiera Blu per la qualit&agrave; delle acque.' );
?>

<!-- HERO -->
<section class="hero" id="hero">
    <div class="hero__slider" id="heroSlider">
        <div class="hero__slide active" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'img/esterni/Esterni-e-Hall-2.jpg' ) ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'img/esterni/facciata-residence.png' ) ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'img/castiglione/castello-tramonto-wide.jpg' ) ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'img/castiglione/lungomare-aerea.jpg' ) ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'img/castiglione/tramonto-spiaggia.jpg' ) ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( get_theme_file_uri( 'img/esterni/Esterni-e-Hall-3.jpg' ) ); ?>')"></div>
    </div>
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <p class="hero__pre"><?php echo esc_html( $hero_pre ); ?></p>
        <h1 class="hero__title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="hero__sub"><?php echo esc_html( $hero_sub ); ?></p>
        <div class="hero__actions">
            <a href="#appartamenti" class="btn btn--primary btn--lg"><?php esc_html_e( 'Scopri gli Appartamenti', 'residence-i-mari' ); ?></a>
            <a href="#" class="btn btn--outline-light btn--lg js-open-booking"><?php esc_html_e( 'Verifica Disponibilit&agrave;', 'residence-i-mari' ); ?></a>
        </div>
    </div>
    <div class="hero__scroll">
        <span><?php esc_html_e( 'Scorri', 'residence-i-mari' ); ?></span>
        <div class="hero__scroll-line"></div>
    </div>
</section>

<!-- IL RESIDENCE -->
<section class="section section--intro" id="residence">
    <div class="container">
        <div class="intro">
            <div class="intro__text">
                <span class="section__tag"><?php esc_html_e( 'Il Residence', 'residence-i-mari' ); ?></span>
                <h2 class="section__title"><?php echo wp_kses_post( $intro_title ); ?></h2>
                <p class="intro__desc"><?php echo wp_kses_post( $intro_p1 ); ?></p>
                <p class="intro__desc"><?php echo wp_kses_post( $intro_p2 ); ?></p>
                <p class="intro__desc"><?php echo wp_kses_post( $intro_p3 ); ?></p>
                <div class="intro__badges">
                    <div class="badge">
                        <span class="badge__icon">&#9733;</span>
                        <span class="badge__text"><?php echo esc_html( $google_rating ); ?> Google<br><small><?php echo esc_html( $google_reviews ); ?> <?php esc_html_e( 'recensioni', 'residence-i-mari' ); ?></small></span>
                    </div>
                    <div class="badge">
                        <span class="badge__icon">&#127958;</span>
                        <span class="badge__text">100m<br><small><?php esc_html_e( 'dal mare', 'residence-i-mari' ); ?></small></span>
                    </div>
                    <div class="badge">
                        <span class="badge__icon">&#128062;</span>
                        <span class="badge__text">Pet<br><small>Friendly</small></span>
                    </div>
                </div>
            </div>
            <div class="intro__images">
                <div class="intro__img intro__img--main">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'img/esterni/facciata-residence.png' ) ); ?>" alt="<?php esc_attr_e( 'Residence I Mari - Facciata', 'residence-i-mari' ); ?>" fetchpriority="high">
                </div>
                <div class="intro__img intro__img--accent">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'img/esterni/retro-residence.png' ) ); ?>" alt="<?php esc_attr_e( 'Residence I Mari - Ingresso con vaso blu', 'residence-i-mari' ); ?>" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- APPARTAMENTI -->
<section class="section section--apartments" id="appartamenti">
    <div class="container">
        <span class="section__tag"><?php esc_html_e( 'I Nostri Appartamenti', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( '9 appartamenti, ognuno con il nome di un mare', 'residence-i-mari' ); ?></h2>
        <p class="section__subtitle"><?php esc_html_e( 'Tutti recentemente ristrutturati, a pochi passi dalla spiaggia', 'residence-i-mari' ); ?></p>

        <div class="apartments-grid">
            <?php
            $apartments = new WP_Query( array(
                'post_type'      => 'appartamento',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'no_found_rows'  => true,
            ) );

            if ( $apartments->have_posts() ) :
                while ( $apartments->have_posts() ) :
                    $apartments->the_post();

                    $apt_id     = get_the_ID();
                    $sqm        = get_post_meta( $apt_id, 'rim_sqm', true );
                    $guests     = get_post_meta( $apt_id, 'rim_guests', true );
                    $rooms      = get_post_meta( $apt_id, 'rim_rooms', true );
                    $short_desc = get_post_meta( $apt_id, 'rim_short_description', true );
                    $base_price = get_post_meta( $apt_id, 'rim_base_price', true );
                    $amenities  = rim_get_amenities( $apt_id );
                    $slug       = sanitize_title( get_the_title() );

                    // Badge: prima parola del titolo (es. "Adriatico" da "Adriatico 1")
                    $title_parts = explode( ' ', get_the_title() );
                    $badge_text  = $title_parts[0];
                    ?>
                    <article class="apt-card" data-apartment="<?php echo esc_attr( $slug ); ?>">
                        <a href="<?php the_permalink(); ?>" class="apt-card__gallery">
                            <div class="apt-card__img">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
                                <?php endif; ?>
                            </div>
                            <span class="apt-card__badge"><?php echo esc_html( $badge_text ); ?></span>
                        </a>
                        <div class="apt-card__body">
                            <h3 class="apt-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="apt-card__meta">
                                <?php if ( $sqm ) : ?>
                                    <span><?php echo esc_html( $sqm ); ?> mq</span>
                                <?php endif; ?>
                                <?php if ( $guests ) : ?>
                                    <span><?php echo esc_html( $guests ); ?> <?php esc_html_e( 'ospiti', 'residence-i-mari' ); ?></span>
                                <?php endif; ?>
                                <?php if ( $rooms ) : ?>
                                    <span><?php echo esc_html( $rooms ); ?> <?php echo esc_html( _n( 'camera', 'camere', (int) $rooms, 'residence-i-mari' ) ); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ( $short_desc ) : ?>
                                <p class="apt-card__desc"><?php echo esc_html( $short_desc ); ?></p>
                            <?php endif; ?>
                            <?php if ( ! empty( $amenities ) ) : ?>
                                <div class="apt-card__amenities">
                                    <?php foreach ( $amenities as $amenity ) :
                                        $label = is_array( $amenity ) ? $amenity['label'] : $amenity; ?>
                                        <span><?php echo esc_html( $label ); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $base_price ) : ?>
                                <p class="apt-card__price">da <strong><?php echo esc_html( $base_price ); ?> &euro;</strong> / notte</p>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--block"><?php esc_html_e( 'Scopri l\'Appartamento', 'residence-i-mari' ); ?></a>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- SERVIZI -->
<section class="section section--services" id="servizi">
    <div class="container">
        <span class="section__tag"><?php esc_html_e( 'Servizi', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( 'Tutto per il vostro comfort', 'residence-i-mari' ); ?></h2>
        <div class="services-grid">
            <div class="service">
                <div class="service__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                </div>
                <h3 class="service__title"><?php esc_html_e( 'Appartamenti Ristrutturati', 'residence-i-mari' ); ?></h3>
                <p class="service__desc"><?php esc_html_e( 'Finiture di pregio: parquet, ceramica di Vietri, arredi moderni e funzionali.', 'residence-i-mari' ); ?></p>
            </div>
            <div class="service">
                <div class="service__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="service__title"><?php esc_html_e( 'Climatizzazione', 'residence-i-mari' ); ?></h3>
                <p class="service__desc"><?php esc_html_e( 'Aria condizionata e riscaldamento autonomo in ogni appartamento.', 'residence-i-mari' ); ?></p>
            </div>
            <div class="service">
                <div class="service__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <h3 class="service__title"><?php esc_html_e( 'Parcheggio Interno', 'residence-i-mari' ); ?></h3>
                <p class="service__desc"><?php esc_html_e( 'Parcheggio privato riservato agli ospiti del residence.', 'residence-i-mari' ); ?></p>
            </div>
            <div class="service">
                <div class="service__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="service__title"><?php esc_html_e( 'Pet Friendly', 'residence-i-mari' ); ?></h3>
                <p class="service__desc"><?php esc_html_e( 'I vostri amici a quattro zampe sono i benvenuti.', 'residence-i-mari' ); ?></p>
            </div>
            <div class="service">
                <div class="service__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="service__title"><?php esc_html_e( 'Spiaggia a 100m', 'residence-i-mari' ); ?></h3>
                <p class="service__desc"><?php esc_html_e( 'Stabilimenti balneari convenzionati raggiungibili comodamente a piedi.', 'residence-i-mari' ); ?></p>
            </div>
            <div class="service">
                <div class="service__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 003 15.546m18-3.046c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 003 12.5m18-3.046c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 003 9.454"/></svg>
                </div>
                <h3 class="service__title"><?php esc_html_e( 'Servizi a 50m', 'residence-i-mari' ); ?></h3>
                <p class="service__desc"><?php esc_html_e( 'Alimentari, bar, farmacia, ristoranti e pizzerie a pochi passi.', 'residence-i-mari' ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- CASTIGLIONE -->
<section class="section section--castiglione" id="castiglione">
    <div class="container">
        <span class="section__tag"><?php esc_html_e( 'La Destinazione', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( 'Castiglione della Pescaia', 'residence-i-mari' ); ?></h2>
        <p class="section__subtitle"><?php esc_html_e( 'Bandiera Blu, una delle perle della Maremma Toscana', 'residence-i-mari' ); ?></p>
    </div>
    <div class="castiglione-grid">
        <div class="castiglione-card castiglione-card--hero">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/spiaggia-castello.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Spiaggia di Levante con il castello', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Spiaggia di Levante', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Sabbia dorata a 100 metri dal Residence', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/castello-panorama.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Castello e borgo medievale', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Il Castello', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Borgo medievale con vista mozzafiato', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/centro-storico.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Centro storico', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Centro Storico', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Vicoli fioriti e botteghe artigiane', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/porto-canale.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Porto canale', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Porto Canale', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Vista aerea del porto turistico', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/tramonto-spiaggia.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Tramonto sulla spiaggia', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Tramonti', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Spettacolo ogni sera sulla costa', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card castiglione-card--wide">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/lungomare-aerea.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Lungomare di Levante dall\'alto', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Lungomare di Levante', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Il Residence si trova qui, a pochi passi dalla spiaggia', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/casa-rossa-riflesso.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Casa Rossa Ximenes', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Casa Rossa Ximenes', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Museo nella riserva Diaccia Botrona', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/borgo.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Borgo di Castiglione', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Il Borgo', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Atmosfera autentica della Maremma', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card castiglione-card--wide">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/girasoli-maremma.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Girasoli in Maremma', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'La Maremma Toscana', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Paesaggi unici tra mare e campagna', 'residence-i-mari' ); ?></p>
            </div>
        </div>
        <div class="castiglione-card castiglione-card--wide">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/castello-tramonto-wide.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Castiglione della Pescaia al tramonto', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay">
                <h3><?php esc_html_e( 'Il Castello al Tramonto', 'residence-i-mari' ); ?></h3>
                <p><?php esc_html_e( 'Vista panoramica sul borgo medievale', 'residence-i-mari' ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- GALLERY -->
<section class="section section--gallery" id="gallery">
    <div class="container">
        <span class="section__tag"><?php esc_html_e( 'Gallery', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( 'Scopri i nostri spazi', 'residence-i-mari' ); ?></h2>
    </div>
    <div class="gallery">
        <div class="gallery__item gallery__item--wide">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/esterni/Esterni-e-Hall-2.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Ingresso con vaso blu', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/appartamenti/adriatico/Adriatico-2.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Appartamento Adriatico', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/appartamenti/egeo/Egeo-3.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Appartamento Egeo', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item gallery__item--tall">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/esterni/Esterni-e-Hall-3.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Veranda del Residence', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/appartamenti/ionio/Ionio-3.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Appartamento Ionio', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/appartamenti/tirreno/Tirreno-3.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Appartamento Tirreno', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item gallery__item--wide">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/castiglione/spiaggia-castello.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Spiaggia di Castiglione', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/appartamenti/adriatico/Adriatico-8.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Camera Adriatico', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
        <div class="gallery__item">
            <img src="<?php echo esc_url( get_theme_file_uri( 'img/appartamenti/egeo/Egeo-7.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Appartamento Egeo', 'residence-i-mari' ); ?>" loading="lazy">
        </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox__close" aria-label="<?php esc_attr_e( 'Chiudi', 'residence-i-mari' ); ?>">&times;</button>
        <button class="lightbox__prev" aria-label="<?php esc_attr_e( 'Precedente', 'residence-i-mari' ); ?>">&#8249;</button>
        <button class="lightbox__next" aria-label="<?php esc_attr_e( 'Successiva', 'residence-i-mari' ); ?>">&#8250;</button>
        <img class="lightbox__img" src="" alt="">
    </div>
</section>

<!-- POSIZIONE -->
<section class="section section--location" id="posizione">
    <div class="container">
        <div class="location">
            <div class="location__text">
                <span class="section__tag"><?php esc_html_e( 'Posizione', 'residence-i-mari' ); ?></span>
                <h2 class="section__title"><?php echo wp_kses_post( __( 'Nel cuore di<br>Castiglione della Pescaia', 'residence-i-mari' ) ); ?></h2>
                <p class="location__desc"><?php echo wp_kses_post( $location_desc ); ?></p>
                <ul class="location__list">
                    <li><?php esc_html_e( '100m dalla spiaggia', 'residence-i-mari' ); ?></li>
                    <li><?php esc_html_e( '50m da negozi e servizi', 'residence-i-mari' ); ?></li>
                    <li><?php esc_html_e( 'Centro storico raggiungibile a piedi', 'residence-i-mari' ); ?></li>
                    <li><?php esc_html_e( 'Facile accesso in auto — parcheggio interno', 'residence-i-mari' ); ?></li>
                </ul>
                <a href="https://maps.google.com/?q=<?php echo esc_attr( rawurlencode( $address ) ); ?>" target="_blank" rel="noopener" class="btn btn--primary"><?php esc_html_e( 'Indicazioni Stradali', 'residence-i-mari' ); ?></a>
            </div>
            <div class="location__map">
                <?php if ( $maps_embed_url ) : ?>
                    <iframe
                        src="<?php echo esc_url( $maps_embed_url ); ?>"
                        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?php esc_attr_e( 'Mappa Residence I Mari', 'residence-i-mari' ); ?>">
                    </iframe>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- CONTATTI -->
<section class="section section--contact" id="contatti">
    <div class="container">
        <div class="contact">
            <div class="contact__info">
                <span class="section__tag"><?php esc_html_e( 'Contatti', 'residence-i-mari' ); ?></span>
                <h2 class="section__title"><?php esc_html_e( 'Prenota il tuo soggiorno', 'residence-i-mari' ); ?></h2>
                <p class="contact__desc"><?php esc_html_e( 'Contattaci per verificare la disponibilit&agrave; e ricevere un preventivo personalizzato.', 'residence-i-mari' ); ?></p>
                <div class="contact__details">
                    <div class="contact__item">
                        <strong><?php esc_html_e( 'Telefono', 'residence-i-mari' ); ?></strong>
                        <a href="tel:+39<?php echo esc_attr( rim_phone_link( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
                    </div>
                    <div class="contact__item">
                        <strong><?php esc_html_e( 'Cellulare', 'residence-i-mari' ); ?></strong>
                        <a href="tel:+39<?php echo esc_attr( rim_phone_link( $cellphone ) ); ?>"><?php echo esc_html( $cellphone ); ?></a>
                    </div>
                    <div class="contact__item">
                        <strong><?php esc_html_e( 'Inverno', 'residence-i-mari' ); ?></strong>
                        <a href="tel:+39<?php echo esc_attr( rim_phone_link( $winter_phone ) ); ?>"><?php echo esc_html( $winter_phone ); ?></a>
                    </div>
                    <div class="contact__item">
                        <strong><?php esc_html_e( 'Email', 'residence-i-mari' ); ?></strong>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                    </div>
                    <div class="contact__item">
                        <strong><?php esc_html_e( 'Indirizzo', 'residence-i-mari' ); ?></strong>
                        <span><?php echo esc_html( $address ); ?></span>
                    </div>
                </div>
            </div>
            <div class="contact__form-wrap">
                <form class="contact-form" id="contactForm">
                    <?php wp_nonce_field( 'rim_booking_nonce', 'rim_contact_nonce_field' ); ?>
                    <div class="contact-form__row">
                        <div class="contact-form__field">
                            <label for="cf-name"><?php esc_html_e( 'Nome *', 'residence-i-mari' ); ?></label>
                            <input type="text" id="cf-name" name="name" required>
                        </div>
                        <div class="contact-form__field">
                            <label for="cf-email"><?php esc_html_e( 'Email *', 'residence-i-mari' ); ?></label>
                            <input type="email" id="cf-email" name="email" required>
                        </div>
                    </div>
                    <div class="contact-form__row">
                        <div class="contact-form__field">
                            <label for="cf-phone"><?php esc_html_e( 'Telefono', 'residence-i-mari' ); ?></label>
                            <input type="tel" id="cf-phone" name="phone">
                        </div>
                        <div class="contact-form__field">
                            <label for="cf-apartment"><?php esc_html_e( 'Appartamento', 'residence-i-mari' ); ?></label>
                            <select id="cf-apartment" name="apartment">
                                <option value=""><?php esc_html_e( 'Seleziona...', 'residence-i-mari' ); ?></option>
                                <?php
                                $contact_apts = new WP_Query( array(
                                    'post_type'      => 'appartamento',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'menu_order',
                                    'order'          => 'ASC',
                                    'no_found_rows'  => true,
                                ) );
                                if ( $contact_apts->have_posts() ) :
                                    while ( $contact_apts->have_posts() ) :
                                        $contact_apts->the_post();
                                        echo '<option>' . esc_html( get_the_title() ) . '</option>';
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    $fallback_apts = array(
                                        'Adriatico 1', 'Adriatico 2',
                                        'Egeo 1', 'Egeo 2',
                                        'Ionio 1', 'Ionio 2',
                                        'Tirreno 1', 'Tirreno 2',
                                        'Mediterraneo',
                                    );
                                    foreach ( $fallback_apts as $apt_name ) {
                                        echo '<option>' . esc_html( $apt_name ) . '</option>';
                                    }
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="contact-form__row">
                        <div class="contact-form__field">
                            <label for="cf-checkin"><?php esc_html_e( 'Check-in', 'residence-i-mari' ); ?></label>
                            <input type="date" id="cf-checkin" name="checkin">
                        </div>
                        <div class="contact-form__field">
                            <label for="cf-checkout"><?php esc_html_e( 'Check-out', 'residence-i-mari' ); ?></label>
                            <input type="date" id="cf-checkout" name="checkout">
                        </div>
                    </div>
                    <div class="contact-form__row">
                        <div class="contact-form__field">
                            <label for="cf-guests"><?php esc_html_e( 'Ospiti', 'residence-i-mari' ); ?></label>
                            <select id="cf-guests" name="adults">
                                <option value="1">1</option>
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                    </div>
                    <div class="contact-form__field contact-form__field--full">
                        <label for="cf-message"><?php esc_html_e( 'Messaggio', 'residence-i-mari' ); ?></label>
                        <textarea id="cf-message" name="message" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn--primary btn--lg btn--block"><?php esc_html_e( 'Invia Richiesta', 'residence-i-mari' ); ?></button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
