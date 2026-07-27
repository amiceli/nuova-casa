import { useStore } from "@nanostores/vue"
import { computed as computedStore } from "nanostores"
import { ImportStore } from "@/modules/import/stores/ImportStore"

export function useImport() {
    const store = ImportStore.get()
    const step = useStore(store.$step)
    const error = useStore(store.$error)
    const started = useStore(store.$started)
    const done = useStore(store.$done)

    const percent = useStore(
        computedStore([store.$done, store.$started], (finished, current) => {
            const total = current?.total ?? 0

            return total === 0 ? 100 : Math.round((finished / total) * 100)
        }),
    )

    return {
        store,
        step,
        error,
        started,
        done,
        percent,
    }
}
