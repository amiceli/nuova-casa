<template>
    <Head :title="t('meta.title')" />

    <nord-layout hide-default-nav-toggle>
        <nord-top-bar slot="top-bar">
            <nord-stack
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <img
                    class="brand-logo"
                    :src="logo"
                    alt="Nuova casa"
                />
                <strong class="brand-name n-typescale-l">{{ t("brand") }}</strong>
            </nord-stack>
            <nord-stack
                slot="end"
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <nord-button
                    :href="repositoryUrl"
                    target="_blank"
                    rel="noopener"
                >
                    {{ t("nav.github") }}
                </nord-button>
                <nord-button
                    variant="primary"
                    @click="goToAuth()"
                >
                    {{ t("nav.login") }}
                </nord-button>
            </nord-stack>
        </nord-top-bar>

        <nord-stack
            gap="l"
            class="landing"
        >
            <nord-card padding="l">
                <nord-stack
                    gap="m"
                    align-items="center"
                    class="n-align-center"
                >
                    <img
                        class="hero-logo"
                        :src="logo"
                        alt="Nuova casa"
                    />
                    <h1 class="n-typescale-xl">{{ t("hero.title") }}</h1>
                    <p class="n-typescale-m n-color-text-weaker">
                        <strong>{{ t("brand") }}</strong>
                        {{ t("hero.subtitle") }}
                    </p>
                    <nord-stack
                        direction="horizontal"
                        gap="m"
                        justify-content="center"
                    >
                        <nord-button
                            variant="primary"
                            size="l"
                            @click="goToAuth()"
                        >
                            {{ t("hero.cta") }}
                        </nord-button>
                    </nord-stack>
                    <span class="n-typescale-s n-color-text-weaker">{{ t("hero.note") }}</span>
                </nord-stack>
            </nord-card>

            <nord-card padding="l">
                <div class="n-grid-2 n-gap-l">
                    <nord-stack gap="m">
                        <nord-badge variant="highlight">{{ t("features.tags.eyebrow") }}</nord-badge>
                        <h2 class="n-typescale-l">{{ t("features.tags.title") }}</h2>
                        <p class="n-color-text-weaker">{{ t("features.tags.description") }}</p>
                        <nord-stack gap="s">
                            <span
                                v-for="point in tagsPoints"
                                :key="point"
                                class="n-typescale-m n-color-text-weak"
                            >
                                {{ t(point) }}
                            </span>
                        </nord-stack>
                    </nord-stack>
                    <nord-stack gap="s">
                        <nord-card
                            v-for="tag in tagsPreview"
                            :key="tag.name"
                            padding="m"
                        >
                            <nord-stack
                                direction="horizontal"
                                gap="m"
                                align-items="center"
                            >
                                <span class="n-typescale-l">{{ tag.icon }}</span>
                                <span class="n-typescale-m">{{ tag.name }}</span>
                                <nord-badge slot="end">{{ t("features.tags.count", { count: tag.count }) }}</nord-badge>
                            </nord-stack>
                        </nord-card>
                    </nord-stack>
                </div>
            </nord-card>

            <nord-card padding="l">
                <div class="n-grid-2 n-gap-l">
                    <nord-stack gap="s">
                        <nord-card
                            v-for="link in linksPreview"
                            :key="link.title"
                            padding="m"
                        >
                            <nord-stack
                                direction="horizontal"
                                gap="m"
                                align-items="center"
                            >
                                <nord-avatar :name="link.title"></nord-avatar>
                                <nord-stack gap="none">
                                    <span class="n-typescale-m n-truncate">{{ link.title }}</span>
                                    <span class="n-typescale-s n-color-text-weaker n-truncate">{{ link.url }}</span>
                                </nord-stack>
                                <nord-badge
                                    v-if="link.favorite"
                                    slot="end"
                                    variant="warning"
                                >
                                    {{ t("features.links.favorite") }}
                                </nord-badge>
                            </nord-stack>
                        </nord-card>
                    </nord-stack>
                    <nord-stack gap="m">
                        <nord-badge variant="highlight">{{ t("features.links.eyebrow") }}</nord-badge>
                        <h2 class="n-typescale-l">{{ t("features.links.title") }}</h2>
                        <p class="n-color-text-weaker">{{ t("features.links.description") }}</p>
                        <nord-stack gap="s">
                            <span
                                v-for="point in linksPoints"
                                :key="point"
                                class="n-typescale-m n-color-text-weak"
                            >
                                {{ t(point) }}
                            </span>
                        </nord-stack>
                    </nord-stack>
                </div>
            </nord-card>

            <nord-card padding="l">
                <div class="n-grid-2 n-gap-l">
                    <nord-stack gap="m">
                        <nord-badge variant="highlight">{{ t("features.newsletters.eyebrow") }}</nord-badge>
                        <h2 class="n-typescale-l">{{ t("features.newsletters.title") }}</h2>
                        <p class="n-color-text-weaker">
                            <strong>{{ t("brand") }}</strong>
                            {{ t("features.newsletters.description") }}
                        </p>
                        <nord-stack gap="s">
                            <span
                                v-for="point in newslettersPoints"
                                :key="point"
                                class="n-typescale-m n-color-text-weak"
                            >
                                {{ t(point) }}
                            </span>
                        </nord-stack>
                    </nord-stack>
                    <nord-stack gap="s">
                        <nord-card
                            v-for="newsletter in newslettersPreview"
                            :key="newsletter.name"
                            padding="m"
                        >
                            <nord-stack
                                direction="horizontal"
                                gap="m"
                                align-items="center"
                            >
                                <nord-stack gap="none">
                                    <span class="n-typescale-m n-truncate">{{ newsletter.name }}</span>
                                    <span class="n-typescale-s n-color-text-weaker n-truncate">
                                        {{ newsletter.article }}
                                    </span>
                                </nord-stack>
                                <nord-badge
                                    slot="end"
                                    :variant="newsletter.read ? 'success' : 'highlight'"
                                >
                                    {{ newsletter.read ? t("features.newsletters.read") : t("features.newsletters.unread") }}
                                </nord-badge>
                            </nord-stack>
                        </nord-card>
                    </nord-stack>
                </div>
            </nord-card>

            <nord-card padding="l">
                <nord-stack
                    gap="m"
                    align-items="center"
                    class="n-align-center"
                >
                    <nord-badge variant="warning">{{ t("soon.badge") }}</nord-badge>
                    <h2 class="n-typescale-l">{{ t("soon.title") }}</h2>
                    <p class="n-color-text-weaker">{{ t("soon.description") }}</p>
                    <nord-stack
                        direction="horizontal"
                        gap="s"
                        justify-content="center"
                    >
                        <nord-badge
                            v-for="browser in browsers"
                            :key="browser"
                        >
                            {{ browser }}
                        </nord-badge>
                    </nord-stack>
                </nord-stack>
            </nord-card>

            <nord-card padding="l">
                <nord-stack
                    gap="m"
                    align-items="center"
                    class="n-align-center"
                >
                    <h2 class="n-typescale-l">{{ t("openSource.title") }}</h2>
                    <p class="n-color-text-weaker">
                        <strong>{{ t("brand") }}</strong>
                        {{ t("openSource.description") }}
                    </p>
                    <nord-stack
                        direction="horizontal"
                        gap="m"
                        justify-content="center"
                    >
                        <nord-button
                            variant="primary"
                            size="l"
                            :href="repositoryUrl"
                            target="_blank"
                            rel="noopener"
                        >
                            {{ t("openSource.cta") }}
                        </nord-button>
                        <nord-button
                            size="l"
                            @click="goToAuth()"
                        >
                            {{ t("nav.login") }}
                        </nord-button>
                    </nord-stack>
                </nord-stack>
            </nord-card>

            <nord-stack
                direction="horizontal"
                gap="s"
                align-items="center"
                justify-content="center"
            >
                <img
                    class="brand-logo"
                    :src="logo"
                    alt="Nuova casa"
                />
                <span class="n-typescale-s n-color-text-weaker">
                    <strong>{{ t("brand") }}</strong>
                    {{ t("footer.text") }}
                </span>
            </nord-stack>
        </nord-stack>
    </nord-layout>
