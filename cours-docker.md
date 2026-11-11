# Cours Docker — Notes

> Mes notes de cours Docker, complétées au fil des leçons.
> Environnement : Linux Mint 22.3 (base Ubuntu 24.04 « noble »).

## Progression

- [x] Leçon 1 — Concepts et installation
- [x] Leçon 2 — Commandes essentielles
- [x] Leçon 3 — Les ports
- [x] Leçon 4 — Les volumes
- [x] Leçon 5 — Le Dockerfile
- [x] Leçon 6 — Docker Compose

---

## Leçon 1 — Concepts et installation

### Pourquoi Docker ?

Pour en finir avec « ça marche sur ma machine, mais pas sur le serveur » : on emballe l'application **avec tout son environnement** dans une boîte standard.

### Les 4 mots clés

| Mot | Analogie | Définition |
|---|---|---|
| **Image** | La recette, le moule | Modèle figé en lecture seule (ex. : `php:8.3`, `mysql:8`) |
| **Conteneur** | Le gâteau sorti du moule | Une image **en cours d'exécution**. Plusieurs conteneurs possibles par image. |
| **Dockerfile** | La recette écrite | Fichier texte décrivant comment construire **sa propre** image |
| **Docker Hub** | Le supermarché | hub.docker.com, le registre des images officielles |

```
Dockerfile  --(docker build)-->  Image  --(docker run)-->  Conteneur
                                   ^
                    Docker Hub --(docker pull)
```

Un conteneur **n'est pas une machine virtuelle** : il partage le noyau Linux de l'hôte. Il démarre donc en une seconde et consomme peu.

### Installation sur Linux Mint

**Piège :** utiliser le nom de code **Ubuntu** (`UBUNTU_CODENAME` = `noble`), pas celui de Mint (`zena`).

```bash
sudo apt update
sudo apt install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc

echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $UBUNTU_CODENAME) stable" | sudo tee /etc/apt/sources.list.d/docker.list

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Utiliser docker sans sudo (puis se déconnecter et se reconnecter)
sudo usermod -aG docker $USER
```

### Premier test

```bash
docker run hello-world
```

Ce qui se passe : Docker cherche l'image en local, la télécharge (`pull`) faute de la trouver, crée le conteneur, l'exécute, puis le conteneur s'arrête.

---

## Leçon 2 — Commandes essentielles

### Voir

```bash
docker images        # images téléchargées
docker ps            # conteneurs EN COURS d'exécution
docker ps -a         # TOUS les conteneurs, y compris arrêtés
```

Un conteneur arrêté (`Exited`) **n'est pas supprimé**, il attend.

### Options de `docker run`

| Option | Rôle |
|---|---|
| `--name xxx` | nommer le conteneur |
| `-d` | *detached* : en arrière-plan |
| `-it` | interactif avec un terminal |
| `--rm` | suppression automatique à l'arrêt |

### Exemples

```bash
docker run -it --rm --name test alpine sh   # entrer dans un Linux Alpine (exit pour sortir)
docker run -d --name monweb nginx           # nginx en arrière-plan

docker logs monweb          # voir les journaux
docker logs -f monweb       # suivre en direct (Ctrl+C)
docker exec -it monweb bash # ouvrir un terminal dans un conteneur qui tourne

docker stop monweb          # arrêter
docker start monweb         # redémarrer
```

**`run` ou `exec` ?**
- `run` **crée un nouveau** conteneur à partir d'une image.
- `exec` **entre dans** un conteneur existant qui tourne.

### Ménage

```bash
docker stop monweb && docker rm monweb   # supprimer un conteneur
docker rm -f monweb                      # arrêter + supprimer en une fois
docker rmi hello-world                   # supprimer une image
```

`rmi` échoue si un conteneur (même arrêté) utilise l'image : supprimer d'abord le conteneur.

---

## Leçon 3 — Les ports

Le conteneur a son **propre réseau isolé**. Un service qui écoute sur le port 80 dans le conteneur n'est pas accessible depuis le PC tant que le port n'est pas **publié**.

```
Navigateur → localhost:8080 → [PC hôte] → -p → port 80 [conteneur nginx]
```

### Syntaxe : `-p HÔTE:CONTENEUR` (dehors : dedans)

```bash
docker run -d --name monweb -p 8080:80 nginx   # → http://localhost:8080
docker port monweb                              # voir les ports publiés
```

