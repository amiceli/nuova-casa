<template>
    <nord-stack gap="m">
        <p class="n-color-text-weaker">
            {{ t('onboarding.newsletters.text') }}
        </p>

        <nord-banner
            v-if="errorMessage"
            variant="danger"
        >
            {{ errorMessage }}
        </nord-banner>

        <nord-stack
            v-if="isLoading"
            direction="horizontal"
            gap="m"
            align-items="center"
        >
            <nord-spinner size="s"></nord-spinner>
            <span class="n-typescale-s n-color-text-weaker">{{ t('onboarding.newsletters.loading') }}</span>
        </nord-stack>

        <nord-empty-state v-else-if="suggestions.length === 0">
            <h3 slot="header">{{ t('onboarding.newsletters.emptyTitle') }}</h3>
            <p>{{ t('onboarding.newsletters.emptyText') }}</p>
        </nord-empty-state>

        <div
            v-else
            class="n-grid-2 n-gap-m"
        >
            <nord-card
                v-for="item in suggestions"
                :key="item.id"
                padding="m"
                class="suggestion"
                @click="store.toggle(item.id)"
            >
                <nord-stack
                    direction="horizontal"
                    gap="m"
                    align-items="center"
                >
                    <img
                        class="n-size-icon-l"
                        v-if="item.icon && !failedIcons.includes(item.id)"
                        :src="item.icon"
                        :alt="item.name"
                        @error="onIconError(item.id)"
                    />
                    <nord-stack gap="xs">
                        <span class="n-truncate n-typescale-m">{{ item.name }}</span>
                        <span
                            class="n-typescale-s n-color-text-weaker n-truncate"
                            v-if="item.description"
                        >
                            {{ item.description }}
                        </span>
                    </nord-stack>
                </nord-stack>

                <nord-stack
                    slot="footer"
                    direction="horizontal"
                    gap="s"
                    align-items="center"
                >
                    <nord-badge :variant="isSelected(item) ? 'highlight' : undefined">
                        {{ isSelected(item) ? t('onboarding.newsletters.selected') : t('onboarding.newsletters.select') }}
                    </nord-badge>
                </nord-stack>
            </nord-card>
        </div>

        <span
            class="n-typescale-s n-color-text-weaker"
            v-if="selectedIds.length > 0"
        >
            {{ t('onboarding.newsletters.count', { count: selectedIds.length }) }}
        </span>
    </nord-stack>
</template>

<script
    lang="ts"
    setup
>
import { computed, onMounted, ref } from "vue"
import { useI18n } from "vue-i18n"
import type { AvailableNewsletter } from "@/modules/domain/Types"
import { useSuggestedNewsletters } from "@/modules/onboarding/composables/useSuggestedNewsletters"

const { t, te } = useI18n()
const { store, suggestions, selectedIds, isLoading, error } = useSuggestedNewsletters()

const failedIcons = ref<number[]>([])

const errorMessage = computed<string>(() => {
    if (!error.value) {
        return ""
    }

    return te(`errors.${error.value}`) ? t(`errors.${error.value}`) : ""
})

function isSelected(newsletter: AvailableNewsletter) {
    return selectedIds.value.includes(newsletter.id)
}

function onIconError(id: number) {
    failedIcons.value = [...failedIcons.value, id]
}

onMounted(() => {
    store.loadSuggestions()
})
</script>

<style scoped>
.suggestion {
    cursor: pointer;
}
</style>
