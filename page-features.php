<?php
/**
 * Template Name: PhotoVault Features
 *
 * @package PhotoVault
 */

get_header();

$capabilities = array(
	array( 'number' => '01', 'title' => __( 'Aperçus adaptés', 'photovault' ), 'copy' => __( 'Les grilles utilisent des miniatures générées par WordPress. Le navigateur ne reçoit pas le fichier 4K pendant l’exploration.', 'photovault' ) ),
	array( 'number' => '02', 'title' => __( 'Visionneuse intégrée', 'photovault' ), 'copy' => __( 'Une œuvre peut être agrandie, affichée en plein écran et parcourue avec les commandes précédente et suivante.', 'photovault' ) ),
	array( 'number' => '03', 'title' => __( 'Accès maîtrisés', 'photovault' ), 'copy' => __( 'Les collections privées suivent les autorisations accordées, la propriété du média et les capacités administrateur.', 'photovault' ) ),
	array( 'number' => '04', 'title' => __( 'Protection serveur', 'photovault' ), 'copy' => __( 'Les aperçus protégés passent par un contrôle d’accès et reçoivent un filigrane avant leur remise au navigateur.', 'photovault' ) ),
	array( 'number' => '05', 'title' => __( 'Livraison HD', 'photovault' ), 'copy' => __( 'L’original est transmis uniquement par le flux de téléchargement prévu, après vérification des droits et journalisation.', 'photovault' ) ),
	array( 'number' => '06', 'title' => __( 'Espace personnel', 'photovault' ), 'copy' => __( 'Favoris, téléchargements, shootings, profil et sécurité sont réunis dans une interface cohérente pour chaque client.', 'photovault' ) ),
);
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28">
		<div class="mx-auto grid max-w-[90rem] gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:px-12">
			<div class="lg:col-span-8"><p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Plateforme / Fonctionnement', 'photovault' ); ?></p><h1 class="mt-7 max-w-5xl font-serif text-5xl leading-[1.04] text-white sm:text-7xl"><?php esc_html_e( 'Une galerie rapide pour regarder. Rigoureuse pour livrer.', 'photovault' ); ?></h1></div>
			<p class="max-w-xl self-end text-base leading-8 text-gray-400 lg:col-span-4"><?php esc_html_e( 'Le niveau de qualité servi dépend de l’action : aperçu léger pour naviguer, version protégée pour examiner, original uniquement lorsque le téléchargement est autorisé.', 'photovault' ); ?></p>
		</div>
	</header>

	<section class="mx-auto max-w-[90rem] px-5 py-16 sm:px-8 lg:px-12 lg:py-24" aria-labelledby="capabilities-title">
		<div class="mb-12 grid gap-6 lg:grid-cols-12"><h2 id="capabilities-title" class="font-serif text-4xl text-white lg:col-span-7"><?php esc_html_e( 'Ce que PhotoVault prend réellement en charge', 'photovault' ); ?></h2><p class="text-sm leading-7 text-gray-400 lg:col-span-4 lg:col-start-9"><?php esc_html_e( 'Chaque fonction ci-dessous correspond à un parcours actif de la plateforme.', 'photovault' ); ?></p></div>
		<div class="grid border-t border-white/10 md:grid-cols-2 xl:grid-cols-3">
			<?php foreach ( $capabilities as $index => $capability ) : ?>
				<article class="border-b border-white/10 py-9 md:px-8 <?php echo 0 === $index % 2 ? 'md:border-r md:pl-0' : ''; ?> <?php echo 0 === $index % 3 ? 'xl:pl-0' : ''; ?>">
					<span class="text-xs font-extrabold text-amber-200"><?php echo esc_html( $capability['number'] ); ?></span>
					<h3 class="mt-5 text-xl font-bold text-white"><?php echo esc_html( $capability['title'] ); ?></h3>
					<p class="mt-3 max-w-sm text-sm leading-7 text-gray-400"><?php echo esc_html( $capability['copy'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="border-y border-white/10 bg-[#11100e]">
		<div class="mx-auto grid max-w-[90rem] gap-12 px-5 py-16 sm:px-8 lg:grid-cols-12 lg:px-12 lg:py-24">
			<div class="lg:col-span-4"><p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( 'Flux de livraison', 'photovault' ); ?></p><h2 class="mt-5 font-serif text-4xl leading-tight text-white"><?php esc_html_e( 'De l’import à l’original autorisé.', 'photovault' ); ?></h2></div>
			<ol class="grid gap-0 border-t border-white/10 lg:col-span-7 lg:col-start-6">
				<li class="grid grid-cols-[3rem_1fr] gap-4 border-b border-white/10 py-6"><span class="text-xs font-extrabold text-amber-200">01</span><div><strong class="text-white"><?php esc_html_e( 'Importer et documenter', 'photovault' ); ?></strong><p class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Titre, série, catégorie, statut et protection accompagnent chaque média.', 'photovault' ); ?></p></div></li>
				<li class="grid grid-cols-[3rem_1fr] gap-4 border-b border-white/10 py-6"><span class="text-xs font-extrabold text-amber-200">02</span><div><strong class="text-white"><?php esc_html_e( 'Générer les aperçus', 'photovault' ); ?></strong><p class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Les formats de navigation sont créés et servis à une taille cohérente avec leur affichage.', 'photovault' ); ?></p></div></li>
				<li class="grid grid-cols-[3rem_1fr] gap-4 border-b border-white/10 py-6"><span class="text-xs font-extrabold text-amber-200">03</span><div><strong class="text-white"><?php esc_html_e( 'Accorder l’accès', 'photovault' ); ?></strong><p class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Une demande peut être examinée et liée à une collection sans rendre celle-ci publique.', 'photovault' ); ?></p></div></li>
				<li class="grid grid-cols-[3rem_1fr] gap-4 border-b border-white/10 py-6"><span class="text-xs font-extrabold text-amber-200">04</span><div><strong class="text-white"><?php esc_html_e( 'Télécharger et tracer', 'photovault' ); ?></strong><p class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Le fichier HD n’est délivré qu’après contrôle, puis l’opération rejoint l’historique.', 'photovault' ); ?></p></div></li>
			</ol>
		</div>
	</section>

	<section class="mx-auto flex max-w-[90rem] flex-col items-start justify-between gap-8 px-5 py-20 sm:px-8 lg:flex-row lg:items-end lg:px-12 lg:py-28"><h2 class="max-w-4xl font-serif text-4xl leading-tight text-white sm:text-6xl"><?php esc_html_e( 'Explorez sans charger ce que vous n’avez pas demandé.', 'photovault' ); ?></h2><a class="pv-header-cta min-h-12 shrink-0 px-6" href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>"><?php esc_html_e( 'Ouvrir la galerie', 'photovault' ); ?></a></section>
</main>
<?php get_footer(); ?>
