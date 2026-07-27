import { useEcho } from "@laravel/echo-vue"
import type { ImportProgress, ImportResult } from "@/modules/domain/Types"
import { ImportStore } from "@/modules/import/stores/ImportStore"

/**
 * The icon jobs report on the private channel of the user who started the import.
 */
export function useImportChannel(userId: number) {
    const store = ImportStore.get()
    const channel = `bookmarks-import.${userId}`

    useEcho<ImportProgress>(channel, ".progressed", (payload: ImportProgress) => {
        store.onProgress(payload)
    })

    useEcho<ImportResult>(channel, ".finished", (payload: ImportResult) => {
        store.onFinished(payload)
    })
}
