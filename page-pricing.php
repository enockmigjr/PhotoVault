<?php
/**
 * Template Name: PhotoVault Pricing
 *
 * @package PhotoVault
 */

$services = array(
	array(
		'number' => '01',
		'type'   => 'portrait',
		'title'  => __( 'Portrait privé', 'photovault' ),
		'copy'   => __( 'Une séance individuelle, éditoriale ou intime, préparée autour de votre présence et de l’usage final des images.', 'photovault' ),
		'items'  => array( __( 'Préparation de l’intention et du lieu', 'photovault' ), __( 'Direction pendant la séance', 'photovault' ), __( 'Sélection et retouche des images retenues', 'photovault' ), __( 'Livraison dans une galerie privée', 'photovault' ) ),
	),
	array(
		'number' => '02',
		'type'   => 'family',
		'title'  => __( 'Couple et famille', 'photovault' ),
		'copy'   => __( 'Des images construites avec discrétion, pour conserver les liens et les gestes sans perdre leur naturel.', 'photovault' ),
		'items'  => array( __( 'Échange préalable sur les personnes présentes', 'photovault' ), __( 'Séance en extérieur, à domicile ou dans un lieu choisi', 'photovault' ), __( 'Sélection cohérente comme un petit récit', 'photovault' ), __( 'Partage sécurisé avec les proches', 'photovault' ) ),
	),
	array(
		'number' => '03',
		'type'   => 'corporate',
		'title'  => __( 'Corporate et identité', 'photovault' ),
		'copy'   => __( 'Portraits d’équipe, direction ou reportage de marque, pensés pour rester cohérents sur tous vos supports.', 'photovault' ),
		'items'  => array( __( 'Cadrage des usages et de la direction visuelle', 'photovault' ), __( 'Organisation individuelle ou par équipe', 'photovault' ), __( 'Formats adaptés aux supports convenus', 'photovault' ), __( 'Licence professionnelle précisée au devis', 'photovault' ) ),
	),
	array(
		'number' => '04',
		'type'   => 'event',
		'title'  => __( 'Événement et reportage', 'photovault' ),
		'copy'   => __( 'Documenter une rencontre, une cérémonie ou un lancement sans interrompre ce qui est en train d’arriver.', 'photovault' ),
		'items'  => array( __( 'Repérage du programme et des moments essentiels', 'photovault' ), __( 'Présence ajustée à la durée réelle', 'photovault' ), __( 'Tri éditorial et chronologie de livraison', 'photovault' ), __( 'Galerie destinée aux organisateurs ou invités', 'photovault' ) ),
	),
);

get_header();
?>

