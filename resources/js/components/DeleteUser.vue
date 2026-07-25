<template>
    <nord-card>
        <h2
            slot="header"
            class="n-typescale-l"
        >
            {{ t('settings.deleteTitle') }}
        </h2>

        <nord-banner variant="danger">
            {{ t('settings.deleteWarn') }}
        </nord-banner>

        <nord-button
            slot="footer"
            variant="danger"
            @click="openModal()"
        >
            <nord-icon
                slot="start"
                name="interface-delete"
            ></nord-icon>
            {{ t('settings.deleteAction') }}
        </nord-button>
    </nord-card>

    <nord-modal
        :open="isOpen"
        size="s"
        @close="closeModal()"
    >
        <h2 slot="header">{{ t('settings.deleteTitle') }}</h2>
        <nord-stack gap="m">
            <p>{{ t('settings.deleteText') }}</p>
            <p class="n-color-text-weaker n-typescale-s">
                {{ t('common.irreversible') }}
            </p>
            <nord-input
                expand
                :label="t('settings.confirmLabel')"
                :placeholder="expectedConfirmation"
                :value="form.confirmation"
                :error="errorMessage"
                @input="onConfirmationInput($event)"
            ></nord-input>
        </nord-stack>
        <nord-button
            slot="footer"
            :disabled="form.processing"
            @click="closeModal()"
        >
            {{ t('common.cancel') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="danger"
            :loading="form.processing"
            @click="deleteUser()"
        >
            {{ t('settings.deleteAction') }}
        </nord-button>
    </nord-modal>
</template>

<script
    lang="ts"
    setup
>
import { useForm, usePage } from "@inertiajs/vue3"
import { computed, ref } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import { type User } from "@/types"

const page = usePage()
const { t } = useI18n()
const user = page.props.auth.user as User
const isOpen = ref<boolean>(false)
const form = useForm({
    confirmation: "",
})

const expectedConfirmation = `${user.name}/${user.email}`

const errorMessage = computed<string>(() =>
    form.errors.confirmation ? t(`errors.${form.errors.confirmation}`) : "",
)

function openModal() {
    isOpen.value = true
}

function closeModal() {
    isOpen.value = false
    form.clearErrors()
    form.reset()
}

function onConfirmationInput(event: Event) {
    form.confirmation = (event.target as HTMLInputElement).value
}

function deleteUser() {
    form.delete(route("profile.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    })
}
</script>
