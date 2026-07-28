import { useStore } from "@nanostores/vue"
import { ImportStore } from "@/modules/import/stores/ImportStore"

export function useImport() {
    const store = ImportStore.get()
    const step = useStore(store.$step)
    const error = useStore(store.$error)
    const result = useStore(store.$result)

    return {
        store,
        step,
        error,
        result,
    }
}
