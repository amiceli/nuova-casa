import { useForm } from "@inertiajs/vue3"
import { atom } from "nanostores"
import { route } from "ziggy-js"
import { TagError } from "@/modules/domain/Types"

export class TagStore {
    private static instance: TagStore | null

    public readonly $inProgress = atom<boolean>(false)

    public readonly $searDone = atom<boolean>(false)

    public readonly $status = atom<string>("")

    public readonly $allImages = atom<string[]>([])

    public readonly newTag = useForm({
        name: "",
        icon: "",
    })

    public static get(): TagStore {
        if (!TagStore.instance) {
            TagStore.instance = new TagStore()
        }

        return TagStore.instance
    }

    public setTagIcon(url: string) {
        this.newTag.icon = url
    }

    public async saveTag() {
        this.newTag.post(route("create-tag"), {
            onSuccess: () => {
                this.$status.set("success")
            },
            onError: (args) => {
                console.error(`action=save_tag, status=failed, reason=${JSON.stringify(args)}`)
                this.$status.set("failed")
            },
        })
    }

    public async searchIcon() {
        const tagName = this.newTag.name.trim()

        if (!tagName) {
            this.newTag.setError({ name: TagError.NameRequired })

            return
        }

        this.$inProgress.set(true)
        this.newTag.clearErrors()

        if (await this.isNameAlreadyUsed(tagName)) {
            this.$inProgress.set(false)

            return
        }

        const response = await fetch(route(`sear-xng`, { name: tagName }))
        const json: { images: string[] } = await response.json()

        this.$allImages.set(json.images ?? [])

        setTimeout(() => {
            this.$inProgress.set(false)
            this.$searDone.set(true)
        }, 750)
    }

    public async resetState(defaultName?: string) {
        this.newTag.icon = ""
        this.newTag.name = defaultName ?? ""
        this.newTag.clearErrors()

        this.$searDone.set(false)
        this.$status.set("")
    }

    private async isNameAlreadyUsed(name: string): Promise<boolean> {
        try {
            const response = await fetch(route("tag-name-used", { name }))
            const json: { used: boolean } = await response.json()

            if (json.used) {
                this.newTag.setError({ name: TagError.NameAlreadyUsed })
            }

            return json.used
        } catch (e) {
            console.error(`action=check_tag_name, status=failed, reason=${e}`)
            this.newTag.setError({ name: TagError.CheckFailed })

            return true
        }
    }

    private constructor() {}
}
