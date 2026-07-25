<template>
    <nord-stack
        direction="horizontal"
        gap="s"
        :aria-label="t('settings.theme')"
        role="group"
    >
        <nord-button
            v-for="tab in tabs"
            :key="tab.value"
            :variant="isCurrent(tab) ? 'primary' : 'default'"
            :aria-pressed="isCurrent(tab)"
            @click="updateAppearance(tab.value)"
        >
            <nord-icon
                slot="start"
                :name="tab.icon"
            ></nord-icon>
            {{ t(tab.label) }}
        </nord-button>
    </nord-stack>
</template>

<script
    lang="ts"
    setup
>
import { useI18n } from "vue-i18n"
import { type Appearance, useAppearance } from "@/composables/useAppearance"

interface AppearanceTab {
    value: Appearance
    icon: string
    label: string
}

const { appearance, updateAppearance } = useAppearance()
const { t } = useI18n()

const tabs: AppearanceTab[] = [
    { value: "light", icon: "interface-mode-light", label: "settings.themeLight" },
    { value: "dark", icon: "interface-mode-dark", label: "settings.themeDark" },
    { value: "system", icon: "generic-computer", label: "settings.themeSystem" },
]

function isCurrent(tab: AppearanceTab) {
    return appearance.value === tab.value
}
</script>
