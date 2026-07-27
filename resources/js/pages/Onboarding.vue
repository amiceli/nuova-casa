<template>
    <Head :title="t('onboarding.title')" />

    <nord-stack
        class="onboarding"
        gap="l"
        align-items="center"
    >
        <nord-card padding="l">
            <nord-stack
                slot="header"
                direction="horizontal"
                gap="m"
                align-items="center"
                justify-content="space-between"
            >
                <h1 class="n-typescale-l">{{ t(currentTitle) }}</h1>
                <nord-badge>
                    {{ t('onboarding.stepCount', { current: stepNumber, total: totalSteps }) }}
                </nord-badge>
            </nord-stack>

            <WelcomeStep v-if="step === OnboardingStep.Welcome" />
            <ImportBookmarks v-else-if="step === OnboardingStep.Bookmarks" />
            <ThemeStep v-else-if="step === OnboardingStep.Theme" />
            <NewslettersStep v-else />

            <nord-stack
                slot="footer"
                direction="horizontal"
                gap="m"
                align-items="center"
                justify-content="space-between"
            >
                <nord-button
                    :disabled="step === OnboardingStep.Welcome"
                    @click="store.goToPreviousStep()"
                >
                    {{ t('onboarding.previous') }}
                </nord-button>

                <nord-stack
                    direction="horizontal"
                    gap="s"
                    align-items="center"
                >
                    <nord-button
                        v-if="step !== OnboardingStep.Welcome"
                        @click="store.goToNextStep()"
                    >
                        {{ t('onboarding.skip') }}
                    </nord-button>
                    <nord-button
                        variant="primary"
                        :loading="isCompleting || isFollowing"
                        @click="goToNextStep()"
                    >
                        {{ t(isLastStep ? 'onboarding.finish' : 'onboarding.next') }}
                    </nord-button>
                </nord-stack>
            </nord-stack>
        </nord-card>
    </nord-stack>

    <nord-toast-group></nord-toast-group>
</template>

<script
    lang="ts"
    setup
>
import { Head } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import ImportBookmarks from "@/modules/import/components/ImportBookmarks.vue"
import NewslettersStep from "@/modules/onboarding/components/NewslettersStep.vue"
import ThemeStep from "@/modules/onboarding/components/ThemeStep.vue"
import WelcomeStep from "@/modules/onboarding/components/WelcomeStep.vue"
import { useOnboarding } from "@/modules/onboarding/composables/useOnboarding"
import { useSuggestedNewsletters } from "@/modules/onboarding/composables/useSuggestedNewsletters"
import { OnboardingStep } from "@/modules/onboarding/stores/OnboardingStore"

const { t } = useI18n()
const { store, step, stepNumber, totalSteps, isCompleting, isLastStep } = useOnboarding()
const { store: newslettersStore, isFollowing } = useSuggestedNewsletters()

const titles: Record<OnboardingStep, string> = {
    [OnboardingStep.Welcome]: "onboarding.welcome.title",
    [OnboardingStep.Bookmarks]: "onboarding.bookmarks.title",
    [OnboardingStep.Theme]: "onboarding.theme.title",
    [OnboardingStep.Newsletters]: "onboarding.newsletters.title",
}

const currentTitle = computed<string>(() => titles[step.value as OnboardingStep])

/** only the last step has something left to save before leaving */
async function goToNextStep() {
    if (!isLastStep.value) {
        store.goToNextStep()

        return
    }

    const saved = await newslettersStore.follow()

    if (!saved) {
        return
    }

    store.complete()
}
</script>

<style scoped>
.onboarding {
    max-width: 46rem;
    margin: var(--n-space-xl) auto;
    padding: var(--n-space-m);
}
</style>
