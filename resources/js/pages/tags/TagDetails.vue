<template>

    <Head :title="`${props.tag.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #meta>
            <nord-badge>
                {{ t('tags.pageCount', { count: props.tag.children.length }) }}
            </nord-badge>
        </template>
        <template #actions>
            <AddPageButton :tag="props.tag" />
            <RemoveTag :tag="props.tag" />
        </template>

        <div class="n-grid-3 n-gap-m">
            <PageCard
                v-for="page in props.tag.children"
                :page="{...page, parent: {...props.tag}}"
                :key="page.title"
                edit
            />
        </div>
    </AppLayout>
</template>

<script
    lang="ts"
    setup
>
import { Head } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import AppLayout from "@/layouts/AppLayout.vue"
import { Tag } from "@/modules/domain/Types"
import AddPageButton from "@/modules/pages/components/AddPageButton.vue"
import PageCard from "@/modules/pages/components/PageCard.vue"
import RemoveTag from "@/modules/pages/components/RemoveTag.vue"
import { BreadcrumbItem } from "@/types"

const props = defineProps<{ tag: Tag }>()
const { t } = useI18n()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("tags.title"),
        href: "/tags",
    },
    {
        title: props.tag.name,
        href: "",
    },
]
</script>
