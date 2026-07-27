import { router } from "@inertiajs/vue3"
import { atom } from "nanostores"
import { route } from "ziggy-js"
import { postForm } from "@/lib/http"
import { ImportError, type ImportProgress, type ImportResult, type StartedImport } from "@/modules/domain/Types"

export enum ImportStep {
    Idle = "idle",
    Uploading = "uploading",
    /** tags and links are saved, the jobs are looking for their icons */
    Running = "running",
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

    public readonly $started = atom<StartedImport | null>(null)

    public readonly $done = atom<number>(0)

    public static get(): ImportStore {
        if (!ImportStore.instance) {
            ImportStore.instance = new ImportStore()
        }

        return ImportStore.instance
    }

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

            const started: StartedImport = await response.json()

            this.$started.set(started)

            // nothing left to look for, no job will ever report back
            this.$step.set(started.total === 0 ? ImportStep.Done : ImportStep.Running)
        } catch (e) {
            console.error(`action=import_bookmarks, status=failed, reason=${e}`)
            this.failWith(ImportError.UploadFailed)
        }
    }

    /** a job is over, it says how many of them are */
    public onProgress(progress: ImportProgress) {
        if (progress.importId !== this.$started.get()?.importId) {
            return
        }

        this.$done.set(progress.done)
    }

    /** every icon has been looked for, the lists are worth reloading */
    public onFinished(result: ImportResult) {
        if (result.importId !== this.$started.get()?.importId) {
            return
        }

        this.$step.set(ImportStep.Done)
        this.$done.set(this.$started.get()?.total ?? 0)

        router.reload()
    }

    public resetState() {
        this.$step.set(ImportStep.Idle)
        this.$error.set(null)
        this.$started.set(null)
        this.$done.set(0)
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
