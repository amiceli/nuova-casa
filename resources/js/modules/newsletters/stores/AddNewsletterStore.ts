import { useForm } from "@inertiajs/vue3"
import { useStore } from "@nanostores/vue"
import { atom } from "nanostores"
import { computed } from "vue"
import { route } from "ziggy-js"
import { AvailableNewsletter, NewsletterError } from "@/modules/domain/Types"

export class AddNewsletterStore {
    private static instance: AddNewsletterStore | null

    public readonly $isLoading = atom<boolean>(false)

    public readonly $isCatalogLoading = atom<boolean>(false)

    public readonly $isFeedLoading = atom<boolean>(false)

    public readonly $status = atom<string | null>(null)

    public readonly $catalog = atom<AvailableNewsletter[]>([])

    public readonly $selected = atom<AvailableNewsletter | null>(null)

    public readonly $newNewsletter = useForm({
        url: "",
        title: "",
        available_newsletter_id: null as number | null,
    })

    public static get(): AddNewsletterStore {
        if (!AddNewsletterStore.instance) {
            AddNewsletterStore.instance = new AddNewsletterStore()
        }

        return AddNewsletterStore.instance
    }

    public async prepare() {
        this.resetState()
        this.$status.set(null)

        await this.loadCatalog()
    }

    public async loadCatalog() {
        if (this.$catalog.get().length > 0) {
            return
        }

        this.$isCatalogLoading.set(true)

        try {
            const response = await fetch(route("available-newsletters"), {
                headers: { Accept: "application/json" },
            })
            const json: { newsletters: AvailableNewsletter[] } = await response.json()

            this.$catalog.set(json.newsletters ?? [])
        } catch (e) {
            console.error(`action=load_newsletter_catalog, status=failed, reason=${e}`)
            this.$newNewsletter.setError("url", NewsletterError.CatalogLoadFailed)
        } finally {
            this.$isCatalogLoading.set(false)
        }
    }

    public async selectNewsletter(id: string) {
        const selected = this.$catalog.get().find((item) => String(item.id) === id) ?? null

        this.$selected.set(selected)
        this.$newNewsletter.clearErrors()
        this.$newNewsletter.url = ""
        this.$newNewsletter.title = selected?.name ?? ""
        this.$newNewsletter.available_newsletter_id = selected?.id ?? null

        if (!selected) {
            return
        }

        await this.loadFeedUrl(selected)

        // an already followed newsletter still shows its feed, it just cannot be saved again
        if (selected.followed) {
            this.$newNewsletter.setError("url", NewsletterError.AlreadyFollowed)
        }
    }

    public setFeedUrl(url: string) {
        this.$newNewsletter.url = url
        this.$newNewsletter.clearErrors()
    }

    public saveRss() {
        if (this.$newNewsletter.url.trim() === "") {
            this.$newNewsletter.setError("url", NewsletterError.UrlRequired)

            return
        }

        this.$isLoading.set(true)

        this.$newNewsletter.post(route("create-rss"), {
            onSuccess: () => {
                // the catalog keeps a followed flag, it is stale once a newsletter is added
                this.$catalog.set([])
                this.resetState()
                this.$status.set("success")
            },
            onError: (args) => {
                console.error(`action=save_newsletter, status=failed, reason=${JSON.stringify(args)}`)
                this.$status.set("failed")
            },
            onFinish: () => {
                this.$isLoading.set(false)
            },
        })
    }

    public resetState() {
        this.$selected.set(null)
        this.$newNewsletter.url = ""
        this.$newNewsletter.title = ""
        this.$newNewsletter.available_newsletter_id = null
        this.$newNewsletter.clearErrors()
    }

    private async loadFeedUrl(selected: AvailableNewsletter) {
        if (selected.feedUrl) {
            this.$newNewsletter.url = selected.feedUrl

            return
        }

        this.$isFeedLoading.set(true)

        try {
            const response = await fetch(route("available-newsletter-feed", { availableNewsletter: selected.id }), {
                headers: { Accept: "application/json" },
            })
            const json: { feedUrl: string | null } = await response.json()

            if (!json.feedUrl) {
                this.$newNewsletter.setError("url", NewsletterError.FeedNotFound)

                return
            }

            this.$newNewsletter.url = json.feedUrl
        } catch (e) {
            console.error(`action=load_newsletter_feed, status=failed, reason=${e}`)
            this.$newNewsletter.setError("url", NewsletterError.FeedNotFound)
        } finally {
            this.$isFeedLoading.set(false)
        }
    }
}

export function useNewsletter() {
    const store = AddNewsletterStore.get()
    const isLoading = useStore(store.$isLoading)
    const isCatalogLoading = useStore(store.$isCatalogLoading)
    const isFeedLoading = useStore(store.$isFeedLoading)
    const status = useStore(store.$status)
    const catalog = useStore(store.$catalog)
    const selected = useStore(store.$selected)

    const selectedId = computed<string>(() => (selected.value ? String(selected.value.id) : ""))

    const canSave = computed<boolean>(
        () => !isLoading.value && !isFeedLoading.value && store.$newNewsletter.url.trim() !== "" && !store.$newNewsletter.errors.url,
    )

    return {
        store,
        status,
        isLoading,
        isCatalogLoading,
        isFeedLoading,
        catalog,
        selected,
        selectedId,
        canSave,
        newNewsletter: store.$newNewsletter,
    }
}
