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
$hero_image = $hero_media_id ? photovault_get_secure_image_url( $hero_media_id, 'preview' ) : '';

$featured_media = new WP_Query( array(
	'post_type'      => 'media_item',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
) );

$collections = get_terms( array(
	'taxonomy'   => 'media_folder',
	'hide_empty' => false,
	'number'     => 4,
) );

$stats = photovault_get_photographer_stats( 0 );
$current_year = (int) date( 'Y' );
?>

<section class="relative min-h-[88vh] overflow-hidden bg-[#0d0c0b] flex items-end">
	<?php if ( $hero_image ) : ?>
		<div class="absolute inset-0 bg-cover bg-center scale-105 motion-safe:animate-[pvHeroDrift_18s_ease-in-out_infinite_alternate]" style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"></div>
	<?php endif; ?>
	<div class="absolute inset-0 bg-gradient-to-b from-black/35 via-[#0d0c0b]/45 to-[#0d0c0b]"></div>
	<div class="absolute inset-0 pv-grain opacity-35"></div>
	<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-16 pt-32">
		<div class="max-w-5xl">
			<p class="text-xs sm:text-sm font-black uppercase tracking-[0.38em] text-indigo-400 mb-6">PhotoVault - Archives visuelles</p>
			<h1 class="text-5xl sm:text-7xl lg:text-8xl font-black text-white tracking-tight leading-[0.92] max-w-4xl">L'Art de capturer <span class="block text-indigo-400">le temps suspendu</span></h1>
			<p class="mt-8 max-w-2xl text-base sm:text-xl text-gray-200 leading-relaxed">Le cabinet de curiosites visuelles et le portfolio officiel de l'artiste. Explorez les cliches libres en haute definition, accedez aux collections privees ou reservez une creation photographique sur mesure.</p>
			<div class="mt-10 flex flex-col sm:flex-row gap-4 max-w-md sm:max-w-none">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-all shadow-lg border border-indigo-400/20 text-center cursor-pointer">Explorer la galerie</a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="px-8 py-4 bg-white/10 hover:bg-white/15 text-white font-bold rounded-xl border border-white/20 transition-all text-center cursor-pointer backdrop-blur-md">Reserver un shooting</a>
			</div>
		</div>
		<a href="#manifeste" class="mt-16 inline-flex items-center gap-3 text-xs font-bold uppercase tracking-[0.25em] text-gray-300 hover:text-white transition-colors">Decouvrir <span class="h-10 w-px bg-indigo-400/70"></span></a>
	</div>
</section>

