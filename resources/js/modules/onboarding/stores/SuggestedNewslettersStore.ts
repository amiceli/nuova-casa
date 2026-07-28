import { atom } from "nanostores"
import { route } from "ziggy-js"
import { postJson } from "@/lib/http"
import { type AvailableNewsletter, NewsletterError } from "@/modules/domain/Types"
import { AddNewsletterStore } from "@/modules/newsletters/stores/AddNewsletterStore"

export class SuggestedNewslettersStore {
    private static instance: SuggestedNewslettersStore | null

    /** the onboarding proposes a handful of newsletters, not the whole catalog */
    private static readonly SUGGESTION_COUNT = 12

    public readonly $selectedIds = atom<number[]>([])

    public readonly $isFollowing = atom<boolean>(false)

    public readonly $error = atom<NewsletterError | null>(null)

    public readonly $followed = atom<number | null>(null)

    public static get(): SuggestedNewslettersStore {
        if (!SuggestedNewslettersStore.instance) {
            SuggestedNewslettersStore.instance = new SuggestedNewslettersStore()
        }

        return SuggestedNewslettersStore.instance
    }

    public async loadSuggestions() {
        this.$error.set(null)

        await AddNewsletterStore.get().loadCatalog()
    }

    /**
     * Only the ones with a known feed, following the others fails. The ones
     * with a logo and a description come first.
     */
    public suggestions(catalog: AvailableNewsletter[]): AvailableNewsletter[] {
        return [...catalog]
            .filter((item) => !item.followed && item.feedUrl !== null)
            .sort((first, second) => this.appeal(second) - this.appeal(first))
            .slice(0, SuggestedNewslettersStore.SUGGESTION_COUNT)
    }

    public toggle(id: number) {
        const selected = this.$selectedIds.get()

        this.$selectedIds.set(selected.includes(id) ? selected.filter((item) => item !== id) : [...selected, id])
    }

    public async follow(): Promise<boolean> {
        const ids = this.$selectedIds.get()

        if (ids.length === 0) {
            return true
        }

        this.$isFollowing.set(true)
        this.$error.set(null)

        try {
            const response = await postJson({ url: route("follow-newsletters"), body: { ids } })

            if (!response.ok) {
                this.$error.set(NewsletterError.SaveFailed)

                return false
            }

            const json: { followed: number } = await response.json()

            this.$followed.set(json.followed)

            // the catalog keeps a followed flag, it is stale once they are added
            AddNewsletterStore.get().$catalog.set([])

            return true
        } catch (e) {
            console.error(`action=follow_newsletters, status=failed, reason=${e}`)
            this.$error.set(NewsletterError.SaveFailed)

            return false
        } finally {
            this.$isFollowing.set(false)
        }
    }

    private appeal(newsletter: AvailableNewsletter): number {
        return (newsletter.icon ? 2 : 0) + (newsletter.description ? 1 : 0)
    }

    private constructor() {}
}
