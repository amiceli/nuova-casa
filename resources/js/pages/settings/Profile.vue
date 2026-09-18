<template>
    <Head :title="t('settings.profileTitle')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <SettingsLayout>
            <nord-card>
                <h2
                    slot="header"
                    class="n-typescale-l"
                >
                    {{ t('settings.profileTitle') }}
                </h2>

                <nord-stack gap="m">
                    <nord-avatar
                        size="xxl"
                        variant="square"
                        :name="user.name"
                        :src="user.avatar || undefined"
                    ></nord-avatar>
                    <nord-input
                        expand
                        readonly
                        :label="t('settings.name')"
                        :value="user.name"
                    ></nord-input>
                    <nord-input
                        expand
                        readonly
                        type="email"
                        :label="t('settings.email')"
                        :value="user.email"
                    ></nord-input>
                </nord-stack>
            </nord-card>

            <nord-card>
                <h2
                    slot="header"
                    class="n-typescale-l"
                >
                    {{ t('import.title') }}
                </h2>

                <ImportBookmarks />
            </nord-card>

            <nord-card>
                <h2 slot="header" class="n-typescale-l">
                    {{ t('settings.exportTitle') }}
                </h2>
                <nord-stack gap="m">
                    <p class="n-color-text-weaker n-typescale-s">
                        {{ t('settings.exportHelp') }}
                    </p>
                    <nord-stack direction="horizontal" gap="m" wrap>
                        <nord-button href="/settings/profile/export.json">
                            {{ t('settings.exportJson') }}
                        </nord-button>
                        <nord-button href="/settings/profile/export-browser.json">
                            {{ t('settings.exportBrowser') }}
                        </nord-button>
                    </nord-stack>
                    <p class="n-color-text-weaker n-typescale-s">
                        {{ t('settings.exportHtmlTitle') }}
                    </p>
                    <nord-stack direction="horizontal" gap="m" wrap>
                        <nord-button href="/settings/profile/export.html">
                            {{ t('settings.exportHtml') }}
                        </nord-button>
                        <nord-button href="/settings/profile/export-styled.html">
                            {{ t('settings.exportStyledHtml') }}
                        </nord-button>
                    </nord-stack>
                </nord-stack>
            </nord-card>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import DeleteUser from "@/components/DeleteUser.vue"
import AppLayout from "@/layouts/AppLayout.vue"
import SettingsLayout from "@/layouts/settings/Layout.vue"
import ImportBookmarks from "@/modules/import/components/ImportBookmarks.vue"
import { type BreadcrumbItem, type User } from "@/types"

interface Props {
    status?: string
}

defineProps<Props>()

const page = usePage()
const { t } = useI18n()
const user = page.props.auth.user as User
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t("settings.profileTitle"),
        href: "/settings/profile",
    },
]
</script>
