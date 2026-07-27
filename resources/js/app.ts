import "@/echo"
import "@nordhealth/themes/lib/nord.css"
import "@nordhealth/css"
import "@nordhealth/components/lib/Avatar"
import "@nordhealth/components/lib/Badge"
import "@nordhealth/components/lib/Banner"
import "@nordhealth/components/lib/Button"
import "@nordhealth/components/lib/Card"
import "@nordhealth/components/lib/Combobox"
import "@nordhealth/components/lib/ComboboxOption"
import "@nordhealth/components/lib/Dropdown"
import "@nordhealth/components/lib/EmptyState"
import "@nordhealth/components/lib/DropdownItem"
import "@nordhealth/components/lib/Header"
import "@nordhealth/components/lib/Icon"
import "@nordhealth/components/lib/Input"
import "@nordhealth/components/lib/Layout"
import "@nordhealth/components/lib/Modal"
import "@nordhealth/components/lib/Navigation"
import "@nordhealth/components/lib/NavItem"
import "@nordhealth/components/lib/Spinner"
import "@nordhealth/components/lib/Stack"
import "@nordhealth/components/lib/Toast"
import "@nordhealth/components/lib/ToastGroup"
import "@nordhealth/components/lib/Toggle"
import "@nordhealth/components/lib/Tooltip"
import "@nordhealth/components/lib/TopBar"
import "../css/app.css"

import { createInertiaApp } from "@inertiajs/vue3"
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"
import type { DefineComponent } from "vue"
import { createApp, h } from "vue"
import { ZiggyVue } from "ziggy-js"
import { i18n } from "./i18n"
import { initializeTheme } from "./composables/useAppearance"

const appName = import.meta.env.VITE_APP_NAME || "Laravel"

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>("./pages/**/*.vue")),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(ZiggyVue)
            .mount(el)
    },
    progress: {
        color: "#4B5563",
    },
})

// This will set light / dark mode on page load...
initializeTheme()
