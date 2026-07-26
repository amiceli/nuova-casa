<template>
    <Head :title="t('tags.title')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #meta>
            <nord-badge>
                {{ t('tags.tagCount', { count: props.tags.length }) }}
            </nord-badge>
        </template>
        <template #actions>
            <AddTagButton />
        </template>

        <div class="n-grid-3 n-gap-m">
            <TagCard
                v-for="tag in props.tags"
                :tag="tag"
                :key="tag.name"
            />
        </div>
    </AppLayout>
</template>

<script
    setup
    lang="ts"
>
import { Head } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import AppLayout from "@/layouts/AppLayout.vue"
import { Tag } from "@/modules/domain/Types"
import AddTagButton from "@/modules/tags/components/AddTagButton.vue"
import TagCard from "@/modules/tags/components/TagCard.vue"
import { type BreadcrumbItem } from "@/types"

const props = defineProps<{
    tags: Tag[]
}>()

const { t } = useI18n()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("tags.title"),
        href: "/tags",
    },
]
</script>
