## Projet web M1 GIL

### Documentation

#### Développement en local

```bash
php -S localhost:8000
# Accéder à : http://localhost:8000
```

### Déploiement sur serveur
Compatibilité PHP
Évitez d’utiliser les fonctionnalités de PHP 8 suivantes sans vérifier leur compatibilité avec PHP 7.4 


Pour configurer le projet pour la première fois, suivez les étapes ci-dessous :

```bash
cp -r projet-web /var/www/html
cd /var/www/html
sudo chown -R www-data:www-data projet-web
```

Une page d’installation s’affichera. Renseignez vos informations de base de données :

Hôte : localhost (ou l’adresse de votre serveur de base de données)
Nom de la base : le nom souhaité 
Utilisateur : votre nom d’utilisateur de base de données
Mot de passe : votre mot de passe de base de données

Cliquez sur le bouton « Lancer l’installation ».
L’installateur créera la base de données, les tables nécessaires et générera un fichier .env avec votre configuration.

### Avertissement de sécurité:
Après une installation réussie, vous devez supprimer le dossier /install.
Cette étape est essentielle pour éviter toute réinstallation non autorisée.

```bash
rm -rf install
```

### Admin user
Email: admin@ebazar.fr
password: admin123

