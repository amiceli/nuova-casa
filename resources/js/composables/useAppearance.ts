import darkThemeUrl from "@nordhealth/themes/lib/nord-dark.css?url"
import { onMounted, ref } from "vue"

export type Appearance = "light" | "dark" | "system"

// Nord ships its themes as separate stylesheets, so switching mode means
// enabling or disabling the dark one through its media attribute...
const DARK_THEME_LINK_ID = "nord-dark-theme"
const SYSTEM_DARK_MEDIA = "(prefers-color-scheme: dark)"
const ALWAYS_MEDIA = "all"
const NEVER_MEDIA = "not all"

const appearance = ref<Appearance>("system")

const darkThemeLink = () => {
    const existing = document.getElementById(DARK_THEME_LINK_ID)

    if (existing instanceof HTMLLinkElement) {
        return existing
    }

    const link = document.createElement("link")

    link.id = DARK_THEME_LINK_ID
    link.rel = "stylesheet"
    link.href = darkThemeUrl

    document.head.append(link)

    return link
}

const darkThemeMedia = (value: Appearance) => {
    if (value === "dark") {
        return ALWAYS_MEDIA
    }

    if (value === "light") {
        return NEVER_MEDIA
    }

    return SYSTEM_DARK_MEDIA
}

const setCookie = (options: { name: string; value: string; days?: number }) => {
    if (typeof document === "undefined") {
        return
    }

    const maxAge = (options.days ?? 365) * 24 * 60 * 60

    document.cookie = `${options.name}=${options.value};path=/;max-age=${maxAge};SameSite=Lax`
}

const getStoredAppearance = () => {
    if (typeof window === "undefined") {
        return null
    }

    return localStorage.getItem("appearance") as Appearance | null
}

export function updateTheme(value: Appearance) {
    if (typeof document === "undefined") {
        return
    }

    darkThemeLink().media = darkThemeMedia(value)
}

export function initializeTheme() {
    if (typeof window === "undefined") {
        return
    }

    // Initialize theme from saved preference or default to system...
    updateTheme(getStoredAppearance() || "system")
}

export function useAppearance() {
    onMounted(() => {
        const savedAppearance = getStoredAppearance()

        if (savedAppearance) {
            appearance.value = savedAppearance
        }
    })

    function updateAppearance(value: Appearance) {
        appearance.value = value

        // Store in localStorage for client-side persistence...
        localStorage.setItem("appearance", value)

        // Store in cookie for SSR...
        setCookie({ name: "appearance", value })

        updateTheme(value)
    }

    return {
        appearance,
        updateAppearance,
    }
}
