<?php
/**
 * Homepage / Landing page.
 *
 * Mostra preview di ogni sezione con CTA alle pagine dedicate.
 *
 * @package Residence_I_Mari
 */

defined( 'ABSPATH' ) || exit;

get_header();

$theme_uri      = get_template_directory_uri();
$google_rating  = get_theme_mod( 'rim_google_rating', '4.5/5' );
$google_reviews = get_theme_mod( 'rim_google_reviews_count', '180' );

// Hero
$hero_pre   = get_theme_mod( 'rim_hero_pretitle', 'Benvenuti al' );
$hero_title = get_theme_mod( 'rim_hero_title', 'Residence I Mari — Appartamenti a Castiglione della Pescaia sul Mare' );
$hero_sub   = get_theme_mod( 'rim_hero_subtitle', 'A 100 metri dalla spiaggia · Via Ansedonia, 10 · Castiglione della Pescaia (GR) ★★★' );

// Intro
$intro_title = get_theme_mod( 'rim_intro_title', 'Un angolo di Maremma<br>a due passi dal mare' );
$intro_p1    = get_theme_mod( 'rim_intro_p1', 'Il Residence I Mari è un complesso di <strong>9 appartamenti di recentissima ristrutturazione</strong>, situato a soli 100 metri dal mare nel cuore di Castiglione della Pescaia, una delle località più belle della costa toscana.' );
$intro_p2    = get_theme_mod( 'rim_intro_p2', 'Ogni appartamento è stato progettato con cura: ampie finestre luminose, climatizzatori, pavimenti in parquet o ceramica di Vietri, TV satellite e cassaforte.' );
?>

<!-- HERO -->
<section class="hero" id="hero">
    <div class="hero__slider" id="heroSlider">
        <div class="hero__slide active" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/esterni/Esterni-e-Hall-2.jpg' ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/esterni/facciata-residence.png' ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/castiglione/castello-tramonto-wide.jpg' ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/castiglione/lungomare-aerea.jpg' ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/castiglione/tramonto-spiaggia.jpg' ); ?>')"></div>
        <div class="hero__slide" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/esterni/Esterni-e-Hall-3.jpg' ); ?>')"></div>
    </div>
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <p class="hero__pre"><?php echo esc_html( $hero_pre ); ?></p>
        <h1 class="hero__title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="hero__sub"><?php echo esc_html( $hero_sub ); ?></p>
        <div class="hero__actions">
            <a href="<?php echo esc_url( rim_get_page_url( 'appartamenti' ) ); ?>" class="btn btn--primary btn--lg">Scopri gli Appartamenti</a>
            <a href="<?php echo esc_url( rim_get_page_url( 'contatti' ) ); ?>" class="btn btn--outline-light btn--lg">Richiedi Preventivo</a>
        </div>
    </div>
    <div class="hero__scroll">
        <span>Scorri</span>
        <div class="hero__scroll-line"></div>
    </div>
</section>

<!-- IL RESIDENCE (preview) -->
<section class="section section--intro">
    <div class="container">
        <div class="intro">
            <div class="intro__text">
                <span class="section__tag">Il Residence</span>
                <h2 class="section__title"><?php echo wp_kses_post( $intro_title ); ?></h2>
                <p class="intro__desc"><?php echo wp_kses_post( $intro_p1 ); ?></p>
                <p class="intro__desc"><?php echo wp_kses_post( $intro_p2 ); ?></p>
                <div class="intro__badges">
                    <div class="badge">
                        <span class="badge__icon">&#9733;</span>
                        <span class="badge__text"><?php echo esc_html( $google_rating ); ?> Google<br><small><?php echo esc_html( $google_reviews ); ?> recensioni</small></span>
                    </div>
                    <div class="badge">
                        <span class="badge__icon">&#127958;</span>
                        <span class="badge__text">100m<br><small>dal mare</small></span>
                    </div>
                    <div class="badge">
                        <span class="badge__icon">&#128062;</span>
                        <span class="badge__text">Pet<br><small>Friendly</small></span>
                    </div>
                </div>
                <div style="margin-top: 32px;">
                    <a href="<?php echo esc_url( rim_get_page_url( 'il-residence' ) ); ?>" class="btn btn--outline">Scopri di più</a>
                </div>
            </div>
            <div class="intro__images">
                <div class="intro__img intro__img--main">
                    <img src="<?php echo esc_url( $theme_uri . '/img/esterni/facciata-residence.png' ); ?>" alt="Residence I Mari - Facciata a Castiglione della Pescaia" loading="lazy">
                </div>
                <div class="intro__img intro__img--accent">
                    <img src="<?php echo esc_url( $theme_uri . '/img/esterni/retro-residence.png' ); ?>" alt="Residence I Mari - Giardino" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- APPARTAMENTI (preview: 3 cards) -->
