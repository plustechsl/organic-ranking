# SPEC.md — Organic Ranking

> Documento de especificaciones consolidado. Los puntos marcados como **[ASUNCIÓN]**
> son decisiones razonables tomadas para resolver ambigüedades del spec original.
> Revísalas y corrígelas antes de que Claude Code empiece a generar código —
> son las más caras de cambiar después.

## 1. Visión general

- **Nombre**: Organic Ranking
- **Descripción**: Plataforma tipo clasificados donde usuarios registrados publican
  **listas de enlaces**. Cada lista pertenece a una categoría/subcategoría, y los
  enlaces dentro de ella comparten esa misma categoría/subcategoría. Los usuarios
  pueden votar enlaces y dar reputación a otros usuarios.
- **Stack**: Winter CMS (PHP / Laravel), MySQL.
- **Infraestructura actual**: Docker sobre WSL2, en máquina Windows.
- **Plugin principal**: `Plustech.OrganicLinks`

---

## 2. Modelos de base de datos

### 2.1 Categorías — `plustech_organiclinks_categories`
- `id`, `name`, `slug`, `parent_id` (jerarquía categoría → subcategoría)
- `created_at`, `updated_at`

### 2.2 Listas — `plustech_organiclinks_lists` **[ASUNCIÓN — modelo nuevo, no existía en el spec original]**
El spec original menciona "listas de links" como concepto central pero nunca
definía esta tabla. La añado para cerrar esa laguna:
- `id`, `title`, `slug`, `description`
- `category_id`, `subcategory_id` — deben coincidir con los enlaces que contiene
- `user_id` (autor/creador de la lista)
- `created_at`, `updated_at`
- **[ASUNCIÓN]** Relación Lista↔Link: **muchos-a-muchos** (`plustech_organiclinks_list_links`),
  para que un mismo enlace pueda aparecer en varias listas temáticas. Si prefieres
  que cada link pertenezca a una única lista, es una relación 1-N más simple —
  confírmame cuál quieres.
- **[ASUNCIÓN]** Cualquier usuario registrado puede crear listas (no solo admin).
- **[ASUNCIÓN]** Al añadir un link a una lista, se valida que `link.category_id` y
  `link.subcategory_id` coincidan con los de la lista; si no coinciden, se rechaza
  con un mensaje de error (no se fuerza el cambio de categoría del link).

### 2.3 Enlaces — `plustech_organiclinks_links`
- `id`, `title`, `url`, `description`, `image` (imagen destacada)
- `category_id`, `subcategory_id`
- `user_id` (autor)
- `score` (contador global de puntuación, desnormalizado para ordenación rápida)
- `created_at`, `updated_at`
- Índices: `score`, `created_at`, `category_id` (para ordenación y filtrado rápido)
- **[ASUNCIÓN]** `url` con índice único a nivel de aplicación para evitar duplicados
  exactos. Si dos usuarios quieren compartir la misma URL, se les redirige al link
  existente en lugar de crear uno nuevo.
- **[ASUNCIÓN — antes indefinido]** Los links se publican **inmediatamente**,
  sin cola de moderación previa (moderación es solo reactiva vía reportes/admin).
  Si prefieres revisión previa, es un campo `status` (pending/approved/rejected)
  con un flujo adicional en el panel de admin.

### 2.4 Tags — `plustech_organiclinks_tags` **[ASUNCIÓN — tabla nueva]**
El spec original trata los tags como texto libre embebido, pero para poder
buscar/filtrar y mostrar tags "populares" hace falta tabla propia:
- `id`, `name`, `slug`, `usage_count`
- Relación N-N con Links vía `plustech_organiclinks_link_tags`
- **Límite de tags por link: [CONFIRMAR] — el spec dice 5 en dos sitios y 10 en otro.**
  Uso **10** como valor por defecto en este documento porque es el número más reciente
  que diste, pero avísame si es 5.

### 2.5 Especialidades de usuario — `plustech_organiclinks_user_expertises`
- `user_id`, `category_id` (hasta 3 registros por usuario)
- **[ASUNCIÓN]** Las 3 especialidades se seleccionan a nivel de **subcategoría**
  (no de categoría padre), porque es el nivel más específico y evita ambigüedad
  en el cálculo del bonus de voto. El match para el bonus de 5 puntos (sección 2.6)
  se hace por subcategoría exacta.