<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28 lg:py-32">
		<div class="mx-auto grid max-w-[90rem] gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:px-12">
			<div class="lg:col-span-8">
				<p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Prestations / Devis', 'photovault' ); ?></p>
				<h1 class="mt-5 max-w-5xl font-serif text-4xl leading-[1.04] text-white sm:text-5xl"><?php esc_html_e( 'Le prix vient après une intention clairement posée.', 'photovault' ); ?></h1>
			</div>
			<div class="self-end lg:col-span-4">
				<p class="text-sm leading-7 text-gray-400"><?php esc_html_e( 'Chaque devis dépend du temps de prise de vue, du lieu, du nombre d’images finales, de la retouche et des droits d’utilisation. Aucun faux tarif standardisé n’est affiché.', 'photovault' ); ?></p>
				<a href="<?php echo esc_url( add_query_arg( 'type', 'custom', home_url( '/booking/' ) ) ); ?>" class="mt-7 inline-flex min-h-12 items-center bg-white px-6 text-sm font-extrabold text-black transition hover:bg-amber-200"><?php esc_html_e( 'Présenter mon projet', 'photovault' ); ?></a>
			</div>
		</div>
	</header>

	<section class="mx-auto max-w-[90rem] px-5 py-16 sm:px-8 lg:px-12 lg:py-24" aria-labelledby="services-title">
		<div class="mb-10 border-b border-white/10 pb-7"><p class="text-xs font-bold uppercase text-gray-500"><?php esc_html_e( 'Choisir un point de départ', 'photovault' ); ?></p><h2 id="services-title" class="mt-3 text-3xl font-bold text-white"><?php esc_html_e( 'Prestations photographiques', 'photovault' ); ?></h2></div>
		<div class="divide-y divide-white/10 border-b border-white/10">
			<?php foreach ( $services as $service ) : ?>
				<article class="grid gap-8 py-10 lg:grid-cols-12 lg:py-14">
					<p class="text-sm font-bold text-amber-200 lg:col-span-1"><?php echo esc_html( $service['number'] ); ?></p>
					<div class="lg:col-span-4"><h3 class="font-serif text-3xl text-white sm:text-4xl"><?php echo esc_html( $service['title'] ); ?></h3><p class="mt-5 max-w-md text-sm leading-7 text-gray-400"><?php echo esc_html( $service['copy'] ); ?></p></div>
					<ul class="space-y-3 text-sm leading-6 text-gray-300 lg:col-span-4" aria-label="<?php echo esc_attr( sprintf( __( 'Éléments de la prestation %s', 'photovault' ), $service['title'] ) ); ?>">
						<?php foreach ( $service['items'] as $item ) : ?><li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 bg-amber-200" aria-hidden="true"></span><span><?php echo esc_html( $item ); ?></span></li><?php endforeach; ?>
					</ul>
					<div class="flex items-end lg:col-span-3 lg:justify-end"><a href="<?php echo esc_url( add_query_arg( 'type', $service['type'], home_url( '/booking/' ) ) ); ?>" class="inline-flex min-h-12 items-center border border-white/20 px-5 text-sm font-bold text-white transition hover:border-amber-200 hover:text-amber-200"><?php esc_html_e( 'Demander un devis', 'photovault' ); ?> <span class="ml-3" aria-hidden="true">&rarr;</span></a></div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="border-y border-white/10 bg-[#11100e] py-16 lg:py-20" aria-labelledby="estimate-title">
		<div class="mx-auto grid max-w-[90rem] gap-12 px-5 sm:px-8 lg:grid-cols-12 lg:px-12">
			<div class="lg:col-span-5"><p class="text-xs font-bold uppercase text-amber-200"><?php esc_html_e( 'Un devis lisible', 'photovault' ); ?></p><h2 id="estimate-title" class="mt-4 font-serif text-4xl text-white sm:text-5xl"><?php esc_html_e( 'Ce qui sera défini avant confirmation.', 'photovault' ); ?></h2></div>
			<dl class="grid gap-x-8 gap-y-7 sm:grid-cols-2 lg:col-span-7">
				<div class="border-t border-white/10 pt-4"><dt class="font-bold text-white"><?php esc_html_e( 'Périmètre', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Durée, lieux, participants et contraintes de production.', 'photovault' ); ?></dd></div>
				<div class="border-t border-white/10 pt-4"><dt class="font-bold text-white"><?php esc_html_e( 'Livrables', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Nombre d’images finales, formats, retouche et délai.', 'photovault' ); ?></dd></div>
				<div class="border-t border-white/10 pt-4"><dt class="font-bold text-white"><?php esc_html_e( 'Accès', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Galerie privée, durée de conservation et personnes autorisées.', 'photovault' ); ?></dd></div>
				<div class="border-t border-white/10 pt-4"><dt class="font-bold text-white"><?php esc_html_e( 'Usage', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Usage personnel, éditorial ou commercial et étendue de la licence.', 'photovault' ); ?></dd></div>
			</dl>
		</div>
	</section>

	<section class="px-5 py-20 text-center sm:px-8 lg:py-28">
		<h2 class="mx-auto max-w-3xl font-serif text-4xl text-white sm:text-5xl"><?php esc_html_e( 'Une demande suffit pour commencer la conversation.', 'photovault' ); ?></h2>
		<p class="mx-auto mt-6 max-w-xl text-sm leading-7 text-gray-400"><?php esc_html_e( 'La réservation devient définitive uniquement après échange, devis et confirmation de la date.', 'photovault' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/booking/' ) ); ?>" class="mt-9 inline-flex min-h-12 items-center bg-white px-7 text-sm font-extrabold text-black transition hover:bg-amber-200"><?php esc_html_e( 'Préparer ma demande', 'photovault' ); ?></a>
	</section>
</main>

<?php get_footer(); ?>
