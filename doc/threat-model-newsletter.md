# Threat model - Newsletter Campaign Kit

Derniere mise a jour: 2026-07-13

## Scope

Ce threat model couvre `newsletter-campaign-kit`: inscription consentie, stockage abonnes, listes, tags, unsubscribe tokenise, UI admin, campagnes, programmation WP-Cron, queue de livraison, provider et reporting.

## Actifs a proteger

- Emails abonnes et consentements.
- Tokens de desinscription.
- Hash email et IP hash.
- Statuts d'abonnement: subscribed, unsubscribed, suppressed.
- Listes editoriales, tags et liaisons de segmentation.
- Export CSV contenant des emails en clair.
- Journal audit newsletter sans IP brute, token ni email dans le contexte.
- Brouillons de campagnes, sujets, contenus editoriaux, cibles listes et statuts.
- Campagnes, templates, rapports d'import temporaires et futurs provider credentials.
- Snapshots immuables de ciblage; ID membre detache en cle opaque propre au snapshot lors d'un effacement Privacy.

## Acteurs

| Acteur | Objectif probable |
| --- | --- |
| Visiteur public | S'abonner ou abuser du formulaire |
| Abonne | Se desinscrire proprement |
| Attaquant automatise | Spam, liste bombing, enumeration token |
| Newsletter operator | Gerer abonnes et statuts |
| Analyste/report viewer | Exporter les donnees abonnes |
| Compte admin compromis | Exfiltrer liste email ou envoyer campagne abusive |

## Frontieres de confiance

- Formulaire public newsletter vers `admin-post.php`.
- Lien email unsubscribe vers `admin-post.php`.
- Admin abonnes/listes/tags vers tables custom et export CSV.
- Queue d'envoi vers provider email externe et retour webhook vers WordPress.

## Menaces principales

| Menace | Scenario | Controle actuel | Gap/test requis |
| --- | --- | --- | --- |
| CSRF subscribe | Soumettre un email sans intention | Nonce + consentement | Tester nonce/consentement manquants |
| Liste bombing | Abonner massivement des emails tiers | Double opt-in, cooldown et limites independantes reseau/adresse | Ajouter supervision distribuee en production |
| Enumeration abonnes | Deduire si email existe | Redirects generiques partiels | Tester messages neutres |
| Unsubscribe abuse | Deviner token pour desabonner quelqu'un | Token 64 hex HMAC | Tester token invalide/inconnu/idempotence |
| Export fuite | Role non autorise exporte CSV | `newsletter_view_reports` + nonce | Test role matrix |
| Status tampering | Changer statut par POST forge | `newsletter_manage_subscribers`, nonce, whitelist | Tests non-admin/status invalide |
| Stockage excessif | Garder IP brute/user-agent complet | IP hash, user-agent tronque | Ajouter retention/suppression |
| Injection CSV | Email ou source mal interprete dans tableur | `fputcsv` | Ajouter neutralisation formules si export publicise |
| Import non consenti | Reactiver ou recreer une adresse supprimee | Double capability, nonce, suppression HMAC, consentement et reactivation explicites | Ajouter matrice HTTP role/nonce/upload |
| Import partiel | Creer un abonne avant l'echec d'une audience | Validation avant ecriture et transaction par ligne | Runtime valide; tester panne DB forcee |
| Provider secret leak | Cle API ou secret webhook exposes en DB, logs ou Git | Configuration serveur uniquement, diagnostic admin booleen | Valider rotation sur l'hebergeur final |
| Provider SSRF | Endpoint configure vers le reseau interne | HTTPS obligatoire et `wp_safe_remote_post` | Tester la configuration finale |
| Webhook forge/rejoue | Fausse plainte supprimant un abonne ou rejeu massif | HMAC timestamp/corps, fenetre 5 minutes, comparaison constante et cle anti-rejeu durable | Valider avec le fournisseur reel |
| Audit exposure | Journaliser des donnees personnelles inutiles | Contexte nettoye, IP hash, user-agent tronque | Tester absence email/token/IP brute |
| Campaign tampering | Passer une campagne en sending/sent sans droit | Transitions serveur + capabilities create/send + nonce | Tests roles create vs send |
| Queue delivery abuse | Declencher un batch ou retenter trop vite | Capability send, nonce, limite batch, backoff | Tests role/nonce/backoff |
| Cron silencieux | Scheduler absent, tardif ou bloque sans signal | Heartbeat persistant, cinq etats admin, prochain evenement et verrou DB expirable | Brancher alerte externe en production |
| Pending retention | Conserver indefiniment des adresses non confirmees | Nettoyage horaire transactionnel, retention 1-90 jours, lot max 200 | Valider la duree legale finale |
| Provider crash | Exception adapter laissant une remise processing | Exception bornee par item, retry/backoff et verrou libere | Valider avec provider staging |
| Reporting exposure | Montrer des donnees de campagne a un role non autorise | Capability newsletter_view_reports | Tests roles reports |
| Segment injection | Injecter champ, operateur ou valeur dans le ciblage | Champs fixes, IDs bornes, valeurs via placeholders `wpdb::prepare` | Tester charges SQL et IDs inexistants |
| Audience tampering | Affecter un abonne ou cibler un segment sans droit | Capability manage_lists/create/send, nonces et existence serveur | Tests de role et nonce |
| Audience drift | Une liste change apres le debut d'envoi et modifie les destinataires | Snapshot unique par campagne, membres immuables et queue transactionnelle | Runtime liste modifiee et relance valide |
| Snapshot partiel | Une panne cree metadata, membres ou queue incomplets | Verrou campagne et transaction snapshot/membres/queue | Runtime cible manquante sans residu valide |
| Envoi abusif | Campagne envoyee sans confirmation/audit | Transitions, capabilities, audit et queue bornee | Ajouter confirmation finale et limites par campagne |

