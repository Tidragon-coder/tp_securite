# Installation du projet

```bash
php -S localhost:8080
```

Les comptes (user/password) :
- alice/alice123
- bob/bob123
- admin/admin

Il faut faire un audit complet de sécurité sur ce projet PHP dans un fichier nommé 'reponses.md', puis il faut corriger ces failles de sécurité.

# CORRECTION :

# Audit de sécurité – Projet PHP

## Faille type 1 : Injection SQL

**Fichier :**  
- `login.php`
- `admin.php :`
  - `delete_user`
  - `set_role`
  - `delete_product`
  - `delete_review`
  - `add_balance`
- `product.php :`
  - Récupération du produit
  - Application du coupon
  - Validation de la commande
  - Balance et stock mis à jour après la commande
- `search.php :`
  - Requête de recherche
- `profile.php :`
  - Mise à jour du profil
  - Changement de mot de passe
  - Suppression du compte
- `api.php :`
  - `search`
  - `user`
  - `orders`
  - `transfer` (Attention, cette faille est critique car elle permet de transférer de l'argent vers n'importe quel compte)
  - `delete_all_reviews`
  - `raw_query`


**Source de la faille :**  
Entrées utilisateur utilisées directement dans la requête SQL.

**Manipulation (exemple pourr Login.php) :**  
```html
admin' OR '1'='1' --
```

**Impact :**  
Possibilité d'injection SQL et donc de se connecter en tant qu'administrateur (ou n'importe quel autre utilisateur).

**Correction :**  
Utiliser des requêtes préparées avec les variable comme :password.

---

## Faille type 2 : Cross Site Scripting (XSS)

**Fichier :**  
- `message.php`
- `product.php :`
  - `contenu des avis`
- `search.php :`
  - `champ de recherche`
  - `affichage du nombre de résultats`
- `profile.php :`
  - `bio`

**Source de la faille :**  
Lancement de code JavaScript à partir d'une entrée utilisateur non filtrée.

**Manipulation :**
```html
<script>alert('XSS')</script>
```

**Correction :**  
Utiliser le fonction `htmlspecialchars()`.

---

## Faille type 3 : Cross Site Request Forgery (CSRF)

**Fichier :**  
- `header.php`

**Source de la faille :**  
Absence de protection contre les requêtes intersites (CSRF) pour les actions sensibles.

**Manipulation :**
Un attaquant peut créer une page malveillante qui envoie une requête POST à `admin.php` pour supprimer un produit ou changer le rôle d'un utilisateur lorsque l'administrateur visite cette page.
```html
<form method="POST" action="admin.php">
  <input type="hidden" name="action" value="delete_product">
  <input type="hidden" name="pid" value="1">
  <button type="submit">Supprimer le produit 1</button>
</form>
```

**Correction :**  
- Implémenter des tokens CSRF pour les formulaires sensibles.
- Configurer les cookies de session avec les attributs `HttpOnly`, `Secure` et `SameSite`.

---

## Faille type 4 : Autre faille mineure de sécurité

**Fichier :**  
- `register.php`

**Source de la faille :**  
Absence de caractères minimum pour le mot de passe.

**Manipulation :**
Un utilisateur peut s'inscrire avec un mot de passe très faible, comme "123".

**Correction :**  
- Ajouter une validation côté serveur pour s'assurer que les mots de passe ont une longueur minimale 