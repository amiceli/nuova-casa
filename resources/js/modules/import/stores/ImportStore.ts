import { router } from "@inertiajs/vue3"
import { atom } from "nanostores"
import { route } from "ziggy-js"
import { postForm } from "@/lib/http"
import { ImportError, type ImportResult } from "@/modules/domain/Types"

export enum ImportStep {
    Idle = "idle",
    Uploading = "uploading",
    Done = "done",
}

export type StartImportParams = {
    file: File
    defaultTag: string
}

export class ImportStore {
    private static instance: ImportStore | null

    public readonly $step = atom<ImportStep>(ImportStep.Idle)

    public readonly $error = atom<ImportError | null>(null)

    public readonly $result = atom<ImportResult | null>(null)

    public static get(): ImportStore {
        if (!ImportStore.instance) {
            ImportStore.instance = new ImportStore()
        }

        return ImportStore.instance
    }

    /** the whole import happens in that single call */
    public async startImport(params: StartImportParams) {
        this.resetState()
        this.$step.set(ImportStep.Uploading)

        const body = new FormData()

        body.append("bookmarks", params.file)
        body.append("default_tag", params.defaultTag)

        try {
            const response = await postForm({ url: route("import-bookmarks"), body })

            if (!response.ok) {
                this.failWith(await this.readError(response))

                return
            }

            const result: ImportResult = await response.json()

            this.$result.set(result)
            this.$step.set(ImportStep.Done)

            router.reload()
        } catch (e) {
            console.error(`action=import_bookmarks, status=failed, reason=${e}`)
            this.failWith(ImportError.UploadFailed)
        }
    }

    public resetState() {
        this.$step.set(ImportStep.Idle)
        this.$error.set(null)
        this.$result.set(null)
    }

    private failWith(error: ImportError) {
        this.$error.set(error)
        this.$step.set(ImportStep.Idle)
    }

    private async readError(response: Response): Promise<ImportError> {
        if (response.status === 413) {
            return ImportError.FileTooLarge
        }

        try {
            const json: { errors?: Record<string, string[]> } = await response.json()
            const code = Object.values(json.errors ?? {})[0]?.[0]

            return (code as ImportError) ?? ImportError.UploadFailed
        } catch {
            return ImportError.UploadFailed
        }
    }

    private constructor() {}
}
