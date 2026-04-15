<?php
/**
 * Template per la pagina "Castiglione della Pescaia" (slug: castiglione-della-pescaia).
 *
 * @package Residence_I_Mari
 */

defined( 'ABSPATH' ) || exit;
get_header();

$theme_uri = get_template_directory_uri();
?>

<!-- PAGE HERO -->
<section class="page-hero" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/castiglione/castello-tramonto-wide.jpg' ); ?>'); background-position: center 65%">
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content">
        <span class="page-hero__tag"><?php esc_html_e( 'La Destinazione', 'residence-i-mari' ); ?></span>
        <h1 class="page-hero__title"><?php esc_html_e( 'Castiglione della Pescaia', 'residence-i-mari' ); ?></h1>
        <p class="page-hero__sub"><?php esc_html_e( 'Bandiera Blu, una delle perle della Maremma Toscana', 'residence-i-mari' ); ?></p>
    </div>
</section>

<nav class="breadcrumb">
    <div class="container">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> &rsaquo; <span><?php esc_html_e( 'Castiglione della Pescaia', 'residence-i-mari' ); ?></span>
    </div>
</nav>

<!-- INTRO -->
<section class="content-section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center;">
            <div>
                <span class="section__tag"><?php esc_html_e( 'Mare e Cultura', 'residence-i-mari' ); ?></span>
                <h2 class="section__title"><?php esc_html_e( 'Il borgo sul mare della Maremma', 'residence-i-mari' ); ?></h2>
                <p style="font-size: 1rem; line-height: 1.8; margin-bottom: 16px; color: var(--c-text);">
                    <?php esc_html_e( 'Castiglione della Pescaia è uno dei borghi più affascinanti della costa toscana. Premiata con la Bandiera Blu per la qualità delle acque e dei servizi, offre spiagge di sabbia dorata, un centro storico medievale arroccato sulla collina e un porto turistico vivace.', 'residence-i-mari' ); ?>
                </p>
                <p style="font-size: 1rem; line-height: 1.8; margin-bottom: 16px; color: var(--c-text);">
                    <?php esc_html_e( 'Il territorio circostante regala esperienze uniche: dalla Riserva Naturale della Diaccia Botrona con la storica Casa Rossa Ximenes, alle colline della Maremma con i suoi vigneti e oliveti, fino ai siti archeologici etruschi di Vetulonia e Roselle.', 'residence-i-mari' ); ?>
                </p>
                <p style="font-size: 1rem; line-height: 1.8; color: var(--c-text);">
                    <?php esc_html_e( 'Il Residence I Mari si trova a 100 metri dalla Spiaggia di Levante, nel cuore del lungomare, la posizione ideale per vivere Castiglione e la Maremma.', 'residence-i-mari' ); ?>
                </p>
            </div>
            <div>
                <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/castello-panorama.jpg' ); ?>" alt="<?php esc_attr_e( 'Castello di Castiglione della Pescaia', 'residence-i-mari' ); ?>" style="width: 100%; border-radius: 4px; box-shadow: 0 8px 40px rgba(0,0,0,0.15);" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- SPIAGGE FEATURE -->
