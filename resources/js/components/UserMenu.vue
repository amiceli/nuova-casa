<template>
    <nord-dropdown
        slot="header"
        expand
    >
        <nord-button
            slot="toggle"
            expand
        >
            <nord-avatar
                slot="start"
                :name="authUser.name"
                variant="square"
                :src="authUser.avatar || undefined"
            >
                {{ initials }}
            </nord-avatar>
            {{ authUser.name }}
        </nord-button>
        <nord-dropdown-item :href="profileHref">
            {{ t('nav.profile') }}
        </nord-dropdown-item>
        <nord-dropdown-item @click="logout()">
            {{ t('nav.logout') }}
        </nord-dropdown-item>
    </nord-dropdown>
</template>

<script setup lang="ts">
import { router, usePage } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import type { User } from "@/types"

const page = usePage()
const authUser = page.props.auth.user as User
const { t } = useI18n()

const initials = computed<string>(() =>
    authUser.name
        .split(" ")
        .filter((part) => part.length > 0)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join(""),
)

const profileHref = route("profile.edit")

function logout() {
    router.post(route("logout"))
}
</script>