- Une même image peut tourner plusieurs fois, sur des **ports hôte différents** (8080, 8081, 8082…).
- Un port hôte ne peut servir qu'à **un seul** conteneur à la fois, sinon on obtient l'erreur `port is already allocated`.

### ⚠️ Constat important

Les modifications faites **à l'intérieur** d'un conteneur sont **perdues** quand on le supprime et qu'on le recrée.

---

## Leçon 4 — Les volumes

**Règle d'or :** le conteneur est **jetable**, les données vivent **à l'extérieur**, dans un volume.

### Deux types de stockage

| Type | Syntaxe | Usage |
|---|---|---|
| **Bind mount** | `-v /chemin/absolu/pc:/chemin/conteneur` | **le code** en développement : les modifications sont visibles immédiatement |
| **Volume nommé** | `-v nom_volume:/chemin/conteneur` | **les données** (bases de données), gérées par Docker |

Même logique que pour les ports : **dehors : dedans**.

### Bind mount : le site sur le PC

```bash
mkdir -p ~/docker-cours/site && cd ~/docker-cours
echo "<h1>Mon site</h1>" > site/index.html

docker run -d --name monweb -p 8080:80 -v $(pwd)/site:/usr/share/nginx/html:ro nginx
```

- `$(pwd)` : un bind mount exige un **chemin absolu**.
- `:ro` : lecture seule pour le conteneur.

### Volume nommé : une base MySQL persistante

`-e VAR=valeur` passe une **variable d'environnement** au conteneur.

```bash
docker volume create mesdonnees

docker run -d --name madb \
  -e MYSQL_ROOT_PASSWORD=secret \
  -v mesdonnees:/var/lib/mysql \
  mysql:8

docker exec -it madb mysql -uroot -psecret   # console MySQL
```

Test : `docker rm -f madb`, puis on recrée le conteneur avec le **même volume** : les données sont toujours là ✅

### Gérer les volumes

```bash
docker volume ls
docker volume inspect mesdonnees   # détails + emplacement sur le disque
docker volume rm mesdonnees        # ⚠️ SUPPRESSION DÉFINITIVE DES DONNÉES
```

- `docker rm` sur un conteneur **ne supprime pas** ses volumes nommés.
- `docker volume rm` et `docker volume prune` **effacent les données pour de bon** : toujours se relire avant de les lancer !

---

## Leçon 5 — Le Dockerfile

Le Dockerfile est **la recette** d'une image sur mesure : on part d'une image existante et on ajoute ce dont on a besoin.

### Instructions de base

| Instruction | Rôle | Exemple |
|---|---|---|
| `FROM` | image de départ (toujours en premier) | `FROM php:8.3-apache` |
| `RUN` | commande exécutée **à la construction** | `RUN docker-php-ext-install pdo_mysql` |
| `COPY` | copie des fichiers du PC **dans** l'image | `COPY src/ .` |
| `WORKDIR` | dossier courant pour la suite | `WORKDIR /var/www/html` |
| `ENV` | variable d'environnement | `ENV APP_ENV=prod` |
| `EXPOSE` | **documente** le port (n'ouvre rien : c'est `-p` qui publie) | `EXPOSE 80` |
| `CMD` | commande lancée **à chaque démarrage** du conteneur | `CMD ["apache2-foreground"]` |

> `RUN` s'exécute **une fois, à la construction** ; `CMD` s'exécute **à chaque démarrage**.

### Exemple : application PHP

Structure :

```
monapp/
├── Dockerfile
├── .dockerignore
└── src/
    └── index.php
```

`Dockerfile` :

```dockerfile
FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql
WORKDIR /var/www/html
COPY src/ .
EXPOSE 80
```

Construire et lancer :

```bash
docker build -t monapp:1.0 .      # -t nom:version  —  "." = contexte de construction
docker run -d --name monapp -p 8090:80 monapp:1.0
```

### Le cache des couches

Chaque instruction crée une **couche** mise en cache. **L'ordre compte** : on va du plus stable au plus changeant.

```
FROM          ← change rarement
RUN install   ← change rarement     } réutilisés depuis le cache
COPY code     ← change souvent      ← EN DERNIER
```

```bash
docker history monapp:1.1   # voir les couches
```

Une image est **figée** : après une modification du code, il faut **reconstruire** l'image, puis **recréer** le conteneur.

### `.dockerignore`

Exclut des fichiers du contexte de construction : la construction est plus rapide, et les **secrets** ne finissent pas dans l'image.

