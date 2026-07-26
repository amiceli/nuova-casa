<template>

    <Head :title="t('newsletters.title')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #meta>
            <nord-badge v-if="props.news">
                {{ t('newsletters.count', { count: props.news.length }) }}
            </nord-badge>
        </template>
        <template #actions>
            <AddNewsletter />
        </template>

        <WhenVisible data="news">
            <template #fallback>
                <div class="n-align-center">
                    <nord-spinner size="l"></nord-spinner>
                </div>
            </template>

            <div class="n-grid-3 n-gap-m">
                <RssCard
                    v-for="item in props.news"
                    :rss="item"
                    :key="item.id"
                />
            </div>
        </WhenVisible>
    </AppLayout>
</template>

<script
    setup
    lang="ts"
>
import { Head, WhenVisible } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import AppLayout from "@/layouts/AppLayout.vue"
import { Newsletter } from "@/modules/domain/Types"
import AddNewsletter from "@/modules/newsletters/components/AddNewsletter.vue"
import RssCard from "@/modules/newsletters/components/RssCard.vue"
import { type BreadcrumbItem } from "@/types"

const props = defineProps<{
    news?: Newsletter[]
}>()

const { t } = useI18n()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Newsletters",
        href: "/newsletters",
    },
]
</script>