<div class="feature-row">
    <div class="feature-row__image">
        <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/spiaggia-castello.jpg' ); ?>" alt="<?php esc_attr_e( 'Spiaggia di Levante', 'residence-i-mari' ); ?>" loading="lazy">
    </div>
    <div class="feature-row__content feature-row__content--sand">
        <span class="section__tag"><?php esc_html_e( 'Le Spiagge', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( 'Sabbia dorata e acque cristalline', 'residence-i-mari' ); ?></h2>
        <p class="feature-row__desc"><?php esc_html_e( 'Castiglione della Pescaia offre chilometri di spiagge premiate con la Bandiera Blu. La Spiaggia di Levante, a soli 100 metri dal Residence, è la più comoda e attrezzata.', 'residence-i-mari' ); ?></p>
        <ul class="feature-row__list">
            <li><?php esc_html_e( 'Spiaggia di Levante — 100m dal Residence', 'residence-i-mari' ); ?></li>
            <li><?php esc_html_e( 'Spiaggia di Ponente — tramonto sul mare', 'residence-i-mari' ); ?></li>
            <li><?php esc_html_e( 'Cala Violina — baia incontaminata (20 min)', 'residence-i-mari' ); ?></li>
            <li><?php esc_html_e( 'Stabilimenti balneari convenzionati', 'residence-i-mari' ); ?></li>
        </ul>
    </div>
</div>

<!-- NATURA FEATURE -->
<div class="feature-row feature-row--reverse">
    <div class="feature-row__image">
        <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/diaccia-botrona.jpg' ); ?>" alt="<?php esc_attr_e( 'Riserva Diaccia Botrona', 'residence-i-mari' ); ?>" loading="lazy">
    </div>
    <div class="feature-row__content feature-row__content--white">
        <span class="section__tag"><?php esc_html_e( 'Natura', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( 'Riserve naturali e paesaggi unici', 'residence-i-mari' ); ?></h2>
        <p class="feature-row__desc"><?php esc_html_e( 'La Maremma è un territorio di straordinaria biodiversità. Dalle zone umide della Diaccia Botrona al Parco Naturale della Maremma, dalle colline di ulivi alle pinete che arrivano fino al mare.', 'residence-i-mari' ); ?></p>
        <ul class="feature-row__list">
            <li><?php esc_html_e( 'Riserva Naturale Diaccia Botrona', 'residence-i-mari' ); ?></li>
            <li><?php esc_html_e( 'Parco della Maremma (Uccellina)', 'residence-i-mari' ); ?></li>
            <li><?php esc_html_e( 'Casa Rossa Ximenes — museo nella riserva', 'residence-i-mari' ); ?></li>
            <li><?php esc_html_e( 'Terme di Saturnia (1 ora)', 'residence-i-mari' ); ?></li>
        </ul>
    </div>
</div>

<!-- PHOTO GRID -->
<section class="section section--castiglione">
    <div class="container">
        <span class="section__tag"><?php esc_html_e( 'Esplora', 'residence-i-mari' ); ?></span>
        <h2 class="section__title"><?php esc_html_e( 'I luoghi da non perdere', 'residence-i-mari' ); ?></h2>
    </div>
    <div class="castiglione-grid">
        <div class="castiglione-card castiglione-card--hero">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/spiaggia-castello.jpg' ); ?>" alt="<?php esc_attr_e( 'Spiaggia di Levante con il castello', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Spiaggia di Levante', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Sabbia dorata a 100 metri dal Residence', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/castello-panorama.jpg' ); ?>" alt="<?php esc_attr_e( 'Castello medievale', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Il Castello', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Borgo medievale con vista mozzafiato', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/centro-storico.jpg' ); ?>" alt="<?php esc_attr_e( 'Centro storico', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Centro Storico', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Vicoli fioriti e botteghe artigiane', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/porto-canale.jpg' ); ?>" alt="<?php esc_attr_e( 'Porto canale', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Porto Canale', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Porto turistico vivace', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/tramonto-spiaggia.jpg' ); ?>" alt="<?php esc_attr_e( 'Tramonto', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Tramonti', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Spettacolo ogni sera sulla costa', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card castiglione-card--wide">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/lungomare-aerea.jpg' ); ?>" alt="<?php esc_attr_e( 'Lungomare di Levante', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Lungomare di Levante', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Il Residence si trova qui', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/casa-rossa-riflesso.jpg' ); ?>" alt="<?php esc_attr_e( 'Casa Rossa Ximenes', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Casa Rossa Ximenes', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Museo nella riserva', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/borgo.jpg' ); ?>" alt="<?php esc_attr_e( 'Borgo', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'Il Borgo', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Atmosfera autentica', 'residence-i-mari' ); ?></p></div>
        </div>
        <div class="castiglione-card castiglione-card--wide">
            <img src="<?php echo esc_url( $theme_uri . '/img/castiglione/girasoli-maremma.jpg' ); ?>" alt="<?php esc_attr_e( 'Girasoli in Maremma', 'residence-i-mari' ); ?>" loading="lazy">
            <div class="castiglione-card__overlay"><h3><?php esc_html_e( 'La Maremma Toscana', 'residence-i-mari' ); ?></h3><p><?php esc_html_e( 'Paesaggi unici tra mare e campagna', 'residence-i-mari' ); ?></p></div>
        </div>
    </div>
</section>

<!-- PARALLAX -->
<section class="parallax-divider" style="background-image: url('<?php echo esc_url( $theme_uri . '/img/castiglione/vista-mare.jpg' ); ?>')">
    <div class="parallax-divider__overlay"></div>
    <div class="parallax-divider__content">
        <h2 class="parallax-divider__title"><?php esc_html_e( 'Un territorio da esplorare tutto l\'anno', 'residence-i-mari' ); ?></h2>
        <p class="parallax-divider__text"><?php esc_html_e( 'Dalle spiagge estive ai sentieri autunnali, dai borghi medievali alle degustazioni di vino: la Maremma offre esperienze per ogni stagione.', 'residence-i-mari' ); ?></p>
    </div>
</section>

<!-- COSA FARE -->
<section class="content-section content-section--sand">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="section__tag"><?php esc_html_e( 'Cosa Fare', 'residence-i-mari' ); ?></span>
            <h2 class="section__title"><?php esc_html_e( 'Attività e escursioni nei dintorni', 'residence-i-mari' ); ?></h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
            <div>
                <h3 style="font-family: var(--f-heading); font-size: 1.15rem; margin-bottom: 12px; color: var(--c-dark);"><?php esc_html_e( 'Mare e Spiagge', 'residence-i-mari' ); ?></h3>
                <ul style="list-style: none; line-height: 2.2; color: var(--c-text);">
                    <li>&mdash; <?php esc_html_e( 'Spiaggia di Levante (100m)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Spiaggia di Ponente', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Cala Violina (20 min)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Cala Civette', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Marina di Grosseto', 'residence-i-mari' ); ?></li>
                </ul>
            </div>
            <div>
                <h3 style="font-family: var(--f-heading); font-size: 1.15rem; margin-bottom: 12px; color: var(--c-dark);"><?php esc_html_e( 'Natura e Escursioni', 'residence-i-mari' ); ?></h3>
                <ul style="list-style: none; line-height: 2.2; color: var(--c-text);">
                    <li>&mdash; <?php esc_html_e( 'Riserva Diaccia Botrona', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Parco della Maremma (Uccellina)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Terme di Saturnia (1h)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Sentieri del castello', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Ciclabile del lungomare', 'residence-i-mari' ); ?></li>
                </ul>
            </div>
            <div>
                <h3 style="font-family: var(--f-heading); font-size: 1.15rem; margin-bottom: 12px; color: var(--c-dark);"><?php esc_html_e( 'Cultura e Borghi', 'residence-i-mari' ); ?></h3>
                <ul style="list-style: none; line-height: 2.2; color: var(--c-text);">
                    <li>&mdash; <?php esc_html_e( 'Vetulonia (sito etrusco)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Massa Marittima (30 min)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Pitigliano e Sorano (1h)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Siena (1h30)', 'residence-i-mari' ); ?></li>
                    <li>&mdash; <?php esc_html_e( 'Isola d\'Elba (traghetto)', 'residence-i-mari' ); ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- GUIDE & BLOG -->
<section class="section content-section--sand">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="section__tag"><?php esc_html_e( 'Guide Utili', 'residence-i-mari' ); ?></span>
            <h2 class="section__title"><?php esc_html_e( 'Scopri Castiglione della Pescaia', 'residence-i-mari' ); ?></h2>
            <p style="color: var(--c-text-light); max-width: 560px; margin: 0 auto;"><?php esc_html_e( 'Tutto quello che devi sapere per organizzare la tua vacanza in Maremma.', 'residence-i-mari' ); ?></p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px;">

            <a href="<?php echo esc_url( home_url( '/cosa-fare-castiglione-della-pescaia/' ) ); ?>" class="guide-card">
                <div class="guide-card__img">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/img/castiglione/castello-tramonto-wide.jpg' ); ?>"
                         alt="<?php esc_attr_e( 'Cosa fare a Castiglione della Pescaia', 'residence-i-mari' ); ?>"
                         loading="lazy">
                </div>
                <div class="guide-card__body">
                    <span class="guide-card__tag"><?php esc_html_e( 'Guida', 'residence-i-mari' ); ?></span>
                    <h3 class="guide-card__title"><?php esc_html_e( 'Cosa fare a Castiglione della Pescaia', 'residence-i-mari' ); ?></h3>
                    <p class="guide-card__desc"><?php esc_html_e( 'Spiagge, borgo medievale, riserve naturali e terme. La guida completa per esplorare la Maremma.', 'residence-i-mari' ); ?></p>
                    <span class="guide-card__link"><?php esc_html_e( 'Leggi la guida →', 'residence-i-mari' ); ?></span>
                </div>
            </a>

            <a href="<?php echo esc_url( home_url( '/spiagge-castiglione-della-pescaia/' ) ); ?>" class="guide-card">
                <div class="guide-card__img">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/img/castiglione/spiaggia-castello.jpg' ); ?>"
                         alt="<?php esc_attr_e( 'Spiagge più belle vicino a Castiglione della Pescaia', 'residence-i-mari' ); ?>"
                         loading="lazy">
                </div>
                <div class="guide-card__body">
                    <span class="guide-card__tag"><?php esc_html_e( 'Spiagge', 'residence-i-mari' ); ?></span>
                    <h3 class="guide-card__title"><?php esc_html_e( 'Le spiagge più belle della zona', 'residence-i-mari' ); ?></h3>
                    <p class="guide-card__desc"><?php esc_html_e( 'Da Cala Violina alla Spiaggia di Levante: tutte le spiagge con distanze e consigli pratici.', 'residence-i-mari' ); ?></p>
                    <span class="guide-card__link"><?php esc_html_e( 'Leggi la guida →', 'residence-i-mari' ); ?></span>
                </div>
            </a>

            <a href="<?php echo esc_url( home_url( '/vacanze-bambini-castiglione-della-pescaia/' ) ); ?>" class="guide-card">
                <div class="guide-card__img">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/img/castiglione/lungomare-aerea.jpg' ); ?>"
                         alt="<?php esc_attr_e( 'Vacanze con bambini a Castiglione della Pescaia', 'residence-i-mari' ); ?>"
                         loading="lazy">
                </div>
                <div class="guide-card__body">
                    <span class="guide-card__tag"><?php esc_html_e( 'Famiglie', 'residence-i-mari' ); ?></span>
                    <h3 class="guide-card__title"><?php esc_html_e( 'Vacanze con bambini a Castiglione', 'residence-i-mari' ); ?></h3>
                    <p class="guide-card__desc"><?php esc_html_e( 'Attività, spiagge sicure e consigli pratici per una vacanza in famiglia in Maremma Toscana.', 'residence-i-mari' ); ?></p>
                    <span class="guide-card__link"><?php esc_html_e( 'Leggi la guida →', 'residence-i-mari' ); ?></span>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <h2 class="cta-banner__title"><?php esc_html_e( 'Vivi la Maremma da vicino', 'residence-i-mari' ); ?></h2>
    <p class="cta-banner__text"><?php esc_html_e( 'Soggiorna a 100 metri dal mare nel cuore di Castiglione della Pescaia.', 'residence-i-mari' ); ?></p>
    <a href="<?php echo esc_url( rim_get_page_url( 'appartamenti' ) ); ?>" class="btn btn--outline-light btn--lg" style="margin-right: 12px;"><?php esc_html_e( 'Scopri gli Appartamenti', 'residence-i-mari' ); ?></a>
    <a href="<?php echo esc_url( rim_get_page_url( 'contatti' ) ); ?>" class="btn btn--outline-light btn--lg"><?php esc_html_e( 'Prenota Ora', 'residence-i-mari' ); ?></a>
</section>

<?php get_footer(); ?>