<section id="manifeste" class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">
		<div class="lg:col-span-4">
			<p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">01 - Manifeste</p>
			<h2 class="mt-5 text-4xl sm:text-5xl font-black text-white leading-tight">Chaque image est une trace. Chaque silence, une histoire.</h2>
		</div>
		<div class="lg:col-span-7 lg:col-start-6 space-y-6 text-gray-300 text-lg leading-relaxed">
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
				<p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Oeuvres a la une</p>
				<h2 class="mt-4 text-4xl sm:text-5xl font-black text-white">Une traversee des fragments, des visages et des lieux.</h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="text-sm font-bold text-indigo-400 hover:text-indigo-300">Voir toute la galerie &rarr;</a>
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
					<a href="<?php the_permalink(); ?>" class="group relative overflow-hidden rounded-3xl bg-gray-950 border border-gray-800 <?php echo esc_attr( $classes ); ?>">
						<div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?php echo esc_url( $image ); ?>');"></div>
						<div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
						<div class="absolute left-5 right-5 bottom-5">
							<h3 class="text-white text-xl font-black truncate"><?php the_title(); ?></h3>
							<p class="text-xs text-gray-300 mt-1"><?php echo esc_html( get_the_date( 'Y' ) ); ?> / <?php echo esc_html( $label ); ?></p>
							<span class="inline-flex mt-4 text-xs font-bold text-indigo-300 opacity-0 group-hover:opacity-100 transition-opacity">Voir l'oeuvre</span>
						</div>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div class="py-16 border border-dashed border-gray-800 rounded-3xl text-center text-gray-500">Ajoutez vos premiers medias pour composer cette selection.</div>
		<?php endif; ?>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="max-w-3xl">
			<p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Collections</p>
			<h2 class="mt-4 text-4xl sm:text-5xl font-black text-white">Des ensembles construits comme des recits visuels.</h2>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
			<?php if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) : ?>
				<?php foreach ( $collections as $i => $collection ) : ?>
					<a href="<?php echo esc_url( get_term_link( $collection ) ); ?>" class="group p-6 rounded-3xl bg-gray-950/50 border border-gray-800 hover:border-indigo-500/40 transition-all min-h-[230px] flex flex-col justify-between">
						<span class="text-xs font-black text-indigo-400">0<?php echo esc_html( $i + 1 ); ?></span>
						<div>
							<h3 class="text-2xl font-black text-white"><?php echo esc_html( $collection->name ); ?></h3>
							<p class="text-sm text-gray-400 mt-3"><?php echo esc_html( $collection->count ); ?> oeuvres classees</p>
							<p class="text-xs text-gray-500 mt-4">Archive active / acces controle</p>
						</div>
					</a>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( array( 'Fragments Urbains', 'Presences', 'Apres la pluie', 'Memoire silencieuse' ) as $i => $name ) : ?>
					<div class="p-6 rounded-3xl bg-gray-950/50 border border-gray-800 min-h-[230px] flex flex-col justify-between">
						<span class="text-xs font-black text-indigo-400">0<?php echo esc_html( $i + 1 ); ?></span>
						<div><h3 class="text-2xl font-black text-white"><?php echo esc_html( $name ); ?></h3><p class="text-sm text-gray-500 mt-3">Collection a constituer</p></div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
		<div class="p-8 sm:p-10 rounded-3xl border border-gray-800 bg-gray-950/40 min-h-[320px] flex flex-col justify-between">
			<div><p class="text-xs font-black uppercase tracking-[0.25em] text-indigo-400">Galerie publique</p><h2 class="text-4xl font-black text-white mt-4">Explorer librement</h2><p class="text-gray-400 mt-4 leading-relaxed">Decouvrez une selection de photographies accessibles en consultation fluide, classees par projet, categorie et disponibilite.</p></div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="mt-8 inline-flex text-sm font-bold text-indigo-400">Acceder a la galerie &rarr;</a>
		</div>
		<div class="p-8 sm:p-10 rounded-3xl border border-indigo-500/30 bg-indigo-950/10 min-h-[320px] flex flex-col justify-between">
			<div><p class="text-xs font-black uppercase tracking-[0.25em] text-indigo-400">Collections protegees</p><h2 class="text-4xl font-black text-white mt-4">Acceder a l'inedit</h2><p class="text-gray-400 mt-4 leading-relaxed">Certaines series, commandes privees et archives exclusives sont reservees aux visiteurs autorises.</p></div>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mt-8 inline-flex text-sm font-bold text-indigo-400">Demander un acces &rarr;</a>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
		<div class="lg:col-span-7 rounded-3xl border border-gray-800 bg-gray-950/50 p-5">
			<div class="grid grid-cols-3 gap-3 aspect-[16/10]">
				<div class="rounded-2xl bg-gray-900 border border-gray-800"></div><div class="rounded-2xl bg-gray-800 border border-gray-700"></div><div class="rounded-2xl bg-gray-900 border border-gray-800"></div>
				<div class="rounded-2xl bg-gray-800 border border-gray-700 col-span-2"></div><div class="rounded-2xl bg-gray-900 border border-gray-800"></div>
			</div>
		</div>
		<div class="lg:col-span-5 space-y-6">
			<p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Galerie libre HD</p>
			<h2 class="text-4xl sm:text-5xl font-black text-white">Libre de regarder. Libre de conserver quand l'oeuvre l'autorise.</h2>
			<p class="text-gray-400 leading-relaxed">Explorez par collection, categorie, annee, orientation, disponibilite et statut de protection. Les apercus restent legers ; les fichiers HD passent par un vrai controle de droits.</p>
			<div class="grid grid-cols-2 gap-3 text-sm text-gray-300"><span>- Collection</span><span>- Categorie</span><span>- Annee</span><span>- Orientation</span><span>- Disponibilite</span><span>- Telechargement</span></div>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
		<div class="space-y-6">
			<p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Archives confidentielles</p>
			<h2 class="text-4xl sm:text-5xl font-black text-white">Ce qui n'est pas expose n'est pas pour autant oublie.</h2>
			<p class="text-gray-400 leading-relaxed">Commandes privees, series inedites, travaux confidentiels et collections exclusives restent volontairement hors du regard public. L'acces se demande, se valide et se trace.</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="inline-flex px-7 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold">Demander un acces</a>
		</div>
		<div class="relative rounded-3xl overflow-hidden border border-gray-800 bg-gray-950 min-h-[420px]">
			<div class="absolute inset-0 bg-cover bg-center blur-sm scale-105" <?php if ( $hero_image ) : ?>style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>></div>
			<div class="absolute inset-0 bg-gradient-to-br from-indigo-950/40 via-[#0d0c0b]/70 to-[#0d0c0b]/95"></div>
			<div class="absolute inset-0 flex items-end p-8"><div><p class="text-xs font-black uppercase tracking-[0.25em] text-indigo-300">Collection confidentielle</p><h3 class="text-3xl font-black text-white mt-3">34 oeuvres</h3><p class="text-gray-300 mt-2">Acces sur autorisation</p></div></div>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<div class="max-w-3xl"><p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Services photographiques</p><h2 class="mt-4 text-4xl sm:text-5xl font-black text-white">Creer votre propre temps suspendu.</h2></div>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
			<?php foreach ( array( 'Portrait prive' => 'Portrait individuel, editorial ou intime.', 'Couple & famille' => 'Des instants construits sans perdre leur naturel.', 'Evenement' => 'Documenter sans interrompre.', 'Corporate' => 'Portraits professionnels et identite visuelle.', 'Projet artistique' => 'Collaboration creative et direction visuelle.', 'Commande sur mesure' => 'Un besoin particulier ? Construisons le projet ensemble.' ) as $title => $copy ) : ?>
				<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800"><h3 class="text-xl font-black text-white"><?php echo esc_html( $title ); ?></h3><p class="text-sm text-gray-400 mt-3 leading-relaxed"><?php echo esc_html( $copy ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">
		<div class="lg:col-span-4"><p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Processus</p><h2 class="mt-4 text-4xl font-black text-white">De la premiere idee a l'image finale.</h2></div>
		<div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-5 gap-4">
			<?php foreach ( array( 'Rencontre', 'Direction', 'Session', 'Selection', 'Livraison' ) as $i => $step ) : ?>
				<div class="border-t border-indigo-500/40 pt-4"><span class="text-xs text-indigo-400 font-black">0<?php echo esc_html( $i + 1 ); ?></span><h3 class="text-white font-black mt-3"><?php echo esc_html( $step ); ?></h3></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-20 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['total'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-500 mt-2">images conservees</p></div>
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['folders'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-500 mt-2">collections</p></div>
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['protected'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-500 mt-2">oeuvres protegees</p></div>
		<div><strong class="text-5xl font-black text-white"><?php echo esc_html( $stats['categories'] ); ?></strong><p class="text-xs uppercase tracking-wider text-gray-500 mt-2">territoires visuels</p></div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
		<h2 class="text-4xl sm:text-5xl font-black text-white">Les archives, annee apres annee.</h2>
		<div class="space-y-6">
			<?php foreach ( array( $current_year, $current_year - 1, $current_year - 2 ) as $year ) : ?>
				<div class="grid grid-cols-[80px_1fr] gap-6 items-center"><span class="text-indigo-400 font-black"><?php echo esc_html( $year ); ?></span><div class="border-t border-gray-800 pt-4 text-gray-400">Series, commandes et fragments ajoutes aux archives PhotoVault.</div></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
		<p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Leur regard apres le notre</p>
		<blockquote class="text-3xl sm:text-4xl font-black text-white leading-tight">Nous sommes venus pour quelques portraits. Nous sommes repartis avec une memoire entiere de cette periode de notre vie.</blockquote>
		<p class="text-gray-500 text-sm">Shooting portrait / temoignage client</p>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
		<div class="flex flex-col lg:flex-row justify-between gap-6"><h2 class="text-4xl sm:text-5xl font-black text-white">Carnets visuels</h2><p class="max-w-xl text-gray-400">Des notes pour comprendre la lumiere, la ville, le choix de rendre certaines images publiques et d'en proteger d'autres.</p></div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
			<?php foreach ( array( 'Photographier Cotonou apres la pluie', 'Pourquoi certaines images restent privees', 'Ce qu une archive conserve vraiment' ) as $title ) : ?>
				<article class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800"><p class="text-xs text-indigo-400 font-black uppercase tracking-wider">Journal</p><h3 class="text-xl font-black text-white mt-4"><?php echo esc_html( $title ); ?></h3><p class="text-sm text-gray-500 mt-4">Carnet editorial a publier.</p></article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-24 bg-[#0d0c0b] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
		<div><p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-400">Expositions & publications</p><h2 class="text-4xl font-black text-white mt-4">Un espace pour les collaborations, parutions et evenements.</h2></div>
		<div class="space-y-4 text-gray-400"><p>Ajoutez ici les expositions, magazines, collaborations, prix ou publications lorsque les archives s'ouvrent au public.</p><p>Cette section donne au portfolio une profondeur institutionnelle sans transformer le site en simple vitrine commerciale.</p></div>
	</div>
</section>

<section class="py-24 bg-[#11100f] border-t border-gray-900">
	<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
		<h2 class="text-4xl sm:text-5xl font-black text-white">Questions frequentes</h2>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<?php foreach ( array( 'Les photographies publiques sont-elles gratuites ?', 'Comment acceder a une collection protegee ?', 'Comment reserver un shooting ?', 'Quels formats sont disponibles au telechargement ?', 'Puis-je commander un tirage physique ?', 'Comment mes photographies privees sont-elles protegees ?' ) as $question ) : ?>
				<div class="p-5 rounded-2xl bg-gray-950/40 border border-gray-800 text-sm font-bold text-gray-200"><?php echo esc_html( $question ); ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="relative py-28 bg-[#0d0c0b] overflow-hidden border-t border-gray-900">
	<?php if ( $hero_image ) : ?><div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"></div><?php endif; ?>
	<div class="absolute inset-0 bg-[#0d0c0b]/70"></div>
	<div class="relative max-w-5xl mx-auto px-4 text-center">
		<h2 class="text-4xl sm:text-6xl font-black text-white leading-tight">Certains instants passent. D'autres meritent de rester.</h2>
		<p class="text-gray-300 text-lg mt-6 max-w-2xl mx-auto">Explorez les archives existantes ou creons ensemble les prochaines images.</p>
		<div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center"><a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl">Explorer la galerie</a><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="px-8 py-4 bg-white/10 hover:bg-white/15 text-white font-bold rounded-xl border border-white/20">Reserver un shooting</a></div>
	</div>
</section>

<?php get_footer(); ?>