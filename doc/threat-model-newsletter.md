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
- Futures campagnes, templates, provider credentials et rapports.

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
- Future queue d'envoi vers provider email externe.

## Menaces principales

| Menace | Scenario | Controle actuel | Gap/test requis |
| --- | --- | --- | --- |
| CSRF subscribe | Soumettre un email sans intention | Nonce + consentement | Tester nonce/consentement manquants |
| Liste bombing | Abonner massivement des emails tiers | Email valide + nonce | Ajouter rate limit/double opt-in |
| Enumeration abonnes | Deduire si email existe | Redirects generiques partiels | Tester messages neutres |
| Unsubscribe abuse | Deviner token pour desabonner quelqu'un | Token 64 hex HMAC | Tester token invalide/inconnu/idempotence |
| Export fuite | Role non autorise exporte CSV | `newsletter_view_reports` + nonce | Test role matrix |
| Status tampering | Changer statut par POST forge | `newsletter_manage_subscribers`, nonce, whitelist | Tests non-admin/status invalide |
| Stockage excessif | Garder IP brute/user-agent complet | IP hash, user-agent tronque | Ajouter retention/suppression |
| Injection CSV | Email ou source mal interprete dans tableur | `fputcsv` | Ajouter neutralisation formules si export publicise |
| Provider secret leak | Futures clefs SMTP/API en DB ou Git | Non implemente | Secrets hors Git/options protegees |
| Audit exposure | Journaliser des donnees personnelles inutiles | Contexte nettoye, IP hash, user-agent tronque | Tester absence email/token/IP brute |
| Campaign tampering | Passer une campagne en sending/sent sans droit | Transitions serveur + capabilities create/send + nonce | Tests roles create vs send |
| Queue delivery abuse | Declencher un batch ou retenter trop vite | Capability send, nonce, limite batch, backoff | Tests role/nonce/backoff |
| Reporting exposure | Montrer des donnees de campagne a un role non autorise | Capability newsletter_view_reports | Tests roles reports |
| Segment injection | Injecter champ, operateur ou valeur dans le ciblage | Champs fixes, IDs bornes, valeurs via placeholders `wpdb::prepare` | Tester charges SQL et IDs inexistants |
| Audience tampering | Affecter un abonne ou cibler un segment sans droit | Capability manage_lists/create/send, nonces et existence serveur | Tests de role et nonce |
| Envoi abusif | Campagne envoyee sans confirmation/audit | Transitions, capabilities, audit et queue bornee | Ajouter confirmation finale et limites par campagne |

## Controles existants

- Subscribe public avec nonce, consentement et validation email.
- Email hash et IP hash stockes.
- Unsubscribe public par token serveur sans email dans l'URL.
- Admin abonnes protege par `newsletter_manage_subscribers`.
- Export CSV protege par `newsletter_view_reports` et nonce.
- Status whitelist: `subscribed`, `unsubscribed`, `suppressed`.
- Creation liste/tag/segment/thematique et affectations protegees par capability newsletter_manage_lists, nonce et validation serveur.
- Regles dynamiques limitees a listes, tags, sources et dates; aucun identifiant SQL ne vient de la requete.
- Audit newsletter protege par capability newsletter_view_reports, avec IP hash, user-agent tronque et contexte nettoye.
- Campagnes protegees par capability newsletter_create_campaigns; transitions d'envoi protegees par newsletter_send_campaigns.
- Queue batch protegee par newsletter_send_campaigns pour l'action manuelle, traitement cron borne, verrou atomique, contrainte campagne/abonne, reprise stale et retry/backoff.
- Provider wp_mail configurable sans secret; providers API externes attendus via filtre et secrets hors Git.
- Reports campagne limites aux totaux queue et proteges par newsletter_view_reports.

## Gaps prioritaires

1. Ajouter rate limiting et/ou double opt-in pour inscription publique.
2. Ajouter retention/suppression des donnees abonnes.
3. Ajouter neutralisation CSV contre formules si les exports sont ouverts a plus de roles.
4. Finaliser imports/exports robustes pour listes, segments et tags.
5. Ajouter templates reutilisables avances et previsualisation email.
6. Brancher provider API externe et superviser le cron de traitement queue.
7. Ajouter snapshot d'audience, estimation avant envoi et journal de destinataires.

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
