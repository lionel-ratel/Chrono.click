# chrono.click — Documentation du projet

## Stack technique
- **CMS** : Joomla 6
- **Extensions** : SEBLOD 6, YOOtheme Pro
- **Hébergement** : OVH Mutualisé
- **Déploiement** : GitHub Actions → FTP

---

## Structure du projet

```
~/Sites/WebProjects/chrono.click/
│
├── public_html/                  ← ✅ VERSIONNÉ dans Git (racine Joomla)
│   ├── administrator/
│   ├── components/
│   ├── libraries/
│   ├── modules/
│   ├── plugins/
│   ├── templates/
│   ├── cache/                    → 🔗 symlink → system/cache/
│   ├── tmp/                      → 🔗 symlink → system/tmp/
│   ├── images/                   → 🔗 symlink → resources/public/images/
│   ├── files/                    → 🔗 symlink → resources/public/files/
│   ├── configuration.php         → 🔗 symlink → system/.config/configuration.php
│   └── administrator/logs/       → 🔗 symlink → system/cache_administrator/
│
├── system/                       ← ❌ NON versionné (.gitignore)
│   ├── .config/
│   │   └── configuration.php     ← Identifiants BDD LOCAL (ne jamais committer)
│   ├── cache/
│   ├── cache_administrator/
│   ├── cache_media/
│   └── tmp/
│
├── resources/                    ← ❌ NON versionné (.gitignore)
│   └── public/
│       ├── images/               ← Uploads utilisateurs
│       └── files/                ← Fichiers uploadés
│
├── .github/
│   └── workflows/
│       └── deploy.yml            ← GitHub Actions : déploiement auto FTP
│
├── .gitignore
└── README.md
```

---

## Workflow de développement

```
1. Coder en local  →  2. Commit (GitHub Desktop)  →  3. Push sur main  →  4. Deploy automatique OVH
```

### Branches recommandées
| Branche | Usage |
|---|---|
| `main` | Production → déclenche le déploiement OVH |
| `develop` | Développement courant |
| `feature/nom` | Nouvelle fonctionnalité isolée |

---

## Configuration initiale OVH (à faire une seule fois)

### 1. Configurer les Secrets GitHub
Dans ton repo GitHub → **Settings → Secrets and variables → Actions**, ajouter :

| Secret | Valeur |
|---|---|
| `FTP_SERVER` | ex: `ftp.cluster0XX.hosting.ovh.net` |
| `FTP_USERNAME` | Ton identifiant FTP OVH |
| `FTP_PASSWORD` | Ton mot de passe FTP OVH |

### 2. Uploader configuration.php sur OVH (une seule fois, manuellement)
OVH nécessite son propre `configuration.php` avec les identifiants de SA base de données.
Ce fichier ne passe **jamais** par Git — à uploader via FTP manuellement :
```
Source locale : system/.config/configuration.php
Destination OVH : public_html/configuration.php
```
Penser à modifier dans ce fichier OVH :
- `$host` → serveur MySQL OVH
- `$db` → nom de la base OVH
- `$user` → utilisateur BDD OVH
- `$password` → mot de passe BDD OVH
- `$live_site` → https://chrono.click

### 3. Créer les dossiers runtime sur OVH (une seule fois, via FTP)
OVH ne supporte pas les symlinks. Ces dossiers doivent exister physiquement :
```
public_html/cache/           ← créer vide (chmod 755)
public_html/tmp/             ← créer vide (chmod 755)
public_html/logs/            ← créer vide (chmod 755)
public_html/images/          ← uploader depuis resources/public/images/
public_html/files/           ← uploader depuis resources/public/files/
public_html/media/cache/     ← créer vide (chmod 755)
public_html/administrator/logs/ ← créer vide (chmod 755)
```

---

## Commandes Git utiles

```bash
# Voir l'état du repo
git status

# Créer une branche feature
git checkout -b feature/ma-fonctionnalite

# Fusionner vers main (déclenche le deploy OVH)
git checkout main
git merge feature/ma-fonctionnalite
git push origin main
```

---

## ⚠️ Ne jamais committer

- `system/` → données runtime et configuration locale
- `resources/` → médias uploadés
- `configuration.php` → identifiants base de données
- Fichiers `*.log`
# test
