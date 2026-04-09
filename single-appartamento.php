<?php
/**
 * Single apartment template.
 *
 * @package Residence_I_Mari
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $apt_id     = get_the_ID();
    $sqm        = get_post_meta( $apt_id, 'rim_sqm', true );
    $guests     = get_post_meta( $apt_id, 'rim_guests', true );
    $rooms      = get_post_meta( $apt_id, 'rim_rooms', true );
    $base_price = get_post_meta( $apt_id, 'rim_base_price', true );
    $amenities  = rim_get_amenities( $apt_id );
    $gallery    = get_post_meta( $apt_id, 'rim_gallery', true );
    $gallery    = is_array( $gallery ) ? $gallery : array();

    // Build hero slider images: featured image + gallery
    $hero_images = array();
    if ( has_post_thumbnail() ) {
        $hero_images[] = get_the_post_thumbnail_url( $apt_id, 'full' );
    }
    foreach ( $gallery as $img_id ) {
        $url = wp_get_attachment_image_url( $img_id, 'full' );
        if ( $url && ! in_array( $url, $hero_images, true ) ) {
            $hero_images[] = $url;
        }
    }
    $total_slides = count( $hero_images );
    ?>

    <!-- APT HERO -->
    <section class="apt-hero">
        <div class="apt-hero__slider">
            <?php foreach ( $hero_images as $index => $img_url ) : ?>
                <div class="apt-hero__slide<?php echo 0 === $index ? ' active' : ''; ?>" style="background-image: url('<?php echo esc_url( $img_url ); ?>')"></div>
            <?php endforeach; ?>
        </div>
        <div class="apt-hero__overlay"></div>
        <div class="apt-hero__content">
            <a href="<?php echo esc_url( home_url( '/#appartamenti' ) ); ?>" class="apt-hero__back">&larr; <?php esc_html_e( 'Tutti gli appartamenti', 'residence-i-mari' ); ?></a>
            <h1 class="apt-hero__title"><?php the_title(); ?></h1>
            <div class="apt-hero__meta">
                <?php if ( $sqm ) : ?>
                    <span><?php echo esc_html( $sqm ); ?> mq</span>
                <?php endif; ?>
                <?php if ( $guests ) : ?>
                    <span><?php echo esc_html( $guests ); ?> <?php esc_html_e( 'ospiti', 'residence-i-mari' ); ?></span>
                <?php endif; ?>
                <?php if ( $rooms ) : ?>
                    <span><?php echo esc_html( $rooms ); ?> <?php echo esc_html( _n( 'camera', 'camere', (int) $rooms, 'residence-i-mari' ) ); ?></span>
                <?php endif; ?>
                <?php if ( $base_price ) : ?>
                    <span class="apt-hero__price">da <?php echo esc_html( $base_price ); ?> &euro;/notte</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( $total_slides > 1 ) : ?>
            <span class="apt-hero__counter">1 / <?php echo esc_html( $total_slides ); ?></span>
            <div class="apt-hero__controls">
                <button class="apt-hero__btn apt-hero__btn--prev" aria-label="<?php esc_attr_e( 'Precedente', 'residence-i-mari' ); ?>">&#8249;</button>
                <button class="apt-hero__btn apt-hero__btn--next" aria-label="<?php esc_attr_e( 'Successiva', 'residence-i-mari' ); ?>">&#8250;</button>
            </div>
        <?php endif; ?>
    </section>

    <!-- APT DETAILS -->
    <section class="apt-details">
        <div class="container">
            <div class="apt-details__grid">
                <div class="apt-details__desc">
                    <h2><?php esc_html_e( 'L\'appartamento', 'residence-i-mari' ); ?></h2>
                    <?php the_content(); ?>
                </div>
                <div class="apt-amenities">
                    <?php if ( ! empty( $amenities ) ) : ?>
                        <h3 class="apt-amenities__title"><?php esc_html_e( 'Cosa troverai', 'residence-i-mari' ); ?></h3>
                        <div class="apt-amenities__grid">
                            <?php foreach ( $amenities as $key => $amenity ) :
                                $label = is_array( $amenity ) ? $amenity['label'] : $amenity;
                                $icon  = is_array( $amenity ) ? $amenity['icon']  : '';
                            ?>
                                <div class="apt-amenity">
                                    <?php if ( $icon ) : ?>
                                        <span class="apt-amenity__icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                                    <?php endif; ?>
                                    <span class="apt-amenity__label"><?php echo esc_html( $label ); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="apt-amenities__cta">
                        <a href="<?php echo esc_url( home_url( '/#contatti' ) ); ?>" class="btn btn--primary btn--block"><?php esc_html_e( 'Richiedi Disponibilit&agrave;', 'residence-i-mari' ); ?></a>
                        <?php $phone = get_theme_mod( 'rim_phone', '0564 937081' ); ?>
                        <a href="tel:+39<?php echo esc_attr( rim_phone_link( $phone ) ); ?>" class="btn btn--outline btn--block"><?php esc_html_e( 'Chiama Ora', 'residence-i-mari' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- APT GALLERY -->
    <?php if ( ! empty( $gallery ) ) : ?>
        <section class="apt-gallery">
            <h2 class="apt-gallery__title"><?php esc_html_e( 'Tutte le foto', 'residence-i-mari' ); ?></h2>
            <div class="apt-gallery__grid">
                <?php foreach ( $gallery as $img_id ) :
                    $img_url = wp_get_attachment_image_url( $img_id, 'large' );
                    if ( ! $img_url ) {
                        continue;
                    }
                    $img_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                    if ( empty( $img_alt ) ) {
                        $img_alt = get_the_title();
                    }
                    ?>
                    <div class="apt-gallery__item">
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- PLANIMETRIA -->
    <?php
    $floor_plan_id = get_post_meta( $apt_id, 'rim_floor_plan', true );
    if ( $floor_plan_id ) :
        $fp_url = wp_get_attachment_image_url( $floor_plan_id, 'large' );
        if ( $fp_url ) :
    ?>
        <section class="apt-floorplan">
            <div class="container">
                <h2 class="apt-floorplan__title"><?php esc_html_e( 'Planimetria', 'residence-i-mari' ); ?></h2>
                <div class="apt-floorplan__wrap">
                    <img src="<?php echo esc_url( $fp_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Planimetria %s', 'residence-i-mari' ), get_the_title() ) ); ?>" loading="lazy" class="apt-floorplan__img">
                </div>
            </div>
        </section>
    <?php
        endif;
    endif;
    ?>

    <!-- SCOPRI ANCHE -->
    <?php
    $other_apartments = new WP_Query( array(
        'post_type'      => 'appartamento',
        'posts_per_page' => 4,
        'post__not_in'   => array( $apt_id ),
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ) );

    if ( $other_apartments->have_posts() ) :
        ?>
        <section class="apt-others">
            <div class="container">
                <h2 class="apt-others__title"><?php esc_html_e( 'Scopri anche', 'residence-i-mari' ); ?></h2>
                <div class="apt-others__grid">
                    <?php
                    while ( $other_apartments->have_posts() ) :
                        $other_apartments->the_post();
                        $other_sqm = get_post_meta( get_the_ID(), 'rim_sqm', true );
                        ?>
                        <a href="<?php the_permalink(); ?>" class="apt-others__card">
                            <div class="apt-others__card-img">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
                                <?php endif; ?>
                            </div>
                            <div class="apt-others__card-body">
                                <h4><?php the_title(); ?></h4>
                                <?php if ( $other_sqm ) : ?>
                                    <span><?php echo esc_html( $other_sqm ); ?> mq</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php
        wp_reset_postdata();
    endif;
    ?>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox__close" aria-label="<?php esc_attr_e( 'Chiudi', 'residence-i-mari' ); ?>">&times;</button>
        <button class="lightbox__prev" aria-label="<?php esc_attr_e( 'Precedente', 'residence-i-mari' ); ?>">&#8249;</button>
        <button class="lightbox__next" aria-label="<?php esc_attr_e( 'Successiva', 'residence-i-mari' ); ?>">&#8250;</button>
        <img class="lightbox__img" src="" alt="">
    </div>

<?php endwhile; ?>

<?php get_footer(); ?>
