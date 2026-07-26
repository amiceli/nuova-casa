<template>
    <Head :title="t('search.title')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #meta>
            <nord-badge v-if="props.pages.length > 0">
                {{ t('search.foundLinks', { count: props.pages.length }) }}
            </nord-badge>
        </template>

        <nord-empty-state v-if="props.pages.length === 0">
            <h2>{{ t('search.emptyTitle') }}</h2>
            <p>{{ t('search.emptyText', { value: props.search }) }}</p>
            <Link href="/dashboard">
                <nord-button variant="primary">
                    {{ t('nav.dashboard') }}
                </nord-button>
            </Link>
        </nord-empty-state>

        <div
            class="n-grid-3 n-gap-m"
            v-else
        >
            <PageCard
                v-for="item in props.pages"
                :page="item"
                :key="item.id"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import AppLayout from "@/layouts/AppLayout.vue"
import { Page } from "@/modules/domain/Types"
import PageCard from "@/modules/pages/components/PageCard.vue"
import { type BreadcrumbItem } from "@/types"

const props = defineProps<{
    pages: Page[]
    search: string
}>()

const { t } = useI18n()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("search.for", { value: props.search }),
        href: "/search",
    },
]
</script>
