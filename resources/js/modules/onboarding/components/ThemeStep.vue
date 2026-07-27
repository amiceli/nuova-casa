<template>
    <nord-stack gap="m">
        <p class="n-color-text-weaker">
            {{ t('onboarding.theme.text') }}
        </p>
        <AppearanceTabs />
    </nord-stack>
</template>

<script
    lang="ts"
    setup
>
import { useI18n } from "vue-i18n"
import AppearanceTabs from "@/components/AppearanceTabs.vue"

const { t } = useI18n()
</script>
