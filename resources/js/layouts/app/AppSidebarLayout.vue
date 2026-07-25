<template>
    <nord-layout>
        <nord-navigation slot="nav">
            <UserMenu />

            <nord-nav-item
                v-for="item in mainNavItems"
                :key="item.href"
                :href="item.href"
                :active="isActive(item)"
            >
                {{ item.title }}
            </nord-nav-item>

        </nord-navigation>

        <nord-top-bar slot="top-bar">
            <nord-input
                expand
                :label="t('nav.search')"
                hide-label
                type="search"
                :placeholder="t('nav.searchPlaceholder')"
                :value="currentSearch"
                @keydown.enter="submitSearch($event)"
            ></nord-input>
        </nord-top-bar>

        <nord-header
            v-if="currentTitle"
            slot="header"
        >
            <nord-stack
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <h1 class="n-typescale-l">
                    {{ currentTitle }}
                </h1>
                <slot name="meta" />
            </nord-stack>
            <nord-stack
                v-if="$slots.actions"
                slot="end"
                direction="horizontal"
                gap="m"
                align-items="center"
            >
                <slot name="actions" />
            </nord-stack>
        </nord-header>

        <slot />

        <nord-toast-group></nord-toast-group>
    </nord-layout>
</template>

<script setup lang="ts">
import { router, usePage } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import UserMenu from "@/components/UserMenu.vue"
import type { BreadcrumbItemType } from "@/types"

interface NavItem {
    title: string
    href: string
}

interface Props {
    breadcrumbs?: BreadcrumbItemType[]
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})

const page = usePage()
const { t } = useI18n()

const mainNavItems = computed<NavItem[]>(() => [
    { title: t("nav.dashboard"), href: "/dashboard" },
    { title: t("nav.tags"), href: "/tags" },
    { title: t("nav.newsletters"), href: "/newsletters" },
])

const currentTitle = computed<string>(() => props.breadcrumbs.at(-1)?.title ?? "")

const currentSearch = computed<string>(() => (page.props.search as string) ?? "")

function isActive(item: NavItem) {
    return page.url === item.href || page.url.startsWith(`${item.href}/`)
}

function submitSearch(event: KeyboardEvent) {
    const value = (event.target as HTMLInputElement).value.trim()

    if (!value) {
        return
    }

    router.get(route("search", { value }))
}
</script>
