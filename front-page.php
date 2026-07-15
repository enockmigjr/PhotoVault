<?php
/**
 * Landing Page principale (front-page.php) de PhotoVault.
 *
 * @package PhotoVault
 */

get_header();

$hero_query = new WP_Query( array(
	'post_type'      => 'media_item',
	'post_status'    => 'publish',
	'posts_per_page' => 1,
) );
$hero_media_id = 0;
if ( $hero_query->have_posts() ) {
	$hero_query->the_post();
	$hero_media_id = get_the_ID();
	wp_reset_postdata();
}
$hero_image  = $hero_media_id ? photovault_get_secure_image_url( $hero_media_id, 'preview' ) : get_template_directory_uri() . '/assets/images/home/hero-archive.jpg';
$hero_credit = $hero_media_id ? '' : __( 'Photographie : Ana Markovych / Unsplash', 'photovault' );

$featured_media = new WP_Query( array(
	'post_type'      => 'media_item',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
) );

$gallery_preview = new WP_Query( array(
	'post_type'      => 'media_item',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
) );

$journal_posts = new WP_Query( array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
) );

$collections = get_terms( array(
	'taxonomy'   => 'media_folder',
	'hide_empty' => false,
	'number'     => 4,
) );

$stats = photovault_get_photographer_stats( 0 );
$archive_years = array();
$archive_ids   = get_posts(
	array(
		'post_type'      => 'media_item',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);
foreach ( $archive_ids as $archive_id ) {
	$archive_year = get_the_date( 'Y', $archive_id );
	if ( $archive_year ) {
		$archive_years[ $archive_year ] = isset( $archive_years[ $archive_year ] ) ? $archive_years[ $archive_year ] + 1 : 1;
	}
}
krsort( $archive_years, SORT_NUMERIC );

$publication_posts = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'category_name'  => 'expositions-publications',
	)
);
?>

<main id="main-content">
<section class="relative min-h-[88vh] overflow-hidden bg-[#0d0c0b] flex items-end">
	<?php if ( $hero_image ) : ?>
		<div class="absolute inset-0 bg-cover bg-center scale-105 motion-safe:animate-[pvHeroDrift_18s_ease-in-out_infinite_alternate]" style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"></div>
	<?php endif; ?>
	<div class="absolute inset-0 bg-gradient-to-b from-black/35 via-[#0d0c0b]/45 to-[#0d0c0b]"></div>
	<div class="absolute inset-0 pv-grain opacity-35"></div>
	<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-16 pt-32">
		<div class="max-w-5xl">
			<p class="mb-6 text-xs font-extrabold uppercase text-amber-200 sm:text-sm">PhotoVault / Archives visuelles</p>
			<h1 class="max-w-4xl font-serif text-5xl leading-[0.96] text-white sm:text-7xl lg:text-8xl">L’art de capturer <span class="block text-amber-200">le temps suspendu</span></h1>
			<p class="mt-8 max-w-2xl text-base sm:text-xl text-gray-200 leading-relaxed">Le cabinet de curiosites visuelles et le portfolio officiel de l'artiste. Explorez les cliches libres en haute definition, accedez aux collections privees ou reservez une creation photographique sur mesure.</p>
			<div class="mt-10 flex flex-col sm:flex-row gap-4 max-w-md sm:max-w-none">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="pv-header-cta min-h-12 justify-center px-7">Explorer la galerie</a>
				<a href="<?php echo esc_url( home_url( '/booking/' ) ); ?>" class="inline-flex min-h-12 items-center justify-center border border-white/20 bg-black/20 px-7 font-bold text-white transition hover:border-amber-200/60 hover:text-amber-100">Réserver un shooting</a>
			</div>
		</div>
		<a href="#manifeste" class="mt-16 inline-flex items-center gap-3 text-xs font-bold uppercase text-gray-200 transition-colors hover:text-white">Découvrir <span class="h-10 w-px bg-amber-200/70"></span></a>
	</div>
	<?php if ( $hero_credit ) : ?><p class="absolute bottom-4 right-5 z-10 text-[0.65rem] text-white/50 sm:right-8"><?php echo esc_html( $hero_credit ); ?></p><?php endif; ?>
</section>

