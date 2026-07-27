<template>
    <nord-stack gap="m">
        <p class="n-color-text-weaker n-typescale-s">
            {{ t('import.help') }}
        </p>

        <nord-banner
            v-if="errorMessage"
            variant="danger"
        >
            {{ errorMessage }}
        </nord-banner>

        <input
            ref="fileInput"
            type="file"
            accept=".html,.htm,text/html"
            hidden
            @change="onFileChange($event)"
        />

        <nord-stack
            direction="horizontal"
            gap="m"
            align-items="center"
            v-if="!isRunning"
        >
            <nord-button @click="pickFile()">
                {{ t('import.pickFile') }}
            </nord-button>
            <span class="n-truncate n-typescale-s n-color-text-weaker">
                {{ fileName || t('import.noFile') }}
            </span>
        </nord-stack>

        <nord-stack
            direction="horizontal"
            gap="m"
            align-items="center"
            v-if="isRunning"
        >
            <nord-spinner size="s"></nord-spinner>
            <span class="n-typescale-s">
                {{ t('import.searchingIcons', { done, total: started?.total ?? 0, percent }) }}
            </span>
        </nord-stack>

        <nord-banner
            v-if="isDone && started"
            variant="success"
        >
            {{ t('import.done', { tags: started.tags, pages: started.pages }) }}
        </nord-banner>

        <nord-banner
            v-if="isDone && started && started.skipped > 0"
            variant="warning"
        >
            {{ t('import.skipped', { count: started.skipped }) }}
        </nord-banner>

        <nord-stack
            direction="horizontal"
            gap="m"
            align-items="center"
        >
            <nord-button
                variant="primary"
                :loading="isUploading"
                :disabled="!canImport"
                @click="canImport && startImport()"
            >
                {{ t('import.start') }}
            </nord-button>
        </nord-stack>
    </nord-stack>
</template>

<script
    lang="ts"
    setup
>
import { usePage } from "@inertiajs/vue3"
import { computed, onMounted, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { ImportError } from "@/modules/domain/Types"
import { useImport } from "@/modules/import/composables/useImport"
import { useImportChannel } from "@/modules/import/composables/useImportChannel"
import { ImportStep } from "@/modules/import/stores/ImportStore"
import type { User } from "@/types"

const page = usePage()
const { t } = useI18n()
const { store, step, error, started, done, percent } = useImport()

const fileInput = ref<HTMLInputElement | null>(null)
const file = ref<File | null>(null)

const user = page.props.auth.user as User

useImportChannel(user.id)

const fileName = computed<string>(() => file.value?.name ?? "")

const isUploading = computed<boolean>(() => step.value === ImportStep.Uploading)

const isRunning = computed<boolean>(() => step.value === ImportStep.Running)

const isDone = computed<boolean>(() => step.value === ImportStep.Done)

const canImport = computed<boolean>(() => file.value !== null && !isUploading.value && !isRunning.value)

const errorMessage = computed<string>(() => (error.value ? t(`errors.${error.value}`) : ""))

const emit = defineEmits<{ finished: [] }>()

function pickFile() {
    fileInput.value?.click()
}

function onFileChange(event: Event) {
    const picked = (event.target as HTMLInputElement).files?.[0] ?? null

    file.value = picked

    store.resetState()
}

function startImport() {
    if (!file.value) {
        store.$error.set(ImportError.FileRequired)

        return
    }

    store.startImport({
        file: file.value,
        defaultTag: t("import.defaultTag"),
    })
}

onMounted(() => {
    const overlay = document.querySelector("nord-toast-group")

    watch(step, (value: ImportStep) => {
        if (value !== ImportStep.Done) {
            return
        }

        const message = t("import.done", {
            tags: started.value?.tags ?? 0,
            pages: started.value?.pages ?? 0,
        })

        overlay?.addToast(message, {
            autoDismiss: 6000,
        })

        emit("finished")
    })
})
</script>
