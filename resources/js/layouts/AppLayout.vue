<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <template
            v-if="$slots.meta"
            #meta
        >
            <slot name="meta" />
        </template>
        <template
            v-if="$slots.actions"
            #actions
        >
            <slot name="actions" />
        </template>
        <slot />
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from "@/layouts/app/AppSidebarLayout.vue"
import type { BreadcrumbItemType } from "@/types"

interface Props {
    breadcrumbs?: BreadcrumbItemType[]
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})
</script>
