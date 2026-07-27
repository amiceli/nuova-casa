import { router } from "@inertiajs/vue3"
import { atom } from "nanostores"
import { route } from "ziggy-js"

export enum OnboardingStep {
    Welcome = 0,
    Bookmarks = 1,
    Theme = 2,
    Newsletters = 3,
}

export class OnboardingStore {
    private static instance: OnboardingStore | null

    public static readonly STEPS = [OnboardingStep.Welcome, OnboardingStep.Bookmarks, OnboardingStep.Theme, OnboardingStep.Newsletters]

    public readonly $step = atom<OnboardingStep>(OnboardingStep.Welcome)

    public readonly $isCompleting = atom<boolean>(false)

    public static get(): OnboardingStore {
        if (!OnboardingStore.instance) {
            OnboardingStore.instance = new OnboardingStore()
        }

        return OnboardingStore.instance
    }

    /** the onboarding only proposes, every step can be left as it is */
    public goToNextStep() {
        const current = this.$step.get()

        if (current === OnboardingStep.Newsletters) {
            this.complete()

            return
        }

        this.$step.set(current + 1)
    }

    public goToPreviousStep() {
        const current = this.$step.get()

        if (current === OnboardingStep.Welcome) {
            return
        }

        this.$step.set(current - 1)
    }

    public complete() {
        this.$isCompleting.set(true)

        router.post(
            route("complete-onboarding"),
            {},
            {
                onFinish: () => {
                    this.$isCompleting.set(false)
                },
            },
        )
    }

    private constructor() {}
}
