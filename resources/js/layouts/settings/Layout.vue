<template>
    <nord-stack gap="l">
        <nord-stack
            direction="horizontal"
            gap="s"
        >
            <nord-button
                v-for="item in settingsNavItems"
                :key="item.href"
                :href="item.href"
                :variant="isActive(item) ? 'primary' : 'default'"
            >
                {{ item.title }}
            </nord-button>
        </nord-stack>

        <slot />
    </nord-stack>
</template>

<script setup lang="ts">
import { usePage } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { type NavItem } from "@/types"

const page = usePage()
const { t } = useI18n()

const settingsNavItems = computed<NavItem[]>(() => [
    { title: t("nav.profile"), href: "/settings/profile" },
    { title: t("nav.appearance"), href: "/settings/appearance" },
])

function isActive(item: NavItem) {
    return page.url === item.href
}
</script>
