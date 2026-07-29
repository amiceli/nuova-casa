import { usePage } from "@inertiajs/vue3"
import { onMounted } from "vue"
import { route } from "ziggy-js"
import { type Appearance, useAppearance } from "@/composables/useAppearance"
import { postJson } from "@/lib/http"
import type { User } from "@/types"

/**
 * The theme lives on the user. The local storage keeps a copy, it paints the
 * page before the first response.
 */
export function useThemePreference() {
    const page = usePage()
    const { appearance, updateAppearance } = useAppearance()

    async function selectTheme(value: Appearance) {
        updateAppearance(value)

        try {
            await postJson({ url: route("update-theme"), body: { theme: value } })
        } catch (e) {
            console.error(`action=save_theme, status=failed, reason=${e}`)
        }
    }

    onMounted(() => {
        const stored = (page.props.auth?.user as User | undefined)?.theme

        if (stored && stored !== appearance.value) {
            updateAppearance(stored)
        }
    })

    return {
        appearance,
        selectTheme,
    }
}
