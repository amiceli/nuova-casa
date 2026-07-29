import { useStore } from "@nanostores/vue"
import { computed } from "vue"
import type { AvailableNewsletter } from "@/modules/domain/Types"
import { AddNewsletterStore } from "@/modules/newsletters/stores/AddNewsletterStore"
import { SuggestedNewslettersStore } from "@/modules/onboarding/stores/SuggestedNewslettersStore"

export function useSuggestedNewsletters() {
    const store = SuggestedNewslettersStore.get()
    const catalogStore = AddNewsletterStore.get()

    const selectedIds = useStore(store.$selectedIds)
    const isFollowing = useStore(store.$isFollowing)
    const error = useStore(store.$error)
    const catalog = useStore(catalogStore.$catalog)
    const isLoading = useStore(catalogStore.$isCatalogLoading)

    const suggestions = computed<AvailableNewsletter[]>(() => store.suggestions(catalog.value))

    return {
        store,
        catalog,
        suggestions,
        selectedIds,
        isFollowing,
        isLoading,
        error,
    }
}