## Controles existants

- Subscribe public avec nonce, consentement et validation email.
- Subscribe public cree un statut pending hors audience; confirmation par token HMAC expirable et single-use avant activation.
- Cooldown de renvoi, reponse neutre et compteurs transients independants par reseau et adresse.
- Email hash et IP hash stockes.
- Unsubscribe public par token serveur sans email dans l'URL.
- Admin abonnes protege par `newsletter_manage_subscribers`.
- Export CSV protege par `newsletter_view_reports` et nonce.
- Import CSV protege par `newsletter_manage_subscribers` et `newsletter_manage_lists`, nonce, taille/lignes bornees, preview non mutative et confirmation d'application.
- Les doublons, listes/tags inconnus, suppressions actives et reactivations sans consentement explicite sont refuses avant ecriture.
- Status whitelist: `subscribed`, `unsubscribed`, `suppressed`.
- Creation liste/tag/segment/thematique et affectations protegees par capability newsletter_manage_lists, nonce et validation serveur.
- Regles dynamiques limitees a listes, tags, sources et dates; aucun identifiant SQL ne vient de la requete.
- Audit newsletter protege par capability newsletter_view_reports, avec IP hash, user-agent tronque et contexte nettoye.
- Campagnes protegees par capability newsletter_create_campaigns; transitions d'envoi protegees par newsletter_send_campaigns.
- Queue batch protegee par newsletter_send_campaigns pour l'action manuelle, traitement cron borne, verrou atomique, contrainte campagne/abonne, reprise stale et retry/backoff.
- Batch configurable 1-100, verrou scheduler DB expirable, heartbeat sans PII et retention des pending expires par lots transactionnels.
- Provider wp_mail et provider HTTP JSON generique; HTTPS obligatoire, secret serveur, timeout borne, zero redirection et idempotence stable par remise.
- Webhook bounce/complaint signe HMAC, fenetre de cinq minutes, anti-rejeu en table et suppression durable avec annulation de queue.
- Les preuves provider ne stockent pas l'email brut et perdent leur lien `subscriber_id` lors d'un effacement Privacy.
- Reports campagne limites aux totaux queue et proteges par newsletter_view_reports.
- Le premier envoi capture type, libelle, regles, topic et membres; les membres ne stockent ni email brut ni hash email, et perdent leur ID abonne lors d'un effacement Privacy.
- Envoi manuel et cron creent snapshot et queue dans une transaction, puis toute relance reutilise les memes IDs.

## Gaps prioritaires

1. Ajouter neutralisation CSV contre formules si les exports sont ouverts a plus de roles.
2. Ajouter exports robustes pour listes, segments et tags; l'import des abonnes et affectations est operationnel.
3. Ajouter templates reutilisables avances et previsualisation email.
4. Brancher un fournisseur reel sur le contrat HTTP valide et relier le heartbeat aux alertes externes.
5. Ajouter estimation/confirmation finale avant envoi et politique de retention des preuves de ciblage.

## Tests minimum avant production

1. Subscribe refuse nonce absent/invalide.
2. Subscribe refuse consentement absent.
3. Subscribe refuse email invalide.
4. Subscribe existant reste idempotent et ne fuit pas l'existence.
5. Unsubscribe refuse token invalide et token inconnu sans fuite email.
6. Unsubscribe valide change statut et reste idempotent au second clic.
7. Update status refuse non-admin et nonce invalide.
8. Update status refuse statut hors whitelist.
9. Export CSV refuse utilisateur sans `newsletter_view_reports`.
10. Export CSV ne contient pas IP brute ni token de desinscription si non necessaire.
11. Audit newsletter trace subscribe, unsubscribe, statut, export, liste et tag sans email/token/IP brute.
12. Campagne refuse creation sans newsletter_create_campaigns et refuse transition d'envoi sans newsletter_send_campaigns.
13. Queue refuse traitement sans newsletter_send_campaigns et applique retry/backoff si provider absent.
14. Provider refuse sauvegarde sans newsletter_manage_settings et ne stocke pas de secret.
15. Reports refusent acces sans newsletter_view_reports et ne pretendent pas tracker ouvertures/clics.
16. Segment refuse regle vide, date invalide, liste/tag inconnu et charge SQL.
17. Affectation abonne refuse capability, nonce, abonne ou audience inconnus.
18. Import CSV refuse capability/nonce/upload invalides et ne contourne ni suppression ni consentement.
19. Preview d'import reste non mutative; une audience inconnue ne laisse aucune ecriture partielle.
20. Une audience modifiee apres snapshot ne change ni membres ni queue; une cible disparue ne laisse aucune donnee partielle.
21. Provider HTTP refuse endpoint non HTTPS ou secret absent, normalise les erreurs et conserve la meme cle d'idempotence au retry.
22. Webhook refuse signature invalide/expiree, ignore un rejeu et transforme bounce/complaint en suppression durable sans stocker l'email brut.