<section id="manifeste" class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">
		<div class="lg:col-span-4">
			<p class="text-xs font-extrabold uppercase text-amber-200">01 / Manifeste</p>
			<h2 class="mt-5 text-4xl sm:text-5xl font-black text-white leading-tight">Chaque image est une trace. Chaque silence, une histoire.</h2>
		</div>
		<div class="lg:col-span-7 lg:col-start-6 space-y-6 text-gray-200 text-lg leading-relaxed">
			<p>Chaque photographie nait d'un instant qui ne reviendra jamais. PhotoVault conserve ces fragments comme des archives sensibles : lumiere, mouvement, memoire, visage, territoire.</p>
			<p>Ici, l'image n'est pas seulement affichee. Elle est classee, protegee, contextualisee et livree avec soin, selon son degre d'ouverture ou de confidentialite.</p>
			<p>Les galeries publiques invitent a explorer. Les collections protegees gardent leur part de silence. Les commandes sur mesure deviennent, a leur tour, de futures archives.</p>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
			<div>
				<p class="text-xs font-extrabold uppercase text-amber-200">Œuvres à la une</p>
				<h2 class="mt-4 text-4xl sm:text-5xl font-black text-white">Une traversee des fragments, des visages et des lieux.</h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="text-sm font-bold text-amber-200 hover:text-white">Voir toute la galerie &rarr;</a>
		</div>

		<?php if ( $featured_media->have_posts() ) : ?>
			<div class="grid grid-cols-1 md:grid-cols-6 auto-rows-[220px] gap-5">
				<?php $index = 0; while ( $featured_media->have_posts() ) : $featured_media->the_post(); $index++; ?>
					<?php
					$classes = 1 === $index ? 'md:col-span-4 md:row-span-2' : ( 5 === $index ? 'md:col-span-3 md:row-span-2' : 'md:col-span-2' );
					$image = photovault_get_secure_image_url( get_the_ID(), 'card' );
					$terms = get_the_terms( get_the_ID(), 'media_category' );
					$label = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Archive photographique';
					?>
					<a href="<?php the_permalink(); ?>" class="group relative overflow-hidden rounded-md bg-gray-950 border border-gray-800 <?php echo esc_attr( $classes ); ?>">
						<div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?php echo esc_url( $image ); ?>');"></div>
						<div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
						<div class="absolute left-5 right-5 bottom-5">
							<h3 class="text-white text-xl font-black truncate"><?php the_title(); ?></h3>
							<p class="text-xs text-gray-200 mt-1"><?php echo esc_html( get_the_date( 'Y' ) ); ?> / <?php echo esc_html( $label ); ?></p>
							<span class="mt-4 inline-flex text-xs font-bold text-amber-200 opacity-0 transition-opacity group-hover:opacity-100">Voir l’œuvre</span>
						</div>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div class="border-y border-gray-800 py-16 text-center text-gray-400">La sélection apparaîtra dès la publication des premières œuvres.</div>
		<?php endif; ?>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="max-w-3xl">
			<p class="text-xs font-extrabold uppercase text-amber-200">Collections</p>
			<h2 class="mt-4 text-4xl sm:text-5xl font-black text-white">Des ensembles construits comme des recits visuels.</h2>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
			<?php if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) : ?>
				<?php foreach ( $collections as $i => $collection ) : ?>
					<a href="<?php echo esc_url( get_term_link( $collection ) ); ?>" class="group flex min-h-[230px] flex-col justify-between border-t border-gray-800 py-6 transition hover:border-amber-200/60 md:px-5">
						<span class="text-xs font-black text-amber-200">0<?php echo esc_html( $i + 1 ); ?></span>
						<div>
							<h3 class="text-2xl font-black text-white"><?php echo esc_html( $collection->name ); ?></h3>
							<p class="text-sm text-gray-300 mt-3"><?php echo esc_html( $collection->count ); ?> oeuvres classees</p>
							<p class="text-xs text-gray-300 mt-4">Archive active / acces controle</p>
						</div>
					</a>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="border-y border-white/10 py-12 text-sm text-gray-400 md:col-span-2 lg:col-span-4">Les collections publiées seront présentées ici avec leur nombre réel d’œuvres.</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="mx-auto grid max-w-7xl grid-cols-1 gap-0 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
		<div class="flex min-h-[320px] flex-col justify-between border-y border-gray-800 py-8 lg:border-r lg:pr-12">
			<div><p class="text-xs font-extrabold uppercase text-amber-200">Galerie publique</p><h2 class="mt-4 font-serif text-4xl text-white">Explorer librement</h2><p class="mt-4 leading-relaxed text-gray-300">Découvrez une sélection de photographies accessibles en consultation fluide, classées par projet, catégorie et disponibilité.</p></div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="mt-8 inline-flex text-sm font-bold text-amber-200">Accéder à la galerie &rarr;</a>
		</div>
		<div class="flex min-h-[320px] flex-col justify-between border-b border-gray-800 py-8 lg:border-y lg:pl-12">
			<div><p class="text-xs font-extrabold uppercase text-amber-200">Collections protégées</p><h2 class="mt-4 font-serif text-4xl text-white">Accéder à l’inédit</h2><p class="mt-4 leading-relaxed text-gray-300">Certaines séries, commandes privées et archives exclusives sont réservées aux visiteurs autorisés.</p></div>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mt-8 inline-flex text-sm font-bold text-amber-200">Demander un accès &rarr;</a>
		</div>
	</div>