<section class="section section--apartments">
    <div class="container">
        <span class="section__tag">I Nostri Appartamenti</span>
        <h2 class="section__title">9 appartamenti, ognuno con il nome di un mare</h2>
        <p class="section__subtitle">Tutti recentemente ristrutturati, a pochi passi dalla spiaggia</p>

        <div class="apartments-grid">
            <?php
            $apartments = new WP_Query( array(
                'post_type'      => 'appartamento',
                'posts_per_page' => 3,
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
                    $amenities  = rim_get_amenities( $apt_id );
                    $title_parts = explode( ' ', get_the_title() );
                    $badge_text  = $title_parts[0];
                    ?>
                    <article class="apt-card">
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
                                <?php if ( $sqm ) : ?><span><?php echo esc_html( $sqm ); ?> mq</span><?php endif; ?>
                                <?php if ( $guests ) : ?><span><?php echo esc_html( $guests ); ?> ospiti</span><?php endif; ?>
                                <?php if ( $rooms ) : ?><span><?php echo esc_html( $rooms ); ?> <?php echo esc_html( _n( 'camera', 'camere', (int) $rooms, 'residence-i-mari' ) ); ?></span><?php endif; ?>
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
                            <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--block">Scopri l'Appartamento</a>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                // Fallback statico se non ci sono CPT
                $fallback_apts = array(
                    array( 'name' => 'Adriatico 1', 'badge' => 'Adriatico', 'img' => 'adriatico/Adriatico-1.jpg', 'sqm' => '45', 'guests' => '4', 'rooms' => '1', 'desc' => 'Appartamento luminoso con arredi moderni, climatizzatore, parquet e ceramica di Vietri.' ),
                    array( 'name' => 'Egeo 1', 'badge' => 'Egeo', 'img' => 'egeo/Egeo-1.jpg', 'sqm' => '40', 'guests' => '4', 'rooms' => '1', 'desc' => 'Spazioso e accogliente, con finiture di pregio e vista luminosa.' ),
                    array( 'name' => 'Mediterraneo', 'badge' => 'Mediterraneo', 'img' => 'egeo/Egeo-9.jpg', 'sqm' => '50', 'guests' => '5', 'rooms' => '2', 'desc' => 'Il più ampio della struttura, perfetto per famiglie numerose o soggiorni prolungati.' ),
                );
                foreach ( $fallback_apts as $apt ) :
                    ?>
                    <article class="apt-card">
                        <div class="apt-card__gallery">
                            <div class="apt-card__img"><img src="<?php echo esc_url( $theme_uri . '/img/appartamenti/' . $apt['img'] ); ?>" alt="<?php echo esc_attr( $apt['name'] ); ?>" loading="lazy"></div>
                            <span class="apt-card__badge"><?php echo esc_html( $apt['badge'] ); ?></span>
                        </div>
                        <div class="apt-card__body">
                            <h3 class="apt-card__title"><?php echo esc_html( $apt['name'] ); ?></h3>
                            <div class="apt-card__meta"><span><?php echo esc_html( $apt['sqm'] ); ?> mq</span><span><?php echo esc_html( $apt['guests'] ); ?> ospiti</span><span><?php echo esc_html( $apt['rooms'] ); ?> <?php echo (int) $apt['rooms'] > 1 ? 'camere' : 'camera'; ?></span></div>
                            <p class="apt-card__desc"><?php echo esc_html( $apt['desc'] ); ?></p>
                            <div class="apt-card__amenities"><span>Climatizzatore</span><span>TV Satellite</span><span>Cassaforte</span><span>Angolo cottura</span></div>
                        </div>
                    </article>
                    <?php
                endforeach;
            endif;
            ?>
        </div>

        <div style="text-align: center; margin-top: 48px;">
            <a href="<?php echo esc_url( rim_get_page_url( 'appartamenti' ) ); ?>" class="btn btn--primary btn--lg">Vedi Tutti gli Appartamenti</a>
        </div>
    </div>
</section>

<!-- SERVIZI (preview) -->
<section class="section section--services">
    <div class="container">
        <span class="section__tag">Servizi</span>
        <h2 class="section__title">Tutto per il vostro comfort</h2>
        <div class="services-grid">
            <div class="service">
                <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg></div>
                <h3 class="service__title">Appartamenti Ristrutturati</h3>
                <p class="service__desc">Finiture di pregio: parquet, ceramica di Vietri, arredi moderni e funzionali.</p>
            </div>
            <div class="service">
                <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                <h3 class="service__title">Climatizzazione</h3>
                <p class="service__desc">Aria condizionata e riscaldamento autonomo in ogni appartamento.</p>
            </div>
            <div class="service">
                <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></div>
                <h3 class="service__title">Parcheggio Interno</h3>
                <p class="service__desc">Parcheggio privato riservato agli ospiti del residence.</p>
            </div>
            <div class="service">
                <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                <h3 class="service__title">Pet Friendly</h3>
                <p class="service__desc">I vostri amici a quattro zampe sono i benvenuti.</p>
            </div>
            <div class="service">
                <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <h3 class="service__title">Spiaggia a 100m</h3>
                <p class="service__desc">Stabilimenti balneari convenzionati raggiungibili comodamente a piedi.</p>
            </div>
            <div class="service">
                <div class="service__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 003 15.546"/></svg></div>
                <h3 class="service__title">Servizi a 50m</h3>
                <p class="service__desc">Alimentari, bar, farmacia, ristoranti e pizzerie a pochi passi.</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 48px;">
            <a href="<?php echo esc_url( rim_get_page_url( 'servizi' ) ); ?>" class="btn btn--outline-light btn--lg">Tutti i Servizi e Tariffe</a>
        </div>
    </div>
</section>

<!-- CASTIGLIONE (preview: 5 cards) -->
<section class="section section--castiglione">
    <div class="container">
        <span class="section__tag">La Destinazione</span>
        <h2 class="section__title">Castiglione della Pescaia</h2>
        <p class="section__subtitle">Bandiera Blu, una delle perle della Maremma Toscana</p>
    </div>
    <div class="castiglione-grid">
        <div class="castiglione-card castiglione-card--hero">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/spiaggia-castello.jpg' ); ?>" alt="Spiaggia di Levante con il castello di Castiglione della Pescaia" loading="lazy">
            <div class="castiglione-card__overlay"><h3>Spiaggia di Levante</h3><p>Sabbia dorata a 100 metri dal Residence</p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/castello-panorama.jpg' ); ?>" alt="Castello e borgo medievale" loading="lazy">
            <div class="castiglione-card__overlay"><h3>Il Castello</h3><p>Borgo medievale con vista mozzafiato</p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/centro-storico.jpg' ); ?>" alt="Centro storico" loading="lazy">
            <div class="castiglione-card__overlay"><h3>Centro Storico</h3><p>Vicoli fioriti e botteghe artigiane</p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/porto-canale.jpg' ); ?>" alt="Porto canale" loading="lazy">
            <div class="castiglione-card__overlay"><h3>Porto Canale</h3><p>Vista aerea del porto turistico</p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/tramonto-spiaggia.jpg' ); ?>" alt="Tramonto sulla spiaggia" loading="lazy">
            <div class="castiglione-card__overlay"><h3>Tramonti</h3><p>Spettacolo ogni sera sulla costa</p></div>
        </div>
    </div>
    <div style="text-align: center; padding: 48px 0 0;">
        <a href="<?php echo esc_url( rim_get_page_url( 'castiglione-della-pescaia' ) ); ?>" class="btn btn--primary btn--lg">Scopri Castiglione della Pescaia</a>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <h2 class="cta-banner__title">Prenota il tuo soggiorno</h2>
    <p class="cta-banner__text">Contattaci per verificare la disponibilità e ricevere un preventivo personalizzato.</p>
    <a href="<?php echo esc_url( rim_get_page_url( 'contatti' ) ); ?>" class="btn btn--outline-light btn--lg" style="margin-right: 12px;">Contattaci</a>
    <a href="<?php echo esc_url( rim_get_page_url( 'tariffe' ) ); ?>" class="btn btn--outline-light btn--lg">Vedi Tariffe</a>
</section>

<?php get_footer(); ?>
