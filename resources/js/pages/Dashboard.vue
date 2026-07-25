<template>
    <Head :title="t('dashboard.title')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <nord-banner
            v-if="props.pages.length === 0"
            variant="info"
        >
            {{ t('dashboard.empty') }}
            <AddTagButton
                slot="actions"
                :light="true"
            >
                {{ t('common.createNew') }}
            </AddTagButton>
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
import { useI18n } from "vue-i18n"
import AppLayout from "@/layouts/AppLayout.vue"
import { Page } from "@/modules/domain/Types"
import PageCard from "@/modules/pages/components/PageCard.vue"
import AddTagButton from "@/modules/tags/components/AddTagButton.vue"
import { type BreadcrumbItem } from "@/types"

const props = defineProps<{
    pages: Page[]
}>()
const { t } = useI18n()
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("dashboard.title"),
        href: "/dashboard",
    },
]
</script>
