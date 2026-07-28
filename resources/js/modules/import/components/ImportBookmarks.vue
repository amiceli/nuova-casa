<template>
    <nord-stack gap="m">
        <p class="n-color-text-weaker n-typescale-s">
            {{ t('import.help') }}
        </p>

        <p class="n-color-text-weaker n-typescale-s">
            {{ t('import.iconsLater') }}
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
        >
            <nord-button @click="pickFile()">
                {{ t('import.pickFile') }}
            </nord-button>
            <span class="n-truncate n-typescale-s n-color-text-weaker">
                {{ fileName || t('import.noFile') }}
            </span>
        </nord-stack>

        <nord-banner
            v-if="isDone && result"
            variant="success"
        >
            {{ t('import.done', { tags: result.tags, pages: result.pages }) }}
        </nord-banner>

        <nord-banner
            v-if="isDone && result && result.skipped > 0"
            variant="warning"
        >
            {{ t('import.skipped', { count: result.skipped }) }}
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
import { computed, onMounted, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { ImportError } from "@/modules/domain/Types"
import { useImport } from "@/modules/import/composables/useImport"
import { ImportStep } from "@/modules/import/stores/ImportStore"

const { t } = useI18n()
const { store, step, error, result } = useImport()

const fileInput = ref<HTMLInputElement | null>(null)
const file = ref<File | null>(null)

const fileName = computed<string>(() => file.value?.name ?? "")

const isUploading = computed<boolean>(() => step.value === ImportStep.Uploading)

const isDone = computed<boolean>(() => step.value === ImportStep.Done)

const canImport = computed<boolean>(() => file.value !== null && !isUploading.value)

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
            tags: result.value?.tags ?? 0,
            pages: result.value?.pages ?? 0,
        })

        overlay?.addToast(message, {
            autoDismiss: 6000,
        })

        emit("finished")
    })
})
</script>