</section>


<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
		<div class="space-y-6">
			<p class="text-xs font-extrabold uppercase text-amber-200">Archives confidentielles</p>
			<h2 class="text-4xl sm:text-5xl font-black text-white">Ce qui n'est pas expose n'est pas pour autant oublie.</h2>
			<p class="text-gray-300 leading-relaxed">Commandes privees, series inedites, travaux confidentiels et collections exclusives restent volontairement hors du regard public. L'acces se demande, se valide et se trace.</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="pv-header-cta min-h-12 px-7">Demander un accès</a>
		</div>
		<div class="relative min-h-[420px] overflow-hidden rounded-md border border-gray-800 bg-gray-950">
			<div class="absolute inset-0 bg-cover bg-center blur-sm scale-105" <?php if ( $hero_image ) : ?>style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>></div>
			<div class="absolute inset-0 bg-gradient-to-br from-black/20 via-[#0d0c0b]/70 to-[#0d0c0b]/95"></div>
			<div class="absolute inset-0 flex items-end p-8"><div><p class="text-xs font-extrabold uppercase text-amber-200">Collection confidentielle</p><h3 class="mt-3 font-serif text-3xl text-white"><?php echo esc_html( $stats['protected'] ); ?> <?php echo 1 === (int) $stats['protected'] ? 'œuvre protégée' : 'œuvres protégées'; ?></h3><p class="mt-2 text-gray-200">Accès sur autorisation</p></div></div>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="max-w-3xl"><p class="text-xs font-extrabold uppercase text-amber-200">Services photographiques</p><h2 class="mt-4 font-serif text-4xl text-white sm:text-5xl">Créer votre propre temps suspendu.</h2></div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
			<?php foreach ( array( 'Portrait prive' => 'Portrait individuel, editorial ou intime.', 'Couple & famille' => 'Des instants construits sans perdre leur naturel.', 'Evenement' => 'Documenter sans interrompre.', 'Corporate' => 'Portraits professionnels et identite visuelle.', 'Projet artistique' => 'Collaboration creative et direction visuelle.', 'Commande sur mesure' => 'Un besoin particulier ? Construisons le projet ensemble.' ) as $title => $copy ) : ?>
				<a href="<?php echo esc_url( home_url( '/booking/' ) ); ?>" class="block border-t border-gray-800 py-6 transition hover:border-amber-200/60"><h3 class="text-xl font-bold text-white"><?php echo esc_html( $title ); ?></h3><p class="mt-3 text-sm leading-relaxed text-gray-300"><?php echo esc_html( $copy ); ?></p></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">
		<div class="lg:col-span-4"><p class="text-xs font-extrabold uppercase text-amber-200">Processus</p><h2 class="mt-4 font-serif text-4xl text-white">De la première idée à l’image finale.</h2></div>
		<div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-5 gap-4">
			<?php foreach ( array( 'Rencontre', 'Direction', 'Session', 'Selection', 'Livraison' ) as $i => $step ) : ?>
				<div class="border-t border-amber-200/40 pt-4"><span class="text-xs font-black text-amber-200">0<?php echo esc_html( $i + 1 ); ?></span><h3 class="mt-3 font-bold text-white"><?php echo esc_html( $step ); ?></h3></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-20 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['total'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-300 mt-2">images conservees</p></div>
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['folders'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-300 mt-2">collections</p></div>
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['protected'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-300 mt-2">oeuvres protegees</p></div>
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['categories'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-300 mt-2">territoires visuels</p></div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
			<div>
				<p class="text-xs font-extrabold uppercase text-amber-200">Chronologie</p>
				<h2 class="mt-4 font-serif text-4xl text-white sm:text-5xl">Les archives, année après année.</h2>
			</div>
			<p class="lg:col-span-2 text-gray-300 text-lg leading-relaxed">Chaque annee rassemble des commandes, des series libres, des lieux documentes et des collections parfois conservees hors du regard public. La chronologie donne un rythme a l'archive et transforme la galerie en recit.</p>
		</div>
		<div class="border-t border-white/10">
			<?php if ( $archive_years ) : ?>
				<?php foreach ( array_slice( $archive_years, 0, 6, true ) as $year => $count ) : ?>
					<div class="grid gap-5 border-b border-white/10 py-7 md:grid-cols-[120px_1fr_180px] md:items-center">
						<div class="font-serif text-3xl text-amber-200"><?php echo esc_html( $year ); ?></div>
						<div><h3 class="text-xl font-bold text-white"><?php esc_html_e( 'Œuvres publiées', 'photovault' ); ?></h3><p class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Séries et images rendues visibles dans les archives publiques au cours de cette année.', 'photovault' ); ?></p></div>
						<div class="text-sm font-bold uppercase text-gray-300 md:text-right"><?php echo esc_html( sprintf( _n( '%d image', '%d images', $count, 'photovault' ), $count ) ); ?></div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="border-b border-white/10 py-10"><h3 class="font-serif text-2xl text-white"><?php esc_html_e( 'La chronologie se construira avec les premières publications.', 'photovault' ); ?></h3><p class="mt-3 text-sm leading-7 text-gray-400"><?php esc_html_e( 'Chaque année apparaîtra automatiquement avec son nombre réel d’images publiques.', 'photovault' ); ?></p></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="max-w-3xl">
			<p class="text-xs font-extrabold uppercase text-amber-200">Leur regard après le nôtre</p>
			<h2 class="mt-4 font-serif text-4xl text-white sm:text-5xl">Des séances vécues comme des fragments de mémoire.</h2>
		</div>
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
			<?php
			$testimonials = array(
				array( 'name' => 'Nadia A.', 'type' => 'Portrait éditorial / 2026', 'image' => 'portrait-session-01.jpg', 'credit' => 'Ato Aikins / Unsplash', 'quote' => 'Nous sommes venus pour quelques portraits. Nous sommes repartis avec une mémoire entière de cette période de notre vie.' ),
				array( 'name' => 'Marius & Léa', 'type' => 'Couple et famille / 2025', 'image' => 'portrait-session-02.jpg', 'credit' => 'Unsplash', 'quote' => 'La séance a gardé quelque chose de naturel. Rien ne semblait forcé, mais tout était composé avec précision.' ),
				array( 'name' => 'Studio K.', 'type' => 'Corporate / 2025', 'image' => 'portrait-session-03.jpg', 'credit' => 'Billy Freeman / Unsplash', 'quote' => 'Les portraits ont donné une présence plus juste à notre équipe. Sobres, humains, utilisables partout.' ),
			);
			foreach ( $testimonials as $item ) :
			?>
				<article class="overflow-hidden rounded-md border border-gray-800 bg-gray-950/50">
					<figure><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/testimonials/' . $item['image'] ); ?>" width="720" height="520" alt="<?php esc_attr_e( 'Portrait éditorial d’illustration', 'photovault' ); ?>" class="h-64 w-full object-cover" loading="lazy" decoding="async"><figcaption class="sr-only"><?php echo esc_html( sprintf( __( 'Photographie d’illustration : %s', 'photovault' ), $item['credit'] ) ); ?></figcaption></figure>
					<div class="space-y-4 p-6"><blockquote class="text-lg font-bold leading-snug text-white">&laquo; <?php echo esc_html( $item['quote'] ); ?> &raquo;</blockquote><div><p class="text-sm font-black text-amber-200"><?php echo esc_html( $item['name'] ); ?></p><p class="mt-1 text-xs text-gray-300"><?php echo esc_html( $item['type'] ); ?></p><p class="mt-2 text-[0.65rem] text-gray-600"><?php echo esc_html( sprintf( __( 'Portrait d’illustration : %s', 'photovault' ), $item['credit'] ) ); ?></p></div></div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
		<div class="flex flex-col lg:flex-row justify-between gap-6"><h2 class="text-4xl sm:text-5xl font-black text-white">Carnets visuels</h2><p class="max-w-xl text-gray-300">Les articles du blog deviennent des notes d'atelier : lumiere, protection, choix de serie, ville, archive et livraison.</p></div>
		<div class="grid grid-cols-1 gap-8 md:grid-cols-3">
			<?php if ( $journal_posts->have_posts() ) : ?>
				<?php while ( $journal_posts->have_posts() ) : $journal_posts->the_post(); ?>
					<?php get_template_part( 'templates/post-card', null, array( 'heading_tag' => 'h3' ) ); ?>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="border-y border-white/10 py-12 text-sm text-gray-400 md:col-span-3">Les prochains carnets publiés apparaîtront ici avec leur illustration éditoriale.</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">
		<div class="lg:col-span-4"><p class="text-xs font-extrabold uppercase text-amber-200">Expositions & publications</p><h2 class="mt-4 font-serif text-4xl text-white">Les images sortent parfois de l’archive.</h2><p class="mt-5 text-sm leading-7 text-gray-400">Les annonces publiées dans la rubrique dédiée apparaissent ici automatiquement.</p></div>
		<div class="border-t border-white/10 lg:col-span-8">
			<?php if ( $publication_posts->have_posts() ) : ?>
				<?php while ( $publication_posts->have_posts() ) : $publication_posts->the_post(); ?>
					<a href="<?php the_permalink(); ?>" class="grid gap-4 border-b border-white/10 py-6 transition hover:border-amber-200/60 md:grid-cols-[100px_1fr_auto] md:items-center">
						<time class="font-serif text-xl text-amber-200" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date( 'Y' ) ); ?></time>
						<div><h3 class="text-xl font-bold text-white"><?php the_title(); ?></h3><p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-400"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p></div>
						<span class="text-xs font-bold uppercase text-gray-300"><?php esc_html_e( 'Lire l’annonce', 'photovault' ); ?> &rarr;</span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="border-b border-white/10 py-10"><h3 class="font-serif text-2xl text-white"><?php esc_html_e( 'Aucune exposition annoncée pour le moment.', 'photovault' ); ?></h3><p class="mt-3 text-sm leading-7 text-gray-400"><?php esc_html_e( 'Les prochaines parutions et rencontres seront publiées ici dès confirmation.', 'photovault' ); ?></p></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
		<div><p class="text-xs font-extrabold uppercase text-amber-200">FAQ</p><h2 class="mt-4 font-serif text-4xl text-white sm:text-5xl">Questions fréquentes</h2></div>
		<div class="space-y-3">
			<?php
			$faqs = array(
				'Les photographies publiques sont-elles gratuites ?' => 'Certaines images publiques peuvent etre telechargees en haute definition lorsque leur fiche l autorise. Les usages commerciaux demandent une validation specifique.',
				'Comment acceder a une collection protegee ?' => 'Envoyez une demande depuis la page contact en precisant la collection ou le projet. L acces est ensuite valide manuellement.',
				'Pourquoi certaines collections necessitent-elles une autorisation ?' => 'Les commandes privees, portraits sensibles, series inedites et archives confidentielles ne sont pas exposees sans accord.',
				'Comment reserver un shooting ?' => 'Indiquez le type de seance, la date souhaitee, le lieu et l intention visuelle. Une proposition vous est ensuite retournee.',
				'Quels formats sont disponibles au telechargement ?' => 'Les apercus restent optimises pour le web. Les fichiers originaux sont servis uniquement via le telechargement autorise.',
				'Puis-je commander un tirage physique ?' => 'Oui, une demande de tirage peut etre faite pour les oeuvres disponibles, avec choix du format, papier et usage.',
			);
			foreach ( $faqs as $question => $answer ) :
			?>
				<details class="group border-b border-gray-800 py-5">
					<summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-black text-white sm:text-base"><?php echo esc_html( $question ); ?><span class="text-amber-200 transition-transform group-open:rotate-45">+</span></summary>
					<p class="text-sm text-gray-300 leading-relaxed mt-4 max-w-3xl"><?php echo esc_html( $answer ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="relative py-28 bg-[#0d0c0b] overflow-hidden border-t border-gray-900">
	<?php if ( $hero_image ) : ?><div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"></div><?php endif; ?>
	<div class="absolute inset-0 bg-[#0d0c0b]/70"></div>
	<div class="relative max-w-5xl mx-auto px-4 text-center">
		<h2 class="text-4xl sm:text-6xl font-black text-white leading-tight">Certains instants passent. D'autres meritent de rester.</h2>
		<p class="text-gray-200 text-lg mt-6 max-w-2xl mx-auto">Explorez les archives existantes ou creons ensemble les prochaines images.</p>
		<div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row"><a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="pv-header-cta min-h-12 justify-center px-7">Explorer la galerie</a><a href="<?php echo esc_url( home_url( '/booking/' ) ); ?>" class="inline-flex min-h-12 items-center justify-center border border-white/20 bg-black/20 px-7 font-bold text-white transition hover:border-amber-200/60 hover:text-amber-100">Réserver un shooting</a></div>
	</div>
</section>
</main>

<?php get_footer(); ?>
