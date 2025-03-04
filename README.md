# Entités

- Noms de variables non explicites
- Noms de méthodes peu compréhensibles
- Les propriétés sont publiques, ce qui viole le principe de l'encapsulation. Elles devraient être privées avec des accesseurs (getters et setters).
- Gérer la logique métier au niveau des services plutôt que des entités

# Controller

- Le controller LibraryController est trop large (violation du Single Responsibility Principle - SOLID)
- LibraryController gère à la fois les livres et les utilisateurs. Il aurait fallu un contrôleur distinct pour chaque entité.
- L’accès aux entités est dupliqué dans plusieurs endroits du contrôleur - violation de DRY

# Absence de services (violation de SOLID - Single Responsibility)

- Le contrôleur accède directement aux entités et fait la logique métier. Il faudrait extraire la logique métier dans des services comme BookService et UserService.

# Aucune utilisation des repositories

- La bonne pratique est d'utiliser des repositories pour gérer les interactions avec la BDD
- Actuellement, la récupération des entités (findOneBy, find) est faite directement dans le contrôleur

</br>
</br>

# Améliorations apportées

## Changement de la base de données (des entités)

### Problèmes du code initial

- Absence de relation entre les entités : Les livres empruntés étaient stockés sous forme de tableau (array), ce qui empêchait une bonne gestion des prêts.
- Manque de flexibilité : Il était difficile d’ajouter de nouvelles informations sur les emprunts, comme la date de retour prévue.
- Données mal organisées : Le statut d’emprunt était stocké directement dans l'entité Book, ce qui limitait la gestion de l'historique des prêts.

### Modifications

**1. Création de l'entité Loan**

- Au lieu de stocker les emprunts dans un tableau dans User ou Book, une nouvelle entité Loan a été créée pour gérer les relations entre les utilisateurs et les livres.
- Un Loan est créé pour chaque emprunt, avec :
  - Le livre (book) emprunté.
  - L’utilisateur (borrower) qui l’a emprunté.
  - La date d’emprunt.
  - La date de retour prévue.
  - La date de retour effective.

**Permet :**

- Une meilleure séparation des responsabilités (SRP - SOLID).
- De garder l’historique des prêts et d'ajouter plus tard des fonctionnalités (ex: pénalités en cas de retard).

**2. Modification de l'entité Book**

- Ajout de la relation OneToMany avec Loan (un livre peut être emprunté plusieurs fois dans son historique).

**2. Modification de l'entité User**

- Ajout d’une relation OneToMany vers Loan pour suivre tous les emprunts de l’utilisateur.

# Résultat

## Respect des principes SOLID

- Séparation des responsabilités (Loan s'occupe des emprunts, User et Book sont plus clairs).
- Ouvert aux évolutions (ex: ajouter une date limite de retour).

## Amélioration de la gestion des emprunts

- Permet l’historique des prêts.
- Facile d'ajouter des notifications, des pénalités, des rappels de retour.