</template>

<script setup lang="ts">
import logo from "@assets/casa-logo.webp"
import { Head } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"

const repositoryUrl = "https://github.com/amiceli/nuova-casa"

const browsers = ["Chrome", "Firefox", "Safari", "Edge"]

const tagsPoints = ["features.tags.point1", "features.tags.point2", "features.tags.point3"]
const linksPoints = ["features.links.point1", "features.links.point2", "features.links.point3"]
const newslettersPoints = ["features.newsletters.point1", "features.newsletters.point2", "features.newsletters.point3"]

const tagsPreview = [
    { icon: "💻", name: "Dev", count: 24 },
    { icon: "🎨", name: "Design", count: 12 },
    { icon: "🍕", name: "Recettes", count: 8 },
]

const linksPreview = [
    { title: "Vue.js documentation", url: "vuejs.org", favorite: true },
    { title: "Nord Design System", url: "nordhealth.design", favorite: false },
    { title: "Laravel News", url: "laravel-news.com", favorite: false },
]

const newslettersPreview = [
    { name: "JSter", article: "Weekly JavaScript news", read: false },
    { name: "PHP Annotated", article: "Monthly PHP digest", read: true },
    { name: "CSS Weekly", article: "Issue #612", read: true },
]

const { t } = useI18n({
    useScope: "local",
    messages: {
        fr: {
            brand: "Nuova casa",
            meta: {
                title: "Vos liens, vos tags, vos newsletters",
            },
            nav: {
                github: "GitHub",
                login: "Se connecter",
            },
            hero: {
                title: "Tous vos liens, enfin bien rangés",
                subtitle:
                    "rassemble vos favoris, vos tags et vos newsletters au même endroit, accessibles depuis n'importe quel navigateur.",
                cta: "Se connecter avec GitHub",
                secondary: "Voir le code",
                note: "Gratuit, sans pub et open source.",
            },
            features: {
                tags: {
                    eyebrow: "Tags",
                    title: "Classez vos liens comme vous le pensez",
                    description:
                        "Créez autant de tags que nécessaire, donnez-leur une icône et retrouvez d'un coup d'œil tout ce que vous avez mis de côté.",
                    point1: "Un tag, une icône, un nom unique",
                    point2: "Le nombre de liens visible sur chaque tag",
                    point3: "Suppression d'un tag et de son contenu en un clic",
                    count: "{count} lien(s)",
                },
                links: {
                    eyebrow: "Liens & favoris",
                    title: "Enregistrez une page en quelques secondes",
                    description:
                        "Collez une url : le titre et l'image sont récupérés automatiquement. Marquez vos pages préférées en favori pour les garder en tête de liste.",
                    point1: "Titre et aperçu récupérés via OpenGraph",
                    point2: "Favoris pour vos pages les plus utiles",
                    point3: "Recherche par nom ou par url",
                    favorite: "favori",
                },
                newsletters: {
                    eyebrow: "Newsletters",
                    title: "Suivez vos newsletters sans boîte mail",
                    description:
                        "récupère les derniers articles de vos newsletters via leur flux RSS. Chaque article garde son statut lu ou non lu.",
                    point1: "Flux RSS détecté automatiquement",
                    point2: "Derniers articles rassemblés au même endroit",
                    point3: "Statut lu / non lu par article",
                    read: "lu",
                    unread: "non lue",
                },
            },
            soon: {
                badge: "Bientôt",
                title: "Importez les favoris de votre navigateur",
                description:
                    "Vous avez des centaines de favoris accumulés depuis des années ? L'import de vos marque-pages arrive, avec le classement par tags à la clé.",
            },
            openSource: {
                title: "100 % open source",
                description:
                    "est développé au grand jour : le code est public, les contributions sont les bienvenues et vos données restent les vôtres.",
                cta: "Voir le projet sur GitHub",
            },
            footer: {
                text: "— projet open source, construit avec Laravel, Vue et Nord.",
            },
        },
        en: {
            brand: "Nuova casa",
            meta: {
                title: "Your links, your tags, your newsletters",
            },
            nav: {
                github: "GitHub",
                login: "Log in",
            },
            hero: {
                title: "All your links, finally tidy",
                subtitle: "keeps your bookmarks, your tags and your newsletters in one place, available from any browser.",
                cta: "Log in with GitHub",
                secondary: "View the code",
                note: "Free, ad-free and open source.",
            },
            features: {
                tags: {
                    eyebrow: "Tags",
                    title: "Sort your links the way you think",
                    description: "Create as many tags as you need, give them an icon and see at a glance everything you saved for later.",
                    point1: "One tag, one icon, one unique name",
                    point2: "Link count shown on every tag",
                    point3: "Delete a tag and its content in one click",
                    count: "{count} link(s)",
                },
                links: {
                    eyebrow: "Links & favorites",
                    title: "Save a page in a few seconds",
                    description:
                        "Paste a url: the title and the picture are fetched automatically. Star your best pages to keep them at the top of the list.",
                    point1: "Title and preview fetched with OpenGraph",
                    point2: "Favorites for your most useful pages",
                    point3: "Search by name or by url",
                    favorite: "favorite",
                },
                newsletters: {
                    eyebrow: "Newsletters",
                    title: "Follow your newsletters without an inbox",
                    description:
                        "fetches the latest articles of your newsletters from their RSS feed. Every article keeps its read or unread status.",
                    point1: "RSS feed detected automatically",
                    point2: "Latest articles gathered in one place",
                    point3: "Read / unread status per article",
                    read: "read",
                    unread: "unread",
                },
            },
            soon: {
                badge: "Coming soon",
                title: "Import your browser bookmarks",
                description: "Hundreds of bookmarks piled up over the years? Importing them is on its way, tag sorting included.",
            },
            openSource: {
                title: "100% open source",
                description: "is built in the open: the code is public, contributions are welcome and your data stays yours.",
                cta: "View the project on GitHub",
            },
            footer: {
                text: "— open source project, built with Laravel, Vue and Nord.",
            },
        },
    },
})

function goToAuth() {
    window.location.assign(route("auth-redirect"))
}
</script>

<style scoped>
.brand-logo {
    height: 32px;
    width: 32px;
    border-radius: var(--n-border-radius);
    object-fit: cover;
}

.brand-name {
    color: #fff;
}

.hero-logo {
    height: 96px;
    width: 96px;
    border-radius: var(--n-border-radius);
    object-fit: cover;
}

.landing {
    max-width: 1000px;
    margin: 0 auto;
}
</style>
