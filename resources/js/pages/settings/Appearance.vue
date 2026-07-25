<template>
    <Head :title="t('settings.appearanceTitle')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <SettingsLayout>
            <nord-card>
                <h2
                    slot="header"
                    class="n-typescale-l"
                >
                    {{ t('settings.appearanceTitle') }}
                </h2>

                <nord-stack gap="m">
                    <p class="n-color-text-weaker">{{ t('settings.appearanceSubtitle') }}</p>
                    <AppearanceTabs />
                </nord-stack>
            </nord-card>
        </SettingsLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import AppearanceTabs from "@/components/AppearanceTabs.vue"
import AppLayout from "@/layouts/AppLayout.vue"
import SettingsLayout from "@/layouts/settings/Layout.vue"
import { type BreadcrumbItem } from "@/types"

const { t } = useI18n()
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("settings.appearanceTitle"),
        href: "/settings/appearance",
    },
]
</script>