```
.git
.env
node_modules
vendor
*.log
```

### Bind mount ou COPY ?

| | Bind mount (`-v`) | `COPY` |
|---|---|---|
| Quand | développement | livraison / production |
| Code modifié | visible immédiatement | reconstruction nécessaire |
| Autonome | non | oui, l'image contient tout |

---

## Leçon 6 — Docker Compose

Compose décrit **toute une application** (plusieurs conteneurs, ports, volumes, variables) dans **un seul fichier** : `compose.yaml`. Fini les longues commandes `docker run` à retenir, et le fichier se partage avec l'équipe via Git.

### Le réseau automatique

Compose crée un **réseau commun** à tous les services. Chaque service est joignable **par son nom** : depuis `app`, la base est accessible à l'adresse `db`, et non `localhost` !

```
[navigateur] → localhost:8090 → [app : PHP/Apache] → db:3306 → [db : MySQL]
                                 └─── réseau créé automatiquement par Compose ───┘
```

### Exemple : PHP + MySQL

```
monapp/
├── compose.yaml
├── Dockerfile
├── .dockerignore
└── src/
    └── index.php
```

`compose.yaml` :

```yaml
services:
  app:
    build: .                      # construit l'image depuis le Dockerfile
    ports:
      - "8090:80"                 # HÔTE:CONTENEUR
    volumes:
      - ./src:/var/www/html       # bind mount : code modifiable en direct
    environment:
      DB_HOST: db                 # nom du service = nom d'hôte
      DB_NAME: boulot
      DB_USER: appuser
      DB_PASSWORD: apppass
    depends_on:
      db:
        condition: service_healthy   # attend que MySQL soit VRAIMENT prêt

  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: boulot      # base créée au 1er démarrage
      MYSQL_USER: appuser
      MYSQL_PASSWORD: apppass
    volumes:
      - dbdata:/var/lib/mysql     # volume nommé : données persistantes
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "127.0.0.1", "-uroot", "-psecret"]
      interval: 5s
      retries: 10

volumes:
  dbdata:                         # déclaration du volume nommé
```

Correspondance avec `docker run` :

| `docker run` | `compose.yaml` |
|---|---|
| `--name` | le nom du service (`app`, `db`) |
| `-p 8090:80` | `ports:` |
| `-v ...` | `volumes:` |
| `-e VAR=val` | `environment:` |
| `docker build` | `build: .` |
| image | `image:` |

### `depends_on` et `healthcheck`

- `depends_on` seul : `db` **démarre** avant `app`… mais MySQL met plusieurs secondes à être prêt.
- Avec `condition: service_healthy` : `app` attend que le **healthcheck** de `db` réussisse.

### Commandes Compose (à lancer dans le dossier du `compose.yaml`)

```bash
docker compose up -d            # créer + démarrer tout en arrière-plan
docker compose up -d --build    # idem en reconstruisant les images (Dockerfile modifié)
docker compose ps               # état des services
docker compose logs -f app      # logs d'un service
docker compose exec db mysql -uroot -psecret   # entrer dans un service
docker compose stop / start     # arrêter / redémarrer sans rien supprimer
docker compose down             # supprimer conteneurs + réseau (les volumes sont GARDÉS)
docker compose down -v          # ⚠️ supprime AUSSI les volumes → données perdues
```

### Secrets : le fichier `.env`

Ne pas laisser les mots de passe en dur : Compose lit automatiquement un fichier `.env` placé à côté de `compose.yaml`.

```
# .env  (à ajouter dans .gitignore et .dockerignore !)
DB_PASSWORD=apppass
```

```yaml
    environment:
      DB_PASSWORD: ${DB_PASSWORD}
```

---

## Aide-mémoire global

```bash
# Voir
docker images | docker ps | docker ps -a | docker volume ls

# Créer / lancer
docker run [-d] [-it] [--rm] [--name N] [-p H:C] [-v SRC:DEST[:ro]] [-e VAR=val] image[:tag]
docker build -t nom:version .

# Piloter
docker stop | start | logs [-f] | exec -it N sh

# Inspecter
docker port N | docker history image | docker volume inspect V

# Supprimer
docker rm [-f] N | docker rmi image | docker volume rm V (⚠️ données perdues)

# Compose (dans le dossier du compose.yaml)
docker compose up -d [--build] | ps | logs -f S | exec S cmd
docker compose stop | start | down | down -v (⚠️ volumes supprimés)
```
