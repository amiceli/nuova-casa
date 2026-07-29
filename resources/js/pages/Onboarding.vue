<template>
    <Head :title="t('onboarding.title')" />

    <nord-layout hide-default-nav-toggle>
        <nord-top-bar slot="top-bar">
            <nord-stack
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <img
                    class="brand-logo"
                    :src="logo"
                    alt="Nuova casa"
                />
                <strong class="brand-name n-typescale-l">{{ t('brand') }}</strong>
            </nord-stack>
            <nord-stack
                slot="end"
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <nord-button @click="logout()">
                    {{ t('nav.logout') }}
                </nord-button>
            </nord-stack>
        </nord-top-bar>

        <nord-stack
            class="onboarding"
            gap="l"
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
                    class="onboarding-actions"
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
    </nord-layout>
</template>

<script
    lang="ts"
    setup
>
import logo from "@assets/casa-logo.webp"
import { Head, router } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
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

function logout() {
    router.post(route("logout"))
}
</script>

<style scoped>
.brand-logo {
    height: 32px;
    width: 32px;
    border-radius: var(--n-border-radius);
    object-fit: cover;
}

.brand-name {
    color: #fff;
}

.onboarding-actions {
    margin-block-start: var(--n-space-l);
}

.onboarding {
    max-width: 64rem;
    margin: var(--n-space-xl) auto;
    padding: var(--n-space-m);
}
</style>
