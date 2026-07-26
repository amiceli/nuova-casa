import { useI18n } from "vue-i18n"

export type FormatDateParams = {
    value: string | Date | null | undefined
    withTime?: boolean
}

export function useDateFormat() {
    const { locale } = useI18n()

    function formatDate(params: FormatDateParams): string {
        if (!params.value) {
            return ""
        }

        const date = new Date(params.value)

        if (Number.isNaN(date.getTime())) {
            return ""
        }

        return params.withTime
            ? date.toLocaleString(locale.value, { dateStyle: "long", timeStyle: "short" })
            : date.toLocaleDateString(locale.value, { dateStyle: "long" })
    }

    return { formatDate }
}
