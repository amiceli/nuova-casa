<template>
    <Head :title="t('dashboard.title')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <nord-banner
            v-if="isAccountEmpty"
            variant="info"
        >
            {{ t('dashboard.empty') }}
            <!-- AddTagButton renders the modal next to the button, so the slot
                 has to sit on a real element for nord-banner to place it -->
            <div slot="actions">
                <AddTagButton :light="true" />
            </div>
        </nord-banner>

        <nord-banner
            v-if="hasNoFavorite"
            variant="info"
        >
            {{ t('dashboard.noFavorite') }}
        </nord-banner>
        <div class="n-grid-3 n-gap-m">
            <PageCard
                v-for="page in props.pages"
                :page="page"
                :key="page.id"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import AppLayout from "@/layouts/AppLayout.vue"
import { Page } from "@/modules/domain/Types"
import PageCard from "@/modules/pages/components/PageCard.vue"
import AddTagButton from "@/modules/tags/components/AddTagButton.vue"
import { type BreadcrumbItem } from "@/types"

const props = defineProps<{
    pages: Page[]
    hasTags: boolean
}>()
const { t } = useI18n()

const isAccountEmpty = computed<boolean>(() => !props.hasTags)

/** they have something to browse, they just never starred anything */
const hasNoFavorite = computed<boolean>(() => props.hasTags && props.pages.length === 0)
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("dashboard.title"),
        href: "/dashboard",
    },
]
</script>