### 2.6 Votos de enlaces — `plustech_organiclinks_votes`
- `id`, `link_id`, `user_id` (nullable), `ip_hash` (nullable), `points`, `created_at`
- Un registro por voto emitido; único por `(link_id, user_id)` o `(link_id, ip_hash)`
  para evitar duplicados.
- **Puntuación**:
  - No registrado: 1 punto (con captcha)
  - Registrado, fuera de su especialidad: 1 punto
  - Registrado, dentro de una de sus 3 subcategorías de especialidad: 5 puntos
- **[ASUNCIÓN]** `ip_hash` = hash de `(IP + User-Agent)`, con ventana de
  deduplicación de 24h en vez de bloqueo permanente por IP — así una IP compartida
  (oficina, NAT) no bloquea a todos sus usuarios de forma indefinida, solo repite
  el voto del mismo link en el mismo día.

### 2.7 Reputación de usuarios — `plustech_organiclinks_user_reputation` **[ASUNCIÓN — tabla nueva]**
No existía tabla explícita para esto en el spec original:
- `id`, `from_user_id`, `to_user_id`, `created_at`
- Solo voto positivo (+1), sin voto negativo.
- **[ASUNCIÓN]** Único por `(from_user_id, to_user_id)` — un usuario solo puede dar
  reputación **una vez** a otro usuario (no votos ilimitados). Si luego quiere,
  se podría permitir un voto cada X días; de momento lo dejo como voto único.

---

## 3. Búsqueda y ordenación

- Búsqueda por: texto libre (título de lista, descripción de link), tags,
  categoría, subcategoría.
- **[ASUNCIÓN]** Motor de búsqueda: `FULLTEXT` nativo de MySQL sobre
  `title` + `description` para la v1 (suficiente para el volumen inicial).
  Si el catálogo crece mucho, migrar a Meilisearch es la siguiente opción — lo dejo
  anotado pero no lo implemento ahora.
- Filtro directo al hacer clic en cualquier hashtag.
- Filtrado/ordenación vía AJAX, sin recarga de página.
- **Ordenación — [CONFIRMAR]**: el nombre del proyecto ("Organic Ranking") sugiere
  un ranking con decaimiento temporal (tipo Hacker News/Reddit), no solo "más
  votados" y "más recientes" como dos listas separadas. Por defecto implemento
  **ambos como opciones independientes** (como pide el spec), y dejo el ranking
  ponderado como mejora futura opcional — confírmame si lo quieres ya en v1.

---

## 4. Frontend

- Barra de búsqueda destacada en cabecera + navegador de categorías jerárquico.
- Tarjetas de enlace: imagen, título, descripción, autor, puntos, tags en línea (#tag).
- Logotipo oficial en la cabecera.
- Perfil de usuario: especialidades, links aportados + puntuación, reputación
  pública, edición de hasta [10] tags en links existentes.

---

## 5. Backend / Administración

- CRUD completo de links, listas, categorías y tags.
- Moderación: baneo/suspensión de usuarios, borrado/edición rápida de spam.
- Configuración: parámetros de Captcha y globales del sitio.
- **[ASUNCIÓN — añadido]** Panel de reportes: los usuarios pueden reportar un
  link/tag como spam o roto, y el admin ve una cola de reportes pendientes.

---

## 6. Seguridad y anti-abuso (añadido, no estaba en el spec original)

- Captcha en votos de no-registrados — **[CONFIRMAR proveedor]**: recomiendo
  Cloudflare Turnstile (gratis, sin fricción) sobre reCAPTCHA, pero uso lo que
  prefieras.
- Rate limiting en creación de links/listas por usuario (ej. máx. N por hora)
  para frenar spam masivo, además del rate limiting ya previsto en votos.
- Verificación periódica de que las URLs siguen activas (job programado) —
  **[ASUNCIÓN]** implementación diferida a fase 2, no bloqueante para el MVP.

---

## 7. Puntos pendientes de tu confirmación antes de generar código

1. Límite real de tags por link: **¿5 o 10?**
2. Relación Lista↔Link: **¿N-N (un link en varias listas) o 1-N (un link, una lista)?**
3. ¿Moderación previa de links (cola de aprobación) o publicación inmediata?
4. ¿Ranking ponderado tipo Reddit/HN en v1, o solo "más votados" / "más recientes" por separado?
5. Proveedor de Captcha: ¿Turnstile, reCAPTCHA u otro?
