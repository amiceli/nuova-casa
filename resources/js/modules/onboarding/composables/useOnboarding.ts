import { useStore } from "@nanostores/vue"
import { computed } from "vue"
import { OnboardingStep, OnboardingStore } from "@/modules/onboarding/stores/OnboardingStore"

export function useOnboarding() {
    const store = OnboardingStore.get()
    const step = useStore(store.$step)
    const isCompleting = useStore(store.$isCompleting)

    const isLastStep = computed<boolean>(() => step.value === OnboardingStep.Newsletters)

    const stepNumber = computed<number>(() => step.value + 1)

    return {
        store,
        step,
        stepNumber,
        isCompleting,
        isLastStep,
        totalSteps: OnboardingStore.STEPS.length,
    }
}
