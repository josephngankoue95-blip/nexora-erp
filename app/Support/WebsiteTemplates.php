<?php

namespace App\Support;

class WebsiteTemplates
{
    public static function all(): array
    {
        return [

            // IMMOBILIER
            'immobilier' => [
                'name' => 'Agence immobilière',
                'description' => 'Site vitrine pour agences immobilières et promoteurs.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'biens-immobiliers' => 'Biens immobiliers',
                    'nos-services' => 'Nos services',
                    'a-propos' => 'À propos',
                    'contact' => 'Contact',
                ],
            ],

            // ÉTABLISSEMENT SCOLAIRE
            'ecole' => [
                'name' => 'Établissement scolaire',
                'description' => 'Site de présentation pour écoles, collèges et lycées.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-etablissement' => 'Notre établissement',
                    'formations' => 'Formations',
                    'admissions' => 'Admissions',
                    'contact' => 'Contact',
                ],
            ],

            // SERVICES PROFESSIONNELS
            'services' => [
                'name' => 'Services professionnels',
                'description' => 'Site vitrine pour entreprises de prestations de services.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'nos-services' => 'Nos services',
                    'realisations' => 'Réalisations',
                    'a-propos' => 'À propos',
                    'contact' => 'Contact',
                ],
            ],

            // COMMERCE / E-COMMERCE
            'commerce' => [
                'name' => 'Commerce et E-commerce',
                'description' => 'Site de présentation pour boutiques et commerces.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'nos-produits' => 'Nos produits',
                    'nos-services' => 'Nos services',
                    'a-propos' => 'À propos',
                    'contact' => 'Contact',
                ],
            ],

            // GESTION DE STOCK
            'stock' => [
                'name' => 'Gestion de stock',
                'description' => 'Site professionnel pour distributeurs, grossistes et entreprises commerciales.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-entreprise' => 'Notre entreprise',
                    'catalogue' => 'Catalogue des produits',
                    'nos-partenaires' => 'Nos partenaires',
                    'nos-services' => 'Nos services',
                    'contact' => 'Contact',
                ],
            ],

            // HÔTEL
            'hotel' => [
                'name' => 'Hôtel',
                'description' => 'Site vitrine pour hôtels, résidences et établissements touristiques.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'chambres-et-suites' => 'Chambres et suites',
                    'nos-services' => 'Nos services',
                    'galerie' => 'Galerie',
                    'reservation' => 'Réservation',
                    'contact' => 'Contact',
                ],
            ],

            // RESTAURANT
            'restaurant' => [
                'name' => 'Restaurant',
                'description' => 'Site de présentation pour restaurants et établissements de restauration.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-carte' => 'Notre carte',
                    'specialites' => 'Nos spécialités',
                    'galerie' => 'Galerie',
                    'reservation' => 'Réservation',
                    'contact' => 'Contact',
                ],
            ],

            // CLINIQUE
            'clinique' => [
                'name' => 'Clinique',
                'description' => 'Site vitrine pour cliniques et centres de santé.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-clinique' => 'Notre clinique',
                    'specialites-medicales' => 'Spécialités médicales',
                    'equipe-medicale' => 'Équipe médicale',
                    'consultations' => 'Consultations',
                    'contact' => 'Contact',
                ],
            ],

            // HÔPITAL
            'hopital' => [
                'name' => 'Hôpital',
                'description' => 'Site institutionnel pour établissements hospitaliers.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'presentation' => 'Présentation',
                    'departements' => 'Départements',
                    'services-medicaux' => 'Services médicaux',
                    'equipe-medicale' => 'Équipe médicale',
                    'informations-pratiques' => 'Informations pratiques',
                    'contact' => 'Contact',
                ],
            ],

            // PHARMACIE
            'pharmacie' => [
                'name' => 'Pharmacie',
                'description' => 'Site vitrine pour pharmacies et parapharmacies.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-pharmacie' => 'Notre pharmacie',
                    'nos-produits' => 'Nos produits',
                    'parapharmacie' => 'Parapharmacie',
                    'conseils-sante' => 'Conseils santé',
                    'contact' => 'Contact',
                ],
            ],

            // LOCATION DE VÉHICULES
            'location-vehicules' => [
                'name' => 'Location de véhicules',
                'description' => 'Site de présentation pour agences de location automobile.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-flotte' => 'Notre flotte',
                    'nos-tarifs' => 'Nos tarifs',
                    'conditions-de-location' => 'Conditions de location',
                    'reservation' => 'Réservation',
                    'contact' => 'Contact',
                ],
            ],

            // RESSOURCES HUMAINES
            'ressources-humaines' => [
                'name' => 'Ressources humaines',
                'description' => 'Site pour cabinets RH et agences de recrutement.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-cabinet' => 'Notre cabinet',
                    'nos-services-rh' => 'Nos services RH',
                    'offres-emploi' => 'Offres d’emploi',
                    'recrutement' => 'Recrutement',
                    'contact' => 'Contact',
                ],
            ],

            // GESTION DE PROJETS
            'projets' => [
                'name' => 'Gestion de projets',
                'description' => 'Site de présentation de projets et de réalisations.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-organisation' => 'Notre organisation',
                    'nos-projets' => 'Nos projets',
                    'realisations' => 'Réalisations',
                    'nos-partenaires' => 'Nos partenaires',
                    'contact' => 'Contact',
                ],
            ],

            // MAINTENANCE
            'maintenance' => [
                'name' => 'Maintenance',
                'description' => 'Site vitrine pour entreprises de maintenance et d’assistance technique.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'notre-entreprise' => 'Notre entreprise',
                    'nos-prestations' => 'Nos prestations',
                    'domaines-intervention' => 'Domaines d’intervention',
                    'demande-intervention' => 'Demande d’intervention',
                    'contact' => 'Contact',
                ],
            ],

            // GESTION DOCUMENTAIRE
            'documents' => [
                'name' => 'Gestion documentaire',
                'description' => 'Site de présentation pour services d’archivage et de gestion documentaire.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'nos-solutions' => 'Nos solutions',
                    'gestion-documentaire' => 'Gestion documentaire',
                    'archivage' => 'Archivage',
                    'nos-services' => 'Nos services',
                    'contact' => 'Contact',
                ],
            ],

            // AGENCE WEB
            'site-web' => [
                'name' => 'Agence web',
                'description' => 'Site vitrine pour agences digitales et créateurs de sites web.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'creation-sites-web' => 'Création de sites web',
                    'nos-services' => 'Nos services',
                    'realisations' => 'Réalisations',
                    'nos-offres' => 'Nos offres',
                    'contact' => 'Contact',
                ],
            ],

            // E-LEARNING
            'elearning' => [
                'name' => 'Formation en ligne',
                'description' => 'Site de présentation pour plateformes de formation et centres e-learning.',
                'pages' => [
                    'accueil' => 'Accueil',
                    'nos-formations' => 'Nos formations',
                    'catalogue-des-cours' => 'Catalogue des cours',
                    'nos-formateurs' => 'Nos formateurs',
                    'inscription' => 'Inscription',
                    'contact' => 'Contact',
                ],
            ],

        ];
    }

    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }
}