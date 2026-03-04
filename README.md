# chrono.click — Documentation du projet

## Stack technique
- **CMS** : Joomla 6
- **Extensions** : SEBLOD 6, YOOtheme Pro
- **Hébergement** : OVH mutualisé
- **Déploiement** : manuel (pas de GitHub Actions)

## État actuel du workspace

```text
~/Sites/WebProjects/chrono.click/
├── public_html/                  ← dépôt Git (racine Joomla versionnée)
│   ├── .git/
│   ├── .gitignore
│   ├── README.md
│   ├── administrator/
│   ├── components/
│   ├── libraries/
│   ├── modules/
│   ├── plugins/
│   ├── templates/
│   ├── cache/                    → symlink vers ../system/cache
│   ├── tmp/                      → symlink vers ../system/tmp
│   ├── images/                   → symlink vers ../resources/public/images
│   ├── files/                    → symlink vers ../resources/public/files
│   ├── media/cache/              → symlink vers ../system/cache_media
│   ├── administrator/cache/      → symlink vers ../system/cache_administrator
│   ├── administrator/logs/       → symlink vers ../system/cache_administrator
│   └── configuration.php         → symlink vers ../system/.config/configuration.php
├── system/                       ← hors repo Git
└── resources/                    ← hors repo Git
```

## Workflow actuel

```text
1. Développement local dans public_html
2. Commit / push GitHub depuis public_html
3. Déploiement OVH manuel (FTP/SSH selon ton process)
```

## OVH (manuel)

- `configuration.php` doit être géré manuellement côté OVH avec les identifiants de la BDD OVH.
- Les dossiers runtime doivent exister côté OVH (pas de symlinks sur mutualisé) :
  - `cache/`
  - `tmp/`
  - `logs/`
  - `images/`
  - `files/`
  - `media/cache/`
  - `administrator/logs/`

## Commandes Git utiles

```bash
cd ~/Sites/WebProjects/chrono.click/public_html

git status
git add .
git commit -m "message"
git push
```

## À ne pas versionner

- `configuration.php` (données sensibles)
- `cache`, `tmp`, `images`, `files`, `media/cache`
- `administrator/cache`, `administrator/logs`
- fichiers `*.log`

## TEST 1